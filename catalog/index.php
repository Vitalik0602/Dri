<?php
require_once __DIR__ . '/../includes/functions.php';

// Фильтры
$filters = [
    'brand'        => $_GET['brand'] ?? '',
    'body_type'    => $_GET['body_type'] ?? '',
    'transmission' => $_GET['transmission'] ?? '',
    'price_min'    => $_GET['price_min'] ?? '',
    'price_max'    => $_GET['price_max'] ?? '',
    'year_from'    => $_GET['year_from'] ?? '',
    'year_to'      => $_GET['year_to'] ?? '',
    'sort'         => $_GET['sort'] ?? '',
];

$per_page = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$total = count_cars($filters);
$cars  = get_cars($filters, $per_page, $offset);
$brands = get_brands();
$total_pages = (int)ceil($total / $per_page);

$meta_title = 'Каталог автомобилей — Drive Hub | Купить авто с пробегом в Москве';
$meta_description = 'Каталог проверенных авто с пробегом в Drive Hub. Фильтрация по марке, цене, году выпуска. Честные цены, быстрое оформление. Москва.';
$canonical = 'https://drivehub-rf.ru/catalog/';

require_once __DIR__ . '/../includes/header.php';

$body_types = ['sedan'=>'Седан','hatchback'=>'Хэтчбек','suv'=>'SUV','crossover'=>'Кроссовер','wagon'=>'Универсал','coupe'=>'Купе','minivan'=>'Минивэн','pickup'=>'Пикап','convertible'=>'Кабриолет'];
$transmissions = ['automatic'=>'Автомат','manual'=>'Механика','robot'=>'Робот','variator'=>'Вариатор'];
$body_ru = $body_types;
$trans_ru = $transmissions;
$fuel_ru = ['petrol'=>'Бензин','diesel'=>'Дизель','hybrid'=>'Гибрид','electric'=>'Электро','gas'=>'Газ'];
$sorts = [''=>'По умолчанию','price_asc'=>'Сначала дешевле','price_desc'=>'Сначала дороже','year_desc'=>'Сначала новее','mileage'=>'По пробегу'];

// Формируем base URL для пагинации
$query_params = array_filter($filters, fn($v) => $v !== '');
$base_url = '/catalog/?' . http_build_query($query_params);
?>

<div style="padding-top:72px">

<!-- BREADCRUMB -->
<div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Главная</a>
        <span>/</span>
        <span>Каталог</span>
        <?php if ($filters['brand']): ?><span>/</span><span><?= h($filters['brand']) ?></span><?php endif; ?>
    </nav>
</div>

<!-- TITLE + FILTER -->
<section style="padding:1rem 0 3rem">
<div class="container">
    <h1 style="margin-bottom:1.5rem">Каталог автомобилей
        <span style="font-size:1rem;font-weight:400;color:var(--text-3);margin-left:.75rem"><?= $total ?> авто</span>
    </h1>

    <form method="GET" action="/catalog/" class="filter-bar">
        <div class="filter-grid">
            <div class="filter-group">
                <label>Марка</label>
                <select name="brand">
                    <option value="">Все марки</option>
                    <?php foreach ($brands as $b): ?>
                    <option value="<?= h($b['brand']) ?>" <?= $filters['brand'] === $b['brand'] ? 'selected' : '' ?>>
                        <?= h($b['brand']) ?> (<?= $b['cnt'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Тип кузова</label>
                <select name="body_type">
                    <option value="">Все</option>
                    <?php foreach ($body_types as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filters['body_type'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Коробка</label>
                <select name="transmission">
                    <option value="">Все</option>
                    <?php foreach ($transmissions as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filters['transmission'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Цена от, ₽</label>
                <input type="number" name="price_min" value="<?= h($filters['price_min']) ?>" placeholder="500 000" min="0" step="50000">
            </div>
            <div class="filter-group">
                <label>Цена до, ₽</label>
                <input type="number" name="price_max" value="<?= h($filters['price_max']) ?>" placeholder="5 000 000" min="0" step="50000">
            </div>
            <div class="filter-group">
                <label>Год от</label>
                <input type="number" name="year_from" value="<?= h($filters['year_from']) ?>" placeholder="2015" min="2000" max="2025">
            </div>
            <div class="filter-group">
                <label>Год до</label>
                <input type="number" name="year_to" value="<?= h($filters['year_to']) ?>" placeholder="2024" min="2000" max="2025">
            </div>
            <div class="filter-group">
                <label>Сортировка</label>
                <select name="sort">
                    <?php foreach ($sorts as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filters['sort'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="filter-actions" style="margin-top:1rem">
            <button type="submit" class="btn btn-primary">Применить</button>
            <a href="/catalog/" class="btn btn-ghost">Сбросить</a>
        </div>
    </form>

    <!-- CARS GRID -->
    <?php if (empty($cars)): ?>
    <div style="text-align:center;padding:4rem 0;color:var(--text-2)">
        <div style="font-size:3rem;margin-bottom:1rem">🚗</div>
        <h3>Автомобили не найдены</h3>
        <p style="margin-top:.5rem">Попробуйте изменить параметры фильтра</p>
        <a href="/catalog/" class="btn btn-outline" style="margin-top:1.5rem">Сбросить фильтры</a>
    </div>
    <?php else: ?>
    <div class="cars-grid">
        <?php foreach ($cars as $car): ?>
        <div class="car-card">
            <div class="card-img-wrap">
                <?php if ($car['status'] === 'sold'): ?>
                <div class="card-badge sold">Продано</div>
                <?php elseif ($car['is_featured']): ?>
                <div class="card-badge">Хит</div>
                <?php endif; ?>
                <?php if ($car['main_image']): ?>
                <img src="/uploads/cars/<?= h($car['main_image']) ?>" alt="<?= h($car['brand'] . ' ' . $car['model'] . ' ' . $car['year']) ?>" loading="lazy">
                <?php else: ?>
                <img src="/assets/images/no-car.jpg" alt="Фото недоступно" loading="lazy">
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-title"><a href="/cars/<?= h($car['slug']) ?>/"><?= h($car['brand'] . ' ' . $car['model']) ?></a></div>
                <div class="card-year"><?= h($car['year']) ?> год</div>
                <div class="card-specs">
                    <span class="spec-tag"><?= $body_ru[$car['body_type']] ?? $car['body_type'] ?></span>
                    <span class="spec-tag"><?= $trans_ru[$car['transmission']] ?? $car['transmission'] ?></span>
                    <span class="spec-tag"><?= $fuel_ru[$car['fuel_type']] ?? $car['fuel_type'] ?></span>
                    <?php if ($car['engine_volume']): ?>
                    <span class="spec-tag"><?= $car['engine_volume'] ?> л / <?= $car['engine_power'] ?> л.с.</span>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div>
                        <div class="card-mileage"><?= format_mileage($car['mileage']) ?></div>
                        <div class="card-price"><?= format_price($car['price']) ?></div>
                    </div>
                    <a href="/cars/<?= h($car['slug']) ?>/" class="btn btn-primary btn-sm">Подробнее</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($total_pages > 1): ?>
    <nav class="pagination" aria-label="Страницы каталога">
        <?php if ($page > 1): ?>
        <a href="<?= $base_url ?>&page=<?= $page-1 ?>" class="page-link">‹</a>
        <?php endif; ?>
        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);
        if ($start > 1) { echo '<a href="' . $base_url . '&page=1" class="page-link">1</a>'; if ($start > 2) echo '<span class="page-link dots">…</span>'; }
        for ($i = $start; $i <= $end; $i++):
        ?>
        <a href="<?= $base_url ?>&page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor;
        if ($end < $total_pages) { if ($end < $total_pages - 1) echo '<span class="page-link dots">…</span>'; echo '<a href="' . $base_url . '&page=' . $total_pages . '" class="page-link">' . $total_pages . '</a>'; }
        ?>
        <?php if ($page < $total_pages): ?>
        <a href="<?= $base_url ?>&page=<?= $page+1 ?>" class="page-link">›</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
