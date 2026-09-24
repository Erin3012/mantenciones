<?php
declare(strict_types=1);
require_once __DIR__ . '/client.php';
if (current_user()) redirect('../panel.php');
$state = (string)($_GET['state'] ?? ''); $expected = (string)($_SESSION['sso_state'] ?? ''); unset($_SESSION['sso_state']); $code = (string)($_GET['code'] ?? '');
if ($expected === '' || $state === '' || !hash_equals($expected, $state) || !preg_match('/^[A-Za-z0-9_-]{40,64}$/', $code)) redirect('error.php?code=invalid');
try {
    $identity = sso_exchange($code, 'maintenance');
    $stmt = db()->prepare('SELECT id FROM usuarios WHERE correo=? AND activo=1 LIMIT 1'); $stmt->execute([strtolower(trim((string)$identity['email']))]); $localId = $stmt->fetchColumn();
    if (!$localId) redirect('error.php?code=unlinked');
    session_regenerate_id(true); $_SESSION['user_id'] = (int)$localId; redirect('../panel.php');
} catch (Throwable $e) { error_log($e->getMessage()); redirect('error.php?code=invalid'); }
