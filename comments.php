
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

try {
    if ($action === 'add') {
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        $parentRaw = $_POST['parent_id'] ?? '';
        $parentId = $parentRaw === '' ? null : filter_var($parentRaw, FILTER_VALIDATE_INT);
        $content = trim((string) ($_POST['content'] ?? ''));

        if (!$postId || $postId < 1 || $content === '' || mb_strlen($content) > 1000) {
            header('Location: ' . $redirect . '?error=comment');
            exit;
        }

        $stmt = $pdo->prepare(
            "SELECT id FROM posts WHERE id = ? AND status = 'approved'"
        );
        $stmt->execute([$postId]);

        if (!$stmt->fetch()) {
            header('Location: ' . $redirect);
            exit;
        }

        if ($parentId !== null) {
            if (!$parentId || $parentId < 1) {
                header('Location: ' . $redirect);
                exit;
            }

            $stmt = $pdo->prepare(
                'SELECT id FROM post_comments WHERE id = ? AND post_id = ?'
            );
            $stmt->execute([$parentId, $postId]);

            if (!$stmt->fetch()) {
                header('Location: ' . $redirect);
                exit;
            }
        }

        $stmt = $pdo->prepare(
            'INSERT INTO post_comments (post_id, user_id, parent_id, content)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$postId, $userId, $parentId, $content]);

    } elseif ($action === 'delete') {
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if ($commentId && $commentId > 0) {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'SELECT id FROM post_comments WHERE id = ? AND user_id = ?'
            );
            $stmt->execute([$commentId, $userId]);

            if ($stmt->fetch()) {
                // Delete all descendants before deleting this comment.
                $pending = [$commentId];

                while ($pending) {
                    $current = array_pop($pending);
                    $stmt = $pdo->prepare(
                        'SELECT id FROM post_comments WHERE parent_id = ?'
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
    error_log($exception->getMessage());
}

header('Location: ' . $redirect);
exit;
