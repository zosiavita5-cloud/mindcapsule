
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

/* UPDATE PROFILE */
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

/* GET PROFILE INFORMATION */
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

/* GET PUBLISHED POSTS */
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

<style>
/* =====================================
   PROFILE PAGE - FREEDOM WALL DESIGN
===================================== */

.profile-container {
    width: calc(100% - 40px);
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 0 55px;
    color: #393631;
}

.profile-container * {
    box-sizing: border-box;
}

.profile-container .fw-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 22px;
    color: #916b7c;
    font-size: 14px;
    text-decoration: none;
    transition: color .2s ease;
}

.profile-container .fw-back:hover {
    color: #bd829b;
}

/* PROFILE HERO */

.profile-hero {
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
    padding: 46px 25px 38px;
    border: 1px solid #eee3e8;
    border-radius: 26px;
    background:
        radial-gradient(
            ellipse at top left,
            #f9e9f0 0%,
            transparent 55%
        ),
        radial-gradient(
            ellipse at bottom right,
            #edf4e7 0%,
            transparent 55%
        ),
        #fffdfb;
    text-align: center;
    box-shadow: 0 10px 32px rgba(80, 57, 68, .06);
}

.profile-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    border: 5px solid #fff;
    border-radius: 50%;
    background: linear-gradient(135deg, #e8cbd8, #dce9d3);
    color: #775668;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 40px;
    box-shadow: 0 5px 20px rgba(100, 70, 85, .12);
}

.profile-container .fw-eyebrow,
.profile-container .fw-section-label {
    color: #a4778b;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.profile-hero h1 {
    margin: 12px 0 6px;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(32px, 5vw, 46px);
    font-weight: 500;
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.profile-container .fw-period {
    color: #c28ca3;
}

.profile-username {
    margin: 8px 0 18px;
    color: #91868b;
    font-size: 14px;
}

.profile-bio {
    max-width: 580px;
    margin: 0 auto 22px;
    color: #69615f;
    font-size: 15px;
    line-height: 1.9;
    overflow-wrap: anywhere;
}

.profile-stats {
    margin: 0 0 22px;
    color: #a4778b;
    font-size: 13px;
}

/* BUTTONS */

.profile-container .fw-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 12px 21px;
    border: 1px solid #b9879b;
    border-radius: 30px;
    background: #a9798d;
    color: #fff;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition:
        background .2s ease,
        transform .2s ease,
        box-shadow .2s ease;
}

.profile-container .fw-button:hover {
    background: #906478;
    box-shadow: 0 5px 14px rgba(144, 100, 120, .18);
    transform: translateY(-1px);
}

.profile-container .profile-create-link {
    margin-top: 2px;
}

/* NOTIFICATIONS */

.profile-container .fw-notice,
.profile-container .fw-error {
    margin: 18px 0;
    padding: 15px 18px;
    border-radius: 13px;
    font-size: 14px;
    line-height: 1.7;
}

.profile-container .fw-notice {
    border: 1px solid #dce8d6;
    background: #f3f8ef;
    color: #486847;
}

.profile-container .fw-error {
    border: 1px solid #efd4d8;
    background: #fff3f4;
    color: #9b4654;
}

/* EDIT PROFILE */

.profile-edit-section {
    margin: 30px 0 40px;
    padding: 30px;
    border: 1px solid #eee4e7;
    border-radius: 22px;
    background: #fffdfb;
    box-shadow: 0 8px 26px rgba(80, 57, 68, .045);
}

.profile-edit-section h2,
.profile-posts-section h2 {
    margin: 12px 0 22px;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(25px, 4vw, 32px);
    font-weight: 500;
    line-height: 1.35;
}

.profile-edit-section h2 span {
    color: #c28ca3;
}

.profile-edit-section form {
    display: flex;
    flex-direction: column;
    gap: 11px;
}

.profile-edit-section label {
    margin-top: 7px;
    color: #62575c;
    font-size: 13px;
    font-weight: 600;
}

.profile-edit-section input,
.profile-edit-section textarea {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #e8dfe2;
    border-radius: 12px;
    outline: none;
    background: #fff;
    color: #393538;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.7;
    transition: border-color .2s, box-shadow .2s;
}

.profile-edit-section textarea {
    min-height: 115px;
    resize: vertical;
}

.profile-edit-section input:focus,
.profile-edit-section textarea:focus {
    border-color: #c995aa;
    box-shadow: 0 0 0 3px rgba(201, 149, 170, .15);
}

.profile-edit-section input::placeholder,
.profile-edit-section textarea::placeholder {
    color: #aaa0a4;
}

.profile-edit-section .fw-button {
    align-self: flex-start;
    margin-top: 10px;
}

/* POSTS SECTION */

.profile-posts-section {
    margin-top: 38px;
}

.profile-container .fw-feed-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 8px;
}

.profile-container .fw-feed-heading h2 {
    margin-bottom: 0;
}

.profile-container .fw-count {
    flex-shrink: 0;
    padding: 8px 13px;
    border: 1px solid #eee1e6;
    border-radius: 30px;
    background: #fbf1f5;
    color: #986d80;
    font-size: 12px;
}

.profile-posts-intro {
    margin: 12px 0 24px;
    color: #85817c;
    font-size: 14px;
    line-height: 1.8;
}

.profile-post-list {
    display: grid;
    gap: 20px;
}

/* INDIVIDUAL POST CARD */

.profile-container .profile-post-card {
    padding: 24px;
    border: 1px solid #eee7e5;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 7px 24px rgba(70, 60, 55, .045);
    transition: transform .2s ease, box-shadow .2s ease;
}

.profile-container .profile-post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(70, 60, 55, .075);
}

.profile-post-card .fw-post-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.profile-post-card .fw-category {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 30px;
    background: #f8edf1;
    color: #986d80;
    font-size: 12px;
}

.profile-post-card time {
    color: #96918c;
    font-size: 12px;
}

.profile-post-card .fw-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}

.profile-post-card .fw-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 43px;
    height: 43px;
    border-radius: 50%;
    background: #f2e5e9;
    color: #9b7182;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 19px;
}

.profile-post-card .fw-author strong {
    display: block;
    color: #3d3b38;
    font-size: 14px;
}

.profile-post-card .fw-author small {
    display: block;
    margin-top: 4px;
    color: #96918c;
    font-size: 11px;
    line-height: 1.5;
}

.profile-post-card .fw-post-content {
    margin: 0;
    color: #514f4a;
    font-size: 15px;
    line-height: 1.9;
    overflow-wrap: anywhere;
}

.profile-post-card .fw-post-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px solid #f0ebea;
    color: #a17e8d;
    font-size: 12px;
}

.profile-post-card .fw-post-footer a {
    color: #8d6879;
    font-weight: 600;
    text-decoration: none;
}

.profile-post-card .fw-post-footer a:hover {
    text-decoration: underline;
}

/* EMPTY POSTS */

.profile-container .fw-empty {
    padding: 42px 24px;
    border: 1px dashed #e7d6dd;
    border-radius: 22px;
    background: #fffafc;
    text-align: center;
}

.profile-container .fw-empty-symbol {
    display: block;
    margin-bottom: 14px;
    color: #bd849a;
    font-size: 34px;
}

.profile-container .fw-empty h3 {
    margin: 10px 0;
    color: #4d4548;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 24px;
    font-weight: 500;
}

.profile-container .fw-empty p {
    max-width: 440px;
    margin: 12px auto 22px;
    color: #85817c;
    font-size: 14px;
    line-height: 1.8;
}

/* COMMUNITY FOOTER */

.profile-container .fw-community-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 48px;
    color: #9b8790;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 13px;
    text-align: center;
}

.profile-container .fw-community-footer span {
    color: #bd849a;
}

/* MOBILE RESPONSIVE */

@media (max-width: 600px) {
    .profile-container {
        width: calc(100% - 24px);
        padding-top: 20px;
    }

    .profile-hero {
        padding: 34px 18px 30px;
        border-radius: 22px;
    }

    .profile-avatar {
        width: 85px;
        height: 85px;
        font-size: 34px;
    }

    .profile-edit-section {
        padding: 22px 17px;
    }

    .profile-container .profile-post-card {
        padding: 19px 16px;
    }

    .profile-container .fw-feed-heading {
        align-items: flex-start;
    }

    .profile-post-card .fw-post-top {
        align-items: flex-start;
        flex-direction: column;
    }

    .profile-post-card .fw-post-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .profile-edit-section .fw-button {
        width: 100%;
    }
}
</style>

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

                    // Keep anonymous authors private to other visitors.
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
