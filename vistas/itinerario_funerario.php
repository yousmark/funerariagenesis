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
      Itinerarios Funerarios
      <small>Gestión de itinerarios y rutas</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Itinerarios Funerarios</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Itinerarios Funerarios <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nuevo Itinerario</button></h1>
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
                  <th>Estado</th>
                  <th>Usuario</th>
                  <th>Fecha Creación</th>
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
                  <th>Estado</th>
                  <th>Usuario</th>
                  <th>Fecha Creación</th>
                </tfoot>
              </table>
            </div>

            <div class="panel-body" style="height: auto; display:none;" id="formularioregistros">
              <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                
                <!-- INFORMACIÓN GENERAL -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:0 0 15px 0;">Información General</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Contrato asociado (opcional)</label>
                      <select class="form-control" name="idcontrato" id="idcontrato">
                        <option value="">-- Seleccionar --</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Cotización asociada (opcional)</label>
                      <select class="form-control" name="idcotizacion" id="idcotizacion">
                        <option value="">-- Seleccionar --</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- DATOS DEL DIFUNTO Y FAMILIA -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Datos del Difunto y Familia</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del difunto (*)</label>
                      <input type="text" class="form-control" name="nombres_difunto" id="nombres_difunto" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del difunto (*)</label>
                      <input type="text" class="form-control" name="apellidos_difunto" id="apellidos_difunto" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres de la familia (*)</label>
                      <input type="text" class="form-control" name="nombres_familia" id="nombres_familia" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos de la familia (*)</label>
                      <input type="text" class="form-control" name="apellidos_familia" id="apellidos_familia" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Teléfono de contacto</label>
                      <input type="text" class="form-control" name="telefono_contacto" id="telefono_contacto"
                             data-validacion="telefono" maxlength="8" placeholder="Ej: 71234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
                    </div>
                  </div>
                </div>

                <!-- INFORMACIÓN DEL SEPELIO -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Información del Sepelio</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Fecha del sepelio (*)</label>
                      <input type="date" class="form-control" name="fecha_sepelio" id="fecha_sepelio" required>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Hora del sepelio (*)</label>
                      <input type="time" class="form-control" name="hora_sepelio" id="hora_sepelio" required>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Lugar de sepelio (*)</label>
                      <input type="text" class="form-control" name="lugar_sepelio" id="lugar_sepelio" placeholder="Ej: Cementerio General" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Dirección del lugar de sepelio</label>
                      <textarea class="form-control" name="direccion_sepelio" id="direccion_sepelio" rows="2" placeholder="Dirección completa del cementerio o lugar de sepelio..."></textarea>
                    </div>
                  </div>
                </div>

                <!-- RUTA DEL CORTEJO -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Ruta del Cortejo</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Punto de partida</label>
                      <input type="text" class="form-control" name="punto_partida" id="punto_partida" placeholder="Ej: Funeraria Génesis, Calle Principal 123">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Ruta del cortejo (*)</label>
                      <textarea class="form-control" name="ruta_cortejo" id="ruta_cortejo" rows="4" placeholder="Descripción detallada de la ruta que seguirá el cortejo fúnebre..." required></textarea>
                      <small class="text-muted">Indique las calles, avenidas y puntos de referencia por los que pasará el cortejo</small>
                    </div>
                  </div>
                </div>

                <!-- OBSERVACIONES -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Observaciones</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <textarea class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Observaciones adicionales sobre el itinerario..."></textarea>
                    </div>
                  </div>
                </div>

                <input type="hidden" name="iditinerario" id="iditinerario">
                <input type="hidden" name="codigo" id="codigo">

                <div class="form-group">
                  <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Generar y Guardar Itinerario</button>
                  <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require 'footer.php'; ?>

<script type="text/javascript" src="../public/js/pdfmake.min.js"></script>
<script type="text/javascript" src="../public/js/vfs_fonts.js"></script>
<script src="scripts/itinerario_funerario.js"></script>

<?php
}
ob_end_flush();
?>

