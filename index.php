
<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$quote = 'You do not have to solve everything today. Take one step at a time.';

try {
    $stmt = $pdo->query("SELECT quote FROM quotes ORDER BY RAND() LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['quote'])) {
        $quote = $result['quote'];
    }
} catch (PDOException $e) {
    // Use the default quote if the quotes table is unavailable.
}

$isLoggedIn = isset($_SESSION['user_id']);

// Build the CSS URL relative to this PHP file.
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}
$cssPath = $basePath . '/assets/css/style.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mind Capsule | A Little Space for You</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($cssPath, ENT_QUOTES, 'UTF-8') ?>">

    <style>
        :root {
            --pink: #fce8ee;
            --soft-pink: #fff4f6;
            --red: #c72e50;
            --wine: #762039;
            --cream: #fff9f6;
            --text: #40252d;
            --muted: #80666e;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--cream);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        .mc-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 249, 246, .96);
            border-bottom: 1px solid #f2d9e0;
            backdrop-filter: blur(12px);
        }

        .mc-nav {
            width: min(1120px, 92%);
            min-height: 76px;
            margin: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .mc-brand {
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            font-weight: 700;
            color: var(--wine);
            white-space: nowrap;
        }

        .mc-brand span {
            color: var(--red);
        }

        .mc-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 12px;
            color: var(--muted);
        }

        .mc-links a {
            transition: color .2s ease;
        }

        .mc-links a:hover {
            color: var(--red);
        }

        .mc-login {
            padding: 11px 18px;
            border-radius: 30px;
            color: white !important;
            background: var(--red);
        }

        .mc-login:hover {
            background: var(--wine);
        }

        .mc-hero {
            width: min(1120px, 92%);
            margin: 35px auto 65px;
            padding: 65px 7%;
            min-height: 430px;
            border-radius: 28px;
            background: linear-gradient(120deg, #fce8ee 0%, #fff1f2 55%, #f8dce4 100%);
            display: grid;
            grid-template-columns: 1.3fr .7fr;
            align-items: center;
            gap: 45px;
            overflow: hidden;
        }

        .mc-eyebrow {
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 11px;
            font-weight: 700;
        }

        .mc-hero h1 {
            max-width: 620px;
            margin: 18px 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(40px, 5.3vw, 67px);
            line-height: 1.08;
            font-weight: 600;
            color: var(--wine);
        }

        .mc-hero h1 em {
            color: var(--red);
            font-weight: 500;
        }

        .mc-hero p {
            max-width: 520px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.9;
        }

        .mc-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
            margin-top: 28px;
        }

        .mc-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 14px 22px;
            border: 1px solid var(--red);
            border-radius: 30px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            transition: .2s ease;
            cursor: pointer;
        }

        .mc-button-primary {
            color: white;
            background: var(--red);
        }

        .mc-button-primary:hover {
            background: var(--wine);
            border-color: var(--wine);
            transform: translateY(-2px);
        }

        .mc-button-secondary {
            color: var(--wine);
            background: rgba(255, 255, 255, .45);
        }

        .mc-button-secondary:hover {
            background: white;
        }

        .mc-orbit {
            width: min(280px, 100%);
            aspect-ratio: 1;
            margin: auto;
            border: 1px solid rgba(199, 46, 80, .25);
            border-radius: 50%;
            display: grid;
            place-items: center;
            position: relative;
        }

        .mc-orbit::before,
        .mc-orbit::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(199, 46, 80, .22);
            border-radius: 50%;
        }

        .mc-orbit::before {
            inset: 22px;
        }

        .mc-orbit::after {
            inset: 44px;
        }

        .mc-orbit-center {
            position: relative;
            z-index: 1;
            width: 145px;
            height: 145px;
            border-radius: 50%;
            background: var(--red);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 18px 45px rgba(118, 32, 57, .16);
        }

        .mc-orbit-center strong {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
        }

        .mc-orbit-center small {
            margin-top: 7px;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .mc-section {
            width: min(1120px, 92%);
            margin: 0 auto 80px;
        }

        .mc-section-heading {
            text-align: center;
            margin-bottom: 32px;
        }

        .mc-section-heading small {
            color: var(--red);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
            font-size: 10px;
        }

        .mc-section-heading h2 {
            margin: 12px 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(30px, 4vw, 42px);
            color: var(--wine);
        }

        .mc-section-heading p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .mc-feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .mc-feature {
            display: block;
            min-height: 220px;
            padding: 28px;
            background: white;
            border: 1px solid #f0dce2;
            border-radius: 20px;
            transition: .22s ease;
        }

        .mc-feature:hover {
            transform: translateY(-5px);
            border-color: #d99aaa;
            box-shadow: 0 16px 35px rgba(118, 32, 57, .07);
        }

        .mc-feature-number {
            display: block;
            color: #d78a9d;
            font-size: 11px;
            letter-spacing: 2px;
            margin-bottom: 22px;
        }

        .mc-feature h3 {
            margin: 0 0 10px;
            color: var(--wine);
            font-family: 'Playfair Display', serif;
            font-size: 23px;
        }

        .mc-feature p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .mc-feature-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--red);
            font-size: 12px;
            font-weight: 700;
        }

        .mc-quote {
            width: min(900px, 92%);
            margin: 0 auto 80px;
            padding: 42px 8%;
            border-radius: 22px;
            background: var(--wine);
            color: white;
            text-align: center;
        }

        .mc-quote small {
            color: #f5c4d0;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .mc-quote blockquote {
            margin: 20px 0 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(23px, 3vw, 34px);
            line-height: 1.55;
        }

        .mc-bottom {
            padding: 60px 5%;
            text-align: center;
            background: var(--pink);
        }

        .mc-bottom h2 {
            margin: 0 0 12px;
            color: var(--wine);
            font-family: 'Playfair Display', serif;
            font-size: 36px;
        }

        .mc-bottom p {
            margin: 0 auto 24px;
            max-width: 520px;
            color: var(--muted);
            line-height: 1.8;
            font-size: 14px;
        }

        .mc-footer {
            padding: 25px 5%;
            text-align: center;
            color: var(--muted);
            background: var(--cream);
            font-size: 12px;
        }

        @media (max-width: 1000px) {
            .mc-nav {
                padding: 18px 0;
                align-items: flex-start;
                flex-direction: column;
            }

            .mc-links {
                justify-content: flex-start;
            }
        }

        @media (max-width: 850px) {
            .mc-hero {
                grid-template-columns: 1fr;
                padding: 45px 7%;
                gap: 35px;
            }

            .mc-orbit {
                width: 220px;
            }

            .mc-orbit-center {
                width: 115px;
                height: 115px;
            }

            .mc-feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 540px) {
            .mc-links {
                gap: 12px;
                font-size: 12px;
            }

            .mc-hero {
                margin-top: 20px;
                border-radius: 20px;
            }

            .mc-feature-grid {
                grid-template-columns: 1fr;
            }

            .mc-feature {
                min-height: auto;
            }

            .mc-section {
                margin-bottom: 55px;
            }

            .mc-bottom h2 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

<header class="mc-header">
    <nav class="mc-nav">
        <a class="mc-brand" href="index.php">mind<span>.</span>capsule</a>

        <div class="mc-links">
            <a href="index.php">Home</a>
            <a href="freedom-wall.php">Freedom Wall</a>
            <a href="mood-daily.php">Mood Tracker</a>
            <a href="task-planner.php">Task Planner</a>
            <a href="motivation.php">Motivation</a>
            <a href="calm-corner.php">Calm Corner</a>
            <a href="support.php">Support</a>
            <a href="search-user.php">Search People</a>
            <a href="messages.php">Messages</a>
            <a href="create-post.php">Create a Post</a>
            <a href="search-user.php">Search People</a>
            <a href="profile.php">Your Profile</a>

            <?php if ($isLoggedIn): ?>
                <a class="mc-login" href="logout.php">Log out</a>
            <?php else: ?>
                <a class="mc-login" href="login.php">Log in</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
    <section class="mc-hero">
        <div>
            <span class="mc-eyebrow">A little space just for you</span>

            <h1>Your feelings deserve a <em>safe place.</em></h1>

            <p>
                School can get overwhelming, and life does not always go as planned.
                Mind Capsule is a gentle space where you can express yourself,
                understand your feelings, and take things one step at a time.
            </p>

            <div class="mc-actions">
                <?php if ($isLoggedIn): ?>
                    <a class="mc-button mc-button-primary" href="freedom-wall.php">
                        Visit your safe space
                    </a>
                <?php else: ?>
                    <a class="mc-button mc-button-primary" href="register.php">
                        Create your account
                    </a>
                    <a class="mc-button mc-button-secondary" href="login.php">
                        I already have an account
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mc-orbit" aria-label="Mind Capsule">
            <div class="mc-orbit-center">
                <strong>Mind</strong>
                <small>Pause. Breathe. Begin.</small>
            </div>
        </div>
    </section>

    <section class="mc-section">
        <div class="mc-section-heading">
            <small>Your personal space</small>
            <h2>A little something for every day</h2>
            <p>Choose what you need today. There is no right or wrong place to start.</p>
        </div>

        <div class="mc-feature-grid">
            <a class="mc-feature" href="freedom-wall.php">
                <span class="mc-feature-number">01 / EXPRESS</span>
                <h3>Freedom Wall</h3>
                <p>
                    Share your thoughts anonymously and read community posts.
                    Posts may be reviewed before appearing publicly.
                </p>
                <span class="mc-feature-link">Visit the wall &rarr;</span>
            </a>

            <a class="mc-feature" href="mood-daily.php">
                <span class="mc-feature-number">02 / REFLECT</span>
                <h3>Mood Tracker</h3>
                <p>
                    Check in with yourself, record how your day feels,
                    and make space for your emotions without judgment.
                </p>
                <span class="mc-feature-link">Check in with yourself &rarr;</span>
            </a>

            <a class="mc-feature" href="task-planner.php">
                <span class="mc-feature-number">03 / ORGANIZE</span>
                <h3>Task Planner</h3>
                <p>
                    Keep track of schoolwork and personal tasks.
                    Break bigger responsibilities into manageable steps.
                </p>
                <span class="mc-feature-link">Plan your day &rarr;</span>
            </a>

            <a class="mc-feature" href="motivation.php">
                <span class="mc-feature-number">04 / ENCOURAGE</span>
                <h3>Daily Motivation</h3>
                <p>
                    Read gentle reminders for the days when you need
                    a little encouragement to keep going.
                </p>
                <span class="mc-feature-link">Read a reminder &rarr;</span>
            </a>

            <a class="mc-feature" href="calm-corner.php">
                <span class="mc-feature-number">05 / UNWIND</span>
                <h3>Calm Corner</h3>
                <p>
                    Take a quiet break with relaxing sounds and give
                    yourself permission to pause for a while.
                </p>
                <span class="mc-feature-link">Take a little break &rarr;</span>
            </a>

            <a class="mc-feature" href="support.php">
                <span class="mc-feature-number">06 / CONNECT</span>
                <h3>Support Resources</h3>
                <p>
                    Find helpful information and guidance on where
                    to reach out when you need support.
                </p>
                <span class="mc-feature-link">Explore support &rarr;</span>
            </a>
        </div>
    </section>

    <section class="mc-quote">
        <small>A gentle reminder</small>
        <blockquote>
            &ldquo;<?= htmlspecialchars($quote, ENT_QUOTES, 'UTF-8') ?>&rdquo;
        </blockquote>
    </section>

    <section class="mc-bottom">
        <h2>You can start small.</h2>
        <p>
            You do not have to have everything figured out.
            Give yourself a moment, take a breath, and begin wherever you are.
        </p>

        <?php if ($isLoggedIn): ?>
            <a class="mc-button mc-button-primary" href="task-planner.php">
                Continue at your own pace
            </a>
        <?php else: ?>
            <a class="mc-button mc-button-primary" href="register.php">
                Find your space
            </a>
        <?php endif; ?>
    </section>
</main>

<footer class="mc-footer">
    <p>Mind Capsule &middot; A gentle reminder to take care of yourself.</p>
    <p>Made for reflection, encouragement, and connection.</p>
</footer>

<script src="<?= htmlspecialchars($basePath . '/assets/js/script.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>

</body>
</html>
