<?php
// === НАСТРОЙКИ САЙТА ===
define('SITE_NAME', 'Drive Hub');
define('SITE_URL', 'https://drivehub-rf.ru');
define('SITE_DESCRIPTION', 'Drive Hub — автоплощадка в Москве. Проверенные автомобили с пробегом по честным ценам. Покупка автомобиля быстро и без лишних хлопот.');
define('SITE_KEYWORDS', 'купить автомобиль, авто с пробегом, автоплощадка, продажа авто, Drive Hub, автомобили Москва');
define('SITE_PHONE', '+7 (915) 119-95-89');
define('SITE_EMAIL', 'info@drivehub-rf.ru');
define('SITE_ADDRESS', 'Москва, ул. Автомобильная, д. 1');
define('SITE_WORKING_HOURS', 'Пн–Вс: 9:00 – 21:00');

// === БАЗА ДАННЫХ (ЗАПОЛНИТЬ ПОСЛЕ СОЗДАНИЯ БД НА РЕГ.РУ) ===
define('DB_HOST', 'localhost');
define('DB_NAME', 'u3451616_default');       // имя вашей БД
define('DB_USER', 'u3451616_default');     // пользователь БД
define('DB_PASS', '8Fe97i7fp1PhLIIx');  // пароль БД
define('DB_CHARSET', 'utf8mb4');

// === ЗАГРУЗКИ ===
define('UPLOAD_DIR', __DIR__ . '/../uploads/cars/');
define('UPLOAD_URL', SITE_URL . '/uploads/cars/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

// === БЕЗОПАСНОСТЬ ===
define('ADMIN_LOGIN', 'admin');           // логин админа
define('ADMIN_PASSWORD_HASH', password_hash('DriveHub2024!', PASSWORD_BCRYPT)); // сменить пароль!
define('SECRET_KEY', 'dh_sk_2024_change_this_key');

// === РЕЖИМ ОТЛАДКИ (поставить false на продакшне) ===
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
