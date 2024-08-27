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
    $.when(getUserData(id), getTypeUserData())
        .done(function (userDataResponse, typeUserDataResponse) {
            var userData = userDataResponse[0];
            var typeUserData = typeUserDataResponse[0];

            // Obtener los selects y limpiarlos
            var typeSelect = $("#typeuserE");
            var companySelect = $("#companyE");

            typeSelect.empty();
            companySelect.empty();

            // Agregar opciones al select de tipo de usuario
            typeSelect.append('<option value="">Selecciona un tipo de usuario</option>');
            $.each(typeUserData.typeuser, function (index, type) {
                var selected = userData.typeofUser_id === type.id ? "selected" : "";
                typeSelect.append(
                    '<option value="' + type.id + '" ' + selected + '>' + type.name + '</option>'
                );
            });

            // Agregar opciones al select de empresa
            companySelect.append('<option value="">Selecciona una empresa</option>');
          
            $.each(typeUserData.company, function (index, company) {
                console.log(company);
                var selected = userData.company_id === company.id ? "selected" : "";
                companySelect.append(
                    '<option value="' + company.id + '" ' + selected + '>' + company.tradeName + ' | ' + company.documentNumber + '</option>'
                );
            });

            // Rellenar los campos del formulario con los datos del usuario
            $("#usernameE").val(userData.username);
            $("#idE").val(userData.id);
            $("#typeuserE").val(userData.typeofUser_id).trigger('change'); // Trigger change for select2
            $("#companyE").val(userData.company_id).trigger('change'); // Trigger change for select2

            // Inicializar select2 después de cargar las opciones
           

            // Mostrar el modal
            $("#modalEditarUsuario").modal("show");
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Error al cargar datos:", errorThrown);
            console.log("Respuesta del servidor:", jqXHR.responseText);
        });
}



// Cierra el modal al hacer clic en el botón de cerrar
$(document).on("click", "#cerrarModalUsuarioE", function () {
    $("#modalNuevoUsuarioE").modal("hide");
});
