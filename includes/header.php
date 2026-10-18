
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPage = (strpos($currentScript, '/admin/') !== false);

$baseUrl = $isAdminPage ? '../' : '';

$pageTitle = $pageTitle ?? 'Mind Capsule';

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7d2948">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Mind Capsule</title>

    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="<?= $baseUrl ?>index.php">
            Mind Capsule
        </a>

        <button
            class="menu-toggle"
            type="button"
            aria-label="Toggle navigation"
            aria-expanded="false"
            id="menuToggle"
        >
            ☰
        </button>

        <div class="nav-links" id="navLinks">
            <a href="<?= $baseUrl ?>index.php">Home</a>
            <a href="<?= $baseUrl ?>motivation.php">Motivation</a>
            <a href="<?= $baseUrl ?>calm-corner.php">Calm Corner</a>
            <a href="<?= $baseUrl ?>freedom-wall.php">Freedom Wall</a>
            <a href="<?= $baseUrl ?>mood-daily.php">Mood Tracker</a>

            <?php if ($isLoggedIn): ?>
                <a href="<?= $baseUrl ?>create-post.php">Create Post</a>
                <a href="<?= $baseUrl ?>search-user.php">Search People</a>
                <a href="<?= $baseUrl ?>messages.php">Messages</a>
                <a href="<?= $baseUrl ?>profile.php">My Profile</a>
                <a href="<?= $baseUrl ?>logout.php">Log Out</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>login.php">Log In</a>
                <a href="<?= $baseUrl ?>register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="page-content">
