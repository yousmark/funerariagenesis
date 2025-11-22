var tabla;

//Función que se ejecuta al inicio
function init(){
    mostrarform(false);
    listar();
    
    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    })
    
    //Cargar materias
   //Cargamos los items al select materia
$.post("../ajax/curso.php?op=selectMateria", function(r){
    $("#idmateria").html(r);
    $("#idmateria").selectpicker('refresh');
});

//Cargamos los items al select docente
$.post("../ajax/curso.php?op=selectDocente", function(r){
    $("#iddocente").html(r);
    $("#iddocente").selectpicker('refresh');
});

}

//Función limpiar
function limpiar()
{
    $("#nombre").val("");
    $("#idcurso").val("");
    $("#idmateria").val("");
    $("#iddocente").val("");
    $("#idmateria").selectpicker('refresh');
    $("#iddocente").selectpicker('refresh');
}

//Función mostrar formulario
function mostrarform(flag)
{
    limpiar();
    if (flag)
    {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled",false);
        $("#btnagregar").hide();
    }
    else
    {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//Función cancelarform
function cancelarform()
{
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar()
{
    tabla=$('#tbllistado').dataTable(
    {
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
            url: '../ajax/curso.php?op=listar',
            type : "get",
            dataType : "json",
            error: function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[ 0, "desc" ]]
    }).DataTable();
}

//Función para guardar o editar
function guardaryeditar(e)
{
    e.preventDefault();
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/curso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos)
        {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
    limpiar();
}

function mostrar(idcurso)
{
    $.post("../ajax/curso.php?op=mostrar",{idcurso : idcurso}, function(data, status)
    {
        data = JSON.parse(data);
        mostrarform(true);

      $("#idmateria").val(data.idmateria);
$("#idmateria").selectpicker('refresh');

$("#iddocente").val(data.iddocente);
$("#iddocente").selectpicker('refresh');

$("#nombre").val(data.nombre);
$("#idcurso").val(data.idcurso);

    })
}

//Función para desactivar registros
function desactivar(idcurso)
{
    bootbox.confirm("¿Está Seguro de desactivar el curso?", function(result){
        if(result)
        {
            $.post("../ajax/curso.php?op=desactivar", {idcurso : idcurso}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

//Función para activar registros
function activar(idcurso)
{
    bootbox.confirm("¿Está Seguro de activar el Curso?", function(result){
        if(result)
        {
            $.post("../ajax/curso.php?op=activar", {idcurso : idcurso}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

init();
