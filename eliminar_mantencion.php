<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_role('supervisor');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Solicitud no válida.'); }
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT); $codigo = trim((string)($_POST['carro'] ?? ''));
if (!$id) { http_response_code(400); exit('Mantención inválida.'); }
$stmt = db()->prepare('SELECT foto FROM mantenciones WHERE id=?'); $stmt->execute([$id]); $row = $stmt->fetch();
if ($row) { $delete = db()->prepare('DELETE FROM mantenciones WHERE id=?'); $delete->execute([$id]); if ($row['foto'] && preg_match('/^[a-f0-9]{40}\.(jpg|png|webp)$/', $row['foto'])) @unlink(__DIR__.'/uploads/'.$row['foto']); }
redirect('ver.php?carro='.rawurlencode($codigo));
