// Función para obtener los datos del usuario
function getUserData(id) {
    return $.ajax({
        url: "user/" + id,
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
    // Llamadas asíncronas para obtener datos del usuario y tipos de usuario y empresas
   
}



// Cierra el modal al hacer clic en el botón de cerrar
$(document).on("click", "#cerrarModalUsuarioE", function () {
    $("#modalNuevoUsuarioE").modal("hide");
});
