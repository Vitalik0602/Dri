<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$cars = db()->fetchAll("SELECT slug, updated_at FROM cars WHERE status='active' ORDER BY updated_at DESC");
$today = date('Y-m-d');
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc><?= SITE_URL ?>/</loc><lastmod><?= $today ?></lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>
    <url><loc><?= SITE_URL ?>/catalog/</loc><lastmod><?= $today ?></lastmod><changefreq>daily</changefreq><priority>0.9</priority></url>
    <url><loc><?= SITE_URL ?>/about/</loc><lastmod><?= $today ?></lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>
    <url><loc><?= SITE_URL ?>/contacts/</loc><lastmod><?= $today ?></lastmod><changefreq>monthly</changefreq><priority>0.7</priority></url>
    <?php foreach ($cars as $car): ?>
    <url>
        <loc><?= SITE_URL ?>/cars/<?= htmlspecialchars($car['slug']) ?>/</loc>
        <lastmod><?= date('Y-m-d', strtotime($car['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
</urlset>
