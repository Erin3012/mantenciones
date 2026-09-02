<?php
/**
 * Login Module - Mantenciones System
 * 
 * GitHub Copilot estuvo aquí modificando este archivo.
 * Última modificación: 2026-08-28
 * 
 * Este módulo maneja la autenticación de usuarios en el sistema.
 */

declare(strict_types=1);
require_once __DIR__ . '/auth.php';

if (current_user()) {
    redirect('panel.php');
}
$error = '';
$return = (string) ($_GET['return'] ?? $_POST['return'] ?? 'panel.php');
$legacyPath = (string) (parse_url($return, PHP_URL_PATH) ?? '');
$legacyQuery = [];
parse_str((string) (parse_url($return, PHP_URL_QUERY) ?? ''), $legacyQuery);
if (in_array(ltrim($legacyPath, '/'), ['historial.php'], true) && preg_match('/^[A-Za-z0-9_-]{1,80}$/', (string)($legacyQuery['carro'] ?? ''))) {
    redirect('ver.php?carro=' . rawurlencode((string)$legacyQuery['carro']));
}
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
*{box-sizing:border-box}body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.box{max-width:430px;margin:8vh auto;padding:32px;background:white;border-radius:16px;box-shad[...]
</style></head><body><main class="box"><div class="brand"><div class="logo" aria-hidden="true"><svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M39.2 9.8a17 17 0 0 [...]
