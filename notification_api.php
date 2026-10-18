
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$userId = (int) $_SESSION['user_id'];

function sendNotificationJson(array $data, int $status = 200): void
{
    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

/**
 * Allow only safe, internal destinations.
 * Preserve anchors such as #post-123 and query strings.
 */
function safeNotificationLink(?string $link): string
{
    $link = trim((string) $link);

    if ($link === '' || preg_match('/[\r\n\\\\]/', $link)) {
        return 'freedom-wall.php';
    }

    // Reject external URLs, protocol-relative URLs, and schemes.
    if (
        str_starts_with($link, '//') ||
        preg_match('/^[a-z][a-z0-9+.-]*:/i', $link)
    ) {
        return 'freedom-wall.php';
    }

    $path = parse_url($link, PHP_URL_PATH);

    $allowedPaths = [
        'freedom-wall.php',
        'messages.php',
        'profile.php'
    ];

    if (!is_string($path) || !in_array($path, $allowedPaths, true)) {
        return 'freedom-wall.php';
    }

    // Keep the original internal link, including its query and anchor.
    return $link;
}

try {
    $action = $_GET['action'] ?? 'poll';

    /*
    |--------------------------------------------------------------------------
    | GET: LOAD NOTIFICATIONS AND UNREAD COUNT
    |--------------------------------------------------------------------------
    */
    if ($action === 'poll' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM notifications
             WHERE user_id = ? AND is_read = 0'
        );

        $stmt->execute([$userId]);
        $unreadCount = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare(
            'SELECT
                id,
                actor_id,
                type,
                title,
                message,
                link,
                is_read,
                created_at
             FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC, id DESC
             LIMIT 10'
        );

        $stmt->execute([$userId]);

        $notifications = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $notifications[] = [
                'id' => (int) $row['id'],
                'actor_id' => $row['actor_id'] === null
                    ? null
                    : (int) $row['actor_id'],
                'type' => (string) $row['type'],
                'title' => (string) $row['title'],
                'message' => (string) $row['message'],
                'link' => safeNotificationLink($row['link'] ?? ''),
                'is_read' => (bool) $row['is_read'],
                'created_at' => (string) $row['created_at']
            ];
        }

        sendNotificationJson([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST: MARK ONE NOTIFICATION AS READ
    |--------------------------------------------------------------------------
    */
    if (
        $action === 'mark_read' &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        verifyCsrf();

        $notificationId = filter_input(
            INPUT_POST,
            'notification_id',
            FILTER_VALIDATE_INT
        );

        if (!$notificationId || $notificationId < 1) {
            sendNotificationJson([
                'success' => false,
                'error' => 'Invalid notification ID.'
            ], 400);
        }

        $stmt = $pdo->prepare(
            'UPDATE notifications
             SET is_read = 1
             WHERE id = ? AND user_id = ?'
        );

        $stmt->execute([$notificationId, $userId]);

        sendNotificationJson([
            'success' => true
        ]);
    }

    sendNotificationJson([
        'success' => false,
        'error' => 'Unsupported action.'
    ], 400);

} catch (Throwable $exception) {
    error_log('Notification API error: ' . $exception->getMessage());

    sendNotificationJson([
        'success' => false,
        'error' => 'Unable to load notifications. Please try again.'
    ], 500);
}
