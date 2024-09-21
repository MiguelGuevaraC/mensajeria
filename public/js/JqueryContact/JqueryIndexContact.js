//DATATABLE

var columns = [
    {
        data: "id",
        visible: false, // Oculta esta columna
    },
    {
        data: "stateSend", 
        render: function (data, type, row, meta) {
            // Determina si el checkbox está marcado
            const isChecked = data === 1 ? "checked" : "";
            
            // Retorna el checkbox personalizado con sus estilos
            return `
                <div class="checkbox-wrapper">
                    <input 
                        class="checkCompro styled-checkbox" 
                        type="checkbox" 
                        id="checkbox-${row.id}" 
                        value="${row.id}" 
                        ${isChecked}
                    >
                    <label for="checkbox-${row.id}" class="checkbox-label"></label>
                </div>
            `;
        },
        orderable: false, // Deshabilitar la ordenación en esta columna
    },
    
    
    {
        data: "group_send.name",
        render: function (data, type, row, meta) {
            return data;
        },
        orderable: false,
    },
    {
        data: "contact.names",
        render: function (data, type, row, meta) {
            const parts = [];
            
            if (data) parts.push(data);
            if (row.contact.documentNumber) parts.push(row.contact.documentNumber);
            if (row.contact.telephone) parts.push(row.contact.telephone);
            if (row.contact.address) parts.push(row.contact.address);
            
            return parts.join(" - ");
        },
        orderable: false,
    },
    

    {
        data: "contact.concept",
        render: function (data, type, row, meta) {
            return data;
        },
        orderable: false,
    },
    {
        data: "contact.amount",
        render: function (data, type, row, meta) {
            return data;
        },
        orderable: false,
    },

    {
        data: "contact.dateReference",
        render: function (data, type, row, meta) {
            return data ? formatDateOnlyDate(data) : '-'; // Formato de fecha
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

function formatDate(dateString) {
    const date = new Date(dateString);

    // Obtener los componentes de la fecha y hora, formateando con padStart en una sola línea
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0"); // Mes 0-based, por eso sumamos 1
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    // Devolver la fecha en formato YYYY-MM-DD HH:MM:SS
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

function formatDateOnlyDate(dateString) {
    const date = new Date(dateString);

    // Obtener los componentes de la fecha
    const year = date.getFullYear();
    const month = ("0" + (date.getMonth() + 1)).slice(-2); // Añadir 1 al mes porque es 0-based
    const day = ("0" + date.getDate()).slice(-2);

    // Formatear la fecha
    return `${year}-${month}-${day}`;
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
    [30, 50, 100],
    [30, 50, 100],
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
var init = function (settings, json) {
    var api = this.api();
    var table = api.table().node(); // Asegúrate de obtener la referencia de la tabla

    // Agregar checkbox en el encabezado de la primera columna
    var toggleAllCheckbox = $(
        '<input type="checkbox" id="toggleAll" class="form-check-input" style="width: 20px; height: 20px;">'
    );

    var headerCell = $(".filters th").eq(0);

    api.columns()
        .eq(0)
        .each(function (colIdx) {
            var column = api.column(colIdx);
            var header = $(column.header());

            // Configurar filtro para columnas específicas
            if ([1, 2, 3, 4, 5, 6, 7, 8].includes(colIdx)) {
                var cell = $(".filters th").eq(header.index());
                var title = header.text();

                if (colIdx == 1 || colIdx == 8) {
                    $(cell).html("");
                } else {
                    $(cell).html(
                        '<input type="text" placeholder="Escribe aquí..." />'
                    );
                }

                // Evento para filtrar cuando se escriba en el input
                $("input", cell)
                    .off("keyup change")
                    .on("keyup change", function (e) {
                        e.stopPropagation();
                        var checkedInput = $("#toggleAlll").is(":checked");
                        if (this.type === "text") {
                            var cursorPosition = this.selectionStart;
                            column.search(this.value, true, false).draw();

                            $(this)
                                .focus()[0]
                                .setSelectionRange(
                                    cursorPosition,
                                    cursorPosition
                                );

                            checkedInput = "";

                            api.column(1).search("false", true, false).draw();
                        }
                    });
            } else {
                $(header).html("");
            }
        });
};

// var init = function () {
//     var api = this.api();
//     api.columns()
//         .eq(0)
//         .each(function (colIdx) {
//             if (colIdx == 0 || colIdx == 1
//                 || colIdx == 2
//                 || colIdx == 3
//                 || colIdx == 4
//                 || colIdx == 5

//             ) {
//                 var cell = $(".filters th").eq(
//                     $(api.column(colIdx).header()).index()
//                 );
//                 var title = $(cell).text();
//                 $(cell).html(
//                     '<input type="text" placeholder="Escribe aquí..." />'
//                 );
//                 // if (colIdx == 0) {
//                 //     $(cell).html(
//                 //         '<input style="width: 30px;" type="text" placeholder="#" />'
//                 //     );
//                 // }
//                 $(
//                     "input",
//                     $(".filters th").eq($(api.column(colIdx).header()).index())
//                 )
//                     .off("keyup change")
//                     .on("keyup change", function (e) {
//                         e.stopPropagation();
//                         // Get the search value
//                         $(this).attr("title", $(this).val());
//                         var regexr = "({search})";
//                         var cursorPosition = this.selectionStart;
//                         api.column(colIdx)
//                             .search(
//                                 this.value != ""
//                                     ? regexr.replace(
//                                           "{search}",
//                                           "(((" + this.value + ")))"
//                                       )
//                                     : "",
//                                 this.value != "",
//                                 this.value == ""
//                             )
//                             .draw();
//                         $(this)
//                             .focus()[0]
//                             .setSelectionRange(cursorPosition, cursorPosition);
//                     });
//             } else {
//                 var cell = $(".filters th").eq(
//                     $(api.column(colIdx).header()).index()
//                 );
//                 $(cell).html("");
//             }
//         });
// };

$("#tbContacts thead tr")
    .clone(true)
    .addClass("filters")
    .appendTo("#tbContacts thead");

$(document).ready(function () {
    var maxRetries = 3; // Número máximo de reintentos
    var retryCount = 0; // Contador de reintentos

    // Configuración de DataTable para contactss
    var table = $("#tbContacts").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "contactsAll",
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
                    console.log(
                        "Reintentando... (Intento " +
                            retryCount +
                            " de " +
                            maxRetries +
                            ")"
                    );
                    setTimeout(function () {
                        table.ajax.reload(); // Recarga los datos de la tabla
                    }, 1000); // Espera 1 segundo antes de reintentar
                } else {
                    // Mostrar un mensaje de error si se agotaron los reintentos
                    Swal.fire({
                        icon: "error",
                        title: "Error al cargar datos",
                        text: "No se pudo cargar la información después de varios intentos.",
                    });
                }
            },
        },
        orderCellsTop: true,
        fixedHeader: true,
        columns: columns, // Define las columnas según tu configuración
        dom: "lrtip", // Configura los controles de la tabla (filtro, paginación, etc.)
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

$(document).ready(function () {
    $("#marcarByGroup").on("click", function () {
        $.ajax({
            url: "groupsWithContacts", // Cambia esta URL a la ruta que devuelve tus grupos
            method: "GET",
            success: function (data) {
                // Verifica que 'data' sea un array
                let options = `
                    <option value="-1">Marcar Todos los Grupos</option>
                    
                    <option value="-2">Desmarcar Todos los Grupos</option>
                    `;

                // Usa forEach para construir las opciones del select
                data.groupSends.forEach((group) => {
                    if (group.id && group.name) {
                        options += `<option value="${group.id}">${group.name}</option>`;
                    } else {
                        console.error("Objeto sin id o name:", group);
                    }
                });

                Swal.fire({
                    title: "Marcar por grupo",
                    html: `<select id="groupSelect" style="width: 100%; padding: 5px; border-radius: 4px;">
                               ${options}
                           </select>`,
                    showCancelButton: true,
                    confirmButtonText: "Cambiar Estado",
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    preConfirm: () => {
                        const selectedGroup = $("#groupSelect").val();
                        return selectedGroup;
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const groupId = result.value;
                        console.log("Grupo seleccionado:", groupId);

                        // Envía el ID del grupo a la API
                        $.ajax({
                            url: `stateSendByGroup/${groupId}`, // Cambia esta URL si es necesario
                            method: "GET", // O 'POST' dependiendo de cómo esté configurada tu API
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Éxito",
                                    text: "Contactos Seleccionados de forma Exitosa",
                                });
                                $("#tbContacts").DataTable().ajax.reload();
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text:
                                        xhr.responseJSON.error ||
                                        "No se pudo actualizar el estado.",
                                });
                            },
                        });
                    }
                });

                // Inicializa Select2
                $("#groupSelect").select2({
                    placeholder: "Selecciona un grupo",
                    allowClear: true,
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudieron cargar los grupos.",
                });
            },
        });
    });
});
