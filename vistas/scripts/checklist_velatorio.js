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
	
	// Manejar eventos de checkboxes para habilitar/deshabilitar campos
	$('#decoracion_flores').on('change', function(){
		$('#decoracion_flores_detalle').prop('disabled', !this.checked);
	});
	$('#decoracion_cortinas').on('change', function(){
		$('#decoracion_cortinas_color').prop('disabled', !this.checked);
	});
	$('#decoracion_alfombra').on('change', function(){
		$('#decoracion_alfombra_color').prop('disabled', !this.checked);
	});
	$('#mobiliario_catafalco').on('change', function(){
		$('#mobiliario_catafalco_tipo').prop('disabled', !this.checked);
	});
	$('#adicional_pantallas').on('change', function(){
		$('#adicional_pantallas_cantidad').prop('disabled', !this.checked);
	});
	$('#adicional_ventiladores').on('change', function(){
		$('#adicional_ventiladores_cantidad').prop('disabled', !this.checked);
	});
}

function cargarContratos() {
	$.get('../ajax/checklist_velatorio.php?op=listarContratos', function(data){
		var contratos = JSON.parse(data);
		var options = '<option value="">-- Seleccionar --</option>';
		contratos.forEach(function(contrato){
			options += '<option value="'+contrato.idcontrato+'">'+contrato.codigo + ' - ' + contrato.nombre+'</option>';
		});
		$('#idcontrato').html(options);
	});
}

function cargarCotizaciones() {
	$.get('../ajax/checklist_velatorio.php?op=listarCotizaciones', function(data){
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
	$('#nombre_contacto').val("");
	$('#telefono_contacto').val("");
	$('#lugar_velatorio').val("");
	$('#idcontrato').val("");
	$('#idcotizacion').val("");
	$('#ataud_tipo').val("");
	$('#ataud_medidas').val("");
	$('#ataud_observaciones').val("");
	$('#decoracion_flores').prop('checked', false);
	$('#decoracion_flores_detalle').val("").prop('disabled', true);
	$('#decoracion_cortinas').prop('checked', false);
	$('#decoracion_cortinas_color').val("").prop('disabled', true);
	$('#decoracion_alfombra').prop('checked', false);
	$('#decoracion_alfombra_color').val("").prop('disabled', true);
	$('#decoracion_iluminacion').prop('checked', true);
	$('#decoracion_iluminacion_tipo').val("");
	$('#mobiliario_catafalco').prop('checked', false);
	$('#mobiliario_catafalco_tipo').val("").prop('disabled', true);
	$('#mobiliario_sillas').val("0");
	$('#mobiliario_mesas').val("0");
	$('#mobiliario_bancos').val("0");
	$('#mobiliario_libro').prop('checked', false);
	$('#adicional_cafeteria').prop('checked', false);
	$('#adicional_reproduccion_musical').prop('checked', false);
	$('#adicional_pantallas').prop('checked', false);
	$('#adicional_pantallas_cantidad').val("0").prop('disabled', true);
	$('#adicional_ventiladores').prop('checked', false);
	$('#adicional_ventiladores_cantidad').val("0").prop('disabled', true);
	$('#adicional_extintores').prop('checked', false);
	$('#observaciones_generales').val("");
	var ahora = new Date();
	ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
	$('#fecha_velatorio').val(ahora.toISOString().slice(0, 16));
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
			url: '../ajax/checklist_velatorio.php?op=listar',
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
	const nombreContacto = $('#nombre_contacto').val();
	const telefonoContacto = $('#telefono_contacto').val();
	const lugarVelatorio = $('#lugar_velatorio').val();
	const fechaVelatorio = $('#fecha_velatorio').val();
	const ataudTipo = $('#ataud_tipo').val();
	const ataudMedidas = $('#ataud_medidas').val();
	const ataudObservaciones = $('#ataud_observaciones').val();
	const decoracionFlores = $('#decoracion_flores').prop('checked') ? 'Si' : 'No';
	const decoracionFloresDetalle = $('#decoracion_flores_detalle').val();
	const decoracionCortinas = $('#decoracion_cortinas').prop('checked') ? 'Si' : 'No';
	const decoracionCortinasColor = $('#decoracion_cortinas_color').val();
	const decoracionAlfombra = $('#decoracion_alfombra').prop('checked') ? 'Si' : 'No';
	const decoracionAlfombraColor = $('#decoracion_alfombra_color').val();
	const decoracionIluminacion = $('#decoracion_iluminacion').prop('checked') ? 'Si' : 'No';
	const decoracionIluminacionTipo = $('#decoracion_iluminacion_tipo').val();
	const mobiliarioCatafalco = $('#mobiliario_catafalco').prop('checked') ? 'Si' : 'No';
	const mobiliarioCatafalcoTipo = $('#mobiliario_catafalco_tipo').val();
	const mobiliarioSillas = $('#mobiliario_sillas').val();
	const mobiliarioMesas = $('#mobiliario_mesas').val();
	const mobiliarioBancos = $('#mobiliario_bancos').val();
	const mobiliarioLibro = $('#mobiliario_libro').prop('checked') ? 'Si' : 'No';
	const adicionalCafeteria = $('#adicional_cafeteria').prop('checked') ? 'Si' : 'No';
	const adicionalReproduccionMusical = $('#adicional_reproduccion_musical').prop('checked') ? 'Si' : 'No';
	const adicionalPantallas = $('#adicional_pantallas').prop('checked') ? 'Si' : 'No';
	const adicionalPantallasCantidad = $('#adicional_pantallas_cantidad').val();
	const adicionalVentiladores = $('#adicional_ventiladores').prop('checked') ? 'Si' : 'No';
	const adicionalVentiladoresCantidad = $('#adicional_ventiladores_cantidad').val();
	const adicionalExtintores = $('#adicional_extintores').prop('checked') ? 'Si' : 'No';
	const observacionesGenerales = $('#observaciones_generales').val();

	const fechaActual = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
	const horaActual = new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });

	const docDef = {
		pageSize: 'LETTER',
		pageMargins: [60, 100, 60, 80],
		header: function(currentPage, pageCount) {
			return [[{
				columns: [{
					text: ['FUNERARIA GENESIS\n', {text: 'CHECKLIST DE MONTAJE DE VELATORIO', fontSize: 14, bold: true, color: '#333333'}, {text: 'Tel: (591) 2-1234567 | Email: info@funerariagenesis.com', fontSize: 8, color: '#666666'}],
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
			{text: 'CHECKLIST DE VERIFICACIÓN DE MONTAJE DE VELATORIO', style: 'title', alignment: 'center', margin: [0, 0, 0, 20]},
			{text: 'I. INFORMACIÓN GENERAL', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{columns: [
				{text: [{text: 'Difunto: ', bold: true}, nombreDifunto || 'N/A'], width: '*'},
				{text: [{text: 'Fecha/Hora: ', bold: true}, fechaVelatorio ? new Date(fechaVelatorio).toLocaleString('es-BO') : 'N/A'], width: 'auto'}
			], margin: [0, 0, 0, 5]},
			{text: [{text: 'Contacto: ', bold: true}, nombreContacto || 'N/A'], margin: [0, 0, 0, 5]},
			{columns: [
				{text: [{text: 'Teléfono: ', bold: true}, telefonoContacto || 'N/A'], width: '*'},
				{text: [{text: 'Lugar: ', bold: true}, lugarVelatorio || 'N/A'], width: '*'}
			], margin: [0, 0, 0, 20]},
			
			{text: 'II. ATAÚD', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{columns: [{text: [{text: 'Tipo: ', bold: true}, ataudTipo || 'N/A'], width: '*'}, {text: [{text: 'Medidas: ', bold: true}, ataudMedidas || 'N/A'], width: '*'}], margin: [0, 0, 0, 5]},
			{text: [{text: 'Observaciones: ', bold: true}, ataudObservaciones || 'Ninguna'], margin: [0, 0, 0, 20]},
			
			{text: 'III. DECORACIÓN', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{text: decoracionFlores === 'Si' ? '✓ Arreglos florales: Si' : '□ Arreglos florales: No' + (decoracionFloresDetalle ? ' - ' + decoracionFloresDetalle : ''), margin: [0, 0, 0, 3]},
			{text: decoracionCortinas === 'Si' ? '✓ Cortinas: Si - ' + (decoracionCortinasColor || 'N/A') : '□ Cortinas: No', margin: [0, 0, 0, 3]},
			{text: decoracionAlfombra === 'Si' ? '✓ Alfombra: Si - ' + (decoracionAlfombraColor || 'N/A') : '□ Alfombra: No', margin: [0, 0, 0, 3]},
		{text: decoracionIluminacion === 'Si' ? '✓ Iluminación: Si' + (decoracionIluminacionTipo ? ' - ' + decoracionIluminacionTipo : '') : '□ Iluminación: No', margin: [0, 0, 0, 20]},
		
		{text: 'IV. MOBILIARIO', style: 'sectionHeader', margin: [0, 15, 0, 10]},
		{text: mobiliarioCatafalco === 'Si' ? '✓ Catafalco: Si - ' + (mobiliarioCatafalcoTipo || 'N/A') : '□ Catafalco: No', margin: [0, 0, 0, 3]},
		{text: 'Sillas: ' + mobiliarioSillas + ' | Mesas: ' + mobiliarioMesas + ' | Bancos: ' + mobiliarioBancos, margin: [0, 0, 0, 3]},
		{text: mobiliarioLibro === 'Si' ? '✓ Libro de condolencias: Si' : '□ Libro de condolencias: No', margin: [0, 0, 0, 20]},
		
		{text: 'V. ELEMENTOS ADICIONALES', style: 'sectionHeader', margin: [0, 15, 0, 10]},
		{text: adicionalCafeteria === 'Si' ? '✓ Cafetería: Si' : '□ Cafetería: No', margin: [0, 0, 0, 3]},
		{text: adicionalReproduccionMusical === 'Si' ? '✓ Reproducción musical: Si' : '□ Reproducción musical: No', margin: [0, 0, 0, 3]},
		{text: adicionalPantallas === 'Si' ? '✓ Pantallas: Si - Cantidad: ' + adicionalPantallasCantidad : '□ Pantallas: No', margin: [0, 0, 0, 3]},
		{text: adicionalVentiladores === 'Si' ? '✓ Ventiladores: Si - Cantidad: ' + adicionalVentiladoresCantidad : '□ Ventiladores: No', margin: [0, 0, 0, 3]},
		{text: adicionalExtintores === 'Si' ? '✓ Extintores: Si' : '□ Extintores: No', margin: [0, 0, 0, 20]},
			
			{text: 'VI. OBSERVACIONES', style: 'sectionHeader', margin: [0, 15, 0, 10]},
			{text: observacionesGenerales || 'Ninguna', margin: [0, 0, 0, 30]},
			
			{columns: [
				{text: ['_________________________', nombreContacto || 'FAMILIA', 'Representante de la Familia'], alignment: 'center', margin: [0, 20, 0, 0]},
				{text: ['_________________________', 'FUNERARIA GENESIS', 'Personal de Montaje'], alignment: 'center', margin: [0, 20, 0, 0]}
			]}
		],
		styles: {title: {fontSize: 16, bold: true, color: '#333333'}, sectionHeader: {fontSize: 12, bold: true, color: '#333333'}},
		defaultStyle: {fontSize: 10, color: '#333333'}
	};

	const pdfDoc = pdfMake.createPdf(docDef);
	const arrayBuffer = await new Promise((resolve) => pdfDoc.getBuffer((buffer)=> resolve(buffer.buffer)));
	const hashBuf = await crypto.subtle.digest('SHA-256', arrayBuffer);
	const hashHex = Array.from(new Uint8Array(hashBuf)).map(b=>b.toString(16).padStart(2,'0')).join('');

	const blob = new Blob([arrayBuffer], {type: 'application/pdf'});
	const formData = new FormData();
	formData.append('pdf', blob, 'checklist.pdf');
	formData.append('nombre_difunto', nombreDifunto);
	formData.append('nombre_contacto', nombreContacto);
	formData.append('telefono_contacto', telefonoContacto);
	formData.append('lugar_velatorio', lugarVelatorio);
	formData.append('fecha_velatorio', fechaVelatorio);
	formData.append('ataud_tipo', ataudTipo);
	formData.append('ataud_medidas', ataudMedidas);
	formData.append('ataud_observaciones', ataudObservaciones);
	formData.append('decoracion_flores', decoracionFlores);
	formData.append('decoracion_flores_detalle', decoracionFloresDetalle);
	formData.append('decoracion_cortinas', decoracionCortinas);
	formData.append('decoracion_cortinas_color', decoracionCortinasColor);
	formData.append('decoracion_alfombra', decoracionAlfombra);
	formData.append('decoracion_alfombra_color', decoracionAlfombraColor);
	formData.append('decoracion_iluminacion', decoracionIluminacion);
	formData.append('decoracion_iluminacion_tipo', decoracionIluminacionTipo);
	formData.append('mobiliario_catafalco', mobiliarioCatafalco);
	formData.append('mobiliario_catafalco_tipo', mobiliarioCatafalcoTipo);
	formData.append('mobiliario_sillas', mobiliarioSillas);
	formData.append('mobiliario_mesas', mobiliarioMesas);
	formData.append('mobiliario_bancos', mobiliarioBancos);
	formData.append('mobiliario_libro', mobiliarioLibro);
	formData.append('adicional_cafeteria', adicionalCafeteria);
	formData.append('adicional_reproduccion_musical', adicionalReproduccionMusical);
	formData.append('adicional_pantallas', adicionalPantallas);
	formData.append('adicional_pantallas_cantidad', adicionalPantallasCantidad);
	formData.append('adicional_ventiladores', adicionalVentiladores);
	formData.append('adicional_ventiladores_cantidad', adicionalVentiladoresCantidad);
	formData.append('adicional_extintores', adicionalExtintores);
	formData.append('observaciones_generales', observacionesGenerales);
	formData.append('idcontrato', $('#idcontrato').val());
	formData.append('idcotizacion', $('#idcotizacion').val());
	formData.append('hash_sha256', hashHex);

	$.ajax({
		url: '../ajax/checklist_velatorio.php?op=guardar',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function(resp){
			try { var r = JSON.parse(resp); } catch(e){ 
				console.error('Error al parsear respuesta:', resp);
				bootbox.alert('Error: ' + resp);
				return;
			}
			if (r.ok) {
				bootbox.alert('Checklist guardada. Código: '+r.codigo);
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
			bootbox.alert('Error al guardar la checklist. Por favor, verifique los datos e intente nuevamente.');
			$('#btnGuardar').prop('disabled', false);
		}
	});
}

function ver(id) {
	$.post('../ajax/checklist_velatorio.php?op=mostrar', {idchecklist:id}, function(data){
		var d = JSON.parse(data);
		if (!d) return;
		var link = '../'+d.ruta_pdf;
		var confirmadoHtml = '<span class="label label-success">Confirmado</span>';
		if (d.confirmado_familia == 0) {
			confirmadoHtml = '<span class="label label-warning">Pendiente</span> <button class="btn btn-xs btn-info" onclick="confirmarFamilia('+id+')"><i class="fa fa-check"></i> Confirmar</button>';
		}
		var message = '<p><b>Código:</b> '+d.codigo+'</p>'+
			'<p><b>Difunto:</b> '+d.nombre_difunto+'</p>'+
			'<p><b>Contacto:</b> '+d.nombre_contacto+'</p>'+
			'<p><b>Fecha Velatorio:</b> '+d.fecha_velatorio+'</p>'+
			'<p><b>Confirmación Familia:</b> '+confirmadoHtml+'</p>'+
			'<hr>'+
			'<p><a target="_blank" class="btn btn-primary" href="'+link+'"><i class="fa fa-download"></i> Descargar PDF</a></p>';
		bootbox.dialog({title: 'Checklist '+d.codigo, message: message, size: 'large', buttons: {close: {label: 'Cerrar', className:'btn-default'}}});
	});
}

function confirmarFamilia(id) {
	bootbox.confirm('¿Confirma que la familia ha verificado y está conforme con el montaje?', function(result){
		if (result) {
			$.post('../ajax/checklist_velatorio.php?op=confirmarFamilia', {idchecklist:id}, function(data){
				var r = JSON.parse(data);
				bootbox.alert(r.msg, function(){ver(id);});
			});
		}
	});
}

init();

