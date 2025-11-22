var tabla;

//funcion que se ejecuta al inicio
function init(){
	// Verificar que jQuery esté disponible
	if (typeof jQuery === 'undefined') {
		console.error('jQuery no está disponible');
		alert('Error: jQuery no está cargado. Por favor, recarga la página.');
		return;
	}
	
	// Esperar a que el DOM esté listo
	$(document).ready(function() {
		console.log('Inicializando módulo de permisos...');
		listar();
	});
}

//funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,
		"aServerSide": true,
		dom: 'Bfrtip',
		buttons: [
			'copyHtml5',
			'excelHtml5',
			'csvHtml5',
			'pdf'
		],
		"ajax":
		{
			url:'../ajax/permiso.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":10,
		"order":[[1,"asc"]],
		"drawCallback": function(settings) {
			// Esta función se ejecuta cada vez que se redibuja la tabla
			// Asegurarnos de que los eventos estén enlazados
		}
	}).DataTable();
	
	// Usar evento delegado para manejar clics en botones generados dinámicamente
	$(document).off('click', '.btn-gestionar-permisos').on('click', '.btn-gestionar-permisos', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		var idusuario = $(this).data('idusuario');
		var nombreUsuario = $(this).data('nombre');
		
		console.log('Botón clickeado - ID Usuario:', idusuario, 'Nombre:', nombreUsuario);
		
		if (!idusuario) {
			console.error('ID de usuario no encontrado');
			mostrarError('No se pudo obtener el ID del usuario', 'Error');
			return false;
		}
		
		if (!nombreUsuario) {
			console.error('Nombre de usuario no encontrado');
			mostrarError('No se pudo obtener el nombre del usuario', 'Error');
			return false;
		}
		
		// Verificar que la función existe
		if (typeof gestionarPermisos === 'function') {
			gestionarPermisos(idusuario, nombreUsuario);
		} else {
			console.error('La función gestionarPermisos no está definida');
			mostrarError('La función de gestión no está disponible. Por favor, recarga la página.', 'Error del Sistema');
		}
		
		return false;
	});
}

//funcion para gestionar permisos de un usuario
function gestionarPermisos(idusuario, nombreUsuario) {
	console.log('gestionarPermisos llamada con:', idusuario, nombreUsuario);
	
	if (!idusuario || !nombreUsuario) {
		console.error('Datos incompletos:', {idusuario: idusuario, nombreUsuario: nombreUsuario});
		mostrarError('Datos del usuario incompletos', 'Error de Validación');
		return;
	}
	
	console.log('Mostrando indicador de carga...');
	// Mostrar indicador de carga
	var procesando = mostrarProcesando('Cargando permisos...');
	
	console.log('Haciendo petición AJAX para listar todos los permisos...');
	// Cargar permisos disponibles y permisos del usuario
	$.ajax({
		url: '../ajax/permiso.php?op=listarTodosPermisos',
		type: 'GET',
		dataType: 'json',
		success: function(todosPermisos){
			console.log('Permisos recibidos:', todosPermisos);
			if (!todosPermisos || todosPermisos.length === 0) {
				console.error('No se recibieron permisos');
				$('.bootbox').modal('hide');
				ocultarProcesando(procesando);
				mostrarError('No se pudieron cargar los permisos disponibles', 'Error de Datos');
				return;
			}
			
			console.log('Haciendo petición AJAX para permisos del usuario:', idusuario);
			$.ajax({
				url: '../ajax/permiso.php?op=listarPermisosUsuario',
				type: 'POST',
				data: {idusuario: idusuario},
				dataType: 'json',
				success: function(permisosUsuario){
					console.log('Permisos del usuario recibidos:', permisosUsuario);
					$('.bootbox').modal('hide');
					
					if (!permisosUsuario) {
						permisosUsuario = [];
					}
					
					// Crear array de IDs de permisos que tiene el usuario
					var permisosUsuarioIds = [];
					permisosUsuario.forEach(function(p){
						permisosUsuarioIds.push(parseInt(p.idpermiso));
					});
					
					// Construir el HTML del modal con checkboxes
					var html = '<div class="row">';
					html += '<div class="col-md-12">';
					html += '<p><strong>Usuario:</strong> ' + nombreUsuario + '</p>';
					html += '<hr>';
					html += '<div class="form-group">';
					html += '<label>Seleccione los permisos para este usuario:</label>';
					html += '<div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">';
					
					// Agrupar permisos en columnas (3 columnas)
					var totalPermisos = todosPermisos.length;
					var permisosPorColumna = Math.ceil(totalPermisos / 3);
					
					html += '<div class="row">';
					for (var i = 0; i < 3; i++) {
						html += '<div class="col-md-4">';
						for (var j = i * permisosPorColumna; j < Math.min((i + 1) * permisosPorColumna, totalPermisos); j++) {
							var permiso = todosPermisos[j];
							var checked = permisosUsuarioIds.indexOf(parseInt(permiso.idpermiso)) !== -1 ? 'checked' : '';
							html += '<div class="checkbox">';
							html += '<label>';
							html += '<input type="checkbox" name="permisos[]" value="' + permiso.idpermiso + '" ' + checked + '> ';
							html += permiso.nombre;
							html += '</label>';
							html += '</div>';
						}
						html += '</div>';
					}
					html += '</div>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					
					bootbox.dialog({
						title: 'Gestionar Permisos: ' + nombreUsuario,
						message: html,
						size: 'large',
						buttons: {
							guardar: {
								label: '<i class="fa fa-save"></i> Guardar Cambios',
								className: 'btn-success',
								callback: function(){
									guardarPermisos(idusuario);
									return false; // Mantener el modal abierto hasta que se guarde
								}
							},
							cancelar: {
								label: 'Cancelar',
								className: 'btn-default'
							}
						}
					});
				},
				error: function(xhr, status, error) {
					$('.bootbox').modal('hide');
					console.error('Error al cargar permisos del usuario:', xhr.responseText);
					bootbox.alert('Error al cargar los permisos del usuario. Por favor, intente nuevamente.');
				}
			});
		},
		error: function(xhr, status, error) {
			$('.bootbox').modal('hide');
			console.error('Error al cargar todos los permisos:', xhr.responseText);
			bootbox.alert('Error al cargar los permisos disponibles. Por favor, intente nuevamente.');
		}
	});
}

//funcion para guardar los permisos seleccionados
function guardarPermisos(idusuario) {
	var permisos = [];
	$('.bootbox input[name="permisos[]"]:checked').each(function(){
		permisos.push($(this).val());
	});
	
	// Construir los datos como FormData para enviar correctamente el array
	var formData = new FormData();
	formData.append('idusuario', idusuario);
	permisos.forEach(function(permiso, index) {
		formData.append('permisos[]', permiso);
	});
	
	// Mostrar indicador de carga
	var currentModal = $('.bootbox').last();
	if (currentModal.length) {
		currentModal.find('.modal-footer').html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
	}
	
	$.ajax({
		url: '../ajax/permiso.php?op=actualizarPermisos',
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function(respuesta){
			// Cerrar todos los modales de bootbox
			$('.bootbox').modal('hide');
			$('body').removeClass('modal-open');
			$('.modal-backdrop').remove();
			
			if (respuesta && respuesta.ok) {
				// Mostrar mensaje de éxito
				mostrarExito(respuesta.msg || 'Permisos actualizados correctamente', 'Éxito');
				setTimeout(function() {
					tabla.ajax.reload();
				}, 1000);
			} else {
				mostrarError((respuesta ? respuesta.msg : 'No se pudo guardar los permisos'), 'Error');
			}
		},
		error: function(xhr, status, error) {
			$('.bootbox').modal('hide');
			$('body').removeClass('modal-open');
			$('.modal-backdrop').remove();
			
			console.error('Error en AJAX:', status, error, xhr.responseText);
			var errorMsg = 'Error al guardar los permisos. Por favor, intente nuevamente.';
			try {
				var errorResponse = JSON.parse(xhr.responseText);
				if (errorResponse && errorResponse.msg) {
					errorMsg = errorResponse.msg;
				}
			} catch(e) {
				// Si no se puede parsear, usar el mensaje por defecto
			}
			mostrarError(errorMsg, 'Error de Conexión');
		}
	});
}

// Ejecutar init cuando el DOM esté listo
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', init);
} else {
	// DOM ya está listo
	init();
}