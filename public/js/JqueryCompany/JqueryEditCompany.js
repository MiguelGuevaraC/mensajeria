function editCompany(id) {
    $("#editarCompany")[0].reset(); // Reinicia el formulario para evitar datos residuales

    // Muestra el modal de edición
    $("#modalEditarCompany").modal("show");

    // Petición AJAX para obtener los datos del company por su ID
    $.ajax({
        url: "company/" + id,
        type: "GET",
        dataType: "json",
        success: function (data) {
            // Llenar los campos del formulario con los datos recibidos
            $("#companyId").val(data.id);
            $("#documentNumberEdit").val(data.documentNumber);
            $("#businessNameEdit").val(data.businessName);
            $("#tradeNameEdit").val(data.tradeName);
            $("#addressEdit").val(data.address);
            $("#representativeNameEdit").val(data.representativeName);
            $("#telephoneEdit").val(data.telephone);
            $("#costSendE").val(data.costSend);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error al cargar datos del company:", errorThrown);
        }
    });
}
