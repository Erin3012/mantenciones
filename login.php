<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

if (current_user()) redirect('panel.php');
$error = '';
$return = (string)($_GET['return'] ?? $_POST['return'] ?? 'panel.php');
$legacyPath = (string)(parse_url($return, PHP_URL_PATH) ?? '');
$legacyQuery = [];
parse_str((string)(parse_url($return, PHP_URL_QUERY) ?? ''), $legacyQuery);
if (in_array(ltrim($legacyPath, '/'), ['historial.php'], true) && preg_match('/^[A-Za-z0-9_-]{1,80}$/', (string)($legacyQuery['carro'] ?? ''))) {
    redirect('ver.php?carro=' . rawurlencode((string)$legacyQuery['carro']));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = strtolower(trim((string)($_POST['correo'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id, nombre, correo, password_hash, rol FROM usuarios WHERE correo=? AND activo=1 LIMIT 1');
    $stmt->execute([$correo]);
    $account = $stmt->fetch();
    if ($account && password_verify($password, $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$account['id'];
        redirect((str_starts_with($return, '/') && !str_starts_with($return, '//')) ? $return : 'panel.php');
    }
    $error = 'Correo o contraseña incorrectos.';
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Acceso | Mantenciones</title><link rel="stylesheet" href="assets/css/app.css"><style>
.login-page{min-height:100vh;display:grid;place-items:center;padding:20px;background:var(--wash)}.login-box{width:min(420px,100%);background:#fff;border:1px solid var(--line);border-radius:18px;padding:34px;box-shadow:var(--shadow)}.login-brand{display:flex;align-items:center;gap:10px;font-size:1.6rem;font-weight:850;margin-bottom:25px}.login-brand .brand-mark{display:grid;place-items:center;width:34px;height:34px;border:4px solid var(--teal);border-radius:50%;color:var(--teal)}.login-box h1{margin:0 0 6px;font-size:1.8rem}.login-box .intro{margin:0 0 24px;color:var(--muted)}.login-box label{display:block;margin:15px 0 6px;font-weight:750}.login-box input{width:100%;min-height:46px;border:1px solid #cbdcde;border-radius:9px;padding:11px 12px;font:inherit;color:var(--ink)}.login-box input:focus{outline:3px solid #b7dddd;border-color:var(--teal)}.login-box button{width:100%;min-height:46px;margin-top:22px;border:0;border-radius:9px;background:var(--teal);color:#fff;font:inherit;font-weight:800;cursor:pointer}.login-box button:hover{background:#176d70}.error{padding:12px;border-radius:9px;background:#ffe8e8;color:#9b1c1c;margin-bottom:15px}
</style></head><body><main class="login-page"><section class="login-box"><div class="login-brand"><span class="brand-mark">Q</span><span>QLC</span></div><h1>Acceso al sistema</h1><p class="intro">Ingresa para administrar las mantenciones ferroviarias.</p><?php if($error):?><div class="error" role="alert"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="return" value="<?=e($return)?>"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email" autocomplete="username" required><label for="password">Contraseña</label><input id="password" name="password" type="password" autocomplete="current-password" required><button type="submit">Ingresar</button></form></section></main></body></html>
