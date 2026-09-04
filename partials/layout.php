<?php
declare(strict_types=1);

function layout_icon(string $name): string
{
    $paths = [
        'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10Z"/>',
        'edit' => '<path d="m4 20 4.5-1L19 8.5a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z"/><path d="m14.5 7.5 3 3"/>',
        'scan' => '<path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/><path d="M8 12h8M12 8v8"/>',
        'history' => '<path d="M4 12a8 8 0 1 0 2.3-5.7L4 8.5"/><path d="M4 4v4.5h4.5M12 8v4l2.5 1.5"/>',
        'fleet' => '<path d="M4 16V7l2-3h12l2 3v9M4 16h16M7 19h2M15 19h2M7 12h10"/>',
        'qr' => '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2v2h-2zM18 18h2v2h-2zM18 14h2v2h-2z"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">'.($paths[$name] ?? '').'</svg>';
}

function layout_start(string $title, array $user, string $active): void
{
    $items = [['panel.php','home','Escritorio','home'],['index.php','edit','Registrar mantención','registro'],['escanear.php','scan','Escanear QR','escaner'],['historial.php','history','Ver historial','historial']];
    if (($user['rol'] ?? '') === 'supervisor') { $items[] = ['carros.php','fleet','Administrar carros','carros']; $items[] = ['generar_qr.php','qr','Códigos QR','qr']; }
    ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?></title><link rel="stylesheet" href="assets/css/app.css"><link rel="stylesheet" href="assets/css/layout.css"><link rel="stylesheet" href="assets/css/modules.css"></head><body class="module-page"><div class="app-shell"><aside class="sidebar"><a class="brand" href="panel.php"><span class="brand-mark">Q</span><span>QLC</span></a><nav aria-label="Navegación principal"><?php foreach($items as [$href,$icon,$label,$key]): ?><a class="nav-item <?=$active===$key?'active':''?>" href="<?=e($href)?>"><?=layout_icon($icon)?><span><?=e($label)?></span></a><?php endforeach; ?></nav><div class="side-footer"><span class="status-dot"></span>Sistema operativo</div></aside><main class="main-content"><header class="topbar"><button class="menu-button" type="button" aria-label="Mostrar navegación" onclick="document.body.classList.toggle('menu-open')">☰</button><div class="top-user"><span class="avatar"><?=e(strtoupper(substr((string)$user['nombre'],0,1)))?></span><span>Hola, <?=e($user['nombre'])?> <small>(<?=e(ucfirst((string)$user['rol']))?>)</small></span><a href="logout.php">Cerrar sesión</a></div></header><div class="module-content"><?php
}

function layout_end(): void { ?></div></main></div></body></html><?php }
