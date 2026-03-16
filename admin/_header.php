<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$flash = get_flash();
$admin_page = $admin_page ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page_title ?? 'Панель управления') ?> — Drive Hub Admin</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css?v=1.2">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="admin-logo">
        <span>⬡</span> Drive<strong style="color:var(--blue-400)">Hub</strong>
    </div>
    <nav class="admin-nav">
        <a href="/admin/" class="<?= $admin_page==='dashboard'?'active':'' ?>">
            📊 Дашборд
        </a>
        <a href="/admin/cars/" class="<?= $admin_page==='cars'?'active':'' ?>">
            🚗 Автомобили
        </a>
        <a href="/admin/cars/add.php" class="<?= $admin_page==='car-add'?'active':'' ?>">
            ➕ Добавить авто
        </a>
        <a href="/admin/requests/" class="<?= $admin_page==='requests'?'active':'' ?>">
            📩 Заявки
            <?php
            $unread = db()->fetchOne("SELECT COUNT(*) as c FROM requests WHERE is_read=0");
            if (($unread['c'] ?? 0) > 0): ?>
            <span style="background:var(--red);color:#fff;border-radius:20px;padding:.1rem .5rem;font-size:.72rem;margin-left:auto"><?= $unread['c'] ?></span>
            <?php endif; ?>
        </a>
        <a href="/admin/settings.php" class="<?= $admin_page==='settings'?'active':'' ?>">
            ⚙️ Настройки
        </a>
        <a href="/" target="_blank">🌐 Сайт</a>
        <a href="/admin/logout.php" style="color:var(--red);margin-top:auto">🚪 Выйти</a>
    </nav>
</aside>

<main class="admin-content">
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>" style="margin-bottom:1.5rem"><?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>
