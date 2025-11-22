var tabla;

function init() {
	listar();
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/itinerario_funerario_cliente.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e){ 
				console.log(e.responseText);
				if (e.responseText && e.responseText.includes('Sesión expirada')) {
					bootbox.alert('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
					window.location.href = 'login.html';
				}
			}
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[0, "desc"]]
	});
}

function ver(id) {
	$.post('../ajax/itinerario_funerario_cliente.php?op=mostrar', {iditinerario:id}, function(data){
		var d = JSON.parse(data);
		if (d.error) {
			bootbox.alert(d.error);
			return;
		}
		if (!d) {
			bootbox.alert('No se pudo cargar la información del itinerario');
			return;
		}
		var link = '../'+d.ruta_pdf;
		
		// Formatear fecha y hora
		const fechaObj = new Date(d.fecha_sepelio);
		const fechaFormateada = fechaObj.toLocaleDateString('es-BO', { year: 'numeric', month: 'long', day: 'numeric' });
		const horaFormateada = d.hora_sepelio + ' hrs.';
		
		var mensaje = '<div class="row">';
		mensaje += '<div class="col-md-12">';
		
		// Información general
		mensaje += '<div class="panel panel-primary">';
		mensaje += '<div class="panel-heading"><h4 class="panel-title">Información del Itinerario</h4></div>';
		mensaje += '<div class="panel-body">';
		mensaje += '<p><strong>Código:</strong> ' + d.codigo + '</p>';
		mensaje += '<p><strong>Nombre del difunto:</strong> ' + d.nombre_difunto + '</p>';
		mensaje += '<p><strong>Familia:</strong> ' + d.nombre_familia + '</p>';
		if (d.telefono_contacto) {
			mensaje += '<p><strong>Teléfono de contacto:</strong> ' + d.telefono_contacto + '</p>';
		}
		mensaje += '</div></div>';
		
		// Información del sepelio
		mensaje += '<div class="panel panel-info">';
		mensaje += '<div class="panel-heading"><h4 class="panel-title">Información del Sepelio</h4></div>';
		mensaje += '<div class="panel-body">';
		mensaje += '<p><strong>Fecha:</strong> ' + fechaFormateada + '</p>';
		mensaje += '<p><strong>Hora:</strong> ' + horaFormateada + '</p>';
		mensaje += '<p><strong>Lugar:</strong> ' + d.lugar_sepelio + '</p>';
		if (d.direccion_sepelio) {
			mensaje += '<p><strong>Dirección:</strong> ' + d.direccion_sepelio + '</p>';
		}
		mensaje += '</div></div>';
		
		// Ruta del cortejo
		mensaje += '<div class="panel panel-success">';
		mensaje += '<div class="panel-heading"><h4 class="panel-title">Ruta del Cortejo</h4></div>';
		mensaje += '<div class="panel-body">';
		if (d.punto_partida) {
			mensaje += '<p><strong>Punto de partida:</strong> ' + d.punto_partida + '</p>';
		}
		mensaje += '<p><strong>Ruta:</strong></p>';
		mensaje += '<p style="white-space: pre-wrap;">' + d.ruta_cortejo + '</p>';
		mensaje += '</div></div>';
		
		// Observaciones si existen
		if (d.observaciones) {
			mensaje += '<div class="panel panel-warning">';
			mensaje += '<div class="panel-heading"><h4 class="panel-title">Observaciones</h4></div>';
			mensaje += '<div class="panel-body">';
			mensaje += '<p style="white-space: pre-wrap;">' + d.observaciones + '</p>';
			mensaje += '</div></div>';
		}
		
		// Estado de confirmación
		var confirmadoHtml = '';
		if (d.confirmado_familia == 1) {
			confirmadoHtml = '<div class="alert alert-success"><i class="fa fa-check-circle"></i> <strong>Itinerario confirmado</strong><br>';
			if (d.fecha_confirmacion_familia) {
				confirmadoHtml += 'Fecha de confirmación: ' + d.fecha_confirmacion_familia + '</div>';
			}
		} else {
			confirmadoHtml = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <strong>Itinerario pendiente de confirmación</strong><br>';
			confirmadoHtml += 'Por favor, revise todos los detalles y confirme este itinerario para evitar errores en el desarrollo del sepelio.</div>';
		}
		mensaje += confirmadoHtml;
		
		mensaje += '</div></div>';
		
		var botones = {
			descargar: { 
				label: '<i class="fa fa-download"></i> Descargar PDF', 
				className:'btn-primary', 
				callback: function(){
					window.open(link, '_blank');
				} 
			},
			close: { label: 'Cerrar', className:'btn-default' }
		};
		
		// Agregar botón de confirmar solo si no está confirmado
		if (d.confirmado_familia == 0) {
			botones.confirmar = {
				label: '<i class="fa fa-check"></i> Confirmar Itinerario',
				className: 'btn-success',
				callback: function(){
					confirmarItinerario(id);
				}
			};
		}
		
		bootbox.dialog({
			title: 'Itinerario ' + d.codigo,
			message: mensaje,
			size: 'large',
			buttons: botones
		});
	}).fail(function(xhr, status, error) {
		bootbox.alert('Error al cargar el itinerario. Por favor, intente nuevamente.');
	});
}

function confirmarItinerario(id) {
	bootbox.confirm({
		title: 'Confirmar Itinerario',
		message: '<p><strong>¿Está seguro de confirmar este itinerario funerario?</strong></p>' +
		         '<p>Al confirmar, usted está de acuerdo con todos los detalles del sepelio incluyendo fecha, hora, lugar y ruta del cortejo.</p>' +
		         '<p class="text-danger"><i class="fa fa-exclamation-triangle"></i> Una vez confirmado, cualquier cambio deberá ser coordinado con la funeraria.</p>',
		buttons: {
			confirm: {
				label: '<i class="fa fa-check"></i> Sí, Confirmar',
				className: 'btn-success'
			},
			cancel: {
				label: '<i class="fa fa-times"></i> Cancelar',
				className: 'btn-default'
			}
		},
		callback: function(result) {
			if (result) {
				$.post('../ajax/itinerario_funerario_cliente.php?op=confirmar', {
					iditinerario: id
				}, function(data){
					try {
						var r = JSON.parse(data);
						if (r.ok) {
							bootbox.alert({
								message: '<div class="alert alert-success"><i class="fa fa-check-circle"></i> <strong>¡Itinerario confirmado exitosamente!</strong><br>La funeraria ha sido notificada de su confirmación.</div>',
								callback: function(){
									tabla.ajax.reload();
								}
							});
						} else {
							bootbox.alert('Error: ' + (r.msg || 'No se pudo confirmar el itinerario'));
						}
					} catch(e) {
						console.error('Error al parsear respuesta:', e, data);
						bootbox.alert('Error al confirmar el itinerario. Por favor, intente nuevamente.');
					}
				}).fail(function(xhr, status, error) {
					console.error('Error en AJAX:', status, error, xhr.responseText);
					bootbox.alert('Error al confirmar el itinerario. Por favor, intente nuevamente.');
				});
			}
		}
	});
}

init();








