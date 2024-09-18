$("#btonNuevo").click(function (e) {
    $("#registroCompany")[0].reset();
    $("#modalNuevoCompany").modal("show");
});

$(document).on("click", "#cerrarModal", function () {
    $("#modalNuevoCompany").modal("hide");
});
$("#registroCompany").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    // Obtener los datos del formulario
    var documentNumber = $("#documentNumber").val();
    var businessName = $("#businessName").val();
    var tradeName = $("#tradeName").val();
    var representativeName = $("#representativeName").val();
    var representativeDni = $("#dniApoderadoE").val(); // Asegúrate de que este campo exista en el formulario
    var telephone = $("#telephone").val();
    var address = $("#address").val();
    var email = $("#emailEdit").val();

    // Inicializar los mensajes de error
    var errors = [];

    // Validar que el número de documento tenga 11 dígitos
    if (documentNumber.length !== 11) {
        errors.push("El número de documento debe tener 11 dígitos.");
    }

    // Validar que el teléfono tenga 9 dígitos
    if (telephone.length !== 9) {
        errors.push("El teléfono debe tener 9 dígitos.");
    }

    // Si hay errores, mostrarlos en SweetAlert
    if (errors.length > 0) {
        Swal.fire({
            title: "Error",
            icon: "error",
            html: errors.join("<br>"), // Mostrar todos los errores en una sola ventana de SweetAlert
            confirmButtonText: "Aceptar",
        });
        return; // Detener la ejecución si hay errores
    }

    // Si la validación pasa, continuar con el envío del formulario
    var formData = {
        documentNumber: documentNumber,
        businessName: businessName,
        tradeName: tradeName,
        representativeName: representativeName,
        representativeDni: representativeDni,
        telephone: telephone,
        address: address,
        email: email,
    };

    // Obtener el token CSRF
    var token = $('meta[name="csrf-token"]').attr("content");

    // Realizar la solicitud AJAX para almacenar el recurso
    $.ajax({
        url: "company", // Ruta para crear el recurso, no necesita ID
        type: "POST", // Método HTTP POST
        data: formData, // Datos del formulario con los nombres esperados en el servidor
        headers: {
            "X-CSRF-TOKEN": token, // Incluir el token CSRF en el encabezado de la solicitud
        },
        success: function (response) {
            // Cerrar el modal de creación
            $("#modalNuevoCompany").modal("hide");

            // Recargar los datos en la tabla utilizando DataTables
            $("#tbCompanies").DataTable().ajax.reload();

            // Mostrar notificación de éxito
            Swal.fire({
                title: "Éxito",
                icon: "success",
                text: "Empresa registrada correctamente",
                confirmButtonText: "Aceptar",
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al registrar empresa:", errorThrown);

            // Mostrar notificación de error
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "Error al registrar empresa: " + errorThrown,
                confirmButtonText: "Aceptar",
            });
        },
    });
});

$(document).ready(function () {
    $("#search").click(function () {
        // Obtén el valor del campo de entrada
        var documentNumber = $("#documentNumber").val();

        // Verifica si el campo no está vacío
        if (documentNumber.trim() === "") {
            alert("Por favor ingresa un número de documento.");
            return;
        }

        // Realiza la petición AJAX
        $.ajax({
            url: "searchByRuc/" + encodeURIComponent(documentNumber), // URL de la petición
            method: "GET", // Método HTTP
            success: function (response) {
                var data = response[0];
                $("#tradeName").val(data.RazonSocial);
                $("#address").val(data.Direccion);
                $("#businessName").val(data.RazonSocial);

            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", error);
            },
        });
    });
});

$(document).ready(function () {
    $("#searchEdit").click(function () {
        // Obtén el valor del campo de entrada
        var documentNumber = $("#documentNumberEdit").val();

        // Verifica si el campo no está vacío
        if (documentNumber.trim() === "") {
            alert("Por favor ingresa un número de documento.");
            return;
        }

        // Realiza la petición AJAX
        $.ajax({
            url: "searchByRuc/" + encodeURIComponent(documentNumber), // URL de la petición
            method: "GET", // Método HTTP
            success: function (response) {
                var data = response[0];
                $("#tradeNameEdit").val(data.RazonSocial);
                $("#addressEdit").val(data.Direccion);
                $("#businessNameEdit").val(data.RazonSocial);

            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", error);
            },
        });
    });
});
