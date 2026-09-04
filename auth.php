<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Inyecta la navegación común en los módulos autenticados sin duplicar el HTML
// existente de cada pantalla. El dashboard tiene su propia barra lateral.
ob_start(function (string $html): string {
    if (!str_contains($html, '<body') || in_array(basename((string)($_SERVER['SCRIPT_NAME'] ?? '')), ['panel.php', 'login.php', 'logout.php', 'ver.php'], true)) {
        return $html;
    }
    $navUser = current_user();
    if (!$navUser) return $html;
    $page = basename((string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''));
    $active = ['index.php'=>'registro','escanear.php'=>'escaner','historial.php'=>'historial','carros.php'=>'carros','generar_qr.php'=>'qr','editar_mantencion.php'=>'historial'][$page] ?? '';
    $link = static function (string $href, string $key, string $label) use ($active): string {
        return '<a class="'.($active===$key?'active':'').'" href="'.e($href).'">'.$label.'</a>';
    };
    $nav = '<style>.module-nav{position:fixed;z-index:20;top:0;bottom:0;left:0;width:238px;background:#f7fbfb;border-right:1px solid #d9e7e8;padding:28px 16px;display:flex;flex-direction:column}.module-nav .module-brand{display:flex;align-items:center;gap:10px;color:#152f3b;font-size:1.45rem;font-weight:850;text-decoration:none;padding:0 10px 30px}.module-nav .module-mark{display:grid;place-items:center;width:30px;height:30px;border:4px solid #238b8d;border-radius:50%;color:#238b8d}.module-nav nav a{display:flex;align-items:center;gap:10px;min-height:44px;padding:9px 11px;margin:3px 0;border-radius:8px;color:#152f3b;text-decoration:none;font-weight:600;white-space:nowrap}.module-nav nav a:hover,.module-nav nav a.active{background:#dceced;color:#176d70}.module-nav .module-status{margin-top:auto;padding:15px 11px;color:#71808a;font-size:.78rem}.module-nav .dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#42aa75;margin-right:6px}.module-nav~main.wrap{margin-left:270px}@media(max-width:680px){.module-nav{position:relative;top:auto;bottom:auto;width:100%;height:auto;padding:10px 12px;display:block;overflow-x:auto;white-space:nowrap}.module-nav .module-brand{display:inline-flex;padding:0 8px 8px}.module-nav nav{display:flex;gap:4px}.module-nav nav a{display:inline-flex;width:auto}.module-nav .module-status{display:none}.module-nav~main.wrap{margin-left:auto}}</style><aside class="module-nav"><a class="module-brand" href="panel.php"><span class="module-mark">Q</span><span>QLC</span></a><nav aria-label="Navegación principal">'.$link('index.php','registro','✎ Registrar mantención').$link('escanear.php','escaner','⌗ Escanear QR').$link('historial.php','historial','↶ Ver historial').($navUser['rol']==='supervisor'?$link('carros.php','carros','▣ Administrar carros').$link('generar_qr.php','qr','▦ Códigos QR'):'').'</nav><div class="module-status"><span class="dot"></span>Sistema operativo</div></aside>';
    return preg_replace('/<body([^>]*)>/i', '$0'.$nav, $html, 1) ?? $html;
});

function current_user(): ?array
{
    static $loaded = false;
    static $user = null;
    if ($loaded) {
        return $user;
    }
    $loaded = true;
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, nombre, correo, rol FROM usuarios WHERE id = ? AND activo = 1');
    $stmt->execute([(int) $_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;
    if (!$user) {
        unset($_SESSION['user_id']);
    }
    return $user;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        $return = $_SERVER['REQUEST_URI'] ?? 'panel.php';
        redirect('login.php?return=' . rawurlencode($return));
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['rol'], $roles, true)) {
        http_response_code(403);
        exit('No tienes permisos para acceder a esta sección.');
    }
    return $user;
}
