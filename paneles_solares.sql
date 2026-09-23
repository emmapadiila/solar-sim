-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2025 a las 18:58:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `paneles_solares`
--

CREATE DATABASE IF NOT EXISTS `paneles_solares` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `paneles_solares`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_detalle_historial`
--

CREATE TABLE `tbl_detalle_historial` (
  `id_detalle` bigint(20) NOT NULL,
  `fecha_accion` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo_accion` enum('creacion','consulta','exportacion','modificacion') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_historialFK` bigint(20) NOT NULL,
  `id_simulacionFK` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_historial`
--

CREATE TABLE `tbl_historial` (
  `id_historial` bigint(20) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `total_simulaciones` int(11) NOT NULL DEFAULT 0,
  `id_usuarioFK` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_resultados`
--

CREATE TABLE `tbl_resultados` (
  `id_resultado` bigint(20) NOT NULL,
  `energia_generada` decimal(10,2) NOT NULL COMMENT 'kWh/mes',
  `ahorro_mensual` decimal(10,2) NOT NULL COMMENT 'en pesos',
  `ahorro_anual` decimal(10,2) GENERATED ALWAYS AS (`ahorro_mensual` * 12) STORED,
  `retorno_inversion` decimal(5,2) NOT NULL COMMENT 'en años',
  `id_simulacionFK` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_simulacion`
--

CREATE TABLE `tbl_simulacion` (
  `id_simulacion` bigint(20) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `ubicacion` varchar(255) NOT NULL,
  `estrato` int(11) NOT NULL CHECK (`estrato` between 1 and 6),
  `area_disponible` decimal(10,2) NOT NULL COMMENT 'en m²',
  `consumo_mensual` decimal(10,2) NOT NULL COMMENT 'en kWh',
  `tipo_energia` enum('convencional','renovable','mixta') NOT NULL,
  `id_usuarioFK` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuarios`
--

CREATE TABLE `tbl_usuarios` (
  `id_usuario` bigint(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_detalle_historial`
--
ALTER TABLE `tbl_detalle_historial`
  ADD PRIMARY KEY (`id_detalle`),
  ADD UNIQUE KEY `id_detalle` (`id_detalle`),
  ADD KEY `id_historialFK` (`id_historialFK`),
  ADD KEY `id_simulacionFK` (`id_simulacionFK`);

--
-- Indices de la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD UNIQUE KEY `id_historial` (`id_historial`),
  ADD UNIQUE KEY `id_usuarioFK` (`id_usuarioFK`);

--
-- Indices de la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  ADD PRIMARY KEY (`id_resultado`),
  ADD UNIQUE KEY `id_resultado` (`id_resultado`),
  ADD UNIQUE KEY `id_simulacionFK` (`id_simulacionFK`);

--
-- Indices de la tabla `tbl_simulacion`
--
ALTER TABLE `tbl_simulacion`
  ADD PRIMARY KEY (`id_simulacion`),
  ADD UNIQUE KEY `id_simulacion` (`id_simulacion`),
  ADD KEY `id_usuarioFK` (`id_usuarioFK`);

--
-- Indices de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD UNIQUE KEY `nombre_unico` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_detalle_historial`
--
ALTER TABLE `tbl_detalle_historial`
  MODIFY `id_detalle` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  MODIFY `id_historial` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  MODIFY `id_resultado` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_simulacion`
--
ALTER TABLE `tbl_simulacion`
  MODIFY `id_simulacion` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tbl_usuarios`
--
ALTER TABLE `tbl_usuarios`
  MODIFY `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_detalle_historial`
--
ALTER TABLE `tbl_detalle_historial`
  ADD CONSTRAINT `tbl_detalle_historial_ibfk_1` FOREIGN KEY (`id_historialFK`) REFERENCES `tbl_historial` (`id_historial`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_detalle_historial_ibfk_2` FOREIGN KEY (`id_simulacionFK`) REFERENCES `tbl_simulacion` (`id_simulacion`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  ADD CONSTRAINT `tbl_historial_ibfk_1` FOREIGN KEY (`id_usuarioFK`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  ADD CONSTRAINT `tbl_resultados_ibfk_1` FOREIGN KEY (`id_simulacionFK`) REFERENCES `tbl_simulacion` (`id_simulacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_simulacion`
--
ALTER TABLE `tbl_simulacion`
  ADD CONSTRAINT `tbl_simulacion_ibfk_1` FOREIGN KEY (`id_usuarioFK`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Datos iniciales: Usuarios fijos
--
INSERT INTO tbl_usuarios (nombre, contrasena, direccion, edad, rol) VALUES
('Emma', 'emma123', 'carrera 31', 30, 'admin'),
('Breiner', 'breiner123', 'calle 50', 25, 'usuario'),
('Juan', 'juan123', 'calle 40 31', 28, 'usuario');

--
-- Triggers
--

-- Trigger para prevenir la eliminación de usuarios fijos
DELIMITER //
CREATE TRIGGER prevent_fixed_users_deletion
BEFORE DELETE ON tbl_usuarios
FOR EACH ROW
BEGIN
    IF (OLD.nombre = 'Emma' AND OLD.rol = 'admin') OR
       (OLD.nombre = 'Breiner' AND OLD.rol = 'usuario') OR
       (OLD.nombre = 'Juan' AND OLD.rol = 'usuario') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede eliminar un usuario fijo del sistema';
    END IF;
END//
DELIMITER ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DELIMITER //

CREATE PROCEDURE `registrar_usuario` (
    IN `p_nombre` VARCHAR(50), 
    IN `p_contrasena` VARCHAR(255), 
    IN `p_direccion` VARCHAR(255), 
    IN `p_edad` INT, 
    IN `p_rol` ENUM('admin','usuario')
) 
BEGIN
    INSERT INTO tbl_usuarios (nombre, contrasena, direccion, edad, rol)
    VALUES (p_nombre, p_contrasena, p_direccion, p_edad, p_rol);
END //

DELIMITER ;
