<?php
require_once __DIR__ . '/../includes/functions.php';

// Определяем slug из URL
$uri = trim($_SERVER['REQUEST_URI'], '/');
$parts = explode('/', $uri);
// URL: /cars/toyota-camry-2020/ → parts: ['cars', 'toyota-camry-2020', '']
$slug = '';
foreach ($parts as $i => $p) {
    if ($p === 'cars' && isset($parts[$i+1])) { $slug = $parts[$i+1]; break; }
}
if (empty($slug)) { http_response_code(404); require __DIR__ . '/../404.php'; exit; }

$car = get_car_by_slug($slug);
if (!$car) { http_response_code(404); require __DIR__ . '/../404.php'; exit; }

// Счётчик просмотров
db()->query("UPDATE cars SET views = views + 1 WHERE id = ?", [$car['id']]);

$images = get_car_images($car['id']);
$main_image = !empty($images) ? $images[0]['image_path'] : null;

$body_ru  = ['sedan'=>'Седан','hatchback'=>'Хэтчбек','suv'=>'SUV','crossover'=>'Кроссовер','wagon'=>'Универсал','coupe'=>'Купе','minivan'=>'Минивэн','pickup'=>'Пикап','convertible'=>'Кабриолет','other'=>'Другое'];
$trans_ru = ['manual'=>'Механика','automatic'=>'Автомат','robot'=>'Робот','variator'=>'Вариатор'];
$fuel_ru  = ['petrol'=>'Бензин','diesel'=>'Дизель','hybrid'=>'Гибрид','electric'=>'Электро','gas'=>'Газ'];
$drive_ru = ['fwd'=>'Передний','rwd'=>'Задний','awd'=>'Полный','4wd'=>'Подключаемый полный'];

$car_name = $car['brand'] . ' ' . $car['model'] . ' ' . $car['year'];
$meta_title = ($car['meta_title'] ?: "Купить {$car_name} — Drive Hub | {$car['mileage']} км, " . format_price($car['price']));
$meta_description = $car['meta_description'] ?: "Продажа {$car_name} в Москве. Пробег: " . format_mileage($car['mileage']) . ". Цена: " . format_price($car['price']) . ". " . ($car['description'] ? mb_substr(strip_tags($car['description']), 0, 120) . '...' : 'Drive Hub — честная автоплощадка.');
$canonical = 'https://drivehub-rf.ru/cars/' . $car['slug'] . '/';
$og_image  = $main_image ? 'https://drivehub-rf.ru/uploads/cars/' . $main_image : 'https://drivehub-rf.ru/assets/images/og-image.jpg';

// Schema.org Car
$schema_car = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Car',
    'name' => $car_name,
    'brand' => ['@type'=>'Brand','name'=>$car['brand']],
    'model' => $car['model'],
    'modelDate' => (string)$car['year'],
    'mileageFromOdometer' => ['@type'=>'QuantitativeValue','value'=>$car['mileage'],'unitCode'=>'KMT'],
    'vehicleTransmission' => $trans_ru[$car['transmission']] ?? $car['transmission'],
    'fuelType' => $fuel_ru[$car['fuel_type']] ?? $car['fuel_type'],
    'color' => $car['color'],
    'offers' => [
        '@type' => 'Offer',
        'price' => $car['price'],
        'priceCurrency' => 'RUB',
        'availability' => $car['status']==='active' ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
        'url' => $canonical,
        'seller' => ['@type'=>'AutoDealer','name'=>'Drive Hub','url'=>'https://drivehub-rf.ru'],
    ],
    'image' => $og_image,
    'url' => $canonical,
]);

// Похожие авто
$similar = db()->fetchAll(
    "SELECT c.*, (SELECT image_path FROM car_images WHERE car_id = c.id AND is_main = 1 LIMIT 1) as main_image
     FROM cars c WHERE c.status = 'active' AND c.id != ? AND (c.brand = ? OR c.body_type = ?)
     ORDER BY RAND() LIMIT 3",
    [$car['id'], $car['brand'], $car['body_type']]
);

$features = $car['features'] ? json_decode($car['features'], true) : [];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="car-page">
    <div class="container">
        <!-- BREADCRUMB -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Главная</a><span>/</span>
            <a href="/catalog/">Каталог</a><span>/</span>
            <a href="/catalog/?brand=<?= urlencode($car['brand']) ?>"><?= h($car['brand']) ?></a><span>/</span>
            <span><?= h($car_name) ?></span>
        </nav>

        <!-- MAIN LAYOUT -->
        <div class="car-layout" style="margin-bottom:3rem">
            <!-- LEFT: GALLERY + DESCRIPTION -->
            <div>
                <!-- Gallery -->
                <div class="car-gallery" style="margin-bottom:2rem">
                    <div class="car-main-img">
                        <?php if ($main_image): ?>
                        <img id="car-main-img" src="/uploads/cars/<?= h($main_image) ?>" alt="<?= h($car_name) ?>">
                        <?php else: ?>
                        <img id="car-main-img" src="/assets/images/no-car.jpg" alt="Фото недоступно">
                        <?php endif; ?>
                    </div>
                    <?php if (count($images) > 1): ?>
                    <div class="car-thumbs">
                        <?php foreach ($images as $i => $img): ?>
                        <div class="car-thumb <?= $i===0?'active':'' ?>" data-full="/uploads/cars/<?= h($img['image_path']) ?>">
                            <img src="/uploads/cars/<?= h($img['image_path']) ?>" alt="Фото <?= $i+1 ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Описание -->
                <?php if ($car['description']): ?>
                <div style="background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;margin-bottom:2rem">
                    <h2 style="font-size:1.3rem;margin-bottom:1rem">Описание</h2>
                    <div class="car-desc"><?= nl2br(h($car['description'])) ?></div>
                </div>
                <?php endif; ?>

                <!-- Характеристики (полная таблица) -->
                <div style="background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem">
                    <h2 style="font-size:1.3rem;margin-bottom:1.25rem">Характеристики</h2>
                    <table class="car-specs-table">
                        <tr><td>Марка</td><td><?= h($car['brand']) ?></td></tr>
                        <tr><td>Модель</td><td><?= h($car['model']) ?></td></tr>
                        <tr><td>Год выпуска</td><td><?= h($car['year']) ?></td></tr>
                        <tr><td>Пробег</td><td><?= format_mileage($car['mileage']) ?></td></tr>
                        <tr><td>Кузов</td><td><?= $body_ru[$car['body_type']] ?? h($car['body_type']) ?></td></tr>
                        <tr><td>Трансмиссия</td><td><?= $trans_ru[$car['transmission']] ?? h($car['transmission']) ?></td></tr>
                        <tr><td>Топливо</td><td><?= $fuel_ru[$car['fuel_type']] ?? h($car['fuel_type']) ?></td></tr>
                        <tr><td>Привод</td><td><?= $drive_ru[$car['drive']] ?? h($car['drive']) ?></td></tr>
                        <?php if ($car['engine_volume']): ?>
                        <tr><td>Объём двигателя</td><td><?= $car['engine_volume'] ?> л</td></tr>
                        <?php endif; ?>
                        <?php if ($car['engine_power']): ?>
                        <tr><td>Мощность</td><td><?= $car['engine_power'] ?> л.с.</td></tr>
                        <?php endif; ?>
                        <?php if ($car['color']): ?>
                        <tr><td>Цвет</td><td><?= h($car['color']) ?></td></tr>
                        <?php endif; ?>
                        <?php if ($car['vin']): ?>
                        <tr><td>VIN</td><td><code style="font-size:.85rem"><?= h($car['vin']) ?></code></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- RIGHT: PRICE PANEL -->
            <div>
                <div class="car-info-panel">
                    <div class="car-title-block">
                        <?php if ($car['is_featured']): ?>
                        <div style="display:inline-block;background:var(--blue-600);color:#fff;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:6px;margin-bottom:.75rem;letter-spacing:.06em;text-transform:uppercase">⚡ Хит продаж</div>
                        <?php endif; ?>
                        <?php if ($car['status'] === 'sold'): ?>
                        <div style="display:inline-block;background:var(--red);color:#fff;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:6px;margin-bottom:.75rem;text-transform:uppercase">Продано</div>
                        <?php endif; ?>
                        <h1 style="font-size:1.6rem;margin-bottom:.25rem"><?= h($car['brand'] . ' ' . $car['model']) ?></h1>
                        <p style="color:var(--text-3)"><?= h($car['year']) ?> год · <?= format_mileage($car['mileage']) ?></p>
                    </div>

                    <div class="car-price-big"><?= format_price($car['price']) ?></div>
                    <p style="color:var(--text-3);font-size:.82rem;margin-bottom:1.5rem">Цена окончательная, без скрытых доплат</p>

                    <?php if ($car['status'] === 'active'): ?>
                    <button class="btn btn-primary btn-full btn-lg" data-modal data-car-id="<?= $car['id'] ?>" style="margin-bottom:.75rem">
                        Оставить заявку
                    </button>
                    <a href="tel:<?= preg_replace('/[^+\d]/', '', SITE_PHONE) ?>" class="btn btn-outline btn-full" style="margin-bottom:1.5rem">
                        <?= h(SITE_PHONE) ?>
                    </a>
                    <a href="https://wa.me/<?= preg_replace('/[^+\d]/', '', SITE_PHONE) ?>?text=Здравствуйте! Интересует <?= urlencode($car_name) ?>" 
                       target="_blank" rel="noopener" class="btn btn-green btn-full" style="margin-bottom:1.5rem">
                        💬 Написать в WhatsApp
                    </a>
                    <?php else: ?>
                    <div class="alert alert-error">Этот автомобиль уже продан</div>
                    <a href="/catalog/" class="btn btn-primary btn-full">Смотреть другие авто</a>
                    <?php endif; ?>

                    <!-- Краткие спецификации -->
                    <table class="car-specs-table" style="margin-top:1rem">
                        <tr><td>Кузов</td><td><?= $body_ru[$car['body_type']] ?? h($car['body_type']) ?></td></tr>
                        <tr><td>Коробка</td><td><?= $trans_ru[$car['transmission']] ?? h($car['transmission']) ?></td></tr>
                        <tr><td>Двигатель</td><td><?= $car['engine_volume'] ? $car['engine_volume'] . ' л, ' . $car['engine_power'] . ' л.с.' : '—' ?></td></tr>
                        <tr><td>Привод</td><td><?= $drive_ru[$car['drive']] ?? h($car['drive']) ?></td></tr>
                    </table>

                    <!-- Опции -->
                    <?php if (!empty($features)): ?>
                    <div style="margin-top:1.25rem">
                        <p style="font-size:.82rem;font-weight:600;text-transform:uppercase;color:var(--text-3);letter-spacing:.06em;margin-bottom:.75rem">Комплектация</p>
                        <div class="car-features-list">
                            <?php foreach ($features as $f): ?>
                            <span class="feature-tag">✓ <?= h($f) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <p style="color:var(--text-3);font-size:.78rem;text-align:center;margin-top:1rem">
                        👁 Просмотров: <?= (int)$car['views'] + 1 ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- ПОХОЖИЕ АВТО -->
        <?php if (!empty($similar)): ?>
        <div style="margin-bottom:3rem">
            <h2 style="margin-bottom:1.5rem">Похожие автомобили</h2>
            <div class="cars-grid" style="grid-template-columns:repeat(3,1fr)">
                <?php foreach ($similar as $s): ?>
                <div class="car-card">
                    <div class="card-img-wrap">
                        <img src="<?= $s['main_image'] ? '/uploads/cars/'.h($s['main_image']) : '/assets/images/no-car.jpg' ?>" 
                             alt="<?= h($s['brand'].' '.$s['model']) ?>" loading="lazy">
                    </div>
                    <div class="card-body">
                        <div class="card-title"><a href="/cars/<?= h($s['slug']) ?>/"><?= h($s['brand'].' '.$s['model']) ?></a></div>
                        <div class="card-year"><?= $s['year'] ?> год</div>
                        <div class="card-footer">
                            <div>
                                <div class="card-mileage"><?= format_mileage($s['mileage']) ?></div>
                                <div class="card-price"><?= format_price($s['price']) ?></div>
                            </div>
                            <a href="/cars/<?= h($s['slug']) ?>/" class="btn btn-primary btn-sm">Смотреть</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
