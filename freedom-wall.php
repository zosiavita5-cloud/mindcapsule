
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

requireLogin();

$pageTitle = 'Task Planner';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['task_name'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');

        if ($name === '' || mb_strlen($name) > 255) {
            $error = 'Enter a task name of up to 255 characters.';
        } elseif (
            $deadline !== '' &&
            (
                !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline) ||
                !checkdate(
                    (int) substr($deadline, 5, 2),
                    (int) substr($deadline, 8, 2),
                    (int) substr($deadline, 0, 4)
                )
            )
        ) {
            $error = 'Please enter a valid deadline.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO tasks (user_id, task_name, deadline)
                     VALUES (?, ?, ?)'
                );

                $stmt->execute([
                    $_SESSION['user_id'],
                    $name,
                    $deadline !== '' ? $deadline : null
                ]);

                header('Location: task-planner.php?added=1');
                exit;
            } catch (PDOException $e) {
                $error = 'Unable to add your task. Please try again.';
            }
        }
    } elseif ($action === 'toggle' || $action === 'delete') {
        $id = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);

        if ($id) {
            if ($action === 'toggle') {
                $stmt = $pdo->prepare(
                    'UPDATE tasks
                     SET is_completed = 1 - is_completed
                     WHERE id = ? AND user_id = ?'
                );
                $stmt->execute([$id, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare(
                    'DELETE FROM tasks WHERE id = ? AND user_id = ?'
                );
                $stmt->execute([$id, $_SESSION['user_id']]);
            }
        }

        header('Location: task-planner.php');
        exit;
    }
}

$stmt = $pdo->prepare(
    'SELECT id, task_name, deadline, is_completed
     FROM tasks
     WHERE user_id = ?
     ORDER BY is_completed ASC,
              deadline IS NULL ASC,
              deadline ASC,
              id DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalTasks = count($tasks);

$completed = count(array_filter(
    $tasks,
    fn($task) => (int) $task['is_completed'] === 1
));

$progress = $totalTasks > 0
    ? (int) round(($completed / $totalTasks) * 100)
    : 0;

include __DIR__ . '/includes/header.php';
?>

<style>
/* ===== MIND CAPSULE TASK PLANNER ===== */

.task-planner {
    --tp-red: #b83f62;
    --tp-dark: #683448;
    --tp-muted: #927783;
    --tp-pink: #f5dce5;
    --tp-light: #fff8fb;

    color: var(--tp-dark);
    padding: 40px 0 65px;
}

.task-planner,
.task-planner * {
    box-sizing: border-box;
}

.task-planner .tp-container {
    max-width: 1000px;
    margin: 0 auto;
}

/* Page heading */

.task-planner .tp-heading {
    text-align: center;
    max-width: 690px;
    margin: 0 auto 35px;
}

.task-planner .tp-eyebrow {
    color: var(--tp-red);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
}

.task-planner .tp-heading h1 {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(30px, 5vw, 46px);
    line-height: 1.2;
    margin: 12px 0;
}

.task-planner .tp-heading > p:last-child {
    color: var(--tp-muted);
    font-size: 14px;
    line-height: 1.8;
}

/* Two-column layout without a large outer box */

.task-planner .tp-layout {
    display: grid;
    grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.15fr);
    gap: 35px;
    align-items: start;
}

.task-planner .tp-section-title {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 24px;
    margin: 0 0 8px;
}

.task-planner .tp-description {
    color: var(--tp-muted);
    font-size: 13px;
    line-height: 1.7;
    margin: 0 0 22px;
}

/* Form */

.task-planner .tp-field {
    margin-bottom: 20px;
}

.task-planner .tp-field label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 9px;
}

.task-planner .tp-field input {
    width: 100%;
    min-width: 0;
    padding: 13px 14px;
    border: 1px solid var(--tp-pink);
    border-radius: 12px;
    background: var(--tp-light);
    color: var(--tp-dark);
    font: inherit;
    font-size: 14px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.task-planner .tp-field input:focus {
    border-color: var(--tp-red);
    box-shadow: 0 0 0 3px rgba(184, 63, 98, .09);
}

.task-planner .tp-primary-btn {
    display: block;
    width: 100%;
    border: none;
    border-radius: 12px;
    padding: 14px 18px;
    background: var(--tp-red);
    color: #fff;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s, transform .2s;
}

.task-planner .tp-primary-btn:hover {
    background: var(--tp-dark);
    transform: translateY(-1px);
}

/* Error and success messages */

.task-planner .tp-message {
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 13px;
    line-height: 1.6;
}

.task-planner .tp-error {
    color: #9d2945;
    background: #fff0f2;
    border: 1px solid #f1c9d2;
}

.task-planner .tp-success {
    color: #28633b;
    background: #e8f6ed;
    border: 1px solid #c6e7d0;
}

/* Progress */

.task-planner .tp-progress {
    margin: 0 0 24px;
    padding: 17px 18px;
    background: var(--tp-light);
    border: 1px solid var(--tp-pink);
    border-radius: 15px;
}

.task-planner .tp-progress-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 11px;
    font-size: 13px;
}

.task-planner .tp-progress-percent {
    color: var(--tp-red);
    font-weight: 700;
}

.task-planner .tp-progress-track {
    width: 100%;
    height: 7px;
    background: #f0e2e8;
    border-radius: 20px;
    overflow: hidden;
}

.task-planner .tp-progress-fill {
    height: 100%;
    background: var(--tp-red);
    border-radius: 20px;
    transition: width .3s;
}

/* Individual task cards */

.task-planner .tp-task-list {
    display: grid;
    gap: 12px;
}

.task-planner .tp-task {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 17px;
    border: 1px solid var(--tp-pink);
    border-radius: 15px;
    background: #fffafd;
    transition: border-color .2s, background .2s;
}

.task-planner .tp-task:hover {
    border-color: #e6b3c5;
}

.task-planner .tp-task-info {
    min-width: 0;
    flex: 1;
}

.task-planner .tp-task-name {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.task-planner .tp-task-name.tp-done {
    color: var(--tp-muted);
    text-decoration: line-through;
}

.task-planner .tp-deadline {
    margin-top: 6px;
    color: var(--tp-muted);
    font-size: 12px;
    line-height: 1.6;
}

.task-planner .tp-deadline.tp-overdue {
    color: #b83255;
    font-weight: 700;
}

.task-planner .tp-status {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 8px;
    border-radius: 20px;
    background: #f8e6ed;
    color: var(--tp-red);
    font-size: 10px;
    font-weight: 700;
}

.task-planner .tp-status.tp-status-done {
    background: #e8f6ed;
    color: #28633b;
}

/* Task action buttons */

.task-planner .tp-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 6px;
    flex-shrink: 0;
}

.task-planner .tp-action-btn {
    padding: 7px 10px;
    border: 1px solid var(--tp-pink);
    border-radius: 9px;
    background: #fff;
    color: var(--tp-dark);
    font: inherit;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s, color .2s;
}

.task-planner .tp-action-btn:hover {
    background: var(--tp-red);
    border-color: var(--tp-red);
    color: #fff;
}

.task-planner .tp-delete-btn:hover {
    background: #8f2945;
    border-color: #8f2945;
}

/* Empty state */

.task-planner .tp-empty {
    text-align: center;
    padding: 35px 20px;
    border: 1px dashed #e5bac9;
    border-radius: 16px;
    background: var(--tp-light);
}

.task-planner .tp-empty-mark {
    display: block;
    color: var(--tp-red);
    font-family: Georgia, "Times New Roman", serif;
    font-size: 30px;
    margin-bottom: 10px;
}

.task-planner .tp-empty h3 {
    font-family: Georgia, "Times New Roman", serif;
    margin: 0 0 8px;
}

.task-planner .tp-empty p {
    color: var(--tp-muted);
    font-size: 13px;
    line-height: 1.7;
    margin: 0;
}

.task-planner .tp-reminder {
    text-align: center;
    margin: 30px auto 0;
    color: var(--tp-muted);
    font-size: 12px;
    line-height: 1.8;
}

/* Responsive */

@media (max-width: 760px) {
    .task-planner {
        padding-top: 27px;
    }

    .task-planner .tp-layout {
        grid-template-columns: minmax(0, 1fr);
        gap: 35px;
    }
}

@media (max-width: 480px) {
    .task-planner .tp-task {
        flex-direction: column;
    }

    .task-planner .tp-actions {
        justify-content: flex-start;
        width: 100%;
    }

    .task-planner .tp-action-btn {
        padding: 8px 11px;
    }
}
</style>

<section class="section soft-section task-planner">
    <div class="container tp-container">

        <header class="tp-heading">
            <p class="tp-eyebrow">Mind Capsule / One step at a time</p>
            <h1>Make space for what matters.</h1>
            <p>
                You don't need to do everything at once.
                Start small, keep going, and celebrate every little progress.
            </p>
        </header>

        <div class="tp-layout">

            <!-- Add task form -->
            <section class="tp-form-section">
                <h2 class="tp-section-title">A new little goal</h2>
                <p class="tp-description">
                    Write down one thing you want to get done.
                </p>

                <?php if ($error !== ''): ?>
                    <div class="tp-message tp-error" role="alert">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['added'])): ?>
                    <div class="tp-message tp-success" role="status">
                        Your task has been added to your list.
                    </div>
                <?php endif; ?>

                <form method="POST" action="task-planner.php">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrfToken()) ?>"
                    >
                    <input type="hidden" name="action" value="add">

                    <div class="tp-field">
                        <label for="task_name">What do you need to do?</label>
                        <input
                            id="task_name"
                            name="task_name"
                            type="text"
                            maxlength="255"
                            required
                            placeholder="e.g. Review my notes"
                        >
                    </div>

                    <div class="tp-field">
                        <label for="deadline">When is it due?</label>
                        <input
                            id="deadline"
                            name="deadline"
                            type="date"
                        >
                    </div>

                    <button class="tp-primary-btn" type="submit">
                        + Add to my list
                    </button>
                </form>
            </section>

            <!-- Task list -->
            <section class="tp-list-section">
                <h2 class="tp-section-title">My little to-do list</h2>
                <p class="tp-description">
                    Every completed task is progress worth celebrating.
                </p>

                <div class="tp-progress">
                    <div class="tp-progress-top">
                        <span>
                            <?= $completed ?> of <?= $totalTasks ?> completed
                        </span>
                        <span class="tp-progress-percent">
                            <?= $progress ?>%
                        </span>
                    </div>

                    <div
                        class="tp-progress-track"
                        role="progressbar"
                        aria-label="Task completion"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-valuenow="<?= $progress ?>"
                    >
                        <div
                            class="tp-progress-fill"
                            style="width: <?= $progress ?>%;"
                        ></div>
                    </div>
                </div>

                <?php if (!$tasks): ?>
                    <div class="tp-empty">
                        <span class="tp-empty-mark">♡</span>
                        <h3>A fresh start</h3>
                        <p>
                            Your task list is waiting for you.
                            Add one small goal to begin.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="tp-task-list">
                        <?php foreach ($tasks as $task): ?>
                            <?php
                                $isDone = (int) $task['is_completed'] === 1;

                                $isOverdue =
                                    !$isDone &&
                                    !empty($task['deadline']) &&
                                    $task['deadline'] < date('Y-m-d');
                            ?>

                            <article class="tp-task">
                                <div class="tp-task-info">
                                    <p class="tp-task-name <?= $isDone ? 'tp-done' : '' ?>">
                                        <?= e($task['task_name']) ?>
                                    </p>

                                    <div class="tp-deadline <?= $isOverdue ? 'tp-overdue' : '' ?>">
                                        <?php if (!empty($task['deadline'])): ?>
                                            <?php if ($isOverdue): ?>
                                                Past due ·
                                            <?php endif; ?>

                                            Due:
                                            <?= e(date(
                                                'M j, Y',
                                                strtotime($task['deadline'])
                                            )) ?>
                                        <?php else: ?>
                                            No deadline set
                                        <?php endif; ?>
                                    </div>

                                    <span class="tp-status <?= $isDone ? 'tp-status-done' : '' ?>">
                                        <?= $isDone ? 'Completed' : 'In progress' ?>
                                    </span>
                                </div>

                                <div class="tp-actions">
                                    <form method="POST" action="task-planner.php">
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrfToken()) ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="task_id"
                                            value="<?= (int) $task['id'] ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="toggle"
                                        >

                                        <button class="tp-action-btn" type="submit">
                                            <?= $isDone ? 'Undo' : 'Complete' ?>
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="task-planner.php"
                                        onsubmit="return confirm('Delete this task?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e(csrfToken()) ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="task_id"
                                            value="<?= (int) $task['id'] ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <button
                                            class="tp-action-btn tp-delete-btn"
                                            type="submit"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div>

        <p class="tp-reminder">
            You are allowed to rest, reset, and begin again.
            <br>
            Small steps count, too.
        </p>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
