// Función que muestra el modal y realiza la petición AJAX
function destroyRol(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // Usuario confirmó la eliminación, proceder con la solicitud AJAX
            var token = $('meta[name="csrf-token"]').attr("content");

            $.ajax({
                url: "contactByGroup/" + id,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                success: function (data) {
                    Swal.fire({
                        title: 'Éxito',
                        icon: 'success',
                        text: 'Eliminación Exitosa',
                        confirmButtonText: 'Aceptar'
                    });

                    // Eliminar la fila correspondiente de la tabla DataTables
                    var table = $("#tbContacts").DataTable();
                    var row = table.row("#" + id);

                    if (row.length > 0) {
                        row.remove().draw(false);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $.niftyNoty({
                        type: "danger",
                        icon: "fa fa-times",
                        message: "Error al Eliminar: " + textStatus + " - " + errorThrown,
                        container: "floating",
                        timer: 4000,
                    });
                },
            });
        }
    });
   
}
