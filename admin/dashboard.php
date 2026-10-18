
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireLogin();

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';

    $statuses = [
        'approve' => 'approved',
        'reject' => 'rejected'
    ];

    if ($postId && isset($statuses[$action])) {
        $stmt = $pdo->prepare(
            'UPDATE posts SET status = ?
             WHERE id = ? AND status = ?'
        );

        $stmt->execute([
            $statuses[$action],
            $postId,
            'pending'
        ]);
    }

    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->query(
    "SELECT posts.id, posts.category, posts.content, posts.created_at,
            users.username
     FROM posts
     LEFT JOIN users ON posts.user_id = users.id
     WHERE posts.status = 'pending'
     ORDER BY posts.created_at ASC"
);

$pendingPosts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mind Capsule | Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="navbar">
        <a class="logo" href="../index.php">mind capsule<span>.</span></a>
        <nav>
            <a href="../index.php">View Website</a>
            <a href="../freedom-wall.php">Freedom Wall</a>
            <a href="../logout.php">Log out</a>
        </nav>
    </div>
</header>

<main>
<section class="section">
    <div class="container">
        <p class="eyebrow">Content management</p>
        <h1>Freedom Wall moderation</h1>
        <p>Review each submission before it becomes visible to the community.</p>

        <?php if (!$pendingPosts): ?>
            <div class="card">
                <p>There are no pending submissions.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($pendingPosts as $post): ?>
            <article class="card" style="margin-top:20px">
                <p class="notice">
                    Submission #<?= (int) $post['id'] ?> /
                    <?= e($post['category']) ?> /
                    <?= e($post['created_at']) ?>
                </p>

                <p><?= nl2br(e($post['content'])) ?></p>

                <form method="POST" action="dashboard.php"
                    style="display:flex;gap:12px;flex-wrap:wrap">
                    <input type="hidden" name="csrf_token"
                        value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="post_id"
                        value="<?= (int) $post['id'] ?>">

                    <button class="btn" name="action" value="approve"
                        type="submit">Approve</button>

                    <button class="btn btn-outline" name="action" value="reject"
                        type="submit">Reject</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>
</main>

<footer class="site-footer">
    <a class="logo footer-logo" href="../index.php">mind capsule.</a>
    <p>Administrator dashboard</p>
</footer>
</body>
</html>
