$(document).ready(function () {
    $("#registroGroupSend").submit(function (event) {
        event.preventDefault(); // Evita que el formulario se envíe por el método tradicional

        var token = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: "groupSend",
            type: "POST",
            data: {
                name: $("#name").val(),
                comment: $("#comment").val(),

                _token: token,
            },
            success: function (data) {
                console.log("Respuesta del servidor:", data);
                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: "El groupSend ha sido registrado correctamente.",
                }).then(() => {
                    $("#tbGroupSends").DataTable().ajax.reload();
                });
                $("#modalNuevoGroupSend").modal("hide");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error al registrar:", errorThrown);

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

                Swal.fire({
                    icon: "error",
                    title: "Error al registrar",
                    html: errorMessage,
                });
            },
        });
    });

    // Obtener opciones de tipos de groupSend desde la ruta
    $.ajax({
        url: "allTypeUserAndCompanies", // Reemplaza con la ruta correcta en tu aplicación
        type: "GET",
        dataType: "json",
        success: function (response) {
            // Limpiar opciones existentes
            $("#typeuser").empty();
            $("#company").empty();

            // Iterar sobre los tipos de groupSend recibidos
            $.each(response.typeuser, function (index, tipoGroupSend) {
                $("#typeuser").append(
                    $("<option>", {
                        value: tipoGroupSend.id,
                        text: tipoGroupSend.name,
                    })
                );
            });
            $("#typeuser").val(2).trigger("change");
            $("#company").append(
                $("<option>", {
                    value: "",
                    text: "Selecciona una Empresa",
                })
            );

            $.each(response.company, function (index, company) {
                $("#company").append(
                    $("<option>", {
                        value: company.id,
                        text:
                            company.tradeName + " | " + company.documentNumber,
                    })
                );
            });
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar tipos de groupSend:", error);
        },
    });
});

$("#btonNuevo").click(function (e) {
    $("#registroGroupSend")[0].reset();
    $("#modalNuevoGroupSend").modal("show");
});

$(document).on("click", "#cerrarModalGroupSend", function () {
    $("#modalNuevoGroupSend").modal("hide");
});
