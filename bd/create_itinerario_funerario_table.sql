CREATE TABLE IF NOT EXISTS `itinerario_funerario` (
  `iditinerario` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL UNIQUE,
  `idcontrato` int(11) DEFAULT NULL,
  `idcotizacion` int(11) DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `nombre_difunto` varchar(120) NOT NULL,
  `nombre_familia` varchar(120) NOT NULL,
  `telefono_contacto` varchar(30) DEFAULT NULL,
  `fecha_sepelio` date NOT NULL,
  `hora_sepelio` time NOT NULL,
  `lugar_sepelio` varchar(255) NOT NULL,
  `direccion_sepelio` text DEFAULT NULL,
  `ruta_cortejo` text NOT NULL,
  `punto_partida` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `confirmado_familia` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_confirmacion_familia` datetime DEFAULT NULL,
  `ip_confirmacion` varchar(45) DEFAULT NULL,
  `ruta_pdf` varchar(255) NOT NULL,
  `hash_sha256` varchar(64) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`iditinerario`),
  KEY `idusuario` (`idusuario`),
  KEY `idcontrato` (`idcontrato`),
  KEY `idcotizacion` (`idcotizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar claves foráneas después de crear la tabla
ALTER TABLE `itinerario_funerario`
  ADD CONSTRAINT `itinerario_funerario_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `itinerario_funerario_ibfk_2` FOREIGN KEY (`idcontrato`) REFERENCES `contrato` (`idcontrato`) ON DELETE SET NULL,
  ADD CONSTRAINT `itinerario_funerario_ibfk_3` FOREIGN KEY (`idcotizacion`) REFERENCES `cotizacion` (`idcotizacion`) ON DELETE SET NULL;

-- Tabla para permisos de visualización del itinerario (similar a cotizacion_permiso)
CREATE TABLE IF NOT EXISTS `itinerario_permiso` (
  `idpermiso` int(11) NOT NULL AUTO_INCREMENT,
  `iditinerario` int(11) NOT NULL,
  `idusuario_permiso` int(11) NOT NULL,
  `otorgado_por` int(11) NOT NULL,
  `fecha_otorgado` datetime NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`idpermiso`),
  KEY `iditinerario` (`iditinerario`),
  KEY `idusuario_permiso` (`idusuario_permiso`),
  KEY `otorgado_por` (`otorgado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `itinerario_permiso`
  ADD CONSTRAINT `itinerario_permiso_ibfk_1` FOREIGN KEY (`iditinerario`) REFERENCES `itinerario_funerario` (`iditinerario`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerario_permiso_ibfk_2` FOREIGN KEY (`idusuario_permiso`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerario_permiso_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuario` (`idusuario`);








