<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/Contrato.php";

$con = new Contrato();
$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;

if ($idusuario == 0) { 
	echo json_encode(["error"=>"Sesión expirada"]); 
	exit(); 
}

$idcontrato = isset($_POST["idcontrato"]) ? limpiarCadena($_POST["idcontrato"]) : "";
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

	case 'listar':
		$rspta = $con->listarContratosPorUsuario($idusuario);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idcontrato . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_contratante,
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
		$rs = $con->mostrarContratoUsuario($idcontrato, $idusuario);
		if ($rs) {
			echo json_encode($rs);
		} else {
			echo json_encode(["error"=>"No tiene permiso para ver este contrato o no existe"]);
		}
	break;

	default:
		echo json_encode(["error"=>"Operación no válida"]);
	break;
}
?>










