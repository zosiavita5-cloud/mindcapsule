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
        /* --- CLEAN HEADER & NAVBAR FIX --- */
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
            background-color: #fff9fb;
            color: #5d3a48;
        }

        .site-header {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f2cbd7;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(184, 74, 112, 0.04);
        }

        .navbar {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 22px;
            font-weight: 600;
            color: #7f203c;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
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
            flex-wrap: nowrap; /* Pinipilit maging isang linya */
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            overflow-x: auto; /* Nagiging scrollable kung sakaling medyo maliit ang screen */
            scrollbar-width: none; /* Tinanggal ang scrollbar para malinis tingnan */
        }

        .nav-links::-webkit-scrollbar {
            display: none;
        }

        .nav-links a {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 50px;
            color: #8c4c65;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 500;
            white-space: nowrap;
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

        /* Profile & Logout Custom Style */
        .nav-links a[href*="profile.php"] {
            background: #fff0f4;
            border-color: #f0cbd4;
        }

        .nav-links a[href*="logout.php"] {
            background: linear-gradient(135deg, #e03a6b 0%, #ad1f4e 100%);
            color: #fff;
            border-color: transparent;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(224, 58, 107, 0.25);
        }

        .nav-links a[href*="logout.php"]:hover {
            background: linear-gradient(135deg, #c72c50 0%, #8b2345 100%);
            color: #fff;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .menu-toggle {
                display: block;
            }
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(12px);
                padding: 15px 20px;
                border-bottom: 1px solid #f2cbd7;
                box-shadow: 0 10px 25px rgba(184, 74, 112, 0.1);
            }
            .nav-links.active {
                display: flex;
            }
            .nav-links a {
                justify-content: center;
                border-radius: 12px;
                padding: 10px;
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
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        const navLinks = document.getElementById('navLinks');
        navLinks.classList.toggle('active');
    });
</script>

<main class="page-content">
