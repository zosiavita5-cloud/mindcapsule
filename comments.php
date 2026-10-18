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

/**
 * Save a notification without allowing notification errors
 * to prevent the comment itself from being saved.
 */
function createCommentNotification(
    PDO $pdo,
    int $recipientId,
    int $actorId,
    string $type,
    string $title,
    string $message,
    string $link
): void {
    if ($recipientId <= 0 || $recipientId === $actorId) {
        return;
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO notifications
                (user_id, actor_id, type, title, message, link,
                 related_message_id, is_read, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NULL, 0, NOW())'
        );

        $stmt->execute([
            $recipientId,
            $actorId,
            $type,
            $title,
            $message,
            $link
        ]);
    } catch (Throwable $exception) {
        // A notification error must not undo a saved comment.
        error_log('Comment notification error: ' . $exception->getMessage());
    }
}

try {
    if ($action === 'add') {
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

        $parentRaw = $_POST['parent_id'] ?? '';
        $parentId = $parentRaw === ''
            ? null
            : filter_var($parentRaw, FILTER_VALIDATE_INT);

        $content = trim((string) ($_POST['content'] ?? ''));

        if (
            !$postId ||
            $postId < 1 ||
            $content === '' ||
            mb_strlen($content) > 1000
        ) {
            header('Location: freedom-wall.php?error=comment');
            exit;
        }

        // Verify that the post exists and is approved.
        $stmt = $pdo->prepare(
            "SELECT id, user_id
             FROM posts
             WHERE id = ? AND status = 'approved'"
        );
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            header('Location: freedom-wall.php');
            exit;
        }

        $postOwnerId = (int) $post['user_id'];
        $parentOwnerId = null;

        // If replying, verify the parent comment belongs to this post.
        if ($parentId !== null) {
            if (!is_int($parentId) || $parentId < 1) {
                header('Location: freedom-wall.php#post-' . $postId);
                exit;
            }

            $stmt = $pdo->prepare(
                'SELECT user_id
                 FROM post_comments
                 WHERE id = ? AND post_id = ?'
            );
            $stmt->execute([$parentId, $postId]);
            $parentComment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$parentComment) {
                header('Location: freedom-wall.php#post-' . $postId);
                exit;
            }

            $parentOwnerId = (int) $parentComment['user_id'];
        }

        // Save the comment.
        $stmt = $pdo->prepare(
            'INSERT INTO post_comments
                (post_id, user_id, parent_id, content)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$postId, $userId, $parentId, $content]);

        $newCommentId = (int) $pdo->lastInsertId();

        if ($parentId === null) {
            // Notify the post owner about a new top-level comment.
            createCommentNotification(
                $pdo,
                $postOwnerId,
                $userId,
                'comment',
                'New comment',
                'Someone commented on your post.',
                'freedom-wall.php#post-' . $postId
            );
        } else {
            // Notify the parent comment author about a reply.
            createCommentNotification(
                $pdo,
                (int) $parentOwnerId,
                $userId,
                'reply',
                'New reply',
                'Someone replied to your comment.',
                'freedom-wall.php#comment-' . $newCommentId
            );
        }

        // Return to the relevant post or newly created comment.
        if ($parentId !== null && $newCommentId > 0) {
            header('Location: freedom-wall.php#comment-' . $newCommentId);
        } else {
            header('Location: freedom-wall.php#post-' . $postId);
        }
        exit;

    } elseif ($action === 'delete') {
        $commentId = filter_input(
            INPUT_POST,
            'comment_id',
            FILTER_VALIDATE_INT
        );

        if (!$commentId || $commentId < 1) {
            header('Location: freedom-wall.php');
            exit;
        }

        $pdo->beginTransaction();

        // Get the post ID before deleting, for the return link.
        $stmt = $pdo->prepare(
            'SELECT id, post_id
             FROM post_comments
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$commentId, $userId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        $postId = $comment ? (int) $comment['post_id'] : 0;

        if ($comment) {
            // Delete descendants before deleting the selected comment.
            $pending = [$commentId];

            while (!empty($pending)) {
                $current = array_pop($pending);

                $stmt = $pdo->prepare(
                    'SELECT id
                     FROM post_comments
                     WHERE parent_id = ?'
                );
                $stmt->execute([$current]);

                foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $childId) {
                    $pending[] = (int) $childId;
                }

                $stmt = $pdo->prepare(
                    'DELETE FROM post_comments WHERE id = ?'
                );
                $stmt->execute([$current]);
            }
        }

        $pdo->commit();

        if ($postId > 0) {
            header('Location: freedom-wall.php#post-' . $postId);
        } else {
            header('Location: freedom-wall.php');
        }
        exit;

    } else {
        header('Location: freedom-wall.php');
        exit;
    }

} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Comments error: ' . $exception->getMessage());

    header('Location: freedom-wall.php?error=comment');
    exit;
}
```
