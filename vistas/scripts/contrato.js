var tabla;

function init() {
	mostrarform(false);
	listar();
	cargarListaCotizaciones();

	$('#formulario').on('submit', function(e){
		e.preventDefault();
		generarYEnviarPDF();
	});
	
	// Actualizar el campo detalle cuando cambien los checkboxes
	$(document).on('change', '.servicio-check', function(){
		actualizarDetalle();
	});
	
	// Validar edad en tiempo real (máximo 122 años)
	$(document).on('input blur', '#edad_fallecido', function(){
		var edad = parseInt($(this).val()) || 0;
		if (edad > 122) {
			$(this).val(122);
			mostrarError('La edad no puede superar los 122 años', 'Validación de Edad');
		} else if (edad < 0) {
			$(this).val(0);
		}
	});
}

function limpiar() {
	$('#select_cotizacion').val("");
	$('#nombres_fallecido').val("");
	$('#apellidos_fallecido').val("");
	$('#fecha_fallecimiento').val("");
	$('#edad_fallecido').val("");
	$('#nombres_contratante').val("");
	$('#apellidos_contratante').val("");
	$('#cedula_contratante').val("");
	$('#complemento_contratante').val("");
	$('#contacto_email').val("");
	$('#contacto_celular').val("");
	$('#direccion_contratante').val("");
	$('#detalle').val("");
	$('#total').val("");
	// Desmarcar todos los checkboxes
	$('.servicio-check').prop('checked', false);
	actualizarDetalle();
}

// Cargar lista de cotizaciones vigentes para el selector
function cargarListaCotizaciones() {
	// Solo cargar si el selector existe (cuando el formulario está visible)
	if ($('#select_cotizacion').length === 0) {
		return;
	}
	
	$.get('../ajax/cotizacion.php?op=listarVigentes', function(data) {
		try {
			var cotizaciones = typeof data === 'string' ? JSON.parse(data) : data;
			var select = $('#select_cotizacion');
			if (select.length === 0) return; // Verificar nuevamente que existe
			
			select.empty();
			select.append('<option value="">-- Seleccione una cotización para cargar los datos --</option>');
			
			if (!cotizaciones || cotizaciones.length === 0) {
				select.append('<option value="" disabled>No hay cotizaciones vigentes disponibles</option>');
				return;
			}
			
			cotizaciones.forEach(function(cot) {
				select.append('<option value="' + cot.idcotizacion + '">' + cot.texto + '</option>');
			});
		} catch(e) {
			console.error('Error al cargar cotizaciones:', e);
			// No mostrar error si el formulario no está visible
			if ($('#formularioregistros').is(':visible')) {
				mostrarError('Error al cargar la lista de cotizaciones', 'Error');
			}
		}
	}).fail(function(xhr, status, error) {
		// No mostrar error si el formulario no está visible
		if ($('#formularioregistros').is(':visible')) {
			mostrarError('Error al comunicarse con el servidor para cargar cotizaciones', 'Error de Conexión');
		}
	});
}

// Cargar datos desde una cotización seleccionada
function cargarDesdeCotizacion() {
	var idcotizacion = $('#select_cotizacion').val();
	if (!idcotizacion || idcotizacion === '') {
		return;
	}
	
	var procesando = mostrarProcesando('Cargando datos de la cotización...');
	
	$.post('../ajax/cotizacion.php?op=mostrar', {idcotizacion: idcotizacion}, function(data) {
		try {
			var cot = typeof data === 'string' ? JSON.parse(data) : data;
			if (!cot || !cot.idcotizacion) {
				ocultarProcesando(procesando);
				mostrarError('No se pudo cargar la información de la cotización', 'Error');
				return;
			}
			
			// Parsear el detalle JSON
			var detalleJson = {};
			try {
				if (cot.detalle_json) {
					detalleJson = typeof cot.detalle_json === 'string' ? JSON.parse(cot.detalle_json) : cot.detalle_json;
				}
			} catch(e) {
				console.error('Error al parsear detalle JSON:', e);
				console.log('detalle_json recibido:', cot.detalle_json);
			}
			
			// Cargar datos del fallecido (desde detalle_json o campos directos si existen)
			// Probar primero con campos separados
			if (detalleJson.nombres_fallecido) {
				$('#nombres_fallecido').val(detalleJson.nombres_fallecido);
			}
			if (detalleJson.apellidos_fallecido) {
				$('#apellidos_fallecido').val(detalleJson.apellidos_fallecido);
			}
			// Si no hay campos separados, intentar dividir el nombre completo
			if (!detalleJson.nombres_fallecido && detalleJson.nombre_fallecido) {
				var partes = detalleJson.nombre_fallecido.trim().split(' ');
				$('#nombres_fallecido').val(partes[0] || '');
				$('#apellidos_fallecido').val(partes.slice(1).join(' ') || '');
			}
			
			if (detalleJson.fecha_fallecimiento) {
				// Convertir fecha si viene en formato diferente
				var fechaFallecimiento = detalleJson.fecha_fallecimiento;
				if (fechaFallecimiento.includes(' ')) {
					fechaFallecimiento = fechaFallecimiento.split(' ')[0];
				}
				$('#fecha_fallecimiento').val(fechaFallecimiento);
			}
			if (detalleJson.edad) {
				$('#edad_fallecido').val(detalleJson.edad);
			}
			
			// Cargar datos del contratante (nombres_familia y apellidos_familia se convierten en nombres_contratante y apellidos_contratante)
			// Probar primero con campos separados
			if (detalleJson.nombres_familia) {
				$('#nombres_contratante').val(detalleJson.nombres_familia);
			}
			if (detalleJson.apellidos_familia) {
				$('#apellidos_contratante').val(detalleJson.apellidos_familia);
			}
			// Si no hay campos separados, intentar desde nombre_familia de la cotización
			if (!detalleJson.nombres_familia && cot.nombre_familia) {
				var partes = cot.nombre_familia.trim().split(' ');
				$('#nombres_contratante').val(partes[0] || '');
				$('#apellidos_contratante').val(partes.slice(1).join(' ') || '');
			}
			// Si tampoco hay nombre_familia, intentar desde nombre_familia en detalle_json
			if (!detalleJson.nombres_familia && !cot.nombre_familia && detalleJson.nombre_familia) {
				var partes = detalleJson.nombre_familia.trim().split(' ');
				$('#nombres_contratante').val(partes[0] || '');
				$('#apellidos_contratante').val(partes.slice(1).join(' ') || '');
			}
			
			// Cargar cédula y complemento si existen en detalle_json
			if (detalleJson.cedula) {
				$('#cedula_contratante').val(detalleJson.cedula);
			}
			if (detalleJson.complemento) {
				$('#complemento_contratante').val(detalleJson.complemento);
			}
			
			// Cargar datos de contacto
			if (cot.contacto_email) {
				$('#contacto_email').val(cot.contacto_email);
			}
			if (cot.contacto_celular) {
				$('#contacto_celular').val(cot.contacto_celular);
			}
			if (detalleJson.direccion_familia) {
				$('#direccion_contratante').val(detalleJson.direccion_familia);
			}
			
			// Cargar total
			if (cot.total) {
				$('#total').val(cot.total);
			}
			
			// Cargar servicios desde el detalle_json
			if (detalleJson.items && Array.isArray(detalleJson.items) && detalleJson.items.length > 0) {
				// Desmarcar todos primero
				$('.servicio-check').prop('checked', false);
				
				// Recorrer los items y marcar los servicios correspondientes
				detalleJson.items.forEach(function(item) {
					var concepto = (item.concepto || '').trim();
					if (concepto) {
						// Buscar el checkbox que coincida con el concepto
						$('.servicio-check').each(function() {
							var valor = $(this).val();
							var valorConcepto = valor.split(':')[0].trim();
							if (concepto.toLowerCase() === valorConcepto.toLowerCase()) {
								$(this).prop('checked', true);
								return false; // break del each
							}
						});
					}
				});
				
				// Actualizar el detalle después de marcar los servicios
				setTimeout(function() {
					actualizarDetalle();
				}, 100);
			} else if (detalleJson.detalle) {
				// Si hay un campo detalle con texto, intentar parsearlo
				var detalleTexto = detalleJson.detalle;
				$('#detalle').val(detalleTexto);
				
				// Intentar marcar los servicios basándose en el texto del detalle
				$('.servicio-check').prop('checked', false);
				var lineas = detalleTexto.split('\n');
				lineas.forEach(function(linea) {
					if (linea.trim()) {
						var concepto = linea.split(':')[0].trim();
						$('.servicio-check').each(function() {
							var valor = $(this).val();
							var valorConcepto = valor.split(':')[0].trim();
							if (concepto.toLowerCase() === valorConcepto.toLowerCase()) {
								$(this).prop('checked', true);
								return false;
							}
						});
					}
				});
				actualizarDetalle();
			}
			
			ocultarProcesando(procesando);
			mostrarExito('Datos de la cotización cargados correctamente. Puede revisar y modificar los datos antes de guardar.', 'Éxito');
			
		} catch(e) {
			ocultarProcesando(procesando);
			console.error('Error al procesar datos de cotización:', e);
			mostrarError('Error al procesar los datos de la cotización: ' + e.message, 'Error');
		}
	}, 'json').fail(function(xhr, status, error) {
		ocultarProcesando(procesando);
		mostrarError('Error al comunicarse con el servidor: ' + error, 'Error de Conexión');
	});
}

function mostrarform(flag) {
	limpiar();
	if (flag) {
		$('#listadoregistros').hide();
		$('#formularioregistros').show();
		$('#btnGuardar').prop('disabled', false);
		// Cargar lista de cotizaciones cuando se muestra el formulario
		cargarListaCotizaciones();
	} else {
		$('#listadoregistros').show();
		$('#formularioregistros').hide();
	}
}

function cancelarform() {
	limpiar();
	mostrarform(false);
}

function actualizarDetalle() {
	var serviciosSeleccionados = [];
	// Capturar todos los checkboxes marcados
	$('.servicio-check:checked').each(function(){
		var valor = $(this).val();
		if (valor && valor.trim().length > 0) {
			serviciosSeleccionados.push(valor);
		}
	});
	var detalleTexto = serviciosSeleccionados.join('\n');
	$('#detalle').val(detalleTexto);
	
	// Actualizar el total si hay servicios seleccionados
	if (serviciosSeleccionados.length > 0 && !$('#total').val()) {
		// El total se puede calcular o dejar que el usuario lo ingrese manualmente
		// Por ahora no calculamos automáticamente
	}
	
	console.log('Servicios seleccionados:', serviciosSeleccionados.length, detalleTexto);
	return detalleTexto;
}

function seleccionarPaquete(tipo) {
	switch(tipo) {
		case 'basico':
			$('.servicio-check.basico').prop('checked', true);
			break;
		case 'intermedio':
			$('.servicio-check.intermedio').prop('checked', true);
			break;
		case 'premium':
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
			url: '../ajax/contrato.php?op=listar',
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
	const edadFallecido = $('#edad_fallecido').val();
	
	// Obtener nombres y apellidos del contratante
	const nombresContratante = $('#nombres_contratante').val().trim();
	const apellidosContratante = $('#apellidos_contratante').val().trim();
	const nombreContratante = (nombresContratante + ' ' + apellidosContratante).trim();
	
	const cedulaContratante = $('#cedula_contratante').val();
	const complementoContratante = $('#complemento_contratante').val().trim().toUpperCase();
	const direccionContratante = $('#direccion_contratante').val();
	const contactoEmail = $('#contacto_email').val();
	const contactoCelular = $('#contacto_celular').val();
	const detalleTexto = $('#detalle').val();
	const total = $('#total').val();

	console.log('Detalle texto capturado:', detalleTexto);
	console.log('Checkboxes marcados:', $('.servicio-check:checked').length);

	// Validar campos requeridos
	if (!nombresContratante || nombresContratante.trim() === '') {
		mostrarError('Los nombres del contratante son requeridos', 'Error de Validación');
		$('#nombres_contratante').focus();
		return;
	}
	
	if (!apellidosContratante || apellidosContratante.trim() === '') {
		mostrarError('Los apellidos del contratante son requeridos', 'Error de Validación');
		$('#apellidos_contratante').focus();
		return;
	}
	
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
	if (edadFallecido && edadFallecido.trim() !== '') {
		var edad = parseInt(edadFallecido);
		if (isNaN(edad) || edad < 0 || edad > 122) {
			mostrarError('La edad debe estar entre 0 y 122 años', 'Error de Validación');
			$('#edad_fallecido').focus();
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
		mostrarError('Por favor, seleccione al menos un servicio antes de generar el contrato', 'Error de Validación');
		return;
	}
	
	// Mostrar indicador de procesamiento
	var procesando = mostrarProcesando('Generando PDF y guardando contrato...');

	// Parsear detalle a JSON simple
	const items = detalleTexto.split('\n').filter(x=>x.trim().length>0).map(line=>{
		const parts = line.split(':');
		const concepto = (parts[0]||'').trim();
		const montoStr = (parts[1]||'0').trim();
		const monto = parseFloat(montoStr.replace(/[^0-9.,-]/g,'').replace(',', '.'))||0;
		return { concepto: concepto, monto: monto };
	});
	
	console.log('Items parseados:', items.length, items);
	
	// Validar que se parsearon items
	if (items.length === 0) {
		ocultarProcesando(procesando);
		mostrarError('Error: No se pudieron procesar los servicios seleccionados. Por favor, seleccione al menos un servicio.', 'Error de Procesamiento');
		return;
	}

	// Fecha actual
	const fechaActual = new Date();
	const fechaFormato = fechaActual.toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
	const fechaContrato = fechaActual.toLocaleDateString('es-BO', { year: 'numeric', month: '2-digit', day: '2-digit' });

	// Construir tabla de servicios
	const body = [
		[
			{text: '#', style: 'tableHeader', alignment: 'center', width: 30},
			{text: 'DESCRIPCIÓN DEL SERVICIO', style: 'tableHeader'},
			{text: 'MONTO (Bs.)', style: 'tableHeader', alignment: 'right', width: 100}
		]
	];
	let numItem = 1;
	let subtotal = 0;
	items.forEach(it => {
		subtotal += it.monto;
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
	body.push([
		{text: 'TOTAL:', bold: true, fontSize: 12, colSpan: 2, alignment: 'right'},
		{},
		{text: 'Bs. ' + Number(total||0).toFixed(2), bold: true, fontSize: 12, alignment: 'right'}
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
								{text: 'CONTRATO DE SERVICIOS FUNERARIOS', fontSize: 14, bold: true, alignment: 'right', color: '#333333'},
								{text: fechaFormato, fontSize: 10, alignment: 'right', color: '#666666', margin: [0, 5, 0, 0]}
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
			// Título del contrato
			{
				text: 'CONTRATO DE PRESTACIÓN DE SERVICIOS FUNERARIOS',
				style: 'title',
				alignment: 'center',
				margin: [0, 0, 0, 20]
			},
			{
				text: [
					{text: 'En la ciudad de La Paz, a los ', fontSize: 10},
					{text: fechaContrato.split('/')[0], fontSize: 10, bold: true},
					{text: ' días del mes de ', fontSize: 10},
					{text: fechaActual.toLocaleDateString('es-BO', { month: 'long' }), fontSize: 10, bold: true},
					{text: ' del año ', fontSize: 10},
					{text: fechaContrato.split('/')[2], fontSize: 10, bold: true},
					{text: ', se celebra el presente contrato entre:', fontSize: 10}
				],
				margin: [0, 0, 0, 15]
			},

			// Parte contratante
			{
				text: 'PARTE CONTRATANTE',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				text: [
					{text: 'Nombre completo: ', bold: true},
					{text: nombreContratante || 'N/A'}
				],
				margin: [0, 0, 0, 5]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{text: 'Cédula de Identidad: ', bold: true},
							{text: (cedulaContratante || 'N/A') + (complementoContratante ? ' ' + complementoContratante : '')}
						]
					},
					{
						width: '*',
						text: [
							{text: 'Teléfono: ', bold: true},
							{text: contactoCelular || 'N/A'}
						]
					}
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{text: 'Dirección: ', bold: true},
					{text: direccionContratante || 'N/A'}
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{text: 'Email: ', bold: true},
					{text: contactoEmail || 'N/A'}
				],
				margin: [0, 0, 0, 15]
			},

			// Datos del fallecido
			{
				text: 'DATOS DEL FALLECIDO',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
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
							{text: edadFallecido || 'N/A'}
						]
					}
				],
				margin: [0, 0, 0, 15]
			},

			// Servicios contratados
			{
				text: 'SERVICIOS CONTRATADOS',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				text: 'Por el presente contrato, la FUNERARIA GENESIS se compromete a prestar los siguientes servicios funerarios:',
				margin: [0, 0, 0, 10],
				fontSize: 10
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

			// Términos y condiciones
			{
				text: 'TÉRMINOS Y CONDICIONES',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				ul: [
					'El CONTRATANTE se compromete a pagar el monto total acordado según las condiciones establecidas.',
					'Los servicios serán prestados conforme a los estándares de calidad de la FUNERARIA GENESIS.',
					'Cualquier modificación al presente contrato deberá ser acordada por ambas partes por escrito.',
					'El presente contrato tiene validez legal desde la fecha de su firma.',
					'En caso de incumplimiento, se aplicarán las sanciones establecidas por la ley vigente.'
				],
				margin: [0, 0, 0, 15],
				fontSize: 9
			},

			// Firmas
			{
				columns: [
					{
						width: '*',
						text: [
							{text: '_________________________\n', alignment: 'center'},
							{text: nombreContratante || 'CONTRATANTE', alignment: 'center', bold: true},
							{text: '\nC.I.: ' + (cedulaContratante || 'N/A') + (complementoContratante ? ' ' + complementoContratante : ''), alignment: 'center', fontSize: 9}
						],
						margin: [0, 20, 0, 0]
					},
					{
						width: '*',
						text: [
							{text: '_________________________\n', alignment: 'center'},
							{text: 'FUNERARIA GENESIS', alignment: 'center', bold: true},
							{text: '\nRepresentante Legal', alignment: 'center', fontSize: 9}
						],
						margin: [0, 20, 0, 0]
					}
				]
			}
		],
		styles: {
			title: {
				fontSize: 16,
				bold: true,
				color: '#333333',
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
	formData.append('pdf', blob, 'contrato.pdf');
	// Enviar nombres y apellidos separados
	formData.append('nombres_fallecido', nombresFallecido);
	formData.append('apellidos_fallecido', apellidosFallecido);
	formData.append('nombres_contratante', nombresContratante);
	formData.append('apellidos_contratante', apellidosContratante);
	// También enviar nombre completo para compatibilidad
	formData.append('nombre_fallecido', nombreFallecido);
	formData.append('nombre_contratante', nombreContratante);
	formData.append('fecha_fallecimiento', fechaFallecimiento);
	formData.append('edad_fallecido', edadFallecido);
	formData.append('cedula_contratante', cedulaContratante);
	formData.append('complemento_contratante', complementoContratante);
	formData.append('contacto_email', contactoEmail);
	formData.append('contacto_celular', contactoCelular);
	formData.append('direccion_contratante', direccionContratante);
	formData.append('detalle_json', JSON.stringify({
		items: items,
		nombres_fallecido: nombresFallecido,
		apellidos_fallecido: apellidosFallecido,
		nombre_fallecido: nombreFallecido,
		nombres_contratante: nombresContratante,
		apellidos_contratante: apellidosContratante,
		nombre_contratante: nombreContratante,
		fecha_fallecimiento: fechaFallecimiento,
		edad_fallecido: edadFallecido
	}));
	formData.append('total', total);
	formData.append('hash_sha256', hashHex);
	formData.append('firma_nombres', nombresContratante);
	formData.append('firma_apellidos', apellidosContratante);
	formData.append('firma_nombre', nombreContratante);
	formData.append('firma_fecha', new Date().toISOString().slice(0,19).replace('T',' '));

	$.ajax({
		url: '../ajax/contrato.php?op=guardar',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function(resp){
			ocultarProcesando(procesando);
			try { 
				var r = JSON.parse(resp); 
				if (r.ok) {
					mostrarExito('Contrato guardado correctamente. Código: ' + r.codigo, 'Éxito');
					setTimeout(function() {
						mostrarform(false);
						tabla.ajax.reload();
					}, 1500);
				} else {
					mostrarError(r.mensaje || 'Error al guardar el contrato', 'Error');
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
	$.post('../ajax/contrato.php?op=mostrar', {idcontrato:id}, function(data){
		var d = JSON.parse(data);
		if (!d) return;
		var link = '../'+d.ruta_pdf;
		
		// Cargar permisos existentes
		$.post('../ajax/contrato.php?op=listarPermisos', {idcontrato:id}, function(permisosData){
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
				permisosHtml += '<p class="text-muted">No hay usuarios con permiso para ver este contrato.</p>';
			}
			permisosHtml += '</div></div>';
			
			var message = '<p><b>Contratante:</b> '+d.nombre_contratante+'</p>'+
				'<p><a target="_blank" class="btn btn-primary" href="'+link+'"><i class="fa fa-download"></i> Descargar PDF</a></p>'+
				'<hr>'+permisosHtml;
			
			bootbox.dialog({
				title: 'Contrato '+d.codigo,
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

function mostrarModalClientes(idcontrato) {
	// Cargar lista de usuarios registrados
	$.get('../ajax/contrato.php?op=listarClientes', function(data){
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
						otorgarPermiso(idcontrato, idcliente);
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

function otorgarPermiso(idcontrato, idcliente) {
	$.post('../ajax/contrato.php?op=otorgarPermiso', {idcontrato:idcontrato, idcliente:idcliente}, function(data){
		var r = JSON.parse(data);
		if (r.ok) {
			mostrarExito(r.msg, 'Permiso Otorgado');
			setTimeout(function() {
				ver(idcontrato); // Recargar el modal
			}, 1000);
		} else {
			mostrarError(r.msg, 'Error');
		}
	});
}

function quitarPermiso(idcontrato, idcliente) {
	bootbox.confirm('¿Está seguro de revocar el permiso de visualización para este usuario?', function(result){
		if (result) {
			$.post('../ajax/contrato.php?op=quitarPermiso', {idcontrato:idcontrato, idcliente:idcliente}, function(data){
				var r = JSON.parse(data);
				if (r.ok) {
					mostrarExito(r.msg, 'Permiso Revocado');
					setTimeout(function() {
						ver(idcontrato); // Recargar el modal
					}, 1000);
				} else {
					mostrarError(r.msg || 'Error al revocar permiso', 'Error');
				}
			});
		}
	});
}

init();



