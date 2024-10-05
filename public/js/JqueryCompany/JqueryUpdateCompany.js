$("#editarCompany").submit(function (event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    // Obtener el ID de la empresa del campo oculto
    var companyId = $("#companyId").val();

    // Obtener los datos del formulario
    var documentNumber = $("#documentNumberEdit").val();
    var telephone = $("#telephoneEdit").val();

    // Inicializar los mensajes de error
    var errors = [];

    // Expresión regular para validar que el campo solo contenga dígitos
    var digitsOnly = /^\d+$/;

    // Validar que el número de documento (RUC) tenga 11 dígitos y solo dígitos
    if (!digitsOnly.test(documentNumber) || documentNumber.length !== 11) {
        errors.push("El número de documento debe tener exactamente 11 dígitos numéricos.");
    }

    // Validar que el teléfono tenga 9 dígitos y solo dígitos
    if (!digitsOnly.test(telephone) || telephone.length !== 9) {
        errors.push("El teléfono debe tener exactamente 9 dígitos numéricos.");
    }

    // Si hay errores, mostrarlos en SweetAlert
    if (errors.length > 0) {
        Swal.fire({
            title: 'Error',
            icon: 'error',
            html: errors.join('<br>'), // Mostrar todos los errores en una sola ventana de SweetAlert
            confirmButtonText: 'Aceptar'
        });
        return; // Detener la ejecución si hay errores
    }

    // Obtener los datos del formulario después de validaciones
    var formData = {
        documentNumber: documentNumber,
        businessName: $("#businessNameEdit").val(),
        tradeName: $("#tradeNameEdit").val(),
        representativeName: $("#representativeNameEdit").val(),
        representativeDni: $("#dniApoderadoE").val(),
        telephone: telephone,
        address: $("#addressEdit").val(),
        email: $("#emailEdit").val(),
        costSend: $("#costSendE").val(),
        status: $("#statusEdit").prop('checked') ? 1 : 0, // Suponiendo que es un checkbox
    };

    // Obtener el token CSRF
    var token = $('meta[name="csrf-token"]').attr("content");

    // Realizar la solicitud AJAX para actualizar el recurso
    $.ajax({
        url: `company/${companyId}`, // Ruta para actualizar el recurso, con ID
        type: "PUT", // Método HTTP PUT
        data: formData, // Datos del formulario
        headers: {
            "X-CSRF-TOKEN": token, // Incluir el token CSRF en el encabezado de la solicitud
        },
        success: function (response) {
            // Cerrar el modal de edición
            $("#modalEditarCompany").modal("hide");

            // Recargar los datos en la tabla utilizando DataTables
            // $("#tbCompanies").DataTable().ajax.reload();
            $("#tbCompanies").DataTable().ajax.reload(null, false);

            // Mostrar notificación de éxito con SweetAlert
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Empresa actualizada correctamente.',
                confirmButtonText: 'Aceptar'
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Mostrar notificación de error con SweetAlert
            var errors = [];
            
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                // Extraer errores del JSON
                $.each(jqXHR.responseJSON.errors, function (key, value) {
                    errors.push(value.join('<br>'));
                });
            } else {
                errors.push('Error al actualizar empresa: ' + errorThrown);
            }

            // Mostrar errores con SweetAlert
            Swal.fire({
                title: 'Error',
                icon: 'error',
                html: errors.join('<br>'), // Mostrar todos los errores en una sola ventana de SweetAlert
                confirmButtonText: 'Aceptar'
            });
        }
    });
});
