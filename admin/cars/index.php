<?php
$page_title = 'Автомобили';
$admin_page = 'cars';
require_once __DIR__ . '/../admin/_header.php';

// Удаление
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    csrf_check();
    $id = (int)$_GET['delete'];
    // Удалить фото с диска
    $imgs = db()->fetchAll("SELECT image_path FROM car_images WHERE car_id=?", [$id]);
    foreach ($imgs as $img) {
        $path = UPLOAD_DIR . $img['image_path'];
        if (file_exists($path)) unlink($path);
    }
    db()->delete('cars', 'id=?', [$id]);
    flash('success', 'Автомобиль удалён');
    header('Location: /admin/cars/'); exit;
}

$search = trim($_GET['q'] ?? '');
$status_filter = $_GET['status'] ?? '';
$where = ['1=1']; $params = [];
if ($search) { $where[] = '(brand LIKE ? OR model LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($status_filter) { $where[] = 'status=?'; $params[] = $status_filter; }
$whereStr = implode(' AND ', $where);
$cars = db()->fetchAll("SELECT c.*, (SELECT image_path FROM car_images WHERE car_id=c.id AND is_main=1 LIMIT 1) as main_image FROM cars c WHERE $whereStr ORDER BY c.created_at DESC", $params);
?>

<div class="admin-topbar">
    <h1>Автомобили (<?= count($cars) ?>)</h1>
    <a href="/admin/cars/add.php" class="btn btn-primary btn-sm">➕ Добавить авто</a>
</div>

<!-- Поиск + фильтр статуса -->
<form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Марка, модель..." style="flex:1;min-width:180px;background:var(--surface);border:1px solid var(--border-2);border-radius:8px;color:var(--text);padding:.55rem .9rem;font-size:.9rem">
    <select name="status" onchange="this.form.submit()" style="background:var(--surface);border:1px solid var(--border-2);border-radius:8px;color:var(--text);padding:.55rem .9rem;font-size:.9rem">
        <option value="">Все статусы</option>
        <option value="active" <?= $status_filter==='active'?'selected':'' ?>>Активные</option>
        <option value="sold" <?= $status_filter==='sold'?'selected':'' ?>>Проданные</option>
        <option value="hidden" <?= $status_filter==='hidden'?'selected':'' ?>>Скрытые</option>
    </select>
    <button type="submit" class="btn btn-ghost btn-sm">Найти</button>
    <?php if ($search || $status_filter): ?>
    <a href="/admin/cars/" class="btn btn-ghost btn-sm">✕ Сбросить</a>
    <?php endif; ?>
</form>

<table class="admin-table">
    <thead>
        <tr>
            <th style="width:60px">Фото</th>
            <th>Авто</th>
            <th>Год</th>
            <th>Цена</th>
            <th>Пробег</th>
            <th>Статус</th>
            <th>Хит</th>
            <th>Просм.</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($cars)): ?>
    <tr><td colspan="9" style="text-align:center;padding:3rem;color:var(--text-3)">Автомобили не найдены</td></tr>
    <?php else: foreach ($cars as $c): ?>
    <tr>
        <td>
            <?php if ($c['main_image']): ?>
            <img src="/uploads/cars/<?= htmlspecialchars($c['main_image']) ?>" style="width:56px;height:40px;object-fit:cover;border-radius:6px" loading="lazy">
            <?php else: ?>
            <div style="width:56px;height:40px;background:var(--surface);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--text-3);font-size:1.2rem">🚗</div>
            <?php endif; ?>
        </td>
        <td>
            <strong><?= htmlspecialchars($c['brand'].' '.$c['model']) ?></strong><br>
            <small style="color:var(--text-3);font-size:.78rem">/cars/<?= htmlspecialchars($c['slug']) ?>/</small>
        </td>
        <td><?= $c['year'] ?></td>
        <td style="font-weight:600;white-space:nowrap"><?= number_format($c['price'],0,',',' ') ?> ₽</td>
        <td style="white-space:nowrap"><?= number_format($c['mileage'],0,',',' ') ?> км</td>
        <td>
            <?php
            $badges = ['active'=>['badge-green','Активен'],'sold'=>['badge-red','Продано'],'hidden'=>['badge-gray','Скрыт']];
            [$cls,$lbl] = $badges[$c['status']] ?? ['badge-gray','?'];
            ?>
            <span class="badge <?= $cls ?>"><?= $lbl ?></span>
        </td>
        <td style="text-align:center"><?= $c['is_featured'] ? '⭐' : '—' ?></td>
        <td><?= $c['views'] ?></td>
        <td>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                <a href="/cars/<?= htmlspecialchars($c['slug']) ?>/" target="_blank" class="btn btn-ghost btn-sm" title="Смотреть">👁</a>
                <a href="/admin/cars/edit.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm" title="Редактировать">✏️</a>
                <a href="/admin/cars/?delete=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>" class="btn btn-ghost btn-sm" 
                   style="color:var(--red)" title="Удалить"
                   onclick="return confirm('Удалить <?= htmlspecialchars($c['brand'].' '.$c['model']) ?>? Это действие необратимо.')">🗑</a>
            </div>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../admin/_footer.php'; ?>
