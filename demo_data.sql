-- Datos ficticios para una demostración comercial.
-- Importar una sola vez después de schema.sql, seleccionando la base correcta.
INSERT INTO usuarios (nombre, correo, password_hash, rol) VALUES
('Ana Pérez', 'ana.demo@example.com', '$2y$10$7O9gTjbqRMK2PdZgkEDbuOAzntktEJBn5ugJUPhdiZsBCcw7tkWCW', 'operario'),
('Carlos Soto', 'carlos.demo@example.com', '$2y$10$7O9gTjbqRMK2PdZgkEDbuOAzntktEJBn5ugJUPhdiZsBCcw7tkWCW', 'operario')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), rol = VALUES(rol), activo = 1;
INSERT INTO carros (codigo, descripcion, ubicacion) VALUES
('CARRO-101', 'Carro transporte pasajeros serie A', 'Taller Norte'),
('CARRO-102', 'Carro transporte pasajeros serie A', 'Taller Norte'),
('CARRO-103', 'Carro transporte pasajeros serie B', 'Taller Sur'),
('CARRO-104', 'Carro transporte pasajeros serie B', 'Taller Sur'),
('CARRO-105', 'Carro de inspección técnica', 'Vía de pruebas')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion), ubicacion = VALUES(ubicacion), activo = 1;
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Preventiva','Inspección visual de bogies, revisión de fijaciones y limpieza de sensores.','2026-08-31 08:15:00' FROM carros c JOIN usuarios u ON u.correo='ana.demo@example.com' WHERE c.codigo='CARRO-101';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Correctiva','Ajuste de cierre de puerta lateral y prueba funcional de apertura y bloqueo.','2026-08-29 14:40:00' FROM carros c JOIN usuarios u ON u.correo='carlos.demo@example.com' WHERE c.codigo='CARRO-101';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Preventiva','Control de niveles, lubricación de componentes móviles y verificación de señalización.','2026-08-30 09:05:00' FROM carros c JOIN usuarios u ON u.correo='ana.demo@example.com' WHERE c.codigo='CARRO-102';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Correctiva','Reemplazo de luminaria interior y comprobación del circuito eléctrico.','2026-08-27 11:20:00' FROM carros c JOIN usuarios u ON u.correo='carlos.demo@example.com' WHERE c.codigo='CARRO-102';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Preventiva','Revisión de freno de estacionamiento, pastillas y estado general del sistema neumático.','2026-08-28 07:50:00' FROM carros c JOIN usuarios u ON u.correo='ana.demo@example.com' WHERE c.codigo='CARRO-103';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Correctiva','Corrección de vibración detectada en marcha y reapriete de elementos del salón.','2026-08-26 16:10:00' FROM carros c JOIN usuarios u ON u.correo='carlos.demo@example.com' WHERE c.codigo='CARRO-103';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Preventiva','Chequeo de ruedas, ejes y registro fotográfico del estado del conjunto.','2026-08-25 10:35:00' FROM carros c JOIN usuarios u ON u.correo='ana.demo@example.com' WHERE c.codigo='CARRO-104';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Preventiva','Prueba de radio, inspección de instrumentos y actualización de lista de chequeo.','2026-08-24 13:00:00' FROM carros c JOIN usuarios u ON u.correo='carlos.demo@example.com' WHERE c.codigo='CARRO-105';
INSERT INTO mantenciones (carro_id, usuario_id, tipo, descripcion, realizada_en) SELECT c.id,u.id,'Correctiva','Cambio de conector deteriorado en panel de control y prueba de continuidad.','2026-08-22 15:25:00' FROM carros c JOIN usuarios u ON u.correo='ana.demo@example.com' WHERE c.codigo='CARRO-105';
