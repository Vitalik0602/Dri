<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';

$meta_title = $meta_title ?? SITE_NAME . ' — Автоплощадка в Москве';
$meta_description = $meta_description ?? SITE_DESCRIPTION;
$meta_keywords = $meta_keywords ?? SITE_KEYWORDS;
$canonical = $canonical ?? SITE_URL . $_SERVER['REQUEST_URI'];
$og_image = $og_image ?? SITE_URL . '/assets/images/og-image.jpg';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($meta_title) ?></title>
    <meta name="description" content="<?= h($meta_description) ?>">
    <meta name="keywords" content="<?= h($meta_keywords) ?>">
    <link rel="canonical" href="<?= h($canonical) ?>">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= h($meta_title) ?>">
    <meta property="og:description" content="<?= h($meta_description) ?>">
    <meta property="og:image" content="<?= h($og_image) ?>">
    <meta property="og:url" content="<?= h($canonical) ?>">
    <meta property="og:site_name" content="<?= h(SITE_NAME) ?>">
    <meta property="og:locale" content="ru_RU">
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= h($meta_title) ?>">
    <meta name="twitter:description" content="<?= h($meta_description) ?>">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css?v=1.2">
    <!-- Schema.org -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "AutoDealer",
      "name": "<?= SITE_NAME ?>",
      "url": "<?= SITE_URL ?>",
      "telephone": "<?= SITE_PHONE ?>",
      "email": "<?= SITE_EMAIL ?>",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Москва",
        "addressCountry": "RU",
        "streetAddress": "<?= SITE_ADDRESS ?>"
      },
      "openingHours": "Mo-Su 09:00-21:00",
      "image": "<?= $og_image ?>"
    }
    </script>
    <?php if (!empty($schema_car)): ?>
    <script type="application/ld+json"><?= $schema_car ?></script>
    <?php endif; ?>
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="site-header" id="site-header">
    <div class="header-inner container">
        <a href="/" class="logo" aria-label="Drive Hub — на главную">
            <span class="logo-icon">⬡</span>
            <span class="logo-text">Drive<strong>Hub</strong></span>
        </a>

        <nav class="main-nav" aria-label="Основная навигация">
            <a href="/" class="nav-link">Главная</a>
            <a href="/catalog/" class="nav-link">Каталог</a>
            <a href="/about/" class="nav-link">О нас</a>
            <a href="/contacts/" class="nav-link">Контакты</a>
        </nav>

        <div class="header-contacts">
            <a href="tel:<?= preg_replace('/[^+\d]/', '', SITE_PHONE) ?>" class="header-phone">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                <?= h(SITE_PHONE) ?>
            </a>
            <a href="/catalog/" class="btn btn-primary btn-sm">Все авто</a>
        </div>

        <button class="burger" id="burger" aria-label="Открыть меню" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true">
    <nav>
        <a href="/">Главная</a>
        <a href="/catalog/">Каталог</a>
        <a href="/about/">О нас</a>
        <a href="/contacts/">Контакты</a>
    </nav>
    <a href="tel:<?= preg_replace('/[^+\d]/', '', SITE_PHONE) ?>" class="mob-phone"><?= h(SITE_PHONE) ?></a>
</div>
<div class="overlay" id="overlay"></div>
