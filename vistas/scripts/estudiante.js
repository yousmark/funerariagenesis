var tabla;

function init() {
    mostrarform(false);
    listar();
    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });
    // Formato para fecha de nacimiento
    $('#fecha_nac').inputmask('yyyy-mm-dd');
}

function limpiar() {
    $("#nombre").val("");
    $("#apellido").val("");
    $("#edad").val("");
    $("#cedula").val("");
    $("#direccion").val("");
    $("#celular").val("");
    $("#correo").val("");
    $("#fecha_nac").val("");
    $("#idestudiante").val("");
}

function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

function cancelarform() {
    limpiar();
    mostrarform(false);
}

function listar() {
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "dom": 'Bfrtip',
        "buttons": ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdf'],
        "ajax": {
            url: '../ajax/estudiante.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[0, "desc"]]
    }).DataTable();
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/estudiante.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
    limpiar();
}

function mostrar(idestudiante) {
    $.post("../ajax/estudiante.php?op=mostrar", {idestudiante: idestudiante}, function(data) {
        data = JSON.parse(data);
        mostrarform(true);
        $("#nombre").val(data.nombre);
        $("#apellido").val(data.apellido);
        $("#edad").val(data.edad);
        $("#cedula").val(data.cedula);
        $("#direccion").val(data.direccion);
        $("#celular").val(data.celular);
        $("#correo").val(data.correo);
        $("#fecha_nac").val(data.fecha_nac);
        $("#idestudiante").val(data.idestudiante);
    });
}

function desactivar(idestudiante) {
    bootbox.confirm("¿Está seguro de desactivar al estudiante?", function(result) {
        if (result) {
            $.post("../ajax/estudiante.php?op=desactivar", {idestudiante: idestudiante}, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function activar(idestudiante) {
    bootbox.confirm("¿Está seguro de activar al estudiante?", function(result) {
        if (result) {
            $.post("../ajax/estudiante.php?op=activar", {idestudiante: idestudiante}, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

init();