
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPage = strpos($currentScript, '/admin/') !== false;
$baseUrl = $isAdminPage ? '../' : '';

$pageTitle = $pageTitle ?? 'Home';
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
            id="menuToggle"
            aria-label="Toggle navigation"
            aria-controls="navLinks"
            aria-expanded="false"
        >
            ☰
        </button>

        <div class="nav-links" id="navLinks">
            <a href="<?= $baseUrl ?>index.php">Home</a>
            <a href="<?= $baseUrl ?>freedom-wall.php">Freedom Wall</a>
            <a href="<?= $baseUrl ?>mood-daily.php">Mood Tracker</a>
            <a href="<?= $baseUrl ?>task-planner.php">Task Planner</a>
            <a href="<?= $baseUrl ?>motivation.php">Motivation</a>
            <a href="<?= $baseUrl ?>calm-corner.php">Calm Corner</a>
            <a href="<?= $baseUrl ?>support.php">Support</a>
            <a href="<?= $baseUrl ?>search-user.php">Search People</a>
            <a href="<?= $baseUrl ?>messages.php">Messages</a>
            <a href="<?= $baseUrl ?>create-post.php">Create a Post</a>
            <a href="<?= $baseUrl ?>search-user.php">Search People</a>
            <a href="<?= $baseUrl ?>profile.php">Your Profile</a>
            <a href="<?= $baseUrl ?>logout.php">Log out</a>
        </div>
    </nav>
</header>

<main class="page-content">
