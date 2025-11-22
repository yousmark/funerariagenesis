$("#frmAcceso").on('submit', function(e) {
    e.preventDefault();

    var logina = $("#logina").val().trim();
    var clavea = $("#clavea").val().trim();

    if (logina === "" || clavea === "") {
        bootbox.dialog({
            title: "Campos Vacíos",
            message: "<p class='text-center'>Por favor, complete todos los campos.</p>",
            buttons: {
                ok: {
                    label: "Aceptar",
                    className: 'btn-primary'
                }
            }
        });
        return;
    }

    $.post("../ajax/usuario.php?op=verificar", {
        logina: logina,
        clavea: clavea
    }, function(data) {
        try {
            // Parsear la respuesta si es string
            var response = typeof data === 'string' ? JSON.parse(data) : data;
            
            // Verificar si hay error
            if (response.error) {
                var mensaje = response.mensaje || "Error de autenticación";
                if (response.password_expirada) {
                    mostrarAdvertencia(mensaje, "Contraseña Expirada");
                    setTimeout(function() {
                        window.location.href = "cambiar_password.php";
                    }, 2000);
                } else {
                    mostrarError(mensaje, "Error de Autenticación");
                }
            } else if (response && response.idusuario && response.idusuario > 0) {
                // Redirigir directamente al dashboard sin mostrar modal
                window.location.replace("dashboard.php");
            } else {
                mostrarError("Usuario y/o contraseña incorrectos", "Error de Autenticación");
            }
        } catch (e) {
            // Si hay error parseando, mostrar mensaje
            mostrarError("Error al procesar la respuesta del servidor: " + e.message, "Error del Sistema");
        }
    }, "json").fail(function(xhr, status, error) {
        mostrarError("Error al conectar con el servidor. Por favor, intente nuevamente. (" + error + ")", "Error de Conexión");
    });
});
