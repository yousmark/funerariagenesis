<?php
// Incluir la conexión de base de datos
require_once "../config/Conexion.php";

class DocumentoRegistroCivil {

	public function __construct() {}

	public function insertar($codigo, $idcontrato, $idcotizacion, $idusuario, $nombre_difunto, $cedula_difunto, $ruta_cedula_difunto, $nombre_responsable, $cedula_responsable, $ruta_cedula_responsable, $telefono_responsable, $direccion_responsable, $testigo1_nombre, $testigo1_cedula, $testigo1_ruta_cedula, $testigo2_nombre, $testigo2_cedula, $testigo2_ruta_cedula, $testigo3_nombre, $testigo3_cedula, $testigo3_ruta_cedula, $documentos_completos, $observaciones) {
		$codigo = limpiarCadena($codigo);
		$idcontrato = ($idcontrato == "") ? "NULL" : limpiarCadena($idcontrato);
		$idcotizacion = ($idcotizacion == "") ? "NULL" : limpiarCadena($idcotizacion);
		$nombre_difunto = limpiarCadena($nombre_difunto);
		$cedula_difunto = limpiarCadena($cedula_difunto);
		$ruta_cedula_difunto = limpiarCadena($ruta_cedula_difunto);
		$nombre_responsable = limpiarCadena($nombre_responsable);
		$cedula_responsable = limpiarCadena($cedula_responsable);
		$ruta_cedula_responsable = limpiarCadena($ruta_cedula_responsable);
		$telefono_responsable = limpiarCadena($telefono_responsable);
		$direccion_responsable = limpiarCadena($direccion_responsable);
		$testigo1_nombre = limpiarCadena($testigo1_nombre);
		$testigo1_cedula = limpiarCadena($testigo1_cedula);
		$testigo1_ruta_cedula = limpiarCadena($testigo1_ruta_cedula);
		$testigo2_nombre = limpiarCadena($testigo2_nombre);
		$testigo2_cedula = limpiarCadena($testigo2_cedula);
		$testigo2_ruta_cedula = limpiarCadena($testigo2_ruta_cedula);
		$testigo3_nombre = limpiarCadena($testigo3_nombre);
		$testigo3_cedula = limpiarCadena($testigo3_cedula);
		$testigo3_ruta_cedula = limpiarCadena($testigo3_ruta_cedula);
		$documentos_completos = limpiarCadena($documentos_completos);
		global $conexion;
		$observaciones = mysqli_real_escape_string($conexion, $observaciones);

		$sql = "INSERT INTO documento_registro_civil (codigo, idcontrato, idcotizacion, idusuario, nombre_difunto, cedula_difunto, ruta_cedula_difunto, nombre_responsable, cedula_responsable, ruta_cedula_responsable, telefono_responsable, direccion_responsable, testigo1_nombre, testigo1_cedula, testigo1_ruta_cedula, testigo2_nombre, testigo2_cedula, testigo2_ruta_cedula, testigo3_nombre, testigo3_cedula, testigo3_ruta_cedula, documentos_completos, observaciones, estado, fecha_creacion)
				VALUES ('$codigo', $idcontrato, $idcotizacion, '$idusuario', '$nombre_difunto', '$cedula_difunto', '$ruta_cedula_difunto', '$nombre_responsable', '$cedula_responsable', '$ruta_cedula_responsable', '$telefono_responsable', '$direccion_responsable', " . ($testigo1_nombre ? "'$testigo1_nombre'" : "NULL") . ", " . ($testigo1_cedula ? "'$testigo1_cedula'" : "NULL") . ", " . ($testigo1_ruta_cedula ? "'$testigo1_ruta_cedula'" : "NULL") . ", " . ($testigo2_nombre ? "'$testigo2_nombre'" : "NULL") . ", " . ($testigo2_cedula ? "'$testigo2_cedula'" : "NULL") . ", " . ($testigo2_ruta_cedula ? "'$testigo2_ruta_cedula'" : "NULL") . ", " . ($testigo3_nombre ? "'$testigo3_nombre'" : "NULL") . ", " . ($testigo3_cedula ? "'$testigo3_cedula'" : "NULL") . ", " . ($testigo3_ruta_cedula ? "'$testigo3_ruta_cedula'" : "NULL") . ", '$documentos_completos', '$observaciones', 'pendiente', NOW())";
		return ejecutarConsulta_retornarID($sql);
	}

	public function mostrar($iddocumento) {
		$sql = "SELECT d.*, u.nombre AS usuario, co.codigo AS codigo_contrato, cot.codigo AS codigo_cotizacion
				FROM documento_registro_civil d
				INNER JOIN usuario u ON d.idusuario = u.idusuario
				LEFT JOIN contrato co ON d.idcontrato = co.idcontrato
				LEFT JOIN cotizacion cot ON d.idcotizacion = cot.idcotizacion
				WHERE d.iddocumento='$iddocumento'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listar() {
		$sql = "SELECT d.iddocumento, d.codigo, d.nombre_difunto, d.nombre_responsable, d.documentos_completos, d.estado, d.fecha_creacion, u.nombre AS usuario
				FROM documento_registro_civil d
				INNER JOIN usuario u ON d.idusuario = u.idusuario
				ORDER BY d.iddocumento DESC";
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

	public function actualizarEstado($iddocumento, $estado) {
		$estado = limpiarCadena($estado);
		$sql = "UPDATE documento_registro_civil SET estado='$estado' WHERE iddocumento='$iddocumento'";
		return ejecutarConsulta($sql);
	}

	public function marcarCompletos($iddocumento) {
		$sql = "UPDATE documento_registro_civil SET documentos_completos=1, estado='completo' WHERE iddocumento='$iddocumento'";
		return ejecutarConsulta($sql);
	}

	public function desactivar($iddocumento) {
		$sql = "UPDATE documento_registro_civil SET estado='inactivo' WHERE iddocumento='$iddocumento'";
		return ejecutarConsulta($sql);
	}
}
?>
