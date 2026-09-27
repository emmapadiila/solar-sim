-- Migración 001: mensajes del formulario de contacto.
-- Para bases creadas antes de este cambio. Importar dentro de la base de datos (phpMyAdmin > Importar).
-- Los archivos paneles_solares.sql y paneles_solares_hosting.sql ya incluyen esta tabla.

CREATE TABLE IF NOT EXISTS `tbl_mensajes_contacto` (
  `id_mensaje` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `id_usuarioFK` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`),
  KEY `id_usuarioFK` (`id_usuarioFK`),
  CONSTRAINT `tbl_mensajes_contacto_ibfk_1` FOREIGN KEY (`id_usuarioFK`) REFERENCES `tbl_usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
