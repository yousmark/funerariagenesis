<?php
require_once "../config/Conexion.php";
require_once "../config/global.php";

class Backup {
	
	public function __construct() {}
	
	/**
	 * Crear backup de la base de datos
	 */
	public function crearBackup($rutaDestino = null) {
		if ($rutaDestino === null) {
			$directorio = "../backups/";
			if (!file_exists($directorio)) {
				mkdir($directorio, 0755, true);
			}
			$nombreArchivo = "backup_" . DB_NAME . "_" . date('Y-m-d_H-i-s') . ".sql";
			$rutaDestino = $directorio . $nombreArchivo;
		}
		
		// Comando mysqldump
		$comando = sprintf(
			'mysqldump --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
			escapeshellarg(DB_HOST),
			escapeshellarg(DB_USERNAME),
			escapeshellarg(DB_PASSWORD),
			escapeshellarg(DB_NAME),
			escapeshellarg($rutaDestino)
		);
		
		// Ejecutar backup
		exec($comando . ' 2>&1', $output, $return_var);
		
		if ($return_var === 0 && file_exists($rutaDestino)) {
			// Comprimir el backup
			$rutaComprimida = $rutaDestino . '.gz';
			$gz = gzopen($rutaComprimida, 'w9');
			$archivo = fopen($rutaDestino, 'r');
			while (!feof($archivo)) {
				gzwrite($gz, fread($archivo, 8192));
			}
			fclose($archivo);
			gzclose($gz);
			
			// Eliminar archivo sin comprimir
			unlink($rutaDestino);
			
			// Registrar en la base de datos
			$nombreArchivo = basename($rutaComprimida);
			$tamaño = filesize($rutaComprimida);
			$sql = "INSERT INTO backup (nombre_archivo, ruta, tamaño, fecha_creacion, estado) 
					VALUES ('$nombreArchivo', '$rutaComprimida', '$tamaño', NOW(), 'activo')";
			$id = ejecutarConsulta_retornarID($sql);
			
			return array(
				'success' => true,
				'id' => $id,
				'archivo' => $nombreArchivo,
				'ruta' => $rutaComprimida,
				'tamaño' => $tamaño
			);
		} else {
			return array(
				'success' => false,
				'error' => implode("\n", $output)
			);
		}
	}
	
	/**
	 * Listar backups disponibles
	 */
	public function listarBackups() {
		$sql = "SELECT * FROM backup WHERE estado='activo' ORDER BY fecha_creacion DESC";
		return ejecutarConsulta($sql);
	}
	
	/**
	 * Eliminar backup
	 */
	public function eliminarBackup($idbackup) {
		// Obtener información del backup
		$sql = "SELECT ruta FROM backup WHERE idbackup='$idbackup'";
		$backup = ejecutarConsultaSimpleFila($sql);
		
		if ($backup && file_exists($backup['ruta'])) {
			unlink($backup['ruta']);
		}
		
		// Marcar como eliminado
		$sql = "UPDATE backup SET estado='eliminado' WHERE idbackup='$idbackup'";
		return ejecutarConsulta($sql);
	}
	
	/**
	 * Restaurar backup
	 */
	public function restaurarBackup($rutaArchivo) {
		if (!file_exists($rutaArchivo)) {
			return array('success' => false, 'error' => 'Archivo no encontrado');
		}
		
		// Descomprimir si es necesario
		if (pathinfo($rutaArchivo, PATHINFO_EXTENSION) === 'gz') {
			$archivoTemporal = sys_get_temp_dir() . '/' . uniqid('backup_') . '.sql';
			$gz = gzopen($rutaArchivo, 'r');
			$archivo = fopen($archivoTemporal, 'w');
			while (!gzeof($gz)) {
				fwrite($archivo, gzread($gz, 8192));
			}
			fclose($archivo);
			gzclose($gz);
			$rutaArchivo = $archivoTemporal;
		}
		
		// Leer y ejecutar SQL
		$sql = file_get_contents($rutaArchivo);
		
		// Dividir en sentencias individuales
		$sentencias = array_filter(array_map('trim', explode(';', $sql)));
		
		global $conexion;
		$conexion->autocommit(FALSE);
		$error = false;
		
		foreach ($sentencias as $sentencia) {
			if (!empty($sentencia)) {
				if (!$conexion->query($sentencia)) {
					$error = $conexion->error;
					$conexion->rollback();
					break;
				}
			}
		}
		
		if (!$error) {
			$conexion->commit();
			$resultado = array('success' => true);
		} else {
			$resultado = array('success' => false, 'error' => $error);
		}
		
		$conexion->autocommit(TRUE);
		
		// Eliminar archivo temporal si existe
		if (isset($archivoTemporal) && file_exists($archivoTemporal)) {
			unlink($archivoTemporal);
		}
		
		return $resultado;
	}
	
	/**
	 * Obtener información de backup
	 */
	public function mostrar($idbackup) {
		$sql = "SELECT * FROM backup WHERE idbackup='$idbackup'";
		return ejecutarConsultaSimpleFila($sql);
	}
}

?>







