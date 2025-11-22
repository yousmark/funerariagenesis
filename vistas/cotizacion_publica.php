<?php
require_once "../config/Conexion.php";

$token = isset($_GET['token']) ? limpiarCadena($_GET['token']) : '';
$msg = '';
$ruta = '';
$codigo = '';
$familia = '';
if ($token !== '') {
	$sql = "SELECT c.ruta_pdf, c.codigo, c.nombre_familia, a.expira_en
			FROM cotizacion_acceso a INNER JOIN cotizacion c ON a.idcotizacion=c.idcotizacion
			WHERE a.token='$token' LIMIT 1";
	$row = ejecutarConsultaSimpleFila($sql);
	if ($row) {
		if (!empty($row['expira_en']) && strtotime($row['expira_en']) < time()) {
			$msg = 'El enlace ha expirado.';
		} else {
			$ruta = '../'.$row['ruta_pdf'];
			$codigo = $row['codigo'];
			$familia = $row['nombre_familia'];
		}
	} else {
		$msg = 'Enlace inválido.';
	}
} else {
	$msg = 'Token no provisto.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cotización</title>
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <link rel="stylesheet" href="../public/css/font-awesome.css">
</head>
<body class="container" style="padding-top:40px;">
  <div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Cotización <?php echo htmlspecialchars($codigo); ?></h3></div>
    <div class="panel-body">
      <?php if ($msg !== '') { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($msg); ?></div>
      <?php } else { ?>
        <p><b>Familia:</b> <?php echo htmlspecialchars($familia); ?></p>
        <p>
          <a class="btn btn-primary" href="<?php echo $ruta; ?>" target="_blank"><i class="fa fa-download"></i> Descargar PDF</a>
        </p>
      <?php } ?>
    </div>
  </div>
</body>
</html>


