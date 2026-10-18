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
    
    <!-- Fallback / Main Stylesheet link -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">

    <style>
        /* --- INLINE NAVBAR & HEADER STYLING FIX --- */
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
            background-color: #fff9fb;
            color: #5d3a48;
        }

        .site-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f2cbd7;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(184, 74, 112, 0.04);
        }

        .navbar {
            max-width: 1350px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 600;
            color: #7f203c;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: #e03a6b;
        }

        .menu-toggle {
            display: none;
            background: #fff0f4;
            border: 1px solid #f2cbd7;
            color: #8c4c65;
            padding: 8px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 50px;
            color: #8c4c65;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            background: #fff5f8;
            border: 1px solid #f7d9e3;
        }

        .nav-links a:hover {
            background: #e03a6b;
            color: #ffffff;
            border-color: #e03a6b;
            transform: translateY(-1px);
        }

        /* Log out highlight style */
        .nav-links a[href*="logout.php"] {
            background: #fff0f2;
            color: #a42d4b;
            border-color: #f0cbd4;
        }

        .nav-links a[href*="logout.php"]:hover {
            background: #a42d4b;
            color: #fff;
        }

        @media (max-width: 900px) {
            .menu-toggle {
                display: block;
            }
            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                padding-top: 10px;
            }
            .nav-links.active {
                display: flex;
            }
            .nav-links a {
                text-align: center;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="<?= $baseUrl ?>index.php">
            Mind <span>Capsule</span> ♡
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
            <a href="<?= $baseUrl ?>profile.php">Your Profile</a>
            <a href="<?= $baseUrl ?>logout.php">Log out</a>
        </div>
    </nav>
</header>

<script>
    // Simple toggle support for mobile menu
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        const navLinks = document.getElementById('navLinks');
        navLinks.classList.toggle('active');
    });
</script>

<main class="page-content">
