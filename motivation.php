
<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Support Resources';
include __DIR__ . '/includes/header.php';
?>

<style>
/* SUPPORT PAGE — QCU TRACK ME / MIND CAPSULE */
.support-page {
    --rose: #c65d7b;
    --rose-dark: #9e3f5c;
    --ink: #49343f;
    --muted: #887984;
    width: 100%;
    padding: 28px 0 65px;
    color: var(--ink);
    font-family: inherit;
    box-sizing: border-box;
}

.support-page *,
.support-page *::before,
.support-page *::after {
    box-sizing: border-box;
}

.support-page .sp-wrap {
    width: min(1120px, calc(100% - 36px));
    margin: 0 auto;
}

/* HERO */
.support-page .sp-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.2fr .8fr;
    align-items: center;
    gap: 28px;
    min-height: 340px;
    padding: clamp(28px, 5vw, 56px);
    border: 1px solid #f0d8e1;
    border-radius: 28px;
    background: linear-gradient(125deg, #fff8f5 0%, #fff0f5 55%, #f5dfe8 100%);
    box-shadow: 0 16px 42px rgba(142, 74, 98, .09);
}

.support-page .sp-hero::before {
    content: "";
    position: absolute;
    width: 250px;
    height: 250px;
    top: -130px;
    right: -55px;
    border-radius: 50%;
    background: rgba(255,255,255,.55);
    pointer-events: none;
}

.support-page .sp-hero-copy,
.support-page .sp-art {
    position: relative;
    z-index: 1;
}

.support-page .sp-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    padding: 9px 14px;
    border: 1px solid #efd0dc;
    border-radius: 999px;
    background: rgba(255,255,255,.8);
    color: #a34e6a;
    font-size: 11px;
    font-weight: 750;
    letter-spacing: .5px;
}

.support-page .sp-pill span {
    font-size: 15px;
    color: var(--rose);
}

.support-page .sp-hero h1 {
    max-width: 590px;
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(36px, 5vw, 56px);
    font-weight: 500;
    line-height: 1.1;
    letter-spacing: -1.5px;
}

.support-page .sp-hero h1 em {
    color: var(--rose);
    font-weight: 500;
}

.support-page .sp-hero-description {
    max-width: 490px;
    margin: 18px 0 0;
    color: #806d78;
    font-size: 14px;
    line-height: 1.9;
}

/* DECORATIVE ART */
.support-page .sp-art {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 240px;
}

.support-page .sp-art-circle {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: min(100%, 280px);
    aspect-ratio: 1;
    border: 1px solid rgba(255,255,255,.95);
    border-radius: 50%;
    background: radial-gradient(circle at 35% 25%, #fff 0%, #fff5f8 38%, #f0d2de 100%);
    box-shadow: inset 0 0 0 12px rgba(255,255,255,.3),
                0 18px 40px rgba(160,84,112,.1);
}

.support-page .sp-art-inner {
    width: 72%;
    padding: 27px 15px;
    border: 1px solid #e9c4d2;
    border-radius: 45% 45% 43% 43%;
    background: rgba(255,255,255,.62);
    text-align: center;
    transform: rotate(-4deg);
}

.support-page .sp-art-symbol {
    color: var(--rose);
    font-size: 35px;
    line-height: 1.2;
}

.support-page .sp-art-inner p {
    margin: 10px 0 0;
    color: #75495c;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 23px;
    line-height: 1.3;
}

.support-page .sp-art-inner small {
    display: block;
    margin-top: 12px;
    color: #9b7586;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.support-page .sp-heart,
.support-page .sp-star {
    position: absolute;
    pointer-events: none;
}

.support-page .sp-heart {
    color: #c65d7b;
    font-size: 24px;
}

.support-page .sp-heart-one {
    top: 12%;
    right: 14%;
}

.support-page .sp-heart-two {
    bottom: 16%;
    left: 12%;
    font-size: 18px;
}

.support-page .sp-star {
    color: #d4a0b4;
    font-size: 22px;
}

.support-page .sp-star-one {
    top: 17%;
    left: 17%;
}

.support-page .sp-star-two {
    right: 12%;
    bottom: 18%;
}

/* INTRO */
.support-page .sp-intro {
    margin: 42px 0 22px;
}

.support-page .sp-eyebrow {
    margin: 0 0 9px;
    color: var(--rose);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.support-page .sp-intro h2,
.support-page .sp-note h2 {
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(26px, 3.5vw, 35px);
    font-weight: 500;
    line-height: 1.3;
    letter-spacing: -.5px;
}

.support-page .sp-intro-description {
    max-width: 650px;
    margin: 12px 0 0;
    color: var(--muted);
    font-size: 14px;
    line-height: 1.85;
}

/* RESOURCE CARDS */
.support-page .sp-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.support-page .sp-card {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 26px 24px;
    border: 1px solid #f0e0e6;
    border-radius: 21px;
    background: linear-gradient(150deg, #fff 0%, #fff9fb 100%);
    box-shadow: 0 10px 28px rgba(84,48,65,.045);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
}

.support-page .sp-card:hover {
    transform: translateY(-4px);
    border-color: #e7c1d0;
    box-shadow: 0 15px 30px rgba(84,48,65,.075);
}

.support-page .sp-card-number {
    margin-bottom: 20px;
    color: #b56b85;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.8px;
}

.support-page .sp-card-icon {
    display: grid;
    width: 54px;
    height: 54px;
    place-items: center;
    margin-bottom: 19px;
    border: 1px solid #f0d5df;
    border-radius: 16px;
    background: #fff0f4;
    font-size: 23px;
}

.support-page .sp-card:nth-child(2) .sp-card-icon {
    border-color: #f0dfc9;
    background: #fdf3e7;
}

.support-page .sp-card:nth-child(3) .sp-card-icon {
    border-color: #e3d9f3;
    background: #f3effb;
}

.support-page .sp-card h3 {
    margin: 0 0 12px;
    color: #4b3642;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 21px;
    font-weight: 600;
    line-height: 1.4;
}

.support-page .sp-card-description {
    flex: 1;
    margin: 0;
    color: #887984;
    font-size: 13px;
    line-height: 1.9;
}

.support-page .sp-card-tag {
    display: inline-block;
    align-self: flex-start;
    margin-top: 20px;
    padding: 7px 10px;
    border-radius: 8px;
    background: #fff0f4;
    color: #a34e6a;
    font-size: 10px;
    font-weight: 750;
}

/* REMINDER */
.support-page .sp-note {
    margin-top: 26px;
    padding: clamp(24px, 4vw, 38px);
    border: 1px solid #ecced9;
    border-radius: 23px;
    background: linear-gradient(120deg, #fff0f5, #fff8f5);
}

.support-page .sp-note-heading {
    display: flex;
    align-items: flex-start;
    gap: 17px;
}

.support-page .sp-note-icon {
    display: grid;
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
    place-items: center;
    border: 1px solid #ecc8d5;
    border-radius: 15px;
    background: #fff;
    color: var(--rose);
    font-size: 22px;
}

.support-page .sp-note h2 {
    font-size: 27px;
}

.support-page .sp-note-description {
    margin: 11px 0 0;
    color: #806d78;
    font-size: 13px;
    line-height: 1.9;
}

.support-page .sp-notice {
    margin-top: 22px;
    padding: 16px 18px;
    border: 1px solid #efd8e1;
    border-radius: 14px;
    background: rgba(255,255,255,.78);
    color: #8a6676;
    font-size: 12px;
    line-height: 1.85;
}

.support-page .sp-notice strong {
    color: #9e3f5c;
}

.support-page .sp-bottom {
    margin: 25px 0 0;
    color: #9a8490;
    font-size: 12px;
    line-height: 1.8;
    text-align: center;
}

/* RESPONSIVE */
@media (max-width: 760px) {
    .support-page {
        padding-top: 18px;
    }

    .support-page .sp-hero {
        grid-template-columns: 1fr;
        gap: 8px;
        min-height: 0;
        padding: 29px 23px 20px;
        border-radius: 23px;
    }

    .support-page .sp-hero h1 {
        font-size: clamp(37px, 10vw, 50px);
    }

    .support-page .sp-art {
        min-height: 205px;
    }

    .support-page .sp-art-circle {
        width: min(70vw, 245px);
    }

    .support-page .sp-intro {
        margin-top: 32px;
    }

    .support-page .sp-grid {
        grid-template-columns: 1fr;
    }

    .support-page .sp-card {
        padding: 24px;
    }

    .support-page .sp-card-number {
        margin-bottom: 14px;
    }

    .support-page .sp-note-heading {
        gap: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .support-page *,
    .support-page *::before,
    .support-page *::after {
        transition: none !important;
    }
}
</style>

<main class="support-page">
    <div class="sp-wrap">

        <section class="sp-hero" aria-labelledby="spTitle">
            <div class="sp-hero-copy">
                <div class="sp-pill">
                    <span aria-hidden="true">♡</span>
                    A safe space to begin
                </div>

                <h1 id="spTitle">
                    You don't have to do it <em>alone.</em>
                </h1>

                <p class="sp-hero-description">
                    Some days are harder than others, and asking for help
                    is okay. You deserve to be listened to, understood,
                    and supported as you work through life's challenges.
                </p>
            </div>

            <div class="sp-art" aria-hidden="true">
                <div class="sp-art-circle">
                    <span class="sp-heart sp-heart-one">♥</span>
                    <span class="sp-heart sp-heart-two">♥</span>
                    <span class="sp-star sp-star-one">✦</span>
                    <span class="sp-star sp-star-two">✧</span>

                    <div class="sp-art-inner">
                        <div class="sp-art-symbol">♡</div>
                        <p>You matter.<br>You are heard.</p>
                        <small>Support starts here</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="sp-intro" aria-labelledby="spOptionsTitle">
            <p class="sp-eyebrow">You deserve support</p>
            <h2 id="spOptionsTitle">People you can reach out to.</h2>
            <p class="sp-intro-description">
                You don't need to handle every concern by yourself.
                Here are a few places to start when you need someone
                to talk to or help figuring out your next step.
            </p>
        </section>

        <section class="sp-grid" aria-label="Support resources">

            <article class="sp-card">
                <div class="sp-card-number">01 / SCHOOL</div>
                <div class="sp-card-icon" aria-hidden="true">🏫</div>

                <h3>School Guidance Office</h3>

                <p class="sp-card-description">
                    Contact your school's official guidance office to ask
                    about appointments, student counseling, and available
                    support services. You can ask a teacher how to reach them.
                </p>

                <span class="sp-card-tag">Student support</span>
            </article>

            <article class="sp-card">
                <div class="sp-card-number">02 / YOUR CIRCLE</div>
                <div class="sp-card-icon" aria-hidden="true">🫶</div>

                <h3>A Trusted Person</h3>

                <p class="sp-card-description">
                    Consider talking with a trusted family member, teacher,
                    friend, or another supportive adult. You can start by
                    sharing only what you feel comfortable sharing.
                </p>

                <span class="sp-card-tag">Someone to listen</span>
            </article>

            <article class="sp-card">
                <div class="sp-card-number">03 / PROFESSIONAL</div>
                <div class="sp-card-icon" aria-hidden="true">🌷</div>

                <h3>Professional Support</h3>

                <p class="sp-card-description">
                    If your concerns continue or feel overwhelming,
                    consider contacting a qualified mental health
                    professional who can help you explore your options.
                </p>

                <span class="sp-card-tag">Qualified assistance</span>
            </article>

        </section>

        <section class="sp-note" aria-labelledby="spReminderTitle">
            <div class="sp-note-heading">
                <div class="sp-note-icon" aria-hidden="true">♡</div>

                <div>
                    <p class="sp-eyebrow">A gentle reminder</p>

                    <h2 id="spReminderTitle">
                        Your feelings deserve care.
                    </h2>

                    <p class="sp-note-description">
                        You deserve to be heard and supported. You don't
                        have to explain everything perfectly to ask for help.
                        If you feel unsafe or are in immediate danger,
                        reach out to a trusted adult and contact local
                        emergency services for urgent assistance.
                    </p>
                </div>
            </div>

            <div class="sp-notice">
                <strong>For the website administrator:</strong>
                Before publishing this page, add verified contact details
                for your school's guidance office and appropriate local
                support services. Confirm their official contact information
                and availability before displaying them to students.
            </div>
        </section>

        <p class="sp-bottom">
            Mind Capsule offers encouragement and reflection tools.
            This page does not replace professional mental health care.
            <br>
            Take things one step at a time. You deserve support. ♡
        </p>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
