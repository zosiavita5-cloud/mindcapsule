
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
.motivation-page {
    --rose: #c65d7b;
    --rose-dark: #9e3f5c;
    --pink: #f7dce5;
    --blush: #fff0f4;
    --ink: #392d35;
    --muted: #887983;
    color: var(--ink);
    padding: 30px 0 70px;
}
.motivation-page * { box-sizing: border-box; }
.motivation-page .mot-wrap {
    width: min(1120px, calc(100% - 36px));
    margin: 0 auto;
}

/* HERO */
.motivation-page .mot-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.12fr .88fr;
    gap: 34px;
    align-items: center;
    min-height: 360px;
    padding: clamp(28px, 5vw, 58px);
    border: 1px solid #f3dce4;
    border-radius: 30px;
    background: linear-gradient(
        125deg,
        #fff7f4 0%,
        #fff0f5 55%,
        #f8e4eb 100%
    );
    box-shadow: 0 18px 50px rgba(142, 74, 98, .09);
}
.motivation-page .mot-hero::before,
.motivation-page .mot-hero::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.motivation-page .mot-hero::before {
    width: 230px;
    height: 230px;
    right: -75px;
    top: -95px;
    background: rgba(255,255,255,.5);
}
.motivation-page .mot-hero::after {
    width: 150px;
    height: 150px;
    right: 32%;
    bottom: -105px;
    background: rgba(226,164,184,.15);
}
.motivation-page .mot-hero-copy,
.motivation-page .mot-quote-art {
    position: relative;
    z-index: 1;
}
.motivation-page .mot-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 18px;
    padding: 8px 13px;
    border: 1px solid #efd1dc;
    border-radius: 999px;
    background: rgba(255,255,255,.75);
    color: var(--rose-dark);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.motivation-page .mot-kicker-dot {
    width: 7px;
    height: 7px;
    display: inline-block;
    border-radius: 50%;
    background: var(--rose);
}
.motivation-page .mot-hero h1 {
    max-width: 520px;
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(37px, 5vw, 58px);
    font-weight: 500;
    line-height: 1.08;
    letter-spacing: -1.8px;
}
.motivation-page .mot-hero h1 em {
    color: var(--rose);
    font-weight: 500;
}
.motivation-page .mot-hero-description {
    max-width: 450px;
    margin: 18px 0 0;
    color: #806d78;
    font-size: 15px;
    line-height: 1.85;
}

/* DECORATIVE ILLUSTRATION */
.motivation-page .mot-quote-art {
    display: flex;
    min-height: 240px;
    align-items: center;
    justify-content: center;
}
.motivation-page .mot-art-circle {
    width: min(100%, 310px);
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    border: 1px solid rgba(255,255,255,.9);
    border-radius: 50%;
    background: radial-gradient(
        circle at 35% 25%,
        #fff 0,
        #fff5f7 35%,
        #f4d5e0 100%
    );
    box-shadow:
        inset 0 0 0 12px rgba(255,255,255,.28),
        0 20px 45px rgba(160,84,112,.12);
}
.motivation-page .mot-art-inner {
    width: 72%;
    padding: 28px 20px;
    border: 1px solid rgba(207,143,165,.4);
    border-radius: 45% 45% 42% 42%;
    text-align: center;
    transform: rotate(-4deg);
    background: rgba(255,255,255,.52);
}
.motivation-page .mot-art-sparkle {
    color: var(--rose);
    font-size: 27px;
    line-height: 1;
}
.motivation-page .mot-art-inner p {
    margin: 10px 0 0;
    color: #75495c;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(18px, 2.2vw, 24px);
    line-height: 1.3;
}
.motivation-page .mot-art-caption {
    display: block;
    margin-top: 12px;
    color: #9b7586;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.7px;
    text-transform: uppercase;
}
.motivation-page .mot-floating-heart {
    position: absolute;
    color: #c65d7b;
    font-size: 25px;
}
.motivation-page .mot-heart-one {
    top: 12%;
    right: 14%;
    transform: rotate(12deg);
}
.motivation-page .mot-heart-two {
    left: 12%;
    bottom: 19%;
    font-size: 18px;
    transform: rotate(-14deg);
}
.motivation-page .mot-star {
    position: absolute;
    color: #d9a0b4;
    font-size: 21px;
}
.motivation-page .mot-star-one { left: 17%; top: 17%; }
.motivation-page .mot-star-two {
    right: 13%;
    bottom: 19%;
    font-size: 16px;
}

/* SECTION HEADINGS */
.motivation-page .mot-quote-section { margin-top: 34px; }
.motivation-page .mot-section-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 18px;
    margin: 0 0 17px;
}
.motivation-page .mot-eyebrow {
    margin: 0 0 7px;
    color: var(--rose);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.motivation-page .mot-section-head h2,
.motivation-page .mot-thoughts-heading h2 {
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(25px, 3vw, 34px);
    font-weight: 500;
    letter-spacing: -.5px;
}
.motivation-page .mot-section-note {
    max-width: 310px;
    margin: 0;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.7;
}

/* DAILY QUOTE CARD */
.motivation-page .mot-quote-card {
    position: relative;
    overflow: hidden;
    padding: clamp(28px, 5vw, 48px);
    border: 1px solid #f0dce3;
    border-radius: 25px;
    background: #fff;
    box-shadow: 0 12px 35px rgba(84,48,65,.055);
}
.motivation-page .mot-quote-card::after {
    content: "“";
    position: absolute;
    top: -28px;
    right: 26px;
    color: #fff0f4;
    font-family: Georgia, serif;
    font-size: 190px;
    line-height: 1;
    pointer-events: none;
}
.motivation-page .mot-quote-label {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #a75b76;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}
.motivation-page .mot-quote-label span {
    display: inline-grid;
    width: 28px;
    height: 28px;
    place-items: center;
    border-radius: 9px;
    background: #fff0f4;
    font-size: 14px;
}
.motivation-page .mot-quote-text {
    position: relative;
    z-index: 1;
    max-width: 800px;
    min-height: 75px;
    margin: 24px 0 27px;
    color: #47333e;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(25px, 3.5vw, 39px);
    line-height: 1.38;
    letter-spacing: -.5px;
}
.motivation-page .mot-quote-footer {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}
.motivation-page .mot-quote-byline {
    color: #9a8791;
    font-size: 12px;
}
.motivation-page .mot-next-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 13px 19px;
    border: 0;
    border-radius: 13px;
    background: var(--rose);
    color: white;
    font: inherit;
    font-size: 13px;
    font-weight: 750;
    cursor: pointer;
    box-shadow: 0 7px 16px rgba(198,93,123,.2);
    transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
}
.motivation-page .mot-next-btn:hover {
    transform: translateY(-2px);
    background: var(--rose-dark);
    box-shadow: 0 10px 22px rgba(198,93,123,.25);
}
.motivation-page .mot-next-btn:focus-visible {
    outline: 3px solid #e9adc1;
    outline-offset: 3px;
}
.motivation-page .mot-next-btn:active { transform: translateY(0); }

/* POSITIVE REMINDER CARDS */
.motivation-page .mot-thoughts { margin-top: 43px; }
.motivation-page .mot-thoughts-heading { margin-bottom: 19px; }
.motivation-page .mot-thoughts-heading p:last-child {
    max-width: 590px;
    margin: 10px 0 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.8;
}
.motivation-page .mot-card-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 17px;
}
.motivation-page .mot-thought-card {
    min-height: 205px;
    padding: 25px;
    border: 1px solid #f0e2e6;
    border-radius: 21px;
    background: linear-gradient(145deg, #fff 0%, #fff9fa 100%);
    box-shadow: 0 9px 25px rgba(84,48,65,.035);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}
.motivation-page .mot-thought-card:hover {
    transform: translateY(-4px);
    border-color: #e8c5d2;
    box-shadow: 0 15px 30px rgba(84,48,65,.075);
}
.motivation-page .mot-card-icon {
    display: grid;
    width: 46px;
    height: 46px;
    place-items: center;
    margin-bottom: 20px;
    border: 1px solid #f2d9e2;
    border-radius: 15px;
    background: #fff0f4;
    font-size: 20px;
}
.motivation-page .mot-thought-card:nth-child(2) .mot-card-icon {
    background: #fdf2e6;
    border-color: #f3e2c9;
}
.motivation-page .mot-thought-card:nth-child(3) .mot-card-icon {
    background: #f1eefb;
    border-color: #e3dcf5;
}
.motivation-page .mot-thought-card h3 {
    margin: 0 0 9px;
    color: #4d3743;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 21px;
    font-weight: 600;
}
.motivation-page .mot-thought-card p {
    margin: 0;
    color: #887983;
    font-size: 13px;
    line-height: 1.8;
}
.motivation-page .mot-bottom-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 32px;
    padding: 18px;
    border: 1px dashed #e9c7d3;
    border-radius: 16px;
    background: rgba(255,245,248,.75);
    color: #956b7b;
    text-align: center;
    font-size: 12px;
    line-height: 1.7;
}
.motivation-page .mot-bottom-note strong { color: #9e3f5c; }

/* MOBILE */
@media (max-width: 760px) {
    .motivation-page { padding-top: 18px; }
    .motivation-page .mot-hero {
        grid-template-columns: 1fr;
        gap: 14px;
        min-height: 0;
        padding: 30px 24px 24px;
        border-radius: 24px;
    }
    .motivation-page .mot-hero h1 {
        font-size: clamp(39px, 10vw, 52px);
    }
    .motivation-page .mot-quote-art { min-height: 200px; }
    .motivation-page .mot-art-circle { width: min(72vw, 250px); }
    .motivation-page .mot-section-head {
        align-items: start;
        flex-direction: column;
    }
    .motivation-page .mot-card-grid { grid-template-columns: 1fr; }
    .motivation-page .mot-thought-card { min-height: auto; }
    .motivation-page .mot-quote-footer {
        align-items: start;
        flex-direction: column;
    }
    .motivation-page .mot-next-btn { width: 100%; }
}
@media (prefers-reduced-motion: reduce) {
    .motivation-page *,
    .motivation-page *::before,
    .motivation-page *::after {
        scroll-behavior: auto !important;
        transition: none !important;
    }
}
</style>

<main class="motivation-page">
    <div class="mot-wrap">

        <section class="mot-hero" aria-labelledby="motivationTitle">
            <div class="mot-hero-copy">
                <p class="mot-kicker">
                    <span class="mot-kicker-dot"></span>
                    A little space for you
                </p>

                <h1 id="motivationTitle">
                    Give yourself a little <em>kindness.</em>
                </h1>

                <p class="mot-hero-description">
                    School, plans, and everyday life can feel like a lot.
                    Take a breath, slow down for a moment, and remember that
                    you are allowed to grow at your own pace.
                </p>
            </div>

            <div class="mot-quote-art" aria-hidden="true">
                <div class="mot-art-circle">
                    <span class="mot-floating-heart mot-heart-one">♥</span>
                    <span class="mot-floating-heart mot-heart-two">♥</span>
                    <span class="mot-star mot-star-one">✦</span>
                    <span class="mot-star mot-star-two">✧</span>

                    <div class="mot-art-inner">
                        <div class="mot-art-sparkle">✧</div>
                        <p>Small steps.<br>Soft heart.<br>Fresh start.</p>
                        <span class="mot-art-caption">One day at a time</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="mot-quote-section" aria-labelledby="dailyReminderHeading">
            <div class="mot-section-head">
                <div>
                    <p class="mot-eyebrow">Your daily pause</p>
                    <h2 id="dailyReminderHeading">A reminder to keep close.</h2>
                </div>

                <p class="mot-section-note">
                    Read it slowly. Keep the words that you need today.
                </p>
            </div>

            <div class="mot-quote-card">
                <div class="mot-quote-label">
                    <span aria-hidden="true">♡</span>
                    A note for your heart
                </div>

                <p class="mot-quote-text" id="quoteText" aria-live="polite">
                    <?= e($quotes[0]) ?>
                </p>

                <div class="mot-quote-footer">
                    <span class="mot-quote-byline">
                        A gentle reminder, just for today ✿
                    </span>

                    <button class="mot-next-btn" type="button" id="nextQuote">
                        Another reminder <span aria-hidden="true">↗</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="mot-thoughts" aria-labelledby="positiveMindsHeading">
            <div class="mot-thoughts-heading">
                <p class="mot-eyebrow">Little things to remember</p>
                <h2 id="positiveMindsHeading">Let good things find you.</h2>

                <p>
                    Not every day has to be perfect. Sometimes, taking care
                    of yourself and noticing the little wins is already enough.
                </p>
            </div>

            <div class="mot-card-grid">
                <article class="mot-thought-card">
                    <div class="mot-card-icon" aria-hidden="true">🌷</div>
                    <h3>Celebrate small wins.</h3>
                    <p>
                        Finished a task? Got through a difficult day?
                        Give yourself credit. Little progress is still progress.
                    </p>
                </article>

                <article class="mot-thought-card">
                    <div class="mot-card-icon" aria-hidden="true">☁️</div>
                    <h3>Rest is part of the process.</h3>
                    <p>
                        You are a person, not a productivity machine.
                        A pause can help you return with a little more energy.
                    </p>
                </article>

                <article class="mot-thought-card">
                    <div class="mot-card-icon" aria-hidden="true">🌱</div>
                    <h3>Grow at your own pace.</h3>
                    <p>
                        Your journey does not need to look like anyone else's.
                        Keep learning, keep trying, and give yourself time.
                    </p>
                </article>
            </div>
        </section>

        <div class="mot-bottom-note">
            <span aria-hidden="true">♡</span>
            <span>
                <strong>Before you go:</strong>
                You do not have to figure everything out today.
                One small step is enough.
            </span>
        </div>

    </div>
</main>

<script>
const motivationQuotes = <?= json_encode(
    array_values($quotes),
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

let currentQuoteIndex = 0;

const quoteText = document.getElementById('quoteText');
const nextQuoteButton = document.getElementById('nextQuote');

if (quoteText && nextQuoteButton && motivationQuotes.length > 0) {
    nextQuoteButton.addEventListener('click', function () {
        if (motivationQuotes.length > 1) {
            currentQuoteIndex =
                (currentQuoteIndex + 1) % motivationQuotes.length;
        }

        quoteText.textContent = motivationQuotes[currentQuoteIndex];
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
