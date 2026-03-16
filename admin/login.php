<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin/'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass  = $_POST['password'] ?? '';
    if ($login === ADMIN_LOGIN && password_verify($pass, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: /admin/'); exit;
    } else {
        sleep(1); // замедление брутфорса
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Вход — Drive Hub Admin</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css?v=1.2">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--dark); }
        .login-box { width:100%; max-width:400px; padding:1rem; }
        .login-card { background:var(--dark-2); border:1px solid var(--border-2); border-radius:var(--radius-xl); padding:2.5rem; }
        .login-logo { text-align:center; margin-bottom:2rem; font-family:var(--font-head); font-size:1.5rem; font-weight:800; }
        .login-logo span { color:var(--blue-400); }
        .login-logo small { display:block; font-size:.8rem; color:var(--text-3); font-weight:400; margin-top:.25rem; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-card">
        <div class="login-logo">Drive<span>Hub</span><small>Панель управления</small></div>
        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:1.25rem"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="login" required autocomplete="username" placeholder="admin">
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary btn-full" style="margin-top:.5rem">Войти</button>
        </form>
    </div>
</div>
</body>
</html>
