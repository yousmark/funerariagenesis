var tabla;

//Función que se ejecuta al inicio
function init(){
    mostrarform(false);
    listar();
    cargarInscripciones();

    $("#formulario").on("submit",function(e){
        guardaryeditar(e);
    });
}

// Función para cargar inscripciones
function cargarInscripciones(){
    $.ajax({
        url: "../ajax/notas.php?op=selectInscripcion",
        type: "GET",
        success: function(r){
            $("#inscripcion_id").html(r);
            $('#inscripcion_id').selectpicker('refresh');
        },
        error: function(){
            console.log("Error al cargar inscripciones");
            $("#inscripcion_id").html('<option value="">Error al cargar</option>');
            $('#inscripcion_id').selectpicker('refresh');
        }
    });
}

//Función limpiar
function limpiar(){
    $("#nota_id").val("");
    $("#inscripcion_id").val("").selectpicker("refresh");
    $("#nota1").val("");
    $("#nota2").val("");
    $("#nota3").val("");
    $("#observaciones").val("");
}

//Función mostrar formulario
function mostrarform(flag){
    limpiar();
    if (flag){
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
        
        // Recargar las inscripciones cada vez que se muestra el formulario
        cargarInscripciones();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//Función cancelarform
function cancelarform(){
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar(){
    tabla = $('#tbllistado').DataTable({
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
        "buttons": [
            {
                extend: 'copy',
                text: 'Copiar',
                className: 'btn btn-default'
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-default'
            },
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-default'
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-default'
            }
        ],
        "ajax": {
            url: '../ajax/notas.php?op=listar',
            type: "GET",
            dataType: "json",
            error: function(xhr, error, thrown){
                console.log("Error en AJAX:", error);
                console.log("Detalles:", thrown);
            }
        },
        "columns": [
            { "data": "0" },
            { "data": "1" },
            { "data": "2" },
            { "data": "3" },
            { "data": "4" },
            { "data": "5" },
            { "data": "6" },
            { "data": "7" },
            { "data": "8" },
            { "data": "9" }
        ],
        "destroy": true,
        "pageLength": 5,
        "order": [[ 0, "desc" ]],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
}

//Función para guardar o editar
function guardaryeditar(e){
    e.preventDefault();
    
    // Validar que se haya seleccionado una inscripción
    if($("#inscripcion_id").val() == ""){
        bootbox.alert("Debe seleccionar una inscripción");
        return false;
    }
    
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/notas.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
            $("#btnGuardar").prop("disabled",false);
        },
        error: function(){
            bootbox.alert("Error al procesar la solicitud");
            $("#btnGuardar").prop("disabled",false);
        }
    });
}

function mostrar(nota_id){
    $.ajax({
        url: "../ajax/notas.php?op=mostrar",
        type: "POST",
        data: {nota_id: nota_id},
        success: function(data){
            if(data && data != 'null'){
                try {
                    var dataObj = JSON.parse(data);
                    mostrarform(true);
                    $("#nota_id").val(dataObj.idnota);
                    $("#inscripcion_id").val(dataObj.idinscripcion).selectpicker("refresh");
                    $("#nota1").val(dataObj.nota1);
                    $("#nota2").val(dataObj.nota2);
                    $("#nota3").val(dataObj.nota3);
                    $("#observaciones").val(dataObj.observaciones);
                } catch(e) {
                    console.log("Error parsing JSON:", e);
                    bootbox.alert("Error al cargar los datos");
                }
            } else {
                bootbox.alert("No se pudieron cargar los datos");
            }
        },
        error: function(){
            bootbox.alert("Error al cargar los datos");
        }
    });
}

//Función para desactivar registros
function desactivar(nota_id){
    bootbox.confirm("¿Está seguro de desactivar la Nota?", function(result){
        if(result){
            $.post("../ajax/notas.php?op=desactivar", {nota_id : nota_id}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

//Función para activar registros
function activar(nota_id){
    bootbox.confirm("¿Está seguro de activar la Nota?", function(result){
        if(result){
            $.post("../ajax/notas.php?op=activar", {nota_id : nota_id}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

// Inicializar cuando el documento esté listo
$(document).ready(function(){
    init();
});