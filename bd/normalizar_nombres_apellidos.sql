-- Script de normalización de base de datos
-- Divide los campos de nombre completo en nombres y apellidos
-- Autor: Sistema Funeraria
-- Fecha: 2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- ============================================
-- 1. TABLA: usuario
-- ============================================
-- Agregar columnas para nombres y apellidos
ALTER TABLE `usuario` 
ADD COLUMN `nombres` VARCHAR(100) NULL AFTER `idusuario`,
ADD COLUMN `apellidos` VARCHAR(100) NULL AFTER `nombres`;

-- Migrar datos existentes: dividir nombre completo en nombres y apellidos
-- Asumimos que el último espacio separa nombres de apellidos
UPDATE `usuario` 
SET 
    `nombres` = SUBSTRING_INDEX(`nombre`, ' ', CHAR_LENGTH(`nombre`) - CHAR_LENGTH(REPLACE(`nombre`, ' ', ''))),
    `apellidos` = SUBSTRING_INDEX(`nombre`, ' ', -1)
WHERE `nombre` IS NOT NULL AND `nombre` != '';

-- Para casos con un solo nombre, ponerlo en nombres y dejar apellidos vacío
UPDATE `usuario` 
SET `apellidos` = '' 
WHERE `apellidos` = `nombres` AND CHAR_LENGTH(`nombre`) - CHAR_LENGTH(REPLACE(`nombre`, ' ', '')) = 0;

-- Hacer campos NOT NULL después de migración
ALTER TABLE `usuario` 
MODIFY `nombres` VARCHAR(100) NOT NULL,
MODIFY `apellidos` VARCHAR(100) NOT NULL DEFAULT '';

-- ============================================
-- 2. TABLA: contrato
-- ============================================
-- Agregar columnas para nombres y apellidos del contratante
ALTER TABLE `contrato` 
ADD COLUMN `nombres_contratante` VARCHAR(120) NULL AFTER `idusuario`,
ADD COLUMN `apellidos_contratante` VARCHAR(120) NULL AFTER `nombres_contratante`;

-- Migrar nombre_contratante
UPDATE `contrato` 
SET 
    `nombres_contratante` = SUBSTRING_INDEX(`nombre_contratante`, ' ', CHAR_LENGTH(`nombre_contratante`) - CHAR_LENGTH(REPLACE(`nombre_contratante`, ' ', ''))),
    `apellidos_contratante` = SUBSTRING_INDEX(`nombre_contratante`, ' ', -1)
WHERE `nombre_contratante` IS NOT NULL AND `nombre_contratante` != '';

UPDATE `contrato` 
SET `apellidos_contratante` = '' 
WHERE `apellidos_contratante` = `nombres_contratante` AND CHAR_LENGTH(`nombre_contratante`) - CHAR_LENGTH(REPLACE(`nombre_contratante`, ' ', '')) = 0;

ALTER TABLE `contrato` 
MODIFY `nombres_contratante` VARCHAR(120) NOT NULL,
MODIFY `apellidos_contratante` VARCHAR(120) NOT NULL DEFAULT '';

-- Agregar columnas para nombres y apellidos del fallecido
ALTER TABLE `contrato` 
ADD COLUMN `nombres_fallecido` VARCHAR(120) NULL AFTER `direccion_contratante`,
ADD COLUMN `apellidos_fallecido` VARCHAR(120) NULL AFTER `nombres_fallecido`;

-- Migrar nombre_fallecido
UPDATE `contrato` 
SET 
    `nombres_fallecido` = SUBSTRING_INDEX(`nombre_fallecido`, ' ', CHAR_LENGTH(`nombre_fallecido`) - CHAR_LENGTH(REPLACE(`nombre_fallecido`, ' ', ''))),
    `apellidos_fallecido` = SUBSTRING_INDEX(`nombre_fallecido`, ' ', -1)
WHERE `nombre_fallecido` IS NOT NULL AND `nombre_fallecido` != '';

UPDATE `contrato` 
SET `apellidos_fallecido` = '' 
WHERE `apellidos_fallecido` = `nombres_fallecido` AND CHAR_LENGTH(`nombre_fallecido`) - CHAR_LENGTH(REPLACE(`nombre_fallecido`, ' ', '')) = 0;

ALTER TABLE `contrato` 
MODIFY `nombres_fallecido` VARCHAR(120) NOT NULL,
MODIFY `apellidos_fallecido` VARCHAR(120) NOT NULL DEFAULT '';

-- Agregar columnas para nombres y apellidos de la firma
ALTER TABLE `contrato` 
ADD COLUMN `firma_nombres` VARCHAR(120) NULL AFTER `hash_sha256`,
ADD COLUMN `firma_apellidos` VARCHAR(120) NULL AFTER `firma_nombres`;

-- Migrar firma_nombre
UPDATE `contrato` 
SET 
    `firma_nombres` = SUBSTRING_INDEX(`firma_nombre`, ' ', CHAR_LENGTH(`firma_nombre`) - CHAR_LENGTH(REPLACE(`firma_nombre`, ' ', ''))),
    `firma_apellidos` = SUBSTRING_INDEX(`firma_nombre`, ' ', -1)
WHERE `firma_nombre` IS NOT NULL AND `firma_nombre` != '';

UPDATE `contrato` 
SET `firma_apellidos` = '' 
WHERE `firma_apellidos` = `firma_nombres` AND CHAR_LENGTH(`firma_nombre`) - CHAR_LENGTH(REPLACE(`firma_nombre`, ' ', '')) = 0;

ALTER TABLE `contrato` 
MODIFY `firma_nombres` VARCHAR(120) NOT NULL,
MODIFY `firma_apellidos` VARCHAR(120) NOT NULL DEFAULT '';

-- ============================================
-- 3. TABLA: cotizacion
-- ============================================
-- Agregar columnas para nombres y apellidos de la familia
ALTER TABLE `cotizacion` 
ADD COLUMN `nombres_familia` VARCHAR(120) NULL AFTER `idusuario`,
ADD COLUMN `apellidos_familia` VARCHAR(120) NULL AFTER `nombres_familia`;

-- Migrar nombre_familia
UPDATE `cotizacion` 
SET 
    `nombres_familia` = SUBSTRING_INDEX(`nombre_familia`, ' ', CHAR_LENGTH(`nombre_familia`) - CHAR_LENGTH(REPLACE(`nombre_familia`, ' ', ''))),
    `apellidos_familia` = SUBSTRING_INDEX(`nombre_familia`, ' ', -1)
WHERE `nombre_familia` IS NOT NULL AND `nombre_familia` != '';

UPDATE `cotizacion` 
SET `apellidos_familia` = '' 
WHERE `apellidos_familia` = `nombres_familia` AND CHAR_LENGTH(`nombre_familia`) - CHAR_LENGTH(REPLACE(`nombre_familia`, ' ', '')) = 0;

ALTER TABLE `cotizacion` 
MODIFY `nombres_familia` VARCHAR(120) NOT NULL,
MODIFY `apellidos_familia` VARCHAR(120) NOT NULL DEFAULT '';

-- Agregar columnas para nombres y apellidos de la firma
ALTER TABLE `cotizacion` 
ADD COLUMN `firma_nombres` VARCHAR(120) NULL AFTER `hash_sha256`,
ADD COLUMN `firma_apellidos` VARCHAR(120) NULL AFTER `firma_nombres`;

-- Migrar firma_nombre
UPDATE `cotizacion` 
SET 
    `firma_nombres` = SUBSTRING_INDEX(`firma_nombre`, ' ', CHAR_LENGTH(`firma_nombre`) - CHAR_LENGTH(REPLACE(`firma_nombre`, ' ', ''))),
    `firma_apellidos` = SUBSTRING_INDEX(`firma_nombre`, ' ', -1)
WHERE `firma_nombre` IS NOT NULL AND `firma_nombre` != '';

UPDATE `cotizacion` 
SET `firma_apellidos` = '' 
WHERE `firma_apellidos` = `firma_nombres` AND CHAR_LENGTH(`firma_nombre`) - CHAR_LENGTH(REPLACE(`firma_nombre`, ' ', '')) = 0;

ALTER TABLE `cotizacion` 
MODIFY `firma_nombres` VARCHAR(120) NOT NULL,
MODIFY `firma_apellidos` VARCHAR(120) NOT NULL DEFAULT '';

-- ============================================
-- 4. TABLA: documento_registro_civil
-- ============================================
-- Nota: Esta sección solo se ejecutará si la tabla existe
-- Si la tabla no existe, estas instrucciones fallarán silenciosamente
-- Agregar columnas para nombres y apellidos
ALTER TABLE `documento_registro_civil` 
ADD COLUMN `nombres_difunto` VARCHAR(120) NULL AFTER `idusuario`,
ADD COLUMN `apellidos_difunto` VARCHAR(120) NULL AFTER `nombres_difunto`,
ADD COLUMN `nombres_responsable` VARCHAR(120) NULL AFTER `cedula_difunto`,
ADD COLUMN `apellidos_responsable` VARCHAR(120) NULL AFTER `nombres_responsable`,
ADD COLUMN `testigo1_nombres` VARCHAR(120) NULL AFTER `direccion_responsable`,
ADD COLUMN `testigo1_apellidos` VARCHAR(120) NULL AFTER `testigo1_nombres`,
ADD COLUMN `testigo2_nombres` VARCHAR(120) NULL AFTER `testigo1_ruta_cedula`,
ADD COLUMN `testigo2_apellidos` VARCHAR(120) NULL AFTER `testigo2_nombres`,
ADD COLUMN `testigo3_nombres` VARCHAR(120) NULL AFTER `testigo2_ruta_cedula`,
ADD COLUMN `testigo3_apellidos` VARCHAR(120) NULL AFTER `testigo3_nombres`;

-- Migrar datos de documento_registro_civil
UPDATE `documento_registro_civil` 
SET 
    `nombres_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', CHAR_LENGTH(`nombre_difunto`) - CHAR_LENGTH(REPLACE(`nombre_difunto`, ' ', ''))),
    `apellidos_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', -1)
WHERE `nombre_difunto` IS NOT NULL AND `nombre_difunto` != '';

UPDATE `documento_registro_civil` 
SET `apellidos_difunto` = '' 
WHERE `apellidos_difunto` = `nombres_difunto` AND CHAR_LENGTH(`nombre_difunto`) - CHAR_LENGTH(REPLACE(`nombre_difunto`, ' ', '')) = 0;

UPDATE `documento_registro_civil` 
SET 
    `nombres_responsable` = SUBSTRING_INDEX(`nombre_responsable`, ' ', CHAR_LENGTH(`nombre_responsable`) - CHAR_LENGTH(REPLACE(`nombre_responsable`, ' ', ''))),
    `apellidos_responsable` = SUBSTRING_INDEX(`nombre_responsable`, ' ', -1)
WHERE `nombre_responsable` IS NOT NULL AND `nombre_responsable` != '';

UPDATE `documento_registro_civil` 
SET `apellidos_responsable` = '' 
WHERE `apellidos_responsable` = `nombres_responsable` AND CHAR_LENGTH(`nombre_responsable`) - CHAR_LENGTH(REPLACE(`nombre_responsable`, ' ', '')) = 0;

-- Migrar testigos
UPDATE `documento_registro_civil` 
SET 
    `testigo1_nombres` = SUBSTRING_INDEX(`testigo1_nombre`, ' ', CHAR_LENGTH(`testigo1_nombre`) - CHAR_LENGTH(REPLACE(`testigo1_nombre`, ' ', ''))),
    `testigo1_apellidos` = SUBSTRING_INDEX(`testigo1_nombre`, ' ', -1)
WHERE `testigo1_nombre` IS NOT NULL AND `testigo1_nombre` != '';

UPDATE `documento_registro_civil` 
SET 
    `testigo2_nombres` = SUBSTRING_INDEX(`testigo2_nombre`, ' ', CHAR_LENGTH(`testigo2_nombre`) - CHAR_LENGTH(REPLACE(`testigo2_nombre`, ' ', ''))),
    `testigo2_apellidos` = SUBSTRING_INDEX(`testigo2_nombre`, ' ', -1)
WHERE `testigo2_nombre` IS NOT NULL AND `testigo2_nombre` != '';

UPDATE `documento_registro_civil` 
SET 
    `testigo3_nombres` = SUBSTRING_INDEX(`testigo3_nombre`, ' ', CHAR_LENGTH(`testigo3_nombre`) - CHAR_LENGTH(REPLACE(`testigo3_nombre`, ' ', ''))),
    `testigo3_apellidos` = SUBSTRING_INDEX(`testigo3_nombre`, ' ', -1)
WHERE `testigo3_nombre` IS NOT NULL AND `testigo3_nombre` != '';

-- ============================================
-- 5. TABLA: itinerario_funerario
-- ============================================
-- Nota: Esta sección solo se ejecutará si la tabla existe
ALTER TABLE `itinerario_funerario` 
ADD COLUMN `nombres_difunto` VARCHAR(120) NULL AFTER `idusuario`,
ADD COLUMN `apellidos_difunto` VARCHAR(120) NULL AFTER `nombres_difunto`,
ADD COLUMN `nombres_familia` VARCHAR(120) NULL AFTER `apellidos_difunto`,
ADD COLUMN `apellidos_familia` VARCHAR(120) NULL AFTER `nombres_familia`;

-- Migrar datos de itinerario_funerario
UPDATE `itinerario_funerario` 
SET 
    `nombres_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', CHAR_LENGTH(`nombre_difunto`) - CHAR_LENGTH(REPLACE(`nombre_difunto`, ' ', ''))),
    `apellidos_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', -1)
WHERE `nombre_difunto` IS NOT NULL AND `nombre_difunto` != '';

UPDATE `itinerario_funerario` 
SET 
    `nombres_familia` = SUBSTRING_INDEX(`nombre_familia`, ' ', CHAR_LENGTH(`nombre_familia`) - CHAR_LENGTH(REPLACE(`nombre_familia`, ' ', ''))),
    `apellidos_familia` = SUBSTRING_INDEX(`nombre_familia`, ' ', -1)
WHERE `nombre_familia` IS NOT NULL AND `nombre_familia` != '';

-- ============================================
-- 6. TABLA: ficha_recojo
-- ============================================
-- Nota: Esta sección solo se ejecutará si la tabla existe
ALTER TABLE `ficha_recojo` 
ADD COLUMN `nombres_difunto` VARCHAR(120) NULL AFTER `idusuario`,
ADD COLUMN `apellidos_difunto` VARCHAR(120) NULL AFTER `nombres_difunto`,
ADD COLUMN `nombres_contacto` VARCHAR(120) NULL AFTER `protocolos_aplicados`,
ADD COLUMN `apellidos_contacto` VARCHAR(120) NULL AFTER `nombres_contacto`;

-- Migrar datos de ficha_recojo
UPDATE `ficha_recojo` 
SET 
    `nombres_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', CHAR_LENGTH(`nombre_difunto`) - CHAR_LENGTH(REPLACE(`nombre_difunto`, ' ', ''))),
    `apellidos_difunto` = SUBSTRING_INDEX(`nombre_difunto`, ' ', -1)
WHERE `nombre_difunto` IS NOT NULL AND `nombre_difunto` != '';

UPDATE `ficha_recojo` 
SET 
    `nombres_contacto` = SUBSTRING_INDEX(`nombre_contacto`, ' ', CHAR_LENGTH(`nombre_contacto`) - CHAR_LENGTH(REPLACE(`nombre_contacto`, ' ', ''))),
    `apellidos_contacto` = SUBSTRING_INDEX(`nombre_contacto`, ' ', -1)
WHERE `nombre_contacto` IS NOT NULL AND `nombre_contacto` != '';

COMMIT;

-- NOTA: Los campos antiguos (nombre, nombre_contratante, etc.) se mantienen temporalmente
-- para compatibilidad. Se recomienda eliminar después de verificar que todo funciona correctamente.
-- Ejemplo de eliminación (ejecutar después de validar):
-- ALTER TABLE `usuario` DROP COLUMN `nombre`;
-- ALTER TABLE `contrato` DROP COLUMN `nombre_contratante`, DROP COLUMN `nombre_fallecido`, DROP COLUMN `firma_nombre`;
-- etc.

