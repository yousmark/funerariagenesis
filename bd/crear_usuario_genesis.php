<?php
/**
 * Script para crear o actualizar el usuario genesis con contraseña 1234
 * Ejecutar desde el navegador o línea de comandos
 */

require_once "../config/global.php";
require_once "../config/Conexion.php";
require_once "../config/Seguridad.php";

$login = "genesis";
$clave = "1234";
$nombres = "Genesis";
$apellidos = "Admin";
$nombre = "Genesis Admin";
$celula = "0000000";
$direccion = "La Paz";
$celular = "00000000";
$email = "genesis@funeraria.com";
$cargo = "Administrador";
$imagen = "default.png";
$condicion = 1;

// Verificar si el usuario existe
$sql_check = "SELECT idusuario FROM usuario WHERE login='$login'";
$result = ejecutarConsultaSimpleFila($sql_check);

if ($result) {
    // Usuario existe, actualizar contraseña
    if (is_object($result)) {
        $idusuario = $result->idusuario;
    } else if (is_array($result) && isset($result['idusuario'])) {
        $idusuario = $result['idusuario'];
    } else {
        $idusuario = null;
    }
    
    if ($idusuario) {
        $clavehash = Seguridad::encriptarPassword($clave);
        
        // Verificar si existen las columnas nombres y apellidos
        $sql_check = "SHOW COLUMNS FROM usuario LIKE 'nombres'";
        $result_check = ejecutarConsulta($sql_check);
        $tieneNombres = ($result_check && $result_check->num_rows > 0);
        
        // Actualizar usuario
        if ($tieneNombres) {
            $sql = "UPDATE usuario SET 
                    nombres='$nombres', 
                    apellidos='$apellidos', 
                    nombre='$nombre',
                    clave='$clavehash', 
                    fecha_actualizacion_password=NOW(),
                    condicion=1
                    WHERE idusuario='$idusuario'";
        } else {
            $sql = "UPDATE usuario SET 
                    nombre='$nombre',
                    clave='$clavehash', 
                    fecha_actualizacion_password=NOW(),
                    condicion=1
                    WHERE idusuario='$idusuario'";
        }
        
        if (ejecutarConsulta($sql)) {
            echo "Usuario 'genesis' actualizado correctamente. Contraseña: 1234\n";
        } else {
            echo "Error al actualizar el usuario\n";
        }
    }
} else {
    // Usuario no existe, crearlo
    $clavehash = Seguridad::encriptarPassword($clave);
    
    // Verificar si existen las columnas nombres y apellidos
    $sql_check = "SHOW COLUMNS FROM usuario LIKE 'nombres'";
    $result_check = ejecutarConsulta($sql_check);
    $tieneNombres = ($result_check && $result_check->num_rows > 0);
    
    if ($tieneNombres) {
        $sql = "INSERT INTO usuario (nombres, apellidos, nombre, celula, direccion, celular, email, cargo, login, clave, imagen, condicion, fecha_actualizacion_password)
                VALUES ('$nombres', '$apellidos', '$nombre', '$celula', '$direccion', '$celular', '$email', '$cargo', '$login', '$clavehash', '$imagen', $condicion, NOW())";
    } else {
        $sql = "INSERT INTO usuario (nombre, celula, direccion, celular, email, cargo, login, clave, imagen, condicion, fecha_actualizacion_password)
                VALUES ('$nombre', '$celula', '$direccion', '$celular', '$email', '$cargo', '$login', '$clavehash', '$imagen', $condicion, NOW())";
    }
    
    $idusuario = ejecutarConsulta_retornarID($sql);
    
    if ($idusuario > 0) {
        // Asignar todos los permisos
        $permisos = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        foreach ($permisos as $idpermiso) {
            $sql_permiso = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES ('$idusuario', '$idpermiso')";
            ejecutarConsulta($sql_permiso);
        }
        
        echo "Usuario 'genesis' creado correctamente. Contraseña: 1234\n";
    } else {
        echo "Error al crear el usuario\n";
    }
}

?>

