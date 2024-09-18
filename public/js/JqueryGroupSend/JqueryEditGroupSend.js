// Función para obtener los datos del groupSend
function getUserData(id) {
    return $.ajax({
        url: "groupSend/" + id,
        type: "GET",
        dataType: "json",
    });
}

function getTypeUserData() {
    return $.ajax({
        url: "allTypeUserAndCompanies",
        type: "GET",
        dataType: "json",
    });
}

function editRol(id) {
    // Mostrar el modal antes de realizar la llamada AJAX
    $("#modalEditarGroupSend").modal("show");

    // Realizar la llamada AJAX
    $.ajax({
        url: `groupSend/${id}`, // Ruta que apunta a tu controlador
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Datos recibidos del servidor:", data);
            $('#idE').val(id);
            // Suponiendo que tienes inputs con estos IDs en el modal, los llenamos con los datos recibidos
            $('#nameE').val(data.name); // Campo nombre del groupSend
            $('#commentE').val(data.comment); // Campo comentario del groupSend


            // Puedes ajustar esto según los campos disponibles en tu formulario
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener los datos del servidor:", error);

            // Puedes mostrar una alerta si hay un error
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los datos del groupSend.',
            });
        }
    });
}




// Cierra el modal al hacer clic en el botón de cerrar
$(document).on("click", "#cerrarModalGroupSendE", function () {
    $("#modalEditarGroupSend").modal("hide");
});
