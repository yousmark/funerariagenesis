<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/Permiso.php";

$permiso = new Permiso();

$op = isset($_GET["op"]) ? $_GET["op"] : "";
$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$idpermiso = isset($_POST["idpermiso"]) ? limpiarCadena($_POST["idpermiso"]) : "";

switch ($op) {
	
	case 'listar':
		$rspta = $permiso->listarUsuarios();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$estadoBadge = $reg->condicion ? '<span class="label bg-green">Activo</span>' : '<span class="label bg-red">Inactivo</span>';
			$permisosTexto = $reg->permisos ? $reg->permisos : '<span class="text-muted">Sin permisos</span>';
			
			// Usar data-attributes en lugar de onclick para evitar problemas de escape
			$data[] = array(
				"0" => '<button class="btn btn-info btn-xs btn-gestionar-permisos" data-idusuario="' . $reg->idusuario . '" data-nombre="' . htmlspecialchars($reg->nombre, ENT_QUOTES, 'UTF-8') . '"><i class="fa fa-key"></i> Gestionar Permisos</button>',
				"1" => $reg->nombre,
				"2" => $reg->email ? $reg->email : 'N/A',
				"3" => $reg->cargo ? $reg->cargo : 'N/A',
				"4" => $permisosTexto,
				"5" => $estadoBadge
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

	case 'listarPermisosUsuario':
		header('Content-Type: application/json');
		if (empty($idusuario)) {
			echo json_encode([]);
			exit();
		}
		$rspta = $permiso->listarPermisosUsuario($idusuario);
		$data = array();
		if ($rspta) {
			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"idpermiso" => $reg->idpermiso,
					"nombre" => $reg->nombre
				);
			}
		}
		echo json_encode($data);
		exit();
		break;

	case 'listarTodosPermisos':
		header('Content-Type: application/json');
		$rspta = $permiso->listarTodosPermisos();
		$data = array();
		if ($rspta) {
			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"idpermiso" => $reg->idpermiso,
					"nombre" => $reg->nombre
				);
			}
		}
		echo json_encode($data);
		exit();
		break;

	case 'actualizarPermisos':
		header('Content-Type: application/json');
		if (empty($idusuario)) {
			echo json_encode(["ok" => false, "msg" => "Usuario no especificado"]);
			exit();
		}
		// Recibir permisos como array
		$permisos = array();
		if (isset($_POST["permisos"]) && is_array($_POST["permisos"])) {
			$permisos = $_POST["permisos"];
		} else if (isset($_POST["permisos"])) {
			// Si viene como string JSON
			$permisos = json_decode($_POST["permisos"], true);
			if (!is_array($permisos)) {
				$permisos = array();
			}
		}
		// Limpiar cada permiso
		$permisos_limpios = array();
		foreach ($permisos as $p) {
			$permisos_limpios[] = limpiarCadena($p);
		}
		$rspta = $permiso->actualizarPermisosUsuario($idusuario, $permisos_limpios);
		if ($rspta) {
			echo json_encode(["ok" => true, "msg" => "Permisos actualizados correctamente"]);
		} else {
			echo json_encode(["ok" => false, "msg" => "Error al actualizar permisos"]);
		}
		exit();
		break;

	case 'asignarPermiso':
		if (empty($idusuario) || empty($idpermiso)) {
			echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
			break;
		}
		$rspta = $permiso->asignarPermiso($idusuario, $idpermiso);
		if ($rspta === false) {
			echo json_encode(["ok" => false, "msg" => "El usuario ya tiene este permiso"]);
		} else if ($rspta) {
			echo json_encode(["ok" => true, "msg" => "Permiso asignado correctamente"]);
		} else {
			echo json_encode(["ok" => false, "msg" => "Error al asignar permiso"]);
		}
		break;

	case 'quitarPermiso':
		if (empty($idusuario) || empty($idpermiso)) {
			echo json_encode(["ok" => false, "msg" => "Datos incompletos"]);
			break;
		}
		$rspta = $permiso->quitarPermiso($idusuario, $idpermiso);
		if ($rspta) {
			echo json_encode(["ok" => true, "msg" => "Permiso removido correctamente"]);
		} else {
			echo json_encode(["ok" => false, "msg" => "Error al remover permiso"]);
		}
		break;
}
 ?>