<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
$user = require_role('operario', 'supervisor');

$carros = db()->query('SELECT id, codigo, descripcion FROM carros WHERE activo=1 ORDER BY codigo')->fetchAll();
$codigoSeleccionado = trim((string) ($_GET['carro'] ?? ''));
$carroSeleccionado = null;
$historial = [];
$error = '';

if ($codigoSeleccionado !== '') {
    $stmt = db()->prepare('SELECT id, codigo, descripcion, ubicacion FROM carros WHERE codigo = ? AND activo = 1 LIMIT 1');
    $stmt->execute([$codigoSeleccionado]);
    $carroSeleccionado = $stmt->fetch();

    if (!$carroSeleccionado) {
        $error = 'El carro seleccionado no existe o está inactivo.';
    } else {
        $stmt = db()->prepare('SELECT m.id, m.tipo, m.descripcion AS trabajo, m.foto, m.realizada_en, u.nombre AS trabajador
            FROM mantenciones m
            INNER JOIN usuarios u ON u.id = m.usuario_id
            WHERE m.carro_id = ?
            ORDER BY m.realizada_en DESC, m.id DESC');
        $stmt->execute([(int) $carroSeleccionado['id']]);
        $historial = $stmt->fetchAll();
    }
}
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Historial de mantenciones</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.wrap{max-width:900px;margin:auto;padding:20px}.box,.item{background:white;padding:24px;border-radius:16px;box-shadow:0 3px 15px #18304412;margin-top:18px}label{display:block;margin:0 0 6px;font-weight:700}select,button{width:100%;padding:13px;border:1px solid #c8d4de;border-radius:9px;font:inherit}button{margin-top:15px;background:#136f8a;color:white;border:0;font-weight:700}a{color:#136f8a}.error{padding:10px;background:#ffe4e4;color:#9b1c1c;border-radius:8px;margin-top:15px}.empty{padding:18px;background:#f5f8fa;border-radius:10px;margin-top:18px}.item h3{margin:0 0 8px}.meta{color:#607587;font-size:14px;margin-bottom:12px}.foto{max-width:100%;max-height:320px;border-radius:10px;margin-top:12px}.back{display:inline-block;margin-bottom:2px}.header{display:flex;justify-content:space-between;gap:15px;align-items:center;flex-wrap:wrap}.header h1{margin:0}.badge{display:inline-block;padding:5px 10px;border-radius:20px;background:#e3f1f5;color:#136f8a;font-weight:700;font-size:13px}.actions{display:flex;gap:8px;align-items:center;margin-top:14px;flex-wrap:wrap}.edit,.delete{display:inline-block;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;border:0;font:inherit;cursor:pointer}.edit{background:#eef6f8;color:#136f8a}.edit:hover{background:#dceef2}.delete{background:#fff0f0;color:#a32121}.delete:hover{background:#ffe0e0}
</style>
</head>
<body><main class="wrap">
<a class="back" href="panel.php">← Volver al panel</a>
<section class="box">
<div class="header"><h1>Historial de mantenciones</h1></div>
<p>Selecciona un carro para consultar todas sus mantenciones registradas.</p>
<form method="get">
<label for="carro">Carro</label>
<select id="carro" name="carro" required>
<option value="">Selecciona un carro</option>
<?php foreach($carros as $carro): ?>
<option value="<?=e($carro['codigo'])?>" <?=$codigoSeleccionado===$carro['codigo']?'selected':''?>><?=e($carro['codigo'])?><?= $carro['descripcion'] ? ' — '.e($carro['descripcion']) : ''?></option>
<?php endforeach; ?>
</select>
<button type="submit">Abrir historial</button>
</form>
<?php if($error): ?><p class="error"><?=e($error)?></p><?php endif; ?>
</section>

<?php if($carroSeleccionado): ?>
<section class="box">
<div class="header"><div><h2><?=e($carroSeleccionado['codigo'])?></h2><p><?=e($carroSeleccionado['descripcion'] ?? '')?><?= $carroSeleccionado['ubicacion'] ? ' · '.e($carroSeleccionado['ubicacion']) : ''?></p></div><span class="badge"><?=count($historial)?> mantención<?=count($historial)===1?'':'es'?></span></div>
<?php if(!$historial): ?>
<div class="empty">No hay mantenciones registradas para este carro.</div>
<?php else: ?>
<?php foreach($historial as $registro): ?>
<article class="item">
<h3><?=e($registro['tipo'])?></h3>
<div class="meta"><?=e(date('d/m/Y H:i', strtotime($registro['realizada_en'])))?> · Registrada por <?=e($registro['trabajador'])?></div>
<div><?=nl2br(e($registro['trabajo']))?></div>
<?php if($registro['foto']): ?><img class="foto" src="uploads/<?=e($registro['foto'])?>" alt="Foto de la mantención" loading="lazy"><?php endif; ?>
<?php if (($user['rol'] ?? '') === 'supervisor'): ?>
<div class="actions">
<a class="edit" href="editar_mantencion.php?id=<?=e((string)$registro['id'])?>">✏️ Editar</a>
<form method="post" action="eliminar_mantencion.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta mantención? Esta acción no se puede deshacer.');" style="margin:0">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<input type="hidden" name="id" value="<?=e((string)$registro['id'])?>">
<button type="submit" class="delete">🗑️ Eliminar</button>
</form>
</div>
<?php endif; ?>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
<?php endif; ?>
</main></body></html>
