<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/ItinerarioFunerario.php";

$itinerario = new ItinerarioFunerario();
$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;

if ($idusuario == 0) { 
	echo json_encode(["error"=>"Sesión expirada"]); 
	exit(); 
}

$iditinerario = isset($_POST["iditinerario"]) ? limpiarCadena($_POST["iditinerario"]) : "";
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

	case 'listar':
		$rspta = $itinerario->listarItinerariosPorUsuario($idusuario);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$confirmadoHtml = '<span class="label label-warning">Pendiente</span>';
			if ($reg->confirmado_familia == 1) {
				$confirmadoHtml = '<span class="label label-success">Confirmado</span>';
			}
			
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->iditinerario . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_difunto,
				"3" => $reg->nombre_familia,
				"4" => $reg->fecha_sepelio . ' ' . $reg->hora_sepelio,
				"5" => $reg->lugar_sepelio,
				"6" => $confirmadoHtml,
				"7" => $reg->fecha_otorgado
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

	case 'mostrar':
		$rs = $itinerario->mostrarItinerarioUsuario($iditinerario, $idusuario);
		if ($rs) {
			echo json_encode($rs);
		} else {
			echo json_encode(["error"=>"No tiene permiso para ver este itinerario o no existe"]);
		}
	break;

	case 'confirmar':
		// Verificar que el usuario tenga permiso para confirmar este itinerario
		$rs = $itinerario->mostrarItinerarioUsuario($iditinerario, $idusuario);
		if (!$rs) {
			echo json_encode(["ok"=>false, "msg"=>"No tiene permiso para confirmar este itinerario"]);
			break;
		}
		
		// Verificar que no esté ya confirmado
		if ($rs['confirmado_familia'] == 1) {
			echo json_encode(["ok"=>false, "msg"=>"Este itinerario ya ha sido confirmado anteriormente"]);
			break;
		}
		
		// Obtener IP del cliente
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		// Confirmar el itinerario
		$rspta = $itinerario->confirmarFamilia($iditinerario, $ip);
		if ($rspta) {
			echo json_encode(["ok"=>true, "msg"=>"Itinerario confirmado exitosamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al confirmar el itinerario"]);
		}
	break;

	default:
		echo json_encode(["error"=>"Operación no válida"]);
	break;
}
?>








