
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
