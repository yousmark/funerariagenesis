<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/ItinerarioFunerario.php";

$itinerario = new ItinerarioFunerario();

$iditinerario = isset($_POST["iditinerario"]) ? limpiarCadena($_POST["iditinerario"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$idcontrato = isset($_POST["idcontrato"]) ? limpiarCadena($_POST["idcontrato"]) : "";
$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$nombre_difunto = isset($_POST["nombre_difunto"]) ? limpiarCadena($_POST["nombre_difunto"]) : "";
$nombre_familia = isset($_POST["nombre_familia"]) ? limpiarCadena($_POST["nombre_familia"]) : "";
$telefono_contacto = isset($_POST["telefono_contacto"]) ? limpiarCadena($_POST["telefono_contacto"]) : "";
$fecha_sepelio = isset($_POST["fecha_sepelio"]) ? limpiarCadena($_POST["fecha_sepelio"]) : "";
$hora_sepelio = isset($_POST["hora_sepelio"]) ? limpiarCadena($_POST["hora_sepelio"]) : "";
$lugar_sepelio = isset($_POST["lugar_sepelio"]) ? limpiarCadena($_POST["lugar_sepelio"]) : "";
$direccion_sepelio = isset($_POST["direccion_sepelio"]) ? $_POST["direccion_sepelio"] : "";
$ruta_cortejo = isset($_POST["ruta_cortejo"]) ? $_POST["ruta_cortejo"] : "";
$punto_partida = isset($_POST["punto_partida"]) ? limpiarCadena($_POST["punto_partida"]) : "";
$observaciones = isset($_POST["observaciones"]) ? $_POST["observaciones"] : "";
$hash_sha256 = isset($_POST["hash_sha256"]) ? limpiarCadena($_POST["hash_sha256"]) : "";

switch ($_GET["op"]) {

	case 'guardar':
		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "msg"=>"Sesión expirada"]); 
			exit(); 
		}

		// Validar campos requeridos
		if (empty($nombre_difunto) || empty($nombre_familia) || empty($fecha_sepelio) || empty($hora_sepelio) || empty($lugar_sepelio) || empty($ruta_cortejo)) {
			echo json_encode(["ok"=>false, "msg"=>"Debe completar todos los campos requeridos"]);
			exit();
		}

		// Generar código si no existe
		if ($codigo === '') { 
			$codigo = 'ITIN-' . strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); 
		}

		// Validar que el PDF fue generado y enviado
		if (!isset($_FILES['pdf']) || !is_uploaded_file($_FILES['pdf']['tmp_name'])) {
			echo json_encode(["ok"=>false, "msg"=>"Archivo PDF no recibido"]);
			exit();
		}

		// Crear directorio si no existe
		if (!file_exists("../files/itinerarios_funerarios/")) {
			mkdir("../files/itinerarios_funerarios/", 0777, true);
		}

		// Guardar PDF
		$ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
		$nombre_archivo = $codigo . '.pdf';
		$ruta_pdf = "files/itinerarios_funerarios/" . $nombre_archivo;
		if (!move_uploaded_file($_FILES['pdf']['tmp_name'], "../" . $ruta_pdf)) {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo guardar el PDF"]);
			exit();
		}

		$idnew = $itinerario->insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $nombre_familia, $telefono_contacto, $fecha_sepelio, $hora_sepelio, $lugar_sepelio, $direccion_sepelio, $ruta_cortejo, $punto_partida, $observaciones, $ruta_pdf, $hash_sha256);

		if ($idnew) {
			echo json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "ruta_pdf"=>$ruta_pdf]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo registrar el itinerario. Verifique los datos e intente nuevamente."]);
		}
	break;

	case 'listar':
		$rspta = $itinerario->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$confirmado_badge = '<span class="label label-success">Confirmado</span>';
			if ($reg->confirmado_familia == 0) {
				$confirmado_badge = '<span class="label label-warning">Pendiente</span>';
			}
			
			$estado_badge = '<span class="label label-success">Activo</span>';
			if ($reg->estado == 'inactivo') {
				$estado_badge = '<span class="label label-danger">Inactivo</span>';
			}

			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->iditinerario . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_difunto,
				"3" => $reg->nombre_familia,
				"4" => $reg->fecha_sepelio . ' ' . $reg->hora_sepelio,
				"5" => $reg->lugar_sepelio,
				"6" => $confirmado_badge,
				"7" => $estado_badge,
				"8" => $reg->usuario,
				"9" => $reg->fecha_creacion
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
		$rs = $itinerario->mostrar($iditinerario);
		echo json_encode($rs);
	break;

	case 'listarContratos':
		$rspta = $itinerario->listarContratos();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"idcontrato" => $reg->idcontrato,
				"codigo" => $reg->codigo,
				"nombre" => $reg->nombre_contratante
			);
		}
		echo json_encode($data);
	break;

	case 'listarCotizaciones':
		$rspta = $itinerario->listarCotizaciones();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"idcotizacion" => $reg->idcotizacion,
				"codigo" => $reg->codigo,
				"nombre" => $reg->nombre_familia
			);
		}
		echo json_encode($data);
	break;

	case 'listarClientes':
		$rspta = $itinerario->listarClientes();
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
		$rspta = $itinerario->listarPermisos($iditinerario);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$estado_badge = ($reg->estado == 'activo') ? '<span class="label label-success">Activo</span>' : '<span class="label label-danger">Revocado</span>';
			$data[] = array(
				"nombre" => $reg->cliente_nombre,
				"email" => $reg->cliente_email,
				"celular" => $reg->cliente_celular,
				"fecha_otorgado" => $reg->fecha_otorgado,
				"estado" => $estado_badge,
				"acciones" => ($reg->estado == 'activo') ? '<button class="btn btn-xs btn-danger" onclick="quitarPermiso('.$iditinerario.','.$reg->idcliente.')"><i class="fa fa-times"></i></button>' : ''
			);
		}
		echo json_encode($data);
	break;

	case 'otorgarPermiso':
		$idusuario_permiso = isset($_POST["idusuario_permiso"]) ? limpiarCadena($_POST["idusuario_permiso"]) : "";
		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "msg"=>"Sesión expirada"]); 
			exit(); 
		}
		$resultado = $itinerario->otorgarPermiso($iditinerario, $idusuario_permiso, $idusuario);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso otorgado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"El usuario ya tiene permiso o hubo un error"]);
		}
	break;

	case 'quitarPermiso':
		$idusuario_permiso = isset($_POST["idusuario_permiso"]) ? limpiarCadena($_POST["idusuario_permiso"]) : "";
		$resultado = $itinerario->quitarPermiso($iditinerario, $idusuario_permiso);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso revocado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al revocar el permiso"]);
		}
	break;

	case 'confirmarFamilia':
		$ip = $_SERVER['REMOTE_ADDR'] ?? '';
		$resultado = $itinerario->confirmarFamilia($iditinerario, $ip);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Itinerario confirmado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al confirmar el itinerario"]);
		}
	break;

	case 'desactivar':
		$rspta = $itinerario->desactivar($iditinerario);
		echo $rspta ? "Itinerario desactivado" : "Itinerario no se puede desactivar";
	break;
}
?>








