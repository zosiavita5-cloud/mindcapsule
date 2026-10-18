
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: freedom-wall.php');
    exit;
}

verifyCsrf();

$userId = (int) $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

if (!$postId || $postId < 1) {
    header('Location: freedom-wall.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id FROM posts WHERE id = ? AND status = 'approved'"
    );
    $stmt->execute([$postId]);

    if (!$stmt->fetch()) {
        header('Location: freedom-wall.php');
        exit;
    }

    if ($action === 'like') {
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO post_likes (post_id, user_id) VALUES (?, ?)'
        );
        $stmt->execute([$postId, $userId]);
    } elseif ($action === 'unlike') {
        $stmt = $pdo->prepare(
            'DELETE FROM post_likes WHERE post_id = ? AND user_id = ?'
        );
        $stmt->execute([$postId, $userId]);
    }
} catch (PDOException $exception) {
    error_log($exception->getMessage());
}

$redirect = $_POST['redirect'] ?? 'freedom-wall.php';
$allowed = ['freedom-wall.php', 'profile.php'];

if (!in_array($redirect, $allowed, true)) {
    $redirect = 'freedom-wall.php';
}

header('Location: ' . $redirect);
exit;
