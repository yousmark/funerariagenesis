<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"]))
{
  header("Location: login.html");
}
else
{
require 'header.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Dashboard
      <small>Panel de Control</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <!-- Tarjetas de Estadísticas -->
    <div class="row">
      <!-- Cotizaciones -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3 id="stat-cot-total">0</h3>
            <p>Total Cotizaciones</p>
          </div>
          <div class="icon">
            <i class="fa fa-file-pdf-o"></i>
          </div>
          <a href="cotizacion.php" class="small-box-footer">
            Más información <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Contratos -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3 id="stat-con-total">0</h3>
            <p>Total Contratos</p>
          </div>
          <div class="icon">
            <i class="fa fa-file-text-o"></i>
          </div>
          <a href="contrato.php" class="small-box-footer">
            Más información <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Monto Total -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3 id="stat-monto-total">Bs. 0</h3>
            <p>Monto Total</p>
          </div>
          <div class="icon">
            <i class="fa fa-money"></i>
          </div>
          <a href="#" class="small-box-footer">
            <i class="fa fa-info-circle"></i>
          </a>
        </div>
      </div>

      <!-- Usuarios Activos -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3 id="stat-usuarios">0</h3>
            <p>Usuarios Activos</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <a href="usuario.php" class="small-box-footer">
            Más información <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="row">
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-pdf-o"></i> Cotizaciones</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-box">
                  <span class="info-box-icon bg-aqua"><i class="fa fa-check"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vigentes</span>
                    <span class="info-box-number" id="stat-cot-vigentes">0</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Anuladas</span>
                    <span class="info-box-number" id="stat-cot-anuladas">0</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <p class="text-center">
                  <strong>Monto Total: <span id="stat-cot-monto">Bs. 0.00</span></strong>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-file-text-o"></i> Contratos</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <div class="info-box">
                  <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vigentes</span>
                    <span class="info-box-number" id="stat-con-vigentes">0</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Anulados</span>
                    <span class="info-box-number" id="stat-con-anulados">0</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <p class="text-center">
                  <strong>Monto Total: <span id="stat-con-monto">Bs. 0.00</span></strong>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Módulos del Sistema -->
    <div class="row">
      <div class="col-md-3 col-sm-6">
        <div class="box box-info">
          <div class="box-body text-center">
            <h4><i class="fa fa-file-o"></i> Fichas de Recojo</h4>
            <h2 id="stat-fichas">0</h2>
            <a href="ficha_recojo.php" class="btn btn-info btn-sm">Ver más</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-warning">
          <div class="box-body text-center">
            <h4><i class="fa fa-map-marker"></i> Itinerarios</h4>
            <h2 id="stat-itinerarios">0</h2>
            <a href="itinerario_funerario.php" class="btn btn-warning btn-sm">Ver más</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box" style="border-top-color: #605ca8;">
          <div class="box-body text-center">
            <h4><i class="fa fa-folder-open"></i> Documentos</h4>
            <h2 id="stat-documentos">0</h2>
            <a href="documento_registro_civil.php" class="btn btn-sm" style="background-color: #605ca8; color: white;">Ver más</a>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="box box-primary">
          <div class="box-body text-center">
            <h4><i class="fa fa-file-invoice"></i> Facturación</h4>
            <h2><i class="fa fa-external-link"></i></h2>
            <a href="facturacion.php" class="btn btn-primary btn-sm">Acceder</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico y Actividad Reciente -->
    <div class="row">
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Actividad (Últimos 6 Meses)</h3>
          </div>
          <div class="box-body">
            <canvas id="chartActividad" style="height: 250px;"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Actividad Reciente</h3>
          </div>
          <div class="box-body">
            <ul class="products-list product-list-in-box" id="lista-actividad">
              <li class="item">
                <div class="product-info">
                  <p class="text-center">Cargando...</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
require 'footer.php';
?>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script src="scripts/dashboard.js"></script>

<?php
}
ob_end_flush();
?>
