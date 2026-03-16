<?php
$meta_title = 'О нас — Drive Hub | Честная автоплощадка в Москве';
$meta_description = 'Узнайте о Drive Hub — молодой и честной автоплощадке в Москве. Наши принципы, миссия и почему мы выбрали этот путь.';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="padding-top:72px">
<section class="section">
<div class="container">
    <nav class="breadcrumb"><a href="/">Главная</a><span>/</span><span>О нас</span></nav>

    <div class="about-grid" style="margin-bottom:5rem;margin-top:2rem">
        <div class="about-text">
            <span class="section-label">Наша история</span>
            <h1 style="margin-bottom:1.5rem">Мы только начинаем,<br>но уже делаем это честно</h1>
            <div class="car-desc">
                <p>Drive Hub — это молодая автоплощадка, которую мы основали с простой идеей: продавать машины так, как хотели бы покупать их сами.</p>
                <p>На рынке перекупов много серых схем. Скрученные пробеги, замалёванные аварии, комиссии, о которых узнаёшь в последний момент. Мы решили работать иначе.</p>
                <p>Каждый автомобиль, который мы выставляем на продажу, мы проверяем лично. Рассказываем всё — и хорошее, и то, что стоит учесть. Цена на сайте — это окончательная цена.</p>
                <p>Мы в начале пути, но уже сейчас каждая сделка — это доброе слово от покупателя. Именно это нас и мотивирует двигаться дальше.</p>
            </div>
        </div>
        <div class="about-img">
            <img src="/assets/images/about.jpg" alt="Команда Drive Hub" loading="lazy" onerror="this.style.display='none';this.parentElement.style.background='var(--surface)'">
        </div>
    </div>

    <div class="section-header" style="margin-bottom:2.5rem">
        <span class="section-label">Наши ценности</span>
        <h2>На чём мы стоим</h2>
    </div>
    <div class="why-grid" style="margin-bottom:5rem">
        <div class="why-card">
            <div class="why-icon">🤝</div>
            <h3>Честность</h3>
            <p>Мы говорим правду о каждом автомобиле. Даже если это усложняет продажу — умолчать или приукрасить не в нашем стиле.</p>
        </div>
        <div class="why-card">
            <div class="why-icon">💎</div>
            <h3>Качество над количеством</h3>
            <p>Лучше меньше авто в наличии, но каждое — проверенное и достойное. Мы не гонимся за объёмами.</p>
        </div>
        <div class="why-card">
            <div class="why-icon">🔓</div>
            <h3>Прозрачность</h3>
            <p>История, документы, реальные фото — всё открыто. Скрывать нечего.</p>
        </div>
        <div class="why-card">
            <div class="why-icon">🚀</div>
            <h3>Развитие</h3>
            <p>Мы учимся каждый день и становимся лучше. Каждая сделка — опыт, каждый отзыв — повод расти.</p>
        </div>
    </div>

    <div style="text-align:center">
        <h2 style="margin-bottom:1rem">Есть вопрос или хотите посмотреть авто?</h2>
        <p style="color:var(--text-2);margin-bottom:2rem">Будем рады познакомиться лично — приезжайте на площадку или напишите нам</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
            <a href="/catalog/" class="btn btn-primary btn-lg">Смотреть каталог</a>
            <a href="/contacts/" class="btn btn-outline btn-lg">Контакты</a>
        </div>
    </div>
</div>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
