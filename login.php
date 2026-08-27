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
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Acceso | Mantenciones</title><style>
*{box-sizing:border-box}body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.box{max-width:430px;margin:8vh auto;padding:32px;background:white;border-radius:16px;box-shadow:0 8px 30px #18304418}.brand{text-align:center;margin-bottom:22px}.logo{width:78px;height:78px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;background:#136f8a;border-radius:20px;box-shadow:0 8px 18px #136f8a30}.logo svg{width:48px;height:48px}.brand h1{margin:0;color:#183044;font-size:26px}.brand p{margin:6px 0 0;color:#647888;font-size:14px}label{display:block;margin:15px 0 6px;font-weight:700}input,button{width:100%;padding:13px;border:1px solid #c8d4de;border-radius:9px;font:inherit}input:focus{outline:2px solid #136f8a35;border-color:#136f8a}button{margin-top:20px;border:0;background:#136f8a;color:#fff;font-weight:700;cursor:pointer}button:hover{background:#0f5f76}.error{padding:10px;background:#ffe4e4;color:#9b1c1c;border-radius:8px}
</style></head><body><main class="box"><div class="brand"><div class="logo" aria-hidden="true"><svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M39.2 9.8a17 17 0 0 0-1.8 21.4L18.1 50.5a5.2 5.2 0 1 0 7.4 7.4l19.3-19.3a17 17 0 0 0 21.4-1.8l-11.4-2.1-6.2-6.2-2.1-11.4L39.2 9.8Z" fill="white"/><path d="M15 13h18v6H15z" fill="white" opacity=".35"/></svg></div><h1>Mantenciones</h1><p>Gestión y control de mantenciones</p></div><?php if($error):?><p class="error"><?=e($error)?></p><?php endif;?><form method="post"><input type="hidden" name="return" value="<?=e($return)?>"><label for="correo">Correo</label><input id="correo" type="email" name="correo" required autocomplete="username"><label for="password">Contraseña</label><input id="password" type="password" name="password" required autocomplete="current-password"><button type="submit">Ingresar</button></form></main></body></html>
