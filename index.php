```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$pageTitle = $pageTitle ?? 'Mind Capsule';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Mind Capsule</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --mc-rose: #b95f7d;
            --mc-burgundy: #7f203c;
            --mc-dark: #49343f;
            --mc-muted: #887781;
            --mc-light: #fff5f7;
            --mc-border: #f0dce3;
            --mc-white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: #fffaf9;
            color: var(--mc-dark);
            font-family: 'DM Sans', sans-serif;
        }

        a {
            color: inherit;
        }

        /* NAVIGATION */
        .mc-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            background: rgba(255, 250, 249, .96);
            border-bottom: 1px solid var(--mc-border);
            backdrop-filter: blur(14px);
        }

        .mc-nav-inner {
            width: min(1200px, 92%);
            min-height: 78px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
        }

        .mc-brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            color: var(--mc-burgundy);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 25px;
            font-weight: 700;
            white-space: nowrap;
        }

        .mc-brand-icon {
            display: inline-grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8dce5, #fcecf0);
            color: var(--mc-rose);
            font-family: 'DM Sans', sans-serif;
            font-size: 24px;
        }

        .mc-nav-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .mc-nav-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 11px 16px;
            border-radius: 25px;
            color: #705b66;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: background .2s ease, color .2s ease,
                        transform .2s ease;
        }

        .mc-nav-links a:hover {
            background: #f9e8ee;
            color: var(--mc-burgundy);
        }

        .mc-nav-links a.mc-nav-active {
            background: #f9e8ee;
            color: var(--mc-burgundy);
        }

        .mc-nav-links a.mc-nav-primary {
            background: var(--mc-burgundy);
            color: #fff;
            padding-left: 21px;
            padding-right: 21px;
        }

        .mc-nav-links a.mc-nav-primary:hover {
            background: var(--mc-rose);
            transform: translateY(-1px);
        }

        .mc-nav-links a.mc-nav-logout {
            border: 1px solid #e8c8d3;
            color: var(--mc-burgundy);
        }

        .mc-nav-links a.mc-nav-logout:hover {
            background: #f9e8ee;
        }

        /* OPTIONAL SHARED CONTENT WIDTH */
        .container {
            width: min(1100px, 92%);
            margin-left: auto;
            margin-right: auto;
        }

        /* MOBILE NAVIGATION */
        .mc-menu-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            border: 1px solid var(--mc-border);
            border-radius: 13px;
            background: #fff;
            color: var(--mc-burgundy);
            font-size: 23px;
            cursor: pointer;
        }

        @media (max-width: 760px) {
            .mc-nav-inner {
                min-height: 70px;
                flex-wrap: wrap;
                gap: 0;
                padding: 12px 0;
            }

            .mc-brand {
                font-size: 22px;
            }

            .mc-brand-icon {
                width: 38px;
                height: 38px;
                font-size: 21px;
            }

            .mc-menu-toggle {
                display: inline-flex;
            }

            .mc-nav-links {
                display: none;
                width: 100%;
                padding: 14px 0 5px;
                flex-direction: column;
                align-items: stretch;
                gap: 5px;
            }

            .mc-nav-links.mc-nav-open {
                display: flex;
            }

            .mc-nav-links a {
                width: 100%;
                justify-content: flex-start;
                padding: 13px 16px;
            }

            .mc-nav-links a.mc-nav-primary,
            .mc-nav-links a.mc-nav-logout {
                justify-content: center;
                margin-top: 4px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<header class="mc-navbar">
    <nav class="mc-nav-inner" aria-label="Main navigation">

        <a href="index.php" class="mc-brand">
            <span class="mc-brand-icon" aria-hidden="true">♡</span>
            <span>Mind Capsule</span>
        </a>

        <button
            class="mc-menu-toggle"
            type="button"
            id="mcMenuToggle"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="mcNavLinks"
        >
            ☰
        </button>

        <div class="mc-nav-links" id="mcNavLinks">

            <a
                href="freedom-wall.php"
                class="<?= $currentPage === 'freedom-wall.php' ? 'mc-nav-active' : '' ?>"
            >
                Freedom Wall
            </a>

            <a
                href="search-people.php"
                class="<?= $currentPage === 'search-people.php' ? 'mc-nav-active' : '' ?>"
            >
                Search People
            </a>

            <?php if ($isLoggedIn): ?>

                <a href="logout.php" class="mc-nav-logout">
                    Log Out
                </a>

            <?php else: ?>

                <a
                    href="login.php"
                    class="<?= $currentPage === 'login.php' ? 'mc-nav-active' : '' ?>"
                >
                    Log In
                </a>

                <a
                    href="register.php"
                    class="mc-nav-primary"
                >
                    Register
                </a>

            <?php endif; ?>

        </div>
    </nav>
</header>

<script>
(function () {
    const toggle = document.getElementById('mcMenuToggle');
    const nav = document.getElementById('mcNavLinks');

    if (!toggle || !nav) {
        return;
    }

    toggle.addEventListener('click', function () {
        const isOpen = nav.classList.toggle('mc-nav-open');

        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute(
            'aria-label',
            isOpen ? 'Close navigation menu' : 'Open navigation menu'
        );

        toggle.textContent = isOpen ? '✕' : '☰';
    });
})();
</script>
```
