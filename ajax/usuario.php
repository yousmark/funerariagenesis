<?php 
// Establecer headers para JSON y prevenir errores
header('Content-Type: application/json; charset=utf-8');

// Iniciar buffer de salida para capturar errores
ob_start();

session_start();
require_once "../modelos/Usuario.php";
require_once "../config/Validacion.php";
require_once "../config/Seguridad.php";

$usuario = new Usuario();

// Limpiar cualquier salida previa
ob_clean();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$celula = isset($_POST["celula"]) ? limpiarCadena($_POST["celula"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$celular = isset($_POST["celular"]) ? limpiarCadena($_POST["celular"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$cargo = isset($_POST["cargo"]) ? limpiarCadena($_POST["cargo"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

// Verificar que la operación esté definida
if (!isset($_GET["op"])) {
	ob_clean();
	echo json_encode(array('error' => true, 'mensaje' => 'Operación no especificada'));
	exit();
}

$op = $_GET["op"];

switch ($op) {

	case 'guardaryeditar':
		// Imagen
		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
			$imagen = $_POST["imagenactual"];
		} else {
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
			}
		}

		// Validar contraseña si se proporciona
		if (!empty($clave)) {
			$validacionPassword = Validacion::validarPassword($clave);
			if (!$validacionPassword['valida']) {
				echo json_encode(array('error' => true, 'mensaje' => implode('. ', $validacionPassword['errores'])));
				exit();
			}
			$clavehash = Seguridad::encriptarPassword($clave);
		} else {
			// Si no se proporciona contraseña nueva, mantener la actual
			$clavehash = '';
		}
		
		// Validar email
		if (!empty($email) && !Validacion::validarEmail($email)) {
			echo json_encode(array('error' => true, 'mensaje' => 'El email no es válido'));
			exit();
		}
		
		// Validar teléfono
		if (!empty($celular) && !Validacion::validarTelefono($celular)) {
			echo json_encode(array('error' => true, 'mensaje' => 'El teléfono debe contener solo números (7-15 dígitos)'));
			exit();
		}
		
		$permisos = isset($_POST["permiso"]) ? $_POST["permiso"] : array();

		if (empty($idusuario)) {
			if (empty($clavehash)) {
				echo json_encode(array('error' => true, 'mensaje' => 'La contraseña es requerida para crear un nuevo usuario'));
				exit();
			}
			$rspta = $usuario->insertar($nombre, $celula, $direccion, $celular, $email, $cargo, $login, $clavehash, $imagen, $permisos);
			echo $rspta ? json_encode(array('success' => true, 'mensaje' => 'Usuario registrado correctamente')) : json_encode(array('error' => true, 'mensaje' => 'No se pudo registrar el usuario'));
		} else {
			$rspta = $usuario->editar($idusuario, $nombre, $celula, $direccion, $celular, $email, $cargo, $login, $clavehash, $imagen, $permisos);
			echo $rspta ? json_encode(array('success' => true, 'mensaje' => 'Usuario actualizado correctamente')) : json_encode(array('error' => true, 'mensaje' => 'No se pudo actualizar el usuario'));
		}
		break;

	case 'desactivar':
		$rspta = $usuario->desactivar($idusuario);
		echo $rspta ? "Usuario desactivado" : "No se pudo desactivar";
		break;

	case 'activar':
		$rspta = $usuario->activar($idusuario);
		echo $rspta ? "Usuario activado" : "No se pudo activar";
		break;

	case 'mostrar':
		$rspta = $usuario->mostrar($idusuario);
		echo json_encode($rspta);
		break;

	case 'listar':
		$rspta = $usuario->listar();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => ($reg->condicion) ? '<button class="btn btn-warning" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' .
					' <button class="btn btn-danger" onclick="desactivar(' . $reg->idusuario . ')"><i class="fa fa-close"></i></button>' :
					'<button class="btn btn-warning" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' .
					' <button class="btn btn-primary" onclick="activar(' . $reg->idusuario . ')"><i class="fa fa-check"></i></button>',
				"1" => $reg->nombre,
				"2" => $reg->celula,
				"3" => $reg->celular,
				"4" => $reg->email,
				"5" => $reg->login,
				"6" => "<img src='../files/usuarios/" . $reg->imagen . "' height='50px' width='50px'>",
				"7" => ($reg->condicion) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
			);
		}

		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data
		);
		echo json_encode($results);
		break;

	case 'permisos':
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();
		$id = $_GET['id'];
		$marcados = $usuario->listarmarcados($id);
		$valores = array();

		while ($per = $marcados->fetch_object()) {
			array_push($valores, $per->idpermiso);
		}

		while ($reg = $rspta->fetch_object()) {
			$sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
			echo '<li><input type="checkbox" ' . $sw . ' name="permiso[]" value="' . $reg->idpermiso . '">' . $reg->nombre . '</li>';
		}
		break;

	case 'verificar':
		try {
			// Verificar que los parámetros POST estén presentes
			if (!isset($_POST['logina']) || !isset($_POST['clavea'])) {
				ob_clean();
				echo json_encode(array('error' => true, 'mensaje' => 'Faltan parámetros de autenticación'));
				exit();
			}
			
			$logina = limpiarCadena($_POST['logina']);
			$clavea = $_POST['clavea'];
		
		// Verificar intentos fallidos
		$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		if (Seguridad::verificarIntentosFallidos($logina)) {
			Seguridad::registrarIntentoFallido($logina, $ip);
			ob_clean();
			echo json_encode(array('error' => true, 'mensaje' => 'Demasiados intentos fallidos. Su cuenta ha sido temporalmente bloqueada. Intente más tarde.'));
			exit();
		}
		
		$rspta = $usuario->verificar($logina);
		if (!$rspta) {
			Seguridad::registrarIntentoFallido($logina, $ip);
			ob_clean();
			echo json_encode(array('error' => true, 'mensaje' => 'Error al consultar la base de datos'));
			exit();
		}
		
		$fetch = $rspta->fetch_object();
		
		// Verificar si el usuario existe
		if (!$fetch) {
			Seguridad::registrarIntentoFallido($logina, $ip);
			ob_clean();
			echo json_encode(array('error' => true, 'mensaje' => 'Usuario y/o contraseña incorrectos'));
			exit();
		}
		
		$passwordValido = false;
		$necesitaMigracion = false;
		
		// Verificar contraseña con password_verify (bcrypt)
		if (Seguridad::verificarPassword($clavea, $fetch->clave)) {
			$passwordValido = true;
		} else {
			// Verificar si usa hash antiguo SHA-256 para migración
			$clavehash_sha256 = hash("SHA256", $clavea);
			if ($fetch->clave === $clavehash_sha256) {
				$passwordValido = true;
				$necesitaMigracion = true;
			}
		}
		
		// Si la contraseña no es válida, rechazar
		if (!$passwordValido) {
			Seguridad::registrarIntentoFallido($logina, $ip);
			ob_clean();
			echo json_encode(array('error' => true, 'mensaje' => 'Usuario y/o contraseña incorrectos'));
			exit();
		}
		
		// Si necesita migración, actualizar el hash
		if ($necesitaMigracion) {
			$nuevoHash = Seguridad::encriptarPassword($clavea);
			$sql = "UPDATE usuario SET clave='$nuevoHash', fecha_actualizacion_password=NOW() WHERE idusuario='" . $fetch->idusuario . "'";
			ejecutarConsulta($sql);
			$fetch->clave = $nuevoHash;
			$fetch->fecha_actualizacion_password = date('Y-m-d H:i:s');
		}
		
		// Verificar si la contraseña ha expirado (90 días) - solo si tiene fecha_actualizacion_password
		if (!empty($fetch->fecha_actualizacion_password)) {
			$fechaActualizacion = $fetch->fecha_actualizacion_password;
			if (Seguridad::passwordExpirada($fechaActualizacion, 90)) {
				$_SESSION['password_expirada'] = true;
				$_SESSION['idusuario'] = $fetch->idusuario;
				ob_clean();
				echo json_encode(array('error' => true, 'password_expirada' => true, 'mensaje' => 'Su contraseña ha expirado. Debe cambiarla antes de continuar.'));
				exit();
			}
		}

		if (isset($fetch)) {
			// Crear sesión segura
			$_SESSION['idusuario'] = $fetch->idusuario;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['login'] = $fetch->login;
			$_SESSION['cargo'] = $fetch->cargo;
			$_SESSION['ultima_actividad'] = time();
			
			// Registrar sesión en base de datos (si la tabla existe)
			try {
				$token = Seguridad::generarToken();
				$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
				$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
				$fechaExpiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
				$sql = "INSERT INTO sesion (idusuario, token, ip, user_agent, fecha_inicio, fecha_ultima_actividad, fecha_expiracion, estado) 
						VALUES ('" . $fetch->idusuario . "', '$token', '$ip', '$user_agent', NOW(), NOW(), '$fechaExpiracion', 'activa')";
				$result = @ejecutarConsulta($sql);
				// Si la tabla no existe, no es crítico, solo continuamos
				if ($result) {
					$_SESSION['token'] = $token;
				}
			} catch (Exception $e) {
				// Si hay error con la tabla sesion, continuamos sin ella
				// No es crítico para el login
			}

			$marcados = $usuario->listarmarcados($fetch->idusuario);
			$valores = array();

			while ($per = $marcados->fetch_object()) {
				array_push($valores, $per->idpermiso);
			}

			in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
			in_array(2, $valores) ? $_SESSION['estudiantes'] = 1 : $_SESSION['estudiantes'] = 0;
			in_array(3, $valores) ? $_SESSION['docentes'] = 1 : $_SESSION['docentes'] = 0;
			in_array(4, $valores) ? $_SESSION['academico'] = 1 : $_SESSION['academico'] = 0;
			in_array(5, $valores) ? $_SESSION['cursos'] = 1 : $_SESSION['cursos'] = 0;
			in_array(6, $valores) ? $_SESSION['inscripcion'] = 1 : $_SESSION['inscripcion'] = 0;
			in_array(7, $valores) ? $_SESSION['notas'] = 1 : $_SESSION['notas'] = 0;
			in_array(8, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
			in_array(9, $valores) ? $_SESSION['consultai'] = 1 : $_SESSION['consultai'] = 0;
			in_array(10, $valores) ? $_SESSION['consultan'] = 1 : $_SESSION['consultan'] = 0;
			in_array(11, $valores) ? $_SESSION['cotizaciones'] = 1 : $_SESSION['cotizaciones'] = 0;

			// Habilitar Cotizaciones por cargo: Gerente
			if (isset($_SESSION['cargo']) && strtolower($_SESSION['cargo']) === 'gerente') {
				$_SESSION['cotizaciones'] = 1;
			}
		}

		// Devolver información del usuario (sin la contraseña)
		$response = array(
			'idusuario' => $fetch->idusuario,
			'nombre' => $fetch->nombre,
			'imagen' => $fetch->imagen,
			'login' => $fetch->login,
			'cargo' => $fetch->cargo
		);
		ob_clean(); // Limpiar cualquier salida previa
		echo json_encode($response);
		} catch (Exception $e) {
			ob_clean();
			echo json_encode(array(
				'error' => true,
				'mensaje' => 'Error durante la autenticación: ' . $e->getMessage()
			));
		} catch (Error $e) {
			ob_clean();
			echo json_encode(array(
				'error' => true,
				'mensaje' => 'Error fatal durante la autenticación: ' . $e->getMessage()
			));
		}
		break;

	case 'salir':
		session_unset();
		session_destroy();
		header("Location: ../index.php");
		break;
}
?>
