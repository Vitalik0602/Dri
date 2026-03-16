<?php
$page_title = 'Добавить автомобиль';
$admin_page = 'car-add';
require_once __DIR__ . '/../../admin/_header.php';

$errors = [];
$data = [
    'brand'=>'','model'=>'','year'=>date('Y'),'price'=>'','mileage'=>'',
    'body_type'=>'sedan','transmission'=>'automatic','fuel_type'=>'petrol','drive'=>'fwd',
    'engine_volume'=>'','engine_power'=>'','color'=>'','vin'=>'',
    'description'=>'','features'=>'','status'=>'active','is_featured'=>0,
    'meta_title'=>'','meta_description'=>'',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // Валидация
    $d = $_POST;
    $required = ['brand','model','year','price','mileage'];
    foreach ($required as $f) { if (empty(trim($d[$f] ?? ''))) $errors[] = "Поле «$f» обязательно"; }

    if (empty($errors)) {
        $slug_base = make_slug(($d['brand'] ?? '') . '-' . ($d['model'] ?? '') . '-' . ($d['year'] ?? ''));
        $slug = $slug_base;
        $i = 1;
        while (db()->fetchOne("SELECT id FROM cars WHERE slug=?", [$slug])) { $slug = $slug_base . '-' . $i++; }

        $features_json = null;
        if (!empty($d['features'])) {
            $feats = array_filter(array_map('trim', explode("\n", $d['features'])));
            $features_json = json_encode(array_values($feats), JSON_UNESCAPED_UNICODE);
        }

        $car_id = db()->insert('cars', [
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
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // Загрузка фото
        if (!empty($_FILES['images']['name'][0])) {
            $is_first = true;
            foreach ($_FILES['images']['name'] as $i => $fname) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $file = [
                    'name'     => $fname,
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error'    => $_FILES['images']['error'][$i],
                    'size'     => $_FILES['images']['size'][$i],
                ];
                $img_name = upload_image($file, 'car' . $car_id);
                if ($img_name) {
                    db()->insert('car_images', [
                        'car_id'     => $car_id,
                        'image_path' => $img_name,
                        'is_main'    => $is_first ? 1 : 0,
                        'sort_order' => $i,
                    ]);
                    $is_first = false;
                }
            }
        }

        flash('success', "Автомобиль «{$d['brand']} {$d['model']}» успешно добавлен!");
        header('Location: /admin/cars/'); exit;
    }
    $data = array_merge($data, $_POST);
}

$body_types = ['sedan'=>'Седан','hatchback'=>'Хэтчбек','suv'=>'SUV','crossover'=>'Кроссовер','wagon'=>'Универсал','coupe'=>'Купе','minivan'=>'Минивэн','pickup'=>'Пикап','convertible'=>'Кабриолет','other'=>'Другое'];
$transmissions = ['manual'=>'Механика','automatic'=>'Автомат','robot'=>'Робот','variator'=>'Вариатор'];
$fuel_types = ['petrol'=>'Бензин','diesel'=>'Дизель','hybrid'=>'Гибрид','electric'=>'Электро','gas'=>'Газ'];
$drives = ['fwd'=>'Передний (FWD)','rwd'=>'Задний (RWD)','awd'=>'Полный (AWD)','4wd'=>'Подкл. полный (4WD)'];
?>

<div class="admin-topbar">
    <h1>Добавить автомобиль</h1>
    <a href="/admin/cars/" class="btn btn-ghost btn-sm">← Назад</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error" style="margin-bottom:1.25rem">
    <?php foreach ($errors as $e): ?><div>⚠️ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

<!-- Основное -->
<div class="admin-form-section">
    <h3>Основная информация</h3>
    <div class="admin-grid-3">
        <div class="form-group">
            <label>Марка *</label>
            <input type="text" name="brand" value="<?= htmlspecialchars($data['brand']) ?>" required placeholder="Toyota">
        </div>
        <div class="form-group">
            <label>Модель *</label>
            <input type="text" name="model" value="<?= htmlspecialchars($data['model']) ?>" required placeholder="Camry">
        </div>
        <div class="form-group">
            <label>Год выпуска *</label>
            <input type="number" name="year" value="<?= htmlspecialchars($data['year']) ?>" required min="1990" max="<?= date('Y') ?>">
        </div>
    </div>
    <div class="admin-grid-3">
        <div class="form-group">
            <label>Цена, ₽ *</label>
            <input type="number" name="price" value="<?= htmlspecialchars($data['price']) ?>" required min="0" placeholder="1500000">
        </div>
        <div class="form-group">
            <label>Пробег, км *</label>
            <input type="number" name="mileage" value="<?= htmlspecialchars($data['mileage']) ?>" required min="0" placeholder="75000">
        </div>
        <div class="form-group">
            <label>Цвет</label>
            <input type="text" name="color" value="<?= htmlspecialchars($data['color']) ?>" placeholder="Белый перламутр">
        </div>
    </div>
</div>

<!-- Технические -->
<div class="admin-form-section">
    <h3>Технические характеристики</h3>
    <div class="admin-grid-3">
        <div class="form-group">
            <label>Тип кузова</label>
            <select name="body_type">
                <?php foreach ($body_types as $v => $l): ?><option value="<?= $v ?>" <?= $data['body_type']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Коробка передач</label>
            <select name="transmission">
                <?php foreach ($transmissions as $v => $l): ?><option value="<?= $v ?>" <?= $data['transmission']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Тип топлива</label>
            <select name="fuel_type">
                <?php foreach ($fuel_types as $v => $l): ?><option value="<?= $v ?>" <?= $data['fuel_type']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Привод</label>
            <select name="drive">
                <?php foreach ($drives as $v => $l): ?><option value="<?= $v ?>" <?= $data['drive']===$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Объём двигателя, л</label>
            <input type="number" name="engine_volume" value="<?= htmlspecialchars($data['engine_volume']) ?>" step="0.1" min="0.5" max="10" placeholder="2.0">
        </div>
        <div class="form-group">
            <label>Мощность, л.с.</label>
            <input type="number" name="engine_power" value="<?= htmlspecialchars($data['engine_power']) ?>" min="30" max="1500" placeholder="150">
        </div>
    </div>
    <div class="admin-grid-2">
        <div class="form-group">
            <label>VIN номер</label>
            <input type="text" name="vin" value="<?= htmlspecialchars($data['vin']) ?>" maxlength="17" placeholder="XTA123456Y7890123" style="text-transform:uppercase">
        </div>
    </div>
</div>

<!-- Описание и опции -->
<div class="admin-form-section">
    <h3>Описание и комплектация</h3>
    <div class="form-group">
        <label>Описание</label>
        <textarea name="description" rows="5" placeholder="Подробное описание автомобиля, история, особенности..."><?= htmlspecialchars($data['description']) ?></textarea>
    </div>
    <div class="form-group">
        <label>Опции комплектации <small style="font-weight:400;text-transform:none;color:var(--text-3)">(каждая опция с новой строки)</small></label>
        <textarea name="features" rows="6" placeholder="Кожаный салон&#10;Панорамная крыша&#10;Адаптивный круиз&#10;Подогрев сидений&#10;Камера 360°"><?= htmlspecialchars($data['features']) ?></textarea>
    </div>
</div>

<!-- Фотографии -->
<div class="admin-form-section">
    <h3>Фотографии</h3>
    <p style="color:var(--text-3);font-size:.88rem;margin-bottom:1rem">Первое фото станет главным. Форматы: JPG, PNG, WebP. Макс. размер: 5 МБ на файл.</p>
    <div class="form-group">
        <label>Загрузить фото (можно несколько)</label>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" id="photo-input"
               style="background:var(--surface);border:1px dashed var(--border-2);border-radius:10px;color:var(--text);padding:1rem;width:100%">
    </div>
    <div id="photo-preview" style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.5rem"></div>
</div>

<!-- Публикация -->
<div class="admin-form-section">
    <h3>Публикация и SEO</h3>
    <div class="admin-grid-3">
        <div class="form-group">
            <label>Статус</label>
            <select name="status">
                <option value="active" <?= $data['status']==='active'?'selected':'' ?>>✅ Активен (виден на сайте)</option>
                <option value="hidden" <?= $data['status']==='hidden'?'selected':'' ?>>🔒 Скрыт</option>
                <option value="sold" <?= $data['status']==='sold'?'selected':'' ?>>🔴 Продано</option>
            </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end">
            <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;text-transform:none;font-size:.9rem">
                <input type="checkbox" name="is_featured" <?= $data['is_featured']?'checked':'' ?> style="width:18px;height:18px">
                ⭐ Показывать на главной (хит)
            </label>
        </div>
    </div>
    <div class="form-group">
        <label>SEO-заголовок <small style="font-weight:400;text-transform:none;color:var(--text-3)">(оставьте пустым — сгенерируется автоматически)</small></label>
        <input type="text" name="meta_title" value="<?= htmlspecialchars($data['meta_title']) ?>" maxlength="255" placeholder="Купить Toyota Camry 2020 — Drive Hub | 45 000 км, 2 350 000 ₽">
    </div>
    <div class="form-group">
        <label>SEO-описание</label>
        <textarea name="meta_description" rows="2" maxlength="500" placeholder="Продажа Toyota Camry 2020 в Москве. Пробег 45 000 км. Один владелец..."><?= htmlspecialchars($data['meta_description']) ?></textarea>
    </div>
</div>

<div style="display:flex;gap:1rem;justify-content:flex-end">
    <a href="/admin/cars/" class="btn btn-ghost">Отмена</a>
    <button type="submit" class="btn btn-primary btn-lg">💾 Сохранить автомобиль</button>
</div>
</form>

<script>
document.getElementById('photo-input')?.addEventListener('change', function() {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    [...this.files].forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;width:100px;height:70px;border-radius:8px;overflow:hidden';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover';
            if (i === 0) {
                const badge = document.createElement('div');
                badge.textContent = 'Главное';
                badge.style.cssText = 'position:absolute;bottom:0;left:0;right:0;background:rgba(26,86,219,.8);color:#fff;font-size:.65rem;text-align:center;padding:2px';
                wrap.appendChild(badge);
            }
            wrap.appendChild(img);
            preview.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
</script>

<?php require_once __DIR__ . '/../../admin/_footer.php'; ?>
