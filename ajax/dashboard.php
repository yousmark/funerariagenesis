<?php
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/Dashboard.php";

$dashboard = new Dashboard();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
	
	case 'estadisticas':
		header('Content-Type: application/json');
		$stats = $dashboard->obtenerEstadisticas();
		echo json_encode($stats);
		break;
		
	case 'estadisticasPorMes':
		header('Content-Type: application/json');
		$datos = $dashboard->obtenerEstadisticasPorMes();
		echo json_encode($datos);
		break;
		
	case 'actividadReciente':
		header('Content-Type: application/json');
		$limite = isset($_GET["limite"]) ? intval($_GET["limite"]) : 10;
		$rspta = $dashboard->obtenerActividadReciente($limite);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"tipo" => $reg->tipo,
				"codigo" => $reg->codigo,
				"nombre" => $reg->nombre,
				"total" => number_format($reg->total, 2),
				"fecha_creacion" => $reg->fecha_creacion,
				"estado" => $reg->estado,
				"id" => $reg->id
			);
		}
		echo json_encode($data);
		break;
		
	default:
		echo json_encode(["error" => "Operación no válida"]);
		break;
}
?>








