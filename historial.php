<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
$carros = db()->query('SELECT codigo, descripcion FROM carros WHERE activo=1 ORDER BY codigo')->fetchAll();
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ver historial</title><style>
body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.wrap{max-width:620px;margin:auto;padding:20px}.box{background:white;padding:24px;border-radius:16px;box-shadow:0 3px 15px #18304412}label{display:block;margin:15px 0 6px;font-weight:700}select,button{width:100%;padding:13px;border:1px solid #c8d4de;border-radius:9px;font:inherit}button{margin-top:20px;background:#136f8a;color:white;border:0;font-weight:700}a{color:#136f8a}
</style></head><body><main class="wrap"><a href="panel.php">← Volver al panel</a><section class="box"><h1>Ver historial</h1><p>Selecciona un carro para consultar sus mantenciones.</p><form><label for="carro">Carro</label><select id="carro" name="carro" required><option value="">Selecciona un carro</option><?php foreach($carros as $carro):?><option value="<?=e($carro['codigo'])?>"><?=e($carro['codigo'])?><?= $carro['descripcion'] ? ' — '.e($carro['descripcion']) : ''?></option><?php endforeach;?></select><button type="submit">Abrir historial</button></form></section></main></body></html>
