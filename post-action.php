
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

$redirect = $_POST['redirect'] ?? 'freedom-wall.php';
$allowed = ['freedom-wall.php', 'profile.php'];

if (!in_array($redirect, $allowed, true)) {
    $redirect = 'freedom-wall.php';
}

if (!$postId || $postId < 1) {
    header('Location: ' . $redirect);
    exit;
}

try {
    // Get the approved post and its owner.
    $stmt = $pdo->prepare(
        "SELECT id, user_id
         FROM posts
         WHERE id = ? AND status = 'approved'"
    );
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: ' . $redirect);
        exit;
    }

    $postOwnerId = (int) $post['user_id'];

    if ($action === 'like') {
        // INSERT IGNORE prevents duplicate likes when the table
        // has a unique constraint for post_id + user_id.
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO post_likes (post_id, user_id)
             VALUES (?, ?)'
        );
        $stmt->execute([$postId, $userId]);

        // Notify only when a new like was actually inserted.
        if ($stmt->rowCount() > 0 && $postOwnerId !== $userId) {
            try {
                $notification = $pdo->prepare(
                    'INSERT INTO notifications
                        (user_id, actor_id, type, title, message, link,
                         related_message_id, is_read, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, NULL, 0, NOW())'
                );

                $notification->execute([
                    $postOwnerId,
                    $userId,
                    'like',
                    'New like',
                    'Someone liked your post.',
                    'freedom-wall.php'
                ]);
            } catch (Throwable $exception) {
                // A notification failure should not undo a successful like.
                error_log(
                    'Like notification error: ' . $exception->getMessage()
                );
            }
        }

    } elseif ($action === 'unlike') {
        $stmt = $pdo->prepare(
            'DELETE FROM post_likes
             WHERE post_id = ? AND user_id = ?'
        );
        $stmt->execute([$postId, $userId]);
    }

} catch (Throwable $exception) {
    error_log('Post action error: ' . $exception->getMessage());
}

header('Location: ' . $redirect);
exit;
