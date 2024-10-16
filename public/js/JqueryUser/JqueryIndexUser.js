//DATATABLE

var columns = [

    {
        data: "username",
        render: function (data, type, row, meta) {
            return data;
        },
        orderable: false,
    },
    {
        data: "company",
        render: function (data, type, row, meta) {
            return data.businessName + ' | '+data.tradeName+ ' | '+data.documentNumber;
        },
        orderable: false,
    },

    {
        data: "type_user",
        render: function (data, type, row, meta) {
            return data["name"];
        },
        orderable: false,
    },

    {
        data: null,
        render: function (data, type, full, meta) {
            return `
                <a href="javascript:void(0)" onclick="editRol(${data.id})" style="background:#ffc107; color:white;" class="btn btn-info"> 
                    <i class="fas fa-edit"></i>
                </a>
                <a href="javascript:void(0)" onclick="destroyRol(${data.id})" style="background:#dc3545; color:white;" class="btn btn-danger"> 
                    <i class="fas fa-trash-alt"></i>
                </a>
             `;
        },
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
    [5, 10, 50, -1],
    [5, 10, 50, "Todos"],
];
var butomns = [
    {
        extend: "copy",
        text: 'COPY <i class="fa-solid fa-copy"></i>',
        className: "btn-secondary copy",
        exportOptions: {
            columns: [0, 1, 2], // las columnas que se exportarán
        },
    },

    {
        extend: "excel",
        text: 'EXCEL <i class="fas fa-file-excel"></i>',
        className: "excel btn-success",
        exportOptions: {
            columns: [0, 1, 2], // las columnas que se exportarán
        },
    },
    {
        extend: "pdf",

        text: 'PDF <i class="far fa-file-pdf"></i>',
        className: "btn-danger pdf",
        exportOptions: {
            columns: [0, 1, 2], // las columnas que se exportarán
        },
    },
    {
        extend: "print",
        text: 'PRINT <i class="fa-solid fa-print"></i>',
        className: "btn-dark print",
        exportOptions: {
            columns: [0, 1, 2], // las columnas que se exportarán
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
            if (colIdx == 0 || colIdx == 1
                || colIdx == 2 
               
            ) {
                var cell = $(".filters th").eq(
                    $(api.column(colIdx).header()).index()
                );
                var title = $(cell).text();
                $(cell).html(
                    '<input type="text" placeholder="Escribe aquí..." />'
                );
                // if (colIdx == 0) {
                //     $(cell).html(
                //         '<input style="width: 30px;" type="text" placeholder="#" />'
                //     );
                // }
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

$("#tbUsuarios thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbUsuarios thead");

    $(document).ready(function () {
        var maxRetries = 3; // Número máximo de reintentos
        var retryCount = 0; // Contador de reintentos
    
        // Configuración de DataTable para usuarios
        var table = $("#tbUsuarios").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "userAll",
                type: "GET",
                dataSrc: function (json) {
                    console.log(json); // Verifica la respuesta del servidor
                    return json.data; // Retorna los datos para DataTable
                },
                error: function (xhr, error, thrown) {
                    console.error("Error en la solicitud AJAX:", error);
    
                    // Intentar nuevamente si no se alcanzó el número máximo de reintentos
                    if (retryCount < maxRetries) {
                        retryCount++;
                        console.log("Reintentando... (Intento " + retryCount + " de " + maxRetries + ")");
                        setTimeout(function() {
                            table.ajax.reload(); // Recarga los datos de la tabla
                        }, 1000); // Espera 1 segundo antes de reintentar
                    } else {
                        // Mostrar un mensaje de error si se agotaron los reintentos
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al cargar datos',
                            text: 'No se pudo cargar la información después de varios intentos.',
                        });
                    }
                }
            },
            orderCellsTop: true,
            fixedHeader: true,
            columns: columns, // Define las columnas según tu configuración
            dom: "rtip", // Configura los controles de la tabla (filtro, paginación, etc.)
            buttons: butomns, // Botones de acción (como exportar)
            lengthMenu: lengthmenu, // Configura las opciones de paginación
            language: lenguag, // Configura el lenguaje de la tabla
            search: search, // Configura la búsqueda
            initComplete: init, // Función a ejecutar cuando la tabla se inicializa
            stripeClasses: ["odd-row", "even-row"], // Estilos para las filas alternas
            rowId: "id", // ID de las filas
            scrollY: "300px", // Altura de la tabla con scroll vertical
            scrollX: true, // Habilita el scroll horizontal
        });
    });
    
