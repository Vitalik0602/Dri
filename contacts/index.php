<?php
$meta_title = 'Контакты — Drive Hub | Адрес, телефон, режим работы';
$meta_description = 'Контакты автоплощадки Drive Hub в Москве. Телефон, адрес, email, режим работы. Приезжайте на осмотр или звоните прямо сейчас!';
require_once __DIR__ . '/../includes/functions.php';

// Обработка формы
$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    csrf_check();
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $phone) {
        save_request(['name'=>$name,'phone'=>$phone,'message'=>$message]);
        $flash = ['type'=>'success','msg'=>'Спасибо! Мы перезвоним вам в ближайшее время.'];
    } else {
        $flash = ['type'=>'error','msg'=>'Пожалуйста, заполните имя и телефон.'];
    }
}
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/header.php';
?>

<div style="padding-top:72px">
<section class="section">
<div class="container">
    <nav class="breadcrumb"><a href="/">Главная</a><span>/</span><span>Контакты</span></nav>

    <h1 style="margin:1.5rem 0 2.5rem">Контакты</h1>

    <div class="contacts-grid">
        <!-- Контактная информация + форма -->
        <div>
            <div class="contact-card" style="margin-bottom:1.5rem">
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    </div>
                    <div>
                        <strong>Телефон</strong>
                        <p><a href="tel:<?= preg_replace('/[^+\d]/', '', SITE_PHONE) ?>"><?= h(SITE_PHONE) ?></a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <div>
                        <strong>Email</strong>
                        <p><a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    </div>
                    <div>
                        <strong>Адрес</strong>
                        <p><?= h(SITE_ADDRESS) ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                    </div>
                    <div>
                        <strong>Режим работы</strong>
                        <p><?= h(SITE_WORKING_HOURS) ?></p>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="contact-card">
                <h3 style="margin-bottom:1.25rem">Написать нам</h3>
                <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= h($flash['msg']) ?></div>
                <?php endif; ?>
                <form method="POST" action="/contacts/">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="form-group">
                        <label>Имя *</label>
                        <input type="text" name="name" required placeholder="Иван Иванов">
                    </div>
                    <div class="form-group">
                        <label>Телефон *</label>
                        <input type="tel" name="phone" required placeholder="+7 (___) ___-__-__">
                    </div>
                    <div class="form-group">
                        <label>Сообщение</label>
                        <textarea name="message" rows="4" placeholder="Ваш вопрос..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Отправить сообщение</button>
                    <p class="form-privacy" style="margin-top:.75rem">Нажимая кнопку, вы соглашаетесь с <a href="/privacy/">политикой конфиденциальности</a></p>
                </form>
            </div>
        </div>

        <!-- Карта -->
        <div>
            <div class="ymap">
                <!-- Замените координаты на реальные -->
                <iframe 
                    src="https://yandex.ru/map-widget/v1/?ll=37.617698,55.755864&z=14&pt=37.617698,55.755864,pm2blm&text=Drive+Hub"
                    allowfullscreen
                    title="Drive Hub на карте"
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
