// Función que muestra el modal y realiza la petición AJAX
function editRol(id) {
    // Mostrar el modal
    $("#modalUpdateContact").modal("show");

    // Hacer una solicitud AJAX para obtener los datos del contacto por grupo
    $.ajax({
        url: 'contactByGroup/' + id, // La ruta para obtener los datos del contacto
        method: 'GET', // Tipo de solicitud
        success: function(response) {
            // Llenar los campos del formulario con los datos recibidos en la respuesta
            $('#idEContact').val(id); // ID oculto del contacto
            $('#contactId').val(response.contact.id); // ID oculto del contacto
            $('#documentNumber').val(response.contact.documentNumber);
            $('#names').val(response.contact.names);
            $('#telephone').val(response.contact.telephone);
            $('#address').val(response.contact.address);
            $('#concept').val(response.contact.concept);
            $('#amount').val(response.contact.amount);
            $('#dateReference').val(response.contact.dateReference);

            // Mostrar el modal una vez que los datos han sido cargados
            $("#modalUpdateContact").modal("show");
        },
        error: function(xhr) {
            // Manejar el error en caso de que ocurra
            console.error('Error al obtener los datos del contacto: ', xhr.responseText);
            alert('Hubo un problema al cargar los datos del contacto.');
        }
    });
}
