<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
$user = require_role('operario', 'supervisor');
$errors = [];
$success = '';
$old = ['carro_id'=>'','tipo'=>'Preventiva','descripcion'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $errors[] = 'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.';
    }
    $old['carro_id'] = (string) ($_POST['carro_id'] ?? '');
    $old['tipo'] = (string) ($_POST['tipo'] ?? '');
    $old['descripcion'] = trim((string) ($_POST['descripcion'] ?? ''));
    $carroId = filter_var($old['carro_id'], FILTER_VALIDATE_INT);
    if (!$carroId) $errors[] = 'Selecciona un carro válido.';
    if (!in_array($old['tipo'], ['Preventiva','Correctiva'], true)) $errors[] = 'Selecciona un tipo válido.';
    if ($old['descripcion'] === '' || mb_strlen($old['descripcion']) > 10000) $errors[] = 'La descripción es obligatoria y no puede superar 10.000 caracteres.';
    $fotoNombre = null;
    $file = $_FILES['foto'] ?? null;
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'La foto no se pudo cargar correctamente.';
        } elseif ($file['size'] > MAX_UPLOAD_BYTES) {
            $errors[] = 'La foto supera los 5 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            $dimensions = @getimagesize($file['tmp_name']);
            if (!isset($allowed[$mime]) || !$dimensions) {
                $errors[] = 'Solo se permiten imágenes JPG, PNG o WEBP.';
            } else {
                $fotoNombre = bin2hex(random_bytes(20)) . '.' . $allowed[$mime];
                $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) $errors[] = 'No se pudo crear la carpeta de imágenes.';
                if (!$errors && !move_uploaded_file($file['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $fotoNombre)) $errors[] = 'No se pudo guardar la foto.';
            }
        }
    }
    if (!$errors) {
        $check = db()->prepare('SELECT id FROM carros WHERE id = ? AND activo = 1'); $check->execute([$carroId]);
        if (!$check->fetchColumn()) $errors[] = 'El carro seleccionado no existe o está inactivo.';
    }
    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, foto) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$carroId, $user['id'], $old['tipo'], $old['descripcion'], $fotoNombre]);
        $success = 'Mantención registrada correctamente.';
        $old = ['carro_id'=>'','tipo'=>'Preventiva','descripcion'=>''];
    } elseif ($fotoNombre && is_file(__DIR__ . '/uploads/' . $fotoNombre)) { @unlink(__DIR__ . '/uploads/' . $fotoNombre); }
}
$carros = db()->query('SELECT id, codigo, descripcion FROM carros WHERE activo = 1 ORDER BY codigo')->fetchAll();
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Registrar mantención</title><style>
*{box-sizing:border-box}body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.wrap{max-width:700px;margin:auto;padding:20px}.box{background:#fff;padding:24px;border-radius:16px;box-shadow:0 4px 20px #18304414}label{display:block;margin:16px 0 6px;font-weight:700}select,textarea,input,button{width:100%;padding:12px;border:1px solid #c8d4de;border-radius:8px;font:inherit}textarea{min-height:140px;resize:vertical}button{margin-top:20px;background:#136f8a;color:#fff;border:0;font-weight:700}.error{background:#ffe5e5;color:#921c1c;padding:10px;border-radius:8px}.ok{background:#e3f7e9;color:#176b35;padding:10px;border-radius:8px}.back{display:inline-block;margin-bottom:18px;color:#136f8a}
</style></head><body><main class="wrap"><a class="back" href="panel.php">← Volver al panel</a><section class="box"><h1>Registrar mantención</h1><?php if($errors):?><div class="error"><ul><?php foreach($errors as $error):?><li><?=e($error)?></li><?php endforeach;?></ul></div><?php endif;?><?php if($success):?><p class="ok"><?=e($success)?></p><?php endif;?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label for="carro_id">Carro</label><select id="carro_id" name="carro_id" required><option value="">Selecciona un carro</option><?php foreach($carros as $carro):?><option value="<?=$carro['id']?>" <?=$old['carro_id']==$carro['id']?'selected':''?>><?=e($carro['codigo'])?><?= $carro['descripcion'] ? ' — '.e($carro['descripcion']) : ''?></option><?php endforeach;?></select><label for="trabajador">Trabajador</label><input id="trabajador" type="text" value="<?=e($user['nombre'])?>" readonly aria-describedby="trabajador-ayuda"><small id="trabajador-ayuda">Se asociará tu usuario autenticado a esta mantención.</small><label for="tipo">Tipo de mantención</label><select id="tipo" name="tipo" required><option <?=$old['tipo']==='Preventiva'?'selected':''?>>Preventiva</option><option <?=$old['tipo']==='Correctiva'?'selected':''?>>Correctiva</option></select><label for="descripcion">Descripción del trabajo</label><textarea id="descripcion" name="descripcion" required maxlength="10000"><?=e($old['descripcion'])?></textarea><label for="foto">Foto (opcional, máximo 5 MB)</label><input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" capture="environment"><button type="submit">Guardar mantención</button></form></section></main></body></html>
