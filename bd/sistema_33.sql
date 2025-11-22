-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-08-2025 a las 01:45:38
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema-33`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula`
--

CREATE TABLE `aula` (
  `idaula` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `aula`
--

INSERT INTO `aula` (`idaula`, `nombre`, `condicion`) VALUES
(1, 'Laboratorio 1', 1),
(2, 'Aula 1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

CREATE TABLE `carrera` (
  `idcarrera` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `carrera`
--

INSERT INTO `carrera` (`idcarrera`, `nombre`, `condicion`) VALUES
(1, 'Sistemas Informáticos', 1),
(2, 'Contaduria General', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `idcurso` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `idmateria` int(11) NOT NULL,
  `iddocente` int(11) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docente`
--

CREATE TABLE `docente` (
  `iddocente` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `apellido` varchar(70) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `celular` bigint(20) NOT NULL,
  `correo` varchar(70) NOT NULL,
  `nivel_est` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `docente`
--

INSERT INTO `docente` (`iddocente`, `nombre`, `apellido`, `cedula`, `direccion`, `celular`, `correo`, `nivel_est`, `condicion`) VALUES
(1, 'Raul', 'Nina Zegarra', '7894125', 'Av. Riobamba, Nro. 1', 77779580, 'raul@gmail.com', 'Licenciatura', 1),
(2, 'Victor Eduardo', 'Ramos Zegarra', '5632148', 'Av. Chacaltaya, Nro. 1258', 75881281, 'victor@gmail.com', 'Licenciatura', 1),
(3, 'Carolina', 'Mendoza', '45236452', 'z/achachicala, c/Ramos Gavilan', 54632189, 'carolina@gmail.com', 'Maestria', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `idestudiante` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `apellido` varchar(70) NOT NULL,
  `edad` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `direccion` varchar(70) NOT NULL,
  `celular` bigint(20) NOT NULL,
  `correo` varchar(70) NOT NULL,
  `fecha_nac` date NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estudiante`
--

INSERT INTO `estudiante` (`idestudiante`, `nombre`, `apellido`, `edad`, `cedula`, `direccion`, `celular`, `correo`, `fecha_nac`, `condicion`) VALUES
(1, 'Roger', 'Gay', 22, '4523156', 'c/laja, nro. 234', 71589456, 'roger@gmail.com', '2005-11-03', 1),
(2, 'Maria', 'Vera', 24, '564234', 'Av. Entre rios, nro. 236', 7824563, 'ana@gmail.com', '2001-02-25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado`
--

CREATE TABLE `grado` (
  `idgrado` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `grado`
--

INSERT INTO `grado` (`idgrado`, `nombre`, `condicion`) VALUES
(1, 'Primer Año', 1),
(2, 'Segundo Año', 1),
(3, 'Tercer Año', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion`
--

CREATE TABLE `inscripcion` (
  `idinscripcion` int(11) NOT NULL,
  `idestudiante` int(11) NOT NULL,
  `idcarrera` int(11) NOT NULL,
  `idgrado` int(11) NOT NULL,
  `idcurso` int(11) NOT NULL,
  `idturno` int(11) NOT NULL,
  `idaula` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha_ins` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materia`
--

CREATE TABLE `materia` (
  `idmateria` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `materia`
--

INSERT INTO `materia` (`idmateria`, `nombre`, `condicion`) VALUES
(1, 'Redes de Computadoras I', 1),
(2, 'Programación 1', 1),
(3, 'Programación en Internet', 1),
(4, 'Hardware de Computadoras', 0),
(5, 'Ingenieria de Software', 0),
(6, 'Redes de Computadoras II', 0),
(7, 'Hardware de Computadoras I', 0),
(8, 'Taller', 0),
(9, 'Hardware de Computadoras II', 0),
(10, 'web', 1),
(11, 'web 2', 1),
(12, 'Taller de Grado', 1),
(13, 'Hardware de Computadoras', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `idpermiso` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`idpermiso`, `nombre`) VALUES
(1, 'escritorio'),
(2, 'estudiantes'),
(3, 'docentes'),
(4, 'academico'),
(5, 'cursos'),
(6, 'inscripcion'),
(7, 'notas'),
(8, 'acceso'),
(9, 'consultac'),
(10, 'consultav'),
(11, 'cotizaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `idturno` int(11) NOT NULL,
  `nombre` varchar(70) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`idturno`, `nombre`, `condicion`) VALUES
(1, 'Mañana', 1),
(2, 'Noche', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `celula` varchar(20) NOT NULL,
  `direccion` varchar(70) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `cargo` varchar(20) NOT NULL,
  `login` varchar(20) NOT NULL,
  `clave` varchar(64) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `condicion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `celula`, `direccion`, `celular`, `email`, `cargo`, `login`, `clave`, `imagen`, `condicion`) VALUES
(1, 'Yerko Luna', '7099469', 'Nuevos Horizontes', '74086740', 'yerko@gmail.com', 'Administrador', 'yerkis', '314706067b19fe2b37c15e86c86700b1c7150e44f7c65d88cfade138b587e1ed', '1753736911.jpeg', 1),
(4, 'Maria Vera', '15030578', 'Villa Adela', '78947949', 'maria@gmail.com', 'Estudiante', 'majito', '46dfef5592848544f3ff9b3d048ff34bac31a2d8da479267f8eee3e0ed60287d', '1753818544.png', 1),
(5, 'Dolly Vera', '4567890', 'VILLA ESPERANZA', '72756789', 'dolly@gmail.com', 'Docente', 'dolly', 'cf9b9e85b99a49d2f1eb0d981825c64f069daf0a60c5fc19f8745bf5e3d2909e', '1753820583.jpg', 1),
(6, 'Lex Luthor', '6667890', 'Nuevos Horizontes', '78947949', 'mamamni@gmail.com', 'Estudiante', 'mamani', '4b7adffc7ea1436ef16fca18e85d62b052f05309ff6649f984a4201943b0ad2c', '1753835426.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_permiso`
--

CREATE TABLE `usuario_permiso` (
  `idusuario_permiso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idpermiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario_permiso`
--

INSERT INTO `usuario_permiso` (`idusuario_permiso`, `idusuario`, `idpermiso`) VALUES
(50, 1, 1),
(51, 1, 2),
(52, 1, 3),
(53, 1, 4),
(54, 1, 5),
(55, 1, 6),
(56, 1, 7),
(57, 1, 8),
(58, 1, 9),
(59, 1, 10),
(63, 5, 3),
(64, 5, 4),
(65, 4, 2),
(72, 6, 4);

-- Otorgar permiso de cotizaciones al administrador (idusuario=1)
INSERT INTO `usuario_permiso` (`idusuario`, `idpermiso`) VALUES (1, 11);

-- Otorgar permiso de cotizaciones al usuario 7
INSERT INTO `usuario_permiso` (`idusuario`, `idpermiso`) VALUES (7, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion`
--

CREATE TABLE `cotizacion` (
  `idcotizacion` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `nombre_familia` varchar(120) NOT NULL,
  `contacto_email` varchar(120) DEFAULT NULL,
  `contacto_celular` varchar(30) DEFAULT NULL,
  `detalle_json` mediumtext NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `ruta_pdf` varchar(255) NOT NULL,
  `hash_sha256` varchar(64) NOT NULL,
  `firma_nombre` varchar(120) NOT NULL,
  `firma_fecha` datetime NOT NULL,
  `firma_ip` varchar(45) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'vigente',
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_acceso`
--

CREATE TABLE `cotizacion_acceso` (
  `idacceso` int(11) NOT NULL,
  `idcotizacion` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expira_en` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`idaula`);

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`idcarrera`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`idcurso`),
  ADD KEY `idmateria` (`idmateria`),
  ADD KEY `id_docente` (`iddocente`);

--
-- Indices de la tabla `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`iddocente`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`idestudiante`);

--
-- Indices de la tabla `grado`
--
ALTER TABLE `grado`
  ADD PRIMARY KEY (`idgrado`);

--
-- Indices de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD PRIMARY KEY (`idinscripcion`),
  ADD KEY `idestudiante` (`idestudiante`),
  ADD KEY `idgrado` (`idgrado`),
  ADD KEY `idcurso` (`idcurso`),
  ADD KEY `idturno` (`idturno`),
  ADD KEY `idaula` (`idaula`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idcarrera` (`idcarrera`);

--
-- Indices de la tabla `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`idmateria`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`idpermiso`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`idturno`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- Indices de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD PRIMARY KEY (`idusuario_permiso`),
  ADD KEY `idusuario` (`idusuario`),
  ADD KEY `idpermiso` (`idpermiso`);

-- Indices de la tabla `cotizacion`
ALTER TABLE `cotizacion`
  ADD PRIMARY KEY (`idcotizacion`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idusuario` (`idusuario`);

-- Indices de la tabla `cotizacion_acceso`
ALTER TABLE `cotizacion_acceso`
  ADD PRIMARY KEY (`idacceso`),
  ADD KEY `idcotizacion` (`idcotizacion`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aula`
--
ALTER TABLE `aula`
  MODIFY `idaula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `idcarrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `idcurso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `docente`
--
ALTER TABLE `docente`
  MODIFY `iddocente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `idestudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `grado`
--
ALTER TABLE `grado`
  MODIFY `idgrado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `idinscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materia`
--
ALTER TABLE `materia`
  MODIFY `idmateria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `idturno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  MODIFY `idusuario_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

-- AUTO_INCREMENT de la tabla `cotizacion`
ALTER TABLE `cotizacion`
  MODIFY `idcotizacion` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT de la tabla `cotizacion_acceso`
ALTER TABLE `cotizacion_acceso`
  MODIFY `idacceso` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`idmateria`) REFERENCES `materia` (`idmateria`);

--
-- Filtros para la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD CONSTRAINT `inscripcion_ibfk_1` FOREIGN KEY (`idturno`) REFERENCES `turno` (`idturno`),
  ADD CONSTRAINT `inscripcion_ibfk_2` FOREIGN KEY (`idestudiante`) REFERENCES `estudiante` (`idestudiante`),
  ADD CONSTRAINT `inscripcion_ibfk_3` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `inscripcion_ibfk_4` FOREIGN KEY (`idgrado`) REFERENCES `grado` (`idgrado`),
  ADD CONSTRAINT `inscripcion_ibfk_5` FOREIGN KEY (`idaula`) REFERENCES `aula` (`idaula`),
  ADD CONSTRAINT `inscripcion_ibfk_6` FOREIGN KEY (`idcarrera`) REFERENCES `carrera` (`idcarrera`),
  ADD CONSTRAINT `inscripcion_ibfk_7` FOREIGN KEY (`idcurso`) REFERENCES `curso` (`idcurso`);

--
-- Filtros para la tabla `usuario_permiso`
--
ALTER TABLE `usuario_permiso`
  ADD CONSTRAINT `usuario_permiso_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`),
  ADD CONSTRAINT `usuario_permiso_ibfk_2` FOREIGN KEY (`idpermiso`) REFERENCES `permiso` (`idpermiso`);

-- Filtros para la tabla `cotizacion`
ALTER TABLE `cotizacion`
  ADD CONSTRAINT `cotizacion_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuario` (`idusuario`);

-- Filtros para la tabla `cotizacion_acceso`
ALTER TABLE `cotizacion_acceso`
  ADD CONSTRAINT `cotizacion_acceso_ibfk_1` FOREIGN KEY (`idcotizacion`) REFERENCES `cotizacion` (`idcotizacion`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
