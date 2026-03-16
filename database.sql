-- ====================================================
-- DRIVE HUB — SQL СХЕМА БАЗЫ ДАННЫХ
-- Выполните этот файл в phpMyAdmin на рег.ру
-- ====================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- Таблица автомобилей
CREATE TABLE IF NOT EXISTS `cars` (
  `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `slug`            VARCHAR(200)     NOT NULL UNIQUE,
  `brand`           VARCHAR(80)      NOT NULL,
  `model`           VARCHAR(80)      NOT NULL,
  `year`            SMALLINT         NOT NULL,
  `price`           INT UNSIGNED     NOT NULL,
  `mileage`         INT UNSIGNED     NOT NULL DEFAULT 0,
  `body_type`       ENUM('sedan','hatchback','suv','crossover','wagon','coupe','minivan','pickup','convertible','other') NOT NULL DEFAULT 'other',
  `transmission`    ENUM('manual','automatic','robot','variator') NOT NULL DEFAULT 'automatic',
  `fuel_type`       ENUM('petrol','diesel','hybrid','electric','gas') NOT NULL DEFAULT 'petrol',
  `drive`           ENUM('fwd','rwd','awd','4wd') NOT NULL DEFAULT 'fwd',
  `engine_volume`   DECIMAL(3,1)     DEFAULT NULL,
  `engine_power`    SMALLINT         DEFAULT NULL,
  `color`           VARCHAR(60)      DEFAULT NULL,
  `vin`             VARCHAR(20)      DEFAULT NULL,
  `description`     TEXT,
  `features`        TEXT COMMENT 'JSON array of features',
  `status`          ENUM('active','sold','hidden') NOT NULL DEFAULT 'active',
  `is_featured`     TINYINT(1)       NOT NULL DEFAULT 0,
  `views`           INT UNSIGNED     NOT NULL DEFAULT 0,
  `meta_title`      VARCHAR(255)     DEFAULT NULL,
  `meta_description` VARCHAR(500)   DEFAULT NULL,
  `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_brand` (`brand`),
  INDEX `idx_price` (`price`),
  INDEX `idx_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица фотографий
CREATE TABLE IF NOT EXISTS `car_images` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `car_id`      INT UNSIGNED NOT NULL,
  `image_path`  VARCHAR(255) NOT NULL,
  `is_main`     TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order`  TINYINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_car_id` (`car_id`),
  CONSTRAINT `fk_images_car` FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица заявок
CREATE TABLE IF NOT EXISTS `requests` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `car_id`     INT UNSIGNED DEFAULT NULL,
  `name`       VARCHAR(120) NOT NULL,
  `phone`      VARCHAR(30)  NOT NULL,
  `message`    TEXT,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица настроек сайта
CREATE TABLE IF NOT EXISTS `settings` (
  `key`   VARCHAR(80)  NOT NULL,
  `value` TEXT,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Начальные данные настроек
INSERT INTO `settings` (`key`, `value`) VALUES
('phone', '+7 (999) 123-45-67'),
('email', 'info@drivehub-rf.ru'),
('address', 'Москва, ул. Автомобильная, д. 1'),
('working_hours', 'Пн–Вс: 9:00 – 21:00'),
('vk_link', ''),
('telegram_link', ''),
('whatsapp_number', '79991234567'),
('yandex_metrika', ''),
('about_text', 'Drive Hub — молодая и честная автоплощадка в Москве. Мы тщательно проверяем каждый автомобиль перед продажей и гарантируем прозрачность сделки.');

-- Демо-автомобиль (можно удалить)
INSERT INTO `cars` (`slug`,`brand`,`model`,`year`,`price`,`mileage`,`body_type`,`transmission`,`fuel_type`,`drive`,`engine_volume`,`engine_power`,`color`,`description`,`status`,`is_featured`) VALUES
('toyota-camry-2020',     'Toyota',   'Camry',    2020, 2350000, 45000,  'sedan',    'automatic', 'petrol', 'fwd', 2.5, 181, 'Белый перламутр', 'Отличное состояние, один владелец, полный сервисный журнал.', 'active', 1),
('volkswagen-tiguan-2019','Volkswagen','Tiguan',   2019, 1980000, 68000,  'crossover','automatic', 'petrol', 'awd', 2.0, 150, 'Серый металлик',  'Комплектация Highline, панорамная крыша, адаптивный круиз.', 'active', 1),
('kia-k5-2021',           'KIA',       'K5',       2021, 2150000, 22000,  'sedan',    'automatic', 'petrol', 'fwd', 2.0, 150, 'Чёрный',          'Максимальная комплектация Prestige, пробег подтверждён.', 'active', 1),
('hyundai-tucson-2022',   'Hyundai',  'Tucson',   2022, 2680000, 18000,  'crossover','automatic', 'petrol', 'fwd', 2.0, 150, 'Синий',           'Свежий рестайлинг, как новый.', 'active', 0),
('mazda-cx5-2020',        'Mazda',    'CX-5',     2020, 2100000, 55000,  'crossover','automatic', 'petrol', 'awd', 2.5, 194, 'Красный',         'Soul Red Crystal, идеальная комплектация Active.', 'active', 1),
('skoda-octavia-2019',    'Skoda',    'Octavia',  2019, 1450000, 90000,  'sedan',    'robot',     'petrol', 'fwd', 1.4, 150, 'Серебристый',     'Стильная и надёжная, полный аналог VW Golf. DSG в отличном состоянии.', 'active', 0);

SET foreign_key_checks = 1;
