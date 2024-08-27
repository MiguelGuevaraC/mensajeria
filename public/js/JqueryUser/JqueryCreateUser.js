
$(document).ready(function () {

    $("#registroUsuario").submit(function (event) {
        event.preventDefault(); // Evita que el formulario se envíe por el método tradicional
    
        var token = $('meta[name="csrf-token"]').attr("content");
    
        $.ajax({
            url: "user",
            type: "POST",
            data: {
                password: $("#pass").val(),
                username: $("#username").val(),
                typeofUser_id: $("#typeuser").val(),
                company_id: $("#company").val(),
                _token: token,
            },
            success: function (data) {
                console.log("Respuesta del servidor:", data);
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    text: 'El usuario ha sido registrado correctamente.',
                });
                $("#tbUsuarios").DataTable().ajax.reload();
                $("#cerrarModal").click();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error al registrar:", errorThrown);
            
                // Extraer errores del servidor
                var errors = jqXHR.responseJSON;
                var errorMessage = '<ul>';
    
                // Si el error es un mensaje de texto simple
                if (errors.error) {
                    errorMessage += '<li>' + errors.error + '</li>';
                } else if (errors.errors) {
                    // Si el error es un array de mensajes
                    $.each(errors.errors, function (key, value) {
                        errorMessage += '<li>' + value[0] + '</li>'; // Asume que los errores son arrays
                    });
                }
    
                errorMessage += '</ul>';
    
                Swal.fire({
                    icon: 'error',
                    title: 'Error al registrar',
                    html: errorMessage,
                   
                });
            },
        });
    });
    

    // Obtener opciones de tipos de usuario desde la ruta
    $.ajax({
        url: "allTypeUserAndCompanies", // Reemplaza con la ruta correcta en tu aplicación
        type: "GET",
        dataType: "json",
        success: function (response) {
    
            // Limpiar opciones existentes
            $("#typeuser").empty();
            $("#company").empty();
    
            // Agregar opciones al select
            $("#typeuser").append(
                $("<option>", {
                    value: "",
                    text: "Selecciona un tipo de usuario",
                })
            );
    
            // Iterar sobre los tipos de usuario recibidos
            $.each(response.typeuser, function (index, tipoUsuario) {
                $("#typeuser").append(
                    $("<option>", {
                        value: tipoUsuario.id,
                        text: tipoUsuario.name
                    })
                );
            });
            $("#typeuser").val(2).trigger('change');
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
                        text: company.tradeName + ' | ' + company.documentNumber
                    })
                );
            });
         
            // Inicializar select2 después de cargar las opciones
            $('#typeuser').select2();
            $('#company').select2();

     
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar tipos de usuario:", error);
        },
    });
    
});

$("#btonNuevo").click(function (e) {
    $("#registroUsuario")[0].reset();
    $("#modalNuevoUsuario").modal("show");
});

$(document).on("click", "#cerrarModalUsuario", function () {
    $("#modalNuevoUsuario").modal("hide");
});
