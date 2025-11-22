var tabla;

function init() {
	mostrarform(false);
	listar();
	cargarContratos();
	cargarCotizaciones();

	$('#formulario').on('submit', function(e){
		e.preventDefault();
		generarYEnviarPDF();
	});
}

function cargarContratos() {
	$.get('../ajax/itinerario_funerario.php?op=listarContratos', function(data){
		var contratos = JSON.parse(data);
		var options = '<option value="">-- Seleccionar --</option>';
		contratos.forEach(function(contrato){
			options += '<option value="'+contrato.idcontrato+'">'+contrato.codigo + ' - ' + contrato.nombre+'</option>';
		});
		$('#idcontrato').html(options);
	});
}

function cargarCotizaciones() {
	$.get('../ajax/itinerario_funerario.php?op=listarCotizaciones', function(data){
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
	$('#nombre_familia').val("");
	$('#telefono_contacto').val("");
	$('#fecha_sepelio').val("");
	$('#hora_sepelio').val("");
	$('#lugar_sepelio').val("");
	$('#direccion_sepelio').val("");
	$('#ruta_cortejo').val("");
	$('#punto_partida').val("");
		$('#observaciones').val("");
	$('#idcontrato').val("");
	$('#idcotizacion').val("");
	$('#iditinerario').val("");
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
			url: '../ajax/itinerario_funerario.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e){ console.log(e.responseText); }
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[0, "desc"]]
	});
}

async function generarYEnviarPDF() {
	$('#btnGuardar').prop('disabled', true);
	
	const nombreDifunto = $('#nombre_difunto').val();
	const nombreFamilia = $('#nombre_familia').val();
	const telefonoContacto = $('#telefono_contacto').val();
	const fechaSepelio = $('#fecha_sepelio').val();
	const horaSepelio = $('#hora_sepelio').val();
	const lugarSepelio = $('#lugar_sepelio').val();
	const direccionSepelio = $('#direccion_sepelio').val();
	const rutaCortejo = $('#ruta_cortejo').val();
	const puntoPartida = $('#punto_partida').val();
	const observaciones = $('#observaciones').val();

	// Validar campos requeridos
	if (!nombreDifunto || !nombreFamilia || !fechaSepelio || !horaSepelio || !lugarSepelio || !rutaCortejo) {
		bootbox.alert('Por favor, complete todos los campos requeridos (*)');
		$('#btnGuardar').prop('disabled', false);
		return;
	}

	// Formatear fecha
	const fechaObj = new Date(fechaSepelio);
	const fechaFormateada = fechaObj.toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
	
	// Formatear hora
	const horaFormateada = horaSepelio + ' hrs.';

	// Fecha y hora actual
	const fechaActual = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
	const horaActual = new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });

	const docDef = {
		pageSize: 'LETTER',
		pageMargins: [60, 100, 60, 80],
		header: function(currentPage, pageCount) {
			return [[{
				columns: [{
					text: ['FUNERARIA GENESIS\n', {text: 'COMPROBANTE DE ITINERARIO FUNERARIO', fontSize: 14, bold: true, color: '#333333'}, {text: 'Tel: (591) 2-1234567 | Email: info@funerariagenesis.com', fontSize: 8, color: '#666666'}],
					width: '*'
				}, {
					text: [fechaActual, {text: horaActual, margin: [0, 3, 0, 0]}],
					alignment: 'right',
					width: 'auto'
				}],
				margin: [0, 0, 0, 20],
				border: [false, false, false, true],
				borderColor: '#cccccc'
			}]];
		},
		footer: function(currentPage, pageCount) {
			return {text: 'Página ' + currentPage + ' de ' + pageCount, alignment: 'center', fontSize: 8, color: '#666666'};
		},
		content: [
			{text: 'ITINERARIO FUNERARIO', style: 'title', alignment: 'center', margin: [0, 0, 0, 20]},
			
			{text: 'I. INFORMACIÓN DEL DIFUNTO Y FAMILIA', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{text: [{text: 'Nombre del difunto: ', bold: true}, nombreDifunto], margin: [0, 0, 0, 5]},
			{text: [{text: 'Familia: ', bold: true}, nombreFamilia], margin: [0, 0, 0, 5]},
			telefonoContacto ? {text: [{text: 'Teléfono de contacto: ', bold: true}, telefonoContacto], margin: [0, 0, 0, 20]} : {text: '', margin: [0, 0, 0, 20]},
			
			{text: 'II. INFORMACIÓN DEL SEPELIO', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{columns: [
				{text: [{text: 'Fecha: ', bold: true}, fechaFormateada], width: '*'},
				{text: [{text: 'Hora: ', bold: true}, horaFormateada], width: '*'}
			], margin: [0, 0, 0, 5]},
			{text: [{text: 'Lugar de sepelio: ', bold: true}, lugarSepelio], margin: [0, 0, 0, 5]},
			direccionSepelio ? {text: [{text: 'Dirección: ', bold: true}, direccionSepelio], margin: [0, 0, 0, 20]} : {text: '', margin: [0, 0, 0, 20]},
			
			{text: 'III. RUTA DEL CORTEJO', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			puntoPartida ? {text: [{text: 'Punto de partida: ', bold: true}, puntoPartida], margin: [0, 0, 0, 10]} : {text: '', margin: [0, 0, 0, 10]},
			{
				text: [{text: 'Ruta: ', bold: true}, rutaCortejo],
				margin: [0, 0, 0, 20],
				style: {lineHeight: 1.5}
			},
			
			observaciones ? {
				text: 'IV. OBSERVACIONES',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			} : {text: '', margin: [0, 0, 0, 0]},
			observaciones ? {
				text: observaciones,
				margin: [0, 0, 0, 30],
				style: {lineHeight: 1.5}
			} : {text: '', margin: [0, 0, 0, 30]},
			
			{
				text: 'IMPORTANTE: Este itinerario debe ser confirmado por la familia para evitar errores en el desarrollo del sepelio.',
				style: {fontSize: 9, italics: true, color: '#d9534f'},
				margin: [0, 0, 0, 20],
				alignment: 'center'
			},
			
			{columns: [
				{
					text: ['_________________________', nombreFamilia || 'FAMILIA', 'Confirmación de la Familia'],
					alignment: 'center',
					margin: [0, 20, 0, 0]
				},
				{
					text: ['_________________________', 'FUNERARIA GENESIS', 'Personal Autorizado'],
					alignment: 'center',
					margin: [0, 20, 0, 0]
				}
			]}
		],
		styles: {
			title: {fontSize: 16, bold: true, color: '#333333'},
			sectionHeader: {fontSize: 12, bold: true, color: '#333333', decoration: 'underline'}
		},
		defaultStyle: {fontSize: 10, color: '#333333'}
	};

	const pdfDoc = pdfMake.createPdf(docDef);
	const arrayBuffer = await new Promise((resolve) => pdfDoc.getBuffer((buffer)=> resolve(buffer.buffer)));
	const hashBuf = await crypto.subtle.digest('SHA-256', arrayBuffer);
	const hashHex = Array.from(new Uint8Array(hashBuf)).map(b=>b.toString(16).padStart(2,'0')).join('');

	const blob = new Blob([arrayBuffer], {type: 'application/pdf'});
	const formData = new FormData();
	formData.append('pdf', blob, 'itinerario.pdf');
	formData.append('nombre_difunto', nombreDifunto);
	formData.append('nombre_familia', nombreFamilia);
	formData.append('telefono_contacto', telefonoContacto);
	formData.append('fecha_sepelio', fechaSepelio);
	formData.append('hora_sepelio', horaSepelio);
	formData.append('lugar_sepelio', lugarSepelio);
	formData.append('direccion_sepelio', direccionSepelio);
	formData.append('ruta_cortejo', rutaCortejo);
	formData.append('punto_partida', puntoPartida);
	formData.append('observaciones', observaciones);
	formData.append('idcontrato', $('#idcontrato').val());
	formData.append('idcotizacion', $('#idcotizacion').val());
	formData.append('hash_sha256', hashHex);

	$.ajax({
		url: '../ajax/itinerario_funerario.php?op=guardar',
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
				bootbox.alert('Itinerario guardado. Código: '+r.codigo);
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
			bootbox.alert('Error al guardar el itinerario. Por favor, verifique los datos e intente nuevamente.');
			$('#btnGuardar').prop('disabled', false);
		}
	});
}

function ver(id) {
	$.post('../ajax/itinerario_funerario.php?op=mostrar', {iditinerario:id}, function(data){
		var d = JSON.parse(data);
		if (!d) return;
		
		var link = '../'+d.ruta_pdf;
		var confirmadoHtml = '<span class="label label-success">Confirmado</span>';
		if (d.confirmado_familia == 0) {
			confirmadoHtml = '<span class="label label-warning">Pendiente de confirmación</span>';
		}
		
		// Cargar permisos
		$.post('../ajax/itinerario_funerario.php?op=listarPermisos', {iditinerario:id}, function(permisosData){
			var permisos = JSON.parse(permisosData);
			var permisosHtml = '<h5>Usuarios con permiso de visualización:</h5>';
			if (permisos.length > 0) {
				permisosHtml += '<table class="table table-condensed"><thead><tr><th>Nombre</th><th>Email</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
				permisos.forEach(function(p){
					permisosHtml += '<tr><td>'+p.nombre+'</td><td>'+p.email+'</td><td>'+p.estado+'</td><td>'+p.acciones+'</td></tr>';
				});
				permisosHtml += '</tbody></table>';
			} else {
				permisosHtml += '<p class="text-muted">No hay usuarios con permiso asignado</p>';
			}
			
			var message = '<div class="row">'+
				'<div class="col-md-12">'+
				'<p><b>Código:</b> '+d.codigo+'</p>'+
				'<p><b>Difunto:</b> '+d.nombre_difunto+'</p>'+
				'<p><b>Familia:</b> '+d.nombre_familia+'</p>'+
				'<p><b>Fecha/Hora Sepelio:</b> '+d.fecha_sepelio+' '+d.hora_sepelio+'</p>'+
				'<p><b>Lugar:</b> '+d.lugar_sepelio+'</p>'+
				'<p><b>Confirmación Familia:</b> '+confirmadoHtml+'</p>'+
				'<hr>'+
				'<p><a target="_blank" class="btn btn-primary" href="'+link+'"><i class="fa fa-download"></i> Descargar PDF</a></p>'+
				'<hr>'+
				'<button class="btn btn-success" onclick="mostrarModalClientes('+id+')"><i class="fa fa-user-plus"></i> Otorgar Permiso a Usuario</button>'+
				'<hr>'+
				permisosHtml+
				'</div></div>';
			
			bootbox.dialog({
				title: 'Itinerario '+d.codigo,
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
	});
}

function mostrarModalClientes(iditinerario) {
	$.post('../ajax/itinerario_funerario.php?op=listarClientes', function(data){
		var clientes = JSON.parse(data);
		var options = '<option value="">-- Seleccionar usuario --</option>';
		clientes.forEach(function(cliente){
			options += '<option value="'+cliente.idcliente+'">'+cliente.nombre+' ('+cliente.email+')</option>';
		});
		
		var modal = '<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog">'+
			'<div class="modal-dialog" role="document">'+
			'<div class="modal-content">'+
			'<div class="modal-header">'+
			'<button type="button" class="close" data-dismiss="modal">&times;</button>'+
			'<h4 class="modal-title">Otorgar Permiso</h4>'+
			'</div>'+
			'<div class="modal-body">'+
			'<label>Seleccionar usuario:</label>'+
			'<select class="form-control" id="selectCliente">'+options+'</select>'+
			'</div>'+
			'<div class="modal-footer">'+
			'<button type="button" class="btn btn-primary" onclick="otorgarPermiso('+iditinerario+')">Otorgar Permiso</button>'+
			'<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>'+
			'</div>'+
			'</div>'+
			'</div>'+
			'</div>';
		
		$('body').append(modal);
		$('#modalClientes').modal('show');
		$('#modalClientes').on('hidden.bs.modal', function(){
			$(this).remove();
		});
	});
}

function otorgarPermiso(iditinerario) {
	var idcliente = $('#selectCliente').val();
	if (!idcliente) {
		bootbox.alert('Por favor, seleccione un usuario');
		return;
	}
	
	$.post('../ajax/itinerario_funerario.php?op=otorgarPermiso', {
		iditinerario: iditinerario,
		idusuario_permiso: idcliente
	}, function(data){
		var r = JSON.parse(data);
		bootbox.alert(r.msg, function(){
			$('#modalClientes').modal('hide');
			ver(iditinerario);
		});
	});
}

function quitarPermiso(iditinerario, idcliente) {
	bootbox.confirm('¿Está seguro de revocar el permiso a este usuario?', function(result){
		if (result) {
			$.post('../ajax/itinerario_funerario.php?op=quitarPermiso', {
				iditinerario: iditinerario,
				idusuario_permiso: idcliente
			}, function(data){
				var r = JSON.parse(data);
				bootbox.alert(r.msg, function(){
					ver(iditinerario);
				});
			});
		}
	});
}

init();








