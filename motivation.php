
<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Support Resources';
include __DIR__ . '/includes/header.php';
?>

<style>
.support-page {
    --support-rose: #c65d7b;
    --support-rose-dark: #9e3f5c;
    --support-ink: #49343f;
    --support-muted: #887984;
    color: var(--support-ink);
    padding: 30px 0 70px;
}
.support-page * {
    box-sizing: border-box;
}
.support-page .support-wrap {
    width: min(1120px, calc(100% - 36px));
    margin: 0 auto;
}

/* HERO */
.support-page .support-hero {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: 1.2fr .8fr;
    align-items: center;
    gap: 30px;
    min-height: 330px;
    padding: clamp(30px, 5vw, 58px);
    border: 1px solid #f0d8e1;
    border-radius: 30px;
    background: linear-gradient(
        125deg,
        #fff8f5 0%,
        #fff0f5 54%,
        #f5dfe8 100%
    );
    box-shadow: 0 18px 45px rgba(142, 74, 98, .08);
}
.support-page .support-hero::before {
    content: "";
    position: absolute;
    width: 250px;
    height: 250px;
    top: -120px;
    right: -60px;
    border-radius: 50%;
    background: rgba(255,255,255,.5);
}
.support-page .support-hero-copy,
.support-page .support-art {
    position: relative;
    z-index: 1;
}
.support-page .support-eyebrow {
    margin: 0 0 10px;
    color: var(--support-rose);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.support-page .support-pill {
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
}
.support-page .support-pill span {
    color: #c65d7b;
    font-size: 15px;
}
.support-page .support-hero h1 {
    max-width: 600px;
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(37px, 5vw, 57px);
    font-weight: 500;
    line-height: 1.1;
    letter-spacing: -1.5px;
}
.support-page .support-hero h1 em {
    color: var(--support-rose);
    font-weight: 500;
}
.support-page .support-hero-copy > p:last-child {
    max-width: 500px;
    margin: 18px 0 0;
    color: #806d78;
    font-size: 14px;
    line-height: 1.9;
}

/* DECORATIVE SUPPORT ILLUSTRATION */
.support-page .support-art {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 230px;
}
.support-page .support-art-circle {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: min(100%, 280px);
    aspect-ratio: 1;
    border: 1px solid rgba(255,255,255,.9);
    border-radius: 50%;
    background: radial-gradient(
        circle at 35% 25%,
        #fff 0,
        #fff5f8 38%,
        #f0d2de 100%
    );
    box-shadow:
        inset 0 0 0 12px rgba(255,255,255,.3),
        0 18px 40px rgba(160,84,112,.1);
}
.support-page .support-art-inner {
    width: 72%;
    padding: 27px 16px;
    border: 1px solid #e9c4d2;
    border-radius: 45% 45% 43% 43%;
    background: rgba(255,255,255,.58);
    text-align: center;
    transform: rotate(-4deg);
}
.support-page .support-art-symbol {
    color: var(--support-rose);
    font-size: 34px;
    line-height: 1.2;
}
.support-page .support-art-inner p {
    margin: 10px 0 0;
    color: #75495c;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 23px;
    line-height: 1.3;
}
.support-page .support-art-inner small {
    display: block;
    margin-top: 12px;
    color: #9b7586;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.support-page .support-art-heart {
    position: absolute;
    color: #c65d7b;
    font-size: 25px;
}
.support-page .support-heart-one {
    top: 12%;
    right: 14%;
}
.support-page .support-heart-two {
    bottom: 16%;
    left: 12%;
    font-size: 18px;
}
.support-page .support-art-star {
    position: absolute;
    color: #d4a0b4;
    font-size: 22px;
}
.support-page .support-star-one {
    top: 17%;
    left: 17%;
}
.support-page .support-star-two {
    right: 12%;
    bottom: 18%;
}

/* SECTION HEADING */
.support-page .support-intro {
    margin: 42px 0 22px;
}
.support-page .support-intro h2,
.support-page .support-note h2 {
    margin: 0;
    color: #49313d;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(27px, 3.5vw, 35px);
    font-weight: 500;
    letter-spacing: -.5px;
}
.support-page .support-intro > p:last-child {
    max-width: 650px;
    margin: 12px 0 0;
    color: var(--support-muted);
    font-size: 14px;
    line-height: 1.85;
}

/* RESOURCE CARDS */
.support-page .support-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}
.support-page .support-card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 27px 24px;
    border: 1px solid #f0e0e6;
    border-radius: 22px;
    background: linear-gradient(150deg, #fff 0%, #fff9fb 100%);
    box-shadow: 0 10px 28px rgba(84,48,65,.04);
    transition: transform .2s ease, box-shadow .2s ease,
                border-color .2s ease;
}
.support-page .support-card:hover {
    transform: translateY(-4px);
    border-color: #e7c1d0;
    box-shadow: 0 15px 30px rgba(84,48,65,.075);
}
.support-page .support-card-number {
    margin-bottom: 22px;
    color: #b56b85;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.8px;
}
.support-page .support-card-icon {
    display: grid;
    width: 54px;
    height: 54px;
    place-items: center;
    margin-bottom: 20px;
    border: 1px solid #f0d5df;
    border-radius: 17px;
    background: #fff0f4;
    font-size: 23px;
}
.support-page .support-card:nth-child(2) .support-card-icon {
    border-color: #f0dfc9;
    background: #fdf3e7;
}
.support-page .support-card:nth-child(3) .support-card-icon {
    border-color: #e3d9f3;
    background: #f3effb;
}
.support-page .support-card h3 {
    margin: 0 0 12px;
    color: #4b3642;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 22px;
    font-weight: 600;
    line-height: 1.3;
}
.support-page .support-card > p {
    flex: 1;
    margin: 0;
    color: #887984;
    font-size: 13px;
    line-height: 1.9;
}
.support-page .support-card-tag {
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

/* REMINDER PANEL */
.support-page .support-note {
    position: relative;
    overflow: hidden;
    margin-top: 26px;
    padding: clamp(25px, 4vw, 38px);
    border: 1px solid #ecced9;
    border-radius: 24px;
    background: linear-gradient(120deg, #fff0f5, #fff8f5);
}
.support-page .support-note-top {
    display: flex;
    align-items: flex-start;
    gap: 17px;
}
.support-page .support-note-icon {
    display: grid;
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
    place-items: center;
    border: 1px solid #ecc8d5;
    border-radius: 15px;
    background: #fff;
    color: var(--support-rose);
    font-size: 22px;
}
.support-page .support-note h2 {
    font-size: 27px;
}
.support-page .support-note p {
    margin: 11px 0 0;
    color: #806d78;
    font-size: 13px;
    line-height: 1.9;
}
.support-page .support-notice {
    margin-top: 22px;
    padding: 16px 18px;
    border: 1px solid #efd8e1;
    border-radius: 14px;
    background: rgba(255,255,255,.75);
    color: #8a6676;
    font-size: 12px;
    line-height: 1.85;
}
.support-page .support-notice strong {
    color: #9e3f5c;
}
.support-page .support-bottom {
    margin: 25px 0 0;
    color: #9a8490;
    font-size: 12px;
    line-height: 1.8;
    text-align: center;
}

/* MOBILE */
@media (max-width: 760px) {
    .support-page {
        padding-top: 18px;
    }
    .support-page .support-hero {
        grid-template-columns: 1fr;
        gap: 10px;
        min-height: 0;
        padding: 30px 24px 22px;
        border-radius: 24px;
    }
    .support-page .support-hero h1 {
        font-size: clamp(38px, 10vw, 51px);
    }
    .support-page .support-art {
        min-height: 205px;
    }
    .support-page .support-art-circle {
        width: min(70vw, 245px);
    }
    .support-page .support-grid {
        grid-template-columns: 1fr;
    }
    .support-page .support-card {
        padding: 24px;
    }
    .support-page .support-card-number {
        margin-bottom: 14px;
    }
    .support-page .support-intro {
        margin-top: 32px;
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
    <div class="support-wrap">

        <section class="support-hero" aria-labelledby="supportTitle">
            <div class="support-hero-copy">
                <div class="support-pill">
                    <span aria-hidden="true">♡</span>
                    A safe space to begin
                </div>

                <h1 id="supportTitle">
                    You don't have to do it <em>alone.</em>
                </h1>

                <p>
                    Some days are harder than others, and asking for help
                    is okay. You deserve to be listened to, understood,
                    and supported as you work through life's challenges.
                </p>
            </div>

            <div class="support-art" aria-hidden="true">
                <div class="support-art-circle">
                    <span class="support-art-heart support-heart-one">♥</span>
                    <span class="support-art-heart support-heart-two">♥</span>
                    <span class="support-art-star support-star-one">✦</span>
                    <span class="support-art-star support-star-two">✧</span>

                    <div class="support-art-inner">
                        <div class="support-art-symbol">♡</div>
                        <p>You matter.<br>You are heard.</p>
                        <small>Support starts here</small>
                    </div>
                </div>
            </div>
        </section>

        <section class="support-intro" aria-labelledby="supportOptionsTitle">
            <p class="support-eyebrow">You deserve support</p>
            <h2 id="supportOptionsTitle">People you can reach out to.</h2>
            <p>
                You don't need to handle every concern by yourself.
                Here are a few places to start when you need someone
                to talk to or help figuring out your next step.
            </p>
        </section>

        <section class="support-grid" aria-label="Support resources">
            <article class="support-card">
                <div class="support-card-number">01 / SCHOOL</div>
                <div class="support-card-icon" aria-hidden="true">🏫</div>

                <h3>School Guidance Office</h3>

                <p>
                    Contact your school's official guidance office to ask
                    about appointments, student counseling, and available
                    support services. You can ask a teacher how to reach them.
                </p>

                <span class="support-card-tag">Student support</span>
            </article>

            <article class="support-card">
                <div class="support-card-number">02 / YOUR CIRCLE</div>
                <div class="support-card-icon" aria-hidden="true">🫶</div>

                <h3>A Trusted Person</h3>

                <p>
                    Consider talking with a trusted family member, teacher,
                    friend, or another supportive adult. You can start by
                    sharing only what you feel comfortable sharing.
                </p>

                <span class="support-card-tag">Someone to listen</span>
            </article>

            <article class="support-card">
                <div class="support-card-number">03 / PROFESSIONAL</div>
                <div class="support-card-icon" aria-hidden="true">🌷</div>

                <h3>Professional Support</h3>

                <p>
                    If your concerns continue or feel overwhelming,
                    consider contacting a qualified mental health
                    professional who can help you explore your options.
                </p>

                <span class="support-card-tag">Qualified assistance</span>
            </article>
        </section>

        <section class="support-note" aria-labelledby="gentleReminderTitle">
            <div class="support-note-top">
                <div class="support-note-icon" aria-hidden="true">♡</div>

                <div>
                    <p class="support-eyebrow">A gentle reminder</p>
                    <h2 id="gentleReminderTitle">
                        Your feelings deserve care.
                    </h2>

                    <p>
                        You deserve to be heard and supported. You don't
                        have to explain everything perfectly to ask for help.
                        If you feel unsafe or are in immediate danger,
                        reach out to a trusted adult and contact local
                        emergency services for urgent assistance.
                    </p>
                </div>
            </div>

            <div class="support-notice">
                <strong>For the website administrator:</strong>
                Before publishing this page, add verified contact details
                for your school's guidance office and appropriate local
                support services. Confirm their official contact information
                and availability before displaying it to students.
            </div>
        </section>

        <p class="support-bottom">
            Mind Capsule offers encouragement and reflection tools.
            This page does not replace professional mental health care.
            <br>
            Take things one step at a time. You deserve support. ♡
        </p>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
