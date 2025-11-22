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
      Usuarios
      <small>Gestión de usuarios del sistema</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Usuarios</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">

          <div class="box-header with-border">
            <h1 class="box-title">Usuarios 
              <button class="btn btn-success" onclick="mostrarform(true)" id="btnagregar">
                <i class="fa fa-plus-circle"></i> Agregar
              </button>
            </h1>
          </div>

          <!-- Tabla -->
          <div class="panel-body table-responsive" id="listadoregistros">
            <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Celula</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Login</th>
                <th>Foto</th>
                <th>Estado</th>
              </thead>
              <tbody></tbody>
              <tfoot>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Celula</th>
                <th>Celular</th>
                <th>Email</th>
                <th>Login</th>
                <th>Foto</th>
                <th>Estado</th>
              </tfoot>   
            </table>
          </div>

          <!-- Formulario -->
          <div class="panel-body" id="formularioregistros">
            <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="idusuario" id="idusuario">

              <div class="form-group col-lg-3">
                <label>Nombres(*):</label>
                <input type="text" class="form-control" name="nombres" id="nombres" maxlength="100" 
                       data-validacion="soloLetras" required 
                       placeholder="Solo letras y espacios">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo se permiten letras y espacios</small>
              </div>

              <div class="form-group col-lg-3">
                <label>Apellidos(*):</label>
                <input type="text" class="form-control" name="apellidos" id="apellidos" maxlength="100" 
                       data-validacion="soloLetras" required 
                       placeholder="Solo letras y espacios">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo se permiten letras y espacios</small>
              </div>

              <div class="form-group col-lg-6">
                <label>Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" required
                       placeholder="Dirección completa">
              </div>

              <div class="form-group col-lg-3">
                <label>Cédula(*):</label>
                <input type="text" class="form-control" name="celula" id="celula" maxlength="8" 
                       data-validacion="cedula" required
                       placeholder="Ej: 1234567">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
              </div>
              <div class="form-group col-lg-1">
                <label>Complemento</label>
                <input type="text" class="form-control" name="complemento_ci" id="complemento_ci" maxlength="1" 
                       data-validacion="complemento" placeholder="A" style="text-transform: uppercase;">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
              </div>

              <div class="form-group col-lg-4">
                <label>Celular:</label>
                <input type="text" class="form-control" name="celular" id="celular" maxlength="8" 
                       data-validacion="telefono" required
                       placeholder="Ej: 71234567">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
              </div>

              <div class="form-group col-lg-4">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="70" 
                       data-validacion="email" required
                       placeholder="ejemplo@dominio.com">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Formato: usuario@dominio.com</small>
              </div>

              <div class="form-group col-lg-4">
                <label>Cargo:</label>
                <input type="text" class="form-control" name="cargo" id="cargo" maxlength="20" required
                       placeholder="Cargo o puesto">
              </div>

              <div class="form-group col-lg-4">
                <label>Login(*):</label>
                <input type="text" class="form-control" name="login" id="login" maxlength="70"
                       data-validacion="alfanumerico" required
                       placeholder="Nombre de usuario">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras, números y guiones</small>
              </div>

              <div class="form-group col-lg-4">
                <label>Clave(*):</label>
                <input type="password" class="form-control" name="clave" id="clave" maxlength="64"
                       data-validacion="password" required
                       placeholder="Mínimo 8 caracteres">
                <small class="help-block-info"><i class="fa fa-info-circle"></i> Debe contener: mayúsculas, minúsculas, números y caracteres especiales</small>
              </div>

              <div class="form-group col-lg-4">
                <label>Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="imagen">
                <input type="hidden" name="imagenactual" id="imagenactual">
                <img src="" width="150px" height="120px" id="imagenmuestra">
              </div>

              <div class="form-group col-lg-12 text-center">
                <button class="btn btn-primary" type="submit" id="btnGuardar">
                  <i class="fa fa-save"></i> Guardar
                </button>
                <button class="btn btn-danger" type="button" onclick="cancelarform()">
                  <i class="fa fa-arrow-circle-left"></i> Cancelar
                </button>
              </div>
            </form>
          </div>
          <!-- Fin Formulario -->

           

        </div>
      </div>
    </div>
  </section>
</div>

<?php 
}else{
 require 'noacceso.php'; 
}
require 'footer.php';
 ?>
<script src="scripts/usuario.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  // Nombre SOLO LETRAS
  document.getElementById("nombre").addEventListener("keypress", function (e) {
    if (!/[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(e.key)) {
      e.preventDefault();
      bootbox.alert("Solo puedes ingresar letras en el campo Nombre.");
    }
  });

  // Celular SOLO NÚMEROS
  document.getElementById("celular").addEventListener("keypress", function (e) {
    if (!/[0-9]/.test(e.key)) {
      e.preventDefault();
      bootbox.alert("Solo puedes ingresar números en el campo Celular.");
    }
  });

  // Cédula LETRAS Y NÚMEROS (alfanumérico con guión opcional)
  document.getElementById("celula").addEventListener("keypress", function (e) {
    if (!/[a-zA-Z0-9\-]/.test(e.key)) {
      e.preventDefault();
      bootbox.alert("En el campo Cédula solo puedes ingresar letras, números o guiones.");
    }
  });
});
</script>


<?php 
}
ob_end_flush();
?>
