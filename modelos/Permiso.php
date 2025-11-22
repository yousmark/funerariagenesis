<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Permiso{ 

	//implementamos nuestro constructor
	public function __construct(){

	}

	//listar registros de permisos disponibles
	public function listar(){
		$sql="SELECT * FROM permiso ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	//listar usuarios con sus permisos
	public function listarUsuarios(){
		$sql="SELECT u.idusuario, u.nombre, u.email, u.cargo, u.condicion,
				GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') as permisos
				FROM usuario u
				LEFT JOIN usuario_permiso up ON u.idusuario = up.idusuario
				LEFT JOIN permiso p ON up.idpermiso = p.idpermiso
				GROUP BY u.idusuario
				ORDER BY u.nombre ASC";
		return ejecutarConsulta($sql);
	}

	//listar permisos de un usuario específico
	public function listarPermisosUsuario($idusuario){
		$sql="SELECT up.idusuario_permiso, up.idpermiso, p.nombre 
				FROM usuario_permiso up
				INNER JOIN permiso p ON up.idpermiso = p.idpermiso
				WHERE up.idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//listar todos los permisos disponibles (para checkboxes)
	public function listarTodosPermisos(){
		$sql="SELECT * FROM permiso ORDER BY nombre ASC";
		return ejecutarConsulta($sql);
	}

	//asignar permiso a usuario
	public function asignarPermiso($idusuario, $idpermiso){
		// Verificar si ya existe
		$sql_check = "SELECT idusuario_permiso FROM usuario_permiso WHERE idusuario='$idusuario' AND idpermiso='$idpermiso'";
		$existe = ejecutarConsultaSimpleFila($sql_check);
		
		if ($existe) {
			return false; // Ya tiene el permiso
		}
		
		$sql = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES ('$idusuario', '$idpermiso')";
		return ejecutarConsulta($sql);
	}

	//quitar permiso de usuario
	public function quitarPermiso($idusuario, $idpermiso){
		$sql = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario' AND idpermiso='$idpermiso'";
		return ejecutarConsulta($sql);
	}

	//actualizar todos los permisos de un usuario
	public function actualizarPermisosUsuario($idusuario, $permisos){
		// Eliminar todos los permisos actuales
		$sqldel = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqldel);

		$sw = true;
		if (is_array($permisos) && count($permisos) > 0) {
			foreach ($permisos as $idpermiso) {
				$sql_detalle = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES('$idusuario','$idpermiso')";
				ejecutarConsulta($sql_detalle) or $sw = false;
			}
		}

		return $sw;
	}
}

 ?>
