-- Tabla para permisos de visualización de la ficha de recojo
CREATE TABLE IF NOT EXISTS `ficha_recojo_permiso` (
  `idpermiso` int(11) NOT NULL AUTO_INCREMENT,
  `idficha` int(11) NOT NULL,
  `idusuario_permiso` int(11) NOT NULL,
  `otorgado_por` int(11) NOT NULL,
  `fecha_otorgado` datetime NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`idpermiso`),
  KEY `idficha` (`idficha`),
  KEY `idusuario_permiso` (`idusuario_permiso`),
  KEY `otorgado_por` (`otorgado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `ficha_recojo_permiso`
  ADD CONSTRAINT `ficha_recojo_permiso_ibfk_1` FOREIGN KEY (`idficha`) REFERENCES `ficha_recojo` (`idficha`) ON DELETE CASCADE,
  ADD CONSTRAINT `ficha_recojo_permiso_ibfk_2` FOREIGN KEY (`idusuario_permiso`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `ficha_recojo_permiso_ibfk_3` FOREIGN KEY (`otorgado_por`) REFERENCES `usuario` (`idusuario`);








