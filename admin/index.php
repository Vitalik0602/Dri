<?php
$page_title = 'Дашборд';
$admin_page = 'dashboard';
require_once __DIR__ . '/_header.php';

$total_cars   = db()->fetchOne("SELECT COUNT(*) as c FROM cars WHERE status='active'")['c'] ?? 0;
$sold_cars    = db()->fetchOne("SELECT COUNT(*) as c FROM cars WHERE status='sold'")['c'] ?? 0;
$total_req    = db()->fetchOne("SELECT COUNT(*) as c FROM requests")['c'] ?? 0;
$unread_req   = db()->fetchOne("SELECT COUNT(*) as c FROM requests WHERE is_read=0")['c'] ?? 0;
$total_views  = db()->fetchOne("SELECT SUM(views) as v FROM cars")['v'] ?? 0;

$recent_requests = db()->fetchAll(
    "SELECT r.*, c.brand, c.model FROM requests r LEFT JOIN cars c ON r.car_id=c.id ORDER BY r.created_at DESC LIMIT 8"
);
$recent_cars = db()->fetchAll(
    "SELECT * FROM cars ORDER BY created_at DESC LIMIT 6"
);
?>

<div class="admin-topbar">
    <h1>Дашборд</h1>
    <a href="/admin/cars/add.php" class="btn btn-primary btn-sm">➕ Добавить авто</a>
</div>

<!-- Stat cards -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon">🚗</div>
        <div class="stat-label">Авто в наличии</div>
        <div class="stat-value" style="color:var(--blue-400)"><?= $total_cars ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Продано</div>
        <div class="stat-value" style="color:var(--green)"><?= $sold_cars ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📩</div>
        <div class="stat-label">Заявок всего</div>
        <div class="stat-value"><?= $total_req ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🔴</div>
        <div class="stat-label">Непрочитанных заявок</div>
        <div class="stat-value" style="color:var(--red)"><?= $unread_req ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👁</div>
        <div class="stat-label">Просмотров авто</div>
        <div class="stat-value"><?= number_format($total_views, 0, ',', ' ') ?></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:0">

    <!-- Последние заявки -->
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 style="font-size:1rem">Последние заявки</h3>
            <a href="/admin/requests/" class="btn btn-ghost btn-sm">Все заявки</a>
        </div>
        <table class="admin-table">
            <thead><tr><th>Имя / Телефон</th><th>Авто</th><th>Дата</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($recent_requests)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:2rem">Заявок нет</td></tr>
            <?php else: foreach ($recent_requests as $r): ?>
            <tr style="<?= !$r['is_read'] ? 'background:rgba(26,86,219,0.05)' : '' ?>">
                <td>
                    <?php if (!$r['is_read']): ?><span class="badge badge-blue" style="margin-right:.4rem">NEW</span><?php endif; ?>
                    <strong><?= htmlspecialchars($r['name']) ?></strong><br>
                    <small style="color:var(--text-3)"><?= htmlspecialchars($r['phone']) ?></small>
                </td>
                <td style="color:var(--text-2);font-size:.85rem"><?= $r['brand'] ? htmlspecialchars($r['brand'].' '.$r['model']) : '—' ?></td>
                <td style="font-size:.82rem;color:var(--text-3);white-space:nowrap"><?= date('d.m H:i', strtotime($r['created_at'])) ?></td>
                <td><a href="/admin/requests/?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm">→</a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Последние авто -->
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 style="font-size:1rem">Последние добавленные авто</h3>
            <a href="/admin/cars/" class="btn btn-ghost btn-sm">Все авто</a>
        </div>
        <table class="admin-table">
            <thead><tr><th>Авто</th><th>Цена</th><th>Статус</th><th></th></tr></thead>
            <tbody>
            <?php if (empty($recent_cars)): ?>
            <tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:2rem">Авто нет</td></tr>
            <?php else: foreach ($recent_cars as $c): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($c['brand'].' '.$c['model']) ?></strong><br>
                    <small style="color:var(--text-3)"><?= $c['year'] ?></small>
                </td>
                <td style="font-weight:600;white-space:nowrap"><?= number_format($c['price'],0,',',' ') ?> ₽</td>
                <td>
                    <?php
                    $badges = ['active'=>['badge-green','Активен'],'sold'=>['badge-red','Продано'],'hidden'=>['badge-gray','Скрыт']];
                    [$cls,$lbl] = $badges[$c['status']] ?? ['badge-gray','?'];
                    ?>
                    <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                </td>
                <td><a href="/admin/cars/edit.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">✏️</a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/_footer.php'; ?>
