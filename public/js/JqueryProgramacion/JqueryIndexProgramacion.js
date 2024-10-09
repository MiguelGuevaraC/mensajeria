//DATATABLE

var columns = [
    {
        data: "status",
        render: function (data, type, row, meta) {
            return data;
        },
        orderable: false,
    },

    {
        data: "dateProgram",
        render: function (data, type, row, meta) {
            if (!data) return "";

            const date = new Date(data);
            const day = ("0" + date.getDate()).slice(-2);
            const month = ("0" + (date.getMonth() + 1)).slice(-2);
            const year = date.getFullYear();
            const hours = ("0" + date.getHours()).slice(-2);
            const minutes = ("0" + date.getMinutes()).slice(-2);
            const seconds = ("0" + date.getSeconds()).slice(-2);

            // Cambiamos el formato a año-mes-día horas
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        },
        orderable: true,
    },
    {
        data: "dateSend",
        render: function (data, type, row, meta) {
            if (!data) return "Aun No Enviado";

            const date = new Date(data);
            const day = ("0" + date.getDate()).slice(-2);
            const month = ("0" + (date.getMonth() + 1)).slice(-2);
            const year = date.getFullYear();
            const hours = ("0" + date.getHours()).slice(-2);
            const minutes = ("0" + date.getMinutes()).slice(-2);
            const seconds = ("0" + date.getSeconds()).slice(-2);

            // Cambiamos el formato a año-mes-día horas
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        },
        orderable: true,
    },
    {
        data: "created_at",
        render: function (data, type, row, meta) {
            if (!data) return "";

            const date = new Date(data);
            const day = ("0" + date.getDate()).slice(-2);
            const month = ("0" + (date.getMonth() + 1)).slice(-2);
            const year = date.getFullYear();
            const hours = ("0" + date.getHours()).slice(-2);
            const minutes = ("0" + date.getMinutes()).slice(-2);
            const seconds = ("0" + date.getSeconds()).slice(-2);

            // Cambiamos el formato a año-mes-día horas
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        },
        orderable: true,
    },
    {
        data: "user.username",
        render: function (data, type, row, meta) {
            return (
                data +
                " | " +
                row.user.company.businessName +
                " | " +
                row.user.company.documentNumber
            ); // Formato de fecha
        },
        orderable: true,
    },

    {
        data: "id",
        render: function (data, type, row) {
            let viewButton =
                '<a style="background:green; color:white; margin-right:5px;" class="view-description btn btn-success" data-description="' +
                data +
                '" title="Ver"><i class="fa-solid fa-eye"></i></a>';

            let editButton =
                '<a style="background:blue; color:white;" class="edit-description btn btn-primary" data-description="' +
                data +
                '" title="Editar"><i class="fa-solid fa-edit"></i></a>';

            // Si el estado no es 'Enviado', muestra el botón de editar
            if (row.status !== "Enviado") {
                return viewButton + editButton;
            } else {
                return viewButton; // Solo muestra el botón de ver si está "Enviado"
            }
        },
    },
];

$(document).on("click", ".view-description", function () {
    var idProgramming = $(this).data("description");

    // Realizar la llamada AJAX
    $.ajax({
        url: "programming/" + idProgramming, // Ruta para obtener la programación
        type: "GET",
        success: function (data) {
            // Formatear las fechas
            const formattedDateProgram = formatDate(data.dateProgram);
            const formattedDateSend = data.dateSend
                ? formatDate(data.dateSend)
                : "Aun no Enviado";
            const formattedCreatedAt = formatDate(data.created_at);

            // Resumen informativo
            const summaryHtml = `
    <div style="display: flex; flex-wrap: wrap; justify-content: space-around; margin-bottom: 10px;">
        <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
            <i class="fas fa-calendar-alt" style="font-size: 3vw; color: #3085d6;"></i>
            <p style="margin: 5px 0;"><b>Fecha de Programación:</b><br>${formattedDateProgram}</p>
        </div>
        <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
            <i class="fas fa-paper-plane" style="font-size: 3vw; color: #3085d6;"></i>
            <p style="margin: 5px 0;"><b>Fecha de Envío:</b><br>${formattedDateSend}</p>
        </div>
        <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
            <i class="fas fa-clock" style="font-size: 3vw; color: #3085d6;"></i>
            <p style="margin: 5px 0;"><b>Fecha de Registro:</b><br>${formattedCreatedAt}</p>
        </div>
        <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
            <i class="fas fa-envelope" style="font-size: 3vw; color: #3085d6;"></i>
            <p style="margin: 5px 0;"><b>Registros Enviados:</b><br>${data.count}</p>
        </div>
    </div>
`;


            // Tabla con DataTables
            let tableContent = `
                <table id="contactsTable" class="display" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="text-align:center;border: 0.5px solid #ffffff; padding: 8px;">Nombre</th>
                            <th style="text-align:center;border: 0.5px solid #ffffff; padding: 8px;">Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Agregar filas de contacts_by_group
            data.detail_programming.forEach((contactByGroup) => {
                tableContent += `
                    <tr>
                        <td style="border: 0.5px solid #ffffff; padding: 8px;">${contactByGroup.names}</td>
                        <td style="border: 0.5px solid #ffffff; padding: 8px;">${contactByGroup.telephone}</td>
                    </tr>
                `;
            });

            tableContent += `
                    </tbody>
                </table>
            `;

            // Mostrar SweetAlert con la tabla de contactos y resumen
            Swal.fire({
                title: "Detalles de la Programación",
                html: `
                    <div style="padding:0px 15px;max-height: 300px; overflow-y: auto;">
                        ${summaryHtml}
                        ${tableContent}
                    </div>
                `,
                showCloseButton: true, // Oculta el botón de cerrar (X)
                showCancelButton: false, // Asegúrate de que esto esté configurado como false
                showConfirmButton: false,
                confirmButtonText: "", // Sin texto para el botón de confirmación
            });

            // Inicializar DataTables después de que el DOM esté completamente cargado
            $("#contactsTable").DataTable({
                paging: true, // Activar paginación
                searching: true, // Mostrar buscador
                info: false, // Ocultar información adicional (opcional)
                lengthChange: false, // Desactivar el cambio del número de resultados mostrados por página
                pageLength: 5, // Número de filas por página
                language: {
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente",
                    },
                    search: "Buscar:", // Personalización del buscador
                },
            });
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            Swal.fire({
                title: "Error",
                text: "No se pudo recuperar la información de la programación.",
                icon: "error",
                confirmButtonText: "Aceptar",
            });
        },
    });
});

// Función para formatear la fecha
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    };
    return date.toLocaleString("es-ES", options);
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
    [15, 50, -1],
    [15, 50, "Todos"],
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
    toggleAllCheckbox.addClass("form-check-input");

    var headerCell = $(".filters th").eq(0);
    $(headerCell).html(toggleAllCheckbox);

    // Evento para marcar o desmarcar todos los checkboxes
    toggleAllCheckbox.on("change", function () {
        var isChecked = $(this).prop("checked");
        $(".checkCominments").prop("checked", isChecked);
    });

    // Configuración de DataTables
    api.columns()
        .eq(0)
        .each(function (colIdx) {
            var column = api.column(colIdx);
            var header = $(column.header());

            // Configurar filtro para columnas específicas
            if (
                colIdx == 0 ||
                colIdx == 1 ||
                colIdx == 2 ||
                colIdx == 3 ||
                colIdx == 4
            ) {
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

$("#tbProgramaciones thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbProgramaciones thead");

$("#tbProgramaciones .filters input").on("keyup change", function () {
    table.ajax.reload();
});

$(document).ready(function () {
    $(document).ready(function () {
        // Get today's date and yesterday's date
        let today = new Date();
        let yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 7);

        // Format dates to YYYY-MM-DD
        function formatDate(date) {
            let year = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-based
            let day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        }

        // Set the default values of the date inputs
        $("#startDate").val(formatDate(yesterday));
        $("#endDate").val(formatDate(today));

        var maxRetries = 3; // Número máximo de reintentos
        var retryCount = 0; // Contador de reintentos

        var table = $("#tbProgramaciones").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "programmingAll",
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
            createdRow: function (row, data, dataIndex) {
                if (data.status === "Pendiente") {
                    $(row).css("background-color", "rgb(255 217 102 / 25%)"); // Amarillo anaranjado
                } else if (data.status === "Enviado") {
                    $(row).css("background-color", "rgb(144 238 144 / 41%)"); // Verde
                }
            },
        });

        // Handle form submission
        $("#filterForm").on("submit", function (event) {
            event.preventDefault(); // Prevent the default form submission
            table.ajax.reload(); // Reload table data with new filters
        });

        // Handle PDF export
        $("#savePdf").on("click", function () {
            $.ajax({
                url: "pdfExport",
                type: "GET",
                data: {
                    startDate: $("#startDate").val(),
                    endDate: $("#endDate").val(),
                },
                xhrFields: {
                    responseType: "blob",
                },
                success: function (response, status, xhr) {
                    // Obtener la fecha actual en formato YYYY-MM-DD
                    const currentDate = new Date().toISOString().split("T")[0];

                    // Obtener las fechas de los parámetros
                    const startDate = $("#startDate").val();
                    const endDate = $("#endDate").val();

                    // Formatear el nombre del archivo
                    let filename = `Reporte_${currentDate}_${startDate}_a_${endDate}.pdf`;

                    // Obtener el nombre del archivo desde la respuesta del servidor si está disponible
                    const disposition = xhr.getResponseHeader(
                        "Content-Disposition"
                    );
                    if (
                        disposition &&
                        disposition.indexOf("attachment") !== -1
                    ) {
                        const matches = /"([^"]*)"/.exec(disposition);
                        if (matches != null && matches[1])
                            filename = matches[1];
                    }

                    // Crear un enlace para la descarga
                    const link = document.createElement("a");
                    const url = window.URL.createObjectURL(response);
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                },
            });
        });

        // Handle Excel export
        $("#saveExcel").on("click", function () {
            $.ajax({
                url: "excelExport",
                type: "GET",
                data: {
                    startDate: $("#startDate").val(),
                    endDate: $("#endDate").val(),
                },
                xhrFields: {
                    responseType: "blob",
                },
                success: function (response, status, xhr) {
                    // Obtener la fecha actual en formato YYYY-MM-DD
                    const currentDate = new Date().toISOString().split("T")[0];

                    // Obtener las fechas de los parámetros
                    const startDate = $("#startDate").val();
                    const endDate = $("#endDate").val();

                    // Formatear el nombre del archivo
                    let filename = `Reporte_${currentDate}_${startDate}_a_${endDate}.xlsx`;

                    // Obtener el nombre del archivo desde la respuesta del servidor si está disponible
                    const disposition = xhr.getResponseHeader(
                        "Content-Disposition"
                    );
                    if (
                        disposition &&
                        disposition.indexOf("attachment") !== -1
                    ) {
                        const matches = /"([^"]*)"/.exec(disposition);
                        if (matches != null && matches[1])
                            filename = matches[1];
                    }

                    // Crear un enlace para la descarga
                    const link = document.createElement("a");
                    const url = window.URL.createObjectURL(response);
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                },
            });
        });
    });
});
