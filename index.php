<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<style>
/* --- HOMEPAGE SPECIFIC DESIGN --- */
.home-page {
    --home-rose: #b95f7d;
    --home-burgundy: #7f203c;
    --home-dark: #49343f;
    --home-muted: #887781;
    --home-pink-light: #fff0f4;
    color: var(--home-dark);
    padding: 20px 0 60px;
}

.home-page .home-hero {
    display: grid;
    grid-template-columns: 1.25fr 0.75fr;
    align-items: center;
    gap: 40px;
    padding: clamp(40px, 6vw, 75px);
    background: linear-gradient(120deg, #fff0f4 0%, #fde7ed 55%, #f8dce5 100%);
    border: 1px solid #f4d4df;
    border-radius: 30px;
    box-shadow: 0 15px 40px rgba(127, 32, 60, 0.05);
    margin-bottom: 45px;
}

.home-page .home-eyebrow {
    color: var(--home-rose);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin: 0 0 12px;
}

.home-page .home-hero h1 {
    margin: 0 0 20px;
    color: var(--home-burgundy);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(38px, 5.5vw, 60px);
    font-weight: 500;
    line-height: 1.15;
}

.home-page .home-hero h1 span {
    color: var(--home-rose);
    font-style: italic;
}

.home-page .home-hero p {
    color: var(--home-muted);
    font-size: 16px;
    line-height: 1.9;
    max-width: 580px;
    margin: 0 0 30px;
}

.home-page .home-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: var(--home-burgundy);
    color: #fff;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(127, 32, 60, 0.2);
    transition: transform 0.2s, background 0.2s;
}

.home-page .home-btn:hover {
    background: var(--home-rose);
    transform: translateY(-2px);
}

.home-page .home-visual {
    display: flex;
    justify-content: center;
    align-items: center;
}

.home-page .home-circle {
    width: 230px;
    height: 230px;
    background: var(--home-burgundy);
    border: 14px solid #f7d4df;
    border-radius: 50%;
    box-shadow: 0 0 0 14px rgba(255,255,255,.5), 0 0 0 30px rgba(231,139,164,.12);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
    font-family: 'Playfair Display', Georgia, serif;
    padding: 20px;
}

.home-page .home-circle h3 {
    margin: 0;
    font-size: 28px;
    color: #fff;
}

.home-page .home-circle p {
    margin: 6px 0 0;
    font-size: 11px;
    font-family: 'DM Sans', sans-serif;
    letter-spacing: 1px;
    opacity: 0.9;
}

.home-page .home-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
    margin-top: 40px;
}

.home-page .home-card {
    background: #fff;
    border: 1px solid #efd0db;
    border-radius: 22px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(127, 32, 60, 0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.home-page .home-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(127, 32, 60, 0.1);
}

.home-page .home-card h3 {
    margin: 0 0 12px;
    color: var(--home-burgundy);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 22px;
}

.home-page .home-card p {
    margin: 0;
    color: var(--home-muted);
    font-size: 14px;
    line-height: 1.8;
}

@media (max-width: 900px) {
    .home-page .home-hero {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 35px 20px;
    }
    .home-page .home-hero p {
        margin-left: auto;
        margin-right: auto;
    }
    .home-page .home-btn {
        margin: 0 auto;
    }
    .home-page .home-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="home-page">
    <div class="container">
        
        <!-- HERO SECTION -->
        <section class="home-hero">
            <div>
                <p class="home-eyebrow">A LITTLE SPACE JUST FOR YOU</p>
                <h1>Your feelings deserve a <span>safe place.</span></h1>
                <p>
                    School and daily life can get overwhelming, and things do not always go as planned. 
                    Mind Capsule is a gentle corner where you can express your thoughts, understand your feelings, 
                    and take things one step at a time.
                </p>
                <a href="freedom-wall.php" class="home-btn">Visit your safe space ♡</a>
            </div>

            <div class="home-visual">
                <div class="home-circle">
                    <h3>Mind</h3>
                    <p>PAUSE. BREATHE. BEGIN.</p>
                </div>
            </div>
        </section>

        <!-- FEATURES / DASHBOARD CARDS -->
        <div style="margin-top: 50px;">
            <p class="home-eyebrow">FEATURES</p>
            <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:32px; color:#7f203c; margin:5px 0 25px;">
                Everything you need to find your balance.
            </h2>
        </div>

        <div class="home-grid">
            <article class="home-card">
                <h3>Freedom Wall</h3>
                <p>Share your heavy thoughts, little victories, and personal reflections anonymously or openly without judgment.</p>
            </article>

            <article class="home-card">
                <h3>Mood Tracker</h3>
                <p>Check in with yourself daily, record how you're feeling, and look back on your emotional patterns over time.</p>
            </article>

            <article class="home-card">
                <h3>Task Planner</h3>
                <p>Break down your academic or personal goals into small, manageable steps so you never feel overwhelmed.</p>
            </article>
        </div>

    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
