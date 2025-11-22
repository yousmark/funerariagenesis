<?php 
session_start();
require_once "../modelos/FichaRecojo.php";

$ficha = new FichaRecojo();

// Recibir todos los campos del formulario
$idficha = isset($_POST["idficha"]) ? limpiarCadena($_POST["idficha"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre_difunto = isset($_POST["nombre_difunto"]) ? limpiarCadena($_POST["nombre_difunto"]) : "";
$cedula_difunto = isset($_POST["cedula_difunto"]) ? limpiarCadena($_POST["cedula_difunto"]) : "";
$edad_difunto = isset($_POST["edad_difunto"]) ? limpiarCadena($_POST["edad_difunto"]) : "";
$fecha_fallecimiento = isset($_POST["fecha_fallecimiento"]) ? limpiarCadena($_POST["fecha_fallecimiento"]) : "";
$lugar_fallecimiento = isset($_POST["lugar_fallecimiento"]) ? limpiarCadena($_POST["lugar_fallecimiento"]) : "";
// Campo médico_certificador eliminado del formulario
$causa_muerte = isset($_POST["causa_muerte"]) ? limpiarCadena($_POST["causa_muerte"]) : "";
$direccion_recojo = isset($_POST["direccion_recojo"]) ? limpiarCadena($_POST["direccion_recojo"]) : "";
$ciudad_recojo = isset($_POST["ciudad_recojo"]) ? limpiarCadena($_POST["ciudad_recojo"]) : "";
$barrio_zona = isset($_POST["barrio_zona"]) ? limpiarCadena($_POST["barrio_zona"]) : "";
$tipo_lugar = isset($_POST["tipo_lugar"]) ? limpiarCadena($_POST["tipo_lugar"]) : "Domicilio";
$condiciones_lugar = isset($_POST["condiciones_lugar"]) ? limpiarCadena($_POST["condiciones_lugar"]) : "";
$temperatura_ambiente = isset($_POST["temperatura_ambiente"]) ? limpiarCadena($_POST["temperatura_ambiente"]) : "";
$tiempo_trascurrido = isset($_POST["tiempo_trascurrido"]) ? limpiarCadena($_POST["tiempo_trascurrido"]) : "";
$estado_general = isset($_POST["estado_general"]) ? limpiarCadena($_POST["estado_general"]) : "Conservado";
$rigor_mortis = isset($_POST["rigor_mortis"]) ? limpiarCadena($_POST["rigor_mortis"]) : "Presente";
$livideces = isset($_POST["livideces"]) ? limpiarCadena($_POST["livideces"]) : "Si";
$lesiones_visibles = isset($_POST["lesiones_visibles"]) ? limpiarCadena($_POST["lesiones_visibles"]) : "";
$indumentaria = isset($_POST["indumentaria"]) ? limpiarCadena($_POST["indumentaria"]) : "";
$observaciones_cuerpo = isset($_POST["observaciones_cuerpo"]) ? limpiarCadena($_POST["observaciones_cuerpo"]) : "";
$equipo_recojo = isset($_POST["equipo_recojo"]) ? limpiarCadena($_POST["equipo_recojo"]) : "";
$personal_presente = isset($_POST["personal_presente"]) ? limpiarCadena($_POST["personal_presente"]) : "";
$insumos_bioseguridad = isset($_POST["insumos_bioseguridad"]) ? limpiarCadena($_POST["insumos_bioseguridad"]) : "";
$protocolos_aplicados = isset($_POST["protocolos_aplicados"]) ? limpiarCadena($_POST["protocolos_aplicados"]) : "";
$nombre_contacto = isset($_POST["nombre_contacto"]) ? limpiarCadena($_POST["nombre_contacto"]) : "";
$telefono_contacto = isset($_POST["telefono_contacto"]) ? limpiarCadena($_POST["telefono_contacto"]) : "";
$parentesco_contacto = isset($_POST["parentesco_contacto"]) ? limpiarCadena($_POST["parentesco_contacto"]) : "";
$fecha_recojo = isset($_POST["fecha_recojo"]) ? limpiarCadena($_POST["fecha_recojo"]) : date('Y-m-d H:i:s');
$hash_sha256 = isset($_POST["hash_sha256"]) ? limpiarCadena($_POST["hash_sha256"]) : "";

switch ($_GET["op"]) {

	case 'guardar':
		$ruta_pdf = '';
		if (isset($_FILES['pdf']) && is_uploaded_file($_FILES['pdf']['tmp_name'])) {
			$ext = explode('.', $_FILES['pdf']['name']);
			$nombre_archivo = $codigo !== '' ? $codigo : round(microtime(true));
			$nombre_archivo = $nombre_archivo . '.pdf';
			$ruta_destino = "../files/fichas_recojo/" . $nombre_archivo;
			if (!file_exists("../files/fichas_recojo/")) {
				mkdir("../files/fichas_recojo/", 0777, true);
			}
			if (move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_destino)) {
				$ruta_pdf = "files/fichas_recojo/" . $nombre_archivo;
			} else {
				echo "No se pudo guardar el PDF";
				exit();
			}
		} else {
			echo "Archivo PDF no recibido";
			exit();
		}

		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { echo "Sesión expirada"; exit(); }

		if ($codigo === '') { $codigo = 'FR-' . strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); }

		$idnew = $ficha->insertar($codigo, $idusuario, $nombre_difunto, $cedula_difunto, $edad_difunto, $fecha_fallecimiento, $lugar_fallecimiento, '', $causa_muerte, $direccion_recojo, $ciudad_recojo, $barrio_zona, $tipo_lugar, $condiciones_lugar, $temperatura_ambiente, $tiempo_trascurrido, $estado_general, $rigor_mortis, $livideces, $lesiones_visibles, $indumentaria, $observaciones_cuerpo, $equipo_recojo, $personal_presente, $insumos_bioseguridad, $protocolos_aplicados, $nombre_contacto, $telefono_contacto, $parentesco_contacto, $ruta_pdf, $hash_sha256, $fecha_recojo);
		echo $idnew ? json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "ruta_pdf"=>$ruta_pdf]) : "No se pudo registrar la ficha";
	break;

	case 'listar':
		$rspta = $ficha->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idficha . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_difunto,
				"3" => $reg->usuario,
				"4" => $reg->fecha_recojo,
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
		$rs = $ficha->mostrar($idficha);
		echo json_encode($rs);
	break;

	case 'listarClientes':
		$rspta = $ficha->listarClientes();
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
		$id = $idficha;
		if (empty($id)) { echo json_encode([]); exit(); }
		$rspta = $ficha->listarPermisos($id);
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
		$id = $idficha;
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
		$resultado = $ficha->otorgarPermiso($id, $idcliente, $idusuario);
		if ($resultado === false) {
			echo json_encode(["ok"=>false, "msg"=>"El usuario ya tiene permiso para ver esta ficha"]);
		} else if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso otorgado correctamente al usuario"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al otorgar permiso"]);
		}
	break;

	case 'quitarPermiso':
		$id = $idficha;
		$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
		if (empty($id) || empty($idcliente)) { 
			echo json_encode(["ok"=>false, "msg"=>"Datos incompletos"]); 
			exit(); 
		}
		$resultado = $ficha->quitarPermiso($id, $idcliente);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Permiso revocado correctamente"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al revocar permiso"]);
		}
	break;

}
?>


