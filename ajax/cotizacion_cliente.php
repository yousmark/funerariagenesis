<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/Cotizacion.php";

$cot = new Cotizacion();
$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;

if ($idusuario == 0) { 
	echo json_encode(["error"=>"Sesión expirada"]); 
	exit(); 
}

$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

	case 'listar':
		$rspta = $cot->listarCotizacionesPorUsuario($idusuario);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idcotizacion . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_familia,
				"3" => number_format($reg->total,2),
				"4" => $reg->fecha_otorgado,
				"5" => $reg->estado
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
		$rs = $cot->mostrarCotizacionUsuario($idcotizacion, $idusuario);
		if ($rs) {
			echo json_encode($rs);
		} else {
			echo json_encode(["error"=>"No tiene permiso para ver esta cotización o no existe"]);
		}
	break;

	default:
		echo json_encode(["error"=>"Operación no válida"]);
	break;
}
?>

