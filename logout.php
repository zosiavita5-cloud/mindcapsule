
<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]);
    }

    session_destroy();

    header('Location: index.php');
    exit;
}

$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out | Mind Capsule</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pink: #fce8ee;
            --cream: #fff9f6;
            --red: #c72e50;
            --wine: #762039;
            --text: #40252d;
            --muted: #80666e;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, #fce8ee, transparent 45%),
                radial-gradient(circle at bottom right, #f8dce4, transparent 40%),
                var(--cream);
        }

        .logout-card {
            width: min(440px, 100%);
            padding: 44px 34px;
            text-align: center;
            background: rgba(255, 255, 255, .88);
            border: 1px solid #f2d9e0;
            border-radius: 28px;
            box-shadow: 0 20px 60px rgba(118, 32, 57, .10);
        }

        .brand {
            display: inline-block;
            margin-bottom: 30px;
            color: var(--wine);
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            font-weight: 700;
            text-decoration: none;
        }

        .brand span {
            color: var(--red);
        }

        .icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 24px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--red);
            background: var(--pink);
            font-size: 36px;
        }

        h1 {
            margin: 0 0 14px;
            color: var(--wine);
            font-family: 'Playfair Display', serif;
            font-size: 36px;
        }

        p {
            margin: 0 auto 28px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .actions {
            display: grid;
            gap: 12px;
        }

        .button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 48px;
            padding: 13px 20px;
            border: 1px solid var(--red);
            border-radius: 30px;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .button-primary {
            color: white;
            background: var(--red);
        }

        .button-primary:hover {
            background: var(--wine);
            border-color: var(--wine);
            transform: translateY(-2px);
        }

        .button-secondary {
            color: var(--wine);
            background: white;
        }

        .button-secondary:hover {
            background: var(--pink);
        }

        .footer {
            margin-top: 28px;
            color: #a48b92;
            font-size: 11px;
        }

        @media (max-width: 480px) {
            .logout-card {
                padding: 36px 24px;
            }

            h1 {
                font-size: 31px;
            }
        }
    </style>
</head>
<body>

    <main class="logout-card">
        <a class="brand" href="index.php">mind<span>.</span>capsule</a>

        <div class="icon" aria-hidden="true">♡</div>

        <h1>Taking a little break?</h1>

        <p>
            Are you sure you want to log out?
            Your little space will be here whenever you return.
        </p>

        <form class="actions" method="POST" action="logout.php">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
            >

            <button class="button button-primary" type="submit">
                Yes, log me out
            </button>

            <a class="button button-secondary" href="index.php">
                No, take me back
            </a>
        </form>

        <div class="footer">
            Mind Capsule · Pause. Breathe. Begin.
        </div>
    </main>

</body>
</html>
