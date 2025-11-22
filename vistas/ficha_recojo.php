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
      Ficha de Recojo
      <small>Registro de recolección del difunto</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Ficha de Recojo</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h1 class="box-title">Ficha de Recojo del Difunto <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nueva Ficha</button></h1>
            <div class="box-tools pull-right"></div>
          </div>
          <div class="box-body">
            <div class="panel-body table-responsive" id="listadoregistros">
              <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Usuario</th>
                  <th>Fecha Recojo</th>
                  <th>Estado</th>
                  <th>Fecha Creación</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <th>Opciones</th>
                  <th>Código</th>
                  <th>Difunto</th>
                  <th>Usuario</th>
                  <th>Fecha Recojo</th>
                  <th>Estado</th>
                  <th>Fecha Creación</th>
                </tfoot>
              </table>
            </div>

            <div class="panel-body" style="height: auto;" id="formularioregistros">
              <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                
                <!-- DATOS DEL DIFUNTO -->
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:0 0 15px 0;">Datos del Difunto</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Nombres del difunto(*)</label>
                      <input type="text" class="form-control" name="nombres_difunto" id="nombres_difunto" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Apellidos del difunto(*)</label>
                      <input type="text" class="form-control" name="apellidos_difunto" id="apellidos_difunto" 
                             data-validacion="soloLetras" required placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Cédula de Identidad</label>
                      <input type="text" class="form-control" name="cedula_difunto" id="cedula_difunto"
                             data-validacion="cedula" maxlength="8" placeholder="Ej: 1234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Entre 6 y 8 dígitos</small>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Complemento</label>
                      <input type="text" class="form-control text-uppercase" name="complemento_ci" id="complemento_ci"
                             data-validacion="complemento" maxlength="1" placeholder="A">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> 1 letra</small>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Edad</label>
                      <input type="number" class="form-control" name="edad_difunto" id="edad_difunto" 
                             min="0" max="122" placeholder="Ej: 75">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Máximo 122 años</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Fecha y hora de fallecimiento(*)</label>
                      <input type="datetime-local" class="form-control" name="fecha_fallecimiento" id="fecha_fallecimiento" required>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Lugar de fallecimiento</label>
                      <input type="text" class="form-control" name="lugar_fallecimiento" id="lugar_fallecimiento">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Causa de muerte</label>
                      <input type="text" class="form-control" name="causa_muerte" id="causa_muerte">
                    </div>
                  </div>
                </div>

                <!-- INFORMACIÓN DEL LUGAR DE RECOJO -->
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Información del Lugar de Recojo</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Dirección de recojo(*)</label>
                      <textarea class="form-control" name="direccion_recojo" id="direccion_recojo" rows="2" required></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Ciudad</label>
                      <input type="text" class="form-control" name="ciudad_recojo" id="ciudad_recojo">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Barrio/Zona</label>
                      <input type="text" class="form-control" name="barrio_zona" id="barrio_zona">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Tipo de lugar</label>
                      <select class="form-control" name="tipo_lugar" id="tipo_lugar">
                        <option value="Domicilio">Domicilio</option>
                        <option value="Hospital">Hospital</option>
                        <option value="Clinica">Clínica</option>
                        <option value="Calle">Calle</option>
                        <option value="Otro">Otro</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Condiciones del lugar (iluminación, accesibilidad, etc.)</label>
                      <textarea class="form-control" name="condiciones_lugar" id="condiciones_lugar" rows="2"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Temperatura ambiente (°C)</label>
                      <input type="number" step="0.01" class="form-control" name="temperatura_ambiente" id="temperatura_ambiente">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Tiempo transcurrido desde muerte</label>
                      <input type="text" class="form-control" name="tiempo_trascurrido" id="tiempo_trascurrido" placeholder="Ej: 2 horas">
                    </div>
                  </div>
                </div>

                <!-- ESTADO DEL CUERPO -->
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Estado del Cuerpo</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Estado general</label>
                      <select class="form-control" name="estado_general" id="estado_general">
                        <option value="Conservado">Conservado</option>
                        <option value="En Descomposicion">En Descomposición</option>
                        <option value="Momificado">Momificado</option>
                        <option value="Carbonizado">Carbonizado</option>
                        <option value="Otro">Otro</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Rigor mortis</label>
                      <select class="form-control" name="rigor_mortis" id="rigor_mortis">
                        <option value="Presente">Presente</option>
                        <option value="Ausente">Ausente</option>
                        <option value="Parcial">Parcial</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Livideces</label>
                      <select class="form-control" name="livideces" id="livideces">
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                        <option value="Parcial">Parcial</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Lesiones visibles</label>
                      <textarea class="form-control" name="lesiones_visibles" id="lesiones_visibles" rows="2"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Indumentaria presente</label>
                      <textarea class="form-control" name="indumentaria" id="indumentaria" rows="2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Observaciones adicionales del cuerpo</label>
                      <textarea class="form-control" name="observaciones_cuerpo" id="observaciones_cuerpo" rows="3"></textarea>
                    </div>
                  </div>
                </div>

                <!-- BIOSEGURIDAD Y PERSONAL -->
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Bioseguridad y Personal</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Equipo de recojo</label>
                      <textarea class="form-control" name="equipo_recojo" id="equipo_recojo" rows="2"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Personal presente</label>
                      <textarea class="form-control" name="personal_presente" id="personal_presente" rows="2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Insumos de bioseguridad utilizados</label>
                      <textarea class="form-control" name="insumos_bioseguridad" id="insumos_bioseguridad" rows="3"></textarea>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Protocolos aplicados</label>
                      <textarea class="form-control" name="protocolos_aplicados" id="protocolos_aplicados" rows="3"></textarea>
                    </div>
                  </div>
                </div>

                <!-- DATOS DE CONTACTO -->
                <div class="row">
                  <div class="col-sm-12">
                    <h4 class="bg-primary" style="padding:10px; color:white; margin:15px 0;">Datos de Contacto</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Nombres del contacto</label>
                      <input type="text" class="form-control" name="nombres_contacto" id="nombres_contacto" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Apellidos del contacto</label>
                      <input type="text" class="form-control" name="apellidos_contacto" id="apellidos_contacto" 
                             data-validacion="soloLetras" placeholder="Solo letras y espacios">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Solo letras y espacios</small>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Teléfono</label>
                      <input type="text" class="form-control" name="telefono_contacto" id="telefono_contacto"
                             data-validacion="telefono" maxlength="8" placeholder="Ej: 71234567">
                      <small class="help-block-info"><i class="fa fa-info-circle"></i> Exactamente 8 dígitos (ejemplo: 71234567)</small>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Parentesco</label>
                      <input type="text" class="form-control" name="parentesco_contacto" id="parentesco_contacto">
                    </div>
                  </div>
                </div>

                <!-- FECHA DE RECOJO -->
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Fecha y hora de recojo(*)</label>
                      <input type="datetime-local" class="form-control" name="fecha_recojo" id="fecha_recojo" required>
                    </div>
                  </div>
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

<?php
require 'footer.php';
?>

<script src="../public/datatables/pdfmake.min.js"></script>
<script src="../public/datatables/vfs_fonts.js"></script>
<script src="scripts/ficha_recojo.js"></script>

<?php
}
ob_end_flush();
?>



