<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

if (current_user()) {
    redirect('panel.php');
}
$error = '';
$return = (string) ($_GET['return'] ?? $_POST['return'] ?? 'panel.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT * FROM usuarios WHERE correo = ? AND activo = 1 LIMIT 1');
    $stmt->execute([$correo]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        redirect((str_starts_with($return, '/') && !str_starts_with($return, '//')) ? $return : 'panel.php');
    }
    $error = 'Correo o contraseña incorrectos.';
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Acceso</title><style>
*{box-sizing:border-box}body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.box{max-width:430px;margin:8vh auto;padding:28px;background:white;border-radius:16px;box-shadow:0 8px 30px #18304418}h1{margin-top:0}label{display:block;margin:15px 0 6px;font-weight:700}input,button{width:100%;padding:13px;border:1px solid #c8d4de;border-radius:9px;font:inherit}button{margin-top:20px;border:0;background:#136f8a;color:#fff;font-weight:700}.error{padding:10px;background:#ffe4e4;color:#9b1c1c;border-radius:8px}
</style></head><body><main class="box"><h1>Acceso al sistema</h1><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?><form method="post"><input type="hidden" name="return" value="<?=e($return)?>"><label for="correo">Correo</label><input id="correo" type="email" name="correo" required autocomplete="username"><label for="password">Contraseña</label><input id="password" type="password" name="password" required autocomplete="current-password"><button type="submit">Ingresar</button></form></main></body></html>
