<?php
// Incluir la conexión de base de datos
require "../config/Conexion.php";

class Usuario {

    // Constructor vacío
    public function __construct() {}

    // Insertar usuario con permisos
    public function insertar($nombres, $apellidos, $celula, $direccion, $celular, $email, $cargo, $login, $clave, $imagen, $permisos) {
        $nombres = limpiarCadena($nombres);
        $apellidos = limpiarCadena($apellidos);
        $celula = limpiarCadena($celula);
        $direccion = limpiarCadena($direccion);
        $celular = limpiarCadena($celular);
        $email = limpiarCadena($email);
        $cargo = limpiarCadena($cargo);
        $login = limpiarCadena($login);
        $clave = limpiarCadena($clave);
        $imagen = limpiarCadena($imagen);
        
        // Mantener compatibilidad con campo nombre (concatenar nombres y apellidos)
        $nombre = trim($nombres . ' ' . $apellidos);
        
        // Verificar si existen las columnas nombres y apellidos
        $sql_check = "SHOW COLUMNS FROM usuario LIKE 'nombres'";
        $result = ejecutarConsulta($sql_check);
        $tieneNombres = ($result && $result->num_rows > 0);
        
        if ($tieneNombres) {
            // Si existen las columnas nuevas, usarlas
            $sql = "INSERT INTO usuario (nombres, apellidos, nombre, celula, direccion, celular, email, cargo, login, clave, imagen, condicion, fecha_actualizacion_password)
                    VALUES ('$nombres', '$apellidos', '$nombre', '$celula', '$direccion', '$celular', '$email', '$cargo', '$login', '$clave', '$imagen', '1', NOW())";
        } else {
            // Si no existen, usar solo nombre (estructura antigua)
            $sql = "INSERT INTO usuario (nombre, celula, direccion, celular, email, cargo, login, clave, imagen, condicion, fecha_actualizacion_password)
                    VALUES ('$nombre', '$celula', '$direccion', '$celular', '$email', '$cargo', '$login', '$clave', '$imagen', '1', NOW())";
        }
        
        $idusuarionew = ejecutarConsulta_retornarID($sql);
        $sw = true;

        if (is_array($permisos)) {
            foreach ($permisos as $idpermiso) {
                $sql_detalle = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES('$idusuarionew','$idpermiso')";
                ejecutarConsulta($sql_detalle) or $sw = false;
            }
        }

        return $sw;
    }

    // Editar usuario con permisos
    public function editar($idusuario, $nombres, $apellidos, $celula, $direccion, $celular, $email, $cargo, $login, $clave, $imagen, $permisos) {
        $nombres = limpiarCadena($nombres);
        $apellidos = limpiarCadena($apellidos);
        $celula = limpiarCadena($celula);
        $direccion = limpiarCadena($direccion);
        $celular = limpiarCadena($celular);
        $email = limpiarCadena($email);
        $cargo = limpiarCadena($cargo);
        $login = limpiarCadena($login);
        $clave = limpiarCadena($clave);
        $imagen = limpiarCadena($imagen);
        
        // Mantener compatibilidad con campo nombre (concatenar nombres y apellidos)
        $nombre = trim($nombres . ' ' . $apellidos);
        
        // Verificar si existen las columnas nombres y apellidos
        $sql_check = "SHOW COLUMNS FROM usuario LIKE 'nombres'";
        $result = ejecutarConsulta($sql_check);
        $tieneNombres = ($result && $result->num_rows > 0);
        
        // Si se proporciona nueva contraseña, actualizarla
        if (!empty($clave)) {
            if ($tieneNombres) {
                $sql = "UPDATE usuario SET nombres='$nombres', apellidos='$apellidos', nombre='$nombre', celula='$celula', direccion='$direccion', celular='$celular',
                        email='$email', cargo='$cargo', login='$login', clave='$clave', imagen='$imagen', 
                        fecha_actualizacion_password=NOW() 
                        WHERE idusuario='$idusuario'";
            } else {
                $sql = "UPDATE usuario SET nombre='$nombre', celula='$celula', direccion='$direccion', celular='$celular',
                        email='$email', cargo='$cargo', login='$login', clave='$clave', imagen='$imagen', 
                        fecha_actualizacion_password=NOW() 
                        WHERE idusuario='$idusuario'";
            }
        } else {
            if ($tieneNombres) {
                $sql = "UPDATE usuario SET nombres='$nombres', apellidos='$apellidos', nombre='$nombre', celula='$celula', direccion='$direccion', celular='$celular',
                        email='$email', cargo='$cargo', login='$login', imagen='$imagen' 
                        WHERE idusuario='$idusuario'";
            } else {
                $sql = "UPDATE usuario SET nombre='$nombre', celula='$celula', direccion='$direccion', celular='$celular',
                        email='$email', cargo='$cargo', login='$login', imagen='$imagen' 
                        WHERE idusuario='$idusuario'";
            }
        }
        
        ejecutarConsulta($sql);

        // Eliminar permisos actuales
        $sqldel = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
        ejecutarConsulta($sqldel);

        $sw = true;
        if (is_array($permisos)) {
            foreach ($permisos as $idpermiso) {
                $sql_detalle = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES('$idusuario','$idpermiso')";
                ejecutarConsulta($sql_detalle) or $sw = false;
            }
        }

        return $sw;
    }

    public function desactivar($idusuario) {
        $sql = "UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function activar($idusuario) {
        $sql = "UPDATE usuario SET condicion='1' WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idusuario) {
        $sql = "SELECT * FROM usuario WHERE idusuario='$idusuario'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar() {
        $sql = "SELECT * FROM usuario";
        return ejecutarConsulta($sql);
    }

    public function listarmarcados($idusuario) {
        $sql = "SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function verificar($login) {
        $login = limpiarCadena($login);
        // Primero verificar si existen las columnas nombres y apellidos
        $sql_check = "SHOW COLUMNS FROM usuario LIKE 'nombres'";
        $result = ejecutarConsulta($sql_check);
        $tieneNombres = ($result && $result->num_rows > 0);
        
        if ($tieneNombres) {
            // Si existen las columnas nuevas, usarlas
            $sql = "SELECT idusuario, nombres, apellidos, nombre, celula, direccion, celular, email, cargo, login, imagen, clave, fecha_actualizacion_password 
                    FROM usuario WHERE login='$login' AND condicion='1'";
        } else {
            // Si no existen, usar solo nombre (estructura antigua)
            $sql = "SELECT idusuario, nombre, '' as nombres, '' as apellidos, celula, direccion, celular, email, cargo, login, imagen, clave, 
                    COALESCE(fecha_actualizacion_password, NULL) as fecha_actualizacion_password
                    FROM usuario WHERE login='$login' AND condicion='1'";
        }
        return ejecutarConsulta($sql);
    }
}
?>
