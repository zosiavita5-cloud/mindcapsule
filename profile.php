
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$currentUserId = (int) $_SESSION['user_id'];
$profileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$profileId || $profileId < 1) {
    $profileId = $currentUserId;
}

$isOwner = ($profileId === $currentUserId);
$error = '';
$success = '';

/*
|--------------------------------------------------------------------------
| Update profile
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    if (!$isOwner) {
        http_response_code(403);
        exit('You cannot edit another member\'s profile.');
    }

    $displayName = trim((string) ($_POST['display_name'] ?? ''));
    $bio = trim((string) ($_POST['bio'] ?? ''));

    if ($displayName === '' || mb_strlen($displayName) > 100) {
        $error = 'Display name is required and must be at most 100 characters.';
    } elseif (mb_strlen($bio) > 500) {
        $error = 'Your bio must be at most 500 characters.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO user_profiles (user_id, display_name, bio)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE
                display_name = VALUES(display_name),
                bio = VALUES(bio)"
        );

        $stmt->execute([
            $currentUserId,
            $displayName,
            $bio
        ]);

        header('Location: profile.php?updated=1');
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Get profile information
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare(
    "SELECT
        u.id,
        u.username,
        up.display_name,
        up.bio
     FROM users u
     LEFT JOIN user_profiles up ON up.user_id = u.id
     WHERE u.id = ?
     LIMIT 1"
);

$stmt->execute([$profileId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    http_response_code(404);
    exit('Profile not found.');
}

$displayName = trim((string) ($profile['display_name'] ?? ''));

if ($displayName === '') {
    $displayName = (string) $profile['username'];
}

$bio = (string) ($profile['bio'] ?? '');

/*
|--------------------------------------------------------------------------
| Get all published posts belonging to this profile
|--------------------------------------------------------------------------
| Anonymous posts remain visible to their owner on their own profile.
| Other visitors see the anonymous label, not the author's identity.
*/
$stmt = $pdo->prepare(
    "SELECT
        id,
        user_id,
        category,
        content,
        is_anonymous,
        created_at
     FROM posts
     WHERE user_id = ?
       AND status = 'approved'
     ORDER BY created_at DESC"
);

$stmt->execute([$profileId]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['updated'])) {
    $success = 'Your profile has been updated successfully.';
}

$pageTitle = $displayName . ' | Profile';

include __DIR__ . '/includes/header.php';
?>

<main class="fw-container profile-container">

    <a class="fw-back" href="freedom-wall.php">
        ← Back to Freedom Wall
    </a>

    <!-- PROFILE HEADER -->
    <section class="profile-hero">
        <div class="profile-avatar">
            <?= e(mb_strtoupper(mb_substr($displayName, 0, 1))) ?>
        </div>

        <span class="fw-eyebrow">A LITTLE ABOUT ME</span>

        <h1>
            <?= e($displayName) ?><span class="fw-period">.</span>
        </h1>

        <p class="profile-username">
            @<?= e($profile['username']) ?>
        </p>

        <p class="profile-bio">
            <?= $bio !== ''
                ? nl2br(e($bio))
                : 'Still writing my story. ♡' ?>
        </p>

        <div class="profile-stats">
            <span>♡ <?= count($posts) ?> published posts</span>
        </div>

        <?php if ($isOwner): ?>
            <a class="fw-button profile-create-link"
               href="freedom-wall.php">
                Create a post ↗
            </a>
        <?php endif; ?>
    </section>

    <?php if ($success !== ''): ?>
        <div class="fw-notice">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="fw-error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- EDIT PROFILE: OWNER ONLY -->
    <?php if ($isOwner): ?>
        <section class="fw-compose profile-edit-section">
            <div class="fw-section-label">YOUR PERSONAL SPACE</div>
            <h2>Edit your profile <span>♡</span></h2>

            <form method="post" action="profile.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrfToken()) ?>"
                >

                <label for="display_name">Display name</label>

                <input
                    type="text"
                    id="display_name"
                    name="display_name"
                    maxlength="100"
                    value="<?= e($displayName) ?>"
                    placeholder="What should we call you?"
                    required
                >

                <label for="bio">About you</label>

                <textarea
                    id="bio"
                    name="bio"
                    rows="4"
                    maxlength="500"
                    placeholder="A little something about yourself..."
                ><?= e($bio) ?></textarea>

                <button class="fw-button" type="submit">
                    Save changes ♡
                </button>
            </form>
        </section>
    <?php endif; ?>

    <!-- USER POSTS -->
    <section class="fw-feed profile-posts-section">

        <div class="fw-feed-heading">
            <div>
                <div class="fw-section-label">
                    LETTERS FROM THE HEART
                </div>

                <h2>
                    <?= $isOwner ? 'Your little corner.' : 'Shared thoughts.' ?>
                </h2>
            </div>

            <span class="fw-count">
                <?= count($posts) ?>
                <?= count($posts) === 1 ? 'post' : 'posts' ?>
            </span>
        </div>

        <p class="profile-posts-intro">
            Every thought you share becomes a little part of your story.
        </p>

        <?php if (empty($posts)): ?>
            <div class="fw-empty">
                <span class="fw-empty-symbol">୨୧</span>

                <h3>
                    <?= $isOwner
                        ? 'Your story starts here.'
                        : 'No posts to show yet.' ?>
                </h3>

                <p>
                    <?= $isOwner
                        ? 'Share your first thought on the Freedom Wall. It will appear here after publishing.'
                        : 'There are no published posts to display right now.' ?>
                </p>

                <?php if ($isOwner): ?>
                    <a class="fw-button" href="freedom-wall.php">
                        Write your first post ♡
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <div class="profile-post-list">

                <?php foreach ($posts as $post): ?>
                    <?php
                    $isAnonymous = (int) ($post['is_anonymous'] ?? 1) === 1;

                    // Do not reveal the author's identity on anonymous posts
                    // when another person views the profile.
                    $showAnonymous = $isAnonymous && !$isOwner;
                    ?>

                    <article class="fw-post profile-post-card">

                        <div class="fw-post-top">
                            <span class="fw-category">
                                ♡ <?= e($post['category'] ?? 'Just Sharing') ?>
                            </span>

                            <time datetime="<?= e(date(
                                'c',
                                strtotime($post['created_at'])
                            )) ?>">
                                <?= e(date(
                                    'M j, Y · g:i A',
                                    strtotime($post['created_at'])
                                )) ?>
                            </time>
                        </div>

                        <div class="fw-author">
                            <span class="fw-avatar">
                                <?= $showAnonymous
                                    ? '♡'
                                    : e(mb_strtoupper(mb_substr($displayName, 0, 1))) ?>
                            </span>

                            <div>
                                <strong>
                                    <?php if ($showAnonymous): ?>
                                        Anonymous
                                    <?php else: ?>
                                        <?= e($displayName) ?>
                                    <?php endif; ?>
                                </strong>

                                <small>
                                    <?php if ($isAnonymous && $isOwner): ?>
                                        Your anonymous post
                                    <?php elseif ($showAnonymous): ?>
                                        Shared anonymously with the community
                                    <?php else: ?>
                                        A thought shared with the community
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>

                        <p class="fw-post-content">
                            <?= nl2br(e($post['content'])) ?>
                        </p>

                        <div class="fw-post-footer">
                            <span>
                                <?= $isAnonymous
                                    ? 'shared anonymously ♡'
                                    : 'shared with the community ♡' ?>
                            </span>

                            <?php if ($isOwner): ?>
                                <a href="freedom-wall.php">
                                    Visit Freedom Wall ↗
                                </a>
                            <?php endif; ?>
                        </div>

                    </article>
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

    <footer class="fw-community-footer">
        <span>୨୧</span>
        every little feeling has a place here.
        <span>୨୧</span>
    </footer>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
