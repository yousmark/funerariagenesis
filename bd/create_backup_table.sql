-- Tabla para registrar backups de la base de datos
CREATE TABLE IF NOT EXISTS `backup` (
  `idbackup` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `tamaño` bigint(20) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_restauracion` datetime DEFAULT NULL,
  `estado` enum('activo','eliminado','restaurado') DEFAULT 'activo',
  `idusuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idbackup`),
  KEY `idx_fecha` (`fecha_creacion`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla para registrar actualización de contraseñas
ALTER TABLE `usuario` 
ADD COLUMN IF NOT EXISTS `fecha_actualizacion_password` datetime DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `password_hash` varchar(255) DEFAULT NULL;

-- Tabla para registrar sesiones activas
CREATE TABLE IF NOT EXISTS `sesion` (
  `idsesion` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `fecha_inicio` datetime NOT NULL,
  `fecha_ultima_actividad` datetime NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `estado` enum('activa','expirada','cerrada') DEFAULT 'activa',
  PRIMARY KEY (`idsesion`),
  KEY `idx_usuario` (`idusuario`),
  KEY `idx_token` (`token`),
  KEY `idx_estado` (`estado`),
  KEY `idx_expiracion` (`fecha_expiracion`),
  CONSTRAINT `fk_sesion_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;








