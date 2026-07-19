-- ===================================
-- BASE DE DATOS: IED MIGUEL SAMPER
-- ===================================

CREATE DATABASE IF NOT EXISTS `ied_miguel_samper` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `ied_miguel_samper`;

-- ===================================
-- TABLA: ROLES
-- ===================================
CREATE TABLE `roles` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: USUARIOS
-- ===================================
CREATE TABLE `usuarios` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `telefono` VARCHAR(20),
  `cedula` VARCHAR(20) UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol_id` INT NOT NULL,
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `avatar` VARCHAR(255),
  `recordar_token` VARCHAR(255),
  `token_expira` DATETIME,
  `ultimo_acceso` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_email` (`email`),
  INDEX `idx_cedula` (`cedula`),
  INDEX `idx_rol_id` (`rol_id`),
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: ESTUDIANTES
-- ===================================
CREATE TABLE `estudiantes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `numero_documento` VARCHAR(20) NOT NULL UNIQUE,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `genero` ENUM('masculino', 'femenino', 'otro') NOT NULL,
  `direccion` VARCHAR(255),
  `telefono` VARCHAR(20),
  `email` VARCHAR(100),
  `nombre_acudiente` VARCHAR(100),
  `telefono_acudiente` VARCHAR(20),
  `grado` VARCHAR(50),
  `jornada` ENUM('mañana', 'tarde', 'noche') DEFAULT 'mañana',
  `estado` ENUM('activo', 'inactivo', 'graduado') DEFAULT 'activo',
  `foto` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_numero_documento` (`numero_documento`),
  INDEX `idx_grado` (`grado`),
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: MATRICULAS
-- ===================================
CREATE TABLE `matriculas` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `estudiante_id` INT NOT NULL,
  `grado` VARCHAR(50) NOT NULL,
  `jornada` ENUM('mañana', 'tarde', 'noche') DEFAULT 'mañana',
  `año_academico` INT NOT NULL,
  `estado` ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
  `observaciones` TEXT,
  `fecha_solicitud` DATE NOT NULL,
  `fecha_aprobacion` DATE,
  `aprobado_por` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_estudiante_id` (`estudiante_id`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_año_academico` (`año_academico`),
  INDEX `idx_grado` (`grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: NOTICIAS
-- ===================================
CREATE TABLE `noticias` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `contenido` LONGTEXT,
  `autor_id` INT NOT NULL,
  `imagen` VARCHAR(255),
  `estado` ENUM('borrador', 'publicado', 'archivado') DEFAULT 'borrador',
  `fecha_publicacion` DATE,
  `destacado` BOOLEAN DEFAULT FALSE,
  `vistas` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_fecha_publicacion` (`fecha_publicacion`),
  INDEX `idx_autor_id` (`autor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: GALERIA
-- ===================================
CREATE TABLE `galeria` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `imagen` VARCHAR(255) NOT NULL,
  `categoria` VARCHAR(100),
  `autor_id` INT NOT NULL,
  `estado` ENUM('activo', 'inactivo') DEFAULT 'activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_categoria` (`categoria`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_autor_id` (`autor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: CONTACTOS
-- ===================================
CREATE TABLE `contactos` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20),
  `asunto` VARCHAR(255) NOT NULL,
  `mensaje` TEXT NOT NULL,
  `leido` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_leido` (`leido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- TABLA: LOGS
-- ===================================
CREATE TABLE `logs` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `usuario_id` INT,
  `accion` VARCHAR(255) NOT NULL,
  `tabla` VARCHAR(100),
  `registro_id` INT,
  `detalles` JSON,
  `ip_address` VARCHAR(45),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_usuario_id` (`usuario_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================
-- INSERTAR ROLES
-- ===================================
INSERT INTO `roles` (`nombre`, `descripcion`) VALUES
('Administrador', 'Acceso total al sistema'),
('Rector', 'Acceso a dashboard, matrículas y estudiantes'),
('Coordinador', 'Acceso a noticias, estudiantes y galería'),
('Secretaría', 'Acceso a matrículas y estudiantes'),
('Docente', 'Acceso a consultar estudiantes y noticias');

-- ===================================
-- INSERTAR USUARIO ADMINISTRADOR
-- ===================================
INSERT INTO `usuarios` (`nombre`, `apellido`, `email`, `cedula`, `password`, `rol_id`, `estado`) VALUES
('Admin', 'Sistema', 'admin@iedmiguelsamper.edu.co', '1000000001', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DXo4zO', 1, 'activo');

-- Contraseña: password
