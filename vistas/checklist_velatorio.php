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
      Checklist Velatorio
      <small>Verificación de elementos del velatorio</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Checklist Velatorio</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Checklist de Montaje de Velatorio <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nueva Checklist</button></h1>
          </div>
          <div class="box-body">
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Contacto</th>
                  <th>Usuario</th>
                  <th>Fecha Velatorio</th>
                  <th>Estado</th>
                  <th>Fecha Creación</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Contacto</th>
                  <th>Usuario</th>
                  <th>Fecha Velatorio</th>
                  <th>Estado</th>
                  <th>Fecha Creación</th>
                </tfoot>
              </table>
            </div>

            <div class="panel-body" style="height: auto;" id="formularioregistros">
              <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                
                <!-- INFORMACIÓN GENERAL -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:0 0 15px 0;">Información General</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-6"><div class="form-group"><label>Nombre del difunto(*)</label><input type="text" class="form-control" name="nombre_difunto" id="nombre_difunto" required></div></div>
                  <div class="col-sm-6"><div class="form-group"><label>Nombre del contacto(*)</label><input type="text" class="form-control" name="nombre_contacto" id="nombre_contacto" required></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Teléfono de contacto</label>
                      <input type="text" class="form-control" name="telefono_contacto" id="telefono_contacto"
                             data-validacion="telefono" maxlength="8" placeholder="Ej: 71234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
                    </div>
                  </div>
                  <div class="col-sm-4"><div class="form-group"><label>Lugar del velatorio</label><input type="text" class="form-control" name="lugar_velatorio" id="lugar_velatorio"></div></div>
                  <div class="col-sm-4"><div class="form-group"><label>Fecha y hora del velatorio(*)</label><input type="datetime-local" class="form-control" name="fecha_velatorio" id="fecha_velatorio" required></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-6"><div class="form-group"><label>Contrato asociado (opcional)</label><select class="form-control" name="idcontrato" id="idcontrato"><option value="">-- Seleccionar --</option></select></div></div>
                  <div class="col-sm-6"><div class="form-group"><label>Cotización asociada (opcional)</label><select class="form-control" name="idcotizacion" id="idcotizacion"><option value="">-- Seleccionar --</option></select></div></div>
                </div>

                <!-- CHECKLIST ATAÚD -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Ataúd</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-6"><div class="form-group"><label>Tipo de ataúd</label><input type="text" class="form-control" name="ataud_tipo" id="ataud_tipo" placeholder="Ej: Estándar, Premium, De lujo"></div></div>
                  <div class="col-sm-6"><div class="form-group"><label>Medidas</label><input type="text" class="form-control" name="ataud_medidas" id="ataud_medidas" placeholder="Ej: 200cm x 70cm"></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-12"><div class="form-group"><label>Observaciones del ataúd</label><textarea class="form-control" name="ataud_observaciones" id="ataud_observaciones" rows="2"></textarea></div></div>
                </div>

                <!-- CHECKLIST DECORACIÓN -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Decoración</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="decoracion_flores" id="decoracion_flores" value="Si"> Arreglos florales</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="text" class="form-control" name="decoracion_flores_detalle" id="decoracion_flores_detalle" placeholder="Detalle de arreglos" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="decoracion_cortinas" id="decoracion_cortinas" value="Si"> Cortinas</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="text" class="form-control" name="decoracion_cortinas_color" id="decoracion_cortinas_color" placeholder="Color de cortinas" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="decoracion_alfombra" id="decoracion_alfombra" value="Si"> Alfombra</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="text" class="form-control" name="decoracion_alfombra_color" id="decoracion_alfombra_color" placeholder="Color de alfombra" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="decoracion_iluminacion" id="decoracion_iluminacion" value="Si" checked> Iluminación</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="text" class="form-control" name="decoracion_iluminacion_tipo" id="decoracion_iluminacion_tipo" placeholder="Tipo de iluminación"></div></div>
                </div>

                <!-- CHECKLIST MOBILIARIO -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Mobiliario</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="mobiliario_catafalco" id="mobiliario_catafalco" value="Si"> Catafalco</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="text" class="form-control" name="mobiliario_catafalco_tipo" id="mobiliario_catafalco_tipo" placeholder="Tipo de catafalco" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="form-group"><label>Sillas</label><input type="number" class="form-control" name="mobiliario_sillas" id="mobiliario_sillas" value="0"></div></div>
                  <div class="col-sm-4"><div class="form-group"><label>Mesas</label><input type="number" class="form-control" name="mobiliario_mesas" id="mobiliario_mesas" value="0"></div></div>
                  <div class="col-sm-4"><div class="form-group"><label>Bancos</label><input type="number" class="form-control" name="mobiliario_bancos" id="mobiliario_bancos" value="0"></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-12"><div class="checkbox"><label><input type="checkbox" name="mobiliario_libro" id="mobiliario_libro" value="Si"> Libro de condolencias</label></div></div>
                </div>

                <!-- CHECKLIST ELEMENTOS ADICIONALES -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Elementos Adicionales</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-6"><div class="checkbox"><label><input type="checkbox" name="adicional_cafeteria" id="adicional_cafeteria" value="Si"> Cafetería</label></div></div>
                  <div class="col-sm-6"><div class="checkbox"><label><input type="checkbox" name="adicional_reproduccion_musical" id="adicional_reproduccion_musical" value="Si"> Reproducción musical</label></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="adicional_pantallas" id="adicional_pantallas" value="Si"> Pantallas</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="number" class="form-control" name="adicional_pantallas_cantidad" id="adicional_pantallas_cantidad" value="0" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-4"><div class="checkbox"><label><input type="checkbox" name="adicional_ventiladores" id="adicional_ventiladores" value="Si"> Ventiladores</label></div></div>
                  <div class="col-sm-8"><div class="form-group"><input type="number" class="form-control" name="adicional_ventiladores_cantidad" id="adicional_ventiladores_cantidad" value="0" disabled></div></div>
                </div>
                <div class="row">
                  <div class="col-sm-12"><div class="checkbox"><label><input type="checkbox" name="adicional_extintores" id="adicional_extintores" value="Si"> Extintores</label></div></div>
                </div>

                <!-- OBSERVACIONES -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Observaciones Generales</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-12"><div class="form-group"><textarea class="form-control" name="observaciones_generales" id="observaciones_generales" rows="3"></textarea></div></div>
                </div>

                <input type="hidden" name="hash_sha256" id="hash_sha256">

                <div class="form-group">
                  <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Generar PDF y Guardar</button>
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

<script src="scripts/checklist_velatorio.js"></script>

<?php
}
ob_end_flush();
?>

