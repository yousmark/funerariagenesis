<?php
/**
 * Clase para validación de datos
 */
class Validacion {
	
	/**
	 * Validar contraseña según política de seguridad
	 * - Mínimo 8 caracteres
	 * - Al menos una mayúscula
	 * - Al menos una minúscula
	 * - Al menos un número
	 * - Al menos un carácter especial
	 */
	public static function validarPassword($password) {
		$errores = array();
		
		if (strlen($password) < 8) {
			$errores[] = "La contraseña debe tener al menos 8 caracteres";
		}
		
		if (!preg_match('/[A-Z]/', $password)) {
			$errores[] = "La contraseña debe contener al menos una letra mayúscula";
		}
		
		if (!preg_match('/[a-z]/', $password)) {
			$errores[] = "La contraseña debe contener al menos una letra minúscula";
		}
		
		if (!preg_match('/[0-9]/', $password)) {
			$errores[] = "La contraseña debe contener al menos un número";
		}
		
		if (!preg_match('/[^A-Za-z0-9]/', $password)) {
			$errores[] = "La contraseña debe contener al menos un carácter especial";
		}
		
		return array(
			'valida' => empty($errores),
			'errores' => $errores
		);
	}
	
	/**
	 * Validar email
	 */
	public static function validarEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	/**
	 * Validar teléfono/celular (solo números)
	 */
	public static function validarTelefono($telefono) {
		return preg_match('/^[0-9]{7,15}$/', $telefono);
	}
	
	/**
	 * Validar cédula de identidad
	 */
	public static function validarCedula($cedula) {
		// Permitir letras, números y guiones
		return preg_match('/^[A-Za-z0-9\-]{5,15}$/', $cedula);
	}
	
	/**
	 * Validar que un campo no esté vacío
	 */
	public static function validarRequerido($valor, $nombreCampo) {
		if (empty(trim($valor))) {
			return array(
				'valida' => false,
				'mensaje' => "El campo '$nombreCampo' es requerido"
			);
		}
		return array('valida' => true);
	}
	
	/**
	 * Validar longitud mínima y máxima
	 */
	public static function validarLongitud($valor, $min, $max, $nombreCampo) {
		$longitud = strlen(trim($valor));
		if ($longitud < $min) {
			return array(
				'valida' => false,
				'mensaje' => "El campo '$nombreCampo' debe tener al menos $min caracteres"
			);
		}
		if ($longitud > $max) {
			return array(
				'valida' => false,
				'mensaje' => "El campo '$nombreCampo' no puede tener más de $max caracteres"
			);
		}
		return array('valida' => true);
	}
	
	/**
	 * Validar número
	 */
	public static function validarNumero($valor, $nombreCampo) {
		if (!is_numeric($valor)) {
			return array(
				'valida' => false,
				'mensaje' => "El campo '$nombreCampo' debe ser un número válido"
			);
		}
		return array('valida' => true);
	}
	
	/**
	 * Validar fecha
	 */
	public static function validarFecha($fecha, $nombreCampo) {
		$d = DateTime::createFromFormat('Y-m-d', $fecha);
		if (!$d || $d->format('Y-m-d') !== $fecha) {
			return array(
				'valida' => false,
				'mensaje' => "El campo '$nombreCampo' debe ser una fecha válida (YYYY-MM-DD)"
			);
		}
		return array('valida' => true);
	}
	
	/**
	 * Validar fecha y hora
	 */
	public static function validarFechaHora($fechahora, $nombreCampo) {
		$d = DateTime::createFromFormat('Y-m-d H:i:s', $fechahora);
		if (!$d || $d->format('Y-m-d H:i:s') !== $fechahora) {
			// Intentar formato datetime-local
			$d = DateTime::createFromFormat('Y-m-d\TH:i', $fechahora);
			if (!$d) {
				return array(
					'valida' => false,
					'mensaje' => "El campo '$nombreCampo' debe ser una fecha y hora válida"
				);
			}
		}
		return array('valida' => true);
	}
	
	/**
	 * Sanitizar entrada para prevenir XSS
	 */
	public static function sanitizar($dato) {
		return htmlspecialchars(strip_tags(trim($dato)), ENT_QUOTES, 'UTF-8');
	}
	
	/**
	 * Validar que el archivo sea una imagen
	 */
	public static function validarImagen($archivo) {
		$tiposPermitidos = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
		$tipoArchivo = $archivo['type'];
		
		if (!in_array($tipoArchivo, $tiposPermitidos)) {
			return array(
				'valida' => false,
				'mensaje' => "El archivo debe ser una imagen (JPG, PNG o GIF)"
			);
		}
		
		// Validar tamaño (máximo 5MB)
		if ($archivo['size'] > 5242880) {
			return array(
				'valida' => false,
				'mensaje' => "El archivo no puede exceder 5MB"
			);
		}
		
		return array('valida' => true);
	}
	
	/**
	 * Validar fecha de fallecimiento
	 * - No puede ser más de 4 días en el pasado desde hoy
	 * - No puede ser una fecha futura
	 * - Si está vacía, se considera válida (campo opcional)
	 */
	public static function validarFechaFallecimiento($fecha) {
		// Si la fecha está vacía, es válida (campo opcional)
		if (empty($fecha)) {
			return array('valida' => true);
		}
		
		// Validar formato de fecha
		$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
		if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha) {
			return array(
				'valida' => false,
				'mensaje' => "La fecha de fallecimiento debe tener un formato válido (YYYY-MM-DD)"
			);
		}
		
		$hoy = new DateTime();
		$hoy->setTime(0, 0, 0); // Establecer hora a medianoche para comparar solo fechas
		$fecha_obj->setTime(0, 0, 0);
		
		// No permitir fechas futuras
		if ($fecha_obj > $hoy) {
			return array(
				'valida' => false,
				'mensaje' => "La fecha de fallecimiento no puede ser una fecha futura"
			);
		}
		
		// Calcular diferencia en días
		$diferencia = $hoy->diff($fecha_obj);
		$diasDiferencia = $diferencia->days;
		
		// No permitir más de 4 días en el pasado
		if ($diasDiferencia > 4) {
			return array(
				'valida' => false,
				'mensaje' => "La fecha de fallecimiento no puede ser más de 4 días en el pasado"
			);
		}
		
		return array('valida' => true);
	}
}

?>


