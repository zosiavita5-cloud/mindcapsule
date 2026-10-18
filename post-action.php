```php
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

// Only allow known internal pages as redirect destinations.
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
        // Avoid duplicate likes.
        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO post_likes (post_id, user_id)
             VALUES (?, ?)'
        );
        $stmt->execute([$postId, $userId]);

        // Notify the post owner only if a new like was added.
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
                    'freedom-wall.php#post-' . $postId
                ]);
            } catch (Throwable $exception) {
                // Keep the like even if notification creation fails.
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

    } else {
        header('Location: freedom-wall.php#post-' . $postId);
        exit;
    }

} catch (Throwable $exception) {
    error_log('Post action error: ' . $exception->getMessage());
}

// Return to the relevant post on the Freedom Wall.
if ($redirect === 'freedom-wall.php') {
    header('Location: freedom-wall.php#post-' . $postId);
} else {
    header('Location: ' . $redirect);
}

exit;
```
