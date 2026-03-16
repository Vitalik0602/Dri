<?php
$page_title = 'Редактировать автомобиль';
$admin_page = 'cars';
require_once __DIR__ . '/../../admin/_header.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flash('error','Неверный ID'); header('Location: /admin/cars/'); exit; }

$car = db()->fetchOne("SELECT * FROM cars WHERE id=?", [$id]);
if (!$car) { flash('error','Автомобиль не найден'); header('Location: /admin/cars/'); exit; }

$car_images = db()->fetchAll("SELECT * FROM car_images WHERE car_id=? ORDER BY is_main DESC, sort_order", [$id]);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $action = $_POST['action'] ?? 'save';

    // Удалить отдельное фото
    if ($action === 'delete_image' && isset($_POST['image_id'])) {
        $img = db()->fetchOne("SELECT * FROM car_images WHERE id=? AND car_id=?", [(int)$_POST['image_id'], $id]);
        if ($img) {
            $path = UPLOAD_DIR . $img['image_path'];
            if (file_exists($path)) unlink($path);
            db()->delete('car_images', 'id=?', [(int)$_POST['image_id']]);
            // Если удалили главное — назначить первое оставшееся
            if ($img['is_main']) {
                $first = db()->fetchOne("SELECT id FROM car_images WHERE car_id=? LIMIT 1", [$id]);
                if ($first) db()->update('car_images', ['is_main'=>1], 'id=?', [$first['id']]);
            }
            flash('success','Фото удалено');
        }
        header("Location: /admin/cars/edit.php?id=$id"); exit;
    }

    // Назначить главное фото
    if ($action === 'set_main' && isset($_POST['image_id'])) {
        db()->query("UPDATE car_images SET is_main=0 WHERE car_id=?", [$id]);
        db()->update('car_images', ['is_main'=>1], 'id=? AND car_id=?', [(int)$_POST['image_id'], $id]);
        flash('success','Главное фото обновлено');
        header("Location: /admin/cars/edit.php?id=$id"); exit;
    }

    // Основное сохранение
    $d = $_POST;
    $required = ['brand','model','year','price','mileage'];
    foreach ($required as $f) { if (empty(trim($d[$f] ?? ''))) $errors[] = "Поле «$f» обязательно"; }

    if (empty($errors)) {
        // Пересчитать slug только если изменилось название или год
        $new_base = make_slug(($d['brand']??'').'-'.($d['model']??'').'-'.($d['year']??''));
        $slug = $car['slug'];
        if ($new_base !== make_slug($car['brand'].'-'.$car['model'].'-'.$car['year'])) {
            $slug = $new_base; $i = 1;
            while ($conflict = db()->fetchOne("SELECT id FROM cars WHERE slug=? AND id!=?", [$slug, $id])) {
                $slug = $new_base . '-' . $i++;
            }
        }

        $features_json = null;
        if (!empty($d['features'])) {
            $feats = array_filter(array_map('trim', explode("\n", $d['features'])));
            $features_json = json_encode(array_values($feats), JSON_UNESCAPED_UNICODE);
        }

        db()->update('cars', [
            'slug'            => $slug,
            'brand'           => trim($d['brand']),
            'model'           => trim($d['model']),
            'year'            => (int)$d['year'],
            'price'           => (int)str_replace([' ','₽',','], '', $d['price']),
            'mileage'         => (int)str_replace([' ','км',','], '', $d['mileage']),
            'body_type'       => $d['body_type'],
            'transmission'    => $d['transmission'],
            'fuel_type'       => $d['fuel_type'],
            'drive'           => $d['drive'],
            'engine_volume'   => $d['engine_volume'] ?: null,
            'engine_power'    => $d['engine_power'] ?: null,
            'color'           => trim($d['color'] ?? ''),
            'vin'             => strtoupper(trim($d['vin'] ?? '')),
            'description'     => trim($d['description'] ?? ''),
            'features'        => $features_json,
            'status'          => $d['status'],
            'is_featured'     => isset($d['is_featured']) ? 1 : 0,
            'meta_title'      => trim($d['meta_title'] ?? ''),
            'meta_description'=> trim($d['meta_description'] ?? ''),
            'updated_at'      => date('Y-m-d H:i:s'),
        ], 'id=?', [$id]);

        // Новые фото
        if (!empty($_FILES['images']['name'][0])) {
            $has_main = (bool)db()->fetchOne("SELECT id FROM car_images WHERE car_id=? AND is_main=1", [$id]);
            $sort = (int)(db()->fetchOne("SELECT MAX(sort_order) as m FROM car_images WHERE car_id=?", [$id])['m'] ?? 0);
            foreach ($_FILES['images']['name'] as $i => $fname) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = ['name'=>$fname,'tmp_name'=>$_FILES['images']['tmp_name'][$i],'error'=>$_FILES['images']['error'][$i],'size'=>$_FILES['images']['size'][$i]];
                $img_name = upload_image($file, 'car'.$id);
                if ($img_name) {
                    db()->insert('car_images', ['car_id'=>$id,'image_path'=>$img_name,'is_main'=>(!$has_main && $i===0) ? 1 : 0,'sort_order'=>++$sort]);
                    $has_main = true;
                }
            }
        }

        flash('success', "Автомобиль обновлён!");
        header("Location: /admin/cars/edit.php?id=$id"); exit;
    }
    $car = array_merge($car, $_POST);
    $car_images = db()->fetchAll("SELECT * FROM car_images WHERE car_id=? ORDER BY is_main DESC, sort_order", [$id]);
}

$body_types = ['sedan'=>'Седан','hatchback'=>'Хэтчбек','suv'=>'SUV','crossover'=>'Кроссовер','wagon'=>'Универсал','coupe'=>'Купе','minivan'=>'Минивэн','pickup'=>'Пикап','convertible'=>'Кабриолет','other'=>'Другое'];
$transmissions = ['manual'=>'Механика','automatic'=>'Автомат','robot'=>'Робот','variator'=>'Вариатор'];
$fuel_types = ['petrol'=>'Бензин','diesel'=>'Дизель','hybrid'=>'Гибрид','electric'=>'Электро','gas'=>'Газ'];
$drives = ['fwd'=>'Передний (FWD)','rwd'=>'Задний (RWD)','awd'=>'Полный (AWD)','4wd'=>'Подкл. полный (4WD)'];
$features_str = $car['features'] ? implode("\n", json_decode($car['features'], true) ?? []) : '';
?>

<div class="admin-topbar">
    <h1>✏️ <?= htmlspecialchars($car['brand'].' '.$car['model'].' '.$car['year']) ?></h1>
    <div style="display:flex;gap:.5rem">
        <a href="/cars/<?= htmlspecialchars($car['slug']) ?>/" target="_blank" class="btn btn-ghost btn-sm">👁 Смотреть</a>
        <a href="/admin/cars/" class="btn btn-ghost btn-sm">← Назад</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error" style="margin-bottom:1.25rem"><?php foreach ($errors as $e) echo '<div>⚠️ '.htmlspecialchars($e).'</div>'; ?></div>
<?php endif; ?>

<!-- Фотографии -->
<div class="admin-form-section" style="margin-bottom:1.5rem">
    <h3>Фотографии</h3>
    <?php if (!empty($car_images)): ?>
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem">
        <?php foreach ($car_images as $img): ?>
        <div style="position:relative;width:120px">
            <img src="/uploads/cars/<?= htmlspecialchars($img['image_path']) ?>" style="width:120px;height:85px;object-fit:cover;border-radius:8px;border:2px solid <?= $img['is_main']?'var(--blue-600)':'var(--border)' ?>">
            <?php if ($img['is_main']): ?><div style="text-align:center;font-size:.7rem;color:var(--blue-400);margin-top:.25rem">Главное</div><?php endif; ?>
            <div style="display:flex;gap:.25rem;margin-top:.35rem">
                <?php if (!$img['is_main']): ?>
                <form method="POST" style="flex:1">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="set_main">
                    <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;font-size:.7rem" title="Сделать главным">⭐</button>
                </form>
                <?php endif; ?>
                <form method="POST" style="flex:1" onsubmit="return confirm('Удалить фото?')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete_image">
                    <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;color:var(--red);font-size:.7rem">🗑</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text-3);margin-bottom:1rem">Фото не добавлены</p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="save">
        <div class="form-group">
            <label>Добавить фото</label>
            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                   style="background:var(--surface);border:1px dashed var(--border-2);border-radius:10px;color:var(--text);padding:1rem;width:100%">
        </div>
    </form>
</div>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
<input type="hidden" name="action" value="save">

<div class="admin-form-section">
    <h3>Основная информация</h3>
    <div class="admin-grid-3">
        <div class="form-group"><label>Марка *</label><input type="text" name="brand" value="<?= htmlspecialchars($car['brand']) ?>" required></div>
        <div class="form-group"><label>Модель *</label><input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required></div>
        <div class="form-group"><label>Год *</label><input type="number" name="year" value="<?= $car['year'] ?>" required min="1990" max="<?= date('Y') ?>"></div>
        <div class="form-group"><label>Цена, ₽ *</label><input type="number" name="price" value="<?= $car['price'] ?>" required min="0"></div>
        <div class="form-group"><label>Пробег, км *</label><input type="number" name="mileage" value="<?= $car['mileage'] ?>" required min="0"></div>
        <div class="form-group"><label>Цвет</label><input type="text" name="color" value="<?= htmlspecialchars($car['color'] ?? '') ?>"></div>
    </div>
</div>

<div class="admin-form-section">
    <h3>Технические характеристики</h3>
    <div class="admin-grid-3">
        <div class="form-group"><label>Тип кузова</label><select name="body_type"><?php foreach ($body_types as $v=>$l): ?><option value="<?= $v ?>" <?= $car['body_type']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Коробка</label><select name="transmission"><?php foreach ($transmissions as $v=>$l): ?><option value="<?= $v ?>" <?= $car['transmission']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Топливо</label><select name="fuel_type"><?php foreach ($fuel_types as $v=>$l): ?><option value="<?= $v ?>" <?= $car['fuel_type']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Привод</label><select name="drive"><?php foreach ($drives as $v=>$l): ?><option value="<?= $v ?>" <?= $car['drive']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>Объём, л</label><input type="number" name="engine_volume" value="<?= $car['engine_volume'] ?>" step="0.1"></div>
        <div class="form-group"><label>Мощность, л.с.</label><input type="number" name="engine_power" value="<?= $car['engine_power'] ?>"></div>
    </div>
    <div class="admin-grid-2"><div class="form-group"><label>VIN</label><input type="text" name="vin" value="<?= htmlspecialchars($car['vin'] ?? '') ?>" style="text-transform:uppercase"></div></div>
</div>

<div class="admin-form-section">
    <h3>Описание и комплектация</h3>
    <div class="form-group"><label>Описание</label><textarea name="description" rows="5"><?= htmlspecialchars($car['description'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Опции (каждая с новой строки)</label><textarea name="features" rows="6"><?= htmlspecialchars($features_str) ?></textarea></div>
</div>

<div class="admin-form-section">
    <h3>Публикация и SEO</h3>
    <div class="admin-grid-3">
        <div class="form-group"><label>Статус</label><select name="status"><option value="active" <?= $car['status']==='active'?'selected':'' ?>>✅ Активен</option><option value="hidden" <?= $car['status']==='hidden'?'selected':'' ?>>🔒 Скрыт</option><option value="sold" <?= $car['status']==='sold'?'selected':'' ?>>🔴 Продано</option></select></div>
        <div class="form-group" style="display:flex;align-items:flex-end"><label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;text-transform:none;font-size:.9rem"><input type="checkbox" name="is_featured" <?= $car['is_featured']?'checked':'' ?> style="width:18px;height:18px"> ⭐ Хит на главной</label></div>
    </div>
    <div class="form-group"><label>SEO-заголовок</label><input type="text" name="meta_title" value="<?= htmlspecialchars($car['meta_title'] ?? '') ?>" maxlength="255"></div>
    <div class="form-group"><label>SEO-описание</label><textarea name="meta_description" rows="2" maxlength="500"><?= htmlspecialchars($car['meta_description'] ?? '') ?></textarea></div>
</div>

<div style="display:flex;gap:1rem;justify-content:flex-end">
    <a href="/admin/cars/" class="btn btn-ghost">Отмена</a>
    <button type="submit" class="btn btn-primary btn-lg">💾 Сохранить изменения</button>
</div>
</form>

<?php require_once __DIR__ . '/../../admin/_footer.php'; ?>
