<?php
$meta_title = 'Drive Hub — Автоплощадка в Москве | Проверенные авто с пробегом';
$meta_description = 'Drive Hub — покупайте авто с пробегом выгодно и безопасно. Честные цены, проверенные машины, полная прозрачность. Москва.';
require_once __DIR__ . '/includes/header.php';
$featured_cars = get_featured_cars(6);
$brands = get_brands();
$total_cars = count_cars();
?>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid-lines"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span>🚗 <?= $total_cars ?> авто в наличии</span>
            </div>
            <h1>Купи авто<br><span>без лишних</span><br>хлопот</h1>
            <p class="hero-sub">Drive Hub — молодая и честная автоплощадка в Москве. Мы проверяем каждую машину и называем реальную цену сразу.</p>
            <div class="hero-actions">
                <a href="/catalog/" class="btn btn-primary btn-lg">Смотреть каталог</a>
                <button class="btn btn-outline btn-lg" data-modal data-car-id="">Оставить заявку</button>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <strong data-count="<?= $total_cars ?>" data-suffix="">0</strong>
                    <span>авто в наличии</span>
                </div>
                <div class="hero-stat">
                    <strong data-count="100" data-suffix="%">0</strong>
                    <span>проверенные машины</span>
                </div>
                <div class="hero-stat">
                    <strong data-count="15" data-suffix=" мин">0</strong>
                    <span>перезвоним за</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Декоративный svg-автомобиль -->
    <svg class="hero-car-visual" viewBox="0 0 800 320" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M80 240 L120 140 Q200 80 380 70 Q560 60 680 120 L740 240 Z" fill="rgba(26,86,219,0.6)" />
        <rect x="60" y="240" width="700" height="30" rx="10" fill="rgba(26,86,219,0.4)" />
        <circle cx="200" cy="275" r="40" fill="none" stroke="rgba(59,130,246,0.8)" stroke-width="12"/>
        <circle cx="200" cy="275" r="20" fill="rgba(26,86,219,0.5)"/>
        <circle cx="600" cy="275" r="40" fill="none" stroke="rgba(59,130,246,0.8)" stroke-width="12"/>
        <circle cx="600" cy="275" r="20" fill="rgba(26,86,219,0.5)"/>
        <rect x="200" y="100" width="340" height="110" rx="20" fill="rgba(59,130,246,0.15)" stroke="rgba(59,130,246,0.4)" stroke-width="2"/>
    </svg>
</section>

<!-- ===== ПОЧЕМУ МЫ ===== -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-label">Наши принципы</span>
            <h2>Почему выбирают Drive Hub</h2>
            <p>Мы только начинаем, но уже делаем это честно и с уважением к каждому покупателю</p>
        </div>
        <div class="why-grid">
            <div class="why-card reveal">
                <div class="why-icon">🔍</div>
                <h3>Честная проверка</h3>
                <p>Каждый автомобиль осматривается перед выставлением на продажу. Никаких скрытых аварий и скрученного пробега.</p>
            </div>
            <div class="why-card reveal">
                <div class="why-icon">💰</div>
                <h3>Честная цена</h3>
                <p>Цена на сайте — это финальная цена. Без доплат, комиссий и «а ещё надо оформление».</p>
            </div>
            <div class="why-card reveal">
                <div class="why-icon">📋</div>
                <h3>Прозрачная история</h3>
                <p>Сервисная история, данные о владельцах и информация о ДТП — всё расскажем открыто.</p>
            </div>
            <div class="why-card reveal">
                <div class="why-icon">⚡</div>
                <h3>Быстрое оформление</h3>
                <p>Приедьте, посмотрите, купите. Документы оформим за один день без бюрократической волокиты.</p>
            </div>
            <div class="why-card reveal">
                <div class="why-icon">📞</div>
                <h3>Живой диалог</h3>
                <p>Мы всегда на связи. Ответим на WhatsApp и Telegram, перезвоним в течение 15 минут.</p>
            </div>
            <div class="why-card reveal">
                <div class="why-icon">🤝</div>
                <h3>Торг уместен</h3>
                <p>Готовы обсудить цену при личной встрече. Будем рады найти комфортное решение для обеих сторон.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== АКТУАЛЬНЫЕ АВТО ===== -->
<?php if (!empty($featured_cars)): ?>
<section class="section" style="background:var(--dark-2); padding: 5rem 0">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-label">Свежие поступления</span>
            <h2>Актуальные предложения</h2>
            <p>Горячие предложения — смотрите и звоните, хорошие машины уходят быстро</p>
        </div>
        <div class="cars-grid">
            <?php foreach ($featured_cars as $car): ?>
            <div class="car-card reveal">
                <div class="card-img-wrap">
                    <?php if ($car['status'] === 'sold'): ?>
                    <div class="card-badge sold">Продано</div>
                    <?php elseif ($car['is_featured']): ?>
                    <div class="card-badge">Хит</div>
                    <?php endif; ?>
                    <?php if ($car['main_image']): ?>
                    <img src="/uploads/cars/<?= h($car['main_image']) ?>" alt="<?= h($car['brand'] . ' ' . $car['model']) ?>" loading="lazy">
                    <?php else: ?>
                    <img src="/assets/images/no-car.jpg" alt="Фото недоступно" loading="lazy">
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="card-title"><a href="/cars/<?= h($car['slug']) ?>/"><?= h($car['brand'] . ' ' . $car['model']) ?></a></div>
                    <div class="card-year"><?= h($car['year']) ?> год</div>
                    <div class="card-specs">
                        <?php
                        $body_ru = ['sedan'=>'Седан','hatchback'=>'Хэтчбек','suv'=>'SUV','crossover'=>'Кроссовер','wagon'=>'Универсал','coupe'=>'Купе','minivan'=>'Минивэн','pickup'=>'Пикап','convertible'=>'Кабриолет','other'=>'Другое'];
                        $trans_ru = ['manual'=>'Механика','automatic'=>'Автомат','robot'=>'Робот','variator'=>'Вариатор'];
                        $fuel_ru  = ['petrol'=>'Бензин','diesel'=>'Дизель','hybrid'=>'Гибрид','electric'=>'Электро','gas'=>'Газ'];
                        ?>
                        <span class="spec-tag"><?= $body_ru[$car['body_type']] ?? $car['body_type'] ?></span>
                        <span class="spec-tag"><?= $trans_ru[$car['transmission']] ?? $car['transmission'] ?></span>
                        <span class="spec-tag"><?= $fuel_ru[$car['fuel_type']] ?? $car['fuel_type'] ?></span>
                        <?php if ($car['engine_volume']): ?>
                        <span class="spec-tag"><?= $car['engine_volume'] ?> л</span>
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
        <div style="text-align:center;margin-top:2.5rem">
            <a href="/catalog/" class="btn btn-outline btn-lg">Все автомобили</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== КАК МЫ РАБОТАЕМ ===== -->
<section class="section steps-section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-label">Процесс</span>
            <h2>Как проходит покупка</h2>
        </div>
        <div class="steps-grid">
            <div class="step-item reveal">
                <div class="step-num">1</div>
                <h4>Выберите авто</h4>
                <p>Просматривайте каталог на сайте или позвоните — поможем подобрать подходящий вариант</p>
            </div>
            <div class="step-item reveal">
                <div class="step-num">2</div>
                <h4>Приедьте смотреть</h4>
                <p>Живой осмотр, тест-драйв, честные ответы на все вопросы</p>
            </div>
            <div class="step-item reveal">
                <div class="step-num">3</div>
                <h4>Договоритесь о цене</h4>
                <p>Обсудим условия, возможен лёгкий торг при наличных</p>
            </div>
            <div class="step-item reveal">
                <div class="step-num">4</div>
                <h4>Оформите и заберите</h4>
                <p>Документы за один день — договор купли-продажи и передача ПТС</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="section">
    <div class="container">
        <div class="cta-section reveal">
            <h2>Не нашли нужное авто?</h2>
            <p>Оставьте заявку — подберём машину под ваши параметры и бюджет, свяжемся в течение дня</p>
            <button class="btn btn-primary btn-lg" data-modal data-car-id="" style="margin:0 auto">
                Оставить заявку
            </button>
        </div>
    </div>
</section>

<style>
.reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
.reveal.revealed { opacity: 1; transform: none; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
