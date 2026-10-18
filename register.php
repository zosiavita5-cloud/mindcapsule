
<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($username) > 50) {
        $error = 'Username must be 50 characters or fewer.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $check = $pdo->prepare(
                'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1'
            );
            $check->execute([$username, $email]);

            if ($check->fetch()) {
                $error = 'Username or email is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $insert = $pdo->prepare(
                    'INSERT INTO users (username, email, password)
                     VALUES (?, ?, ?)'
                );
                $insert->execute([$username, $email, $hash]);

                header('Location: login.php?registered=1');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | Mind Capsule</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    padding: 30px 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #fff1f6, #ffd8e7, #ffe7e7);
    color: #542638;
    font-family: Arial, sans-serif;
}

.register-page {
    width: 100%;
    max-width: 900px;
    display: grid;
    grid-template-columns: 0.9fr 1.1fr;
    overflow: hidden;
    background: white;
    border: 1px solid #f0cada;
    border-radius: 24px;
    box-shadow: 0 15px 45px #8c31501a;
}

.register-intro {
    padding: 45px 35px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(150deg, #f7a8c8, #c63c64);
    color: white;
}

.register-symbol {
    margin-bottom: 18px;
    font-size: 30px;
    letter-spacing: 5px;
}

.register-brand {
    margin: 0 0 20px;
    font-family: Georgia, serif;
    font-size: 38px;
}

.register-description {
    line-height: 1.9;
    color: #fff5f8;
}

.register-form-area {
    padding: 40px;
}

.register-heading {
    margin: 0 0 8px;
    color: #a52d53;
    font-family: Georgia, serif;
    font-size: 30px;
}

.register-subheading {
    margin-bottom: 25px;
    color: #987987;
    font-size: 14px;
}

.register-error {
    margin-bottom: 18px;
    padding: 12px;
    border-radius: 10px;
    background: #fff0f3;
    border: 1px solid #f0b6c4;
    color: #a32143;
}

.register-field {
    margin-bottom: 16px;
}

.register-field label {
    display: block;
    margin-bottom: 7px;
    color: #7e2948;
    font-size: 13px;
    font-weight: bold;
}

.register-field input {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #efd2df;
    border-radius: 10px;
    background: #fffafd;
    color: #542638;
    outline: none;
}

.register-field input:focus {
    border-color: #d94b76;
    box-shadow: 0 0 0 3px #f5a3c733;
}

.register-button {
    width: 100%;
    margin-top: 8px;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: #b8325b;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.register-button:hover {
    background: #862441;
}

.register-login {
    margin-top: 22px;
    color: #927584;
    font-size: 13px;
    text-align: center;
}

.register-login a {
    color: #b8325b;
    font-weight: bold;
    text-decoration: none;
}

.register-footer {
    margin-top: 24px;
    color: #b29aa5;
    font-size: 10px;
    letter-spacing: 2px;
    text-align: center;
}

@media (max-width: 650px) {
    body {
        padding: 15px;
    }

    .register-page {
        grid-template-columns: 1fr;
    }

    .register-intro {
        padding: 28px;
    }

    .register-brand {
        font-size: 30px;
    }

    .register-form-area {
        padding: 27px 23px;
    }
}
</style>
</head>

<body>
<div class="register-page">

    <div class="register-intro">
        <div class="register-symbol">✳ ♡ ✳</div>
        <h1 class="register-brand">mind capsule.</h1>
        <p class="register-description">
            A little space for your thoughts, feelings, and growth.
            Take a breath, be kind to yourself, and begin here.
        </p>
    </div>

    <div class="register-form-area">
        <h2 class="register-heading">Create your account</h2>
        <p class="register-subheading">
            Start your Mind Capsule journey.
        </p>

        <?php if ($error !== ''): ?>
            <div class="register-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="register-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                    maxlength="50"
                    value="<?= htmlspecialchars($username) ?>"
                    placeholder="Choose a username"
                    autocomplete="username" required>
            </div>

            <div class="register-field">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email"
                    maxlength="150"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="Enter your email"
                    autocomplete="email" required>
            </div>

            <div class="register-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    minlength="8"
                    placeholder="At least 8 characters"
                    autocomplete="new-password" required>
            </div>

            <div class="register-field">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password"
                    name="confirm_password" minlength="8"
                    placeholder="Enter password again"
                    autocomplete="new-password" required>
            </div>

            <button type="submit" class="register-button">
                Create Account
            </button>
        </form>

        <div class="register-login">
            Already have an account?
            <a href="login.php">Log in here</a>
        </div>

        <div class="register-footer">
            MIND CAPSULE · YOUR SPACE TO BREATHE
        </div>
    </div>

</div>
</body>
</html>
