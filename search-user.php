
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
$searchTerm = trim($_GET['q'] ?? '');
$users = [];
$errorMessage = '';

if ($searchTerm !== '') {
    try {
        $stmt = $pdo->prepare("
            SELECT id, username
            FROM users
            WHERE id != :current_user
              AND username LIKE :search_term
            ORDER BY username ASC
            LIMIT 50
        ");

        $stmt->execute([
            'current_user' => $currentUserId,
            'search_term' => '%' . $searchTerm . '%'
        ]);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Search User Error: ' . $e->getMessage());
        $errorMessage = 'Something went wrong. Please try again.';
    }
}

function searchUserEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search People | Mind Capsule</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .search-user-page {
            width: min(900px, calc(100% - 32px));
            margin: 45px auto;
            color: #542b3c;
        }

        .search-user-heading {
            margin-bottom: 8px;
            font-size: clamp(28px, 5vw, 40px);
            font-weight: 800;
        }

        .search-user-description {
            color: #936477;
            line-height: 1.7;
            margin-bottom: 26px;
        }

        .search-user-panel {
            padding: 25px;
            background: #fff8fb;
            border: 1px solid #f2d4e0;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(116, 45, 77, 0.08);
        }

        .search-user-form {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .search-user-input {
            flex: 1;
            min-width: 0;
            padding: 14px 16px;
            border: 1px solid #e8bfd0;
            border-radius: 13px;
            background: white;
            color: #542b3c;
            font-size: 15px;
            outline: none;
        }

        .search-user-input:focus {
            border-color: #c94f7d;
            box-shadow: 0 0 0 3px rgba(201, 79, 125, 0.12);
        }

        .search-user-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 19px;
            border: none;
            border-radius: 13px;
            background: #c94f7d;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .search-user-button:hover {
            background: #a93461;
        }

        .search-user-button svg {
            width: 19px;
            height: 19px;
        }

        .search-user-results-heading {
            margin: 28px 0 15px;
            font-size: 20px;
        }

        .search-user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .search-user-card {
            padding: 20px;
            background: white;
            border: 1px solid #f0d5e0;
            border-radius: 17px;
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .search-user-avatar {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f7dce7;
            color: #a93461;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .search-user-info {
            min-width: 0;
            flex: 1;
        }

        .search-user-name {
            margin: 0 0 12px;
            overflow-wrap: anywhere;
            font-size: 16px;
        }

        .search-user-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .search-user-link {
            display: inline-block;
            padding: 8px 11px;
            border-radius: 9px;
            background: #f9e6ee;
            color: #96385e;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }

        .search-user-link:hover {
            background: #f2ccdc;
        }

        .search-user-link.message {
            background: #c94f7d;
            color: white;
        }

        .search-user-link.message:hover {
            background: #a93461;
        }

        .search-user-notice {
            margin-top: 20px;
            padding: 15px;
            border-radius: 12px;
            background: #fff0f5;
            color: #85415c;
            line-height: 1.6;
        }

        .search-user-back {
            display: inline-block;
            margin-top: 22px;
            color: #a93461;
            text-decoration: none;
            font-weight: 700;
        }

        .search-user-back:hover {
            text-decoration: underline;
        }

        @media (max-width: 520px) {
            .search-user-page {
                margin: 25px auto;
            }

            .search-user-panel {
                padding: 17px;
            }

            .search-user-form {
                flex-direction: column;
            }

            .search-user-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <main class="search-user-page">
        <h1 class="search-user-heading">Search People</h1>

        <p class="search-user-description">
            Find people in Mind Capsule and connect with other members.
        </p>

        <section class="search-user-panel">
            <form class="search-user-form" method="GET" action="">
                <input
                    class="search-user-input"
                    type="search"
                    name="q"
                    placeholder="Search by username..."
                    value="<?= searchUserEscape($searchTerm) ?>"
                    aria-label="Search users by username"
                    maxlength="100"
                    required
                >

                <button class="search-user-button" type="submit">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>
                    Search
                </button>
            </form>

            <?php if ($errorMessage !== ''): ?>
                <p class="search-user-notice">
                    <?= searchUserEscape($errorMessage) ?>
                </p>

            <?php elseif ($searchTerm === ''): ?>
                <p class="search-user-notice">
                    Enter a username above to find other members.
                </p>

            <?php else: ?>
                <h2 class="search-user-results-heading">
                    Search results for
                    “<?= searchUserEscape($searchTerm) ?>”
                </h2>

                <?php if (count($users) > 0): ?>
                    <p>
                        <?= count($users) ?> matching
                        <?= count($users) === 1 ? 'user' : 'users' ?> found.
                    </p>

                    <div class="search-user-grid">
                        <?php foreach ($users as $user): ?>
                            <article class="search-user-card">
                                <div class="search-user-avatar">
                                    <?= searchUserEscape(
                                        mb_substr($user['username'], 0, 1)
                                    ) ?>
                                </div>

                                <div class="search-user-info">
                                    <h3 class="search-user-name">
                                        @<?= searchUserEscape($user['username']) ?>
                                    </h3>

                                    <div class="search-user-actions">
                                        <a
                                            class="search-user-link"
                                            href="profile.php?id=<?= (int) $user['id'] ?>"
                                        >
                                            View Profile
                                        </a>

                                        <a
                                            class="search-user-link message"
                                            href="messages.php?to=<?= (int) $user['id'] ?>"
                                        >
                                            Message
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <p class="search-user-notice">
                        No users found for
                        “<?= searchUserEscape($searchTerm) ?>”.
                        Try another username.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <a class="search-user-back" href="index.php">
            &larr; Back to Home
        </a>
    </main>
</body>
</html>
