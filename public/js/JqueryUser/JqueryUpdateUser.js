// Evento submit del formulario de edición
// Evento submit del formulario de edición
$("#registroUsuarioE").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    // Obtener el token CSRF
    var token = $('meta[name="csrf-token"]').attr("content");

    // Obtener los datos del formulario
    var formData = {
        password: $("#passE").val(),
        id: $("#idE").val(),
        username: $("#usernameE").val(),
        typeofUser_id: $("#typeuserE").val(),
        company_id: $("#companyE").val(),
        _token: token,
    };

    $.ajax({
        url: "user/" + formData.id,
        type: "PUT",
        data: formData,
        headers: {
            "X-CSRF-TOKEN": token,
        },
        success: function (response) {
            // Cerrar el modal de edición
            $("#modalNuevoUsuarioE").modal("hide");

            // Recargar la tabla de usuarios
            // $("#tbUsuarios").DataTable().ajax.reload();
            $("#tbUsuarios").DataTable().ajax.reload(null, false);
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: "success",
                title: "Actualización exitosa",
                text: "El usuario ha sido actualizado correctamente.",
            });
            $("#passE").val("");
            $("#modalEditarUsuario").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error al actualizar tipo de usuario:", errorThrown);

            // Extraer errores del servidor
            var errors = jqXHR.responseJSON;
            var errorMessage = "<ul>";

            // Si el error es un mensaje de texto simple
            if (errors.error) {
                errorMessage += "<li>" + errors.error + "</li>";
            } else if (errors.errors) {
                // Si el error es un array de mensajes
                $.each(errors.errors, function (key, value) {
                    errorMessage += "<li>" + value[0] + "</li>"; // Asume que los errores son arrays
                });
            }

            errorMessage += "</ul>";

            // Mostrar mensaje de error
            Swal.fire({
                icon: "error",
                title: "Error al actualizar",
                html: errorMessage,
            });
        },
    });
});
$(document).on("click", "#cerrarModalEditar", function () {
    $("#modalEditarUsuario").modal("hide");
});
