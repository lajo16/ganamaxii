-- Estructura de tablas base para GanaMaxii
-- (versión de ejemplo, sin datos reales)

DROP DATABASE IF EXISTS ganamaxii;
CREATE DATABASE ganamaxii CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ganamaxii;

-- Tabla propiedades
CREATE TABLE propiedades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(12,2) NOT NULL,
  moneda ENUM('PEN','USD') DEFAULT 'PEN',
  area DECIMAL(8,2),
  dormitorios INT DEFAULT 0,
  banos INT DEFAULT 0,
  direccion VARCHAR(255),
  departamento VARCHAR(100),
  provincia VARCHAR(100),
  distrito VARCHAR(100),
  tipo ENUM('Casa','Departamento','Lote','Local','Estacionamiento') DEFAULT 'Departamento',
  estado ENUM('Disponible','Vendido','Alquilado') DEFAULT 'Disponible',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla imágenes
CREATE TABLE imagenes_propiedades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  propiedad_id INT NOT NULL,
  ruta_imagen VARCHAR(255) NOT NULL,
  FOREIGN KEY (propiedad_id) REFERENCES propiedades(id) ON DELETE CASCADE
);

-- Datos ficticios de ejemplo
INSERT INTO propiedades 
(titulo, descripcion, precio, moneda, area, dormitorios, banos, direccion, departamento, provincia, distrito, tipo, estado)
VALUES
('Departamento en Miraflores', 'Hermoso departamento cerca al malecón.', 150000.00, 'USD', 85.00, 3, 2, 'Av. Pardo 123', 'Lima', 'Lima', 'Miraflores', 'Departamento', 'Disponible'),
('Casa en San Juan de Lurigancho', 'Casa amplia ideal para familia grande.', 350000.00, 'PEN', 160.00, 4, 3, 'Jr. Los Próceres 456', 'Lima', 'Lima', 'San Juan de Lurigancho', 'Casa', 'Disponible'),
('Terreno en Cusco', 'Terreno con excelente vista en zona turística.', 90000.00, 'USD', 300.00, 0, 0, 'Av. Principal s/n', 'Cusco', 'Cusco', 'Cusco', 'Lote', 'Disponible');

INSERT INTO imagenes_propiedades (propiedad_id, ruta_imagen) VALUES
(1, 'assets/img/demo/departamento1.jpg'),
(1, 'assets/img/demo/departamento2.jpg'),
(2, 'assets/img/demo/casa1.jpg'),
(3, 'assets/img/demo/terreno1.jpg');
