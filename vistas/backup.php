<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';
  
  // Solo administradores pueden acceder
  if ($_SESSION['acceso'] != 1) {
    require 'noacceso.php';
    require 'footer.php';
    exit();
  }
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Respaldo de Base de Datos
      <small>Gestión de copias de seguridad</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Backup</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h1 class="box-title">Respaldo de Base de Datos 
              <button class="btn btn-success" id="btnCrearBackup">
                <i class="fa fa-database"></i> Crear Backup
              </button>
            </h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> 
              <strong>Importante:</strong> Los backups se crean automáticamente comprimidos para ahorrar espacio. 
              Se recomienda crear backups regularmente para proteger los datos del sistema.
            </div>
            
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Nombre Archivo</th>
                  <th>Tamaño</th>
                  <th>Fecha Creación</th>
                  <th>Estado</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Nombre Archivo</th>
                  <th>Tamaño</th>
                  <th>Fecha Creación</th>
                  <th>Estado</th>
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

<script src="scripts/backup.js"></script>

<?php
}
ob_end_flush();
?>








