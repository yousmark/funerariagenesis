<?php
/**
 * Script para limpiar la base de datos eliminando todo lo relacionado con el sistema académico
 * Este script elimina las tablas y permisos del sistema académico original
 * 
 * IMPORTANTE: Este script es destructivo. Asegúrate de tener un respaldo antes de ejecutarlo.
 * 
 * Para ejecutar: Accede a este archivo desde tu navegador o ejecútalo desde la línea de comandos
 */

require_once "../config/Conexion.php";

echo "<h2>Limpieza de Base de Datos - Eliminando Sistema Académico</h2>";
echo "<hr>";

// Desactivar verificación de claves foráneas temporalmente
ejecutarConsulta("SET FOREIGN_KEY_CHECKS = 0");
echo "<p>✓ Verificación de claves foráneas desactivada</p>";

// Lista de tablas del sistema académico a eliminar
$tablas_academicas = [
    'inscripcion',  // Primero la que tiene más dependencias
    'curso',
    'estudiante',
    'docente',
    'materia',
    'carrera',
    'grado',
    'aula',
    'turno'
];

// Eliminar tablas
echo "<h3>Eliminando tablas del sistema académico:</h3>";
foreach ($tablas_academicas as $tabla) {
    $sql = "DROP TABLE IF EXISTS `$tabla`";
    ejecutarConsulta($sql);
    echo "<p>✓ Tabla <strong>$tabla</strong> eliminada</p>";
}

// Lista de permisos académicos a eliminar
$permisos_academicos = ['estudiantes', 'docentes', 'academico', 'cursos', 'inscripcion', 'notas', 'consultac', 'consultav'];

// Obtener IDs de permisos académicos antes de eliminarlos
echo "<h3>Eliminando permisos académicos:</h3>";
$ids_permisos = [];

foreach ($permisos_academicos as $nombre_permiso) {
    $sql = "SELECT idpermiso FROM permiso WHERE nombre = '$nombre_permiso'";
    $resultado = ejecutarConsulta($sql);
    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $ids_permisos[] = $row['idpermiso'];
    }
}

// Eliminar referencias en usuario_permiso
if (!empty($ids_permisos)) {
    $ids_str = implode(',', $ids_permisos);
    $sql = "DELETE FROM usuario_permiso WHERE idpermiso IN ($ids_str)";
    ejecutarConsulta($sql);
    echo "<p>✓ Permisos académicos eliminados de <strong>usuario_permiso</strong> (IDs: $ids_str)</p>";
}

// Eliminar permisos de la tabla permiso
foreach ($permisos_academicos as $nombre_permiso) {
    $sql = "DELETE FROM permiso WHERE nombre = '$nombre_permiso'";
    ejecutarConsulta($sql);
    echo "<p>✓ Permiso <strong>$nombre_permiso</strong> eliminado</p>";
}

// Reactivar verificación de claves foráneas
ejecutarConsulta("SET FOREIGN_KEY_CHECKS = 1");
echo "<p>✓ Verificación de claves foráneas reactivada</p>";

echo "<hr>";
echo "<h3 style='color: green;'>✓ Limpieza completada exitosamente</h3>";
echo "<p>Se eliminaron todas las tablas y permisos del sistema académico.</p>";
echo "<p><strong>Tablas eliminadas:</strong> " . implode(', ', $tablas_academicas) . "</p>";
echo "<p><strong>Permisos eliminados:</strong> " . implode(', ', $permisos_academicos) . "</p>";
echo "<hr>";
echo "<p><em>Las tablas y permisos del sistema funerario se mantienen intactos.</em></p>";
?>








