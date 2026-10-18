<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Mind Capsule';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Mind Capsule</title>

    <link rel="stylesheet" href="/Mind%20Capsule/assets/style.css">
</head>
<body>

<header class="site-header">
    <a class="brand" href="/Mind%20Capsule/index.php">
        <span class="brand-symbol"></span>
        <span>Mind capsule</span>
    </a>

    <button class="menu-toggle" type="button"
            onclick="document.querySelector('.main-nav').classList.toggle('nav-open')"
            aria-label="Toggle navigation">
        ☰
    </button>

    <nav class="main-nav">
        <a href="/Mind%20Capsule/index.php">Home</a>
        <a href="/Mind%20Capsule/freedom-wall.php">Freedom Wall</a>
        <a href="/Mind%20Capsule/mood-daily.php">Mood Tracker</a>
        <a href="/Mind%20Capsule/task-planner.php">Task Planner</a>
        <a href="/Mind%20Capsule/motivation.php">Motivation</a>
        <a href="/Mind%20Capsule/calm-corner.php">Calm Corner</a>
        <a href="/Mind%20Capsule/support.php">Support</a>
        
<a href="/Mind%20Capsule/search-user.php">Search People</a>
<a href="/Mind%20Capsule/messages.php">Messages</a>

<a href="create-post.php">Create a Post</a>
<a href="search-user.php">Search People</a>


<a href="profile.php">Your Profile</a>


        <?php if ($loggedIn): ?>
            <a class="nav-account" href="/Mind%20Capsule/logout.php">Log out</a>
        <?php else: ?>
            <a href="/Mind%20Capsule/login.php">Log in</a>
            <a class="nav-register" href="/Mind%20Capsule/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main class="page-content">
