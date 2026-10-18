
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$userId = (int) $_SESSION['user_id'];
$error = '';

$categories = [
    'School Life',
    'Personal Growth',
    'Family and Friends',
    'A Small Win',
    'Just Sharing'
];

// CREATE POST
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['form_action'] ?? '') === 'create_post'
) {
    verifyCsrf();

    $category = trim((string) ($_POST['category'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    if (!in_array($category, $categories, true)) {
        $error = 'Please select a valid category.';
    } elseif (
        $content === ''
        || mb_strlen($content) > 1000
    ) {
        $error = 'Your post must contain 1–1,000 characters.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO posts
                (user_id, category, content, is_anonymous, status)
             VALUES (?, ?, ?, ?, 'approved')"
        );

        $stmt->execute([
            $userId,
            $category,
            $content,
            $anonymous
        ]);

        // The same post will appear on the wall and the author's profile.
        header('Location: freedom-wall.php?submitted=1');
        exit;
    }
}

// GET ALL PUBLISHED POSTS FOR THE FREEDOM WALL
$stmt = $pdo->prepare(
    "SELECT p.id, p.user_id, p.category, p.content,
            p.is_anonymous, p.created_at,
            (SELECT COUNT(*)
             FROM post_likes l
             WHERE l.post_id = p.id) AS like_count,
            (SELECT COUNT(*)
             FROM post_comments c
             WHERE c.post_id = p.id
               AND c.parent_id IS NULL) AS comment_count,
            EXISTS(
                SELECT 1
                FROM post_likes l
                WHERE l.post_id = p.id
                  AND l.user_id = ?
            ) AS liked
     FROM posts p
     WHERE p.status = 'approved'
     ORDER BY p.created_at DESC
     LIMIT 50"
);

$stmt->execute([$userId]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DISPLAY COMMENTS AND REPLIES
function renderComments(
    PDO $pdo,
    int $postId,
    int $userId,
    ?int $parentId = null,
    int $depth = 0
): void {
    if ($depth > 3) {
        return;
    }

    if ($parentId === null) {
        $stmt = $pdo->prepare(
            "SELECT c.id, c.user_id, c.content, c.created_at,
                    u.username
             FROM post_comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
               AND c.parent_id IS NULL
             ORDER BY c.created_at ASC"
        );

        $stmt->execute([$postId]);
    } else {
        $stmt = $pdo->prepare(
            "SELECT c.id, c.user_id, c.content, c.created_at,
                    u.username
             FROM post_comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
               AND c.parent_id = ?
             ORDER BY c.created_at ASC"
        );

        $stmt->execute([$postId, $parentId]);
    }

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $comment) {
        $commentId = (int) $comment['id'];
        $commentUserId = (int) $comment['user_id'];

        echo '<div class="fw-comment" style="margin-left:'
            . ($depth * 14) . 'px">';

        echo '<div class="fw-comment-meta">';
        echo '<strong>'
            . e($comment['username'] ?? 'Member')
            . '</strong>';

        echo '<span>'
            . e(date(
                'M j, Y · g:i A',
                strtotime($comment['created_at'])
            ))
            . '</span>';

        echo '</div>';

        echo '<p>' . nl2br(e($comment['content'])) . '</p>';

        if ($depth < 3) {
            echo '<form method="post" action="comments.php" class="fw-reply-form">';
            echo '<input type="hidden" name="csrf_token" value="'
                . e(csrfToken()) . '">';
            echo '<input type="hidden" name="action" value="add">';
            echo '<input type="hidden" name="post_id" value="' . $postId . '">';
            echo '<input type="hidden" name="parent_id" value="' . $commentId . '">';
            echo '<input type="hidden" name="redirect" value="freedom-wall.php">';
            echo '<input name="content" maxlength="1000" placeholder="Write a reply..." required>';
            echo '<button type="submit" class="fw-small-btn">Reply</button>';
            echo '</form>';
        }

        if ($commentUserId === $userId) {
            echo '<form method="post" action="comments.php" class="fw-delete-form" '
                . 'onsubmit="return confirm(\'Delete this comment?\')">';

            echo '<input type="hidden" name="csrf_token" value="'
                . e(csrfToken()) . '">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="comment_id" value="' . $commentId . '">';
            echo '<input type="hidden" name="redirect" value="freedom-wall.php">';
            echo '<button type="submit" class="fw-delete-btn">Delete</button>';
            echo '</form>';
        }

        renderComments(
            $pdo,
            $postId,
            $userId,
            $commentId,
            $depth + 1
        );

        echo '</div>';
    }
}

$pageTitle = 'Freedom Wall';
include __DIR__ . '/includes/header.php';
?>

<section class="section freedom-wall">
    <div class="container fw-container">

        <header class="fw-hero">
            <span class="fw-symbol">୨୧ ── ♡ ── ୨୧</span>
            <p class="fw-eyebrow">A LITTLE CORNER OF THE INTERNET</p>

            <h1>Your feelings deserve a <span>space.</span></h1>

            <p class="fw-intro">
                A gentle place for your thoughts, little victories,
                and everything your heart wants to share.
            </p>

            <div class="fw-divider">
                <span>✧</span>
                <span>WORDS WITHOUT JUDGMENT</span>
                <span>✧</span>
            </div>
        </header>

        <?php if (isset($_GET['submitted'])): ?>
            <div class="fw-alert fw-success">
                <span class="fw-alert-icon">♡</span>
                <div>
                    <strong>Your words are now on the wall!</strong>
                    <p>Your post has been published successfully.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="fw-alert fw-error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="fw-layout">

            <aside class="fw-compose-column">
                <div class="fw-card fw-compose">

                    <div class="fw-card-heading">
                        <span class="fw-mini-symbol">୨୧</span>
                        <div>
                            <p class="fw-kicker">YOUR PERSONAL SPACE</p>
                            <h2>Let it out, darling.</h2>
                        </div>
                    </div>

                    <p class="fw-description">
                        No perfect words needed. Start wherever you are.
                    </p>

                    <form method="post" action="freedom-wall.php">
                        <input type="hidden" name="csrf_token"
                               value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="form_action"
                               value="create_post">

                        <div class="fw-field">
                            <label for="category">
                                CHOOSE YOUR CHAPTER <span>♡</span>
                            </label>

                            <select id="category" name="category" required>
                                <?php foreach ($categories as $item): ?>
                                    <option value="<?= e($item) ?>">
                                        <?= e($item) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="fw-field">
                            <label for="content">
                                WHAT'S ON YOUR MIND? <span>✧</span>
                            </label>

                            <textarea
                                id="content"
                                name="content"
                                maxlength="1000"
                                required
                                placeholder="Dear diary, today I feel..."
                            ></textarea>

                            <div class="fw-character-note">
                                <span>♡ take your time</span>
                                <span>1,000 characters max</span>
                            </div>
                        </div>

                        <label class="fw-checkbox">
                            <input type="checkbox" name="is_anonymous"
                                   value="1" checked>
                            <span>Post anonymously</span>
                        </label>

                        <div class="fw-notice">
                            <span class="fw-notice-symbol">✧</span>
                            <p>
                                Protect your privacy. Avoid sharing names,
                                addresses, or contact details. Your post
                                will be published immediately. Your account
                                is still recorded internally.
                            </p>
                        </div>

                        <button class="fw-submit" type="submit">
                            <span>Send your thoughts</span>
                            <span class="fw-button-symbol">♡</span>
                        </button>

                        <p class="fw-bottom-note">
                            made with care, for the things left unsaid ♡
                        </p>
                    </form>
                </div>

                <div class="fw-side-note">
                    <span class="fw-side-quote">“</span>
                    <p>You are allowed to feel everything you feel.</p>
                    <span class="fw-side-caption">A LITTLE REMINDER FOR YOU</span>
                    <span class="fw-side-decoration">˚₊‧♡‧₊˚</span>
                </div>
            </aside>

            <main class="fw-community">
                <div class="fw-community-heading">
                    <div>
                        <p class="fw-kicker">LETTERS FROM THE HEART</p>
                        <h2>The freedom wall<span>.</span></h2>
                    </div>

                    <span class="fw-wall-symbol">♡</span>
                </div>

                <p class="fw-community-intro">
                    Little thoughts, big feelings, and stories worth hearing.
                </p>

                <div class="fw-post-list">
                    <?php if (!$posts): ?>
                        <div class="fw-empty">
                            <span class="fw-empty-symbol">୨୧</span>
                            <h3>A blank page, for now.</h3>
                            <p>
                                No thoughts have been shared publicly yet.
                                Maybe your words will be the first to bloom.
                            </p>
                            <span class="fw-empty-decoration">˚₊‧♡‧₊˚</span>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($posts as $post): ?>
                        <article class="fw-post">
                            <div class="fw-post-top">
                                <span class="fw-post-category">
                                    ♡ <?= e($post['category']) ?>
                                </span>

                                <time datetime="<?= e(date(
                                    'c',
                                    strtotime($post['created_at'])
                                )) ?>">
                                    <?= e(date(
                                        'M j, Y',
                                        strtotime($post['created_at'])
                                    )) ?>
                                </time>
                            </div>

                            <div class="fw-author">
                                <span class="fw-avatar">
                                    <?= (int) $post['is_anonymous'] === 1 ? '♡' : 'M' ?>
                                </span>

                                <div>
                                    <strong>
                                        <?= (int) $post['is_anonymous'] === 1
                                            ? 'Anonymous'
                                            : 'Community member' ?>
                                    </strong>
                                    <small>A thought shared with the community</small>
                                </div>
                            </div>

                            <p class="fw-post-content"><?= nl2br(e($post['content'])) ?></p>

                            <div class="fw-actions">
                                <form method="post" action="post-action.php">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="post_id"
                                           value="<?= (int) $post['id'] ?>">
                                    <input type="hidden" name="redirect"
                                           value="freedom-wall.php">
                                    <input type="hidden" name="action"
                                           value="<?= (int) $post['liked'] ? 'unlike' : 'like' ?>">

                                    <button class="fw-action-btn <?= (int) $post['liked'] ? 'is-liked' : '' ?>"
                                            type="submit">
                                        <?= (int) $post['liked'] ? '♥ Liked' : '♡ Like' ?>
                                        <span><?= (int) $post['like_count'] ?></span>
                                    </button>
                                </form>

                                <span class="fw-action-static">
                                    ♡ <?= (int) $post['comment_count'] ?> comments
                                </span>
                            </div>

                            <div class="fw-comments">
                                <h3>Conversation <span>♡</span></h3>

                                <?php renderComments(
                                    $pdo,
                                    (int) $post['id'],
                                    $userId
                                ); ?>

                                <form method="post" action="comments.php"
                                      class="fw-comment-form">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="post_id"
                                           value="<?= (int) $post['id'] ?>">
                                    <input type="hidden" name="redirect"
                                           value="freedom-wall.php">

                                    <input name="content" maxlength="1000"
                                           placeholder="Leave a kind comment..."
                                           required>

                                    <button type="submit" class="fw-button">
                                        Comment
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div class="fw-community-footer">
                    <span>୨୧</span>
                    every little feeling has a place here.
                    <span>୨୧</span>
                </div>
            </main>

        </div>

        <footer class="fw-page-footer">
            <span>♡</span>
            <p>be gentle with yourself. you're still becoming.</p>
            <span>♡</span>
        </footer>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
