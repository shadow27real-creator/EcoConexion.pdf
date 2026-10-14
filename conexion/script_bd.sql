CREATE DATABASE IF NOT EXISTS ecoconexion
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ecoconexion;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    tipo_usuario VARCHAR(20) NOT NULL DEFAULT 'generador',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS materiales (
    id_material INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS publicaciones (
    id_publicacion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    cantidad VARCHAR(80) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'disponible',
    fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_material INT UNSIGNED NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    CONSTRAINT fk_pub_materiales FOREIGN KEY (id_material) REFERENCES materiales(id_material) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pub_usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT IGNORE INTO materiales (nombre) VALUES 
('Cartón'), ('Plástico'), ('Vidrio'), ('Metal'), ('Papel');