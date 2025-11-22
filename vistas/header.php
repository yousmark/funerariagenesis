<?php

if(strlen(session_id())<1)
  session_start();

// Verificar expiración de sesión
require_once "../config/Seguridad.php";
if (isset($_SESSION['ultima_actividad'])) {
	if (Seguridad::sesionExpirada()) {
		session_unset();
		session_destroy();
		header("Location: login.html?expired=1");
		exit();
	}
}

// Cargar cargo desde BD (para habilitar Cotizaciones a Gerente aunque no se haya reiniciado sesión)
require_once "../config/Conexion.php";
$esGerente = false;
if (isset($_SESSION['idusuario'])) {
  $id = intval($_SESSION['idusuario']);
  $row = ejecutarConsultaSimpleFila("SELECT cargo FROM usuario WHERE idusuario='$id'");
  if ($row && isset($row['cargo'])) {
    $esGerente = (strtolower($row['cargo']) === 'gerente');
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Funeraria | Genesis</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../public/css/font-awesome.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="../public/css/_all-skins.min.css">
    <link rel="stylesheet" href="../public/css/custom.css">


  <link rel="apple" href="../public/img/apple-touch-icon.jpg">
  <link rel="shortcut icon" href="../public/img/apple-touch-icon.jpg">

  <!-- DATATABLES -->
  <link rel="stylesheet" type="text/css" href="../public/datatables/jquery.dataTables.min.css">
  <link href="../public/datatables/buttons.dataTables.min.css" rel="stylesheet" />
  <link href="../public/datatables/responsive.dataTables.min.css" rel="stylesheet" />

  <link rel="stylesheet" type="text/css" href="../public/css/bootstrap-select.min.css">
  
  <!-- Toastr Notifications -->
  <link rel="stylesheet" href="../public/css/toastr.css">
</head>

<body class="hold-transition skin-dark sidebar-mini">
  <div class="wrapper">

    <!-- ================= HEADER ================= -->
    <header class="main-header">
      <!-- Logo -->
      

      <!-- Logo -->
<a href="dashboard.php" class="logo">
  <!-- Versión mini -->
  <p class="logo-mini">
    <b class="fa fa-hospital-o">F.G</b>
  </p>
  <!-- Versión grande -->
  <p class="logo-lg">
    <b class="fa fa-university">Funeraria Genesis</b> 
  </p>
</a>


      <!-- Navbar -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Navegación</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            

            <!-- Usuario -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="user-image" alt="User Image">
                <span class="hidden-xs"><?php echo $_SESSION['nombre']; ?></span>
              </a>
              <ul class="dropdown-menu">
                <!-- Imagen y nombre -->
                <li class="user-header bg-purple">
                  <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">
                  <p>
                    <?php echo $_SESSION['nombre']; ?>
                    <small>Usuario del Sistema</small>
                  </p>
                </li>
               <!-- Footer del menú -->
<li class="user-footer">
  <div class="pull-left">
    <a href="#" class="btn btn-default btn-flat">Perfil</a>
  </div>
  <div class="pull-right">
    <!-- Botón que abre la modal -->
    <a href="#" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modalCerrarSesion">Salir</a>
  </div>
</li>
              </ul>
            </li>

          </ul>
        </div>
      </nav>
    </header>

    <!--  SIDEBAR  -->
    <aside class="main-sidebar">
      <section class="sidebar">

        <!-- Panel de usuario en el sidebar -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?php echo $_SESSION['nombre']; ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> En línea</a>
          </div>
        </div>

                  <!-- Menú lateral -->
        <ul class="sidebar-menu">
          <li class="header">NAVEGACIÓN</li>
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
          <li class="header">GESTIÓN DOCUMENTAL</li>

          

          



          

          

          


          

          <?php
          if($_SESSION['acceso']==1)
          {
          echo '<li class="treeview">
              <a href="#"><i class="fa fa-folder"></i> <span>Acceso</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                <li><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#"><i class="fa fa-shield"></i> <span>Seguridad</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="backup.php"><i class="fa fa-database"></i> Backup</a></li>
              </ul>
            </li>';
          }
          ?>
         

          <?php
          if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
          {
          echo '<li><a href="cotizacion.php"><i class="fa fa-file-pdf-o"></i> <span>Cotizaciones</span></a></li>';
          }
          ?>

          <?php
          if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
          {
          echo '<li><a href="contrato.php"><i class="fa fa-file-text-o"></i> <span>Contratos</span></a></li>';
          }
          ?>

          <?php
          if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
          {
          echo '<li><a href="ficha_recojo.php"><i class="fa fa-file-o"></i> <span>Ficha de Recojo</span></a></li>';
          }
          ?>

                      <?php
            if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
            {
          echo '<li><a href="checklist_velatorio.php"><i class="fa fa-check-square-o"></i> <span>Checklist Velatorio</span></a></li>';
            }
            ?>

            <?php
            if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
            {
          echo '<li><a href="documento_registro_civil.php"><i class="fa fa-folder-open"></i> <span>Documentos Registro Civil</span></a></li>';
            }
            ?>

            <?php
            if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
            {
          echo '<li><a href="itinerario_funerario.php"><i class="fa fa-map-marker"></i> <span>Itinerarios Funerarios</span></a></li>';
            }
            ?>

            <?php
            if((isset($_SESSION['cotizaciones']) && $_SESSION['cotizaciones']==1) || $esGerente)
            {
          echo '<li><a href="facturacion.php"><i class="fa fa-file-invoice"></i> <span>Facturación SIAT</span></a></li>';
            }
            ?>

          


          <?php
          // Todos los usuarios pueden ver sus cotizaciones (excepto gerentes que tienen acceso completo)
          if (!$esGerente) {
            echo '<li><a href="cotizacion_cliente.php"><i class="fa fa-eye"></i> <span>Mis Cotizaciones</span></a></li>';
          }
          ?>

          <?php
          // Todos los usuarios pueden ver sus itinerarios (excepto gerentes que tienen acceso completo)
          if (!$esGerente) {
            echo '<li><a href="itinerario_funerario_cliente.php"><i class="fa fa-map"></i> <span>Mis Itinerarios</span></a></li>';
          }
          ?>

          <?php
          // Todos los usuarios pueden ver sus fichas de recojo (excepto gerentes que tienen acceso completo)
          if (!$esGerente) {
            echo '<li><a href="ficha_recojo_cliente.php"><i class="fa fa-file"></i> <span>Mis Fichas de Recojo</span></a></li>';
          }
          ?>

          <?php
          // Todos los usuarios pueden ver sus contratos (excepto gerentes que tienen acceso completo)
          if (!$esGerente) {
            echo '<li><a href="contrato_cliente.php"><i class="fa fa-file-text"></i> <span>Mis Contratos</span></a></li>';
          }
          ?>

          <!-- Cerrar sesión -->
<li> <a href="#" data-toggle="modal" data-target="#modalCerrarSesion"> 
  <i class="fa fa-sign-out"></i> <span>Cerrar Sesion</span>
 </a>
 </li>

        </ul>
      </section>
    </aside>
    
 
    <!-- Modal Cerrar Sesión -->
<div class="modal fade" id="modalCerrarSesion" tabindex="-1" role="dialog" aria-labelledby="modalCerrarSesionLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalCerrarSesionLabel"><i class="fa fa-exclamation-triangle"></i> Confirmar Cierre de Sesión</h4>
      </div>
      <div class="modal-body">
        ¿Estás seguro que deseas cerrar sesión?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <a href="../ajax/usuario.php?op=salir" class="btn btn-danger">Cerrar Sesión</a>
      </div>
    </div>
  </div>
</div>
