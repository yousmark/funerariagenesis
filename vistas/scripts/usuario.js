var tabla;

//funcion que se ejecuta al inicio
function init(){
   mostrarform(false);
   listar();

   $("#formulario").on("submit",function(e){
   	guardaryeditar(e);
   })

   $("#imagenmuestra").hide();

   // Validar campo nombre (solo letras)
   document.getElementById("nombre").addEventListener("input", function () {
       this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, "");
   });

   // Validar campo celular (solo números)
   document.getElementById("celular").addEventListener("input", function () {
       this.value = this.value.replace(/\D/g, "");
   });

   // Validar campo cédula (solo números)
   document.getElementById("celula").addEventListener("input", function () {
       this.value = this.value.replace(/\D/g, "");
   });
}

//funcion limpiar
function limpiar(){
	$("#nombre").val("");
	$("#celula").val("");
	$("#complemento_ci").val("");
	$("#direccion").val("");
	$("#celular").val("");
	$("#email").val("");
	$("#cargo").val("");
	$("#login").val("");
	$("#clave").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#idusuario").val("");
}

//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}

//funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,
		"aServerSide": true,
		dom: 'Bfrtip',
		buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdf'],
		"ajax":{
			url:'../ajax/usuario.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":5,
		"order":[[0,"desc"]]
	}).DataTable();
}

//funcion para guardaryeditar
function guardaryeditar(e){
     e.preventDefault();
     
     // Validar formulario completo antes de enviar
     if (!validarFormulario($("#formulario")[0])) {
         mostrarError("Por favor, corrija los errores en el formulario antes de continuar", "Error de Validación");
         return false;
     }
     
     // Validaciones adicionales antes de enviar
     let nombre = $("#nombre").val().trim();
     let celular = $("#celular").val().trim();
     let celula = $("#celula").val().trim();
     let email = $("#email").val().trim();
     let clave = $("#clave").val();

     if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
         mostrarError("El campo 'Nombre' solo debe contener letras y espacios", "Error de Validación");
         $("#nombre").focus();
         return false;
     }

     if (!/^\d{8}$/.test(celular)) {
         mostrarError("El campo 'Celular' debe contener exactamente 8 dígitos (ejemplo: 71234567)", "Error de Validación");
         $("#celular").focus();
         return false;
     }
     
     if (!/^\d{6,8}$/.test(celula)) {
         mostrarError("El campo 'Cédula' debe contener entre 6 y 8 dígitos", "Error de Validación");
         $("#celula").focus();
         return false;
     }
     
     // Validar complemento si se proporciona
     var complemento = $("#complemento_ci").val().trim();
     if (complemento !== '' && !/^[A-Za-z]{1}$/.test(complemento)) {
         mostrarError("El campo 'Complemento' debe ser una sola letra (ejemplo: A, B, C)", "Error de Validación");
         $("#complemento_ci").focus();
         return false;
     }
     
     if (email !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
         mostrarError("El email no tiene un formato válido (ejemplo: usuario@dominio.com)", "Error de Validación");
         $("#email").focus();
         return false;
     }
     
     // Validar contraseña solo si se está creando un nuevo usuario o si se está cambiando
     if ($("#idusuario").val() === '' || clave !== '') {
         if (clave.length < 8) {
             mostrarError("La contraseña debe tener al menos 8 caracteres", "Error de Validación");
             $("#clave").focus();
             return false;
         }
     }

     // Mostrar indicador de procesamiento
     var procesando = mostrarProcesando("Guardando usuario...");
     $("#btnGuardar").prop("disabled", true).html("<i class='fa fa-spinner fa-spin'></i> Guardando...");

     var formData = new FormData($("#formulario")[0]);

     $.ajax({
     	url: "../ajax/usuario.php?op=guardaryeditar",
     	type: "POST",
     	data: formData,
     	contentType: false,
     	processData: false,

     	success: function(datos){
     		ocultarProcesando(procesando);
     		$("#btnGuardar").prop("disabled", false).html("<i class='fa fa-save'></i> Guardar");
     		
     		try {
     			var respuesta = typeof datos === 'string' ? JSON.parse(datos) : datos;
     			if (respuesta.error) {
     				mostrarError(respuesta.mensaje || "Error al guardar el usuario", "Error");
     			} else {
     				mostrarExito(respuesta.mensaje || "Usuario guardado correctamente", "Éxito");
     				setTimeout(function() {
     					mostrarform(false);
     					tabla.ajax.reload();
     					limpiar();
     				}, 1000);
     			}
     		} catch(e) {
     			// Si no es JSON, mostrar directamente
     			if (datos.indexOf("correctamente") !== -1 || datos.indexOf("actualizado") !== -1) {
     				mostrarExito(datos, "Éxito");
     				setTimeout(function() {
     					mostrarform(false);
     					tabla.ajax.reload();
     					limpiar();
     				}, 1000);
     			} else {
     				mostrarError(datos, "Error");
     			}
     		}
     	},
     	error: function(xhr, status, error) {
     		ocultarProcesando(procesando);
     		$("#btnGuardar").prop("disabled", false).html("<i class='fa fa-save'></i> Guardar");
     		mostrarError("Error al comunicarse con el servidor: " + error, "Error de Conexión");
     	}
     });

     return false;
}

function mostrar(idusuario){
	$.post("../ajax/usuario.php?op=mostrar",{idusuario : idusuario},
		function(data,status)
		{
			data=JSON.parse(data);
			mostrarform(true);

			$("#nombre").val(data.nombre);
            $("#celula").val(data.celula);
            $("#complemento_ci").val(data.complemento_ci || "");
            $("#celula").selectpicker('refresh');
            $("#direccion").val(data.direccion);
            $("#celular").val(data.celular);
            $("#email").val(data.email);
            $("#cargo").val(data.cargo);
            $("#login").val(data.login);
            $("#clave").val(data.clave);
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src","../files/usuarios/"+data.imagen);
            $("#imagenactual").val(data.imagen);
            $("#idusuario").val(data.idusuario);
		});
}

//funcion para desactivar
function desactivar(idusuario){
	bootbox.confirm("¿Está seguro de desactivar este usuario?", function(result){
		if (result) {
			$.post("../ajax/usuario.php?op=desactivar", {idusuario : idusuario}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(idusuario){
	bootbox.confirm("¿Está seguro de activar este usuario?" , function(result){
		if (result) {
			$.post("../ajax/usuario.php?op=activar", {idusuario : idusuario}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

init();
