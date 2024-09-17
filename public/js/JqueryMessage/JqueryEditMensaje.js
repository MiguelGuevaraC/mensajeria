function editMessage(id) {
    // Llamada AJAX para obtener el mensaje
    $.ajax({
        url: "message/" + id, // Ruta para obtener el mensaje
        type: "GET",
        success: function (response) {
            // Verificar si hay datos
            $('#registroMensajeNuevoE').trigger('reset');
            $("#titleE").val(response.title);
            $("#idE").val(response.id);
            $("#block1E").val(response.block1);
            $("#block2E").val(response.block2);
            $("#block3E").val(response.block3);
            $("#block4E").val(response.block4);

            // Si hay un archivo, puedes manejarlo aquí (por ejemplo, mostrando un enlace de descarga o algo similar)
        
            $("#modalNuevoMensajeE").modal("show");
        },
        error: function () {
            alert("Hubo un error al obtener los datos del mensaje.");
        },
    });
}
