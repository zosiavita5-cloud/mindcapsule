
<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Calm Corner';
include __DIR__ . '/includes/header.php';
?>

<style>
/* CALM CORNER */
.calm-page {
    --calm-pink: #fce7ee;
    --calm-light: #fff8fa;
    --calm-red: #a83250;
    --calm-dark: #49232f;
    --calm-muted: #896b75;
    --calm-border: #f0d4de;

    color: var(--calm-dark);
    padding: 38px 0 60px;
}

.calm-page * {
    box-sizing: border-box;
}

.calm-page .calm-container {
    width: min(1100px, 92%);
    margin: 0 auto;
}

.calm-page .calm-heading {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 38px;
}

.calm-page .calm-eyebrow {
    color: var(--calm-red);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 14px;
}

.calm-page .calm-heading h1 {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(34px, 5vw, 53px);
    font-weight: 400;
    line-height: 1.15;
    margin: 0 0 16px;
}

.calm-page .calm-heading h1 span {
    color: var(--calm-red);
    font-style: italic;
}

.calm-page .calm-heading > p:last-child {
    color: var(--calm-muted);
    font-size: 15px;
    line-height: 1.8;
    margin: 0;
}

.calm-page .calm-divider {
    width: 55px;
    height: 2px;
    background: var(--calm-red);
    margin: 24px auto 0;
}

.calm-page .calm-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    align-items: stretch;
}

.calm-page .calm-card {
    min-width: 0;
    padding: 26px 23px;
    background: var(--calm-light);
    border: 1px solid var(--calm-border);
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, border-color 0.2s ease;
}

.calm-page .calm-card:hover {
    transform: translateY(-4px);
    border-color: #d99aaa;
}

.calm-page .calm-number {
    color: var(--calm-red);
    font-size: 12px;
    letter-spacing: 2px;
    font-weight: 700;
    margin-bottom: 22px;
}

.calm-page .calm-card h2 {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 25px;
    font-weight: 400;
    margin: 0 0 10px;
}

.calm-page .calm-card > p {
    color: var(--calm-muted);
    font-size: 14px;
    line-height: 1.8;
    margin: 0 0 22px;
}

.calm-page .calm-audio {
    width: 100%;
    margin-top: auto;
    accent-color: var(--calm-red);
}

.calm-page .calm-audio-note {
    color: var(--calm-muted);
    font-size: 12px;
    line-height: 1.6;
    margin-top: 12px;
    overflow-wrap: anywhere;
}

.calm-page .calm-breath-card {
    text-align: center;
    align-items: center;
}

.calm-page .breath-circle {
    width: 132px;
    height: 132px;
    border: 2px solid #d99aaa;
    border-radius: 50%;
    background: var(--calm-pink);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin: 8px auto 20px;
    padding: 15px;
    color: var(--calm-red);
    font-family: Georgia, "Times New Roman", serif;
    font-size: 17px;
    line-height: 1.4;
    transition: transform 4s ease-in-out, background 4s ease-in-out;
}

.calm-page .breath-circle.inhale {
    transform: scale(1.12);
    background: #f7d5e0;
}

.calm-page .breath-circle.exhale {
    transform: scale(0.92);
    background: var(--calm-light);
}

.calm-page .breath-status {
    min-height: 24px;
    color: var(--calm-red);
    font-size: 13px;
    margin-bottom: 14px;
}

.calm-page .calm-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 11px 19px;
    border: 1px solid var(--calm-red);
    border-radius: 8px;
    background: var(--calm-red);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease;
}

.calm-page .calm-button:hover {
    background: transparent;
    color: var(--calm-red);
}

.calm-page .calm-button:focus-visible {
    outline: 2px solid var(--calm-red);
    outline-offset: 4px;
}

.calm-page .calm-tip {
    margin-top: 26px;
    padding: 18px 20px;
    border-left: 3px solid var(--calm-red);
    background: var(--calm-pink);
    border-radius: 0 10px 10px 0;
    color: var(--calm-dark);
    font-size: 13px;
    line-height: 1.8;
}

.calm-page .calm-tip strong {
    color: var(--calm-red);
}

@media (max-width: 850px) {
    .calm-page .calm-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .calm-page .calm-breath-card {
        grid-column: 1 / -1;
    }
}

@media (max-width: 560px) {
    .calm-page {
        padding-top: 25px;
    }

    .calm-page .calm-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .calm-page .calm-breath-card {
        grid-column: auto;
    }

    .calm-page .calm-card {
        padding: 23px 20px;
    }

    .calm-page .calm-heading {
        margin-bottom: 28px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .calm-page .calm-card,
    .calm-page .breath-circle {
        transition: none;
    }
}
</style>

<section class="calm-page">
    <div class="calm-container">

        <header class="calm-heading">
            <p class="calm-eyebrow">A little space for yourself</p>

            <h1>Welcome to your <span>Calm Corner.</span></h1>

            <p>
                Let the noise settle. Choose a soothing sound, take a gentle
                breathing break, and give yourself permission to slow down.
            </p>

            <div class="calm-divider"></div>
        </header>

        <div class="calm-grid">

            <!-- RAIN SOUNDS -->
            <article class="calm-card">
                <div class="calm-number">01 / LISTEN</div>

                <h2>Rain Sounds</h2>

                <p>
                    Gentle rainfall to accompany a quiet study session,
                    reading time, or a peaceful break.
                </p>

                <audio class="calm-audio" controls preload="none">
                    <source src="assets/sounds/rain.mp3" type="audio/mpeg">
                    Your browser does not support audio.
                </audio>

                <p class="calm-audio-note">
                    Rain ambience · Use the player to listen.
                </p>
            </article>

            <!-- WIND SOUNDS -->
            <article class="calm-card">
                <div class="calm-number">02 / UNWIND</div>

                <h2>Gentle Wind</h2>

                <p>
                    Soft wind ambience for moments when you want to pause,
                    rest your mind, and focus on the present.
                </p>

                <audio class="calm-audio" controls preload="none">
                    <source src="assets/sounds/wind.mp3" type="audio/mpeg">
                    Your browser does not support audio.
                </audio>

                <p class="calm-audio-note">
                    Wind ambience · Use the player to listen.
                </p>
            </article>

            <!-- BREATHING EXERCISE -->
            <article class="calm-card calm-breath-card">
                <div class="calm-number">03 / TAKE A PAUSE</div>

                <h2>Breathe Gently</h2>

                <p>
                    Follow the circle at a comfortable pace.
                    Stop whenever you need to.
                </p>

                <div
                    class="breath-circle"
                    id="breathCircle"
                    aria-live="polite"
                >
                    Ready when you are
                </div>

                <p class="breath-status" id="breathStatus">
                    Take a moment to get comfortable.
                </p>

                <button
                    type="button"
                    class="calm-button"
                    id="breathButton"
                    aria-pressed="false"
                >
                    Start breathing
                </button>
            </article>

        </div>

        <div class="calm-tip">
            <strong>A gentle reminder:</strong>
            You do not need to finish everything at once. Relax your shoulders,
            look away from your screen, and breathe in a way that feels
            comfortable for you.
        </div>

    </div>
</section>

<script>
(function () {
    const circle = document.getElementById('breathCircle');
    const status = document.getElementById('breathStatus');
    const button = document.getElementById('breathButton');

    if (!circle || !status || !button) return;

    let running = false;
    let phaseTimer = null;
    let phase = 0;

    const phases = [
        {
            label: 'Breathe in',
            status: 'Gently breathe in, if comfortable.',
            className: 'inhale',
            duration: 4000
        },
        {
            label: 'Breathe out',
            status: 'Slowly breathe out, without forcing it.',
            className: 'exhale',
            duration: 4000
        }
    ];

    function runPhase() {
        if (!running) return;

        const current = phases[phase];

        circle.textContent = current.label;
        circle.classList.remove('inhale', 'exhale');
        circle.classList.add(current.className);
        status.textContent = current.status;

        phase = (phase + 1) % phases.length;
        phaseTimer = window.setTimeout(runPhase, current.duration);
    }

    function stopBreathing() {
        running = false;

        if (phaseTimer !== null) {
            window.clearTimeout(phaseTimer);
            phaseTimer = null;
        }

        circle.classList.remove('inhale', 'exhale');
        circle.textContent = 'Rest a moment';
        status.textContent = 'Breathe normally and continue when ready.';
        button.textContent = 'Start breathing';
        button.setAttribute('aria-pressed', 'false');
    }

    button.addEventListener('click', function () {
        if (running) {
            stopBreathing();
            return;
        }

        running = true;
        phase = 0;
        button.textContent = 'Stop exercise';
        button.setAttribute('aria-pressed', 'true');
        runPhase();
    });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
