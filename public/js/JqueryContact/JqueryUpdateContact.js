$("#updateContactForm").on("submit", function (e) {
    e.preventDefault(); // Prevenir el comportamiento por defecto del formulario

    $.ajax({
        url: `updateContact/${$("#idEContact").val()}`, // Cambia a la URL correcta
        method: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF
        },
        data: $(this).serialize(), // Serializar el formulario
        success: function () {
            Swal.fire(
                "Actualizado",
                "Contacto actualizado con éxito.",
                "success"
            );
            // $("#tbContacts").DataTable().ajax.reload(); // Recargar la tabla
            $("#tbContacts").DataTable().ajax.reload(null, false);
            $("#modalUpdateContact").modal("hide"); // Ocultar el modal
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al registrar empresa:", errorThrown);
    
            let errorMessage = "Error al registrar empresa";
    
            // Verificar si el error es 422 (Unprocessable Entity)
            if (jqXHR.status === 422) {
                const errors = jqXHR.responseJSON.errors;
                errorMessage = "<ul>";
    
                // Iterar sobre los errores y construir el mensaje
                $.each(errors, function (field, messages) {
                    $.each(messages, function (index, message) {
                        errorMessage += "<li>" + message + "</li>";
                    });
                });
    
                errorMessage += "</ul>";
            } else {
                // Mensaje genérico para otros errores
                errorMessage += ": " + errorThrown;
            }
    
            // Mostrar notificación de error
            Swal.fire({
                title: "Error",
                icon: "error",
                html: errorMessage, // Usar `html` para permitir etiquetas HTML
                confirmButtonText: "Aceptar",
            });
        },
    });
});