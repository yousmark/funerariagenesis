var tabla;

function init() {
	listar();
	
	$("#btnCrearBackup").on("click", function() {
		crearBackup();
	});
}

function listar() {
	tabla = $('#tbllistado').DataTable({
		"aProcessing": true,
		"aServerSide": true,
		"ajax": {
			url: '../ajax/backup.php?op=listar',
			type: "get",
			dataType: "json",
			error: function(e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"iDisplayLength": 10,
		"order": [[3, "desc"]]
	}).DataTable();
}

function crearBackup() {
	bootbox.confirm({
		title: "Crear Backup",
		message: "¿Está seguro que desea crear un nuevo backup de la base de datos? Este proceso puede tardar varios minutos.",
		buttons: {
			confirm: {
				label: "Sí, crear backup",
				className: 'btn-success'
			},
			cancel: {
				label: "Cancelar",
				className: 'btn-danger'
			}
		},
		callback: function(result) {
					if (result) {
						var procesando = mostrarProcesando("Creando backup de la base de datos...");
						$("#btnCrearBackup").prop("disabled", true);
						
						$.ajax({
							url: '../ajax/backup.php?op=crear',
							type: "GET",
							dataType: "json",
							success: function(data) {
								ocultarProcesando(procesando);
								$("#btnCrearBackup").prop("disabled", false);
								
								if (data.success) {
									var tamañoMB = (data.tamaño / 1024 / 1024).toFixed(2);
									mostrarExito("Backup creado correctamente. Archivo: " + data.archivo + " (" + tamañoMB + " MB)", "Backup Creado");
									setTimeout(function() {
										tabla.ajax.reload();
									}, 1500);
								} else {
									mostrarError("Error al crear el backup: " + (data.error || "Error desconocido"), "Error");
								}
							},
							error: function(xhr, status, error) {
								ocultarProcesando(procesando);
								$("#btnCrearBackup").prop("disabled", false);
								mostrarError("Error al comunicarse con el servidor: " + error, "Error de Conexión");
							}
						});
					}
		}
	});
}

function descargar(idbackup) {
	window.location.href = '../ajax/backup.php?op=descargar&idbackup=' + idbackup;
}

function restaurar(idbackup) {
	bootbox.confirm({
		title: "Restaurar Backup",
		message: "<div class='alert alert-danger'><strong>¡ADVERTENCIA!</strong><br>Esta acción restaurará la base de datos con el backup seleccionado. Todos los datos actuales serán reemplazados. ¿Está completamente seguro?</div>",
		buttons: {
			confirm: {
				label: "Sí, restaurar",
				className: 'btn-danger'
			},
			cancel: {
				label: "Cancelar",
				className: 'btn-default'
			}
		},
		callback: function(result) {
			if (result) {
				$.post('../ajax/backup.php?op=restaurar', {
					idbackup: idbackup
				}, function(data) {
					var r = typeof data === 'string' ? JSON.parse(data) : data;
					if (r.success) {
						mostrarExito("El backup se ha restaurado correctamente. La página se recargará en breve.", "Backup Restaurado");
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						mostrarError("Error al restaurar el backup: " + (r.error || "Error desconocido"), "Error");
					}
				});
			}
		}
	});
}

function eliminar(idbackup) {
	bootbox.confirm({
		title: "Eliminar Backup",
		message: "¿Está seguro que desea eliminar este backup? Esta acción no se puede deshacer.",
		buttons: {
			confirm: {
				label: "Sí, eliminar",
				className: 'btn-danger'
			},
			cancel: {
				label: "Cancelar",
				className: 'btn-default'
			}
		},
		callback: function(result) {
			if (result) {
				$.post('../ajax/backup.php?op=eliminar', {
					idbackup: idbackup
				}, function(data) {
					var r = typeof data === 'string' ? JSON.parse(data) : data;
					if (r.success) {
						mostrarExito(r.msg || "Backup eliminado correctamente", "Éxito");
						setTimeout(function() {
							tabla.ajax.reload();
						}, 1000);
					} else {
						mostrarError(r.msg || "Error al eliminar backup", "Error");
					}
				});
			}
		}
	});
}

init();

