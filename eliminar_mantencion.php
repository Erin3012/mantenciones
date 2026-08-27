<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_role('supervisor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Mantención inválida.'); }

$stmt = db()->prepare('SELECT m.foto, c.codigo FROM mantenciones m JOIN carros c ON c.id=m.carro_id WHERE m.id=?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) { http_response_code(404); exit('Mantención no encontrada.'); }

$delete = db()->prepare('DELETE FROM mantenciones WHERE id=?');
$delete->execute([$id]);

if ($row['foto'] && preg_match('/^[a-f0-9]{40}\.(jpg|png|webp)$/', $row['foto'])) {
    $path = __DIR__.'/uploads/'.$row['foto'];
    if (is_file($path)) @unlink($path);
}

redirect('historial.php?carro='.rawurlencode($row['codigo']));
