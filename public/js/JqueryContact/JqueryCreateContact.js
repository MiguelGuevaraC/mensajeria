$(document).ready(function () {
    $("#registroContact").on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        // Leer el archivo Excel
        let file = $("#excelFile")[0].files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, { type: "array" });

                // Suponiendo que los datos están en la primera hoja
                let firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                let excelData = XLSX.utils.sheet_to_json(firstSheet, {
                    header: 1,
                });

                // Guardar el archivo en el servidor
                formData.append("excelFile", file);
                $("#modalNuevoContact").modal("hide");
                // Mostrar alerta de espera
                Swal.fire({
                    title: 'Por favor espera...',
                    text: 'Estamos procesando tu solicitud.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Ajustar el z-index para que SweetAlert esté por encima del modal
                        $('.swal2-container').css('z-index', '2000');
                    }
                });

                // Realizar la solicitud AJAX
                $.ajax({
                    url: "importExcel", // Ajusta la ruta según tu configuración de Laravel
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        // Cerrar la alerta de SweetAlert
                        Swal.close();
                        // Aquí puedes manejar la respuesta del servidor
                        
                        $("#tbContacts").DataTable().ajax.reload();
                    },
                    error: function (xhr, status, error) {
                        // Cerrar la alerta de SweetAlert
                        Swal.close();
                        // Mostrar mensaje de error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud.',
                        });
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                    },
                });
            };
            reader.readAsArrayBuffer(file);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo no seleccionado',
                text: 'Por favor, selecciona un archivo Excel.',
            });
        }
    });
});



$("#btonNuevo").click(function (e) {
    $("#registroContact")[0].reset();
    $("#modalNuevoContact").modal("show");
});

$(document).on("click", "#cerrarModal", function () {
    $("#modalNuevoContact").modal("hide");
});
