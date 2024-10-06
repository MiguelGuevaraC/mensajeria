$(document).ready(function () {
    // Función para verificar las contraseñas
    function checkPasswordMatch() {
        if ($("#passNew").val() === $("#passConf").val()) {
            // Si las contraseñas coinciden, cambia el color a verde claro
            $("#passNew, #passConf").css("background-color", "#d4edda"); // Verde claro
        } else {
            // Si no coinciden, cambia el color a rojo claro
            $("#passNew, #passConf").css("background-color", "#f8d7da"); // Rojo claro
        }
        if ($("#passNew").val() === "" && $("#passConf").val() === "") {
            $("#passNew, #passConf").css("background-color", "white");
            $("#passNew, #passConf").css("background-color", "white");
        }
    }

    // Verificar las contraseñas al escribir
    $("#passNew, #passConf").on("input", checkPasswordMatch);

    // Función para mostrar/ocultar contraseñas
    function togglePasswordVisibility(inputSelector, iconSelector) {
        $(iconSelector).click(function () {
            var input = $(inputSelector);
            if (input.attr("type") === "password") {
                input.attr("type", "text");
                $(this)
                    .find("i")
                    .removeClass("fa-eye")
                    .addClass("fa-eye-slash");
            } else {
                input.attr("type", "password");
                $(this)
                    .find("i")
                    .removeClass("fa-eye-slash")
                    .addClass("fa-eye");
            }
        });
    }

    // Llamada a la función para cada input
    togglePasswordVisibility("#passOld", "#mostrar-contrasenaAnterior");
    togglePasswordVisibility("#passNew", "#mostrar-contrasena");
    togglePasswordVisibility("#passConf", "#mostrar-contrasenaConfirm");

    $("#contraseñaNueva").on("submit", function (event) {
        event.preventDefault(); // Evita el envío normal del formulario

        const formData = $(this).serialize(); // Serializa los datos del formulario

        $.ajax({
            url: "updatePass", // URL a la que se enviará la solicitud
            type: "PUT", // Método HTTP
            data: formData,
            success: function (response) {
                // Maneja la respuesta exitosa aquí
                swal({
                    title: "Éxito!",
                    text: "Contraseña actualizada exitosamente.",
                    icon: "success",
                    button: "Aceptar",
                });
                $("#contraseñaNueva")[0].reset();
                $("#userNameUp").text(response.username);
                $("#username").val(response.username);
                checkPasswordMatch()
                console.log(response);
            },
            error: function (xhr) {
                // Maneja los errores aquí
                if (xhr.status === 422) {
                    const error = xhr.responseJSON.error;

                    // Verificar si error es un string o un array
                    let errorMessage =
                        typeof error === "string"
                            ? error
                            : "Se encontraron los siguientes errores:\n\n" +
                              error.join("\n");

                    swal({
                        title: "Advertencia!",
                        text: errorMessage,
                        icon: "warning",
                        button: "Aceptar",
                    });
                } else {
                    const error =
                        xhr.responseJSON.error ||
                        "Ocurrió un error inesperado.";
                    swal({
                        title: "Error!",
                        text: error,
                        icon: "error",
                        button: "Aceptar",
                    });
                }
                console.error(xhr);
            },
        });
    });
});
