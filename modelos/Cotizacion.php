<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class Cotizacion {

	public function __construct() {}

	public function insertar($codigo, $idusuario, $nombres_familia, $apellidos_familia, $contacto_email, $contacto_celular, $detalle_json, $total, $ruta_pdf, $hash_sha256, $firma_nombres, $firma_apellidos, $firma_fecha, $firma_ip) {
		$codigo = limpiarCadena($codigo);
		$nombres_familia = limpiarCadena($nombres_familia);
		$apellidos_familia = limpiarCadena($apellidos_familia);
		$contacto_email = limpiarCadena($contacto_email);
		$contacto_celular = limpiarCadena($contacto_celular);
		$ruta_pdf = limpiarCadena($ruta_pdf);
		$hash_sha256 = limpiarCadena($hash_sha256);
		$firma_nombres = limpiarCadena($firma_nombres);
		$firma_apellidos = limpiarCadena($firma_apellidos);
		$firma_ip = limpiarCadena($firma_ip);

		// Mantener compatibilidad con campos antiguos (concatenar nombres y apellidos)
		$nombre_familia = trim($nombres_familia . ' ' . $apellidos_familia);
		$firma_nombre = trim($firma_nombres . ' ' . $firma_apellidos);

		// Para JSON no usar limpiarCadena porque puede corromper el JSON
		global $conexion;
		$detalle_json = mysqli_real_escape_string($conexion, $detalle_json);

		// Verificar si las columnas nuevas existen antes de insertarlas
		// Por ahora, usar solo las columnas antiguas para compatibilidad
		$sql = "INSERT INTO cotizacion (codigo,idusuario,nombre_familia,contacto_email,contacto_celular,detalle_json,total,ruta_pdf,hash_sha256,firma_nombre,firma_fecha,firma_ip,estado,fecha_creacion)
				VALUES ('$codigo','$idusuario','$nombre_familia','$contacto_email','$contacto_celular', '$detalle_json', '$total', '$ruta_pdf', '$hash_sha256', '$firma_nombre', '$firma_fecha', '$firma_ip', 'vigente', NOW())";
		return ejecutarConsulta_retornarID($sql);
	}

	public function registrarAcceso($idcotizacion, $token, $expira_en) {
		$token = limpiarCadena($token);
		$sql = "INSERT INTO cotizacion_acceso (idcotizacion,token,expira_en,creado_en) VALUES ('$idcotizacion','$token','$expira_en', NOW())";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($idcotizacion) {
		$sql = "SELECT * FROM cotizacion WHERE idcotizacion='$idcotizacion'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarPorCodigo($codigo) {
		$codigo = limpiarCadena($codigo);
		$sql = "SELECT * FROM cotizacion WHERE codigo='$codigo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		// Usar solo las columnas que existen actualmente en la base de datos
		// Las columnas nuevas (nombres_familia, apellidos_familia) se agregarán cuando se ejecute el script de normalización
		$sql = "SELECT c.idcotizacion,c.codigo,
				c.nombre_familia,
				'' AS nombres_familia,
				'' AS apellidos_familia,
				c.total,c.estado,c.fecha_creacion,
				u.nombre AS usuario,
				'' AS usuario_nombres,
				'' AS usuario_apellidos
				FROM cotizacion c INNER JOIN usuario u ON c.idusuario=u.idusuario ORDER BY c.idcotizacion DESC";
		return ejecutarConsulta($sql);
	}

	public function anular($idcotizacion) {
		$sql = "UPDATE cotizacion SET estado='anulada' WHERE idcotizacion='$idcotizacion'";
		return ejecutarConsulta($sql);
	}

	// Funciones para manejar permisos de usuarios
	public function listarClientes() {
		// Listar usuarios registrados en lugar de clientes
		// Usar solo las columnas que existen actualmente
		$sql = "SELECT idusuario AS idcliente, 
				nombre,
				'' AS nombres, 
				'' AS apellidos, 
				email, celular, direccion 
				FROM usuario WHERE condicion=1 
				ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function otorgarPermiso($idcotizacion, $idusuario_permiso, $idusuario) {
		// Verificar si ya existe el permiso
		$sql_check = "SELECT idpermiso FROM cotizacion_permiso WHERE idcotizacion='$idcotizacion' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		$existe = ejecutarConsultaSimpleFila($sql_check);
		
		if ($existe) {
			return false; // Ya tiene permiso
		}
		
		$sql = "INSERT INTO cotizacion_permiso (idcotizacion, idusuario_permiso, otorgado_por, fecha_otorgado, estado) 
				VALUES ('$idcotizacion', '$idusuario_permiso', '$idusuario', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function quitarPermiso($idcotizacion, $idusuario_permiso) {
		$sql = "UPDATE cotizacion_permiso SET estado='revocado' WHERE idcotizacion='$idcotizacion' AND idusuario_permiso='$idusuario_permiso'";
		return ejecutarConsulta($sql);
	}

	public function listarPermisos($idcotizacion) {
		$sql = "SELECT cp.idpermiso, cp.idusuario_permiso AS idcliente, cp.fecha_otorgado, cp.estado, 
				u.nombre AS cliente_nombre,
				'' AS cliente_nombres, 
				'' AS cliente_apellidos, 
				u.email AS cliente_email, u.celular AS cliente_celular,
				ot.nombre AS otorgado_por_nombre,
				'' AS otorgado_por_nombres, 
				'' AS otorgado_por_apellidos
				FROM cotizacion_permiso cp
				INNER JOIN usuario u ON cp.idusuario_permiso = u.idusuario
				INNER JOIN usuario ot ON cp.otorgado_por = ot.idusuario
				WHERE cp.idcotizacion='$idcotizacion'
				ORDER BY cp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function verificarPermiso($idcotizacion, $idusuario_permiso) {
		$sql = "SELECT * FROM cotizacion_permiso WHERE idcotizacion='$idcotizacion' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	// Funciones para módulo de clientes (usuarios que ven sus cotizaciones)
	public function listarCotizacionesPorUsuario($idusuario) {
		$sql = "SELECT DISTINCT c.idcotizacion, c.codigo, 
				c.nombre_familia,
				'' AS nombres_familia, 
				'' AS apellidos_familia, 
				c.total, c.estado, c.fecha_creacion,
				cp.fecha_otorgado, 
				u.nombre AS creado_por,
				'' AS creado_por_nombres, 
				'' AS creado_por_apellidos
				FROM cotizacion c
				INNER JOIN cotizacion_permiso cp ON c.idcotizacion = cp.idcotizacion
				INNER JOIN usuario u ON c.idusuario = u.idusuario
				WHERE cp.idusuario_permiso = '$idusuario' AND cp.estado = 'activo' AND c.estado = 'vigente'
				ORDER BY cp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function mostrarCotizacionUsuario($idcotizacion, $idusuario) {
		// Verificar que el usuario tiene permiso para ver esta cotización
		$sql = "SELECT c.*, cp.fecha_otorgado
				FROM cotizacion c
				INNER JOIN cotizacion_permiso cp ON c.idcotizacion = cp.idcotizacion
				WHERE c.idcotizacion = '$idcotizacion' 
				AND cp.idusuario_permiso = '$idusuario' 
				AND cp.estado = 'activo' 
				AND c.estado = 'vigente'";
		return ejecutarConsultaSimpleFila($sql);
	}
}
?>


