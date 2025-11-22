<?php
/**
 * Clase para funciones de seguridad
 */
class Seguridad {
	
	/**
	 * Encriptar contraseña usando password_hash (bcrypt)
	 */
	public static function encriptarPassword($password) {
		// Usar bcrypt con cost 12 (balance entre seguridad y rendimiento)
		return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
	}
	
	/**
	 * Verificar contraseña
	 */
	public static function verificarPassword($password, $hash) {
		return password_verify($password, $hash);
	}
	
	/**
	 * Generar token seguro
	 */
	public static function generarToken($longitud = 32) {
		return bin2hex(random_bytes($longitud));
	}
	
	/**
	 * Verificar si la contraseña necesita ser actualizada
	 * (por ejemplo, si fue creada con algoritmo antiguo)
	 */
	public static function passwordNecesitaRehash($hash) {
		return password_needs_rehash($hash, PASSWORD_BCRYPT, array('cost' => 12));
	}
	
	/**
	 * Verificar si la sesión ha expirado
	 */
	public static function sesionExpirada($tiempoUltimaActividad = null, $tiempoExpiracion = 3600) {
		if ($tiempoUltimaActividad === null) {
			$tiempoUltimaActividad = isset($_SESSION['ultima_actividad']) ? $_SESSION['ultima_actividad'] : time();
		}
		
		if ((time() - $tiempoUltimaActividad) > $tiempoExpiracion) {
			return true;
		}
		
		// Actualizar última actividad
		$_SESSION['ultima_actividad'] = time();
		return false;
	}
	
	/**
	 * Verificar si la contraseña ha expirado
	 */
	public static function passwordExpirada($fechaUltimaActualizacion, $diasExpiracion = 90) {
		// Si no hay fecha de actualización, no considerar expirada (para permitir migración)
		if (empty($fechaUltimaActualizacion) || $fechaUltimaActualizacion === null) {
			return false;
		}
		
		$fechaExpira = strtotime($fechaUltimaActualizacion . " +$diasExpiracion days");
		return time() > $fechaExpira;
	}
	
	/**
	 * Generar hash SHA-256 para integridad de archivos
	 */
	public static function hashArchivo($contenido) {
		return hash('sha256', $contenido);
	}
	
	/**
	 * Verificar integridad de archivo
	 */
	public static function verificarIntegridad($contenido, $hashEsperado) {
		$hashCalculado = self::hashArchivo($contenido);
		return hash_equals($hashEsperado, $hashCalculado);
	}
	
	/**
	 * Limpiar datos de entrada para prevenir SQL Injection
	 */
	public static function limpiarEntrada($dato) {
		global $conexion;
		if (isset($conexion)) {
			return mysqli_real_escape_string($conexion, trim($dato));
		}
		return trim($dato);
	}
	
	/**
	 * Validar nivel de acceso
	 */
	public static function tieneAcceso($nivelRequerido, $nivelUsuario) {
		// Niveles: 1=Admin, 2=Gerente, 3=Empleado, 4=Cliente
		$niveles = array(
			'admin' => 1,
			'gerente' => 2,
			'empleado' => 3,
			'cliente' => 4
		);
		
		$nivelReq = isset($niveles[$nivelRequerido]) ? $niveles[$nivelRequerido] : 999;
		$nivelUsr = isset($niveles[$nivelUsuario]) ? $niveles[$nivelUsuario] : 999;
		
		return $nivelUsr <= $nivelReq;
	}
	
	/**
	 * Registrar intento de acceso fallido
	 */
	public static function registrarIntentoFallido($login, $ip) {
		$archivo = '../logs/intentos_fallidos.log';
		$fecha = date('Y-m-d H:i:s');
		$linea = "[$fecha] Login: $login | IP: $ip\n";
		file_put_contents($archivo, $linea, FILE_APPEND | LOCK_EX);
	}
	
	/**
	 * Verificar si hay demasiados intentos fallidos
	 */
	public static function verificarIntentosFallidos($login, $maxIntentos = 5, $tiempoBloqueo = 900) {
		$archivo = '../logs/intentos_fallidos.log';
		if (!file_exists($archivo)) {
			return false;
		}
		
		$lineas = file($archivo);
		$intentos = 0;
		$tiempoActual = time();
		
		// Contar intentos en los últimos X segundos
		foreach (array_reverse($lineas) as $linea) {
			if (strpos($linea, "Login: $login") !== false) {
				preg_match('/\[(.+?)\]/', $linea, $matches);
				if (isset($matches[1])) {
					$tiempoIntento = strtotime($matches[1]);
					if (($tiempoActual - $tiempoIntento) < $tiempoBloqueo) {
						$intentos++;
					} else {
						break;
					}
				}
			}
		}
		
		return $intentos >= $maxIntentos;
	}
}

?>


