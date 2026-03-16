<?php
$page_title = 'Настройки сайта';
$admin_page = 'settings';
require_once __DIR__ . '/../admin/_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $fields = ['phone','email','address','working_hours','vk_link','telegram_link','whatsapp_number','yandex_metrika','about_text'];
    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        $exists = db()->fetchOne("SELECT `key` FROM settings WHERE `key`=?", [$key]);
        if ($exists) {
            db()->update('settings', ['value'=>$val], '`key`=?', [$key]);
        } else {
            db()->insert('settings', ['key'=>$key, 'value'=>$val]);
        }
    }

    // Смена пароля
    if (!empty($_POST['new_password'])) {
        $new_hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        // Запишем хеш в файл конфига — в реальном проекте лучше хранить в БД
        flash('info', 'Новый хеш пароля: ' . $new_hash . ' — вставьте его в includes/config.php в строку ADMIN_PASSWORD_HASH');
    }

    flash('success', 'Настройки сохранены!');
    header('Location: /admin/settings.php'); exit;
}

$settings = [];
$rows = db()->fetchAll("SELECT * FROM settings");
foreach ($rows as $r) $settings[$r['key']] = $r['value'];
?>

<div class="admin-topbar">
    <h1>⚙️ Настройки сайта</h1>
</div>

<form method="POST">
<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

<div class="admin-form-section">
    <h3>Контактная информация</h3>
    <div class="admin-grid-2">
        <div class="form-group"><label>Телефон</label><input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" placeholder="+7 (999) 123-45-67"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>"></div>
        <div class="form-group"><label>Адрес</label><input type="text" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"></div>
        <div class="form-group"><label>Режим работы</label><input type="text" name="working_hours" value="<?= htmlspecialchars($settings['working_hours'] ?? '') ?>" placeholder="Пн–Вс: 9:00 – 21:00"></div>
    </div>
</div>

<div class="admin-form-section">
    <h3>Социальные сети и мессенджеры</h3>
    <div class="admin-grid-2">
        <div class="form-group"><label>ВКонтакте (ссылка)</label><input type="url" name="vk_link" value="<?= htmlspecialchars($settings['vk_link'] ?? '') ?>" placeholder="https://vk.com/drivehub_rf"></div>
        <div class="form-group"><label>Telegram (ссылка)</label><input type="url" name="telegram_link" value="<?= htmlspecialchars($settings['telegram_link'] ?? '') ?>" placeholder="https://t.me/drivehub_rf"></div>
        <div class="form-group"><label>WhatsApp (только цифры)</label><input type="text" name="whatsapp_number" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>" placeholder="79991234567"></div>
    </div>
</div>

<div class="admin-form-section">
    <h3>Яндекс.Метрика</h3>
    <div class="form-group">
        <label>ID счётчика (только цифры)</label>
        <input type="text" name="yandex_metrika" value="<?= htmlspecialchars($settings['yandex_metrika'] ?? '') ?>" placeholder="12345678">
        <small style="color:var(--text-3);display:block;margin-top:.4rem">Введите только числовой ID счётчика из Яндекс.Метрики</small>
    </div>
</div>

<div class="admin-form-section">
    <h3>Текст «О нас» на сайте</h3>
    <div class="form-group">
        <textarea name="about_text" rows="5"><?= htmlspecialchars($settings['about_text'] ?? '') ?></textarea>
    </div>
</div>

<div class="admin-form-section">
    <h3>🔐 Смена пароля администратора</h3>
    <p style="color:var(--text-3);font-size:.88rem;margin-bottom:1rem">После сохранения вставьте новый хеш в файл <code>includes/config.php</code></p>
    <div class="admin-grid-2">
        <div class="form-group"><label>Новый пароль</label><input type="password" name="new_password" autocomplete="new-password" placeholder="Оставьте пустым чтобы не менять"></div>
    </div>
</div>

<div style="display:flex;gap:1rem;justify-content:flex-end">
    <button type="submit" class="btn btn-primary btn-lg">💾 Сохранить настройки</button>
</div>
</form>

<?php require_once __DIR__ . '/../admin/_footer.php'; ?>
