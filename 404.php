<?php
http_response_code(404);
$meta_title = '404 — Страница не найдена | Drive Hub';
require_once __DIR__ . '/includes/header.php';
?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding-top:72px">
<div style="text-align:center;padding:2rem">
    <div style="font-size:6rem;font-weight:900;font-family:var(--font-head);color:var(--blue-600);line-height:1">404</div>
    <h1 style="margin:.5rem 0 1rem">Страница не найдена</h1>
    <p style="color:var(--text-2);max-width:400px;margin:0 auto 2rem">Возможно, автомобиль уже продан или адрес изменился</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <a href="/" class="btn btn-primary">На главную</a>
        <a href="/catalog/" class="btn btn-outline">Все автомобили</a>
    </div>
</div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
