<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class ItinerarioFunerario {

	public function __construct() {}

	public function insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $nombre_familia, $telefono_contacto, $fecha_sepelio, $hora_sepelio, $lugar_sepelio, $direccion_sepelio, $ruta_cortejo, $punto_partida, $observaciones, $ruta_pdf, $hash_sha256) {
		$codigo = limpiarCadena($codigo);
		$nombre_difunto = limpiarCadena($nombre_difunto);
		$nombre_familia = limpiarCadena($nombre_familia);
		$telefono_contacto = limpiarCadena($telefono_contacto);
		$fecha_sepelio = limpiarCadena($fecha_sepelio);
		$hora_sepelio = limpiarCadena($hora_sepelio);
		$lugar_sepelio = limpiarCadena($lugar_sepelio);
		$direccion_sepelio = limpiarCadena($direccion_sepelio);
		$punto_partida = limpiarCadena($punto_partida);
		$ruta_pdf = limpiarCadena($ruta_pdf);
		$hash_sha256 = limpiarCadena($hash_sha256);
		global $conexion;
		$ruta_cortejo = mysqli_real_escape_string($conexion, $ruta_cortejo);
		$observaciones = mysqli_real_escape_string($conexion, $observaciones);

		// Manejar idcontrato e idcotizacion correctamente para NULL
		$idcontrato_val = ($idcontrato == "" || $idcontrato == "NULL" || empty($idcontrato)) ? "NULL" : "'" . limpiarCadena($idcontrato) . "'";
		$idcotizacion_val = ($idcotizacion == "" || $idcotizacion == "NULL" || empty($idcotizacion)) ? "NULL" : "'" . limpiarCadena($idcotizacion) . "'";

		$sql = "INSERT INTO itinerario_funerario (codigo, idcontrato, idcotizacion, idusuario, nombre_difunto, nombre_familia, telefono_contacto, fecha_sepelio, hora_sepelio, lugar_sepelio, direccion_sepelio, ruta_cortejo, punto_partida, observaciones, ruta_pdf, hash_sha256, estado, fecha_creacion)
				VALUES ('$codigo', $idcontrato_val, $idcotizacion_val, '$idusuario', '$nombre_difunto', '$nombre_familia', '$telefono_contacto', '$fecha_sepelio', '$hora_sepelio', '$lugar_sepelio', '$direccion_sepelio', '$ruta_cortejo', '$punto_partida', '$observaciones', '$ruta_pdf', '$hash_sha256', 'activo', NOW())";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($iditinerario) {
		$sql = "SELECT i.*, u.nombre AS usuario, co.codigo AS codigo_contrato, cot.codigo AS codigo_cotizacion
				FROM itinerario_funerario i
				INNER JOIN usuario u ON i.idusuario = u.idusuario
				LEFT JOIN contrato co ON i.idcontrato = co.idcontrato
				LEFT JOIN cotizacion cot ON i.idcotizacion = cot.idcotizacion
				WHERE i.iditinerario='$iditinerario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		$sql = "SELECT i.iditinerario, i.codigo, i.nombre_difunto, i.nombre_familia, i.fecha_sepelio, i.hora_sepelio, i.lugar_sepelio, i.confirmado_familia, i.estado, i.fecha_creacion, u.nombre AS usuario
				FROM itinerario_funerario i
				INNER JOIN usuario u ON i.idusuario = u.idusuario
				ORDER BY i.iditinerario DESC";
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

	public function listarClientes() {
		$sql = "SELECT idusuario AS idcliente, nombre, email, celular, direccion FROM usuario WHERE condicion=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function otorgarPermiso($iditinerario, $idusuario_permiso, $idusuario) {
		$sql_check = "SELECT idpermiso FROM itinerario_permiso WHERE iditinerario='$iditinerario' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		$existe = ejecutarConsultaSimpleFila($sql_check);
		
		if ($existe) {
			return false;
		}
		
		$sql = "INSERT INTO itinerario_permiso (iditinerario, idusuario_permiso, otorgado_por, fecha_otorgado, estado) 
				VALUES ('$iditinerario', '$idusuario_permiso', '$idusuario', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function quitarPermiso($iditinerario, $idusuario_permiso) {
		$sql = "UPDATE itinerario_permiso SET estado='revocado' WHERE iditinerario='$iditinerario' AND idusuario_permiso='$idusuario_permiso'";
		return ejecutarConsulta($sql);
	}

	public function listarPermisos($iditinerario) {
		$sql = "SELECT ip.idpermiso, ip.idusuario_permiso AS idcliente, ip.fecha_otorgado, ip.estado, 
				u.nombre AS cliente_nombre, u.email AS cliente_email, u.celular AS cliente_celular,
				ot.nombre AS otorgado_por_nombre
				FROM itinerario_permiso ip
				INNER JOIN usuario u ON ip.idusuario_permiso = u.idusuario
				INNER JOIN usuario ot ON ip.otorgado_por = ot.idusuario
				WHERE ip.iditinerario='$iditinerario'
				ORDER BY ip.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function confirmarFamilia($iditinerario, $ip) {
		$ip = limpiarCadena($ip);
		$sql = "UPDATE itinerario_funerario SET confirmado_familia=1, fecha_confirmacion_familia=NOW(), ip_confirmacion='$ip' WHERE iditinerario='$iditinerario'";
		return ejecutarConsulta($sql);
	}

	public function listarItinerariosPorUsuario($idusuario) {
		$sql = "SELECT DISTINCT i.iditinerario, i.codigo, i.nombre_difunto, i.nombre_familia, i.fecha_sepelio, i.hora_sepelio, i.lugar_sepelio, i.confirmado_familia, i.estado, i.fecha_creacion,
				ip.fecha_otorgado, u.nombre AS creado_por
				FROM itinerario_funerario i
				INNER JOIN itinerario_permiso ip ON i.iditinerario = ip.iditinerario
				INNER JOIN usuario u ON i.idusuario = u.idusuario
				WHERE ip.idusuario_permiso = '$idusuario' AND ip.estado = 'activo' AND i.estado = 'activo'
				ORDER BY ip.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function mostrarItinerarioUsuario($iditinerario, $idusuario) {
		$sql = "SELECT i.*, ip.fecha_otorgado
				FROM itinerario_funerario i
				INNER JOIN itinerario_permiso ip ON i.iditinerario = ip.iditinerario
				WHERE i.iditinerario = '$iditinerario' 
				AND ip.idusuario_permiso = '$idusuario' 
				AND ip.estado = 'activo' 
				AND i.estado = 'activo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function desactivar($iditinerario) {
		$sql = "UPDATE itinerario_funerario SET estado='inactivo' WHERE iditinerario='$iditinerario'";
		return ejecutarConsulta($sql);
	}
}
?>
