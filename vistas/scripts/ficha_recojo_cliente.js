var tabla;

function init() {
	listar();
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/ficha_recojo_cliente.php?op=listar',
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
	$.post('../ajax/ficha_recojo_cliente.php?op=mostrar', {idficha:id}, function(data){
		var d = JSON.parse(data);
		if (d.error) {
			bootbox.alert(d.error);
			return;
		}
		if (!d) {
			bootbox.alert('No se pudo cargar la información de la ficha de recojo');
			return;
		}
		var link = '../'+d.ruta_pdf;
		
		var mensaje = '<div class="row">';
		mensaje += '<div class="col-md-12">';
		
		// Información general
		mensaje += '<div class="panel panel-primary">';
		mensaje += '<div class="panel-heading"><h4 class="panel-title">Información de la Ficha de Recojo</h4></div>';
		mensaje += '<div class="panel-body">';
		mensaje += '<p><strong>Código:</strong> ' + d.codigo + '</p>';
		mensaje += '<p><strong>Difunto:</strong> ' + d.nombre_difunto + '</p>';
		mensaje += '<p><strong>Cédula:</strong> ' + (d.cedula_difunto || 'N/A') + '</p>';
		mensaje += '<p><strong>Fecha de recojo:</strong> ' + d.fecha_recojo + '</p>';
		if (d.fecha_otorgado) {
			mensaje += '<p><strong>Fecha de acceso otorgado:</strong> ' + d.fecha_otorgado + '</p>';
		}
		mensaje += '</div></div>';
		
		mensaje += '</div></div>';
		
		bootbox.dialog({
			title: 'Ficha de Recojo ' + d.codigo,
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
		bootbox.alert('Error al cargar la ficha de recojo. Por favor, intente nuevamente.');
	});
}

init();








