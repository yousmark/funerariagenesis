var tabla;

function init() {
	mostrarform(false);
	listar();
	cargarContratos();
	cargarCotizaciones();

	$('#formulario').on('submit', function(e){
		e.preventDefault();
		guardaryeditar();
	});
}

function cargarContratos() {
	$.get('../ajax/documento_registro_civil.php?op=listarContratos', function(data){
		var contratos = JSON.parse(data);
		var options = '<option value="">-- Seleccionar --</option>';
		contratos.forEach(function(contrato){
			options += '<option value="'+contrato.idcontrato+'">'+contrato.codigo + ' - ' + contrato.nombre+'</option>';
		});
		$('#idcontrato').html(options);
	});
}

function cargarCotizaciones() {
	$.get('../ajax/documento_registro_civil.php?op=listarCotizaciones', function(data){
		var cotizaciones = JSON.parse(data);
		var options = '<option value="">-- Seleccionar --</option>';
		cotizaciones.forEach(function(cotizacion){
			options += '<option value="'+cotizacion.idcotizacion+'">'+cotizacion.codigo + ' - ' + cotizacion.nombre+'</option>';
		});
		$('#idcotizacion').html(options);
	});
}

function limpiar() {
	$('#nombre_difunto').val("");
	$('#cedula_difunto').val("");
	$('#cedula_difunto_file').val("");
	$('#nombre_responsable').val("");
	$('#cedula_responsable').val("");
	$('#cedula_responsable_file').val("");
	$('#telefono_responsable').val("");
	$('#direccion_responsable').val("");
	$('#testigo1_nombre').val("");
	$('#testigo1_cedula').val("");
	$('#cedula_testigo1_file').val("");
	$('#testigo2_nombre').val("");
	$('#testigo2_cedula').val("");
	$('#cedula_testigo2_file').val("");
	$('#testigo3_nombre').val("");
	$('#testigo3_cedula').val("");
	$('#cedula_testigo3_file').val("");
	$('#observaciones').val("");
	$('#idcontrato').val("");
	$('#idcotizacion').val("");
	$('#iddocumento').val("");
	$('#codigo').val("");
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$('#listadoregistros').hide();
		$('#formularioregistros').show();
		$('#btnGuardar').prop('disabled', false);
	} else {
		$('#listadoregistros').show();
		$('#formularioregistros').hide();
	}
}

function cancelarform() {
	mostrarform(false);
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/documento_registro_civil.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e){ console.log(e.responseText); }
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[0, "desc"]]
	});
}

function validarFormulario() {
	// Validar archivos requeridos
	if (!$('#cedula_difunto_file')[0].files.length) {
		bootbox.alert('Debe subir la cédula del difunto');
		return false;
	}

	if (!$('#cedula_responsable_file')[0].files.length) {
		bootbox.alert('Debe subir la cédula del responsable');
		return false;
	}

	// Validar que al menos hay 2 testigos con archivos
	var testigos_con_archivo = 0;
	if ($('#cedula_testigo1_file')[0].files.length > 0) testigos_con_archivo++;
	if ($('#cedula_testigo2_file')[0].files.length > 0) testigos_con_archivo++;
	if ($('#cedula_testigo3_file')[0].files.length > 0) testigos_con_archivo++;

	if (testigos_con_archivo < 2) {
		bootbox.alert('Debe subir al menos 2 cédulas de testigos');
		return false;
	}

	// Validar que si se sube un archivo de testigo, también se complete la información
	if ($('#cedula_testigo1_file')[0].files.length > 0) {
		if (!$('#testigo1_nombre').val() || !$('#testigo1_cedula').val()) {
			bootbox.alert('Debe completar nombre y cédula del testigo 1');
			return false;
		}
	}

	if ($('#cedula_testigo2_file')[0].files.length > 0) {
		if (!$('#testigo2_nombre').val() || !$('#testigo2_cedula').val()) {
			bootbox.alert('Debe completar nombre y cédula del testigo 2');
			return false;
		}
	}

	if ($('#cedula_testigo3_file')[0].files.length > 0) {
		if (!$('#testigo3_nombre').val() || !$('#testigo3_cedula').val()) {
			bootbox.alert('Debe completar nombre y cédula del testigo 3');
			return false;
		}
	}

	return true;
}

function guardaryeditar(e) {
	if (!validarFormulario()) {
		return false;
	}

	$('#btnGuardar').prop('disabled', true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: '../ajax/documento_registro_civil.php?op=guardar',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function(resp){
			try { 
				var r = JSON.parse(resp); 
			} catch(e){ 
				console.error('Error al parsear respuesta:', resp);
				bootbox.alert('Error: ' + resp);
				$('#btnGuardar').prop('disabled', false);
				return;
			}
			if (r.ok) {
				var msg = 'Documentos guardados correctamente. Código: ' + r.codigo;
				if (r.documentos_completos) {
					msg += '<br><strong>Documentos completos ✓</strong>';
				} else {
					msg += '<br><span class="text-warning">Verifique que todos los documentos estén completos</span>';
				}
				bootbox.alert(msg);
				mostrarform(false);
				tabla.ajax.reload();
			} else {
				bootbox.alert('Error: ' + (r.msg || resp));
				$('#btnGuardar').prop('disabled', false);
			}
		},
		error: function(xhr, status, error){
			console.error('Error en AJAX:', status, error);
			console.error('Respuesta del servidor:', xhr.responseText);
			bootbox.alert('Error al guardar los documentos. Por favor, verifique los datos e intente nuevamente.');
			$('#btnGuardar').prop('disabled', false);
		}
	});
}

function ver(id) {
	$.post('../ajax/documento_registro_civil.php?op=mostrar', {iddocumento:id}, function(data){
		var d = JSON.parse(data);
		if (!d) return;

		var message = '<div class="row">';
		message += '<div class="col-md-12">';
		
		message += '<p><b>Código:</b> ' + d.codigo + '</p>';
		message += '<p><b>Estado:</b> ';
		if (d.estado == 'completo') {
			message += '<span class="label label-success">Completo</span>';
		} else if (d.estado == 'incompleto') {
			message += '<span class="label label-danger">Incompleto</span>';
		} else {
			message += '<span class="label label-warning">Pendiente</span>';
		}
		message += '</p>';
		message += '<p><b>Documentos Completos:</b> ' + (d.documentos_completos ? '<span class="label label-success"><i class="fa fa-check"></i> Sí</span>' : '<span class="label label-danger"><i class="fa fa-times"></i> No</span>') + '</p>';

		message += '<hr><h4>Datos del Difunto</h4>';
		message += '<p><b>Nombre:</b> ' + d.nombre_difunto + '</p>';
		message += '<p><b>Cédula:</b> ' + d.cedula_difunto + '</p>';
		if (d.ruta_cedula_difunto) {
			message += '<p><a target="_blank" href="../' + d.ruta_cedula_difunto + '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver Cédula</a></p>';
		}

		message += '<hr><h4>Datos del Responsable</h4>';
		message += '<p><b>Nombre:</b> ' + d.nombre_responsable + '</p>';
		message += '<p><b>Cédula:</b> ' + d.cedula_responsable + '</p>';
		if (d.telefono_responsable) {
			message += '<p><b>Teléfono:</b> ' + d.telefono_responsable + '</p>';
		}
		if (d.direccion_responsable) {
			message += '<p><b>Dirección:</b> ' + d.direccion_responsable + '</p>';
		}
		if (d.ruta_cedula_responsable) {
			message += '<p><a target="_blank" href="../' + d.ruta_cedula_responsable + '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver Cédula</a></p>';
		}

		message += '<hr><h4>Testigos</h4>';
		
		if (d.testigo1_nombre) {
			message += '<p><b>Testigo 1:</b> ' + d.testigo1_nombre + ' - Cédula: ' + d.testigo1_cedula;
			if (d.testigo1_ruta_cedula) {
				message += ' <a target="_blank" href="../' + d.testigo1_ruta_cedula + '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver</a>';
			}
			message += '</p>';
		}

		if (d.testigo2_nombre) {
			message += '<p><b>Testigo 2:</b> ' + d.testigo2_nombre + ' - Cédula: ' + d.testigo2_cedula;
			if (d.testigo2_ruta_cedula) {
				message += ' <a target="_blank" href="../' + d.testigo2_ruta_cedula + '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver</a>';
			}
			message += '</p>';
		}

		if (d.testigo3_nombre) {
			message += '<p><b>Testigo 3:</b> ' + d.testigo3_nombre + ' - Cédula: ' + d.testigo3_cedula;
			if (d.testigo3_ruta_cedula) {
				message += ' <a target="_blank" href="../' + d.testigo3_ruta_cedula + '" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Ver</a>';
			}
			message += '</p>';
		}

		if (d.observaciones) {
			message += '<hr><h4>Observaciones</h4>';
			message += '<p>' + d.observaciones + '</p>';
		}

		message += '<hr><p><b>Registrado por:</b> ' + d.usuario + '</p>';
		message += '<p><b>Fecha de creación:</b> ' + d.fecha_creacion + '</p>';

		message += '</div></div>';

		bootbox.dialog({
			title: 'Documentos Registro Civil - ' + d.codigo,
			message: message,
			size: 'large',
			buttons: {
				close: {
					label: 'Cerrar',
					className: 'btn-default'
				}
			}
		});
	});
}

init();








