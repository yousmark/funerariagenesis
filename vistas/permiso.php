<?php 
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
}else{
  
require 'header.php';
if ($_SESSION['acceso']==1) {
 ?>
    <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestión de Permisos
        <small>Administración de permisos de usuarios</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li><a href="#"><i class="fa fa-folder"></i> Acceso</a></li>
        <li class="active">Permisos</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
      <div class="box">
<div class="box-header with-border">
  <h1 class="box-title">Gestión de Permisos de Usuarios</h1>
  <div class="box-tools pull-right">
    
  </div>
</div>
<!--box-header-->
<!--centro-->
<div class="panel-body table-responsive" id="listadoregistros">
  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
    <thead>
     <th>Opciones</th>
     <th>Usuario</th>
     <th>Email</th>
     <th>Cargo</th>
     <th>Permisos Actuales</th>
     <th>Estado</th>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <th>Opciones</th>
      <th>Usuario</th>
      <th>Email</th>
      <th>Cargo</th>
      <th>Permisos Actuales</th>
      <th>Estado</th>
    </tfoot>   
  </table>
</div>
<!--fin centro-->
      </div>
      </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
<?php 
}else{
 require 'noacceso.php'; 
}
require 'footer.php'
 ?>
 <script type="text/javascript" src="scripts/permiso.js"></script>
<?php 
}
ob_end_flush();
?>
