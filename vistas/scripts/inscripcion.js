var tabla;

function init() {
    mostrarform(false);
    listar();

    // Guardar y editar
    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });

    // cargar selects dinámicos
    $.post("../ajax/inscripcion.php?op=selectEstudiante", function(r) {
        $("#estudiante_id").html(r);
        $("#estudiante_id").selectpicker('refresh');
    });

    $.post("../ajax/inscripcion.php?op=selectCarrera", function(r) {
        $("#carrera_id").html(r);
        $("#carrera_id").selectpicker('refresh');
    });

    $.post("../ajax/inscripcion.php?op=selectGrado", function(r) {
        $("#grado_id").html(r);
        $("#grado_id").selectpicker('refresh');
    });

    $.post("../ajax/inscripcion.php?op=selectCurso", function(r) {
        $("#curso_id").html(r);
        $("#curso_id").selectpicker('refresh');
    });

    $.post("../ajax/inscripcion.php?op=selectTurno", function(r) {
        $("#turno_id").html(r);
        $("#turno_id").selectpicker('refresh');
    });

    $.post("../ajax/inscripcion.php?op=selectAula", function(r) {
        $("#aula_id").html(r);
        $("#aula_id").selectpicker('refresh');
    });
}

// limpiar formulario
function limpiar() {
    $("#inscripcion_id").val("");
    $("#estudiante_id").val("").selectpicker('refresh');
    $("#carrera_id").val("").selectpicker('refresh');
    $("#grado_id").val("").selectpicker('refresh');
    $("#curso_id").val("").selectpicker('refresh');
    $("#turno_id").val("").selectpicker('refresh');
    $("#aula_id").val("").selectpicker('refresh');
}

// mostrar u ocultar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnGuardar").html('<i class="fa fa-save"></i> Guardar');
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
    }
}

function cancelarform() {
    limpiar();
    mostrarform(false);
}

// listar registros
function listar() {
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "ajax": {
            url: '../ajax/inscripcion.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    }).DataTable();
}

// guardar o editar
function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/inscripcion.php?op=guardaryeditar",
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

// mostrar datos en formulario (editar)
function mostrar(idinscripcion) {
    $.post("../ajax/inscripcion.php?op=mostrar", {inscripcion_id: idinscripcion}, function(data, status) {
        data = JSON.parse(data);
        mostrarform(true);

        $("#inscripcion_id").val(data.idinscripcion);
        $("#estudiante_id").val(data.idestudiante).selectpicker('refresh');
        $("#carrera_id").val(data.idcarrera).selectpicker('refresh');
        $("#grado_id").val(data.idgrado).selectpicker('refresh');
        $("#curso_id").val(data.idcurso).selectpicker('refresh');
        $("#turno_id").val(data.idturno).selectpicker('refresh');
        $("#aula_id").val(data.idaula).selectpicker('refresh');

        // cambiar botón a Actualizar
        $("#btnGuardar").html('<i class="fa fa-save"></i> Actualizar');
    });
}

// desactivar
function desactivar(idinscripcion) {
    bootbox.confirm("¿Está seguro de desactivar la inscripción?", function(result) {
        if (result) {
            $.post("../ajax/inscripcion.php?op=desactivar", {inscripcion_id: idinscripcion}, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

// activar
function activar(idinscripcion) {
    bootbox.confirm("¿Está seguro de activar la inscripción?", function(result) {
        if (result) {
            $.post("../ajax/inscripcion.php?op=activar", {inscripcion_id: idinscripcion}, function(e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

init();
