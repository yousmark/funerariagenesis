var tabla;

function init() {
	listar();
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/cotizacion_cliente.php?op=listar',
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
	$.post('../ajax/cotizacion_cliente.php?op=mostrar', {idcotizacion:id}, function(data){
		var d = JSON.parse(data);
		if (d.error) {
			bootbox.alert(d.error);
			return;
		}
		if (!d) {
			bootbox.alert('No se pudo cargar la información de la cotización');
			return;
		}
		var link = '../'+d.ruta_pdf;
		
		// Parsear el detalle JSON
		var detalleJson = {};
		try {
			detalleJson = JSON.parse(d.detalle_json);
		} catch(e) {
			console.error('Error al parsear detalle JSON:', e);
		}
		
		var mensaje = '<div class="row">';
		mensaje += '<div class="col-md-12">';
		
		// Información de la cotización
		mensaje += '<div class="panel panel-primary">';
		mensaje += '<div class="panel-heading"><h4 class="panel-title">Información de la Cotización</h4></div>';
		mensaje += '<div class="panel-body">';
		mensaje += '<p><strong>Código:</strong> ' + d.codigo + '</p>';
		mensaje += '<p><strong>Familia:</strong> ' + d.nombre_familia + '</p>';
		mensaje += '<p><strong>Total:</strong> Bs. ' + parseFloat(d.total).toFixed(2) + '</p>';
		mensaje += '<p><strong>Fecha de creación:</strong> ' + d.fecha_creacion + '</p>';
		if (d.fecha_otorgado) {
			mensaje += '<p><strong>Fecha de acceso otorgado:</strong> ' + d.fecha_otorgado + '</p>';
		}
		mensaje += '</div></div>';
		
		// Información del fallecido si está disponible
		if (detalleJson.nombre_fallecido) {
			mensaje += '<div class="panel panel-info">';
			mensaje += '<div class="panel-heading"><h4 class="panel-title">Datos del Fallecido</h4></div>';
			mensaje += '<div class="panel-body">';
			mensaje += '<p><strong>Nombre:</strong> ' + (detalleJson.nombre_fallecido || 'N/A') + '</p>';
			if (detalleJson.fecha_fallecimiento) {
				mensaje += '<p><strong>Fecha de fallecimiento:</strong> ' + detalleJson.fecha_fallecimiento + '</p>';
			}
			if (detalleJson.edad) {
				mensaje += '<p><strong>Edad:</strong> ' + detalleJson.edad + '</p>';
			}
			mensaje += '</div></div>';
		}
		
		// Servicios incluidos
		if (detalleJson.items && detalleJson.items.length > 0) {
			mensaje += '<div class="panel panel-success">';
			mensaje += '<div class="panel-heading"><h4 class="panel-title">Servicios Incluidos</h4></div>';
			mensaje += '<div class="panel-body">';
			mensaje += '<table class="table table-bordered">';
			mensaje += '<thead><tr><th>#</th><th>Servicio</th><th>Monto (Bs.)</th></tr></thead>';
			mensaje += '<tbody>';
			detalleJson.items.forEach(function(item, index){
				mensaje += '<tr>';
				mensaje += '<td>' + (index + 1) + '</td>';
				mensaje += '<td>' + (item.concepto || 'N/A') + '</td>';
				mensaje += '<td style="text-align: right;">' + parseFloat(item.monto || 0).toFixed(2) + '</td>';
				mensaje += '</tr>';
			});
			mensaje += '</tbody></table>';
			mensaje += '</div></div>';
		}
		
		mensaje += '</div></div>';
		
		bootbox.dialog({
			title: 'Cotización ' + d.codigo,
			message: mensaje,
			size: 'large',
			buttons: {
				descargar: { 
					label: '<i class="fa fa-download"></i> Descargar PDF', 
					className:'btn-primary', 
					callback: function(){
						window.open(link, '_blank');
					} 
				},
				close: { label: 'Cerrar', className:'btn-default' }
			}
		});
	}).fail(function(xhr, status, error) {
		bootbox.alert('Error al cargar la cotización. Por favor, intente nuevamente.');
	});
}

init();












