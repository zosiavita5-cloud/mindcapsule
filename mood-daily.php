<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$pageTitle = 'Mood Tracker';

$error = '';
$success = '';
$selectedMood = '';
$notes = '';

$moods = [
    'Happy' => [
        'description' => 'Light and happy',
        'color' => '#e9b6c7'
    ],
    'Calm' => [
        'description' => 'Peaceful and relaxed',
        'color' => '#c8b9e4'
    ],
    'Loved' => [
        'description' => 'Feeling connected',
        'color' => '#e8a5bc'
    ],
    'Tired' => [
        'description' => 'Needing some rest',
        'color' => '#bcb9df'
    ],
    'Anxious' => [
        'description' => 'A lot on my mind',
        'color' => '#e8c79d'
    ],
    'Sad' => [
        'description' => 'Having a hard day',
        'color' => '#b5cbe5'
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $selectedMood = trim($_POST['mood'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!array_key_exists($selectedMood, $moods)) {
        $error = 'Please choose a mood before saving.';
    } elseif (mb_strlen($notes) > 1000) {
        $error = 'Your note must be 1,000 characters or fewer.';
    } else {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS mood_entries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    mood VARCHAR(30) NOT NULL,
                    notes TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_mood_user_date (user_id, created_at)
                )
            ");

            $stmt = $pdo->prepare("
                INSERT INTO mood_entries (user_id, mood, notes)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $_SESSION['user_id'],
                $selectedMood,
                $notes !== '' ? $notes : null
            ]);

            header('Location: mood-daily.php?saved=1');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Your mood could not be saved. Please try again.';
        }
    }
}

if (isset($_GET['saved'])) {
    $success = 'Your mood check-in has been saved successfully.';
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== MOOD TRACKER DESIGN ===== */

.mood-page {
    --mood-red: #b83f62;
    --mood-dark: #683448;
    --mood-muted: #927783;
    --mood-border: #f0d7e0;

    color: var(--mood-dark);
    padding: 42px 0 65px;
}

.mood-page,
.mood-page * {
    box-sizing: border-box;
}

.mood-page .mood-container {
    max-width: 850px;
    margin: 0 auto;
}

/* Heading */

.mood-page .mood-header {
    max-width: 650px;
    text-align: center;
    margin: 0 auto 38px;
}

.mood-page .mood-eyebrow {
    color: var(--mood-red);
    text-transform: uppercase;
    letter-spacing: 3px;
    font-size: 11px;
    font-weight: 700;
}

.mood-page .mood-header h1 {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(30px, 5vw, 45px);
    line-height: 1.25;
    margin: 12px 0;
}

.mood-page .mood-header > p:last-child {
    color: var(--mood-muted);
    line-height: 1.8;
}

/* Main form */

.mood-page .mood-panel {
    width: 100%;
    padding: 0;
    margin: 0;
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
}

/* Messages */

.mood-page .mood-message {
    padding: 13px 16px;
    margin-bottom: 22px;
    border-radius: 10px;
    font-size: 14px;
    line-height: 1.6;
}

.mood-page .mood-success {
    color: #28633b;
    background: #e8f6ed;
    border: 1px solid #c6e7d0;
}

.mood-page .mood-error {
    color: #9d2945;
    background: #fff0f2;
    border: 1px solid #f1c9d2;
}

/* Question */

.mood-page .mood-question {
    text-align: center;
    margin-bottom: 25px;
}

.mood-page .mood-question h2 {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 25px;
    margin: 0 0 8px;
}

.mood-page .mood-question p {
    color: var(--mood-muted);
    font-size: 14px;
    line-height: 1.7;
    margin: 0;
}

/* Mood cards */

.mood-page .mood-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.mood-page .mood-option {
    display: block;
    position: relative;
    min-width: 0;
    cursor: pointer;
}

.mood-page .mood-option input {
    position: absolute;
    width: 1px;
    height: 1px;
    opacity: 0;
}

.mood-page .mood-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 130px;
    height: 100%;
    padding: 18px 10px;
    text-align: center;
    background: #fffafd;
    border: 1px solid var(--mood-border);
    border-radius: 17px;
    transition: border-color .2s, background-color .2s;
}

/* Remove decorative shapes */

.mood-page .mood-tile::before,
.mood-page .mood-tile::after,
.mood-page .mood-option::before,
.mood-page .mood-option::after {
    content: none !important;
    display: none !important;
}

.mood-page .mood-option:hover .mood-tile {
    border-color: var(--mood-red);
}

.mood-page .mood-option input:checked + .mood-tile {
    background: #fbe8ee;
    border: 2px solid var(--mood-red);
}

.mood-page .mood-option input:focus-visible + .mood-tile {
    outline: 2px solid var(--mood-red);
    outline-offset: 3px;
}

/* Mood color dot */

.mood-page .mood-dot {
    display: block;
    width: 13px;
    height: 13px;
    flex: 0 0 13px;
    border-radius: 50%;
    margin-bottom: 12px;
}

.mood-page .mood-name {
    display: block;
    max-width: 100%;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: break-word;
}

.mood-page .mood-description {
    display: block;
    max-width: 100%;
    margin-top: 5px;
    color: var(--mood-muted);
    font-size: 11px;
    line-height: 1.5;
    overflow-wrap: break-word;
}

/* Journal notes */

.mood-page .mood-field {
    margin-top: 30px;
}

.mood-page .mood-field label {
    display: block;
    margin-bottom: 10px;
    font-weight: 700;
}

.mood-page .mood-optional {
    color: var(--mood-muted);
    font-size: 12px;
    font-weight: 400;
}

.mood-page .mood-field textarea {
    display: block;
    width: 100%;
    min-height: 145px;
    padding: 16px;
    border: 1px solid var(--mood-border);
    border-radius: 14px;
    background: #fffafd;
    color: var(--mood-dark);
    font-family: inherit;
    font-size: 14px;
    line-height: 1.7;
    resize: vertical;
    outline: none;
}

.mood-page .mood-field textarea:focus {
    border-color: var(--mood-red);
    box-shadow: 0 0 0 3px rgba(184, 63, 98, .09);
}

.mood-page .mood-hint {
    color: var(--mood-muted);
    font-size: 12px;
    line-height: 1.6;
    margin-top: 8px;
}

/* Save button */

.mood-page .mood-save {
    display: block;
    width: 100%;
    margin-top: 22px;
    padding: 15px 20px;
    border: none;
    border-radius: 12px;
    background: var(--mood-red);
    color: #fff;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color .2s, transform .2s;
}

.mood-page .mood-save:hover {
    background: var(--mood-dark);
    transform: translateY(-1px);
}

/* Footer reminder */

.mood-page .mood-reminder {
    max-width: 520px;
    margin: 25px auto 0;
    text-align: center;
    color: var(--mood-muted);
    font-size: 12px;
    line-height: 1.9;
}

/* Mobile */

@media (max-width: 600px) {
    .mood-page {
        padding: 26px 0 45px;
    }

    .mood-page .mood-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .mood-page .mood-tile {
        min-height: 115px;
        padding: 15px 8px;
    }

    .mood-page .mood-question h2 {
        font-size: 22px;
    }
}
</style>

<section class="section soft-section mood-page">
    <div class="container mood-container">

        <header class="mood-header">
            <p class="mood-eyebrow">Mind Capsule / Daily Check-in</p>

            <h1>A little moment for yourself.</h1>

            <p>
                Take a breath. Check in with yourself.
                There is no wrong way to feel.
            </p>
        </header>

        <div class="mood-panel">

            <?php if ($success !== ''): ?>
                <div class="mood-message mood-success" role="status">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="mood-message mood-error" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="mood-daily.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrfToken()) ?>"
                >

                <div class="mood-question">
                    <h2>How are you feeling today?</h2>
                    <p>Select the mood that feels closest to you.</p>
                </div>

                <div class="mood-grid">
                    <?php foreach ($moods as $name => $details): ?>
                        <label class="mood-option">
                            <input
                                type="radio"
                                name="mood"
                                value="<?= e($name) ?>"
                                required
                                <?= $selectedMood === $name ? 'checked' : '' ?>
                            >

                            <span class="mood-tile">
                                <span
                                    class="mood-dot"
                                    style="background-color: <?= e($details['color']) ?>;"
                                ></span>

                                <span class="mood-name">
                                    <?= e($name) ?>
                                </span>

                                <span class="mood-description">
                                    <?= e($details['description']) ?>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="mood-field">
                    <label for="notes">
                        A note to yourself
                        <span class="mood-optional">(optional)</span>
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        maxlength="1000"
                        placeholder="Write down anything you want to remember about today..."
                    ><?= e($notes) ?></textarea>

                    <p class="mood-hint">
                        This space is yours. Write a sentence or leave it blank.
                    </p>
                </div>

                <button class="mood-save" type="submit">
                    Save my check-in
                </button>
            </form>
        </div>

        <p class="mood-reminder">
            You deserve the same kindness you give to others.
            Take today one step at a time.
            <br>
            Your check-in is saved privately to your account, not published as a post.
        </p>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>