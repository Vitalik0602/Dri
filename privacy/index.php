<?php
$meta_title = 'Политика конфиденциальности — Drive Hub';
require_once __DIR__ . '/includes/header.php';
?>
<div style="padding-top:72px">
<section class="section"><div class="container" style="max-width:800px">
    <nav class="breadcrumb"><a href="/">Главная</a><span>/</span><span>Политика конфиденциальности</span></nav>
    <h1 style="margin:1.5rem 0 2rem">Политика конфиденциальности</h1>
    <div class="car-desc" style="line-height:1.9">
        <p>Настоящая Политика конфиденциальности описывает, как Drive Hub (далее — «Сайт», расположен по адресу drivehub-rf.ru) собирает, использует и защищает персональные данные пользователей.</p>
        <h3 style="margin:1.5rem 0 .75rem">1. Какие данные мы собираем</h3>
        <p>При заполнении форм на Сайте мы собираем: имя, номер телефона, текст сообщения. Данные передаются исключительно добровольно.</p>
        <h3 style="margin:1.5rem 0 .75rem">2. Как используются данные</h3>
        <p>Собранные данные используются исключительно для связи с вами по вопросу покупки автомобиля. Мы не передаём их третьим лицам.</p>
        <h3 style="margin:1.5rem 0 .75rem">3. Cookie и аналитика</h3>
        <p>Сайт может использовать файлы cookie и систему Яндекс.Метрика для анализа посещаемости в обезличенном виде.</p>
        <h3 style="margin:1.5rem 0 .75rem">4. Хранение данных</h3>
        <p>Данные хранятся в защищённой базе данных и не дольше, чем это необходимо для обработки вашего обращения.</p>
        <h3 style="margin:1.5rem 0 .75rem">5. Ваши права</h3>
        <p>Вы вправе запросить удаление ваших данных, написав на <?= SITE_EMAIL ?>.</p>
        <h3 style="margin:1.5rem 0 .75rem">6. Контакты</h3>
        <p>По вопросам конфиденциальности: <a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--blue-400)"><?= SITE_EMAIL ?></a></p>
        <p style="margin-top:1.5rem;color:var(--text-3)">Дата обновления: <?= date('d.m.Y') ?></p>
    </div>
</div></section>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
