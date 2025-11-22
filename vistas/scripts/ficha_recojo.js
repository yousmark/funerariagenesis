var tabla;

function init() {
	mostrarform(false);
	listar();

	// Validar edad en tiempo real (máximo 122 años)
	$(document).on('input blur', '#edad_difunto', function () {
		var edad = parseInt($(this).val()) || 0;
		if (edad > 122) {
			$(this).val(122);
			mostrarError('La edad no puede superar los 122 años', 'Validación de Edad');
		} else if (edad < 0) {
			$(this).val(0);
		}
	});

	$('#formulario').on('submit', function (e) {
		e.preventDefault();
		generarYEnviarPDF();
	});

	// Establecer fecha actual por defecto
	var ahora = new Date();
	ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
	$('#fecha_recojo').val(ahora.toISOString().slice(0, 16));
}

function limpiar() {
	$('#nombre_difunto').val("");
	$('#cedula_difunto').val("");
	$('#complemento_difunto').val("");
	$('#edad_difunto').val("");
	$('#fecha_fallecimiento').val("");
	$('#lugar_fallecimiento').val("");
	$('#causa_muerte').val("");
	$('#direccion_recojo').val("");
	$('#ciudad_recojo').val("");
	$('#barrio_zona').val("");
	$('#tipo_lugar').val("Domicilio");
	$('#condiciones_lugar').val("");
	$('#temperatura_ambiente').val("");
	$('#tiempo_trascurrido').val("");
	$('#estado_general').val("Conservado");
	$('#rigor_mortis').val("Presente");
	$('#livideces').val("Si");
	$('#lesiones_visibles').val("");
	$('#indumentaria').val("");
	$('#observaciones_cuerpo').val("");
	$('#equipo_recojo').val("");
	$('#personal_presente').val("");
	$('#insumos_bioseguridad').val("");
	$('#protocolos_aplicados').val("");
	$('#nombre_contacto').val("");
	$('#telefono_contacto').val("");
	$('#parentesco_contacto').val("");
	// Establecer fecha actual
	var ahora = new Date();
	ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
	$('#fecha_recojo').val(ahora.toISOString().slice(0, 16));
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

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/ficha_recojo.php?op=listar',
			type: "get",
			dataType: "json",
			error: function (e) { console.log(e.responseText); }
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[0, "desc"]]
	});
}

async function generarYEnviarPDF() {
	// Validar campos requeridos
	const nombreDifunto = $('#nombre_difunto').val().trim();
	const fechaFallecimiento = $('#fecha_fallecimiento').val();
	const direccionRecojo = $('#direccion_recojo').val().trim();
	const fechaRecojo = $('#fecha_recojo').val();

	if (!nombreDifunto || nombreDifunto === '') {
		mostrarError('El nombre del difunto es requerido', 'Error de Validación');
		$('#nombre_difunto').focus();
		return;
	}

	if (!fechaFallecimiento || fechaFallecimiento === '') {
		mostrarError('La fecha de fallecimiento es requerida', 'Error de Validación');
		$('#fecha_fallecimiento').focus();
		return;
	}

	// Validar edad si se proporciona
	const edadDifunto = $('#edad_difunto').val();
	if (edadDifunto && edadDifunto.trim() !== '') {
		var edad = parseInt(edadDifunto);
		if (isNaN(edad) || edad < 0 || edad > 122) {
			mostrarError('La edad debe estar entre 0 y 122 años', 'Error de Validación');
			$('#edad_difunto').focus();
			return;
		}
	}

	if (!direccionRecojo || direccionRecojo === '') {
		mostrarError('La dirección de recojo es requerida', 'Error de Validación');
		$('#direccion_recojo').focus();
		return;
	}

	if (!fechaRecojo || fechaRecojo === '') {
		mostrarError('La fecha de recojo es requerida', 'Error de Validación');
		$('#fecha_recojo').focus();
		return;
	}

	// Mostrar indicador de procesamiento
	var procesando = mostrarProcesando('Generando PDF y guardando ficha de recojo...');

	// Capturar todos los datos del formulario
	const cedulaDifunto = $('#cedula_difunto').val();
	const complementoDifunto = $('#complemento_difunto').val().trim().toUpperCase();
	// const edadDifunto = $('#edad_difunto').val(); // Ya declarado arriba
	// const fechaFallecimiento = $('#fecha_fallecimiento').val(); // Ya declarado arriba
	const lugarFallecimiento = $('#lugar_fallecimiento').val();
	const causaMuerte = $('#causa_muerte').val();
	// const direccionRecojo = $('#direccion_recojo').val(); // Ya declarado arriba
	const ciudadRecojo = $('#ciudad_recojo').val();
	const barrioZona = $('#barrio_zona').val();
	const tipoLugar = $('#tipo_lugar').val();
	const condicionesLugar = $('#condiciones_lugar').val();
	const temperaturaAmbiente = $('#temperatura_ambiente').val();
	const tiempoTrascurrido = $('#tiempo_trascurrido').val();
	const estadoGeneral = $('#estado_general').val();
	const rigorMortis = $('#rigor_mortis').val();
	const livideces = $('#livideces').val();
	const lesionesVisibles = $('#lesiones_visibles').val();
	const indumentaria = $('#indumentaria').val();
	const observacionesCuerpo = $('#observaciones_cuerpo').val();
	const equipoRecojo = $('#equipo_recojo').val();
	const personalPresente = $('#personal_presente').val();
	const insumosBioseguridad = $('#insumos_bioseguridad').val();
	const protocolosAplicados = $('#protocolos_aplicados').val();
	const nombreContacto = $('#nombre_contacto').val();
	const telefonoContacto = $('#telefono_contacto').val();
	const parentescoContacto = $('#parentesco_contacto').val();
	// const fechaRecojo = $('#fecha_recojo').val(); // Ya declarado arriba

	// Fechas formateadas
	const fechaActual = new Date().toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
	const horaActual = new Date().toLocaleTimeString('es-BO', { hour: '2-digit', minute: '2-digit' });

	const docDef = {
		pageSize: 'LETTER',
		pageMargins: [60, 100, 60, 80],
		header: function (currentPage, pageCount) {
			return [
				{
					columns: [
						{
							text: [
								{ text: 'FUNERARIA GENESIS\n', fontSize: 18, bold: true, color: '#333333' },
								{ text: 'Servicios Funerarios Integrales\n', fontSize: 10, color: '#666666' },
								{ text: 'Tel: (591) 2-1234567 | Email: info@funerariagenesis.com\n', fontSize: 8, color: '#666666' },
								{ text: 'Dirección: Av. Principal, La Paz - Bolivia', fontSize: 8, color: '#666666' }
							],
							width: '*'
						},
						{
							text: [
								{ text: 'FICHA DE RECOJO DEL DIFUNTO', fontSize: 14, bold: true, alignment: 'right', color: '#333333' },
								{ text: fechaActual + ' ' + horaActual, fontSize: 10, alignment: 'right', color: '#666666', margin: [0, 5, 0, 0] }
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
		footer: function (currentPage, pageCount) {
			return {
				text: 'Página ' + currentPage + ' de ' + pageCount,
				alignment: 'center',
				fontSize: 8,
				margin: [0, 10, 0, 0],
				color: '#666666'
			};
		},
		content: [
			// Título
			{
				text: 'REGISTRO DE RECOJO Y TRASLADO DEL DIFUNTO',
				style: 'title',
				alignment: 'center',
				margin: [0, 0, 0, 20]
			},

			// Sección 1: Datos del difunto
			{
				text: 'I. DATOS DEL DIFUNTO',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Nombre completo: ', bold: true },
							{ text: nombreDifunto || 'N/A' }
						]
					},
					{
						width: 'auto',
						text: [
							{ text: 'Cédula: ', bold: true },
							{ text: (cedulaDifunto || 'N/A') + (complementoDifunto ? ' ' + complementoDifunto : '') }
						]
					},
					{
						width: 'auto',
						text: [
							{ text: 'Edad: ', bold: true },
							{ text: edadDifunto || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Fecha y hora de fallecimiento: ', bold: true },
							{ text: fechaFallecimiento ? new Date(fechaFallecimiento).toLocaleString('es-BO') : 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Lugar: ', bold: true },
							{ text: lugarFallecimiento || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 10]
			},
			{
				text: [
					{ text: 'Causa de muerte: ', bold: true },
					{ text: causaMuerte || 'N/A' }
				],
				margin: [0, 0, 0, 20]
			},

			// Sección 2: Lugar de recojo
			{
				text: 'II. INFORMACIÓN DEL LUGAR DE RECOJO',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				text: [
					{ text: 'Dirección: ', bold: true },
					{ text: direccionRecojo || 'N/A' }
				],
				margin: [0, 0, 0, 5]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Ciudad: ', bold: true },
							{ text: ciudadRecojo || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Barrio/Zona: ', bold: true },
							{ text: barrioZona || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Tipo de lugar: ', bold: true },
							{ text: tipoLugar || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Condiciones: ', bold: true },
							{ text: condicionesLugar || 'N/A' }
						]
					},
					{
						width: 'auto',
						text: [
							{ text: 'Temp. ambiente: ', bold: true },
							{ text: temperaturaAmbiente ? temperaturaAmbiente + '°C' : 'N/A' }
						]
					},
					{
						width: 'auto',
						text: [
							{ text: 'Tiempo transcurrido: ', bold: true },
							{ text: tiempoTrascurrido || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 20]
			},

			// Sección 3: Estado del cuerpo
			{
				text: 'III. ESTADO DEL CUERPO',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Estado general: ', bold: true },
							{ text: estadoGeneral || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Rigor mortis: ', bold: true },
							{ text: rigorMortis || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Livideces: ', bold: true },
							{ text: livideces || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Lesiones visibles: ', bold: true },
							{ text: lesionesVisibles || 'Ninguna' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Indumentaria: ', bold: true },
							{ text: indumentaria || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{ text: 'Observaciones: ', bold: true },
					{ text: observacionesCuerpo || 'Ninguna' }
				],
				margin: [0, 0, 0, 20]
			},

			// Sección 4: Bioseguridad y personal
			{
				text: 'IV. BIOSEGURIDAD Y PERSONAL',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Equipo de recojo: ', bold: true },
							{ text: equipoRecojo || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Personal presente: ', bold: true },
							{ text: personalPresente || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{ text: 'Insumos utilizados: ', bold: true },
					{ text: insumosBioseguridad || 'N/A' }
				],
				margin: [0, 0, 0, 5]
			},
			{
				text: [
					{ text: 'Protocolos aplicados: ', bold: true },
					{ text: protocolosAplicados || 'N/A' }
				],
				margin: [0, 0, 0, 20]
			},

			// Sección 5: Contacto
			{
				text: 'V. DATOS DE CONTACTO',
				style: 'sectionHeader',
				margin: [0, 15, 0, 10]
			},
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: 'Nombre: ', bold: true },
							{ text: nombreContacto || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Teléfono: ', bold: true },
							{ text: telefonoContacto || 'N/A' }
						]
					},
					{
						width: '*',
						text: [
							{ text: 'Parentesco: ', bold: true },
							{ text: parentescoContacto || 'N/A' }
						]
					}
				],
				margin: [0, 0, 0, 30]
			},

			// Firmas
			{
				columns: [
					{
						width: '*',
						text: [
							{ text: '_________________________\n', alignment: 'center' },
							{ text: nombreContacto || 'RECEPTOR', alignment: 'center', bold: true },
							{ text: 'Testigo/Contacto', alignment: 'center', fontSize: 9 }
						],
						margin: [0, 20, 0, 0]
					},
					{
						width: '*',
						text: [
							{ text: '_________________________\n', alignment: 'center' },
							{ text: 'FUNERARIA GENESIS', alignment: 'center', bold: true },
							{ text: 'Personal Autorizado', alignment: 'center', fontSize: 9 }
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
			}
		},
		defaultStyle: {
			fontSize: 10,
			color: '#333333'
		}
	};

	const pdfDoc = pdfMake.createPdf(docDef);
	const arrayBuffer = await new Promise((resolve) => pdfDoc.getBuffer((buffer) => resolve(buffer.buffer)));
	const hashBuf = await crypto.subtle.digest('SHA-256', arrayBuffer);
	const hashHex = Array.from(new Uint8Array(hashBuf)).map(b => b.toString(16).padStart(2, '0')).join('');

	const blob = new Blob([arrayBuffer], { type: 'application/pdf' });
	const formData = new FormData();
	formData.append('pdf', blob, 'ficha_recojo.pdf');
	formData.append('nombre_difunto', nombreDifunto);
	formData.append('cedula_difunto', cedulaDifunto);
	formData.append('complemento_difunto', complementoDifunto);
	formData.append('edad_difunto', edadDifunto);
	formData.append('fecha_fallecimiento', fechaFallecimiento);
	formData.append('lugar_fallecimiento', lugarFallecimiento);
	formData.append('causa_muerte', causaMuerte);
	formData.append('direccion_recojo', direccionRecojo);
	formData.append('ciudad_recojo', ciudadRecojo);
	formData.append('barrio_zona', barrioZona);
	formData.append('tipo_lugar', tipoLugar);
	formData.append('condiciones_lugar', condicionesLugar);
	formData.append('temperatura_ambiente', temperaturaAmbiente);
	formData.append('tiempo_trascurrido', tiempoTrascurrido);
	formData.append('estado_general', estadoGeneral);
	formData.append('rigor_mortis', rigorMortis);
	formData.append('livideces', livideces);
	formData.append('lesiones_visibles', lesionesVisibles);
	formData.append('indumentaria', indumentaria);
	formData.append('observaciones_cuerpo', observacionesCuerpo);
	formData.append('equipo_recojo', equipoRecojo);
	formData.append('personal_presente', personalPresente);
	formData.append('insumos_bioseguridad', insumosBioseguridad);
	formData.append('protocolos_aplicados', protocolosAplicados);
	formData.append('nombre_contacto', nombreContacto);
	formData.append('telefono_contacto', telefonoContacto);
	formData.append('parentesco_contacto', parentescoContacto);
	formData.append('fecha_recojo', fechaRecojo);
	formData.append('hash_sha256', hashHex);

	$.ajax({
		url: '../ajax/ficha_recojo.php?op=guardar',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		success: function (resp) {
			ocultarProcesando();
			try {
				var r = JSON.parse(resp);
				if (r.ok) {
					mostrarExito('Ficha de recojo guardada correctamente. Código: ' + r.codigo, 'Éxito');
					setTimeout(function () {
						mostrarform(false);
						tabla.ajax.reload();
					}, 1500);
				} else {
					mostrarError(r.mensaje || 'Error al guardar la ficha de recojo', 'Error');
				}
			} catch (e) {
				if (resp.indexOf('correctamente') !== -1 || resp.indexOf('Código') !== -1) {
					mostrarExito(resp, 'Éxito');
					setTimeout(function () {
						mostrarform(false);
						tabla.ajax.reload();
					}, 1500);
				} else {
					mostrarError(resp, 'Error');
				}
			}
		},
		error: function (xhr, status, error) {
			ocultarProcesando();
			mostrarError('Error al comunicarse con el servidor: ' + error, 'Error de Conexión');
		}
	});
}

function ver(id) {
	$.post('../ajax/ficha_recojo.php?op=mostrar', { idficha: id }, function (data) {
		var d = JSON.parse(data);
		if (!d) return;
		var link = '../' + d.ruta_pdf;

		// Cargar permisos existentes
		$.post('../ajax/ficha_recojo.php?op=listarPermisos', { idficha: id }, function (permisosData) {
			var permisos = JSON.parse(permisosData);
			var permisosHtml = '<div class="panel panel-info"><div class="panel-heading"><h4 class="panel-title">Usuarios con Permiso</h4></div><div class="panel-body">';

			if (permisos.length > 0) {
				permisosHtml += '<table class="table table-bordered table-condensed"><thead><tr><th>Usuario</th><th>Email</th><th>Celular</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
				permisos.forEach(function (permiso) {
					var estadoClass = permiso.estado === 'activo' ? 'label-success' : 'label-danger';
					permisosHtml += '<tr><td>' + permiso.cliente_nombre + '</td><td>' + (permiso.cliente_email || 'N/A') + '</td><td>' + (permiso.cliente_celular || 'N/A') + '</td>';
					permisosHtml += '<td>' + permiso.fecha_otorgado + '</td><td><span class="label ' + estadoClass + '">' + permiso.estado + '</span></td>';
					if (permiso.estado === 'activo') {
						permisosHtml += '<td><button class="btn btn-danger btn-xs" onclick="quitarPermiso(' + id + ',' + permiso.idcliente + ')"><i class="fa fa-times"></i> Revocar</button></td>';
					} else {
						permisosHtml += '<td>-</td>';
					}
					permisosHtml += '</tr>';
				});
				permisosHtml += '</tbody></table>';
			} else {
				permisosHtml += '<p class="text-muted">No hay usuarios con permiso para ver esta ficha.</p>';
			}
			permisosHtml += '</div></div>';

			var message = '<p><b>Código:</b> ' + d.codigo + '</p>' +
				'<p><b>Difunto:</b> ' + d.nombre_difunto + '</p>' +
				'<p><b>Fecha de recojo:</b> ' + d.fecha_recojo + '</p>' +
				'<p><b>Estado:</b> ' + d.estado + '</p>' +
				'<hr>' +
				'<p><a target="_blank" class="btn btn-primary" href="' + link + '"><i class="fa fa-download"></i> Descargar PDF</a></p>' +
				'<hr>' + permisosHtml;

			bootbox.dialog({
				title: 'Ficha de Recojo ' + d.codigo,
				message: message,
				size: 'large',
				buttons: {
					permiso: {
						label: 'Otorgar Permiso a Usuario',
						className: 'btn-success',
						callback: function () {
							mostrarModalClientes(id);
						}
					},
					close: { label: 'Cerrar', className: 'btn-default' }
				}
			});
		});
	});
}

function mostrarModalClientes(idficha) {
	// Cargar lista de usuarios registrados
	$.get('../ajax/ficha_recojo.php?op=listarClientes', function (data) {
		var clientes = JSON.parse(data);
		var options = '<option value="">Seleccione un usuario</option>';
		clientes.forEach(function (cliente) {
			options += '<option value="' + cliente.idcliente + '">' + cliente.nombre + ' (' + (cliente.email || 'Sin email') + ')</option>';
		});

		bootbox.prompt({
			title: 'Otorgar Permiso de Visualización',
			inputType: 'select',
			inputOptions: clientes.map(function (c) { return { text: c.nombre + ' (' + (c.email || 'Sin email') + ')', value: c.idcliente }; }),
			callback: function (result) {
				if (result && result !== '') {
					otorgarPermiso(idficha, result);
				}
			}
		});
	});
}

function otorgarPermiso(idficha, idcliente) {
	$.post('../ajax/ficha_recojo.php?op=otorgarPermiso', {
		idficha: idficha,
		idcliente: idcliente
	}, function (data) {
		var r = JSON.parse(data);
		if (r.ok) {
			mostrarExito(r.msg, 'Permiso Otorgado');
			setTimeout(function () {
				ver(idficha); // Recargar la vista para mostrar los permisos actualizados
			}, 1000);
		} else {
			mostrarError(r.msg || 'Error al otorgar permiso', 'Error');
		}
	});
}

function quitarPermiso(idficha, idcliente) {
	bootbox.confirm('¿Está seguro de revocar el permiso a este usuario?', function (result) {
		if (result) {
			$.post('../ajax/ficha_recojo.php?op=quitarPermiso', {
				idficha: idficha,
				idcliente: idcliente
			}, function (data) {
				var r = JSON.parse(data);
				if (r.ok) {
					mostrarExito(r.msg, 'Permiso Revocado');
					setTimeout(function () {
						ver(idficha); // Recargar la vista para mostrar los permisos actualizados
					}, 1000);
				} else {
					mostrarError(r.msg || 'Error al revocar permiso', 'Error');
				}
			});
		}
	});
}

init();


