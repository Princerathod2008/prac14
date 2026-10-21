<?php
require_once __DIR__ . '/auth.php';

$user = current_user();
$notice = take_flash();
$pageTitle = $pageTitle ?? 'Campus Admin';

$menus = [
    ['Dashboard', 'index.php', ['admin', 'staff']],
    ['Students', 'students.php', ['admin', 'staff']],
    ['Registrations', 'registrations.php', ['admin', 'staff']],
    ['Users', 'users.php', ['admin']],
    ['Audit Log', 'audit.php', ['admin']]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Campus Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark topbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            Campus+
        </a>

        <?php if ($user): ?>
            <div class="navbar-nav me-auto flex-row flex-wrap">
                <?php foreach ($menus as $menu): ?>
                    <?php if (in_array($user['role'], $menu[2], true)): ?>
                        <a class="nav-link px-2"
                           href="<?= e($menu[1]) ?>">
                            <?= e($menu[0]) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <span class="text-white me-3">
                <?= e($user['name']) ?>
            </span>

            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token"
                       value="<?= e(csrf_token()) ?>">

                <button class="btn btn-outline-light btn-sm">
                    Logout
                </button>
            </form>
        <?php endif; ?>
    </div>
</nav>

<main class="container py-4">

<?php if ($notice): ?>
    <div class="alert alert-<?= e($notice['type']) ?>">
        <?= e($notice['message']) ?>
    </div>
<?php endif; ?>