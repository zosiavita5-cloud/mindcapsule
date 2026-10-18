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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">

    <style>
        /* --- CLEAN SINGLE-ROW NAVBAR MATCHING SCREENSHOT --- */
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
            background-color: #fbf9f7; /* Off-white cream match */
            color: #49343f;
        }

        .site-header {
            background: transparent;
            padding: 24px 40px;
            width: 100%;
            box-sizing: border-box;
        }

        .navbar {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 500;
            color: #49343f;
            text-decoration: none;
            letter-spacing: -0.5px;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: #786671;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .nav-links a:hover {
            color: #b95f7d;
        }

        /* Log out button styling to match screenshot */
        .nav-links a[href*="logout.php"] {
            background: #b95f7d;
            color: #fff;
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(185, 95, 125, 0.25);
            transition: transform 0.2s, background 0.2s;
        }

        .nav-links a[href*="logout.php"]:hover {
            background: #a44e6c;
            color: #fff;
            transform: translateY(-1px);
        }

        /* Mobile / smaller screens responsive */
        @media (max-width: 900px) {
            .site-header { padding: 15px 20px; }
            .nav-links { gap: 12px; flex-wrap: wrap; justify-content: flex-end; }
            .nav-links a { font-size: 12px; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="<?= $baseUrl ?>index.php">
            mind.capsule
        </a>

        <div class="nav-links">
            <a href="<?= $baseUrl ?>index.php">Home</a>
            <a href="<?= $baseUrl ?>freedom-wall.php">Freedom Wall</a>
            <a href="<?= $baseUrl ?>mood-daily.php">Mood Tracker</a>
            <a href="<?= $baseUrl ?>task-planner.php">Task Planner</a>
            <a href="<?= $baseUrl ?>calm-corner.php">Calm Corner</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $baseUrl ?>logout.php">Log out</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>login.php">Log in</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="page-content">
