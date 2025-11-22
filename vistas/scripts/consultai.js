var tabla;

//Función que se ejecuta al inicio
function init(){
    listar();
    
    // Cargar datos en los select
    cargarSelects();
    
    $("#formulario").on("submit",function(e){
        buscar(e);
    });
}

// Función para cargar los selects
function cargarSelects(){
    // Cargar estudiantes
    $.ajax({
        url: "../ajax/notas.php?op=selectEstudiante",
        type: "GET",
        success: function(r){
            $("#estudiante_id").html(r);
            $("#estudiante_id").selectpicker('refresh');
        },
        error: function(){
            console.log("Error al cargar estudiantes");
        }
    });

    // Cargar cursos
    $.ajax({
        url: "../ajax/notas.php?op=selectCurso",
        type: "GET",
        success: function(r){
            $("#curso_id").html(r);
            $("#curso_id").selectpicker('refresh');
        },
        error: function(){
            console.log("Error al cargar cursos");
        }
    });

    // Cargar carreras
    $.ajax({
        url: "../ajax/notas.php?op=selectCarrera",
        type: "GET",
        success: function(r){
            $("#carrera_id").html(r);
            $("#carrera_id").selectpicker('refresh');
        },
        error: function(){
            console.log("Error al cargar carreras");
        }
    });
}

//Función listar
function listar()
{
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
            url: '../ajax/notas.php?op=consultaInscripciones',
            type: "GET",
            dataType: "json",
            error: function(xhr, error, thrown){
                console.log("Error en AJAX:", error);
                console.log("Detalles:", thrown);
            }
        },
        "columns": [
            { "data": "0", "title": "Estudiante" },
            { "data": "1", "title": "Carrera" },
            { "data": "2", "title": "Grado" },
            { "data": "3", "title": "Curso" },
            { "data": "4", "title": "Turno" },
            { "data": "5", "title": "Aula" },
            { "data": "6", "title": "Fecha Inscripción" },
            { "data": "7", "title": "Registrado por" }
        ],
        "destroy": true,
        "pageLength": 10,
        "order": [[ 6, "desc" ]],
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

//Función para buscar
function buscar(e)
{
    e.preventDefault();
    var estudiante_id = $("#estudiante_id").val();
    var curso_id = $("#curso_id").val();
    var carrera_id = $("#carrera_id").val();
    
    // Destruir la tabla existente
    if ($.fn.DataTable.isDataTable('#tbllistado')) {
        tabla.destroy();
    }
    
    // Crear nueva tabla con filtros
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
            url: '../ajax/notas.php?op=consultaInscripciones&idestudiante='+estudiante_id+'&idcurso='+curso_id+'&idcarrera='+carrera_id,
            type: "GET",
            dataType: "json",
            error: function(xhr, error, thrown){
                console.log("Error en AJAX:", error);
                console.log("Detalles:", thrown);
            }
        },
        "columns": [
            { "data": "0", "title": "Estudiante" },
            { "data": "1", "title": "Carrera" },
            { "data": "2", "title": "Grado" },
            { "data": "3", "title": "Curso" },
            { "data": "4", "title": "Turno" },
            { "data": "5", "title": "Aula" },
            { "data": "6", "title": "Fecha Inscripción" },
            { "data": "7", "title": "Registrado por" }
        ],
        "destroy": true,
        "pageLength": 10,
        "order": [[ 6, "desc" ]],
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

// Inicializar cuando el documento esté listo
$(document).ready(function(){
    init();
});