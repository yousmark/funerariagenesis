var tabla;

function init() {
	mostrarform(false);
	listar();

	$('#formulario').on('submit', function(e){
		e.preventDefault();
		generarYEnviarPDF();
	});
	
	// Inicializar precios en los checkboxes al cargar la página
	$('.servicio-check').each(function() {
		actualizarValorCheckbox(this);
	});
	
	// Actualizar el campo detalle cuando cambien los checkboxes
	$(document).on('change', '.servicio-check', function(){
		actualizarDetalle();
	});
	
	// Habilitar/deshabilitar campo de valor de descuento según el tipo
	$(document).on('change', '#tipo_descuento', function(){
		var tipo = $(this).val();
		if (tipo) {
			$('#valor_descuento').prop('disabled', false);
			$('#valor_descuento').val('');
		} else {
			$('#valor_descuento').prop('disabled', true);
			$('#valor_descuento').val('');
			calcularTotalConDescuento();
		}
	});
	
	// Calcular total cuando cambie el valor del descuento
	$(document).on('input blur', '#valor_descuento', function(){
		calcularTotalConDescuento();
	});
	
	// Configurar límites de fecha de fallecimiento (máximo 4 días en el pasado, no puede ser futura)
	var hoy = new Date();
	var fechaMax = hoy.toISOString().split('T')[0]; // Fecha de hoy (máximo permitido)
	var fechaMin = new Date();
	fechaMin.setDate(fechaMin.getDate() - 4); // 4 días en el pasado (mínimo permitido)
	var fechaMinStr = fechaMin.toISOString().split('T')[0];
	
	$('#fecha_fallecimiento').attr('max', fechaMax);
	$('#fecha_fallecimiento').attr('min', fechaMinStr);
	
	// Validar edad en tiempo real (máximo 122 años)
	$(document).on('input blur', '#edad', function(){
		var edad = parseInt($(this).val()) || 0;
		if (edad > 122) {
			$(this).val(122);
			mostrarError('La edad no puede superar los 122 años', 'Validación de Edad');
		} else if (edad < 0) {
			$(this).val(0);
		}
	});
	
	// Validar fecha de fallecimiento en tiempo real (máximo 4 días en el pasado, no puede ser futura)
	$(document).on('change blur', '#fecha_fallecimiento', function(){
		var fechaSeleccionada = $(this).val();
		if (!fechaSeleccionada) {
			return; // Si está vacío, no validar (campo opcional)
		}
		
		var fecha = new Date(fechaSeleccionada);
		var hoy = new Date();
		hoy.setHours(0, 0, 0, 0);
		fecha.setHours(0, 0, 0, 0);
		
		// Calcular diferencia en días
		var diferenciaTiempo = hoy.getTime() - fecha.getTime();
		var diferenciaDias = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));
		
		// No permitir fechas futuras
		if (fecha > hoy) {
			$(this).val('');
			mostrarError('La fecha de fallecimiento no puede ser una fecha futura', 'Validación de Fecha');
			return;
		}
		
		// No permitir más de 4 días en el pasado
		if (diferenciaDias > 4) {
			$(this).val('');
			mostrarError('La fecha de fallecimiento no puede ser más de 4 días en el pasado', 'Validación de Fecha');
			return;
		}
	});
}

function limpiar() {
	$('#nombres_fallecido').val("");
	$('#apellidos_fallecido').val("");
	$('#fecha_fallecimiento').val("");
	$('#edad').val("");
	$('#nombres_familia').val("");
	$('#apellidos_familia').val("");
	$('#direccion_familia').val("");
	$('#contacto_email').val("");
	$('#contacto_celular').val("");
	$('#cedula').val("");
	$('#complemento').val("");
	$('#detalle').val("");
	$('#subtotal').val("");
	$('#tipo_descuento').val("");
	$('#valor_descuento').val("").prop('disabled', true);
	$('#descuento_bs').val("");
	$('#total').val("");
	// Desmarcar todos los checkboxes
	$('.servicio-check').prop('checked', false);
	// Actualizar el total
	actualizarDetalle();
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
	limpiar();
	mostrarform(false);
}

// Definir precios de servicios
var preciosServicios = {
	// Paquete Básico
	'Ataúd estándar': 1500,
	'Traslado local': 300,
	'Preparación e higiene del cuerpo': 400,
	'Sala velatoria 6 hrs': 600,
	'Trámites básicos': 200,
	// Paquete Intermedio
	'Ataúd de madera barnizada': 2500,
	'Arreglos florales': 500,
	'Cafetería': 400,
	'Libro de condolencias': 100,
	'Transporte al cementerio': 500,
	'Sala velatoria 12 hrs': 1000,
	// Paquete Premium
	'Ataúd de lujo': 4500,
	'Arreglos grandes': 1200,
	'Traslado nacional': 1500,
	'Servicio musical': 600,
	'Cremación o sepultura': 800
};

function actualizarDetalle() {
	var serviciosSeleccionados = [];
	var subtotal = 0;
	
	// Capturar todos los checkboxes marcados y calcular subtotal
	$('.servicio-check:checked').each(function(){
		var valor = $(this).val();
		if (valor && valor.trim().length > 0) {
			// Extraer nombre del servicio y precio
			var partes = valor.split(':');
			var nombreServicio = partes[0].trim();
			var precio = parseFloat(partes[1]) || 0;
			
			serviciosSeleccionados.push(valor);
			subtotal += precio;
		}
	});
	
	var detalleTexto = serviciosSeleccionados.join('\n');
	$('#detalle').val(detalleTexto);
	
	// Actualizar el subtotal
	$('#subtotal').val(subtotal.toFixed(2));
	
	// Calcular total con descuento
	calcularTotalConDescuento();
	
	return detalleTexto;
}

// Función para calcular el total aplicando el descuento
function calcularTotalConDescuento() {
	var subtotal = parseFloat($('#subtotal').val()) || 0;
	var tipoDescuento = $('#tipo_descuento').val();
	var valorDescuento = parseFloat($('#valor_descuento').val()) || 0;
	var descuentoBs = 0;
	var total = subtotal;
	
	if (tipoDescuento && valorDescuento > 0) {
		if (tipoDescuento === 'porcentaje') {
			// Descuento por porcentaje
			if (valorDescuento > 100) {
				valorDescuento = 100; // Máximo 100%
				$('#valor_descuento').val(100);
				mostrarError('El descuento no puede ser mayor al 100%', 'Validación de Descuento');
			}
			descuentoBs = (subtotal * valorDescuento) / 100;
		} else if (tipoDescuento === 'monto') {
			// Descuento por monto fijo
			if (valorDescuento > subtotal) {
				valorDescuento = subtotal; // Máximo el subtotal
				$('#valor_descuento').val(subtotal.toFixed(2));
				mostrarError('El descuento no puede ser mayor al subtotal', 'Validación de Descuento');
			}
			descuentoBs = valorDescuento;
		}
	}
	
	total = subtotal - descuentoBs;
	
	// Asegurar que el total no sea negativo
	if (total < 0) {
		total = 0;
	}
	
	// Actualizar campos
	$('#descuento_bs').val(descuentoBs.toFixed(2));
	$('#total').val(total.toFixed(2));
}

// Función para actualizar el valor del checkbox con el precio
function actualizarValorCheckbox(checkbox) {
	var $checkbox = $(checkbox);
	var $label = $checkbox.closest('label');
	
	// Obtener el texto del label sin el checkbox y sin el precio anterior
	// Clonar el label, remover el checkbox y el precio, y obtener el texto
	var $tempLabel = $label.clone();
	$tempLabel.find('input').remove();
	$tempLabel.find('.precio-servicio').remove();
	var labelTextOriginal = $tempLabel.text().trim();
	
	// Buscar el nombre del servicio en el objeto de precios
	var nombreServicio = Object.keys(preciosServicios).find(function(nombre) {
		return labelTextOriginal.includes(nombre);
	});
	
	if (nombreServicio && preciosServicios[nombreServicio]) {
		var precio = preciosServicios[nombreServicio];
		// Actualizar el valor del checkbox
		$checkbox.val(nombreServicio + ': ' + precio);
		
		// Remover el precio anterior si existe
		$label.find('.precio-servicio').remove();
		
		// Agregar el precio al label
		$label.append(' <span class="precio-servicio text-success" style="font-weight: bold; margin-left: 5px;">- Bs. ' + precio.toFixed(2) + '</span>');
	}
}

function seleccionarPaquete(tipo) {
	switch(tipo) {
		case 'basico':
			// Desmarcar todos primero
			$('.servicio-check').prop('checked', false);
			// Marcar solo los del paquete básico
			$('.servicio-check.basico').prop('checked', true);
			break;
		case 'intermedio':
			// Desmarcar todos primero
			$('.servicio-check').prop('checked', false);
			// Marcar solo los del paquete intermedio
			$('.servicio-check.intermedio').prop('checked', true);
			break;
		case 'premium':
			// Desmarcar todos primero
			$('.servicio-check').prop('checked', false);
			// Marcar solo los del paquete premium
			$('.servicio-check.premium').prop('checked', true);
			break;
		case 'todos':
			$('.servicio-check').prop('checked', true);
			break;
		case 'ninguno':
			$('.servicio-check').prop('checked', false);
			break;
	}
	actualizarDetalle();
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/cotizacion.php?op=listar',
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
	// Asegurar que el detalle esté actualizado antes de generar el PDF
	actualizarDetalle();
	
	// Obtener nombres y apellidos del fallecido
	const nombresFallecido = $('#nombres_fallecido').val().trim();
	const apellidosFallecido = $('#apellidos_fallecido').val().trim();
	const nombreFallecido = (nombresFallecido + ' ' + apellidosFallecido).trim();
	
	const fechaFallecimiento = $('#fecha_fallecimiento').val();
	const edad = $('#edad').val();
	
	// Obtener nombres y apellidos de la familia
	const nombresFamilia = $('#nombres_familia').val().trim();
	const apellidosFamilia = $('#apellidos_familia').val().trim();
	const nombreFamilia = (nombresFamilia + ' ' + apellidosFamilia).trim(); // Para compatibilidad y mostrar en PDF
	
	const direccionFamilia = $('#direccion_familia').val();
	const contactoEmail = $('#contacto_email').val();
	const contactoCelular = $('#contacto_celular').val();
	const cedula = $('#cedula').val();
	const complemento = $('#complemento').val().trim().toUpperCase();
	const detalleTexto = $('#detalle').val();
	const total = $('#total').val();

	// Debug: mostrar en consola
	console.log('Detalle texto capturado:', detalleTexto);
	console.log('Checkboxes marcados:', $('.servicio-check:checked').length);

	// Validar campos requeridos
	if (!nombresFallecido || nombresFallecido.trim() === '') {
		mostrarError('Los nombres del fallecido son requeridos', 'Error de Validación');
		$('#nombres_fallecido').focus();
		return;
	}
	
	if (!apellidosFallecido || apellidosFallecido.trim() === '') {
		mostrarError('Los apellidos del fallecido son requeridos', 'Error de Validación');
		$('#apellidos_fallecido').focus();
		return;
	}
	
	if (!nombresFamilia || nombresFamilia.trim() === '') {
		mostrarError('Los nombres de la familia son requeridos', 'Error de Validación');
		$('#nombres_familia').focus();
		return;
	}
	
	if (!apellidosFamilia || apellidosFamilia.trim() === '') {
		mostrarError('Los apellidos de la familia son requeridos', 'Error de Validación');
		$('#apellidos_familia').focus();
		return;
	}
	
	// Validar email si se proporciona
	if (contactoEmail && contactoEmail.trim() !== '') {
		var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailRegex.test(contactoEmail)) {
			mostrarError('El email no tiene un formato válido (ejemplo: usuario@dominio.com)', 'Error de Validación');
			$('#contacto_email').focus();
			return;
		}
	}
	
	// Validar teléfono si se proporciona
	if (contactoCelular && contactoCelular.trim() !== '') {
		var telefonoRegex = /^[0-9]{8}$/;
		if (!telefonoRegex.test(contactoCelular)) {
			mostrarError('El celular debe contener exactamente 8 dígitos (ejemplo: 71234567)', 'Error de Validación');
			$('#contacto_celular').focus();
			return;
		}
	}
	
	// Validar edad si se proporciona
	if (edad && edad.trim() !== '') {
		var edadNum = parseInt(edad);
		if (isNaN(edadNum) || edadNum < 0 || edadNum > 122) {
			mostrarError('La edad debe estar entre 0 y 122 años', 'Error de Validación');
			$('#edad').focus();
			return;
		}
	}
	
	// Validar fecha de fallecimiento si se proporciona
	if (fechaFallecimiento && fechaFallecimiento.trim() !== '') {
		var fecha = new Date(fechaFallecimiento);
		var hoy = new Date();
		hoy.setHours(0, 0, 0, 0);
		fecha.setHours(0, 0, 0, 0);
		
		// No permitir fechas futuras
		if (fecha > hoy) {
			mostrarError('La fecha de fallecimiento no puede ser una fecha futura', 'Error de Validación');
			$('#fecha_fallecimiento').focus();
			return;
		}
		
		// Calcular diferencia en días
		var diferenciaTiempo = hoy.getTime() - fecha.getTime();
		var diferenciaDias = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));
		
		// No permitir más de 4 días en el pasado
		if (diferenciaDias > 4) {
			mostrarError('La fecha de fallecimiento no puede ser más de 4 días en el pasado', 'Error de Validación');
			$('#fecha_fallecimiento').focus();
			return;
		}
	}
	
	// Validar total
	if (!total || parseFloat(total) <= 0) {
		mostrarError('El total debe ser mayor a cero', 'Error de Validación');
		$('#total').focus();
		return;
	}
	
	// Validar que hay servicios seleccionados
	if (!detalleTexto || detalleTexto.trim().length === 0) {
		mostrarError('Por favor, seleccione al menos un servicio antes de generar la cotización', 'Error de Validación');
		return;
	}
	
	// Mostrar indicador de procesamiento
	var procesando = mostrarProcesando('Generando PDF y guardando cotización...');

	// Parsear detalle a JSON simple
	const items = detalleTexto.split('\n').filter(x=>x.trim().length>0).map(line=>{
		const parts = line.split(':');
		const concepto = (parts[0]||'').trim();
		const montoStr = (parts[1]||'0').trim();
		const monto = parseFloat(montoStr.replace(/[^0-9.,-]/g,'').replace(',', '.'))||0;
		return { concepto: concepto, monto: monto };
	});
	
	// Debug: mostrar items parseados
	console.log('Items parseados:', items.length, items);
	
	// Validar que se parsearon items
	if (items.length === 0) {
		ocultarProcesando(procesando);
		mostrarError('Error: No se pudieron procesar los servicios seleccionados. Por favor, seleccione al menos un servicio.', 'Error de Procesamiento');
		return;
	}

	// Fecha actual
	const fechaActual = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });

	// Construir tabla de servicios
	const body = [
		[
			{text: '#', style: 'tableHeader', alignment: 'center', width: 30},
			{text: 'DESCRIPCIÓN DEL SERVICIO', style: 'tableHeader'},
			{text: 'MONTO (Bs.)', style: 'tableHeader', alignment: 'right', width: 100}
		]
	];
	let numItem = 1;
	items.forEach(it => {
		body.push([
			{text: numItem.toString(), alignment: 'center'},
			{text: it.concepto},
			{text: it.monto.toFixed(2), alignment: 'right'}
		]);
		numItem++;
	});
	body.push([
		{text: '', colSpan: 2, border: [false, false, false, true]},
		{},
		{text: '', border: [false, false, false, true]}
	]);
	
	// Calcular subtotal y descuento para el PDF
	const subtotalPDF = items.reduce((sum, item) => sum + (item.monto || 0), 0);
	const tipoDescuentoPDF = $('#tipo_descuento').val() || '';
	const valorDescuentoPDF = parseFloat($('#valor_descuento').val()) || 0;
	let descuentoBsPDF = 0;
	
	if (tipoDescuentoPDF && valorDescuentoPDF > 0) {
		if (tipoDescuentoPDF === 'porcentaje') {
			descuentoBsPDF = (subtotalPDF * valorDescuentoPDF) / 100;
		} else if (tipoDescuentoPDF === 'monto') {
			descuentoBsPDF = valorDescuentoPDF;
		}
	}
	
	const totalPDF = subtotalPDF - descuentoBsPDF;
	
	// Mostrar subtotal
	body.push([
		{text: 'SUBTOTAL:', bold: true, fontSize: 11, colSpan: 2, alignment: 'right'},
		{},
		{text: 'Bs. ' + subtotalPDF.toFixed(2), fontSize: 11, alignment: 'right'}
	]);
	
	// Mostrar descuento si existe
	if (descuentoBsPDF > 0) {
		let descuentoTexto = 'DESCUENTO';
		if (tipoDescuentoPDF === 'porcentaje') {
			descuentoTexto += ' (' + valorDescuentoPDF.toFixed(2) + '%)';
		}
		body.push([
			{text: descuentoTexto + ':', bold: true, fontSize: 11, colSpan: 2, alignment: 'right', color: '#856404'},
			{},
			{text: '- Bs. ' + descuentoBsPDF.toFixed(2), fontSize: 11, alignment: 'right', color: '#856404'}
		]);
	}
	
	// Mostrar total
	body.push([
		{text: 'TOTAL:', bold: true, fontSize: 12, colSpan: 2, alignment: 'right', margin: [0, 5, 0, 0]},
		{},
		{text: 'Bs. ' + totalPDF.toFixed(2), bold: true, fontSize: 12, alignment: 'right', margin: [0, 5, 0, 0]}
	]);

	const docDef = {
		pageSize: 'LETTER',
		pageMargins: [60, 100, 60, 80],
		header: function(currentPage, pageCount) {
			return [
				{
					columns: [
						{
							text: [
								{text: 'FUNERARIA GENESIS\n', fontSize: 18, bold: true, color: '#333333'},
								{text: 'Servicios Funerarios Integrales\n', fontSize: 10, color: '#666666'},
								{text: 'Tel: (591) 2-1234567 | Email: info@funerariagenesis.com\n', fontSize: 8, color: '#666666'},
								{text: 'Dirección: Av. Principal, La Paz - Bolivia', fontSize: 8, color: '#666666'}
							],
							width: '*'
						},
						{
							text: [
								{text: 'COTIZACIÓN', fontSize: 16, bold: true, alignment: 'right', color: '#333333'},
								{text: fechaActual, fontSize: 10, alignment: 'right', color: '#666666', margin: [0, 5, 0, 0]}
							],
							width: 'auto'
						}
					],
					margin: [0, 0, 0, 20],
					border: [false, false, false, true],
					borderColor: '#cccccc'
				}
			];
		},
		footer: function(currentPage, pageCount) {
			return {
				text: [
					{text: 'Página ', fontSize: 8},
					{text: currentPage.toString(), fontSize: 8},
					{text: ' de ', fontSize: 8},
					{text: pageCount.toString(), fontSize: 8}
				],
				alignment: 'center',
				fontSize: 8,
				margin: [0, 10, 0, 0],
				color: '#666666'
			};
		},
		content: [
			// Información del fallecido
			{
				text: 'DATOS DEL FALLECIDO',
				style: 'sectionHeader',
				margin: [0, 0, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{text: 'Nombre completo: ', bold: true},
							{text: nombreFallecido || 'N/A'}
						]
					},
					{
						width: 'auto',
						text: [
							{text: 'Fecha de fallecimiento: ', bold: true},
							{text: fechaFallecimiento ? new Date(fechaFallecimiento).toLocaleDateString('es-BO') : 'N/A'}
						]
					},
					{
						width: 'auto',
						text: [
							{text: 'Edad: ', bold: true},
							{text: edad || 'N/A'}
						]
					}
				],
				margin: [0, 0, 0, 15]
			},

			// Información de la familia
			{
				text: 'DATOS DE LA FAMILIA',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{text: 'Representante: ', bold: true},
							{text: nombreFamilia || 'N/A'}
						]
					},
					{
						width: 'auto',
						text: [
							{text: 'Cédula: ', bold: true},
							{text: (cedula || 'N/A') + (complemento ? ' ' + complemento : '')}
						]
					}
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{text: 'Dirección: ', bold: true},
					{text: direccionFamilia || 'N/A'}
				],
				margin: [0, 0, 0, 5]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{text: 'Teléfono: ', bold: true},
							{text: contactoCelular || 'N/A'}
						]
					},
					{
						width: '*',
						text: [
							{text: 'Email: ', bold: true},
							{text: contactoEmail || 'N/A'}
						]
					}
				],
				margin: [0, 0, 0, 20]
			},

			// Servicios
			{
				text: 'DETALLE DE SERVICIOS',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				table: {
					headerRows: 1,
					widths: [30, '*', 100],
					body: body
				},
				layout: {
					hLineWidth: function (i, node) {
						return (i === 0 || i === node.table.body.length) ? 1 : 0.5;
					},
					vLineWidth: function (i, node) {
						return 0.5;
					},
					hLineColor: function (i, node) {
						return '#cccccc';
					},
					vLineColor: function (i, node) {
						return '#cccccc';
					},
					fillColor: function (i, node) {
						return (i === 0) ? '#f5f5f5' : null;
					}
				},
				margin: [0, 0, 0, 25]
			},

			// Notas al pie
			{
				text: [
					{text: 'NOTA: ', bold: true, fontSize: 9},
					{text: 'Esta cotización tiene una validez de 15 días calendario a partir de la fecha de emisión. Los precios están sujetos a cambios sin previo aviso.', fontSize: 9, italics: true}
				],
				margin: [0, 20, 0, 0],
				color: '#666666'
			}
		],
		styles: {
			header: {
				fontSize: 18,
				bold: true,
				margin: [0, 0, 0, 10]
			},
			sectionHeader: {
				fontSize: 12,
				bold: true,
				color: '#333333',
				decoration: 'underline'
			},
			tableHeader: {
				bold: true,
				fontSize: 10,
				color: '#333333',
				fillColor: '#f5f5f5'
			}
		},
		defaultStyle: {
			fontSize: 10,
			color: '#333333'
		}
	};

	const pdfDoc = pdfMake.createPdf(docDef);
	const arrayBuffer = await new Promise((resolve) => pdfDoc.getBuffer((buffer)=> resolve(buffer.buffer)));
	const hashBuf = await crypto.subtle.digest('SHA-256', arrayBuffer);
	const hashHex = Array.from(new Uint8Array(hashBuf)).map(b=>b.toString(16).padStart(2,'0')).join('');

	const blob = new Blob([arrayBuffer], {type: 'application/pdf'});
	const formData = new FormData();
	formData.append('pdf', blob, 'cotizacion.pdf');
	// Enviar nombres y apellidos separados
	formData.append('nombres_fallecido', nombresFallecido);
	formData.append('apellidos_fallecido', apellidosFallecido);
	formData.append('nombres_familia', nombresFamilia);
	formData.append('apellidos_familia', apellidosFamilia);
	// También enviar nombre completo para compatibilidad
	formData.append('nombre_fallecido', nombreFallecido);
	formData.append('nombre_familia', nombreFamilia);
	formData.append('fecha_fallecimiento', fechaFallecimiento);
	formData.append('edad', edad);
	formData.append('direccion_familia', direccionFamilia);
	formData.append('contacto_email', contactoEmail);
	formData.append('contacto_celular', contactoCelular);
	formData.append('cedula', cedula);
	formData.append('complemento', complemento);
	
	// Obtener información de descuento
	const subtotal = parseFloat($('#subtotal').val()) || 0;
	const tipoDescuento = $('#tipo_descuento').val() || '';
	const valorDescuento = parseFloat($('#valor_descuento').val()) || 0;
	const descuentoBs = parseFloat($('#descuento_bs').val()) || 0;
	
	formData.append('detalle_json', JSON.stringify({
		items: items,
		nombres_fallecido: nombresFallecido,
		apellidos_fallecido: apellidosFallecido,
		nombre_fallecido: nombreFallecido,
		nombres_familia: nombresFamilia,
		apellidos_familia: apellidosFamilia,
		nombre_familia: nombreFamilia,
		fecha_fallecimiento: fechaFallecimiento,
		edad: edad,
		direccion_familia: direccionFamilia,
		cedula: cedula,
		complemento: complemento,
		subtotal: subtotal,
		tipo_descuento: tipoDescuento,
		valor_descuento: valorDescuento,
		descuento_bs: descuentoBs,
		total: parseFloat(total)
	}));
	formData.append('subtotal', subtotal);
	formData.append('tipo_descuento', tipoDescuento);
	formData.append('valor_descuento', valorDescuento);
	formData.append('descuento_bs', descuentoBs);
	formData.append('total', total);
	formData.append('hash_sha256', hashHex);
	formData.append('firma_nombre', '');
	formData.append('firma_fecha', new Date().toISOString().slice(0,19).replace('T',' '));

	$.ajax({
		url: '../ajax/cotizacion.php?op=guardar',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function(resp){
			ocultarProcesando(procesando);
			try { 
				var r = JSON.parse(resp); 
				if (r.ok) {
					mostrarExito('Cotización guardada correctamente. Código: ' + r.codigo, 'Éxito');
					setTimeout(function() {
						mostrarform(false);
						tabla.ajax.reload();
					}, 1500);
				} else {
					// Mostrar mensaje de error del servidor si existe
					var mensajeError = r.mensaje || 'Error al guardar la cotización';
					mostrarError(mensajeError, 'Error de Validación');
				}
			} catch(e) { 
				if (resp.indexOf('correctamente') !== -1 || resp.indexOf('Código') !== -1) {
					mostrarExito(resp, 'Éxito');
					setTimeout(function() {
						mostrarform(false);
						tabla.ajax.reload();
					}, 1500);
				} else {
					mostrarError(resp, 'Error');
				}
			}
		},
		error: function(xhr, status, error) {
			ocultarProcesando(procesando);
			mostrarError('Error al comunicarse con el servidor: ' + error, 'Error de Conexión');
		}
	});
}

function ver(id) {
	$.post('../ajax/cotizacion.php?op=mostrar', {idcotizacion:id}, function(data){
		var d = JSON.parse(data);
		if (!d) return;
		var link = '../'+d.ruta_pdf;
		
		// Cargar permisos existentes
		$.post('../ajax/cotizacion.php?op=listarPermisos', {idcotizacion:id}, function(permisosData){
			var permisos = JSON.parse(permisosData);
			var permisosHtml = '<div class="panel panel-info"><div class="panel-heading"><h4 class="panel-title">Usuarios con Permiso</h4></div><div class="panel-body">';
			
			if (permisos.length > 0) {
				permisosHtml += '<table class="table table-bordered table-condensed"><thead><tr><th>Usuario</th><th>Email</th><th>Celular</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
				permisos.forEach(function(permiso){
					var estadoClass = permiso.estado === 'activo' ? 'label-success' : 'label-danger';
					permisosHtml += '<tr><td>'+permiso.cliente_nombre+'</td><td>'+(permiso.cliente_email||'N/A')+'</td><td>'+(permiso.cliente_celular||'N/A')+'</td>';
					permisosHtml += '<td>'+permiso.fecha_otorgado+'</td><td><span class="label '+estadoClass+'">'+permiso.estado+'</span></td>';
					if (permiso.estado === 'activo') {
						permisosHtml += '<td><button class="btn btn-danger btn-xs" onclick="quitarPermiso('+id+','+permiso.idcliente+')"><i class="fa fa-times"></i> Revocar</button></td>';
					} else {
						permisosHtml += '<td>-</td>';
					}
					permisosHtml += '</tr>';
				});
				permisosHtml += '</tbody></table>';
			} else {
				permisosHtml += '<p class="text-muted">No hay usuarios con permiso para ver esta cotización.</p>';
			}
			permisosHtml += '</div></div>';
			
			// Usar nombres y apellidos separados si están disponibles, sino usar nombre completo
			var nombreFamiliaCompleto = (d.nombres_familia && d.apellidos_familia) 
				? (d.nombres_familia + ' ' + d.apellidos_familia).trim() 
				: (d.nombre_familia || '');
			
			var message = '<p><b>Familia:</b> '+nombreFamiliaCompleto+'</p>'+
				'<p><a target="_blank" class="btn btn-primary" href="'+link+'"><i class="fa fa-download"></i> Descargar PDF</a></p>'+
				'<hr>'+permisosHtml;
			
			bootbox.dialog({
				title: 'Cotización '+d.codigo,
				message: message,
				size: 'large',
				buttons: {
					permiso: { 
						label: 'Otorgar Permiso a Usuario', 
						className:'btn-success', 
						callback: function(){ 
							mostrarModalClientes(id); 
						} 
					},
					close: { label: 'Cerrar', className:'btn-default' }
				}
			});
		});
	});
}

function mostrarModalClientes(idcotizacion) {
	// Cargar lista de usuarios registrados
	$.get('../ajax/cotizacion.php?op=listarClientes', function(data){
		var usuarios = JSON.parse(data);
		var usuariosHtml = '<div class="form-group"><label>Seleccione un usuario:</label><select class="form-control" id="selectCliente">';
		
		if (usuarios.length > 0) {
			usuarios.forEach(function(usuario){
				usuariosHtml += '<option value="'+usuario.idcliente+'">'+usuario.nombre+' - '+(usuario.email||'Sin email')+'</option>';
			});
		} else {
			usuariosHtml += '<option value="">No hay usuarios registrados</option>';
		}
		
		usuariosHtml += '</select></div>';
		
		bootbox.dialog({
			title: 'Otorgar Permiso de Visualización',
			message: usuariosHtml,
			buttons: {
				ok: {
					label: 'Otorgar Permiso',
					className: 'btn-success',
					callback: function(){
						var idcliente = $('#selectCliente').val();
						if (!idcliente || idcliente === '') {
							mostrarAdvertencia('Por favor seleccione un usuario de la lista', 'Selección Requerida');
							return false;
						}
						otorgarPermiso(idcotizacion, idcliente);
						return true;
					}
				},
				cancel: {
					label: 'Cancelar',
					className: 'btn-default'
				}
			}
		});
	});
}

function otorgarPermiso(idcotizacion, idcliente) {
	$.post('../ajax/cotizacion.php?op=otorgarPermiso', {idcotizacion:idcotizacion, idcliente:idcliente}, function(data){
		var r = JSON.parse(data);
		if (r.ok) {
			mostrarExito(r.msg, 'Permiso Otorgado');
			setTimeout(function() {
				ver(idcotizacion); // Recargar el modal
			}, 1000);
		} else {
			mostrarError(r.msg, 'Error');
		}
	});
}

function quitarPermiso(idcotizacion, idcliente) {
	bootbox.confirm('¿Está seguro de revocar el permiso de visualización para este usuario?', function(result){
		if (result) {
			$.post('../ajax/cotizacion.php?op=quitarPermiso', {idcotizacion:idcotizacion, idcliente:idcliente}, function(data){
				var r = JSON.parse(data);
				if (r.ok) {
					bootbox.alert(r.msg, function(){
						ver(idcotizacion); // Recargar el modal
					});
				} else {
					bootbox.alert(r.msg);
				}
			});
		}
	});
}

init();


