```php
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, username, email, password, role
         FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: index.php');
        exit;
    }

    $error = 'Incorrect email or password.';
}

$pageTitle = 'Log In';
include __DIR__ . '/includes/header.php';
?>

<style>
:root {
    --login-pink: #fce8ee;
    --login-cream: #fff9f6;
    --login-red: #c72e50;
    --login-wine: #762039;
    --login-text: #40252d;
    --login-muted: #80666e;
}

.login-page {
    min-height: 82vh;
    padding: 55px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    background:
        radial-gradient(circle at 10% 10%, #fce8ee, transparent 40%),
        radial-gradient(circle at 90% 90%, #f7dce4, transparent 40%),
        var(--login-cream);
    font-family: 'DM Sans', sans-serif;
    color: var(--login-text);
}

.login-card {
    width: 100%;
    max-width: 455px;
    padding: 42px 38px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid #f2d9e0;
    border-radius: 28px;
    box-shadow: 0 20px 60px rgba(118, 32, 57, 0.10);
}

.login-brand {
    display: block;
    margin-bottom: 35px;
    text-align: center;
    text-decoration: none;
    color: var(--login-wine);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 27px;
    font-weight: 700;
}

.login-brand span {
    color: var(--login-red);
}

.login-welcome {
    text-align: center;
    margin-bottom: 30px;
}

.login-welcome h1 {
    margin: 0 0 13px;
    color: var(--login-wine);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(32px, 5vw, 40px);
    font-weight: 600;
    letter-spacing: -1px;
}

.login-welcome h1 em {
    color: var(--login-red);
    font-weight: 500;
}

.login-description {
    max-width: 315px;
    margin: 0 auto;
    color: var(--login-muted);
    font-size: 14px;
    line-height: 1.8;
}

.login-notice {
    padding: 13px 15px;
    margin-bottom: 20px;
    border-radius: 13px;
    font-size: 13px;
    line-height: 1.6;
}

.login-notice-success {
    background: #f0f8ef;
    border: 1px solid #d4e8d1;
    color: #386342;
}

.login-notice-error {
    background: #fff0f2;
    border: 1px solid #f3cbd4;
    color: #a32342;
}

.login-form {
    display: grid;
    gap: 20px;
}

.login-input-group {
    display: grid;
    gap: 9px;
}

.login-input-group label {
    color: var(--login-wine);
    font-size: 13px;
    font-weight: 700;
}

.login-input-group input {
    width: 100%;
    min-height: 49px;
    padding: 13px 15px;
    border: 1px solid #ead5dc;
    border-radius: 13px;
    outline: none;
    background: #fffdfc;
    color: var(--login-text);
    font: inherit;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.login-input-group input::placeholder {
    color: #b19ba2;
}

.login-input-group input:focus {
    border-color: var(--login-red);
    box-shadow: 0 0 0 4px rgba(199, 46, 80, 0.10);
}

.login-submit-button {
    width: 100%;
    min-height: 51px;
    margin-top: 4px;
    padding: 13px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border: none;
    border-radius: 30px;
    background: var(--login-red);
    color: white;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
}

.login-submit-button span {
    font-size: 19px;
    transition: transform 0.2s;
}

.login-submit-button:hover {
    background: var(--login-wine);
    box-shadow: 0 8px 20px rgba(118, 32, 57, 0.16);
    transform: translateY(-2px);
}

.login-submit-button:hover span {
    transform: translateX(3px);
}

.login-register {
    margin-top: 27px;
    padding-top: 22px;
    border-top: 1px solid #f1e2e6;
    text-align: center;
}

.login-register p {
    margin: 0 0 8px;
    color: var(--login-muted);
    font-size: 13px;
}

.login-register a {
    color: var(--login-red);
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}

.login-register a:hover {
    color: var(--login-wine);
    text-decoration: underline;
}

.login-footer-note {
    margin-top: 27px;
    text-align: center;
    color: #a48b92;
    font-size: 11px;
    letter-spacing: 0.3px;
}

@media (max-width: 480px) {
    .login-page {
        padding: 30px 15px;
    }

    .login-card {
        padding: 32px 23px;
        border-radius: 23px;
    }
}
</style>

<main class="login-page">
    <section class="login-card">

        <a class="login-brand" href="index.php">
            mind<span>.</span>capsule
        </a>

        <div class="login-welcome">
            <h1>Welcome <em>back.</em></h1>
            <p class="login-description">
                Your little corner of peace is waiting for you.
                Take a breath and pick up where you left off.
            </p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="login-notice login-notice-success" role="status">
                Your account is ready. You can log in now.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="login-notice login-notice-error" role="alert">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrfToken()) ?>"
            >

            <div class="login-input-group">
                <label for="email">Email address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="<?= e($email) ?>"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="login-input-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button class="login-submit-button" type="submit">
                Log in <span>→</span>
            </button>
        </form>

        <div class="login-register">
            <p>Don't have an account?</p>
            <a href="register.php">Create an account</a>
        </div>

        <div class="login-footer-note">
            Mind Capsule · Pause. Breathe. Begin.
        </div>

    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
```
