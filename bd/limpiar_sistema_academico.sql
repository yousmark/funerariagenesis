-- Script para limpiar la base de datos eliminando todo lo relacionado con el sistema académico
-- Este script elimina las tablas y permisos del sistema académico original

-- Desactivar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas del sistema académico (en el orden correcto para evitar problemas de claves foráneas)

-- Primero eliminar la tabla que tiene más dependencias
DROP TABLE IF EXISTS `inscripcion`;

-- Eliminar tabla curso (tiene dependencias de materia y docente)
DROP TABLE IF EXISTS `curso`;

-- Eliminar tablas básicas del sistema académico
DROP TABLE IF EXISTS `estudiante`;
DROP TABLE IF EXISTS `docente`;
DROP TABLE IF EXISTS `materia`;
DROP TABLE IF EXISTS `carrera`;
DROP TABLE IF EXISTS `grado`;
DROP TABLE IF EXISTS `aula`;
DROP TABLE IF EXISTS `turno`;

-- Eliminar permisos académicos de la tabla usuario_permiso primero
-- Estos son los ID de permisos académicos que debemos eliminar
-- (estudiantes=2, docentes=3, academico=4, cursos=5, inscripcion=6, notas=7, consultac=9, consultav=10)
DELETE FROM `usuario_permiso` WHERE `idpermiso` IN (2, 3, 4, 5, 6, 7, 9, 10);

-- Eliminar permisos del sistema académico de la tabla permiso
-- Estos son los permisos que no se usan en el sistema funerario
DELETE FROM `permiso` WHERE `nombre` IN ('estudiantes', 'docentes', 'academico', 'cursos', 'inscripcion', 'notas', 'consultac', 'consultav');

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- Mensaje de confirmación
SELECT 'Limpieza completada. Se eliminaron todas las tablas y permisos del sistema académico.' AS Mensaje;
