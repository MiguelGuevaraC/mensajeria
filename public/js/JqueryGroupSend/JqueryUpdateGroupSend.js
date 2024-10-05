// Evento submit del formulario de edición
// Evento submit del formulario de edición
$("#registroGroupSendE").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    // Obtener el token CSRF
    var token = $('meta[name="csrf-token"]').attr("content");

    // Obtener los datos del formulario
    var formData = {
        name: $("#nameE").val(),
        id: $("#idE").val(),
        comment: $("#commentE").val(),

        _token: token,
    };

    $.ajax({
        url: "groupSend/" + formData.id,
        type: "PUT",
        data: formData,
        headers: {
            "X-CSRF-TOKEN": token,
        },
        success: function (response) {
            // Cerrar el modal de edición
            $("#modalNuevoGroupSendE").modal("hide");

            // Mostrar mensaje de éxito
            Swal.fire({
                icon: "success",
                title: "Actualización exitosa",
                text: "El groupSend ha sido actualizado correctamente.",
            }).then(() => {
                $("#tbGroupSends").DataTable().ajax.reload(null, false);

            });
            $("#passE").val("");
            $("#modalEditarGroupSend").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error(
                "Error al actualizar tipo de groupSend:",
                errorThrown
            );

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
    $("#modalEditarGroupSend").modal("hide");
});
