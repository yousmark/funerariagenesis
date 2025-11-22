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
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Contratos
      <small>Gestión de contratos de servicios</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Contratos</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Contratos <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nuevo</button></h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Contratante</th>
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Contratante</th>
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tfoot>
              </table>
            </div>

            <div class="panel-body" style="height: auto;" id="formularioregistros">
              <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                <!-- Selector de Cotización -->
                <div class="row">
                  <div class="col-sm-12">
                    <div class="alert alert-info">
                      <h4 style="margin-top: 0;"><i class="fa fa-info-circle"></i> Cargar desde Cotización</h4>
                      <div class="row">
                        <div class="col-sm-8">
                          <div class="form-group">
                            <label>Seleccionar cotización existente (opcional):</label>
                            <select class="form-control" id="select_cotizacion" onchange="cargarDesdeCotizacion()">
                              <option value="">-- Seleccione una cotización para cargar los datos --</option>
                            </select>
                            <small class="help-block-info"><i class="fa fa-info-circle"></i> Al seleccionar una cotización, se cargarán automáticamente los datos del fallecido, contratante y servicios</small>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info btn-block" onclick="cargarListaCotizaciones()">
                              <i class="fa fa-refresh"></i> Actualizar Lista
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:0 0 15px 0;">Datos del Fallecido</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del fallecido(*)</label>
                      <input type="text" class="form-control" name="nombres_fallecido" id="nombres_fallecido" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del fallecido(*)</label>
                      <input type="text" class="form-control" name="apellidos_fallecido" id="apellidos_fallecido" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Fecha de fallecimiento</label>
                      <input type="date" class="form-control" name="fecha_fallecimiento" id="fecha_fallecimiento">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Edad</label>
                      <input type="number" class="form-control" name="edad_fallecido" id="edad_fallecido" 
                             min="0" max="122" placeholder="Ej: 75">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Máximo 122 años</small>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Datos del Contratante</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del contratante(*)</label>
                      <input type="text" class="form-control" name="nombres_contratante" id="nombres_contratante" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del contratante(*)</label>
                      <input type="text" class="form-control" name="apellidos_contratante" id="apellidos_contratante" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Cédula de Identidad</label>
                      <input type="text" class="form-control" name="cedula_contratante" id="cedula_contratante"
                             data-validacion="cedula" maxlength="8" placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control" name="complemento_contratante" id="complemento_contratante"
                             data-validacion="complemento" maxlength="1" placeholder="A" style="text-transform: uppercase;">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Celular/Teléfono</label>
                      <input type="text" class="form-control" name="contacto_celular" id="contacto_celular"
                             data-validacion="telefono" maxlength="8" placeholder="Ej: 71234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Dirección</label>
                      <input type="text" class="form-control" name="direccion_contratante" id="direccion_contratante">
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Correo de contacto</label>
                      <input type="email" class="form-control" name="contacto_email" id="contacto_email">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Servicios Contratados</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Seleccione los servicios y productos:</label>
                      
                      <!-- Botones de selección rápida -->
                      <div style="margin-bottom: 15px;">
                        <button type="button" class="btn btn-success btn-sm" onclick="seleccionarPaquete('basico')">
                          <i class="fa fa-check-circle"></i> Seleccionar Paquete Básico
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="seleccionarPaquete('intermedio')">
                          <i class="fa fa-check-circle"></i> Seleccionar Paquete Intermedio
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="seleccionarPaquete('premium')">
                          <i class="fa fa-check-circle"></i> Seleccionar Paquete Premium
                        </button>
                        <button type="button" class="btn btn-default btn-sm" onclick="seleccionarPaquete('todos')">
                          <i class="fa fa-check-square"></i> Seleccionar Todos
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="seleccionarPaquete('ninguno')">
                          <i class="fa fa-times"></i> Desmarcar Todos
                        </button>
                      </div>
                      
                      <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background-color: #f9f9f9; max-height: 500px; overflow-y: auto;">
                        <!-- Grupo 1 -->
                        <h5 style="margin-top: 10px; color: #333; font-weight: bold;">Paquete Básico:</h5>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Ataúd estándar: 0" class="servicio-check basico"> Ataúd estándar</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Traslado local: 0" class="servicio-check basico"> Traslado local</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Preparación e higiene del cuerpo: 0" class="servicio-check basico"> Preparación e higiene del cuerpo</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Sala velatoria 6 hrs: 0" class="servicio-check basico"> Sala velatoria 6 hrs</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Trámites básicos: 0" class="servicio-check basico"> Trámites básicos</label>
                        </div>
                        
                        <!-- Grupo 2 -->
                        <h5 style="margin-top: 20px; color: #333; font-weight: bold;">Paquete Intermedio:</h5>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Ataúd de madera barnizada: 0" class="servicio-check intermedio"> Ataúd de madera barnizada</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Arreglos florales: 0" class="servicio-check intermedio"> Arreglos florales</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Cafetería: 0" class="servicio-check intermedio"> Cafetería</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Libro de condolencias: 0" class="servicio-check intermedio"> Libro de condolencias</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Transporte al cementerio: 0" class="servicio-check intermedio"> Transporte al cementerio</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Sala velatoria 12 hrs: 0" class="servicio-check intermedio"> Sala velatoria 12 hrs</label>
                        </div>
                        
                        <!-- Grupo 3 -->
                        <h5 style="margin-top: 20px; color: #333; font-weight: bold;">Paquete Premium:</h5>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Ataúd de lujo: 0" class="servicio-check premium"> Ataúd de lujo</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Arreglos grandes: 0" class="servicio-check premium"> Arreglos grandes</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Traslado nacional: 0" class="servicio-check premium"> Traslado nacional</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Servicio musical: 0" class="servicio-check premium"> Servicio musical</label>
                        </div>
                        <div class="checkbox">
                          <label><input type="checkbox" name="servicios[]" value="Cremación o sepultura: 0" class="servicio-check premium"> Cremación o sepultura</label>
                        </div>
                      </div>
                      <small class="help-block">Marque los servicios que desea incluir en el contrato</small>
                    </div>
                  </div>
                </div>
                <input type="hidden" name="detalle" id="detalle">

                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Resumen</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Total (Bs)(*)</label>
                      <input type="number" step="0.01" class="form-control" name="total" id="total" required>
                    </div>
                  </div>
                </div>

                <input type="hidden" name="detalle_json" id="detalle_json">
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

<?php
require 'footer.php';
?>

<script src="../public/datatables/pdfmake.min.js"></script>
<script src="../public/datatables/vfs_fonts.js"></script>
<script src="scripts/contrato.js"></script>

<?php
}
ob_end_flush();
?>



