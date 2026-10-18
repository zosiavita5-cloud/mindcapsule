
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
    } elseif ($content === '' || mb_strlen($content) > 1000) {
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

        header('Location: freedom-wall.php?submitted=1');
        exit;
    }
}

// GET ALL PUBLISHED POSTS
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
function getComments(PDO $pdo, int $postId, ?int $parentId = null): array
{
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
                    c.parent_id, u.username
             FROM post_comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
               AND c.parent_id = ?
             ORDER BY c.created_at ASC"
        );

        $stmt->execute([$postId, $parentId]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

    $comments = getComments($pdo, $postId, $parentId);

    foreach ($comments as $comment) {
        $commentId = (int) $comment['id'];
        $commentUserId = (int) $comment['user_id'];

        // KEEP THE REST OF YOUR EXISTING COMMENT DISPLAY CODE HERE.
    }
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
                    c.parent_id, u.username
             FROM post_comments c
             LEFT JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
               AND c.parent_id = ?
             ORDER BY c.created_at ASC"
        );

        $stmt->execute([$postId, $parentId]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<style>
/* MIND CAPSULE — FREEDOM WALL ONLY */
.freedom-wall {
    padding: 45px 20px 60px;
    background: linear-gradient(180deg, #fff0f4 0%, #fff9fb 35%, #fff5f8 100%);
    min-height: 100vh;
    color: #654352;
}
.freedom-wall, .freedom-wall * {
    box-sizing: border-box;
}
.freedom-wall .fw-container {
    max-width: 1250px;
    width: 100%;
    margin: 0 auto;
}
.freedom-wall .fw-hero {
    text-align: center;
    padding: 45px 20px;
    margin-bottom: 35px;
}
.freedom-wall .fw-symbol {
    display: block;
    color: #c77b98;
    font-size: 21px;
    margin-bottom: 18px;
}
.freedom-wall .fw-eyebrow,
.freedom-wall .fw-kicker {
    color: #a45a76;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2.5px;
    margin: 0 0 12px;
}
.freedom-wall .fw-hero h1 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: clamp(34px, 5vw, 58px);
    font-weight: 500;
    line-height: 1.2;
    margin: 12px 0 18px;
}
.freedom-wall .fw-hero h1 span {
    color: #c72c50;
    font-style: italic;
}
.freedom-wall .fw-intro {
    max-width: 600px;
    margin: 0 auto;
    color: #906b7b;
    font-size: 15px;
    line-height: 1.9;
}
.freedom-wall .fw-divider {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-top: 28px;
    color: #b96b87;
    font-size: 10px;
    letter-spacing: 2px;
}
.freedom-wall .fw-layout {
    display: grid;
    grid-template-columns: minmax(280px, .85fr) minmax(0, 1.4fr);
    align-items: start;
    gap: 28px;
}
.freedom-wall .fw-compose-column,
.freedom-wall .fw-community {
    min-width: 0;
}
.freedom-wall .fw-compose-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.freedom-wall .fw-card,
.freedom-wall .fw-post,
.freedom-wall .fw-empty,
.freedom-wall .fw-community {
    background: rgba(255, 255, 255, .96);
    border: 1px solid #efd0db;
    border-radius: 22px;
    box-shadow: 0 12px 35px rgba(127, 32, 60, .075);
}
.freedom-wall .fw-card {
    padding: 28px;
}
.freedom-wall .fw-card-heading {
    display: flex;
    align-items: center;
    gap: 14px;
}
.freedom-wall .fw-mini-symbol {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: #fff0f4;
    color: #bd5276;
    font-size: 24px;
}
.freedom-wall .fw-card-heading h2,
.freedom-wall .fw-community-heading h2 {
    margin: 0;
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 25px;
    font-weight: 500;
}
.freedom-wall .fw-description,
.freedom-wall .fw-community-intro {
    color: #987888;
    font-size: 13px;
    line-height: 1.8;
    margin: 16px 0 24px;
}
.freedom-wall .fw-field {
    margin: 22px 0;
}
.freedom-wall .fw-field label {
    display: block;
    color: #85415c;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    margin-bottom: 10px;
}
.freedom-wall .fw-field label span {
    color: #c72c50;
}
.freedom-wall .fw-field select,
.freedom-wall .fw-field textarea,
.freedom-wall .fw-comment-form input,
.freedom-wall .fw-reply-form input {
    box-sizing: border-box;
    width: 100%;
    min-width: 0;
    border: 1px solid #ebcbd7;
    border-radius: 12px;
    background: #fff9fb;
    color: #654352;
    padding: 13px 14px;
    font: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.freedom-wall .fw-field select:focus,
.freedom-wall .fw-field textarea:focus,
.freedom-wall .fw-comment-form input:focus,
.freedom-wall .fw-reply-form input:focus {
    border-color: #c77b98;
    box-shadow: 0 0 0 3px rgba(199, 123, 152, .13);
}
.freedom-wall .fw-field textarea {
    min-height: 150px;
    resize: vertical;
    line-height: 1.8;
}
.freedom-wall .fw-field textarea::placeholder,
.freedom-wall .fw-comment-form input::placeholder,
.freedom-wall .fw-reply-form input::placeholder {
    color: #bd9eac;
}
.freedom-wall .fw-character-note {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 9px;
    color: #ad8798;
    font-size: 10px;
}
.freedom-wall .fw-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #80566a;
    font-size: 12px;
    cursor: pointer;
}
.freedom-wall .fw-checkbox input {
    accent-color: #b53c60;
    width: 16px;
    height: 16px;
}
.freedom-wall .fw-notice {
    display: flex;
    gap: 12px;
    padding: 14px;
    margin: 20px 0;
    background: #fff0f4;
    border: 1px solid #f2d5df;
    border-radius: 13px;
}
.freedom-wall .fw-notice-symbol {
    color: #bd5276;
    font-size: 18px;
}
.freedom-wall .fw-notice p {
    margin: 0;
    color: #936b7d;
    font-size: 11px;
    line-height: 1.8;
}
.freedom-wall .fw-submit,
.freedom-wall .fw-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    border: 1px solid transparent;
    border-radius: 12px;
    background: linear-gradient(135deg, #c72c50, #8b2345);
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: transform .2s, box-shadow .2s, background .2s;
}
.freedom-wall .fw-submit {
    width: 100%;
    padding: 15px 18px;
    margin-top: 20px;
    font-size: 13px;
}
.freedom-wall .fw-button {
    padding: 11px 17px;
    font-size: 12px;
    flex-shrink: 0;
}
.freedom-wall .fw-submit:hover,
.freedom-wall .fw-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(127, 32, 60, .2);
    background: linear-gradient(135deg, #a51d3d, #791c38);
    color: #fff;
}
.freedom-wall .fw-button-symbol {
    font-size: 17px;
}
.freedom-wall .fw-bottom-note {
    text-align: center;
    color: #b08a9b;
    font-size: 10px;
    margin: 17px 0 0;
}
.freedom-wall .fw-side-note {
    position: relative;
    overflow: hidden;
    padding: 25px;
    margin-top: 0;
    border: 1px solid #efd0db;
    border-radius: 20px;
    background: linear-gradient(135deg, #fbe3eb, #fff6f8);
}
.freedom-wall .fw-side-quote {
    color: #c77b98;
    font-family: Georgia, serif;
    font-size: 55px;
    line-height: .8;
}
.freedom-wall .fw-side-note p {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 20px;
    line-height: 1.6;
}
.freedom-wall .fw-side-caption {
    color: #a45a76;
    font-size: 9px;
    letter-spacing: 1.5px;
}
.freedom-wall .fw-side-decoration {
    display: block;
    text-align: right;
    color: #c77b98;
    margin-top: 15px;
}
.freedom-wall .fw-community {
    padding: 27px;
}
.freedom-wall .fw-community-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}
.freedom-wall .fw-community-heading h2 {
    font-size: 32px;
}
.freedom-wall .fw-community-heading h2 span {
    color: #c72c50;
}
.freedom-wall .fw-wall-symbol {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #f9dfe7;
    color: #b53c60;
    font-size: 24px;
}
.freedom-wall .fw-community-intro {
    margin-top: 10px;
}
.freedom-wall .fw-post-list {
    display: grid;
    gap: 20px;
}
.freedom-wall .fw-post {
    padding: 24px;
    min-width: 0;
    transition: transform .2s, box-shadow .2s;
}
.freedom-wall .fw-post:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 35px rgba(127, 32, 60, .11);
}
.freedom-wall .fw-post-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.freedom-wall .fw-post-category {
    padding: 7px 12px;
    border-radius: 30px;
    background: #fff0f4;
    color: #a13f61;
    font-size: 10px;
    font-weight: 700;
}
.freedom-wall .fw-post-top time {
    color: #ae8b9b;
    font-size: 10px;
}
.freedom-wall .fw-author {
    display: flex;
    align-items: center;
    gap: 12px;
}
.freedom-wall .fw-avatar {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f8d5e1, #fcebf1);
    color: #a6385c;
    font-size: 21px;
}
.freedom-wall .fw-author strong {
    display: block;
    color: #7f203c;
    font-size: 13px;
}
.freedom-wall .fw-author small {
    display: block;
    color: #ad8999;
    font-size: 10px;
    margin-top: 4px;
}
.freedom-wall .fw-post-content {
    margin: 22px 0;
    color: #654352;
    font-size: 14px;
    line-height: 1.9;
    overflow-wrap: anywhere;
    white-space: normal;
}
.freedom-wall .fw-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding: 15px 0;
    border-top: 1px solid #f3dfe6;
    border-bottom: 1px solid #f3dfe6;
}
.freedom-wall .fw-actions form {
    margin: 0;
}
.freedom-wall .fw-action-btn,
.freedom-wall .fw-action-static {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 13px;
    border: 1px solid #efd0db;
    border-radius: 25px;
    background: #fff9fb;
    color: #9c4d6a;
    font-size: 11px;
    text-decoration: none;
}
.freedom-wall .fw-action-btn {
    cursor: pointer;
}
.freedom-wall .fw-action-btn:hover,
.freedom-wall .fw-action-btn.is-liked {
    background: #fbe3eb;
    border-color: #e4a6bb;
    color: #a3284e;
}
.freedom-wall .fw-action-btn span {
    font-weight: 700;
}
.freedom-wall .fw-comments {
    margin-top: 22px;
}
.freedom-wall .fw-comments h3 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 19px;
    font-weight: 500;
    margin: 0 0 18px;
}
.freedom-wall .fw-comments h3 span {
    color: #c72c50;
}
.freedom-wall .fw-comment {
    padding: 13px 14px;
    margin: 12px 0;
    border-left: 2px solid #e7b2c4;
    border-radius: 0 12px 12px 0;
    background: #fff7fa;
    overflow-wrap: anywhere;
}
.freedom-wall .fw-comment-meta {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}
.freedom-wall .fw-comment-meta strong {
    color: #8d3655;
    font-size: 11px;
}
.freedom-wall .fw-comment-meta span {
    color: #b08a9b;
    font-size: 10px;
}
.freedom-wall .fw-comment > p {
    color: #765364;
    font-size: 12px;
    line-height: 1.8;
    margin: 0 0 10px;
    overflow-wrap: anywhere;
}
.freedom-wall .fw-comment-form,
.freedom-wall .fw-reply-form {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 14px;
}
.freedom-wall .fw-comment-form input,
.freedom-wall .fw-reply-form input {
    flex: 1;
    padding: 11px 12px;
    font-size: 12px;
}
.freedom-wall .fw-small-btn {
    padding: 9px 12px;
    border: 1px solid #e7bdcc;
    border-radius: 10px;
    background: #fff;
    color: #963c5c;
    font-size: 11px;
    cursor: pointer;
}
.freedom-wall .fw-small-btn:hover {
    background: #fbe3eb;
    color: #7f203c;
}
.freedom-wall .fw-delete-form {
    margin: 8px 0;
}
.freedom-wall .fw-delete-btn {
    padding: 5px 10px;
    border: 1px solid transparent;
    border-radius: 8px;
    background: #fff0f2;
    color: #a42d4b;
    font-size: 10px;
    cursor: pointer;
}
.freedom-wall .fw-delete-btn:hover {
    background: #f8d8e1;
}
.freedom-wall .fw-empty {
    padding: 45px 25px;
    text-align: center;
}
.freedom-wall .fw-empty-symbol {
    color: #c77b98;
    font-size: 30px;
}
.freedom-wall .fw-empty h3 {
    color: #7f203c;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 24px;
    font-weight: 500;
}
.freedom-wall .fw-empty p {
    max-width: 380px;
    margin: 0 auto;
    color: #987888;
    font-size: 13px;
    line-height: 1.9;
}
.freedom-wall .fw-empty-decoration {
    display: block;
    margin-top: 22px;
    color: #c77b98;
}
.freedom-wall .fw-alert {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 16px 20px;
    margin-bottom: 24px;
    border-radius: 14px;
    font-size: 13px;
}
.freedom-wall .fw-success {
    background: #f0faf2;
    border: 1px solid #cce8d1;
    color: #326a42;
}
.freedom-wall .fw-error {
    background: #fff0f2;
    border: 1px solid #f0cbd4;
    color: #a42d4b;
}
.freedom-wall .fw-alert p {
    margin: 5px 0 0;
}
.freedom-wall .fw-alert-icon {
    font-size: 23px;
}
.freedom-wall .fw-community-footer,
.freedom-wall .fw-page-footer {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    color: #ad8194;
    text-align: center;
    font-size: 11px;
}
.freedom-wall .fw-community-footer {
    padding: 25px 10px;
}
.freedom-wall .fw-page-footer {
    margin-top: 45px;
    padding: 0;
    background: transparent;
    border: 0;
    box-shadow: none;
}
.freedom-wall .fw-page-footer p {
    margin: 0;
}

@media (max-width: 900px) {
    .freedom-wall .fw-layout {
        grid-template-columns: minmax(0, 1fr);
    }
    .freedom-wall .fw-compose-column {
        order: 1;
    }
    .freedom-wall .fw-community {
        order: 2;
    }
    .freedom-wall .fw-hero {
        padding: 25px 10px;
    }
}

@media (max-width: 520px) {
    .freedom-wall {
        padding: 25px 12px 40px;
    }
    .freedom-wall .fw-container {
        width: 100%;
    }
    .freedom-wall .fw-card,
    .freedom-wall .fw-post,
    .freedom-wall .fw-community {
        padding: 18px;
        border-radius: 17px;
    }
    .freedom-wall .fw-hero h1 {
        font-size: 35px;
    }
    .freedom-wall .fw-divider {
        gap: 7px;
        font-size: 8px;
        letter-spacing: 1px;
    }
    .freedom-wall .fw-community-heading h2 {
        font-size: 26px;
    }
    .freedom-wall .fw-comment-form,
    .freedom-wall .fw-reply-form {
        align-items: stretch;
    }
    .freedom-wall .fw-comment-form {
        flex-wrap: wrap;
    }
    .freedom-wall .fw-comment-form input {
        flex-basis: 100%;
    }
    .freedom-wall .fw-comment-form .fw-button {
        width: 100%;
    }
    .freedom-wall .fw-post-top {
        align-items: flex-start;
    }
}
</style>

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
