function destroyMessage(id) {
    // Mostrar un mensaje de confirmación con SweetAlert
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás recuperar este mensaje!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            var token = $('meta[name="csrf-token"]').attr("content");
            $.ajax({
                url: "message/" + id, // Ruta hacia el controlador con el ID del mensaje

                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                success: function (response) {
                    // Mostrar mensaje de éxito con SweetAlert
                    Swal.fire(
                        "¡Eliminado!",
                        "El mensaje ha sido eliminado.",
                        "success"
                    ).then(() => {
                        $("#tbMensajes").DataTable().ajax.reload();
                    });
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    // Mostrar mensaje de error con SweetAlert
                    Swal.fire(
                        "Error",
                        "Hubo un problema al eliminar el mensaje.",
                        "error"
                    );
                },
            });
        }
    });
}
