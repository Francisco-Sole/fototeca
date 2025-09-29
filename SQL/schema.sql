-- Crear base de datos (puedes cambiar el nombre si quieres)
CREATE DATABASE IF NOT EXISTS fototeca
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE fototeca;

-- Tabla de álbumes
CREATE TABLE IF NOT EXISTS album (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de fotos
CREATE TABLE IF NOT EXISTS fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_guardado VARCHAR(255) NOT NULL,
    ruta TEXT NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    tamano INT UNSIGNED,
    tipo_mime VARCHAR(100),
    id_album INT DEFAULT NULL,
    CONSTRAINT fk_album FOREIGN KEY (id_album) REFERENCES album(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Crear un álbum
INSERT INTO album (nombre, descripcion)
VALUES ('Gatos', 'Fotos de mi gato adoptado');

-- Insertar una foto en ese álbum
INSERT INTO fotos (nombre_original, nombre_guardado, ruta, tamano, tipo_mime, id_album)
VALUES ('gato.jpg', 'gato_2025.jpg', 'media/gato_2025.jpg', 204800, 'image/jpeg', 1);

