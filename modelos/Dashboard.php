<?php
require_once "../config/Conexion.php";

class Dashboard {
	
	public function __construct() {}
	
	// Obtener estadísticas generales
	public function obtenerEstadisticas() {
		$stats = array();
		
		// Cotizaciones
		$sql_cot_total = "SELECT COUNT(*) as total FROM cotizacion";
		$rs = ejecutarConsultaSimpleFila($sql_cot_total);
		$stats['cotizaciones_total'] = $rs ? $rs['total'] : 0;
		
		$sql_cot_vigentes = "SELECT COUNT(*) as total FROM cotizacion WHERE estado='vigente'";
		$rs = ejecutarConsultaSimpleFila($sql_cot_vigentes);
		$stats['cotizaciones_vigentes'] = $rs ? $rs['total'] : 0;
		
		$sql_cot_anuladas = "SELECT COUNT(*) as total FROM cotizacion WHERE estado='anulada'";
		$rs = ejecutarConsultaSimpleFila($sql_cot_anuladas);
		$stats['cotizaciones_anuladas'] = $rs ? $rs['total'] : 0;
		
		$sql_cot_monto = "SELECT COALESCE(SUM(total), 0) as total FROM cotizacion WHERE estado='vigente'";
		$rs = ejecutarConsultaSimpleFila($sql_cot_monto);
		$stats['cotizaciones_monto_total'] = $rs ? floatval($rs['total']) : 0;
		
		// Contratos
		$sql_con_total = "SELECT COUNT(*) as total FROM contrato";
		$rs = ejecutarConsultaSimpleFila($sql_con_total);
		$stats['contratos_total'] = $rs ? $rs['total'] : 0;
		
		$sql_con_vigentes = "SELECT COUNT(*) as total FROM contrato WHERE estado='vigente'";
		$rs = ejecutarConsultaSimpleFila($sql_con_vigentes);
		$stats['contratos_vigentes'] = $rs ? $rs['total'] : 0;
		
		$sql_con_anulados = "SELECT COUNT(*) as total FROM contrato WHERE estado='anulado'";
		$rs = ejecutarConsultaSimpleFila($sql_con_anulados);
		$stats['contratos_anulados'] = $rs ? $rs['total'] : 0;
		
		$sql_con_monto = "SELECT COALESCE(SUM(total), 0) as total FROM contrato WHERE estado='vigente'";
		$rs = ejecutarConsultaSimpleFila($sql_con_monto);
		$stats['contratos_monto_total'] = $rs ? floatval($rs['total']) : 0;
		
		// Fichas de recojo
		$sql_ficha = "SELECT COUNT(*) as total FROM ficha_recojo WHERE estado='activo'";
		$rs = ejecutarConsultaSimpleFila($sql_ficha);
		$stats['fichas_recojo'] = $rs ? $rs['total'] : 0;
		
		// Itinerarios
		$sql_itinerario = "SELECT COUNT(*) as total FROM itinerario_funerario WHERE estado='activo'";
		$rs = ejecutarConsultaSimpleFila($sql_itinerario);
		$stats['itinerarios'] = $rs ? $rs['total'] : 0;
		
		// Documentos registro civil
		$sql_doc = "SELECT COUNT(*) as total FROM documento_registro_civil WHERE estado='activo'";
		$rs = ejecutarConsultaSimpleFila($sql_doc);
		$stats['documentos_registro'] = $rs ? $rs['total'] : 0;
		
		// Usuarios activos
		$sql_usuarios = "SELECT COUNT(*) as total FROM usuario WHERE condicion=1";
		$rs = ejecutarConsultaSimpleFila($sql_usuarios);
		$stats['usuarios_activos'] = $rs ? $rs['total'] : 0;
		
		return $stats;
	}
	
	// Obtener estadísticas por mes (últimos 6 meses)
	public function obtenerEstadisticasPorMes() {
		$meses = array();
		for ($i = 5; $i >= 0; $i--) {
			$fecha = date('Y-m', strtotime("-$i months"));
			$meses[] = $fecha;
		}
		
		$datos = array();
		
		foreach ($meses as $mes) {
			$inicio = $mes . '-01';
			$fin = date('Y-m-t', strtotime($inicio));
			
			// Cotizaciones del mes
			$sql = "SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as monto 
					FROM cotizacion 
					WHERE DATE_FORMAT(fecha_creacion, '%Y-%m') = '$mes' AND estado='vigente'";
			$rs = ejecutarConsultaSimpleFila($sql);
			
			// Contratos del mes
			$sql2 = "SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as monto 
					 FROM contrato 
					 WHERE DATE_FORMAT(fecha_creacion, '%Y-%m') = '$mes' AND estado='vigente'";
			$rs2 = ejecutarConsultaSimpleFila($sql2);
			
			$datos[] = array(
				'mes' => date('M Y', strtotime($inicio)),
				'cotizaciones' => $rs ? intval($rs['total']) : 0,
				'cotizaciones_monto' => $rs ? floatval($rs['monto']) : 0,
				'contratos' => $rs2 ? intval($rs2['total']) : 0,
				'contratos_monto' => $rs2 ? floatval($rs2['monto']) : 0
			);
		}
		
		return $datos;
	}
	
	// Obtener actividad reciente
	public function obtenerActividadReciente($limite = 10) {
		$limite = intval($limite);
		$sql = "(SELECT 'cotizacion' as tipo, codigo, nombre_familia as nombre, total, fecha_creacion, estado, idcotizacion as id
				 FROM cotizacion
				 ORDER BY fecha_creacion DESC
				 LIMIT $limite)
				UNION ALL
				(SELECT 'contrato' as tipo, codigo, nombre_contratante as nombre, total, fecha_creacion, estado, idcontrato as id
				 FROM contrato
				 ORDER BY fecha_creacion DESC
				 LIMIT $limite)
				ORDER BY fecha_creacion DESC
				LIMIT $limite";
		
		return ejecutarConsulta($sql);
	}
}

?>








