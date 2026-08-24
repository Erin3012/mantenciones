<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_role('supervisor');
$id = filter_var($_GET['id'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT); $errors = [];
if (!$id) { http_response_code(404); exit('Mantención no encontrada.'); }
$stmt = db()->prepare('SELECT m.*, c.codigo FROM mantenciones m JOIN carros c ON c.id=m.carro_id WHERE m.id=?'); $stmt->execute([$id]); $mantencion = $stmt->fetch();
if (!$mantencion) { http_response_code(404); exit('Mantención no encontrada.'); }
$tipo = (string)$mantencion['tipo']; $descripcion = (string)$mantencion['descripcion'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) $errors[] = 'La sesión expiró. Recarga la página.';
    $tipo = (string)($_POST['tipo'] ?? ''); $descripcion = trim((string)($_POST['descripcion'] ?? ''));
    if (!in_array($tipo, ['Preventiva','Correctiva'], true)) $errors[] = 'Tipo inválido.';
    if ($descripcion === '' || mb_strlen($descripcion) > 10000) $errors[] = 'La descripción es obligatoria y no puede superar 10.000 caracteres.';
    $newPhoto = null; $file = $_FILES['foto'] ?? null;
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > MAX_UPLOAD_BYTES) $errors[] = 'La foto es inválida o supera los 5 MB.';
        else { $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']); $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if (!isset($allowed[$mime]) || !@getimagesize($file['tmp_name'])) $errors[] = 'Solo se permiten imágenes JPG, PNG o WEBP.';
            else { $newPhoto = bin2hex(random_bytes(20)) . '.' . $allowed[$mime]; if (!move_uploaded_file($file['tmp_name'], __DIR__.'/uploads/'.$newPhoto)) { $errors[] = 'No se pudo guardar la nueva foto.'; $newPhoto = null; } }
        }
    }
    if (!$errors) { $photo = $newPhoto ?: $mantencion['foto']; $update = db()->prepare('UPDATE mantenciones SET tipo=?, descripcion=?, foto=? WHERE id=?'); $update->execute([$tipo, $descripcion, $photo, $id]); if ($newPhoto && $mantencion['foto'] && preg_match('/^[a-f0-9]{40}\.(jpg|png|webp)$/', $mantencion['foto'])) @unlink(__DIR__.'/uploads/'.$mantencion['foto']); redirect('ver.php?carro='.rawurlencode($mantencion['codigo'])); }
    if ($newPhoto) @unlink(__DIR__.'/uploads/'.$newPhoto);
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editar mantención</title><style>body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.wrap{max-width:650px;margin:auto;padding:20px}.box{background:#fff;padding:24px;border-radius:16px}label{display:block;margin:16px 0 6px;font-weight:700}select,textarea,input,button{width:100%;padding:12px;border:1px solid #c8d4de;border-radius:8px;font:inherit}textarea{min-height:150px}button{margin-top:20px;background:#136f8a;color:white;border:0;font-weight:700}.error{background:#ffe5e5;color:#921c1c;padding:10px;border-radius:8px}.back{color:#136f8a}</style></head><body><main class="wrap"><a class="back" href="ver.php?carro=<?=rawurlencode($mantencion['codigo'])?>">← Volver al historial</a><section class="box"><h1>Editar mantención</h1><p>Carro: <strong><?=e($mantencion['codigo'])?></strong></p><?php if($errors):?><div class="error"><ul><?php foreach($errors as $error):?><li><?=e($error)?></li><?php endforeach;?></ul></div><?php endif;?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$id?>"><label for="tipo">Tipo</label><select id="tipo" name="tipo"><option <?=$tipo==='Preventiva'?'selected':''?>>Preventiva</option><option <?=$tipo==='Correctiva'?'selected':''?>>Correctiva</option></select><label for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion" maxlength="10000" required><?=e($descripcion)?></textarea><label for="foto">Reemplazar foto (opcional)</label><input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" capture="environment"><button type="submit">Guardar cambios</button></form></section></main></body></html>
