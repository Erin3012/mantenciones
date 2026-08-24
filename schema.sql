-- Importa este archivo después de seleccionar la base de datos creada en cPanel.
-- No se incluye CREATE DATABASE porque el usuario MySQL de cPanel normalmente
-- no tiene permiso para crear bases de datos desde un script SQL.

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('operario','supervisor') NOT NULL DEFAULT 'operario',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS carros (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) NOT NULL UNIQUE,
  descripcion VARCHAR(255) NULL,
  ubicacion VARCHAR(120) NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mantenciones (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  carro_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL,
  tipo ENUM('Preventiva','Correctiva') NOT NULL,
  descripcion TEXT NOT NULL,
  foto VARCHAR(255) NULL,
  realizada_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mant_carro FOREIGN KEY (carro_id) REFERENCES carros(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_mant_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_mant_carro_fecha (carro_id, realizada_en)
) ENGINE=InnoDB;

-- Usuario inicial: supervisor@example.com / CambiarAhora!2026
-- Debes cambiar esta contraseña después del primer acceso.
INSERT INTO usuarios (nombre, correo, password_hash, rol)
VALUES ('Supervisor inicial', 'supervisor@example.com', '$2y$10$7O9gTjbqRMK2PdZgkEDbuOAzntktEJBn5ugJUPhdiZsBCcw7tkWCW', 'supervisor')
ON DUPLICATE KEY UPDATE correo = correo;

INSERT INTO carros (codigo, descripcion, ubicacion) VALUES
('CARRO-001', 'Carro de ejemplo 001', 'Taller principal'),
('CARRO-002', 'Carro de ejemplo 002', 'Taller principal')
ON DUPLICATE KEY UPDATE codigo = codigo;
