<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION["nombre"]))
{
  header("Location: login.html");
}
else
{
require 'header.php';

// URL de autenticación del SIAT
$siat_url = 'https://login.impuestos.gob.bo/realms/login2/protocol/openid-connect/auth?client_id=app-frontend&redirect_uri=https%3A%2F%2Fsiat.impuestos.gob.bo%2Fv2%2Flauncher%2F&state=b001b3be-6df4-4bae-90fe-5f5ef07ed3c5&response_mode=fragment&response_type=code&scope=openid&nonce=5dfff76e-f285-4275-91a4-302f8b50b238';
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Facturación SIAT
      <small>Sistema Integrado de Administración Tributaria</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Facturación SIAT</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title"><i class="fa fa-file-invoice"></i> Facturación SIAT</h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                <div class="alert alert-info">
                  <h4><i class="icon fa fa-info-circle"></i> Redirigiendo al Sistema SIAT</h4>
                  <p>Serás redirigido automáticamente al Sistema Integrado de Administración Tributaria (SIAT) de Bolivia.</p>
                  <p>Si no eres redirigido automáticamente, haz clic en el siguiente botón:</p>
                  <p class="text-center" style="margin-top: 20px;">
                    <a href="<?php echo htmlspecialchars($siat_url); ?>" class="btn btn-primary btn-lg" id="btnRedireccionar" target="_blank">
                      <i class="fa fa-external-link"></i> Acceder al SIAT
                    </a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// Redirección automática después de 2 segundos
setTimeout(function() {
  window.location.href = <?php echo json_encode($siat_url); ?>;
}, 2000);
</script>

<?php
require 'footer.php';
?>

<?php
}
ob_end_flush();
?>
