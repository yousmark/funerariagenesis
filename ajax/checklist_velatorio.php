<?php 
session_start();
require_once "../modelos/ChecklistVelatorio.php";

$checklist = new ChecklistVelatorio();

// Recibir todos los campos del formulario
$idchecklist = isset($_POST["idchecklist"]) ? limpiarCadena($_POST["idchecklist"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$idcontrato = isset($_POST["idcontrato"]) ? limpiarCadena($_POST["idcontrato"]) : "";
$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$nombre_difunto = isset($_POST["nombre_difunto"]) ? limpiarCadena($_POST["nombre_difunto"]) : "";
$nombre_contacto = isset($_POST["nombre_contacto"]) ? limpiarCadena($_POST["nombre_contacto"]) : "";
$telefono_contacto = isset($_POST["telefono_contacto"]) ? limpiarCadena($_POST["telefono_contacto"]) : "";
$lugar_velatorio = isset($_POST["lugar_velatorio"]) ? limpiarCadena($_POST["lugar_velatorio"]) : "";
$fecha_velatorio = isset($_POST["fecha_velatorio"]) ? limpiarCadena($_POST["fecha_velatorio"]) : "";
$ataud_tipo = isset($_POST["ataud_tipo"]) ? limpiarCadena($_POST["ataud_tipo"]) : "";
$ataud_medidas = isset($_POST["ataud_medidas"]) ? limpiarCadena($_POST["ataud_medidas"]) : "";
$ataud_observaciones = isset($_POST["ataud_observaciones"]) ? limpiarCadena($_POST["ataud_observaciones"]) : "";
$decoracion_flores = isset($_POST["decoracion_flores"]) ? limpiarCadena($_POST["decoracion_flores"]) : "No";
$decoracion_flores_detalle = isset($_POST["decoracion_flores_detalle"]) ? limpiarCadena($_POST["decoracion_flores_detalle"]) : "";
$decoracion_cortinas = isset($_POST["decoracion_cortinas"]) ? limpiarCadena($_POST["decoracion_cortinas"]) : "No";
$decoracion_cortinas_color = isset($_POST["decoracion_cortinas_color"]) ? limpiarCadena($_POST["decoracion_cortinas_color"]) : "";
$decoracion_alfombra = isset($_POST["decoracion_alfombra"]) ? limpiarCadena($_POST["decoracion_alfombra"]) : "No";
$decoracion_alfombra_color = isset($_POST["decoracion_alfombra_color"]) ? limpiarCadena($_POST["decoracion_alfombra_color"]) : "";
$decoracion_iluminacion = isset($_POST["decoracion_iluminacion"]) ? limpiarCadena($_POST["decoracion_iluminacion"]) : "Si";
$decoracion_iluminacion_tipo = isset($_POST["decoracion_iluminacion_tipo"]) ? limpiarCadena($_POST["decoracion_iluminacion_tipo"]) : "";
$mobiliario_catafalco = isset($_POST["mobiliario_catafalco"]) ? limpiarCadena($_POST["mobiliario_catafalco"]) : "No";
$mobiliario_catafalco_tipo = isset($_POST["mobiliario_catafalco_tipo"]) ? limpiarCadena($_POST["mobiliario_catafalco_tipo"]) : "";
$mobiliario_sillas = isset($_POST["mobiliario_sillas"]) ? limpiarCadena($_POST["mobiliario_sillas"]) : "0";
$mobiliario_mesas = isset($_POST["mobiliario_mesas"]) ? limpiarCadena($_POST["mobiliario_mesas"]) : "0";
$mobiliario_bancos = isset($_POST["mobiliario_bancos"]) ? limpiarCadena($_POST["mobiliario_bancos"]) : "0";
$mobiliario_libro = isset($_POST["mobiliario_libro"]) ? limpiarCadena($_POST["mobiliario_libro"]) : "No";
$adicional_cafeteria = isset($_POST["adicional_cafeteria"]) ? limpiarCadena($_POST["adicional_cafeteria"]) : "No";
$adicional_reproduccion_musical = isset($_POST["adicional_reproduccion_musical"]) ? limpiarCadena($_POST["adicional_reproduccion_musical"]) : "No";
$adicional_pantallas = isset($_POST["adicional_pantallas"]) ? limpiarCadena($_POST["adicional_pantallas"]) : "No";
$adicional_pantallas_cantidad = isset($_POST["adicional_pantallas_cantidad"]) ? limpiarCadena($_POST["adicional_pantallas_cantidad"]) : "0";
$adicional_ventiladores = isset($_POST["adicional_ventiladores"]) ? limpiarCadena($_POST["adicional_ventiladores"]) : "No";
$adicional_ventiladores_cantidad = isset($_POST["adicional_ventiladores_cantidad"]) ? limpiarCadena($_POST["adicional_ventiladores_cantidad"]) : "0";
$adicional_extintores = isset($_POST["adicional_extintores"]) ? limpiarCadena($_POST["adicional_extintores"]) : "No";
$observaciones_generales = isset($_POST["observaciones_generales"]) ? limpiarCadena($_POST["observaciones_generales"]) : "";
$hash_sha256 = isset($_POST["hash_sha256"]) ? limpiarCadena($_POST["hash_sha256"]) : "";

switch ($_GET["op"]) {

	case 'guardar':
		$ruta_pdf = '';
		if (isset($_FILES['pdf']) && is_uploaded_file($_FILES['pdf']['tmp_name'])) {
			$ext = explode('.', $_FILES['pdf']['name']);
			$nombre_archivo = $codigo !== '' ? $codigo : round(microtime(true));
			$nombre_archivo = $nombre_archivo . '.pdf';
			$ruta_destino = "../files/checklist_velatorio/" . $nombre_archivo;
			if (!file_exists("../files/checklist_velatorio/")) {
				mkdir("../files/checklist_velatorio/", 0777, true);
			}
			if (move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_destino)) {
				$ruta_pdf = "files/checklist_velatorio/" . $nombre_archivo;
			} else {
				echo json_encode(["ok"=>false, "msg"=>"No se pudo guardar el PDF"]);
				exit();
			}
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Archivo PDF no recibido"]);
			exit();
		}

		$idusuario = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : 0;
		if ($idusuario == 0) { 
			echo json_encode(["ok"=>false, "msg"=>"Sesión expirada"]); 
			exit(); 
		}

		if ($codigo === '') { $codigo = 'CLV-' . strtoupper(substr(hash('sha256', uniqid('', true)), 0, 8)); }

		$idnew = $checklist->insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $nombre_contacto, $telefono_contacto, $lugar_velatorio, $fecha_velatorio, $ataud_tipo, $ataud_medidas, $ataud_observaciones, $decoracion_flores, $decoracion_flores_detalle, $decoracion_cortinas, $decoracion_cortinas_color, $decoracion_alfombra, $decoracion_alfombra_color, $decoracion_iluminacion, $decoracion_iluminacion_tipo, $mobiliario_catafalco, $mobiliario_catafalco_tipo, $mobiliario_sillas, $mobiliario_mesas, $mobiliario_bancos, $mobiliario_libro, $adicional_cafeteria, $adicional_reproduccion_musical, $adicional_pantallas, $adicional_pantallas_cantidad, $adicional_ventiladores, $adicional_ventiladores_cantidad, $adicional_extintores, $observaciones_generales, $ruta_pdf, $hash_sha256);
		if ($idnew) {
			echo json_encode(["ok"=>true, "id"=>$idnew, "codigo"=>$codigo, "ruta_pdf"=>$ruta_pdf]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"No se pudo registrar la checklist. Verifique los datos e intente nuevamente."]);
		}
	break;

	case 'listar':
		$rspta = $checklist->listar();
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-info" onclick="ver(' . $reg->idchecklist . ')"><i class="fa fa-eye"></i></button>',
				"1" => $reg->codigo,
				"2" => $reg->nombre_difunto,
				"3" => $reg->nombre_contacto,
				"4" => $reg->usuario,
				"5" => $reg->fecha_velatorio,
				"6" => $reg->estado,
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
		$rs = $checklist->mostrar($idchecklist);
		echo json_encode($rs);
	break;

	case 'listarContratos':
		$rspta = $checklist->listarContratos();
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
		$rspta = $checklist->listarCotizaciones();
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

	case 'confirmarFamilia':
		$id = $idchecklist;
		if (empty($id)) { echo json_encode(["ok"=>false, "msg"=>"ID inválido"]); exit(); }
		$resultado = $checklist->confirmarFamilia($id);
		if ($resultado) {
			echo json_encode(["ok"=>true, "msg"=>"Checklist confirmada por la familia"]);
		} else {
			echo json_encode(["ok"=>false, "msg"=>"Error al confirmar"]);
		}
	break;

}
?>

