<?php
// Этот скрипт создаёт placeholder-изображения для разработки
// На продакшне замените на реальные файлы

header('Content-Type: image/svg+xml');
$w = $_GET['w'] ?? 800;
$h = $_GET['h'] ?? 600;
$text = urldecode($_GET['text'] ?? 'Фото\nнедоступно');
?>
<svg width="<?= (int)$w ?>" height="<?= (int)$h ?>" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="#1c2540"/>
  <text x="50%" y="45%" font-family="Arial" font-size="18" fill="#3b5298" text-anchor="middle">🚗</text>
  <text x="50%" y="58%" font-family="Arial" font-size="14" fill="#64748b" text-anchor="middle">Фото недоступно</text>
</svg>
