<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class ChecklistVelatorio {

	public function __construct() {}

	public function insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $nombre_contacto, $telefono_contacto, $lugar_velatorio, $fecha_velatorio, $ataud_tipo, $ataud_medidas, $ataud_observaciones, $decoracion_flores, $decoracion_flores_detalle, $decoracion_cortinas, $decoracion_cortinas_color, $decoracion_alfombra, $decoracion_alfombra_color, $decoracion_iluminacion, $decoracion_iluminacion_tipo, $mobiliario_catafalco, $mobiliario_catafalco_tipo, $mobiliario_sillas, $mobiliario_mesas, $mobiliario_bancos, $mobiliario_libro, $adicional_cafeteria, $adicional_reproduccion_musical, $adicional_pantallas, $adicional_pantallas_cantidad, $adicional_ventiladores, $adicional_ventiladores_cantidad, $adicional_extintores, $observaciones_generales, $ruta_pdf, $hash_sha256) {
		$codigo = limpiarCadena($codigo);
		$nombre_difunto = limpiarCadena($nombre_difunto);
		$nombre_contacto = limpiarCadena($nombre_contacto);
		$telefono_contacto = limpiarCadena($telefono_contacto);
		$lugar_velatorio = limpiarCadena($lugar_velatorio);
		$ataud_tipo = limpiarCadena($ataud_tipo);
		$ataud_medidas = limpiarCadena($ataud_medidas);
		$ataud_observaciones = limpiarCadena($ataud_observaciones);
		$decoracion_flores_detalle = limpiarCadena($decoracion_flores_detalle);
		$decoracion_cortinas_color = limpiarCadena($decoracion_cortinas_color);
		$decoracion_alfombra_color = limpiarCadena($decoracion_alfombra_color);
		$decoracion_iluminacion_tipo = limpiarCadena($decoracion_iluminacion_tipo);
		$mobiliario_catafalco_tipo = limpiarCadena($mobiliario_catafalco_tipo);
		$observaciones_generales = limpiarCadena($observaciones_generales);
		$ruta_pdf = limpiarCadena($ruta_pdf);
		$hash_sha256 = limpiarCadena($hash_sha256);
		$decoracion_flores = limpiarCadena($decoracion_flores);
		$decoracion_cortinas = limpiarCadena($decoracion_cortinas);
		$decoracion_alfombra = limpiarCadena($decoracion_alfombra);
		$decoracion_iluminacion = limpiarCadena($decoracion_iluminacion);
		$mobiliario_catafalco = limpiarCadena($mobiliario_catafalco);
		$mobiliario_libro = limpiarCadena($mobiliario_libro);
		$adicional_cafeteria = limpiarCadena($adicional_cafeteria);
		$adicional_reproduccion_musical = limpiarCadena($adicional_reproduccion_musical);
		$adicional_pantallas = limpiarCadena($adicional_pantallas);
		$adicional_ventiladores = limpiarCadena($adicional_ventiladores);
		$adicional_extintores = limpiarCadena($adicional_extintores);
		$mobiliario_sillas = limpiarCadena($mobiliario_sillas);
		$mobiliario_mesas = limpiarCadena($mobiliario_mesas);
		$mobiliario_bancos = limpiarCadena($mobiliario_bancos);
		$adicional_pantallas_cantidad = limpiarCadena($adicional_pantallas_cantidad);
		$adicional_ventiladores_cantidad = limpiarCadena($adicional_ventiladores_cantidad);

		$sql = "INSERT INTO checklist_velatorio (codigo, idcontrato, idcotizacion, idusuario, nombre_difunto, nombre_contacto, telefono_contacto, lugar_velatorio, fecha_velatorio, ataud_tipo, ataud_medidas, ataud_observaciones, decoracion_flores, decoracion_flores_detalle, decoracion_cortinas, decoracion_cortinas_color, decoracion_alfombra, decoracion_alfombra_color, decoracion_iluminacion, decoracion_iluminacion_tipo, mobiliario_catafalco, mobiliario_catafalco_tipo, mobiliario_sillas, mobiliario_mesas, mobiliario_bancos, mobiliario_libro, adicional_cafeteria, adicional_reproduccion_musical, adicional_pantallas, adicional_pantallas_cantidad, adicional_ventiladores, adicional_ventiladores_cantidad, adicional_extintores, observaciones_generales, confirmado_familia, fecha_confirmacion_familia, ruta_pdf, hash_sha256, fecha_creacion, estado)
				VALUES ('$codigo', " . ($idcontrato ? "'$idcontrato'" : "NULL") . ", " . ($idcotizacion ? "'$idcotizacion'" : "NULL") . ", '$idusuario', '$nombre_difunto', '$nombre_contacto', '$telefono_contacto', '$lugar_velatorio', '$fecha_velatorio', '$ataud_tipo', '$ataud_medidas', '$ataud_observaciones', '$decoracion_flores', '$decoracion_flores_detalle', '$decoracion_cortinas', '$decoracion_cortinas_color', '$decoracion_alfombra', '$decoracion_alfombra_color', '$decoracion_iluminacion', '$decoracion_iluminacion_tipo', '$mobiliario_catafalco', '$mobiliario_catafalco_tipo', '$mobiliario_sillas', '$mobiliario_mesas', '$mobiliario_bancos', '$mobiliario_libro', '$adicional_cafeteria', '$adicional_reproduccion_musical', '$adicional_pantallas', '$adicional_pantallas_cantidad', '$adicional_ventiladores', '$adicional_ventiladores_cantidad', '$adicional_extintores', '$observaciones_generales', '0', NULL, '$ruta_pdf', '$hash_sha256', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($idchecklist) {
		$sql = "SELECT c.*, co.codigo AS codigo_contrato, cot.codigo AS codigo_cotizacion
				FROM checklist_velatorio c
				LEFT JOIN contrato co ON c.idcontrato = co.idcontrato
				LEFT JOIN cotizacion cot ON c.idcotizacion = cot.idcotizacion
				WHERE c.idchecklist='$idchecklist'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		$sql = "SELECT c.idchecklist, c.codigo, c.nombre_difunto, c.nombre_contacto, c.fecha_velatorio, c.fecha_creacion, c.estado, u.nombre AS usuario
				FROM checklist_velatorio c INNER JOIN usuario u ON c.idusuario=u.idusuario ORDER BY c.idchecklist DESC";
		return ejecutarConsulta($sql);
	}

	public function listarContratos() {
		$sql = "SELECT idcontrato, codigo, nombre_contratante FROM contrato WHERE estado='vigente' ORDER BY idcontrato DESC";
		return ejecutarConsulta($sql);
	}

	public function listarCotizaciones() {
		$sql = "SELECT idcotizacion, codigo, nombre_familia FROM cotizacion WHERE estado='vigente' ORDER BY idcotizacion DESC";
		return ejecutarConsulta($sql);
	}

	public function confirmarFamilia($idchecklist) {
		$sql = "UPDATE checklist_velatorio SET confirmado_familia=1, fecha_confirmacion_familia=NOW() WHERE idchecklist='$idchecklist'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idchecklist) {
		$sql = "UPDATE checklist_velatorio SET estado='inactivo' WHERE idchecklist='$idchecklist'";
		return ejecutarConsulta($sql);
	}
}
?>

