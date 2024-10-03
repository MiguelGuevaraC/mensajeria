var columns = [
    {
        data: "businessName",
        render: function (data, type, row, meta) {
            return data + " | " + row.tradeName + " | " + row.documentNumber; // Mostrar solo el nombre de la empresa
        },
        orderable: false,
    },

    {
        data: "representativeName",
        render: function (data, type, row, meta) {
            return data; // Mostrar solo el nombre del representante
        },
        orderable: false,
    },
    {
        data: "address",
        render: function (data, type, row, meta) {
            return data; // Mostrar solo el nombre de la empresa
        },
        orderable: false,
    },
    {
        data: "telephone",
        render: function (data, type, row, meta) {
            return data; // Mostrar solo el teléfono
        },
        orderable: false,
    },
    {
        data: null,
        render: function (data, type, full, meta) {
            return `
                <a href="javascript:void(0)" onclick="editCompany(${data.id})" class="btn btn-info" style="background:#ffc107; color:white;"> 
                    <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)" onclick="destroyCompany(${data.id})" class="btn btn-info" style="background:red; color:white;"> 
                    <i class="fas fa-trash"></i>
                </a>
            `;
        },
        orderable: false,
    },
];

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
    [5, 50, -1],
    [5, 50, "Todos"],
];
var butomns = [
    {
        extend: "copy",
        text: 'COPY <i class="fa-solid fa-copy"></i>',
        className: "btn-secondary copy",
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6], // las columnas que se exportarán
        },
    },

    {
        extend: "excel",
        text: 'EXCEL <i class="fas fa-file-excel"></i>',
        className: "excel btn-success",
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6], // las columnas que se exportarán
        },
    },
    {
        extend: "pdf",

        text: 'PDF <i class="far fa-file-pdf"></i>',
        className: "btn-danger pdf",
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6], // las columnas que se exportarán
        },
    },
    {
        extend: "print",
        text: 'PRINT <i class="fa-solid fa-print"></i>',
        className: "btn-dark print",
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6], // las columnas que se exportarán
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
    api.columns()
        .eq(0)
        .each(function (colIdx) {
            if (colIdx == 0 || colIdx == 1 || colIdx == 2 || colIdx == 3) {
                var cell = $(".filters th").eq(
                    $(api.column(colIdx).header()).index()
                );
                var title = $(cell).text();
                $(cell).html(
                    '<input type="text" placeholder="Escribe aquí..." />'
                );

                $(
                    "input",
                    $(".filters th").eq($(api.column(colIdx).header()).index())
                )
                    .off("keyup change")
                    .on("keyup change", function (e) {
                        e.stopPropagation();
                        // Get the search value
                        $(this).attr("title", $(this).val());
                        var regexr = "({search})";
                        var cursorPosition = this.selectionStart;
                        api.column(colIdx)
                            .search(
                                this.value != ""
                                    ? regexr.replace(
                                          "{search}",
                                          "(((" + this.value + ")))"
                                      )
                                    : "",
                                this.value != "",
                                this.value == ""
                            )
                            .draw();
                        $(this)
                            .focus()[0]
                            .setSelectionRange(cursorPosition, cursorPosition);
                    });
            } else {
                var cell = $(".filters th").eq(
                    $(api.column(colIdx).header()).index()
                );
                $(cell).html("");
            }
        });
};

$("#tbCompanies thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbCompanies thead");

$(document).ready(function () {
    var maxRetries = 3; // Número máximo de reintentos
    var retryCount = 0; // Contador de reintentos
    var table = $("#tbCompanies").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "companyAll",
            type: "GET",
            data: function (d) {
                // Aquí configuramos los filtros de búsqueda por columna
                $("#tbCompanies .filters input").each(function () {
                    var name = $(this).attr("name");
                    d.columns.forEach(function (column) {
                        if (column.data === name) {
                            column.search.value = $(this).val();
                        }
                    }, this);
                });
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
                    fetchTableData(retryCount);
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
        scrollX: true,
    });
});
