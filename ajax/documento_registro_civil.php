<?php 
session_start();
require_once "../config/Conexion.php";
require_once "../modelos/DocumentoRegistroCivil.php";

$doc = new DocumentoRegistroCivil();

$iddocumento = isset($_POST["iddocumento"]) ? limpiarCadena($_POST["iddocumento"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$idcontrato = isset($_POST["idcontrato"]) ? limpiarCadena($_POST["idcontrato"]) : "";
$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$nombre_difunto = isset($_POST["nombre_difunto"]) ? limpiarCadena($_POST["nombre_difunto"]) : "";
$cedula_difunto = isset($_POST["cedula_difunto"]) ? limpiarCadena($_POST["cedula_difunto"]) : "";
$nombre_responsable = isset($_POST["nombre_responsable"]) ? limpiarCadena($_POST["nombre_responsable"]) : "";
$cedula_responsable = isset($_POST["cedula_responsable"]) ? limpiarCadena($_POST["cedula_responsable"]) : "";
$telefono_responsable = isset($_POST["telefono_responsable"]) ? limpiarCadena($_POST["telefono_responsable"]) : "";
$direccion_responsable = isset($_POST["direccion_responsable"]) ? limpiarCadena($_POST["direccion_responsable"]) : "";
$testigo1_nombre = isset($_POST["testigo1_nombre"]) ? limpiarCadena($_POST["testigo1_nombre"]) : "";
$testigo1_cedula = isset($_POST["testigo1_cedula"]) ? limpiarCadena($_POST["testigo1_cedula"]) : "";
$testigo2_nombre = isset($_POST["testigo2_nombre"]) ? limpiarCadena($_POST["testigo2_nombre"]) : "";
$testigo2_cedula = isset($_POST["testigo2_cedula"]) ? limpiarCadena($_POST["testigo2_cedula"]) : "";
$testigo3_nombre = isset($_POST["testigo3_nombre"]) ? limpiarCadena($_POST["testigo3_nombre"]) : "";
$testigo3_cedula = isset($_POST["testigo3_cedula"]) ? limpiarCadena($_POST["testigo3_cedula"]) : "";
$observaciones = isset($_POST["observaciones"]) ? $_POST["observaciones"] : "";

switch ($_GET["op"]) {

	case 'guardar':
		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "msg"=>"Sesión expirada"]); 
			exit(); 
		}

		// Validar archivos requeridos
		if (!isset($_FILES['cedula_difunto_file']) || !is_uploaded_file($_FILES['cedula_difunto_file']['tmp_name'])) {
			echo json_encode(["ok"=>false, "msg"=>"Debe subir la cédula del difunto"]);
			exit();
		}

		if (!isset($_FILES['cedula_responsable_file']) || !is_uploaded_file($_FILES['cedula_responsable_file']['tmp_name'])) {
			echo json_encode(["ok"=>false, "msg"=>"Debe subir la cédula del responsable"]);
			exit();
		}

		// Validar que al menos hay 2 testigos con documentos
		$testigos_completos = 0;
		if (isset($_FILES['cedula_testigo1']) && is_uploaded_file($_FILES['cedula_testigo1']['tmp_name'])) $testigos_completos++;
		if (isset($_FILES['cedula_testigo2']) && is_uploaded_file($_FILES['cedula_testigo2']['tmp_name'])) $testigos_completos++;
		if (isset($_FILES['cedula_testigo3']) && is_uploaded_file($_FILES['cedula_testigo3']['tmp_name'])) $testigos_completos++;

		if ($testigos_completos < 2) {
			echo json_encode(["ok"=>false, "msg"=>"Debe subir al menos 2 cédulas de testigos"]);
			exit();
		}

		// Generar código si no existe
		if ($codigo === '') { 
			$codigo = 'DOC-RC-' . strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); 
		}

		// Crear directorio si no existe
		if (!file_exists("../files/documentos_registro_civil/")) {
			mkdir("../files/documentos_registro_civil/", 0777, true);
		}

		// Guardar cédula del difunto
		$ext1 = pathinfo($_FILES['cedula_difunto_file']['name'], PATHINFO_EXTENSION);
		$nombre_cedula_difunto = $codigo . '_cedula_difunto.' . $ext1;
		$ruta_cedula_difunto = "files/documentos_registro_civil/" . $nombre_cedula_difunto;
		if (!move_uploaded_file($_FILES['cedula_difunto_file']['tmp_name'], "../" . $ruta_cedula_difunto)) {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo guardar la cédula del difunto"]);
			exit();
		}

		// Guardar cédula del responsable
		$ext2 = pathinfo($_FILES['cedula_responsable_file']['name'], PATHINFO_EXTENSION);
		$nombre_cedula_responsable = $codigo . '_cedula_responsable.' . $ext2;
		$ruta_cedula_responsable = "files/documentos_registro_civil/" . $nombre_cedula_responsable;
		if (!move_uploaded_file($_FILES['cedula_responsable_file']['tmp_name'], "../" . $ruta_cedula_responsable)) {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo guardar la cédula del responsable"]);
			exit();
		}

		// Guardar cédulas de testigos (opcionales pero validados)
		$testigo1_ruta_cedula = "";
		$testigo2_ruta_cedula = "";
		$testigo3_ruta_cedula = "";

		if (isset($_FILES['cedula_testigo1']) && is_uploaded_file($_FILES['cedula_testigo1']['tmp_name'])) {
			$ext_t1 = pathinfo($_FILES['cedula_testigo1']['name'], PATHINFO_EXTENSION);
			$nombre_cedula_testigo1 = $codigo . '_cedula_testigo1.' . $ext_t1;
			$testigo1_ruta_cedula = "files/documentos_registro_civil/" . $nombre_cedula_testigo1;
			move_uploaded_file($_FILES['cedula_testigo1']['tmp_name'], "../" . $testigo1_ruta_cedula);
		}

		if (isset($_FILES['cedula_testigo2']) && is_uploaded_file($_FILES['cedula_testigo2']['tmp_name'])) {
			$ext_t2 = pathinfo($_FILES['cedula_testigo2']['name'], PATHINFO_EXTENSION);
			$nombre_cedula_testigo2 = $codigo . '_cedula_testigo2.' . $ext_t2;
			$testigo2_ruta_cedula = "files/documentos_registro_civil/" . $nombre_cedula_testigo2;
			move_uploaded_file($_FILES['cedula_testigo2']['tmp_name'], "../" . $testigo2_ruta_cedula);
		}

		if (isset($_FILES['cedula_testigo3']) && is_uploaded_file($_FILES['cedula_testigo3']['tmp_name'])) {
			$ext_t3 = pathinfo($_FILES['cedula_testigo3']['name'], PATHINFO_EXTENSION);
			$nombre_cedula_testigo3 = $codigo . '_cedula_testigo3.' . $ext_t3;
			$testigo3_ruta_cedula = "files/documentos_registro_civil/" . $nombre_cedula_testigo3;
			move_uploaded_file($_FILES['cedula_testigo3']['tmp_name'], "../" . $testigo3_ruta_cedula);
		}

		// Determinar si los documentos están completos (al menos 2 testigos con documentos)
		$documentos_completos = ($testigos_completos >= 2) ? 1 : 0;

		$idnew = $doc->insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $cedula_difunto, $ruta_cedula_difunto, $nombre_responsable, $cedula_responsable, $ruta_cedula_responsable, $telefono_responsable, $direccion_responsable, $testigo1_nombre, $testigo1_cedula, $testigo1_ruta_cedula, $testigo2_nombre, $testigo2_cedula, $testigo2_ruta_cedula, $testigo3_nombre, $testigo3_cedula, $testigo3_ruta_cedula, $documentos_completos, $observaciones);

		if ($idnew) {
			echo json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "documentos_completos"=>$documentos_completos]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo registrar el documento. Verifique los datos e intente nuevamente."]);
		}
	break;

	case 'listar':
		$rspta = $doc->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$estado_badge = '<span class="label label-warning">Pendiente</span>';
			if ($reg->estado == 'completo') {
				$estado_badge = '<span class="label label-success">Completo</span>';
			} elseif ($reg->estado == 'incompleto') {
				$estado_badge = '<span class="label label-danger">Incompleto</span>';
			}
			
			$completos_badge = $reg->documentos_completos ? '<span class="label label-success"><i class="fa fa-check"></i></span>' : '<span class="label label-danger"><i class="fa fa-times"></i></span>';

			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->iddocumento . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_difunto,
				"3" => $reg->nombre_responsable,
				"4" => $completos_badge,
				"5" => $estado_badge,
				"6" => $reg->usuario,
				"7" => $reg->fecha_creacion
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
		$rs = $doc->mostrar($iddocumento);
		echo json_encode($rs);
	break;

	case 'listarContratos':
		$rspta = $doc->listarContratos();
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
		$rspta = $doc->listarCotizaciones();
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

	case 'marcarCompletos':
		$resultado = $doc->marcarCompletos($iddocumento);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Documentos marcados como completos"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al marcar documentos como completos"]);
		}
	break;

	case 'desactivar':
		$rspta = $doc->desactivar($iddocumento);
		echo $rspta ? "Documento desactivado" : "Documento no se puede desactivar";
	break;
}
?>
