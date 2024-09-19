
$("#btonNuevoGroup").click(function (e) {
    $("#modalNuevoGroupSend").modal("show");
    $("#modalNuevoContact").modal("hide");
});
$("#modalNuevoGroupSend").on("hidden.bs.modal", function (e) {
    $("#modalNuevoContact").modal("show");
});

$(document).on("click", "#cerrarModalGroupSend", function () {
    $("#modalNuevoGroupSend").modal("hide");
});

$(document).ready(function () {
    // Cargar los grupos al cargar la página
    $.ajax({
        url: "allGroups", // Reemplaza con la URL de tu API
        type: "GET",
        dataType: "json",
        success: function (response) {
            $("#groupsend").empty();
            $("#groupsend").append(
                $("<option>", {
                    value: "",
                    text: "Selecciona un grupo",
                    disabled: true,
                    selected: true,
                })
            );

            // Iterar sobre los datos recibidos y añadir opciones al select
            $.each(response.groupSends, function (index, group) {
                $("#groupsend").append(
                    $("<option>", {
                        value: group.id,
                        text: group.name,
                    })
                );
            });
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los grupos:", error);
        },
    });

    // Manejo del formulario para registrar un nuevo grupo
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

                // Añadir el nuevo grupo al select y seleccionarlo
                $("#groupsend").append(
                    $("<option>", {
                        value: data.id, // Asumiendo que la respuesta contiene el ID del nuevo grupo
                        text: data.name, // Asumiendo que la respuesta contiene el nombre del nuevo grupo
                        selected: true, // Seleccionar la nueva opción
                    })
                );

                Swal.fire({
                    icon: "success",
                    title: "Registro exitoso",
                    text: "El grupo ha sido registrado correctamente.",
                }).then(() => {
                    $("#tbGroupSends").DataTable().ajax.reload();
                });

                $("#modalNuevoGroupSend").modal("hide");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error al registrar:", errorThrown);

                var errors = jqXHR.responseJSON;
                var errorMessage = "<ul>";

                if (errors.error) {
                    errorMessage += "<li>" + errors.error + "</li>";
                } else if (errors.errors) {
                    $.each(errors.errors, function (key, value) {
                        errorMessage += "<li>" + value[0] + "</li>";
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
});
