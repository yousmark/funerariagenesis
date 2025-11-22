var tabla;


function init(){
    mostrarform(false);
    listar();

    $("#formulario").on("submit",function(e)
    {
        guardaryeditar(e);
    });
    //$('#mAlmacen').addClass("treeview active");
    //$('#lmaterias').addClass("active");
}


function limpiar()
{
    $("#iddocente").val("");
    $("#nombre").val("");
    $("#apellido").val("");
    $("#cedula").val("");
    $("#direccion").val("");
    $("#celular").val("");
    $("#correo").val("");
    $("#nivel_est").val("");
}


function mostrarform(flag)
{
    limpiar();
    if(flag)
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
function cancelarform()
{
    limpiar();
    mostrarform(false);
} 

function listar()
{
    tabla=$('#tbllistado').dataTable(
    {
        //"lengthMenu": [ 5, 10, 25, 75, 100],
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
            url: '../ajax/docente.php?op=listar',
            type: "get",
            dataType : "json",
            error:function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy":true,
        "iDisplayLength":5,
        "order": [[0,"desc"]]
    }).DataTable();
}

function guardaryeditar(e)
{
    e.preventDefault();
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/docente.php?op=guardaryeditar",
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

function mostrar(iddocente)
{
    $.post("../ajax/docente.php?op=mostrar",{iddocente : iddocente}, function(data,status)
    {
        data = JSON.parse(data);
        mostrarform(true);

        $("#nombre").val(data.nombre);
        $("#apellido").val(data.apellido);
        $("#cedula").val(data.cedula);
        $("#direccion").val(data.direccion);
        $("#celular").val(data.celular);
        $("#correo").val(data.correo);
        $("#nivel_est").val(data.nivel_est);
        $("#iddocente").val(data.iddocente);
    })
}

function desactivar(iddocente)
{
    bootbox.confirm("¿Está Seguro de desactivar el Docente?", function(result){
        if(result)
        {
            $.post("../ajax/docente.php?op=desactivar",{iddocente: iddocente},function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}
function activar(iddocente)
{
    bootbox.confirm("¿Está Seguro de activar el Docente?", function(result){
        if(result)
        {
            $.post("../ajax/docente.php?op=activar", {iddocente : iddocente}, function(e){
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    })
}

init();