<?php
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/Backup.php";

// Verificar que el usuario tenga permisos de acceso (solo administradores)
if (!isset($_SESSION['acceso']) || $_SESSION['acceso'] != 1) {
	echo json_encode(["error" => "Acceso denegado. Solo administradores pueden realizar backups."]);
	exit();
}

$backup = new Backup();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
	
	case 'crear':
		header('Content-Type: application/json');
		$resultado = $backup->crearBackup();
		if ($resultado['success']) {
			// Registrar usuario que creó el backup
			$idbackup = $resultado['id'];
			$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
			$sql = "UPDATE backup SET idusuario='$idusuario' WHERE idbackup='$idbackup'";
			ejecutarConsulta($sql);
		}
		echo json_encode($resultado);
		break;
	
	case 'listar':
		header('Content-Type: application/json');
		$rspta = $backup->listarBackups();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$tamañoFormateado = $reg->tamaño ? number_format($reg->tamaño / 1024 / 1024, 2) . ' MB' : 'N/A';
			$data[] = array(
				"0" => '<button class="btn btn-info btn-xs" onclick="descargar(' . $reg->idbackup . ')"><i class="fa fa-download"></i></button> ' .
					   '<button class="btn btn-warning btn-xs" onclick="restaurar(' . $reg->idbackup . ')"><i class="fa fa-undo"></i></button> ' .
					   '<button class="btn btn-danger btn-xs" onclick="eliminar(' . $reg->idbackup . ')"><i class="fa fa-trash"></i></button>',
				"1" => $reg->nombre_archivo,
				"2" => $tamañoFormateado,
				"3" => $reg->fecha_creacion,
				"4" => $reg->estado
			);
		}
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data
		);
		echo json_encode($results);
		break;
	
	case 'eliminar':
		$idbackup = isset($_POST["idbackup"]) ? limpiarCadena($_POST["idbackup"]) : "";
		if (empty($idbackup)) {
			echo json_encode(["success" => false, "msg" => "ID de backup requerido"]);
			exit();
		}
		$rspta = $backup->eliminarBackup($idbackup);
		echo json_encode(["success" => $rspta, "msg" => $rspta ? "Backup eliminado correctamente" : "Error al eliminar backup"]);
		break;
	
	case 'restaurar':
		$idbackup = isset($_POST["idbackup"]) ? limpiarCadena($_POST["idbackup"]) : "";
		if (empty($idbackup)) {
			echo json_encode(["success" => false, "msg" => "ID de backup requerido"]);
			exit();
		}
		
		// Obtener ruta del backup
		$backup_info = $backup->mostrar($idbackup);
		if (!$backup_info || !file_exists($backup_info['ruta'])) {
			echo json_encode(["success" => false, "msg" => "Backup no encontrado"]);
			exit();
		}
		
		$resultado = $backup->restaurarBackup($backup_info['ruta']);
		if ($resultado['success']) {
			// Actualizar estado
			$sql = "UPDATE backup SET estado='restaurado', fecha_restauracion=NOW() WHERE idbackup='$idbackup'";
			ejecutarConsulta($sql);
		}
		echo json_encode($resultado);
		break;
	
	case 'descargar':
		$idbackup = isset($_GET["idbackup"]) ? limpiarCadena($_GET["idbackup"]) : "";
		if (empty($idbackup)) {
			die("ID de backup requerido");
		}
		
		$backup_info = $backup->mostrar($idbackup);
		if (!$backup_info || !file_exists($backup_info['ruta'])) {
			die("Backup no encontrado");
		}
		
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $backup_info['nombre_archivo'] . '"');
		header('Content-Length: ' . filesize($backup_info['ruta']));
		readfile($backup_info['ruta']);
		exit();
		break;
	
	default:
		echo json_encode(["error" => "Operación no válida"]);
		break;
}

?>







