var tabla;

function init() {
	listar();
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/contrato_cliente.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e){ console.log(e.responseText); }
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[0, "desc"]]
	});
}

async function ver(id) {
	$.post('../ajax/contrato_cliente.php?op=mostrar', {idcontrato:id}, async function(data){
		var d = JSON.parse(data);
		if (d.error) {
			bootbox.alert(d.error);
			return;
		}
		if (!d) return;
		
		var link = '../'+d.ruta_pdf;
		
		// Parsear detalle a JSON simple
		const items = d.detalle_json ? JSON.parse(d.detalle_json).items : [];

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
		body.push([
			{text: 'TOTAL:', bold: true, fontSize: 12, colSpan: 2, alignment: 'right'},
			{},
			{text: 'Bs. ' + Number(d.total||0).toFixed(2), bold: true, fontSize: 12, alignment: 'right'}
		]);

		var serviciosHtml = '';
		if (items.length > 0) {
			serviciosHtml = '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">Servicios Contratados</h4></div><div class="panel-body">';
			serviciosHtml += '<table class="table table-bordered table-condensed"><thead><tr><th>#</th><th>Descripción</th><th>Monto (Bs.)</th></tr></thead><tbody>';
			items.forEach((item, index) => {
				serviciosHtml += `<tr><td>${index + 1}</td><td>${item.concepto}</td><td class="text-right">${item.monto.toFixed(2)}</td></tr>`;
			});
			serviciosHtml += `<tr><td colspan="2" class="text-right"><strong>TOTAL:</strong></td><td class="text-right"><strong>Bs. ${Number(d.total||0).toFixed(2)}</strong></td></tr>`;
			serviciosHtml += '</tbody></table></div></div>';
		} else {
			serviciosHtml = '<p class="text-muted">No hay servicios detallados en este contrato.</p>';
		}

		var message = '<p><b>Código:</b> '+d.codigo+'</p>'+
			'<p><b>Contratante:</b> '+d.nombre_contratante+'</p>'+
			'<p><b>Cédula:</b> '+(d.cedula_contratante||'N/A')+'</p>'+
			'<p><b>Fecha de Creación:</b> '+d.fecha_creacion+'</p>'+
			'<p><b>Estado:</b> '+d.estado+'</p>'+
			'<hr>'+
			'<p><b>Datos del Fallecido:</b></p>'+
			'<p>Nombre: '+(JSON.parse(d.detalle_json).nombre_fallecido || 'N/A')+'</p>'+
			'<p>Fecha Fallecimiento: '+(JSON.parse(d.detalle_json).fecha_fallecimiento ? new Date(JSON.parse(d.detalle_json).fecha_fallecimiento).toLocaleDateString('es-BO') : 'N/A')+'</p>'+
			'<p>Edad: '+(JSON.parse(d.detalle_json).edad_fallecido || 'N/A')+'</p>'+
			'<hr>'+
			serviciosHtml +
			'<hr>'+
			'<p><a target="_blank" class="btn btn-primary" href="'+link+'"><i class="fa fa-download"></i> Descargar PDF</a></p>';
		
		bootbox.dialog({
			title: 'Detalle de Contrato '+d.codigo,
			message: message,
			size: 'large',
			buttons: {
				close: { label: 'Cerrar', className:'btn-default' }
			}
		});
	});
}

init();










