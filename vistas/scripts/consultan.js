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
            $("#estudiante_id").html('<option value="">Error al cargar</option>');
            $("#estudiante_id").selectpicker('refresh');
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
            $("#curso_id").html('<option value="">Error al cargar</option>');
            $("#curso_id").selectpicker('refresh');
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
            url: '../ajax/notas.php?op=consultaNotas',
            type: "GET",
            dataType: "json",
            error: function(xhr, error, thrown){
                console.log("Error en AJAX:", error);
                console.log("Detalles:", thrown);
                // Mostrar tabla vacía en caso de error
                var emptyData = {
                    "sEcho": 1,
                    "iTotalRecords": 0,
                    "iTotalDisplayRecords": 0,
                    "aaData": []
                };
                return emptyData;
            }
        },
        "columns": [
            { "data": "0", "title": "Estudiante" },
            { "data": "1", "title": "Curso" },
            { "data": "2", "title": "Materia" },
            { "data": "3", "title": "Nota 1" },
            { "data": "4", "title": "Nota 2" },
            { "data": "5", "title": "Nota 3" },
            { "data": "6", "title": "Promedio" },
            { "data": "7", "title": "Estado" }
        ],
        "destroy": true,
        "pageLength": 10,
        "order": [[ 0, "asc" ]],
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
            url: '../ajax/notas.php?op=consultaNotas&idestudiante='+estudiante_id+'&idcurso='+curso_id,
            type: "GET",
            dataType: "json",
            error: function(xhr, error, thrown){
                console.log("Error en AJAX:", error);
                console.log("Detalles:", thrown);
                // Mostrar tabla vacía en caso de error
                var emptyData = {
                    "sEcho": 1,
                    "iTotalRecords": 0,
                    "iTotalDisplayRecords": 0,
                    "aaData": []
                };
                return emptyData;
            }
        },
        "columns": [
            { "data": "0", "title": "Estudiante" },
            { "data": "1", "title": "Curso" },
            { "data": "2", "title": "Materia" },
            { "data": "3", "title": "Nota 1" },
            { "data": "4", "title": "Nota 2" },
            { "data": "5", "title": "Nota 3" },
            { "data": "6", "title": "Promedio" },
            { "data": "7", "title": "Estado" }
        ],
        "destroy": true,
        "pageLength": 10,
        "order": [[ 0, "asc" ]],
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

// Función para limpiar filtros
function limpiarFiltros(){
    $("#estudiante_id").val("").selectpicker("refresh");
    $("#curso_id").val("").selectpicker("refresh");
    
    // Recargar la tabla sin filtros
    if ($.fn.DataTable.isDataTable('#tbllistado')) {
        tabla.destroy();
    }
    listar();
}

// Inicializar cuando el documento esté listo
$(document).ready(function(){
    init();
});