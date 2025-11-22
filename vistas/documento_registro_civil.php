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
      Documentos Registro Civil
      <small>Gestión de documentos legales</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Documentos Registro Civil</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Documentos Registro Civil <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nuevo Registro</button></h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Responsable</th>
                  <th>Completos</th>
                  <th>Estado</th>
                  <th>Usuario</th>
                  <th>Fecha Creación</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Responsable</th>
                  <th>Completos</th>
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

                <!-- DATOS DEL DIFUNTO -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Datos del Difunto</h4></div>
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
                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Cédula del difunto (*)</label>
                      <input type="text" class="form-control" name="cedula_difunto" id="cedula_difunto" 
                             data-validacion="cedula" maxlength="8" required placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control text-uppercase" name="complemento_ci_difunto" id="complemento_ci_difunto"
                             data-validacion="complemento" maxlength="1" placeholder="A">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> 1 letra</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Cédula del difunto - Archivo (*)</label>
                      <input type="file" class="form-control" name="cedula_difunto_file" id="cedula_difunto_file" accept="image/*,.pdf" required>
                      <small class="text-muted">Formatos aceptados: JPG, PNG, PDF</small>
                    </div>
                  </div>
                </div>

                <!-- DATOS DEL RESPONSABLE -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Datos del Responsable</h4></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del responsable (*)</label>
                      <input type="text" class="form-control" name="nombres_responsable" id="nombres_responsable" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del responsable (*)</label>
                      <input type="text" class="form-control" name="apellidos_responsable" id="apellidos_responsable" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Cédula del responsable (*)</label>
                      <input type="text" class="form-control" name="cedula_responsable" id="cedula_responsable" 
                             data-validacion="cedula" maxlength="8" required placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control" name="complemento_responsable" id="complemento_responsable"
                             data-validacion="complemento" maxlength="1" placeholder="A" style="text-transform: uppercase;">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Cédula del responsable - Archivo (*)</label>
                      <input type="file" class="form-control" name="cedula_responsable_file" id="cedula_responsable_file" accept="image/*,.pdf" required>
                      <small class="text-muted">Formatos aceptados: JPG, PNG, PDF</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Teléfono del responsable</label>
                      <input type="text" class="form-control" name="telefono_responsable" id="telefono_responsable"
                             data-validacion="telefono" maxlength="8" placeholder="Ej: 71234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Dirección del responsable</label>
                      <input type="text" class="form-control" name="direccion_responsable" id="direccion_responsable">
                    </div>
                  </div>
                </div>

                <!-- TESTIGOS (Mínimo 2 requeridos) -->
                <div class="row">
                  <div class="col-sm-12"><h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Testigos (Mínimo 2 requeridos)</h4></div>
                </div>
                
                <!-- Testigo 1 -->
                <div class="row">
                  <div class="col-sm-12"><h5 style="margin:10px 0;"><strong>Testigo 1</strong></h5></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del testigo 1</label>
                      <input type="text" class="form-control" name="testigo1_nombres" id="testigo1_nombres" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del testigo 1</label>
                      <input type="text" class="form-control" name="testigo1_apellidos" id="testigo1_apellidos" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Cédula del testigo 1</label>
                      <input type="text" class="form-control" name="testigo1_cedula" id="testigo1_cedula"
                             data-validacion="cedula" maxlength="8" placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control" name="complemento_testigo1" id="complemento_testigo1"
                             data-validacion="complemento" maxlength="1" placeholder="A" style="text-transform: uppercase;">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Cédula del testigo 1 - Archivo</label>
                      <input type="file" class="form-control" name="cedula_testigo1" id="cedula_testigo1_file" accept="image/*,.pdf">
                      <small class="text-muted">Formatos aceptados: JPG, PNG, PDF</small>
                    </div>
                  </div>
                </div>

                <!-- Testigo 2 -->
                <div class="row">
                  <div class="col-sm-12"><h5 style="margin:10px 0;"><strong>Testigo 2</strong></h5></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del testigo 2</label>
                      <input type="text" class="form-control" name="testigo2_nombres" id="testigo2_nombres" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del testigo 2</label>
                      <input type="text" class="form-control" name="testigo2_apellidos" id="testigo2_apellidos" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>Cédula del testigo 2</label>
                      <input type="text" class="form-control" name="testigo2_cedula" id="testigo2_cedula"
                             data-validacion="cedula" maxlength="8" placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control" name="complemento_testigo2" id="complemento_testigo2"
                             data-validacion="complemento" maxlength="1" placeholder="A" style="text-transform: uppercase;">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Cédula del testigo 2 - Archivo</label>
                      <input type="file" class="form-control" name="cedula_testigo2" id="cedula_testigo2_file" accept="image/*,.pdf">
                      <small class="text-muted">Formatos aceptados: JPG, PNG, PDF</small>
                    </div>
                  </div>
                </div>

                <!-- Testigo 3 (Opcional) -->
                <div class="row">
                  <div class="col-sm-12"><h5 style="margin:10px 0;"><strong>Testigo 3 (Opcional)</strong></h5></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del testigo 3</label>
                      <input type="text" class="form-control" name="testigo3_nombres" id="testigo3_nombres" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del testigo 3</label>
                      <input type="text" class="form-control" name="testigo3_apellidos" id="testigo3_apellidos" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Cédula del testigo 3</label>
                      <input type="text" class="form-control" name="testigo3_cedula" id="testigo3_cedula"
                             data-validacion="cedula" maxlength="8" placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control" name="complemento_testigo3" id="complemento_testigo3"
                             data-validacion="complemento" maxlength="1" placeholder="A" style="text-transform: uppercase;">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Una letra</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control text-uppercase" name="complemento_ci_testigo3" id="complemento_ci_testigo3"
                             data-validacion="complemento" maxlength="1" placeholder="A">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> 1 letra</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Cédula del testigo 3 - Archivo</label>
                      <input type="file" class="form-control" name="cedula_testigo3" id="cedula_testigo3_file" accept="image/*,.pdf">
                      <small class="text-muted">Formatos aceptados: JPG, PNG, PDF</small>
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
                      <textarea class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Notas adicionales sobre los documentos..."></textarea>
                    </div>
                  </div>
                </div>

                <input type="hidden" name="iddocumento" id="iddocumento">
                <input type="hidden" name="codigo" id="codigo">

                <div class="form-group">
                  <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar Documentos</button>
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

<script src="scripts/documento_registro_civil.js"></script>

<?php
}
ob_end_flush();
?>
