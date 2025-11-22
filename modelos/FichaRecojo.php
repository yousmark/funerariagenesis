<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class FichaRecojo {

	public function __construct() {}

	public function insertar($codigo, $idusuario, $nombre_difunto, $cedula_difunto, $edad_difunto, $fecha_fallecimiento, $lugar_fallecimiento, $medico_certificador, $causa_muerte, $direccion_recojo, $ciudad_recojo, $barrio_zona, $tipo_lugar, $condiciones_lugar, $temperatura_ambiente, $tiempo_trascurrido, $estado_general, $rigor_mortis, $livideces, $lesiones_visibles, $indumentaria, $observaciones_cuerpo, $equipo_recojo, $personal_presente, $insumos_bioseguridad, $protocolos_aplicados, $nombre_contacto, $telefono_contacto, $parentesco_contacto, $ruta_pdf, $hash_sha256, $fecha_recojo) {
		$codigo = limpiarCadena($codigo);
		$nombre_difunto = limpiarCadena($nombre_difunto);
		$cedula_difunto = limpiarCadena($cedula_difunto);
		$lugar_fallecimiento = limpiarCadena($lugar_fallecimiento);
		$medico_certificador = limpiarCadena($medico_certificador);
		$causa_muerte = limpiarCadena($causa_muerte);
		$direccion_recojo = limpiarCadena($direccion_recojo);
		$ciudad_recojo = limpiarCadena($ciudad_recojo);
		$barrio_zona = limpiarCadena($barrio_zona);
		$tipo_lugar = limpiarCadena($tipo_lugar);
		$condiciones_lugar = limpiarCadena($condiciones_lugar);
		$tiempo_trascurrido = limpiarCadena($tiempo_trascurrido);
		$estado_general = limpiarCadena($estado_general);
		$rigor_mortis = limpiarCadena($rigor_mortis);
		$livideces = limpiarCadena($livideces);
		$lesiones_visibles = limpiarCadena($lesiones_visibles);
		$indumentaria = limpiarCadena($indumentaria);
		$observaciones_cuerpo = limpiarCadena($observaciones_cuerpo);
		$equipo_recojo = limpiarCadena($equipo_recojo);
		$personal_presente = limpiarCadena($personal_presente);
		$insumos_bioseguridad = limpiarCadena($insumos_bioseguridad);
		$protocolos_aplicados = limpiarCadena($protocolos_aplicados);
		$nombre_contacto = limpiarCadena($nombre_contacto);
		$telefono_contacto = limpiarCadena($telefono_contacto);
		$parentesco_contacto = limpiarCadena($parentesco_contacto);
		$ruta_pdf = limpiarCadena($ruta_pdf);
		$hash_sha256 = limpiarCadena($hash_sha256);

		$sql = "INSERT INTO ficha_recojo (codigo, idusuario, nombre_difunto, cedula_difunto, edad_difunto, fecha_fallecimiento, lugar_fallecimiento, medico_certificador, causa_muerte, direccion_recojo, ciudad_recojo, barrio_zona, tipo_lugar, condiciones_lugar, temperatura_ambiente, tiempo_trascurrido, estado_general, rigor_mortis, livideces, lesiones_visibles, indumentaria, observaciones_cuerpo, equipo_recojo, personal_presente, insumos_bioseguridad, protocolos_aplicados, nombre_contacto, telefono_contacto, parentesco_contacto, ruta_pdf, hash_sha256, fecha_recojo, fecha_creacion, estado)
				VALUES ('$codigo', '$idusuario', '$nombre_difunto', '$cedula_difunto', '$edad_difunto', '$fecha_fallecimiento', '$lugar_fallecimiento', '$medico_certificador', '$causa_muerte', '$direccion_recojo', '$ciudad_recojo', '$barrio_zona', '$tipo_lugar', '$condiciones_lugar', " . ($temperatura_ambiente ? "'$temperatura_ambiente'" : "NULL") . ", '$tiempo_trascurrido', '$estado_general', '$rigor_mortis', '$livideces', '$lesiones_visibles', '$indumentaria', '$observaciones_cuerpo', '$equipo_recojo', '$personal_presente', '$insumos_bioseguridad', '$protocolos_aplicados', '$nombre_contacto', '$telefono_contacto', '$parentesco_contacto', '$ruta_pdf', '$hash_sha256', '$fecha_recojo', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($idficha) {
		$sql = "SELECT * FROM ficha_recojo WHERE idficha='$idficha'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		$sql = "SELECT f.idficha, f.codigo, f.nombre_difunto, f.fecha_recojo, f.fecha_creacion, f.estado, u.nombre AS usuario
				FROM ficha_recojo f INNER JOIN usuario u ON f.idusuario=u.idusuario ORDER BY f.idficha DESC";
		return ejecutarConsulta($sql);
	}

	public function desactivar($idficha) {
		$sql = "UPDATE ficha_recojo SET estado='inactivo' WHERE idficha='$idficha'";
		return ejecutarConsulta($sql);
	}

	// Funciones para manejar permisos de usuarios
	public function listarClientes() {
		// Listar usuarios registrados en lugar de clientes
		$sql = "SELECT idusuario AS idcliente, nombre, email, celular, direccion FROM usuario WHERE condicion=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	public function otorgarPermiso($idficha, $idusuario_permiso, $idusuario) {
		// Verificar si ya existe el permiso
		$sql_check = "SELECT idpermiso FROM ficha_recojo_permiso WHERE idficha='$idficha' AND idusuario_permiso='$idusuario_permiso' AND estado='activo'";
		$existe = ejecutarConsultaSimpleFila($sql_check);
		
		if ($existe) {
			return false; // Ya tiene permiso
		}
		
		$sql = "INSERT INTO ficha_recojo_permiso (idficha, idusuario_permiso, otorgado_por, fecha_otorgado, estado) 
				VALUES ('$idficha', '$idusuario_permiso', '$idusuario', NOW(), 'activo')";
		return ejecutarConsulta_retornarID($sql);
	}

	public function quitarPermiso($idficha, $idusuario_permiso) {
		$sql = "UPDATE ficha_recojo_permiso SET estado='revocado' WHERE idficha='$idficha' AND idusuario_permiso='$idusuario_permiso'";
		return ejecutarConsulta($sql);
	}

	public function listarPermisos($idficha) {
		$sql = "SELECT fp.idpermiso, fp.idusuario_permiso AS idcliente, fp.fecha_otorgado, fp.estado, 
				u.nombre AS cliente_nombre, u.email AS cliente_email, u.celular AS cliente_celular,
				ot.nombre AS otorgado_por_nombre
				FROM ficha_recojo_permiso fp
				INNER JOIN usuario u ON fp.idusuario_permiso = u.idusuario
				INNER JOIN usuario ot ON fp.otorgado_por = ot.idusuario
				WHERE fp.idficha='$idficha'
				ORDER BY fp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	// Funciones para módulo de clientes (usuarios que ven sus fichas de recojo)
	public function listarFichasPorUsuario($idusuario) {
		$sql = "SELECT DISTINCT f.idficha, f.codigo, f.nombre_difunto, f.fecha_recojo, f.estado, f.fecha_creacion,
				fp.fecha_otorgado, u.nombre AS creado_por
				FROM ficha_recojo f
				INNER JOIN ficha_recojo_permiso fp ON f.idficha = fp.idficha
				INNER JOIN usuario u ON f.idusuario = u.idusuario
				WHERE fp.idusuario_permiso = '$idusuario' AND fp.estado = 'activo' AND f.estado = 'activo'
				ORDER BY fp.fecha_otorgado DESC";
		return ejecutarConsulta($sql);
	}

	public function mostrarFichaUsuario($idficha, $idusuario) {
		// Verificar que el usuario tiene permiso para ver esta ficha
		$sql = "SELECT f.*, fp.fecha_otorgado
				FROM ficha_recojo f
				INNER JOIN ficha_recojo_permiso fp ON f.idficha = fp.idficha
				WHERE f.idficha = '$idficha' 
				AND fp.idusuario_permiso = '$idusuario' 
				AND fp.estado = 'activo' 
				AND f.estado = 'activo'";
		return ejecutarConsultaSimpleFila($sql);
	}
}
?>


