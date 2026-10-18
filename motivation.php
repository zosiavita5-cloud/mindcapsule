
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$quotes = $pdo->query(
    'SELECT message FROM quotes WHERE is_active = 1 ORDER BY id'
)->fetchAll(PDO::FETCH_COLUMN);

if (!$quotes) {
    $quotes = [
        'You can take things one small step at a time.',
        'Your worth is not measured by your productivity.',
        'You deserve patience, especially from yourself.'
    ];
}

$pageTitle = 'Daily Motivation';
include __DIR__ . '/includes/header.php';
?>

<style>
/* DAILY MOTIVATION DESIGN */
.motivation-page {
    --mot-rose: #b95f7d;
    --mot-dark: #49343d;
    --mot-muted: #887781;
    --mot-pink: #f8e8ed;
    --mot-cream: #fffaf7;
    color: var(--mot-dark);
    padding: 36px 0 70px;
    overflow: hidden;
}

.motivation-page * {
    box-sizing: border-box;
}

.motivation-page .mot-container {
    width: min(1100px, 92%);
    margin: 0 auto;
}

.motivation-page .mot-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.25fr .75fr;
    align-items: center;
    gap: 30px;
    min-height: 365px;
    padding: clamp(30px, 6vw, 65px);
    border-radius: 30px;
    background: linear-gradient(125deg, #fff1f3 0%, #f8e5eb 52%, #f4e8f4 100%);
    border: 1px solid #f1dce4;
    box-shadow: 0 16px 45px rgba(112, 66, 83, .08);
}

.motivation-page .mot-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 30px;
    background: rgba(255,255,255,.75);
    color: var(--mot-rose);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.3px;
    text-transform: uppercase;
}

.motivation-page .mot-hero h1 {
    margin: 22px 0 16px;
    max-width: 570px;
    color: var(--mot-dark);
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(36px, 5vw, 57px);
    font-weight: 500;
    line-height: 1.12;
    letter-spacing: -1.5px;
}

.motivation-page .mot-hero h1 span {
    color: var(--mot-rose);
    font-style: italic;
}

.motivation-page .mot-hero-copy {
    max-width: 440px;
    color: #786671;
    font-size: 15px;
    line-height: 1.9;
    margin: 0;
}

.motivation-page .mot-art {
    position: relative;
    display: grid;
    place-items: center;
    min-height: 220px;
}

.motivation-page .mot-orbit {
    display: grid;
    place-items: center;
    width: clamp(170px, 22vw, 245px);
    aspect-ratio: 1;
    border-radius: 50%;
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(185,95,125,.17);
    box-shadow: 0 0 0 18px rgba(255,255,255,.20),
                0 0 0 38px rgba(255,255,255,.13);
}

.motivation-page .mot-heart {
    font-size: clamp(80px, 10vw, 115px);
    color: #c46d89;
    filter: drop-shadow(0 12px 12px rgba(155,78,106,.18));
    animation: mot-float 4s ease-in-out infinite;
}

.motivation-page .mot-spark {
    position: absolute;
    color: #bd7792;
    font-size: 26px;
}

.motivation-page .mot-spark.one {
    top: 16%;
    right: 12%;
}

.motivation-page .mot-spark.two {
    bottom: 15%;
    left: 7%;
    font-size: 20px;
}

.motivation-page .mot-quote-card {
    position: relative;
    margin-top: 28px;
    padding: clamp(28px, 5vw, 48px);
    border-radius: 26px;
    background: #fff;
    border: 1px solid #f0e4e7;
    box-shadow: 0 12px 35px rgba(83,54,66,.06);
    text-align: center;
}

.motivation-page .mot-quote-mark {
    display: block;
    margin-bottom: 4px;
    color: #e9bdcc;
    font-family: Georgia, serif;
    font-size: 75px;
    line-height: .8;
}

.motivation-page .mot-eyebrow {
    margin: 0 0 12px;
    color: var(--mot-rose);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.motivation-page .mot-quote-card h2 {
    margin: 0 auto 20px;
    max-width: 650px;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(27px, 4vw, 39px);
    font-weight: 500;
    line-height: 1.3;
}

.motivation-page .mot-quote-text {
    max-width: 690px;
    min-height: 58px;
    margin: 0 auto 25px;
    color: #806b76;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(18px, 2.5vw, 23px);
    font-style: italic;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.motivation-page .mot-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    min-height: 48px;
    padding: 13px 23px;
    border: none;
    border-radius: 30px;
    background: var(--mot-rose);
    color: #fff;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 7px 18px rgba(185,95,125,.20);
    transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
}

.motivation-page .mot-button:hover {
    background: #a94f6e;
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(185,95,125,.27);
}

.motivation-page .mot-button:focus-visible {
    outline: 3px solid #e6a8bd;
    outline-offset: 4px;
}

.motivation-page .mot-section-heading {
    margin: 58px 0 26px;
    text-align: center;
}

.motivation-page .mot-section-heading h2 {
    margin: 0 0 13px;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: clamp(29px, 4vw, 40px);
    font-weight: 500;
}

.motivation-page .mot-section-heading > p:last-child {
    max-width: 650px;
    margin: 0 auto;
    color: var(--mot-muted);
    font-size: 14px;
    line-height: 1.9;
}

.motivation-page .mot-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
}

.motivation-page .mot-card {
    padding: 28px 25px;
    min-height: 205px;
    border: 1px solid #f0e2e7;
    border-radius: 22px;
    background: #fff;
    box-shadow: 0 8px 24px rgba(83,54,66,.035);
    transition: transform .22s ease, box-shadow .22s ease;
}

.motivation-page .mot-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(83,54,66,.08);
}

.motivation-page .mot-card-icon {
    display: grid;
    place-items: center;
    width: 49px;
    height: 49px;
    margin-bottom: 20px;
    border-radius: 16px;
    background: #faedf1;
    font-size: 23px;
}

.motivation-page .mot-card:nth-child(2) .mot-card-icon {
    background: #f5edf8;
}

.motivation-page .mot-card:nth-child(3) .mot-card-icon {
    background: #fff2e6;
}

.motivation-page .mot-card h3 {
    margin: 0 0 10px;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 21px;
    font-weight: 600;
    line-height: 1.35;
}

.motivation-page .mot-card p {
    margin: 0;
    color: var(--mot-muted);
    font-size: 14px;
    line-height: 1.8;
}

.motivation-page .mot-reminder {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-top: 25px;
    padding: 24px 28px;
    border: 1px solid #f0dfe5;
    border-radius: 22px;
    background: linear-gradient(110deg, #fff4f5, #fffaf6);
}

.motivation-page .mot-reminder-icon {
    flex: 0 0 52px;
    display: grid;
    place-items: center;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #fff;
    font-size: 25px;
}

.motivation-page .mot-reminder h3 {
    margin: 0 0 5px;
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 20px;
}

.motivation-page .mot-reminder p {
    margin: 0;
    color: var(--mot-muted);
    font-size: 14px;
    line-height: 1.8;
}

@keyframes mot-float {
    0%, 100% { transform: translateY(0) rotate(-3deg); }
    50% { transform: translateY(-9px) rotate(3deg); }
}

@media (max-width: 760px) {
    .motivation-page {
        padding-top: 22px;
    }

    .motivation-page .mot-hero {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 32px 25px;
        text-align: center;
    }

    .motivation-page .mot-hero h1,
    .motivation-page .mot-hero-copy {
        margin-left: auto;
        margin-right: auto;
    }

    .motivation-page .mot-art {
        min-height: 180px;
        margin-top: 5px;
    }

    .motivation-page .mot-orbit {
        width: 155px;
    }

    .motivation-page .mot-heart {
        font-size: 78px;
    }

    .motivation-page .mot-grid {
        grid-template-columns: 1fr;
    }

    .motivation-page .mot-card {
        min-height: auto;
    }

    .motivation-page .mot-section-heading {
        margin-top: 42px;
    }

    .motivation-page .mot-reminder {
        align-items: flex-start;
        padding: 21px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .motivation-page .mot-heart {
        animation: none;
    }

    .motivation-page .mot-card,
    .motivation-page .mot-button {
        transition: none;
    }
}
</style>

<main class="motivation-page">
    <div class="mot-container">

        <section class="mot-hero">
            <div>
                <span class="mot-label">♡ A little note for your heart</span>

                <h1>
                    Give yourself<br>
                    a little <span>kindness.</span>
                </h1>

                <p class="mot-hero-copy">
                    Life can feel overwhelming sometimes. Take a breath,
                    slow down, and remember that you are allowed to grow
                    at your own pace.
                </p>
            </div>

            <div class="mot-art" aria-hidden="true">
                <span class="mot-spark one">✦</span>
                <span class="mot-spark two">✧</span>
                <div class="mot-orbit">
                    <span class="mot-heart">♡</span>
                </div>
            </div>
        </section>

        <section class="mot-quote-card" aria-labelledby="motQuoteHeading">
            <span class="mot-quote-mark" aria-hidden="true">“</span>

            <p class="mot-eyebrow">A reminder for you</p>

            <h2 id="motQuoteHeading">A thought to carry with you.</h2>

            <p class="mot-quote-text" id="quoteText" aria-live="polite">
                <?= e($quotes[0]) ?>
            </p>

            <button class="mot-button" type="button" id="nextQuote">
                <span aria-hidden="true">✿</span>
                Another reminder
            </button>
        </section>

        <section class="mot-section-heading">
            <p class="mot-eyebrow">Positive minds · Gentle reminders</p>
            <h2>Let good things find you.</h2>
            <p>
                You don't need to have everything figured out today.
                Sometimes, the smallest reminders help us see things
                with a little more hope.
            </p>
        </section>

        <section class="mot-grid" aria-label="Positive reminders">
            <article class="mot-card">
                <div class="mot-card-icon" aria-hidden="true">🌷</div>
                <h3>Celebrate small wins.</h3>
                <p>
                    A little progress still deserves recognition.
                    Every step forward counts, even the quiet ones.
                </p>
            </article>

            <article class="mot-card">
                <div class="mot-card-icon" aria-hidden="true">☁️</div>
                <h3>Rest is part of the process.</h3>
                <p>
                    You can pause, recover your energy, and begin again.
                    Resting doesn't make your progress less meaningful.
                </p>
            </article>

            <article class="mot-card">
                <div class="mot-card-icon" aria-hidden="true">🌱</div>
                <h3>Grow at your own pace.</h3>
                <p>
                    Your journey doesn't need to look like anyone else's.
                    Give yourself room to learn and change.
                </p>
            </article>
        </section>

        <aside class="mot-reminder">
            <div class="mot-reminder-icon" aria-hidden="true">💌</div>
            <div>
                <h3>One last thing to remember</h3>
                <p>
                    You are a person, not a checklist. Be proud of the
                    effort you make, and give yourself the same kindness
                    you would offer someone you care about.
                </p>
            </div>
        </aside>

    </div>
</main>

<script>
(function () {
    const quotes = <?= json_encode(
        array_values($quotes),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const quoteText = document.getElementById('quoteText');
    const nextButton = document.getElementById('nextQuote');

    if (!quoteText || !nextButton || !quotes.length) {
        return;
    }

    let current = 0;

    nextButton.addEventListener('click', function () {
        if (quotes.length > 1) {
            current = (current + 1) % quotes.length;
        }

        quoteText.textContent = quotes[current];
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
```
