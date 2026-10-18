<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>

<style>
/* --- HOMEPAGE CONTAINER BOX DESIGN --- */
.home-page {
    --home-rose: #b95f7d;
    --home-burgundy: #7f203c;
    --home-dark: #49343f;
    --home-muted: #887781;
    background-color: #fbf9f7;
    color: var(--home-dark);
    padding: 20px 0 60px;
}

.home-page .home-container-box {
    max-width: 1250px;
    margin: 0 auto;
    background: linear-gradient(135deg, #fff4f6 0%, #fff7f9 50%, #fff0f3 100%);
    border: 1px solid #f2cbd7;
    border-radius: 35px;
    padding: clamp(30px, 5vw, 60px);
    box-shadow: 0 20px 50px rgba(185, 95, 125, 0.08);
}

.home-page .home-hero {
    display: grid;
    grid-template-columns: 1.25fr 0.75fr;
    align-items: center;
    gap: 40px;
    margin-bottom: 50px;
}

.home-page .home-eyebrow {
    color: var(--home-rose);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin: 0 0 14px;
}

.home-page .home-hero h1 {
    margin: 0 0 20px;
    color: var(--home-burgundy);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(38px, 5.5vw, 62px);
    font-weight: 500;
    line-height: 1.15;
}

.home-page .home-hero h1 span {
    color: var(--home-rose);
    font-style: italic;
}

.home-page .home-hero p {
    color: var(--home-muted);
    font-size: 15.5px;
    line-height: 1.9;
    max-width: 550px;
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
    width: 220px;
    height: 220px;
    background: var(--home-burgundy);
    border: 12px solid #f7d4df;
    border-radius: 50%;
    box-shadow: 0 0 0 12px rgba(255,255,255,.6), 0 0 0 26px rgba(231,139,164,.12);
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
    font-size: 26px;
    color: #fff;
}

.home-page .home-circle p {
    margin: 6px 0 0;
    font-size: 10.5px;
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
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #efd0db;
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 10px 30px rgba(127, 32, 60, 0.04);
    transition: transform 0.2s, box-shadow 0.2s;
}

.home-page .home-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(127, 32, 60, 0.08);
}

.home-page .home-card h3 {
    margin: 0 0 10px;
    color: var(--home-burgundy);
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 21px;
}

.home-page .home-card p {
    margin: 0;
    color: var(--home-muted);
    font-size: 13.5px;
    line-height: 1.8;
}

@media (max-width: 900px) {
    .home-page .home-container-box {
        padding: 20px;
        border-radius: 20px;
    }
    .home-page .home-hero {
        grid-template-columns: 1fr;
        text-align: center;
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
        
        <!-- MALAKING PINK CONTAINER BOX -->
        <div class="home-container-box">
            
            <!-- HERO SECTION -->
            <section class="home-hero">
                <div>
                    <p class="home-eyebrow">A LITTLE SPACE JUST FOR YOU</p>
                    <h1>Your feelings deserve a <span>safe place.</span></h1>
                    <p>
                        School can get overwhelming, and life does not always go as planned. Mind 
                        Capsule is a gentle space where you can express yourself, understand your 
                        feelings, and take things one step at a time.
                    </p>
                    <a href="freedom-wall.php" class="home-btn">Visit your safe space</a>
                </div>

                <div class="home-visual">
                    <div class="home-circle">
                        <h3>Mind</h3>
                        <p>PAUSE. BREATHE. BEGIN.</p>
                    </div>
                </div>
            </section>

            <!-- FEATURES / CARDS GRID -->
            <div style="margin-top: 45px;">
                <p class="home-eyebrow">FEATURES</p>
                <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:30px; color:#7f203c; margin:4px 0 22px;">
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
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
