
<?php
require_once __DIR__ . '/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isAdminPage = strpos($currentScript, '/admin/') !== false;
$baseUrl = $isAdminPage ? '../' : '';

$pageTitle = $pageTitle ?? 'Home';
$isLoggedIn = !empty($_SESSION['user_id']);
$csrfForNotifications = csrfToken();
$notificationApiUrl = $baseUrl . 'notification_api.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#7d2948">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | Mind Capsule</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>assets/css/style.css">

    <style>
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif, system-ui;
            background-color: #fff9fb;
            color: #5d3a48;
        }

        .site-header {
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f2cbd7;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(184,74,112,.04);
        }

        .navbar {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
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

        .logo span { color: #e03a6b; }

        .nav-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            min-width: 0;
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
            flex-wrap: nowrap;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .nav-links::-webkit-scrollbar { display: none; }

        .nav-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 50px;
            color: #8c4c65;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 500;
            white-space: nowrap;
            transition: all .2s ease;
            background: #fff5f8;
            border: 1px solid #f7d9e3;
        }

        .nav-links a:hover {
            background: #e03a6b;
            color: #fff;
            border-color: #e03a6b;
            transform: translateY(-1px);
        }

        .nav-links a[href*="profile.php"] {
            background: #fff0f4;
            border-color: #f0cbd4;
        }

        .nav-links a[href*="logout.php"] {
            background: linear-gradient(135deg,#e03a6b 0%,#ad1f4e 100%);
            color: #fff;
            border-color: transparent;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(224,58,107,.25);
        }

        .nav-links a[href*="logout.php"]:hover {
            background: linear-gradient(135deg,#c72c50 0%,#8b2345 100%);
        }

        /* Notification bell */
        .mc-notification {
            position: relative;
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .mc-notification-btn {
            position: relative;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f0d8e3;
            border-radius: 50%;
            background: #fff8fb;
            color: #873653;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .mc-notification-btn:hover {
            background: #fbeaf1;
            transform: translateY(-1px);
        }

        .mc-notification-btn svg {
            width: 21px;
            height: 21px;
        }

        .mc-notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            border-radius: 999px;
            background: #b72f59;
            color: #fff;
            font: 700 10px/1 Arial, sans-serif;
        }

        .mc-notification-badge.visible { display: inline-flex; }

        .mc-notification-panel {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            z-index: 2000;
            width: min(370px, calc(100vw - 24px));
            overflow: hidden;
            border: 1px solid #f0dce5;
            border-radius: 20px;
            background: #fffdfd;
            box-shadow: 0 18px 55px rgba(83,30,51,.16);
            color: #442b36;
        }

        .mc-notification-panel[hidden] { display: none; }

        .mc-notification-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px;
            border-bottom: 1px solid #f5e8ee;
        }

        .mc-notification-heading {
            margin: 0;
            color: #6f2945;
            font: 600 19px/1.3 'Playfair Display', Georgia, serif;
        }

        .mc-notification-subtitle {
            margin: 4px 0 0;
            color: #a18491;
            font-size: 11px;
        }

        .mc-notification-refresh {
            border: 0;
            border-radius: 20px;
            background: #fff0f5;
            padding: 8px 10px;
            color: #963958;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .mc-notification-list {
            max-height: 390px;
            overflow-y: auto;
        }

        .mc-notification-item {
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px 16px;
            border: 0;
            border-bottom: 1px solid #f8edf1;
            background: #fffdfd;
            color: inherit;
            text-align: left;
            cursor: pointer;
            transition: background .2s;
        }

        .mc-notification-item:hover,
        .mc-notification-item.unread {
            background: #fff5f8;
        }

        .mc-notification-icon {
            flex: 0 0 39px;
            width: 39px;
            height: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #f9e5ed;
            color: #963958;
            font-size: 18px;
        }

        .mc-notification-copy {
            min-width: 0;
            flex: 1;
        }

        .mc-notification-title {
            display: block;
            margin: 1px 0 4px;
            color: #633047;
            font-size: 12px;
            font-weight: 700;
        }

        .mc-notification-message {
            display: block;
            color: #826c77;
            font-size: 12px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .mc-notification-time {
            display: block;
            margin-top: 6px;
            color: #b09aa4;
            font-size: 10px;
        }

        .mc-notification-dot {
            flex: 0 0 8px;
            width: 8px;
            height: 8px;
            margin-top: 6px;
            border-radius: 50%;
            background: #bd3d68;
        }

        .mc-notification-empty,
        .mc-notification-loading,
        .mc-notification-error {
            padding: 30px 20px;
            color: #9b8290;
            font-size: 12px;
            text-align: center;
            line-height: 1.8;
        }

        .mc-notification-empty-icon {
            display: block;
            margin-bottom: 8px;
            color: #b44c70;
            font-size: 27px;
        }

        @media (max-width: 1024px) {
            .navbar { position: relative; gap: 10px; }
            .nav-right { gap: 8px; }
            .menu-toggle { display: block; }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                box-sizing: border-box;
                flex-direction: column;
                align-items: stretch;
                background: rgba(255,255,255,.98);
                backdrop-filter: blur(12px);
                padding: 15px 20px;
                border-bottom: 1px solid #f2cbd7;
                box-shadow: 0 10px 25px rgba(184,74,112,.1);
                max-height: 75vh;
                overflow-y: auto;
            }

            .nav-links.active { display: flex; }

            .nav-links a {
                justify-content: center;
                border-radius: 12px;
                padding: 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .navbar { padding: 10px 12px; }
            .logo { font-size: 19px; }
            .mc-notification-panel {
                position: fixed;
                top: 70px;
                right: 10px;
                width: calc(100vw - 20px);
            }
            .mc-notification-list { max-height: 65vh; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <nav class="navbar">
        <a class="logo" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>index.php">
            Mind <span>Capsule</span> ♡
        </a>

        <div class="nav-right">
            <?php if ($isLoggedIn): ?>
                <div class="mc-notification" id="mcNotification">
                    <button
                        type="button"
                        class="mc-notification-btn"
                        id="mcNotificationToggle"
                        aria-label="Open notifications"
                        aria-expanded="false"
                        aria-controls="mcNotificationPanel"
                    >
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>
                        </svg>
                        <span class="mc-notification-badge" id="mcNotificationBadge">0</span>
                    </button>

                    <section
                        class="mc-notification-panel"
                        id="mcNotificationPanel"
                        aria-label="Notifications"
                        hidden
                    >
                        <div class="mc-notification-head">
                            <div>
                                <h2 class="mc-notification-heading">Your little updates ♡</h2>
                                <p class="mc-notification-subtitle">A little update from your community</p>
                            </div>
                            <button
                                type="button"
                                class="mc-notification-refresh"
                                id="mcNotificationRefresh"
                            >Refresh</button>
                        </div>

                        <div class="mc-notification-list" id="mcNotificationList" aria-live="polite">
                            <div class="mc-notification-loading">Gathering your little updates... ♡</div>
                        </div>
                    </section>
                </div>
            <?php endif; ?>

            <button
                class="menu-toggle"
                type="button"
                id="menuToggle"
                aria-label="Toggle navigation"
                aria-controls="navLinks"
                aria-expanded="false"
            >☰</button>
        </div>

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
(() => {
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    menuToggle?.addEventListener('click', () => {
        const isOpen = navLinks.classList.toggle('active');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    const root = document.getElementById('mcNotification');
    const toggle = document.getElementById('mcNotificationToggle');
    const panel = document.getElementById('mcNotificationPanel');
    const badge = document.getElementById('mcNotificationBadge');
    const list = document.getElementById('mcNotificationList');
    const refresh = document.getElementById('mcNotificationRefresh');

    if (!root || !toggle || !panel || !badge || !list) return;

    const API = <?= json_encode($notificationApiUrl) ?>;
    const CSRF = <?= json_encode($csrfForNotifications) ?>;
    let notifications = [];
    let loading = false;

    const escapeHTML = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);

    const iconFor = type => ({
        message: '✉',
        like: '♡',
        comment: '💬',
        reply: '↩',
        task: '✓'
    }[type] || '♡');

    function render() {
        const unread = notifications.filter(n =>
            n.is_read === false || n.is_read === 0 || n.is_read === '0'
        ).length;

        badge.textContent = unread > 99 ? '99+' : String(unread);
        badge.classList.toggle('visible', unread > 0);
        toggle.setAttribute(
            'aria-label',
            unread ? `Notifications, ${unread} unread` : 'Open notifications'
        );

        if (!notifications.length) {
            list.innerHTML = `
                <div class="mc-notification-empty">
                    <span class="mc-notification-empty-icon">♡</span>
                    You're all caught up.<br>
                    A little peace for your mind.
                </div>`;
            return;
        }

        list.innerHTML = notifications.map(n => {
            const isUnread = n.is_read === false || n.is_read === 0 || n.is_read === '0';

            return `
                <button type="button"
                    class="mc-notification-item${isUnread ? ' unread' : ''}"
                    data-id="${Number(n.id)}"
                    data-link="${escapeHTML(n.link || '')}">
                    <span class="mc-notification-icon">${iconFor(n.type)}</span>
                    <span class="mc-notification-copy">
                        <span class="mc-notification-title">${escapeHTML(n.title)}</span>
                        <span class="mc-notification-message">${escapeHTML(n.message)}</span>
                        <span class="mc-notification-time">${escapeHTML(n.created_at)}</span>
                    </span>
                    ${isUnread ? '<span class="mc-notification-dot"></span>' : ''}
                </button>`;
        }).join('');
    }

    async function loadNotifications() {
        if (loading) return;
        loading = true;

        try {
            const response = await fetch(`${API}?action=poll`, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Unable to fetch notifications');

            const data = await response.json();
            notifications = Array.isArray(data.notifications) ? data.notifications : [];
            render();
        } catch (error) {
            list.innerHTML = `
                <div class="mc-notification-error">
                    We couldn't load your notifications.<br>
                    Please try refreshing.
                </div>`;
        } finally {
            loading = false;
        }
    }

    async function markRead(id) {
        const body = new FormData();
        body.append('notification_id', id);
        body.append('csrf_token', CSRF);

        const response = await fetch(`${API}?action=mark_read`, {
            method: 'POST',
            credentials: 'same-origin',
            body,
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error('Unable to mark notification read');
        return response.json();
    }

    toggle.addEventListener('click', () => {
        const opening = panel.hidden;
        panel.hidden = !opening;
        toggle.setAttribute('aria-expanded', String(opening));

        if (opening) loadNotifications();
    });

    refresh?.addEventListener('click', loadNotifications);

    document.addEventListener('click', event => {
        if (!root.contains(event.target)) {
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    list.addEventListener('click', async event => {
        const item = event.target.closest('[data-id]');
        if (!item) return;

        const id = item.dataset.id;
        const link = item.dataset.link;

        try {
            const current = notifications.find(n => String(n.id) === id);
            const isUnread = current &&
                (current.is_read === false || current.is_read === 0 || current.is_read === '0');

            if (isUnread) {
                await markRead(id);
            }

            if (link) {
                const target = new URL(link, window.location.href);

                if (target.origin === window.location.origin) {
                    window.location.href = target.href;
                    return;
                }
            }

            await loadNotifications();
        } catch (error) {
            console.error('Could not update notification.');
        }
    });

    loadNotifications();
    window.setInterval(loadNotifications, 15000);
})();
</script>

<main class="page-content">
