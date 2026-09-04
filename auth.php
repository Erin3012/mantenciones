<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly'=>true,'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'samesite'=>'Lax']);
    session_start();
}

function current_user(): ?array {
    static $loaded = false, $user = null;
    if ($loaded) return $user;
    $loaded = true;
    if (empty($_SESSION['user_id'])) return null;
    $stmt = db()->prepare('SELECT id,nombre,correo,rol FROM usuarios WHERE id=? AND activo=1');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;
    if (!$user) unset($_SESSION['user_id']);
    return $user;
}

function require_login(): array {
    $user = current_user();
    if (!$user) redirect('login.php?return='.rawurlencode($_SERVER['REQUEST_URI'] ?? 'panel.php'));
    return $user;
}

function require_role(string ...$roles): array {
    $user = require_login();
    if (!in_array($user['rol'], $roles, true)) { http_response_code(403); exit('No tienes permisos para acceder a esta sección.'); }
    return $user;
}
