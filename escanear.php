<?php
require_once __DIR__ . '/auth.php';
require_role('supervisor');
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Escanear QR</title><script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script><style>
body{margin:0;background:#eef3f7;color:#183044;font:16px system-ui,sans-serif}.wrap{max-width:600px;margin:auto;padding:20px}.box{background:#fff;padding:20px;border-radius:16px;box-shadow:0 3px 15px #18304412}#reader{width:100%;margin-top:15px}a{color:#136f8a}.warn{padding:10px;background:#fff0d5;border-radius:8px}
</style></head><body><main class="wrap"><a href="panel.php">← Volver al panel</a><section class="box"><h1>Escanear código QR</h1><p>Apunta la cámara al QR pegado en el carro.</p><?php if(empty($_SERVER['HTTPS'])||$_SERVER['HTTPS']==='off'):?><p class="warn">La cámara puede ser bloqueada por el navegador. Usa HTTPS en el dominio del hosting.</p><?php endif;?><div id="reader"></div><p id="status"></p></section></main><script>
const status=document.getElementById('status');
function onScan(decoded){try{const url=new URL(decoded,window.location.origin);const code=url.searchParams.get('carro');if(code){status.textContent='Código detectado. Abriendo historial…';window.location.href='ver.php?carro='+encodeURIComponent(code);return;}}catch(e){const code=decoded.trim();if(/^[A-Za-z0-9_-]{1,80}$/.test(code)){status.textContent='Código detectado. Abriendo historial…';window.location.href='ver.php?carro='+encodeURIComponent(code);return;}}status.textContent='El QR no contiene un código de carro válido.';}
function onError(){/* Errores de lectura normales mientras se enfoca la cámara. */}
new Html5QrcodeScanner('reader',{fps:10,qrbox:{width:250,height:250}},false).render(onScan,onError);
</script></body></html>
