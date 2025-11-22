<?php 
session_start();
require_once "../modelos/Contrato.php";

$con = new Contrato();

$idcontrato = isset($_POST["idcontrato"]) ? limpiarCadena($_POST["idcontrato"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
// Nuevos campos: nombres y apellidos separados
$nombres_contratante = isset($_POST["nombres_contratante"]) ? limpiarCadena($_POST["nombres_contratante"]) : "";
$apellidos_contratante = isset($_POST["apellidos_contratante"]) ? limpiarCadena($_POST["apellidos_contratante"]) : "";
// Mantener compatibilidad con campo antiguo
$nombre_contratante = isset($_POST["nombre_contratante"]) ? limpiarCadena($_POST["nombre_contratante"]) : "";
// Si no vienen separados, usar el campo antiguo y dividirlo
if (empty($nombres_contratante) && !empty($nombre_contratante)) {
	$partes = explode(' ', trim($nombre_contratante), 2);
	$nombres_contratante = $partes[0] ?? '';
	$apellidos_contratante = $partes[1] ?? '';
} elseif (empty($nombres_contratante) && empty($apellidos_contratante)) {
	$nombres_contratante = '';
	$apellidos_contratante = '';
}
$cedula_contratante = isset($_POST["cedula_contratante"]) ? limpiarCadena($_POST["cedula_contratante"]) : "";
$complemento_contratante = isset($_POST["complemento_contratante"]) ? limpiarCadena($_POST["complemento_contratante"]) : "";
$contacto_email = isset($_POST["contacto_email"]) ? limpiarCadena($_POST["contacto_email"]) : "";
$contacto_celular = isset($_POST["contacto_celular"]) ? limpiarCadena($_POST["contacto_celular"]) : "";
$direccion_contratante = isset($_POST["direccion_contratante"]) ? limpiarCadena($_POST["direccion_contratante"]) : "";
// Nuevos campos: nombres y apellidos del fallecido separados
$nombres_fallecido = isset($_POST["nombres_fallecido"]) ? limpiarCadena($_POST["nombres_fallecido"]) : "";
$apellidos_fallecido = isset($_POST["apellidos_fallecido"]) ? limpiarCadena($_POST["apellidos_fallecido"]) : "";
// Mantener compatibilidad con campo antiguo
$nombre_fallecido = isset($_POST["nombre_fallecido"]) ? limpiarCadena($_POST["nombre_fallecido"]) : "";
// Si no vienen separados, usar el campo antiguo y dividirlo
if (empty($nombres_fallecido) && !empty($nombre_fallecido)) {
	$partes = explode(' ', trim($nombre_fallecido), 2);
	$nombres_fallecido = $partes[0] ?? '';
	$apellidos_fallecido = $partes[1] ?? '';
} elseif (empty($nombres_fallecido) && empty($apellidos_fallecido)) {
	$nombres_fallecido = '';
	$apellidos_fallecido = '';
}
$fecha_fallecimiento = isset($_POST["fecha_fallecimiento"]) ? limpiarCadena($_POST["fecha_fallecimiento"]) : "";
$edad_fallecido = isset($_POST["edad_fallecido"]) ? limpiarCadena($_POST["edad_fallecido"]) : "";
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
		$ruta_pdf = '';
		if (isset($_FILES['pdf']) && is_uploaded_file($_FILES['pdf']['tmp_name'])) {
			$ext = explode('.', $_FILES['pdf']['name']);
			$nombre_archivo = $codigo !== '' ? $codigo : round(microtime(true));
			$nombre_archivo = $nombre_archivo . '.pdf';
			$ruta_destino = "../files/contratos/" . $nombre_archivo;
			if (!file_exists("../files/contratos/")) {
				mkdir("../files/contratos/", 0777, true);
			}
			if (move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_destino)) {
				$ruta_pdf = "files/contratos/" . $nombre_archivo;
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

		if ($codigo === '') { $codigo = 'CONT-' . strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); }

		$idnew = $con->insertar($codigo, $idusuario, $nombres_contratante, $apellidos_contratante, $cedula_contratante, $contacto_email, $contacto_celular, $direccion_contratante, $nombres_fallecido, $apellidos_fallecido, $fecha_fallecimiento, $edad_fallecido, $detalle_json, $total, $ruta_pdf, $hash_sha256, $firma_nombres, $firma_apellidos, $firma_fecha, $firma_ip);
		echo $idnew ? json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "ruta_pdf"=>$ruta_pdf]) : json_encode(["ok"=>false, "mensaje"=>"No se pudo registrar el contrato"]);
	break;

	case 'listar':
		$rspta = $con->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idcontrato . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_contratante,
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

	case 'mostrar':
		$rs = $con->mostrar($idcontrato);
		echo json_encode($rs);
	break;

	case 'listarClientes':
		$rspta = $con->listarClientes();
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
		$id = $idcontrato;
		if (empty($id)) { echo json_encode([]); exit(); }
		$rspta = $con->listarPermisos($id);
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
		$id = $idcontrato;
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
		$resultado = $con->otorgarPermiso($id, $idcliente, $idusuario);
		if ($resultado === false) {
			echo json_encode(["ok"=>false, "msg"=>"El usuario ya tiene permiso para ver este contrato"]);
		} else if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso otorgado correctamente al usuario"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al otorgar permiso"]);
		}
	break;

	case 'quitarPermiso':
		$id = $idcontrato;
		$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		if (empty($id) || empty($idcliente)) { 
			echo json_encode(["ok"=>false, "msg"=>"Datos incompletos"]); 
			exit(); 
		}
		$resultado = $con->quitarPermiso($id, $idcliente);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso revocado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al revocar permiso"]);
		}
	break;

}
?>





