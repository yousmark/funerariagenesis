<?php 
session_start();
require_once "../modelos/Cotizacion.php";
require_once "../config/Validacion.php";

$cot = new Cotizacion();

$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
// Nuevos campos: nombres y apellidos separados
$nombres_familia = isset($_POST["nombres_familia"]) ? limpiarCadena($_POST["nombres_familia"]) : "";
$apellidos_familia = isset($_POST["apellidos_familia"]) ? limpiarCadena($_POST["apellidos_familia"]) : "";
// Mantener compatibilidad con campo antiguo
$nombre_familia = isset($_POST["nombre_familia"]) ? limpiarCadena($_POST["nombre_familia"]) : "";
// Si no vienen separados, usar el campo antiguo y dividirlo
if (empty($nombres_familia) && !empty($nombre_familia)) {
	$partes = explode(' ', trim($nombre_familia), 2);
	$nombres_familia = $partes[0] ?? '';
	$apellidos_familia = $partes[1] ?? '';
} elseif (empty($nombres_familia) && empty($apellidos_familia)) {
	// Si no viene nada, usar cadena vacía
	$nombres_familia = '';
	$apellidos_familia = '';
}
$contacto_email = isset($_POST["contacto_email"]) ? limpiarCadena($_POST["contacto_email"]) : "";
$contacto_celular = isset($_POST["contacto_celular"]) ? limpiarCadena($_POST["contacto_celular"]) : "";
$detalle_json = isset($_POST["detalle_json"]) ? $_POST["detalle_json"] : "";
$total = isset($_POST["total"]) ? limpiarCadena($_POST["total"]) : "0";
$hash_sha256 = isset($_POST["hash_sha256"]) ? limpiarCadena($_POST["hash_sha256"]) : "";
// Nuevos campos: nombres y apellidos de firma separados
$firma_nombres = isset($_POST["firma_nombres"]) ? limpiarCadena($_POST["firma_nombres"]) : "";
$firma_apellidos = isset($_POST["firma_apellidos"]) ? limpiarCadena($_POST["firma_apellidos"]) : "";
// Mantener compatibilidad con campo antiguo
$firma_nombre = isset($_POST["firma_nombre"]) ? limpiarCadena($_POST["firma_nombre"]) : "";
// Si no vienen separados, usar el campo antiguo y dividirlo
if (empty($firma_nombres) && !empty($firma_nombre)) {
	$partes = explode(' ', trim($firma_nombre), 2);
	$firma_nombres = $partes[0] ?? '';
	$firma_apellidos = $partes[1] ?? '';
} elseif (empty($firma_nombres) && empty($firma_apellidos)) {
	$firma_nombres = '';
	$firma_apellidos = '';
}
$firma_fecha = isset($_POST["firma_fecha"]) ? limpiarCadena($_POST["firma_fecha"]) : date('Y-m-d H:i:s');
$firma_ip = $_SERVER['REMOTE_ADDR'] ?? '';

switch ($_GET["op"]) {

	case 'guardar':
		// Validar fecha de fallecimiento antes de procesar
		$fecha_fallecimiento = isset($_POST["fecha_fallecimiento"]) ? limpiarCadena($_POST["fecha_fallecimiento"]) : "";
		if (!empty($fecha_fallecimiento)) {
			$validacion_fecha = Validacion::validarFechaFallecimiento($fecha_fallecimiento);
			if (!$validacion_fecha['valida']) {
				echo json_encode(["ok"=>false, "mensaje"=>$validacion_fecha['mensaje']]);
				exit();
			}
		}
		
		$ruta_pdf = '';
		if (isset($_FILES['pdf']) && is_uploaded_file($_FILES['pdf']['tmp_name'])) {
			$ext = explode('.', $_FILES['pdf']['name']);
			$nombre_archivo = $codigo !== '' ? $codigo : round(microtime(true));
			$nombre_archivo = $nombre_archivo . '.pdf';
			$ruta_destino = "../files/cotizaciones/" . $nombre_archivo;
			if (move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_destino)) {
				$ruta_pdf = "files/cotizaciones/" . $nombre_archivo;
			} else {
				echo json_encode(["ok"=>false, "mensaje"=>"No se pudo guardar el PDF"]);
				exit();
			}
		} else {
			echo json_encode(["ok"=>false, "mensaje"=>"Archivo PDF no recibido"]);
			exit();
		}

		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "mensaje"=>"Sesión expirada"]); 
			exit(); 
		}

		if ($codigo === '') { $codigo = strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); }

		$idnew = $cot->insertar($codigo, $idusuario, $nombres_familia, $apellidos_familia, $contacto_email, $contacto_celular, $detalle_json, $total, $ruta_pdf, $hash_sha256, $firma_nombres, $firma_apellidos, $firma_fecha, $firma_ip);
		echo $idnew ? json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "ruta_pdf"=>$ruta_pdf]) : json_encode(["ok"=>false, "mensaje"=>"No se pudo registrar la cotización"]);
	break;

	case 'listar':
		$rspta = $cot->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idcotizacion . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_familia,
				"3" => number_format($reg->total,2),
				"4" => $reg->usuario,
				"5" => $reg->estado,
				"6" => $reg->fecha_creacion
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

	case 'listarVigentes':
		// Listar solo cotizaciones vigentes para el selector de contratos
		$rspta = $cot->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			// Solo incluir cotizaciones vigentes
			if ($reg->estado === 'vigente') {
				$data[] = array(
					"idcotizacion" => $reg->idcotizacion,
					"codigo" => $reg->codigo,
					"nombre_familia" => $reg->nombre_familia,
					"total" => $reg->total,
					"fecha_creacion" => $reg->fecha_creacion,
					"texto" => $reg->codigo . ' - ' . $reg->nombre_familia . ' (Bs. ' . number_format($reg->total, 2) . ')'
				);
			}
		}
		// Ordenar por fecha de creación descendente
		usort($data, function($a, $b) {
			return strtotime($b['fecha_creacion']) - strtotime($a['fecha_creacion']);
		});
		echo json_encode($data);
	break;

	case 'mostrar':
		$rs = $cot->mostrar($idcotizacion);
		echo json_encode($rs);
	break;

	case 'listarClientes':
		$rspta = $cot->listarClientes();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"idcliente" => $reg->idcliente,
				"nombre" => $reg->nombre,
				"email" => $reg->email,
				"celular" => $reg->celular,
				"direccion" => $reg->direccion
			);
		}
		echo json_encode($data);
	break;

	case 'listarPermisos':
		$id = $idcotizacion;
		if (empty($id)) { echo json_encode([]); exit(); }
		$rspta = $cot->listarPermisos($id);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"idpermiso" => $reg->idpermiso,
				"idcliente" => $reg->idcliente,
				"cliente_nombre" => $reg->cliente_nombre,
				"cliente_email" => $reg->cliente_email,
				"cliente_celular" => $reg->cliente_celular,
				"fecha_otorgado" => $reg->fecha_otorgado,
				"estado" => $reg->estado,
				"otorgado_por_nombre" => $reg->otorgado_por_nombre
			);
		}
		echo json_encode($data);
	break;

	case 'otorgarPermiso':
		$id = $idcotizacion;
		$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		if (empty($id) || empty($idcliente)) { 
			echo json_encode(["ok"=>false, "msg"=>"Datos incompletos"]); 
			exit(); 
		}
		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "msg"=>"Sesión expirada"]); 
			exit(); 
		}
		$resultado = $cot->otorgarPermiso($id, $idcliente, $idusuario);
		if ($resultado === false) {
			echo json_encode(["ok"=>false, "msg"=>"El usuario ya tiene permiso para ver esta cotización"]);
		} else if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso otorgado correctamente al usuario"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al otorgar permiso"]);
		}
	break;

	case 'quitarPermiso':
		$id = $idcotizacion;
		$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		if (empty($id) || empty($idcliente)) { 
			echo json_encode(["ok"=>false, "msg"=>"Datos incompletos"]); 
			exit(); 
		}
		$resultado = $cot->quitarPermiso($id, $idcliente);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso revocado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al revocar permiso"]);
		}
	break;

}
?>


