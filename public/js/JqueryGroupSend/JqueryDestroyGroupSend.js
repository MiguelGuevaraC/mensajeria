function destroyRol(id) {
    // Mostrar SweetAlert para confirmar la eliminación
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Recuerda que los contactos de este grupo también se desactivarán.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            // GroupSend confirmó la eliminación, proceder con la solicitud AJAX
            var token = $('meta[name="csrf-token"]').attr("content");

            $.ajax({
                url: "groupSend/" + id,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                success: function (data) {
                    Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: "El groupSend ha sido actualizado correctamente.",
                    }).then(() => {
                        $("#tbGroupSends").DataTable().ajax.reload();
                    });
               
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: "error",
                        title: "Error al actualizar",
                        html: errorMessage,
                    });
                },
            });
        }
    });
}
