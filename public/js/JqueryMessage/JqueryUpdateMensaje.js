$(document).ready(function() {
    $('#btonSaveMessageE').on('click', function() {
        var form = $('#registroMensajeNuevoE')[0];
        var formData = new FormData(form);
        var id = $('#idE').val();
        var token = $('meta[name="csrf-token"]').attr("content");

        formData.append("title", $("#titleE").val());
        formData.append("block1", $("#block1E").val());
        formData.append("block2", $("#block2E").val());
        formData.append("block3", $("#block3E").val());
        formData.append("block4", $("#block4E").val());
        
      
        var fileInput = $('#fileUploadE')[0]; // Seleccionar el elemento DOM
        if(fileInput.files.length > 0) {
            formData.append("fileUpload", fileInput.files[0]); // Añadir el archivo al formData
        }

        $.ajax({
            url: 'message/' + id, // La URL debe coincidir con la ruta definida en Laravel
            type: 'POST',
            data: formData,
            headers: {
                "X-CSRF-TOKEN": token, // Incluir el token CSRF en el encabezado de la solicitud
            },
            contentType: false,
            processData: false,
            success: function(response) {
                $("#modalNuevoMensajeE").modal("hide");
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'El mensaje se ha actualizado correctamente.',
                }).then(() => {
                    $("#tbMensajes").DataTable().ajax.reload(); // Recargar la tabla tras el éxito
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // Manejo de errores de validación
                    var errors = xhr.responseJSON.error;

                    Swal.fire({
                        icon: "error",
                        title: "Error Validación",
                        text: errors,
                        confirmButtonText: "Aceptar",
                    });
                } else {
                    // Otros errores
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema al guardar el mensaje.",
                        confirmButtonText: "Aceptar",
                    });
                }
            },
        });
        
    });
});
