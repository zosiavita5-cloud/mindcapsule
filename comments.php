
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
$redirect = $_POST['redirect'] ?? 'freedom-wall.php';

if (!in_array($redirect, ['freedom-wall.php', 'profile.php'], true)) {
    $redirect = 'freedom-wall.php';
}

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
    string $message
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
            'freedom-wall.php'
        ]);
    } catch (Throwable $exception) {
        // Keep the comment working even if notification insertion fails.
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
            header('Location: ' . $redirect . '?error=comment');
            exit;
        }

        // Find the approved post and its owner.
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
        $parentOwnerId = null;

        // If this is a reply, confirm the parent comment belongs to this post.
        if ($parentId !== null) {
            if (!is_int($parentId) || $parentId < 1) {
                header('Location: ' . $redirect);
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
                header('Location: ' . $redirect);
                exit;
            }

            $parentOwnerId = (int) $parentComment['user_id'];
        }

        // Save the comment first.
        $stmt = $pdo->prepare(
            'INSERT INTO post_comments
                (post_id, user_id, parent_id, content)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$postId, $userId, $parentId, $content]);

        // Notify the post owner for a top-level comment.
        if ($parentId === null) {
            createCommentNotification(
                $pdo,
                $postOwnerId,
                $userId,
                'comment',
                'New comment',
                'Someone commented on your post.'
            );
        } else {
            // Notify the author of the comment being replied to.
            createCommentNotification(
                $pdo,
                (int) $parentOwnerId,
                $userId,
                'reply',
                'New reply',
                'Someone replied to your comment.'
            );
        }

    } elseif ($action === 'delete') {
        $commentId = filter_input(
            INPUT_POST,
            'comment_id',
            FILTER_VALIDATE_INT
        );

        if ($commentId && $commentId > 0) {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'SELECT id
                 FROM post_comments
                 WHERE id = ? AND user_id = ?'
            );
            $stmt->execute([$commentId, $userId]);

            if ($stmt->fetch()) {
                // Delete all descendants before deleting the selected comment.
                $pending = [$commentId];

                while ($pending) {
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
        }
    }
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Comments error: ' . $exception->getMessage());
}

header('Location: ' . $redirect);
exit;
