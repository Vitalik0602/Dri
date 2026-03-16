<?php
$page_title = 'Заявки';
$admin_page = 'requests';
require_once __DIR__ . '/../../admin/_header.php';

// Просмотр одной заявки
$view_id = (int)($_GET['id'] ?? 0);
if ($view_id) {
    db()->update('requests', ['is_read'=>1], 'id=?', [$view_id]);
}

// Удалить
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    db()->delete('requests', 'id=?', [(int)$_GET['delete']]);
    flash('success', 'Заявка удалена');
    header('Location: /admin/requests/'); exit;
}

$filter = $_GET['filter'] ?? '';
$where = ['1=1']; $params = [];
if ($filter === 'unread') { $where[] = 'is_read=0'; }
$requests = db()->fetchAll(
    "SELECT r.*, c.brand, c.model, c.slug FROM requests r LEFT JOIN cars c ON r.car_id=c.id WHERE ".implode(' AND ',$where)." ORDER BY r.created_at DESC",
    $params
);
$unread_total = db()->fetchOne("SELECT COUNT(*) as c FROM requests WHERE is_read=0")['c'] ?? 0;
?>

<div class="admin-topbar">
    <h1>Заявки (<?= count($requests) ?>) <?php if ($unread_total): ?><span class="badge badge-red" style="font-size:.8rem"><?= $unread_total ?> новых</span><?php endif; ?></h1>
    <div style="display:flex;gap:.5rem">
        <a href="/admin/requests/" class="btn btn-ghost btn-sm <?= !$filter?'active':'' ?>">Все</a>
        <a href="/admin/requests/?filter=unread" class="btn btn-ghost btn-sm <?= $filter==='unread'?'active':'' ?>">Непрочитанные</a>
        <?php if ($unread_total): ?>
        <a href="/admin/requests/?mark_all_read=1" class="btn btn-ghost btn-sm" onclick="return confirm('Отметить все как прочитанные?')">✓ Все прочитаны</a>
        <?php endif; ?>
    </div>
</div>

<?php
// Пометить все прочитанными
if (isset($_GET['mark_all_read'])) {
    db()->query("UPDATE requests SET is_read=1");
    flash('success','Все заявки помечены как прочитанные');
    header('Location: /admin/requests/'); exit;
}
?>

<?php if (!empty($view_id)): ?>
<?php $req = db()->fetchOne("SELECT r.*, c.brand, c.model, c.slug FROM requests r LEFT JOIN cars c ON r.car_id=c.id WHERE r.id=?", [$view_id]); ?>
<?php if ($req): ?>
<div class="admin-form-section" style="margin-bottom:1.5rem;border-color:var(--blue-600)">
    <h3>Заявка #<?= $req['id'] ?></h3>
    <div class="admin-grid-2">
        <div>
            <p style="margin-bottom:.5rem"><strong>Имя:</strong> <?= htmlspecialchars($req['name']) ?></p>
            <p style="margin-bottom:.5rem"><strong>Телефон:</strong> <a href="tel:<?= preg_replace('/[^+\d]/','',$req['phone']) ?>" style="color:var(--blue-400)"><?= htmlspecialchars($req['phone']) ?></a></p>
            <p style="margin-bottom:.5rem"><strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($req['created_at'])) ?></p>
            <?php if ($req['car_id']): ?>
            <p style="margin-bottom:.5rem"><strong>Автомобиль:</strong> <a href="/cars/<?= htmlspecialchars($req['slug']) ?>/" target="_blank" style="color:var(--blue-400)"><?= htmlspecialchars($req['brand'].' '.$req['model']) ?></a></p>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($req['message']): ?>
            <p style="margin-bottom:.5rem"><strong>Сообщение:</strong></p>
            <p style="background:var(--surface);padding:1rem;border-radius:8px;color:var(--text-2)"><?= nl2br(htmlspecialchars($req['message'])) ?></p>
            <?php else: ?>
            <p style="color:var(--text-3)">Сообщение не оставлено</p>
            <?php endif; ?>
        </div>
    </div>
    <div style="margin-top:1rem;display:flex;gap:.75rem">
        <a href="tel:<?= preg_replace('/[^+\d]/','',$req['phone']) ?>" class="btn btn-primary btn-sm">📞 Позвонить</a>
        <a href="https://wa.me/<?= preg_replace('/[^+\d]/','',$req['phone']) ?>" target="_blank" class="btn btn-green btn-sm">💬 WhatsApp</a>
        <a href="/admin/requests/" class="btn btn-ghost btn-sm">← Назад</a>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<table class="admin-table">
    <thead><tr><th>#</th><th>Имя / Телефон</th><th>Автомобиль</th><th>Сообщение</th><th>Дата</th><th>Статус</th><th>Действия</th></tr></thead>
    <tbody>
    <?php if (empty($requests)): ?>
    <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-3)">Заявок нет</td></tr>
    <?php else: foreach ($requests as $r): ?>
    <tr style="<?= !$r['is_read'] ? 'background:rgba(26,86,219,0.05)' : '' ?>">
        <td style="color:var(--text-3)"><?= $r['id'] ?></td>
        <td>
            <?php if (!$r['is_read']): ?><span class="badge badge-blue" style="display:block;width:fit-content;margin-bottom:.3rem">NEW</span><?php endif; ?>
            <strong><?= htmlspecialchars($r['name']) ?></strong><br>
            <a href="tel:<?= preg_replace('/[^+\d]/','',$r['phone']) ?>" style="color:var(--blue-400);font-size:.88rem"><?= htmlspecialchars($r['phone']) ?></a>
        </td>
        <td style="font-size:.88rem;color:var(--text-2)">
            <?php if ($r['car_id']): ?>
            <a href="/cars/<?= htmlspecialchars($r['slug']) ?>/" target="_blank" style="color:var(--blue-400)"><?= htmlspecialchars($r['brand'].' '.$r['model']) ?></a>
            <?php else: ?>—<?php endif; ?>
        </td>
        <td style="max-width:200px;font-size:.85rem;color:var(--text-3)">
            <?= $r['message'] ? htmlspecialchars(mb_substr($r['message'],0,80)) . (mb_strlen($r['message'])>80?'…':'') : '—' ?>
        </td>
        <td style="font-size:.82rem;color:var(--text-3);white-space:nowrap"><?= date('d.m.Y<br>H:i', strtotime($r['created_at'])) ?></td>
        <td><span class="badge <?= $r['is_read']?'badge-gray':'badge-blue' ?>"><?= $r['is_read']?'Прочитано':'Новая' ?></span></td>
        <td>
            <div style="display:flex;gap:.35rem">
                <a href="/admin/requests/?id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm" title="Подробнее">👁</a>
                <a href="https://wa.me/<?= preg_replace('/[^+\d]/','',$r['phone']) ?>" target="_blank" class="btn btn-ghost btn-sm" title="WhatsApp">💬</a>
                <a href="/admin/requests/?delete=<?= $r['id'] ?>" class="btn btn-ghost btn-sm" style="color:var(--red)" onclick="return confirm('Удалить заявку?')" title="Удалить">🗑</a>
            </div>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../../admin/_footer.php'; ?>
