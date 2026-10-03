-- ============================================================
-- Barbería Premium — Database Schema & Seed Data
-- ============================================================
-- This file defines the complete database structure and includes
-- seed data for testing. Import with:
--   mysql -u root -p barberia < database/init.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS barberia
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE barberia;

-- ------------------------------------------------------------
-- Table: usuarios (Admin / staff accounts)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    tipo      ENUM('admin','staff') DEFAULT 'admin',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: barberos (Barber profiles)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS barberos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    especialidad VARCHAR(200) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: servicio (Services offered)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS servicio (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(150) NOT NULL,
    precio    DECIMAL(10,2) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: reservas (Appointment bookings)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_barbero      INT NOT NULL,
    id_servicio     INT DEFAULT NULL,
    nombre_cliente  VARCHAR(100) NOT NULL,
    telefono_cliente VARCHAR(20) NOT NULL,
    fecha           DATE NOT NULL,
    hora            TIME NOT NULL,
    estado          ENUM('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
    fecha_creacion  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_barbero)  REFERENCES barberos(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicio(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: mensajes (Contact form submissions)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensajes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL,
    mensaje   TEXT NOT NULL,
    fecha     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Table: publicaciones_servicios (Style photo gallery)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS publicaciones_servicios (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    servicio_id INT NOT NULL,
    titulo      VARCHAR(150) NOT NULL,
    imagen      VARCHAR(255) NOT NULL,
    fecha       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicio(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin user (password: admin123)
INSERT INTO usuarios (nombre, email, password, tipo) VALUES
    ('Administrador', 'admin@barberia.com', '$2y$10$92IXUnjNwpO5HKVBcPvVDeO/lkDJqBpWxGqQ5bE/mB7x3F8eQ7yOq', 'admin');

-- Barbers
INSERT INTO barberos (nombre, especialidad) VALUES
    ('Juan Pérez',    'Cortes clásicos y afeitado con navaja'),
    ('Carlos Rodríguez', 'Fades modernos y diseño de barba'),
    ('Luis Fernández', 'Cortes personalizados y estilos vanguardistas');

-- Services
INSERT INTO servicio (nombre, precio, descripcion) VALUES
    ('Corte de Cabello',      25.00, 'Corte clásico o moderno según tu estilo.'),
    ('Afeitado con Navaja',   18.00, 'Afeitado tradicional con navaja de seguridad.'),
    ('Corte y Barba',         35.00, 'Corte de cabello + arreglo de barba.'),
    ('Arreglo de Barba',      15.00, 'Recorte y modelado de barba.'),
    ('Tratamiento Capilar',   30.00, 'Masaje capilar y tratamiento nutritivo.');

-- Sample reservations (for testing)
INSERT INTO reservas (id_barbero, nombre_cliente, telefono_cliente, fecha, hora, estado) VALUES
    (1, 'Pedro González',  '912345678', '2025-06-15', '10:00:00', 'confirmada'),
    (2, 'María López',     '987654321', '2025-06-15', '11:30:00', 'pendiente'),
    (3, 'José Martínez',   '911223344', '2025-06-16', '14:00:00', 'completada');

-- Sample contact messages
INSERT INTO mensajes (nombre, email, mensaje) VALUES
    ('Ana García', 'ana@example.com', '¿Tienen disponibilidad para este fin de semana?');

-- ============================================================
-- End of schema
-- ============================================================
