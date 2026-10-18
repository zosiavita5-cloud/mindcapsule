
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

<section class="section">
    <div class="container">
        <div class="quote-box">
            <p class="eyebrow" style="color:#f3bdce">A reminder for you</p>
            <h1 style="font-size:45px">Give yourself a little kindness.</h1>

            <p class="quote-text" id="quoteText">
                <?= e($quotes[0]) ?>
            </p>

            <button class="btn btn-outline" type="button" id="nextQuote">
                Another reminder
            </button>
        </div>

        <div class="section-heading" style="margin-top:45px">
            <p class="eyebrow">Positive minds</p>
            <h2>Let good things find you.</h2>
            <p>
                This section can feature uplifting stories, student achievements,
                and encouraging videos. Add links to trusted original sources
                after checking their content and usage permissions.
            </p>
        </div>

        <div class="grid">
            <article class="card">
                <h3>Celebrate small wins.</h3>
                <p>A little progress still deserves recognition.</p>
            </article>
            <article class="card">
                <h3>Rest is part of the process.</h3>
                <p>You can pause, recover your energy, and begin again.</p>
            </article>
            <article class="card">
                <h3>Keep growing at your own pace.</h3>
                <p>Your journey does not need to look like anyone else's.</p>
            </article>
        </div>
    </div>
</section>

<script>
const quotes = <?= json_encode(
    array_values($quotes),
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

let current = 0;

document.getElementById('nextQuote').addEventListener('click', () => {
    if (quotes.length > 1) {
        current = (current + 1) % quotes.length;
    }

    document.getElementById('quoteText').textContent = quotes[current];
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
