
<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];
$search = trim($_GET['q'] ?? '');
$receiverId = isset($_GET['to']) ? (int) $_GET['to'] : 0;

$error = '';
$success = '';
$searchResults = [];
$conversations = [];
$messages = [];
$receiver = null;

/*
|--------------------------------------------------------------------------
| SEARCH REGISTERED USERS
|--------------------------------------------------------------------------
*/
if ($search !== '') {
    try {
        $stmt = $pdo->prepare("
            SELECT id, username
            FROM users
            WHERE id != ?
              AND username LIKE ?
            ORDER BY username ASC
            LIMIT 50
        ");

        $stmt->execute([
            $currentUserId,
            '%' . $search . '%'
        ]);

        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $exception) {
        error_log('User search error: ' . $exception->getMessage());
        $error = 'Unable to search accounts right now.';
    }
}

/*
|--------------------------------------------------------------------------
| GET CONVERSATIONS
|--------------------------------------------------------------------------
*/
try {
    $stmt = $pdo->prepare("
        SELECT
            u.id,
            u.username,
            MAX(m.created_at) AS last_message_time
        FROM users u
        INNER JOIN user_messages m
            ON (
                (m.sender_id = u.id AND m.receiver_id = ?)
                OR
                (m.receiver_id = u.id AND m.sender_id = ?)
            )
        WHERE u.id != ?
        GROUP BY u.id, u.username
        ORDER BY last_message_time DESC
    ");

    $stmt->execute([
        $currentUserId,
        $currentUserId,
        $currentUserId
    ]);

    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    error_log('Conversation list error: ' . $exception->getMessage());

    if ($error === '') {
        $error = 'Unable to load conversations. Please check your database tables.';
    }
}

/*
|--------------------------------------------------------------------------
| LOAD SELECTED RECEIVER
|--------------------------------------------------------------------------
*/
if ($receiverId > 0 && $receiverId !== $currentUserId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, username
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$receiverId]);
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$receiver) {
            $receiverId = 0;
        }

    } catch (PDOException $exception) {
        error_log('Receiver lookup error: ' . $exception->getMessage());
        $receiverId = 0;
    }
} else {
    $receiverId = 0;
}

/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $postedReceiverId = (int) ($_POST['receiver_id'] ?? 0);
    $messageText = trim($_POST['message_text'] ?? '');

    // Check CSRF token if the helper is available.
    if (function_exists('verifyCsrf')) {
        if (!verifyCsrf()) {
            $error = 'Your session token expired. Refresh the page and try again.';
        }
    } elseif (
        empty($_SESSION['csrf_token']) ||
        !hash_equals(
            (string) $_SESSION['csrf_token'],
            (string) ($_POST['csrf_token'] ?? '')
        )
    ) {
        $error = 'Your session token expired. Refresh the page and try again.';
    }

    if ($error === '') {
        if ($postedReceiverId <= 0 || $postedReceiverId === $currentUserId) {
            $error = 'Please select a valid account.';
        } elseif ($messageText === '') {
            $error = 'Please enter a message first.';
        } elseif (mb_strlen($messageText) > 5000) {
            $error = 'Your message is too long. Please keep it under 5,000 characters.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    SELECT id
                    FROM users
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmt->execute([$postedReceiverId]);

                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    $error = 'That account could not be found.';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_messages
                            (sender_id, receiver_id, message_text, created_at)
                        VALUES
                            (?, ?, ?, NOW())
                    ");

                    $stmt->execute([
                        $currentUserId,
                        $postedReceiverId,
                        $messageText
                    ]);

                    header('Location: messages.php?to=' . $postedReceiverId);
                    exit;
                }

            } catch (PDOException $exception) {
                error_log('Send message error: ' . $exception->getMessage());
                $error = 'Message could not be sent. Please check your database setup.';
            }
        }
    }

    $receiverId = $postedReceiverId;

    if ($receiverId > 0 && $receiverId !== $currentUserId) {
        try {
            $stmt = $pdo->prepare("
                SELECT id, username
                FROM users
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$receiverId]);
            $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $exception) {
            $receiver = null;
        }
    }
}

/*
|--------------------------------------------------------------------------
| LOAD MESSAGES FOR SELECTED CONVERSATION
|--------------------------------------------------------------------------
*/
if ($receiverId > 0 && $receiver) {
    try {
        $stmt = $pdo->prepare("
            SELECT
                id,
                sender_id,
                receiver_id,
                message_text,
                created_at
            FROM user_messages
            WHERE
                (sender_id = ? AND receiver_id = ?)
                OR
                (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at ASC, id ASC
        ");

        $stmt->execute([
            $currentUserId,
            $receiverId,
            $receiverId,
            $currentUserId
        ]);

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $exception) {
        error_log('Load messages error: ' . $exception->getMessage());

        if ($error === '') {
            $error = 'Unable to load this conversation.';
        }
    }
}

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/
if (function_exists('csrfToken')) {
    $csrfTokenValue = csrfToken();
} else {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $csrfTokenValue = $_SESSION['csrf_token'];
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
.messages-page {
    max-width: 1200px;
    margin: 35px auto;
    padding: 0 20px 40px;
    color: #4d2635;
}

.messages-heading {
    margin-bottom: 24px;
}

.messages-heading h1 {
    margin: 0 0 8px;
    color: #a52e52;
    font-family: Georgia, serif;
    font-size: clamp(28px, 4vw, 38px);
}

.messages-heading p {
    margin: 0;
    color: #86616f;
}

.messages-layout {
    display: grid;
    grid-template-columns: 330px minmax(0, 1fr);
    min-height: 610px;
    overflow: hidden;
    border: 1px solid #f0d4df;
    border-radius: 20px;
    background: #fffafa;
    box-shadow: 0 12px 35px rgba(117, 37, 67, 0.09);
}

.messages-sidebar {
    padding: 22px;
    border-right: 1px solid #f0d4df;
    background: #fff4f7;
}

.messages-sidebar h2 {
    margin: 0 0 16px;
    color: #8e2948;
    font-family: Georgia, serif;
    font-size: 21px;
}

.message-search-form {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
}

.message-search-form input {
    width: 100%;
    min-width: 0;
    padding: 11px 12px;
    border: 1px solid #e8c4d1;
    border-radius: 10px;
    outline: none;
    background: #fff;
    color: #4d2635;
}

.message-search-form input:focus {
    border-color: #c64b70;
    box-shadow: 0 0 0 3px rgba(198, 75, 112, 0.12);
}

.message-search-form button,
.send-message-form button {
    padding: 11px 16px;
    border: none;
    border-radius: 10px;
    background: #b63259;
    color: white;
    cursor: pointer;
    transition: background 0.2s ease;
}

.message-search-form button:hover,
.send-message-form button:hover {
    background: #87213f;
}

.sidebar-section-title {
    margin: 20px 0 10px;
    color: #9d526b;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.sidebar-user-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid transparent;
    border-radius: 12px;
    color: inherit;
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s;
}

.sidebar-user:hover,
.sidebar-user.active {
    border-color: #f0c8d6;
    background: #ffe7ef;
}

.sidebar-avatar {
    display: flex;
    flex: 0 0 42px;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #bd4d70;
    color: #fff;
    font-family: Georgia, serif;
    font-size: 19px;
}

.sidebar-user-info {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 4px;
}

.sidebar-user-info strong {
    overflow-wrap: anywhere;
    color: #70243f;
    font-size: 14px;
}

.sidebar-user-info small {
    color: #9c7281;
    font-size: 12px;
}

.messages-empty {
    padding: 12px;
    color: #9b7885;
    font-size: 13px;
    line-height: 1.6;
}

.messages-main {
    display: flex;
    min-width: 0;
    flex-direction: column;
    background: #fffdfd;
}

.messages-chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    border-bottom: 1px solid #f1dce3;
    background: #fff8fa;
}

.messages-chat-header h2 {
    margin: 0;
    color: #8e2948;
    font-family: Georgia, serif;
    font-size: 21px;
    overflow-wrap: anywhere;
}

.messages-chat-header p {
    margin: 5px 0 0;
    color: #9b7885;
    font-size: 12px;
}

.messages-chat-body {
    display: flex;
    flex: 1;
    min-height: 350px;
    flex-direction: column;
    gap: 14px;
    padding: 24px;
    overflow-wrap: anywhere;
}

.chat-message {
    max-width: min(75%, 480px);
    padding: 12px 15px;
    border: 1px solid #f0d6e0;
    border-radius: 15px 15px 15px 4px;
    background: #fff0f5;
    color: #54283a;
    line-height: 1.55;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.chat-message.mine {
    align-self: flex-end;
    border-color: #b63259;
    border-radius: 15px 15px 4px 15px;
    background: #b63259;
    color: #fff;
}

.chat-message small {
    display: block;
    margin-top: 7px;
    color: inherit;
    font-size: 10px;
    opacity: 0.75;
}

.send-message-form {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    padding: 18px 24px;
    border-top: 1px solid #f1dce3;
    background: #fff8fa;
}

.send-message-form textarea {
    flex: 1;
    min-width: 0;
    min-height: 46px;
    max-height: 140px;
    padding: 12px 14px;
    resize: vertical;
    border: 1px solid #e8c4d1;
    border-radius: 12px;
    outline: none;
    background: white;
    color: #4d2635;
    font: inherit;
}

.send-message-form textarea:focus {
    border-color: #c64b70;
}

.send-message-form button {
    min-height: 46px;
}

.messages-alert {
    margin-bottom: 18px;
    padding: 13px 16px;
    border: 1px solid #edc5d0;
    border-radius: 10px;
    background: #fff0f3;
    color: #8e2948;
}

.messages-welcome {
    display: flex;
    flex: 1;
    min-height: 400px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    text-align: center;
}

.messages-welcome h2 {
    margin-bottom: 8px;
    color: #a52e52;
    font-family: Georgia, serif;
}

.messages-welcome p {
    max-width: 340px;
    color: #9b7885;
    line-height: 1.7;
}

.messages-symbol {
    margin-bottom: 15px;
    color: #c64b70;
    font-family: Georgia, serif;
    font-size: 44px;
}

@media (max-width: 760px) {
    .messages-layout {
        grid-template-columns: 1fr;
    }

    .messages-sidebar {
        border-right: none;
        border-bottom: 1px solid #f0d4df;
    }

    .messages-chat-body {
        min-height: 280px;
        padding: 16px;
    }

    .messages-chat-header {
        padding: 16px;
    }

    .send-message-form {
        padding: 14px;
    }

    .chat-message {
        max-width: 90%;
    }
}
</style>

<main class="messages-page">
    <header class="messages-heading">
        <h1>Private Messages</h1>
        <p>A little space for meaningful conversations.</p>
    </header>

    <?php if ($error !== ''): ?>
        <div class="messages-alert" role="alert">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="messages-layout">
        <aside class="messages-sidebar">
            <h2>Find someone</h2>

            <form method="GET" action="messages.php" class="message-search-form">
                <input
                    type="search"
                    name="q"
                    value="<?= e($search) ?>"
                    placeholder="Search username..."
                    aria-label="Search username"
                    required
                >
                <button type="submit">Search</button>
            </form>

            <?php if ($search !== ''): ?>
                <div class="sidebar-section-title">Search results</div>

                <div class="sidebar-user-list">
                    <?php if (empty($searchResults)): ?>
                        <p class="messages-empty">
                            No accounts found for
                            "<?= e($search) ?>". Check the username and try again.
                        </p>
                    <?php else: ?>
                        <?php foreach ($searchResults as $person): ?>
                            <a
                                class="sidebar-user <?= $receiverId === (int) $person['id'] ? 'active' : '' ?>"
                                href="messages.php?to=<?= (int) $person['id'] ?>"
                            >
                                <span class="sidebar-avatar">
                                    <?= e(strtoupper(substr($person['username'], 0, 1))) ?>
                                </span>

                                <span class="sidebar-user-info">
                                    <strong><?= e($person['username']) ?></strong>
                                    <small>Start a conversation</small>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="sidebar-section-title">Your conversations</div>

            <div class="sidebar-user-list">
                <?php if (empty($conversations)): ?>
                    <p class="messages-empty">
                        No conversations yet. Search for a registered account above
                        to start a conversation.
                    </p>
                <?php else: ?>
                    <?php foreach ($conversations as $person): ?>
                        <a
                            class="sidebar-user <?= $receiverId === (int) $person['id'] ? 'active' : '' ?>"
                            href="messages.php?to=<?= (int) $person['id'] ?>"
                        >
                            <span class="sidebar-avatar">
                                <?= e(strtoupper(substr($person['username'], 0, 1))) ?>
                            </span>

                            <span class="sidebar-user-info">
                                <strong><?= e($person['username']) ?></strong>
                                <small>Open conversation</small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>

        <section class="messages-main">
            <?php if ($receiver): ?>
                <header class="messages-chat-header">
                    <span class="sidebar-avatar">
                        <?= e(strtoupper(substr($receiver['username'], 0, 1))) ?>
                    </span>

                    <div>
                        <h2><?= e($receiver['username']) ?></h2>
                        <p>Private conversation</p>
                    </div>
                </header>

                <div class="messages-chat-body">
                    <?php if (empty($messages)): ?>
                        <p class="messages-empty">
                            This is the beginning of your conversation.
                            Send the first message below.
                        </p>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <?php $isMine = (int) $message['sender_id'] === $currentUserId; ?>

                            <div class="chat-message <?= $isMine ? 'mine' : '' ?>">
                                <?= e($message['message_text']) ?>

                                <small>
                                    <?= e(date('M j, Y · g:i A', strtotime($message['created_at']))) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form method="POST" action="messages.php?to=<?= (int) $receiverId ?>" class="send-message-form">
                    <input
                        type="hidden"
                        name="receiver_id"
                        value="<?= (int) $receiverId ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrfTokenValue) ?>"
                    >

                    <textarea
                        name="message_text"
                        placeholder="Write your message..."
                        aria-label="Write your message"
                        maxlength="5000"
                        required
                    ></textarea>

                    <button type="submit" name="send_message" value="1">
                        Send
                    </button>
                </form>
            <?php else: ?>
                <div class="messages-welcome">
                    <div class="messages-symbol">M</div>
                    <h2>Your message space</h2>
                    <p>
                        Search for another registered account on the left.
                        Select a username to open a private conversation.
                    </p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
