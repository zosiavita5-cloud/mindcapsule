
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$userId = (int) $_SESSION['user_id'];
$error = '';

$categories = [
    'School Life',
    'Personal Growth',
    'Family and Friends',
    'A Small Win',
    'Just Sharing'
];

if (empty($_SESSION['create_post_token'])) {
    $_SESSION['create_post_token'] = bin2hex(random_bytes(32));
}

function postEscape($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');
    $content = trim((string) ($_POST['content'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? 'Just Sharing'));
    $anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    if (!hash_equals($_SESSION['create_post_token'], $token)) {
        $error = 'Invalid request. Please refresh the page.';
    } elseif (!in_array($category, $categories, true)) {
        $error = 'Please select a valid category.';
    } elseif ($content === '' || mb_strlen($content) > 1000) {
        $error = 'Your post must contain 1–1,000 characters.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO posts
                    (user_id, category, content, is_anonymous, status)
                 VALUES (?, ?, ?, ?, 'approved')"
            );

            $stmt->execute([
                $userId,
                $category,
                $content,
                $anonymous
            ]);

            unset($_SESSION['create_post_token']);

            header('Location: freedom-wall.php?submitted=1');
            exit;
        } catch (PDOException $e) {
            error_log('Create post error: ' . $e->getMessage());
            $error = 'Unable to publish your post. Please check the posts table columns.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create a Post | Mind Capsule</title>
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .create-page {
            max-width: 720px;
            width: calc(100% - 32px);
            margin: 45px auto;
            color: #542b3c;
        }

        .create-card {
            padding: 28px;
            border: 1px solid #f0d3df;
            border-radius: 22px;
            background: #fff7fa;
            box-shadow: 0 12px 35px rgba(116, 45, 77, .08);
        }

        .create-card h1 {
            margin-top: 0;
            font-size: clamp(27px, 5vw, 36px);
        }

        .create-description {
            color: #936477;
            line-height: 1.7;
        }

        .create-card textarea,
        .create-card select {
            box-sizing: border-box;
            width: 100%;
            margin-top: 10px;
            padding: 14px;
            border: 1px solid #e8bfd0;
            border-radius: 12px;
            background: white;
            color: #542b3c;
            font: inherit;
        }

        .create-card textarea {
            min-height: 220px;
            line-height: 1.7;
            resize: vertical;
        }

        .create-card textarea:focus,
        .create-card select:focus {
            outline: 2px solid #c94f7d;
            outline-offset: 2px;
        }

        .create-field {
            margin-top: 20px;
        }

        .create-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .create-button {
            padding: 13px 21px;
            border: 0;
            border-radius: 12px;
            background: #c94f7d;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .create-button:hover {
            background: #a93461;
        }

        .create-back {
            color: #a93461;
            text-decoration: none;
            font-weight: 700;
        }

        .create-error {
            padding: 12px;
            border-radius: 10px;
            background: #ffe8ee;
            color: #8d234c;
        }

        .create-anonymous {
            display: flex;
            gap: 9px;
            align-items: center;
            margin-top: 18px;
        }

        .create-anonymous input {
            accent-color: #c94f7d;
        }

        .create-note {
            color: #936477;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 500px) {
            .create-card {
                padding: 19px;
            }

            .create-bottom {
                align-items: stretch;
                flex-direction: column;
            }

            .create-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="create-page">
        <section class="create-card">
            <h1>Create a Post</h1>

            <p class="create-description">
                Your thoughts deserve a space. Share what's on your mind
                with the Mind Capsule community.
            </p>

            <?php if ($error !== ''): ?>
                <p class="create-error"><?= postEscape($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="create-post.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= postEscape($_SESSION['create_post_token']) ?>"
                >

                <div class="create-field">
                    <label for="category"><strong>Choose a category</strong></label>
                    <select id="category" name="category" required>
                        <?php foreach ($categories as $item): ?>
                            <option
                                value="<?= postEscape($item) ?>"
                                <?= (($_POST['category'] ?? 'Just Sharing') === $item) ? 'selected' : '' ?>
                            >
                                <?= postEscape($item) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="create-field">
                    <label for="content"><strong>Your thoughts</strong></label>
                    <textarea
                        id="content"
                        name="content"
                        maxlength="1000"
                        placeholder="Dear diary, today I'm feeling..."
                        required
                    ><?= postEscape($_POST['content'] ?? '') ?></textarea>
                    <p class="create-note">Maximum 1,000 characters.</p>
                </div>

                <label class="create-anonymous">
                    <input
                        type="checkbox"
                        name="is_anonymous"
                        value="1"
                        <?= !isset($_POST['is_anonymous']) && $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : (isset($_POST['is_anonymous']) ? 'checked' : '') ?>
                    >
                    <span>Post anonymously</span>
                </label>

                <p class="create-note">
                    Your post will be published immediately. Your account ID
                    is stored internally so the post can appear on your profile.
                </p>

                <div class="create-bottom">
                    <a class="create-back" href="freedom-wall.php">
                        &larr; Back to Freedom Wall
                    </a>

                    <button class="create-button" type="submit">
                        Publish Post ♡
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
