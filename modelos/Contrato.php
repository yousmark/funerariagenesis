<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class Contrato {

	public function __construct() {}

	public function insertar($codigo, $idusuario, $nombres_contratante, $apellidos_contratante, $cedula_contratante, $contacto_email, $contacto_celular, $direccion_contratante, $nombres_fallecido, $apellidos_fallecido, $fecha_fallecimiento, $edad_fallecido, $detalle_json, $total, $ruta_pdf, $hash_sha256, $firma_nombres, $firma_apellidos, $firma_fecha, $firma_ip) {
		$codigo = limpiarCadena($codigo);
		$nombres_contratante = limpiarCadena($nombres_contratante);
		$apellidos_contratante = limpiarCadena($apellidos_contratante);
		$cedula_contratante = limpiarCadena($cedula_contratante);
		$contacto_email = limpiarCadena($contacto_email);
		$contacto_celular = limpiarCadena($contacto_celular);
		$direccion_contratante = limpiarCadena($direccion_contratante);
		$nombres_fallecido = limpiarCadena($nombres_fallecido);
		$apellidos_fallecido = limpiarCadena($apellidos_fallecido);
		$ruta_pdf = limpiarCadena($ruta_pdf);
		$hash_sha256 = limpiarCadena($hash_sha256);
		$firma_nombres = limpiarCadena($firma_nombres);
		$firma_apellidos = limpiarCadena($firma_apellidos);
		$firma_ip = limpiarCadena($firma_ip);

		// Mantener compatibilidad con campos antiguos (concatenar nombres y apellidos)
		$nombre_contratante = trim($nombres_contratante . ' ' . $apellidos_contratante);
		$nombre_fallecido = trim($nombres_fallecido . ' ' . $apellidos_fallecido);
		$firma_nombre = trim($firma_nombres . ' ' . $firma_apellidos);

		// Para JSON no usar limpiarCadena porque puede corromper el JSON
		global $conexion;
		$detalle_json = mysqli_real_escape_string($conexion, $detalle_json);

		// Usar solo las columnas que existen actualmente en la base de datos
		// Las columnas nuevas se agregarán cuando se ejecute el script de normalización
		$sql = "INSERT INTO contrato (codigo, idusuario, nombre_contratante, cedula_contratante, contacto_email, contacto_celular, direccion_contratante, nombre_fallecido, fecha_fallecimiento, edad_fallecido, detalle_json, total, ruta_pdf, hash_sha256, firma_nombre, firma_fecha, firma_ip, estado, fecha_creacion)
				VALUES ('$codigo', '$idusuario', '$nombre_contratante', '$cedula_contratante', '$contacto_email', '$contacto_celular', '$direccion_contratante', '$nombre_fallecido', " . ($fecha_fallecimiento ? "'$fecha_fallecimiento'" : "NULL") . ", " . ($edad_fallecido ? "'$edad_fallecido'" : "NULL") . ", '$detalle_json', '$total', '$ruta_pdf', '$hash_sha256', '$firma_nombre', '$firma_fecha', '$firma_ip', 'vigente', NOW())";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($idcontrato) {
		$sql = "SELECT * FROM contrato WHERE idcontrato='$idcontrato'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarPorCodigo($codigo) {
		$codigo = limpiarCadena($codigo);
		$sql = "SELECT * FROM contrato WHERE codigo='$codigo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		// Usar solo las columnas que existen actualmente en la base de datos
		// Las columnas nuevas (nombres_contratante, apellidos_contratante) se agregarán cuando se ejecute el script de normalización
		$sql = "SELECT c.idcontrato, c.codigo,
				c.nombre_contratante,
				'' AS nombres_contratante,
				'' AS apellidos_contratante,
				c.total, c.estado, c.fecha_creacion,
				u.nombre AS usuario,
				'' AS usuario_nombres,
				'' AS usuario_apellidos
				FROM contrato c INNER JOIN usuario u ON c.idusuario=u.idusuario ORDER BY c.idcontrato DESC";
		return ejecutarConsulta($sql);
	}

	public function anular($idcontrato) {
		$sql = "UPDATE contrato SET estado='anulado' WHERE idcontrato='$idcontrato'";
		return ejecutarConsulta($sql);
	}

	// Funciones para manejar permisos de usuarios
	public function listarClientes() {
		// Listar usuarios registrados en lugar de clientes
		// Usar solo las columnas que existen actualmente
		$sql = "SELECT idusuario AS idcliente, nombre,
				'' AS nombres, 
				'' AS apellidos, 
				email, celular, direccion 
				FROM usuario WHERE condicion=1 
				ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function otorgarPermiso($idcontrato, $idusuario_permiso, $idusuario) {
		// Verificar si ya existe el permiso
		$sql_check = "SELECT idpermiso FROM contrato_permiso WHERE idcontrato='$idcontrato' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		$existe = ejecutarConsultaSimpleFila($sql_check);
		
		if ($existe) {
			return false; // Ya tiene permiso
		}
		
		$sql = "INSERT INTO contrato_permiso (idcontrato, idusuario_permiso, otorgado_por, fecha_otorgado, estado) 
				VALUES ('$idcontrato', '$idusuario_permiso', '$idusuario', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function quitarPermiso($idcontrato, $idusuario_permiso) {
		$sql = "UPDATE contrato_permiso SET estado='revocado' WHERE idcontrato='$idcontrato' AND idusuario_permiso='$idusuario_permiso'";
		return ejecutarConsulta($sql);
	}

	public function listarPermisos($idcontrato) {
		$sql = "SELECT cp.idpermiso, cp.idusuario_permiso AS idcliente, cp.fecha_otorgado, cp.estado, 
				u.nombre AS cliente_nombre,
				'' AS cliente_nombres, 
				'' AS cliente_apellidos, 
				u.email AS cliente_email, u.celular AS cliente_celular,
				ot.nombre AS otorgado_por_nombre,
				'' AS otorgado_por_nombres, 
				'' AS otorgado_por_apellidos
				FROM contrato_permiso cp
				INNER JOIN usuario u ON cp.idusuario_permiso = u.idusuario
				INNER JOIN usuario ot ON cp.otorgado_por = ot.idusuario
				WHERE cp.idcontrato='$idcontrato'
				ORDER BY cp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function verificarPermiso($idcontrato, $idusuario_permiso) {
		$sql = "SELECT * FROM contrato_permiso WHERE idcontrato='$idcontrato' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	// Funciones para módulo de clientes (usuarios que ven sus contratos)
	public function listarContratosPorUsuario($idusuario) {
		$sql = "SELECT DISTINCT c.idcontrato, c.codigo, 
				c.nombre_contratante,
				'' AS nombres_contratante, 
				'' AS apellidos_contratante, 
				c.total, c.estado, c.fecha_creacion,
				cp.fecha_otorgado, 
				u.nombre AS creado_por,
				'' AS creado_por_nombres, 
				'' AS creado_por_apellidos
				FROM contrato c
				INNER JOIN contrato_permiso cp ON c.idcontrato = cp.idcontrato
				INNER JOIN usuario u ON c.idusuario = u.idusuario
				WHERE cp.idusuario_permiso = '$idusuario' AND cp.estado = 'activo' AND c.estado = 'vigente'
				ORDER BY cp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function mostrarContratoUsuario($idcontrato, $idusuario) {
		// Verificar que el usuario tiene permiso para ver este contrato
		$sql = "SELECT c.*, cp.fecha_otorgado
				FROM contrato c
				INNER JOIN contrato_permiso cp ON c.idcontrato = cp.idcontrato
				WHERE c.idcontrato = '$idcontrato' 
				AND cp.idusuario_permiso = '$idusuario' 
				AND cp.estado = 'activo' 
				AND c.estado = 'vigente'";
		return ejecutarConsultaSimpleFila($sql);
	}
}
?>




