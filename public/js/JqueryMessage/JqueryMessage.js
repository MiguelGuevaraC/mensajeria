$(document).ready(function () {
    $("#btonShowEtiquetas").click(function () {
        Swal.fire({
            title: "Etiquetas",
            html: `
                <table style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Etiqueta</th>
                            <th style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{names}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Nombre del contacto</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{documentNumber}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Número de documento</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{telephone}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Teléfono del contacto</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{address}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Dirección del contacto</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{concept}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Concepto del contacto</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{amount}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Monto relacionado</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left"><strong>{{dateReference}}</strong></td>
                            <td style="border-bottom: 1px solid #ddd; padding: 8px;text-align:left">Fecha de referencia</td>
                        </tr>
                   
                    </tbody>
                </table>
            `,
        });
    });

    $("#btonStoreMensaje").click(function () {
        $("#modalNuevoMensaje").modal("show");
    });

    $(document).on("click", 'button[id^="btonShowView-"]', function () {
        // Obtén el ID del botón
        var id = $(this).data("id");

        // Realiza la solicitud AJAX
        $.ajax({
            url: "message/showExample/" + id,
            method: "GET",
            success: function (response) {
                console.log(response);
                let data = response;
                Swal.fire({
                    title: "VISTA MENSAJE",
                    html: `
                        <div style='text-align:left;'><b>${data.title}</b></div><br>
                        <div style='text-align:left'>
                            <div>${data.block1}</div><br>
                            <div>${data.block2}</div><br>
                            <div>${data.block3}</div><br>
                            <div>${data.block4}</div>
                        </div>
                    `,
                });
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Hubo un problema al obtener los datos.",
                    icon: "error",
                });
            },
        });
    });

    $("#btonSaveMessage").on("click", function () {
        // Crear un objeto FormData
        var formData = new FormData();

        // Agregar los valores de los campos del formulario
        formData.append("title", $("#title").val());
        formData.append("block1", $("#block1").val());
        formData.append("block2", $("#block2").val());
        formData.append("block3", $("#block3").val());
        formData.append("block4", $("#block4").val());

        formData.append("_token", $('input[name="_token"]').val());

        var fileInput = $("#fileUpload")[0]; // Seleccionar el elemento DOM
        if (fileInput.files.length > 0) {
            formData.append("fileUpload", fileInput.files[0]); // Añadir el archivo al formData
        }

        $.ajax({
            url: "message", // Ruta del controlador
            type: "POST", // Usar PUT en lugar de POST
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer el contentType
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Registro actualizado exitosamente",
                    text: "El mensaje se ha guardado correctamente.",
                    confirmButtonText: "Aceptar",
                });
                $("#modalNuevoMensaje").modal("hide");
                $("#tbMensajes").DataTable().ajax.reload();
                $("#registroMensajeNuevo").trigger("reset");
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // Manejo de errores de validación
                    var errors = xhr.responseJSON.error;

                    Swal.fire({
                        icon: "error",
                        title: "Error Validación",
                        text: errors,
                        confirmButtonText: "Aceptar",
                    });
                } else {
                    // Otros errores
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Hubo un problema al guardar el mensaje.",
                        confirmButtonText: "Aceptar",
                    });
                }
            },
        });
    });
});

var columns = [
    { data: "title" },

    {
        data: "user.username",
        render: function (data, type, row, meta) {
            return data+' | '+row.user.company.businessName+' | '+row.user.company.documentNumber; // Formato de fecha
        },
        orderable: true,
    },

    {
        data: "created_at",
        render: function (data, type, row, meta) {
            return formatDate(data); // Formato de fecha
        },
        orderable: true,
    },

    {
        data: "id",
        render: function (data, type, row, meta) {
            return `

            <button id="btonShowView-${data}"  style="margin: 12px" class="btn btn-warning" data-id="${data}">Ver Mensaje</button>
             <a href="javascript:void(0)" onclick="editMessage(${data})" class="btn btn-info" style="background:#ffc107; color:white;"> 
                    <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)" onclick="destroyMessage(${data})" class="btn btn-info" style="background:red; color:white;"> 
                    <i class="fas fa-trash"></i>
                </a> <a href="/mensajeria/${row.routeFile}" target="_blank"
        
             class="btn btn-info" style="background:blue; color:white;"> 
                 <i class="fa-solid fa-file-circle-check"></i>
                </a>`;
        },
    },
];

// Función para formatear fecha
function formatDate(dateString) {
    const date = new Date(dateString);

    // Obtener los componentes de la fecha
    const year = date.getFullYear();
    const month = ("0" + (date.getMonth() + 1)).slice(-2); // Añadir 1 al mes porque es 0-based
    const day = ("0" + date.getDate()).slice(-2);
    const hours = ("0" + date.getHours()).slice(-2);
    const minutes = ("0" + date.getMinutes()).slice(-2);
    const seconds = ("0" + date.getSeconds()).slice(-2);

    // Formatear la fecha
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

var lenguag = {
    lengthMenu: "Mostrar _MENU_ Registros por paginas",
    zeroRecords: "No hay Registros",
    info: "Mostrando la pagina _PAGE_ de _PAGES_",
    infoEmpty: "",
    infoFiltered: "Filtrado de _MAX_ entradas en total",
    search: "Buscar:",
    paginate: {
        next: "Siguiente",
        previous: "Anterior",
    },
};

var lengthmenu = [
    [15, -1],
    [15, "Todos"],
];
var butomns = [
    {
        extend: "copy",
        text: 'COPY <i class="fa-solid fa-copy"></i>',
        className: "btn-secondary copy",
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7], // las columnas que se exportarán
        },
    },

    {
        extend: "excel",
        text: 'EXCEL <i class="fas fa-file-excel"></i>',
        className: "excel btn-success",
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7], // las columnas que se exportarán
        },
    },
    {
        extend: "pdf",

        text: 'PDF <i class="far fa-file-pdf"></i>',
        className: "btn-danger pdf",
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7], // las columnas que se exportarán
        },
    },
    {
        extend: "print",
        text: 'PRINT <i class="fa-solid fa-print"></i>',
        className: "btn-dark print",
        exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7], // las columnas que se exportarán
        },
    },
];

var search = {
    regex: true,
    caseInsensitive: true,
    type: "html-case-insensitive",
};
var init = function () {
    var api = this.api();

    // Agregar checkbox en el encabezado de la primera columna
    var toggleAllCheckbox = $(
        '<input type="checkbox" id="toggleAll" checked="true" class="form-check-input" style="width: 20px; height: 20px;background:red">'
    );

    var headerCell = $(".filters th").eq(0);
    $(headerCell).html(toggleAllCheckbox);

    // Configuración de DataTables
    api.columns()
        .eq(0)
        .each(function (colIdx) {
            var column = api.column(colIdx);
            var header = $(column.header());

            // Configurar filtro para columnas específicas
            if (colIdx == 0 || colIdx == 1|| colIdx == 2) {
                var cell = $(".filters th").eq(header.index());
                var title = header.text();
                $(cell).html(
                    '<input type="text" placeholder="Escribe aquí..." />'
                );

                // Evento para filtrar cuando se escriba en el input
                $("input", cell)
                    .off("keyup change")
                    .on("keyup change", function (e) {
                        e.stopPropagation();
                        var regexr = "({search})";
                        var cursorPosition = this.selectionStart;
                        column
                            .search(
                                this.value !== ""
                                    ? regexr.replace(
                                          "{search}",
                                          "(((" + this.value + ")))"
                                      )
                                    : "",
                                this.value !== "",
                                this.value === ""
                            )
                            .draw();

                        $(this)
                            .focus()[0]
                            .setSelectionRange(cursorPosition, cursorPosition);
                    });
            } else {
                $(header).html("");
            }
        });
};

$("#tbMensajes thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbMensajes thead");

$("#tbMensajes .filters input").on("keyup change", function () {
    table.ajax.reload();
});

$(document).ready(function () {
    var table = $("#tbMensajes").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "messageAll",
            type: "GET",
            data: function (d) {
                // Aquí configuramos los filtros de búsqueda por columna
                d.startDate = $("#startDate").val();
                d.endDate = $("#endDate").val();
                return d;
            },
            debounce: 500,
            error: function (xhr, error, thrown) {
                // Manejo de errores
                console.error("Error en la solicitud AJAX:", error);

                // Intentar nuevamente si no se alcanzó el número máximo de reintentos
                if (retryCount < maxRetries) {
                    retryCount++;
                    console.log(
                        "Reintentando... (Intento " +
                            retryCount +
                            " de " +
                            maxRetries +
                            ")"
                    );
                    table.ajax.reload();
                } else {
                    alert(
                        "No se pudo recuperar los datos después de varios intentos. Por favor, inténtelo de nuevo más tarde."
                    );
                }
            },
        },
        orderCellsTop: true,
        fixedHeader: true,
        columns: columns,
        dom: "rtip",
        buttons: [],

        language: lenguag,
        search: search,
        initComplete: init,
        rowId: "id",
        stripeClasses: ["odd-row", "even-row"],
        scrollY: "300px",
        scrollX: true, // Habilitar desplazamiento horizontal si es necesario
    });
});
