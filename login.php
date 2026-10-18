
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


<main class="login-page">
    <section class="login-card">



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

    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
