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
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Mis Itinerarios Funerarios</h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Familia</th>
                  <th>Fecha/Hora Sepelio</th>
                  <th>Lugar</th>
                  <th>Confirmado</th>
                  <th>Fecha Acceso</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Familia</th>
                  <th>Fecha/Hora Sepelio</th>
                  <th>Lugar</th>
                  <th>Confirmado</th>
                  <th>Fecha Acceso</th>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
require 'footer.php';
?>

<script src="../public/js/pdfmake.min.js"></script>
<script src="../public/js/vfs_fonts.js"></script>
<script src="scripts/itinerario_funerario_cliente.js"></script>

<?php
}
ob_end_flush();
?>








