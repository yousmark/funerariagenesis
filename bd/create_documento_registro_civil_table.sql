CREATE TABLE `documento_registro_civil` (
  `iddocumento` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL UNIQUE,
  `idcontrato` int(11) DEFAULT NULL,
  `idcotizacion` int(11) DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `nombre_difunto` varchar(120) NOT NULL,
  `cedula_difunto` varchar(20) NOT NULL,
  `ruta_cedula_difunto` varchar(255) NOT NULL,
  `nombre_responsable` varchar(120) NOT NULL,
  `cedula_responsable` varchar(20) NOT NULL,
  `ruta_cedula_responsable` varchar(255) NOT NULL,
  `telefono_responsable` varchar(30) DEFAULT NULL,
  `direccion_responsable` varchar(255) DEFAULT NULL,
  `testigo1_nombre` varchar(120) DEFAULT NULL,
  `testigo1_cedula` varchar(20) DEFAULT NULL,
  `testigo1_ruta_cedula` varchar(255) DEFAULT NULL,
  `testigo2_nombre` varchar(120) DEFAULT NULL,
  `testigo2_cedula` varchar(20) DEFAULT NULL,
  `testigo2_ruta_cedula` varchar(255) DEFAULT NULL,
  `testigo3_nombre` varchar(120) DEFAULT NULL,
  `testigo3_cedula` varchar(20) DEFAULT NULL,
  `testigo3_ruta_cedula` varchar(255) DEFAULT NULL,
  `documentos_completos` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`iddocumento`),
  FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  FOREIGN KEY (`idcontrato`) REFERENCES `contrato` (`idcontrato`) ON DELETE SET NULL,
  FOREIGN KEY (`idcotizacion`) REFERENCES `cotizacion` (`idcotizacion`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;








