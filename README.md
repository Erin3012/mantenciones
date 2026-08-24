# Sistema de mantenciones de carros

Aplicación PHP 8+ / MySQL compatible con hosting cPanel.

## Instalación en cPanel

1. Crea una base de datos MySQL y un usuario desde **MySQL Databases**. Asigna el usuario a la base con todos los privilegios.
2. Copia `.env.example` a `.env` y completa `DB_NAME`, `DB_USER` y `DB_PASS`. En cPanel suelen incluir el prefijo de tu cuenta. También cambia `APP_URL` por la URL HTTPS final, sin slash al final.
4. Abre phpMyAdmin, selecciona la base de datos y importa `schema.sql`.
5. Sube todos los archivos a `public_html` por FTP o el Administrador de archivos, incluyendo `.env` y `.htaccess`.
6. Confirma que exista la carpeta `uploads` y que PHP pueda escribir en ella. Normalmente `0755` es suficiente; usa `0775` solo si tu proveedor lo exige.
7. Accede con `supervisor@example.com` y contraseña `password`, genera una nueva contraseña segura y elimina los datos de ejemplo de `schema.sql` si corresponde.

## Uso

- El supervisor entra a `panel.php`, registra mantenciones, escanea carros y genera QR.
- El operario entra a `panel.php` y registra mantenciones.
- Cada QR apunta a `ver.php?carro=CODIGO` y el historial es público para permitir el flujo de escaneo.
- La cámara del teléfono requiere HTTPS, salvo excepciones de desarrollo local.

## Configuración recomendada de PHP

`upload_max_filesize` y `post_max_size` deben permitir al menos 5 MB. El sistema vuelve a validar el límite en código. Deben estar habilitadas las extensiones PDO MySQL, Fileinfo, GD o soporte de imágenes del servidor y Mbstring.

El archivo `uploads/.htaccess` impide ejecutar scripts dentro de la carpeta de imágenes. No subas archivos PHP allí.
