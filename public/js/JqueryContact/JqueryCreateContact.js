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
                    title: "Por favor espera...",
                    text: "Estamos procesando tu solicitud.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Ajustar el z-index para que SweetAlert esté por encima del modal
                        $(".swal2-container").css("z-index", "2000");
                    },
                });

                // Realizar la solicitud AJAX
                $.ajax({
                    url: "importExcel", // Ajusta la ruta según tu configuración de Laravel
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // console.log(response);
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
                            icon: "error",
                            title: "Error",
                            text: "Hubo un problema al procesar la solicitud.",
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
                icon: "warning",
                title: "Archivo no seleccionado",
                text: "Por favor, selecciona un archivo Excel.",
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

$("#tbContacts").on("change", "input.checkCompro", function () {
    var checkbox = $(this);
    var id = checkbox.val(); // Obtener el ID del registro desde el valor del checkbox
    var isChecked = checkbox.is(":checked");

    $.ajax({
        url: "stateSend/" + id,
        method: "PUT", // Cambiar a método PUT
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Incluir el token CSRF
        },
        success: function (response) {
            var table = $("#tbContacts").DataTable();
            table.column(1).search(null, true, false).draw(); // Actualizar la tabla
        },
        error: function (xhr) {
            // Ocultar el modal de espera y mostrar mensaje de error
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al actualizar el estado",
            });
        },
    });
});

$(document).ready(function () {
    $("#contactsForSend").on("click", function () {
        var idMensaje = "";
        // Realiza la solicitud Ajax a summarySend
        $.ajax({
            url: "summarySend", // Asegúrate de que esta URL apunte correctamente a tu API
            method: "GET",
            success: function (response) {
                // Extrae los datos de la respuesta
                const groups = response.arrayGroups; // Datos devueltos con nombre del grupo y cantidad
                const totalGroups = response.countTotalgroupSends; // Total de grupos
                const totalContacts = response.countTotalContact; // Total de contactos
                const messages = response.mensajes;
                // Resumen con íconos Font Awesome
                const summaryHtml = `
                <div style="display: flex; justify-content: space-around; margin-bottom: 10px;">
                    <div style="text-align: center;">
                        <i class="fas fa-users" style="font-size: 19px; color: #3085d6;"></i>
                        <p style="margin: 5px 0;">${totalGroups} Grupos</p>
                    </div>
                    <div style="text-align: center;">
                        <i class="fas fa-paper-plane" style="font-size: 19px; color: #3085d6;"></i>
                        <p style="margin: 5px 0;">${totalContacts} Envíos</p>
                    </div>
                     <div style="text-align: center;">
                      
                        <div class="toggleContainer">

                       <input type="checkbox" id="switch" class="toggle">


                        <label for="switch" class="switch"></label>
  <p style="margin: 5px 0;">Programar</p>
                        </div> 
                    </div>
                </div>
         <div id="dateTimeContainer" style="display: none; margin-top: 10px;">
                <label for="sendDateTime"><b>Seleccionar fecha y hora de envío:</b></label>
                <input type="datetime-local" id="sendDateTime" style="width: 100%; padding: 5px;">
            </div><br>
            `;

                // Construir tabla con datos
                let tableContent = `
                    <table id="tablaPorGrupos" style="width:100%; text-align: left; border-collapse: collapse;" class="swal2-table">
                        <thead>
                            <tr>
                                <th style="text-align:center;padding: 2px; border: 1px solid #ddd;">Grupo</th>
                                <th style="text-align:center;padding: 2px; border: 1px solid #ddd;">Cantidad</th>
                                <th style="text-align:center;padding: 2px; border: 1px solid #ddd;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>`;

                groups.forEach((group) => {
                    tableContent += `
                        <tr>
                            <td style="padding: 5px; border: 1px solid #ddd;">${group.groupName}</td>
                            <td style="padding: 5px; border: 1px solid #ddd;">${group.contactCount}</td>
                            <td style="padding: 5px; border: 1px solid #ddd;">
                                <button class="viewGroupBtn" data-id="${group.idGroupSend}" style="font-size:10px;padding: 3px 7px; background-color: #3085d6; color: white; border: none; border-radius: 4px; cursor: pointer;">Ver</button>
                                <button class="deleteGroupBtn" data-id="${group.idGroupSend}" style="padding: 3px 7px; background-color: #d33; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    <i style="font-size:10px" class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });

                tableContent += `</tbody></table>`;
                tableContent += `
                <div class="form-group ">
                    <label for="message_id" class="form-label"><b>Mensaje:</b></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <select name="message_id" id="message_id" class="form-control select2" required>
                            ${messages
                                .map(
                                    (msg) =>
                                        `<option value="${msg.id}">${msg.title}</option>`
                                )
                                .join("")}
                        </select>
                        <div class="input-group-append">
                            <button style="background-color: green;" class="btn btn-outline btn-primary btonNuevoMessage" type="button">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                      <button id="btonShowView" class="btn btn-warning" 
    data-id="${messages[0]?.id || ""}">
    Ver Mensaje
</button>


                        </div>
                    </div>
                    
                </div>`;

                Swal.fire({
                    title: "Resumen de Envío",
                    html: `
        <div style="max-height: 300px; overflow-y: auto;">
            ${summaryHtml + tableContent}
    
        </div>
    `,
                    width: "1000px",
                    showCancelButton: true,
                    confirmButtonText: "Enviar Mensajes",
                    confirmButtonColor: "green",
                    cancelButtonColor: "#d33",

                    preConfirm: () => {
                        const mensajeId = $("#message_id").val();

                        if (!mensajeId || mensajeId == "") {
                            Swal.showValidationMessage(
                                "El campo Mensaje es obligatorio"
                            );
                            return false;
                        }

                        const isProgram = $("#switch").is(":checked");
                        const sendDateTime = $("#sendDateTime").val() ?? "";

                        if (isProgram) {
                            if (
                                sendDateTime == "" ||
                                sendDateTime == null ||
                                !sendDateTime
                            ) {
                                Swal.showValidationMessage(
                                    "La fecha de envío es Obligatorio"
                                );
                                return false;
                            }
                        }

                        return {
                            mensajeId: mensajeId,
                            isProgram: isProgram,
                            sendDateTime: sendDateTime,
                        };
                    },
                    didRender: () => {
                        $("#switch").change(function () {
                            const isChecked = $(this).is(":checked");
                            if (isChecked) {
                                const now = new Date();

                                console.log(now);
                                now.setMinutes(now.getMinutes() + 20);

                                // Formatear la fecha y hora para input datetime-local
                                const year = now.getFullYear();
                                const month = String(
                                    now.getMonth() + 1
                                ).padStart(2, "0"); // Los meses son 0-indexados
                                const day = String(now.getDate()).padStart(
                                    2,
                                    "0"
                                );
                                const hours = String(now.getHours()).padStart(
                                    2,
                                    "0"
                                );
                                const minutes = String(
                                    now.getMinutes()
                                ).padStart(2, "0");

                                const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`; // 'YYYY-MM-DDTHH:mm'

                                $("#sendDateTime").val(formattedDate);
                                $("#dateTimeContainer").show();
                            } else {
                                $("#sendDateTime").val("");
                                $("#dateTimeContainer").hide();
                            }
                        });

                        $("#sendDateTime").val();
                        $("#tablaPorGrupos").DataTable({
                            paging: true, // Activa la paginación
                            lengthMenu: [3], // Opciones de longitud de página
                            searching: true, // Habilita búsqueda
                            info: true, // Muestra información de la tabla

                            dom: "frtp",
                            language: {
                                emptyTable:
                                    "No hay datos disponibles en la tabla", // Mensaje cuando no hay datos
                                info: "Mostrando _START_ a _END_ de _TOTAL_ entradas", // Mensaje de información
                                infoEmpty: "No se encontraron entradas", // Mensaje cuando no hay entradas
                                infoFiltered:
                                    "(filtrado de _MAX_ entradas totales)", // Mensaje filtrado
                                loadingRecords: "Cargando...", // Mensaje de carga
                                processing: "Procesando...", // Mensaje de procesamiento
                                search: "Buscar:", // Etiqueta de búsqueda
                                zeroRecords: "No se encontraron coincidencias", // Mensaje si no se encuentran coincidencias
                                paginate: {
                                    first: "Primero", // Texto del primer botón de paginación
                                    last: "Último", // Texto del último botón de paginación
                                    next: "Siguiente", // Texto del botón siguiente
                                    previous: "Anterior", // Texto del botón anterior
                                },
                            },
                        });
                        // Hacer la tabla responsive
                        $(".swal2-popup").css("overflow-x", "auto");

                        $("#message_id").on("change", function () {
                            let selectedId = $(this).val();
                            $("#btonShowView").attr("data-id", selectedId);
                        });

                        $(".btonNuevoMessage").on("click", function () {
                            Swal.close(); // Cierra la alerta de SweetAlert
                            $("#modalNuevoMensaje").modal("show"); // Abre el modal

                            // Evento que se dispara cuando se cierra el modal
                        });

                        $(".viewGroupBtn").on("click", function () {
                            const groupId = $(this).data("id");

                            // Aquí realizarías una solicitud Ajax para obtener los contactos del grupo usando el ID
                            $.ajax({
                                url: `contactsForGroup/${groupId}`, // Cambia esta URL según tu implementación
                                method: "GET",
                                success: function (response) {
                                    const contacts =
                                        response.arrayContactsByGroup; // Suponiendo que la respuesta tiene un array de contactos

                                    // Construir la tabla con los detalles del grupo
                                    let contactsTableContent = `
                                        <div style="max-height: 400px; overflow-y: auto;"> <!-- Contenedor con scroll -->
                                            <table id="contactsTable" style="width:100%; text-align: left; border-collapse: collapse;" class="swal2-table">
                                                <thead>
                                                    <tr>
                                                        <th style="padding: 8px; border: 1px solid #ddd;">Contacto</th>
                                                        <th style="padding: 8px; border: 1px solid #ddd;">Teléfono</th>
                                                        <th style="padding: 8px; border: 1px solid #ddd;">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>`;

                                    contacts.forEach((contact) => {
                                        contactsTableContent += `
                                            <tr>
                                                <td style="padding: 8px; border: 1px solid #ddd;">${contact.name}</td>
                                                <td style="padding: 8px; border: 1px solid #ddd;">${contact.telephone}</td>
                                                <td style="padding: 8px; border: 1px solid #ddd;">
                                                    <button class="deleteContactBtn" data-id="${contact.idContactByGroup}" style="padding: 5px 10px; background-color: #d33; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                                        <i class="fas fa-trash"></i> 
                                                    </button>
                                                </td>
                                            </tr>`;
                                    });

                                    contactsTableContent += `</tbody></table></div>`; // Cerrar el contenedor del scroll

                                    // Mostrar SweetAlert con los envíos del grupo
                                    Swal.fire({
                                        title: `Grupo: ${response.groupName}`,

                                        html: `
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            ${contactsTableContent}
                                    
                                        </div>
                                    `,
                                        width: "700px",
                                        showCloseButton: true, // Oculta el botón de cerrar (X)
                                        showCancelButton: false, // Asegúrate de que esto esté configurado como false
                                        showConfirmButton: false,
                                        confirmButtonText: "", // Sin texto para el botón de confirmación
                                        // No permite cerrar haciendo clic fuera del cuadro de diálogo

                                        didClose: () => {
                                            // Cuando se cierra el modal, abrir el resumen
                                            $("#contactsForSend").click(); // Llama al evento original
                                        },
                                        didRender: () => {
                                            // Inicializa DataTable
                                            $("#contactsTable").DataTable({
                                                paging: true, // Activa la paginación
                                                lengthMenu: [5, 10, 25, 50], // Opciones de longitud de página
                                                searching: true, // Habilita búsqueda
                                                info: true, // Muestra información de la tabla

                                                dom: "frtip",
                                                language: {
                                                    emptyTable:
                                                        "No hay datos disponibles en la tabla", // Mensaje cuando no hay datos
                                                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas", // Mensaje de información
                                                    infoEmpty:
                                                        "No se encontraron entradas", // Mensaje cuando no hay entradas
                                                    infoFiltered:
                                                        "(filtrado de _MAX_ entradas totales)", // Mensaje filtrado
                                                    loadingRecords:
                                                        "Cargando...", // Mensaje de carga
                                                    processing: "Procesando...", // Mensaje de procesamiento
                                                    search: "Buscar:", // Etiqueta de búsqueda
                                                    zeroRecords:
                                                        "No se encontraron coincidencias", // Mensaje si no se encuentran coincidencias
                                                    paginate: {
                                                        first: "Primero", // Texto del primer botón de paginación
                                                        last: "Último", // Texto del último botón de paginación
                                                        next: "Siguiente", // Texto del botón siguiente
                                                        previous: "Anterior", // Texto del botón anterior
                                                    },
                                                },
                                            });

                                            // Acción para el botón "Eliminar"
                                            $(".deleteContactBtn").on(
                                                "click",
                                                function () {
                                                    const contactId =
                                                        $(this).data("id");
                                                    Swal.fire({
                                                        title: "¿Estás seguro?",
                                                        text: "Este contacto será deshabilitado.",
                                                        icon: "warning",
                                                        showCancelButton: true,
                                                        confirmButtonColor:
                                                            "#d33",
                                                        cancelButtonColor:
                                                            "#3085d6",
                                                        confirmButtonText:
                                                            "Deshabilitar",
                                                    }).then((result) => {
                                                        if (
                                                            result.isConfirmed
                                                        ) {
                                                            // Llamar a la API para deshabilitar el contacto
                                                            $.ajax({
                                                                url: `stateSend/${contactId}`, // Cambia esta URL según tu implementación
                                                                method: "PUT", // Cambiar a método PUT
                                                                headers: {
                                                                    "X-CSRF-TOKEN":
                                                                        $(
                                                                            'meta[name="csrf-token"]'
                                                                        ).attr(
                                                                            "content"
                                                                        ), // Incluir el token CSRF
                                                                },
                                                                success:
                                                                    function () {
                                                                        Swal.fire(
                                                                            "Deshabilitado",
                                                                            "Se desmarcó con Éxito",
                                                                            "success"
                                                                        );
                                                                        $(
                                                                            "#tbContacts"
                                                                        )
                                                                            .DataTable()
                                                                            .ajax.reload(); // Recargar la tabla
                                                                        $(
                                                                            ".viewGroupBtn"
                                                                        ).click(); // Hacer clic en el botón del grupo
                                                                    },
                                                                error: function () {
                                                                    Swal.fire(
                                                                        "Error",
                                                                        "No se pudo deshabilitar el contacto.",
                                                                        "error"
                                                                    );
                                                                },
                                                            });
                                                        }
                                                    });
                                                }
                                            );
                                        },
                                    });
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "No se pudo obtener los contactos del grupo.",
                                    });
                                },
                            });
                        });

                        // Acción para el botón "Deshabilitar"
                        $(".deleteGroupBtn").on("click", function () {
                            const groupId = $(this).data("id");
                            Swal.fire({
                                title: "¿Estás seguro?",
                                text: "Este grupo será deshabilitado.",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#d33",
                                cancelButtonColor: "#3085d6",
                                confirmButtonText: "Deshabilitar",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Llamar a la API para deshabilitar el grupo
                                    $.ajax({
                                        url: `disabledSendByGroup/${groupId}`, // Cambia esta URL según tu implementación
                                        method: "PUT", // Cambiar a método PUT
                                        headers: {
                                            "X-CSRF-TOKEN": $(
                                                'meta[name="csrf-token"]'
                                            ).attr("content"), // Incluir el token CSRF
                                        },
                                        success: function () {
                                            Swal.fire(
                                                "Deshabilitado",
                                                "Grupo deshabilitado con éxito",
                                                "success"
                                            );
                                            $("#tbContacts")
                                                .DataTable()
                                                .ajax.reload(); // Recargar la tabla si es necesario
                                        },
                                        error: function () {
                                            Swal.fire(
                                                "Error",
                                                "No se pudo deshabilitar el grupo.",
                                                "error"
                                            );
                                        },
                                    });
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        idMensaje = result.value.mensajeId;
                        isProgram = result.value.isProgram;
                        sendDateTime = result.value.sendDateTime;

                        // Formatear la fecha en un formato más amigable
                        const formattedDate = new Date(
                            sendDateTime
                        ).toLocaleString("es-ES", {
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                            hour12: false,
                        });

                        Swal.fire({
                            title: "¿Confirmar el envío?",
                            html: `
                                <div style="text-align: center;">
                                    <i class="fas fa-paper-plane" style="font-size: 24px; color: #3085d6;"></i>
                                    <p style="margin: 5px 0;">Se han registrado ${totalContacts} envíos</p>
                                    <p style="color: #555;">¿Estás seguro de que deseas proceder?</p>
                                    ${
                                        isProgram
                                            ? `<p style="font-weight: bold; color: #3085d6;">Programación activada</p>
                                        <p style="font-size: 18px; font-weight: bold; color: #333; margin-top: 10px;">
                                        <i class="fas fa-clock" style="color: #d9534f; margin-right: 5px;"></i>
                                        Fecha de envío: <span style="color: ##d9534f;">${formattedDate}</span>
                                    </p>`
                                            : ""
                                    }
                                    
                                </div>
                            `,
                            icon: "question",
                            showCancelButton: true,
                            confirmButtonText: "Sí, enviar ahora",
                            cancelButtonText: "Revisar de nuevo",
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: "sendApi", // Cambia esta URL si es necesario
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"), // Asegúrate de tener el token en tu meta
                                    },
                                    data: {
                                        message_id: idMensaje,
                                        isProgram: isProgram,
                                        sendDateTime: sendDateTime,
                                    },
                                    beforeSend: function () {
                                        // Inicializar las variables antes de enviar
                                        totalEnviados = 0;
                                        totalExitosos = 0;
                                        totalErrores = 0;

                                        console.log(isProgram);
                                        if (!isProgram) {
                                            // Mostrar alerta de carga
                                            Swal.fire({
                                                title: "Enviando mensajes...",
                                                html: `
        <div style="text-align: center; font-size: 1.2em; margin-bottom: 20px;">
            <div style="font-size: 1.5em; margin-bottom: 10px;">
                <i class="fas fa-paper-plane" style="color: #007bff;"></i>
                Enviados: <span id="totalEnviados" style="font-weight: bold;">${totalEnviados}</span>
            </div>
            <div style="font-size: 1.5em; margin-bottom: 10px;">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                Éxitos: <span id="totalExitosos" style="font-weight: bold;">${totalExitosos}</span>
            </div>
            <div style="font-size: 1.5em;">
                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                Errores: <span id="totalErrores" style="font-weight: bold;">${totalErrores}</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <progress id="progressBar" value="0" max="100" style="width: 100%; height: 25px;"></progress>
        </div>
    `,
                                                icon: "info",
                                                showConfirmButton: false, // Ocultar botón de confirmar
                                                willOpen: () => {
                                                    Swal.showLoading();
                                                },
                                            });
                                        } else {
                                            Swal.fire({
                                                title: "¡Programación realizada con éxito!",
                                                html: `
                                                    <p>La programación se ha realizado correctamente.</p>
                                                    <a href="programming" target="_blank" class="btn btn-primary" style="margin-top: 10px;">
                                                        Ir a ver programaciones
                                                    </a>
                                                `,
                                                icon: "success",
                                                showConfirmButton: false, // Ocultar botón de confirmar
                                                willOpen: () => {
                                                    Swal.showLoading(); // Muestra una animación de carga hasta que el modal esté completamente cargado
                                                },
                                                didOpen: () => {
                                                    Swal.hideLoading(); // Oculta la animación de carga cuando el modal se abre por completo
                                                },
                                            });
                                        }
                                    },
                                    success: function (response) {
                                        let totalMessages =
                                            response.totalEnviados; // Total de mensajes a enviar

                                        // Iniciar un intervalo para simular el envío
                                        const interval = setInterval(() => {
                                            // Aumentar los contadores usando la respuesta de la API
                                            if (totalEnviados < totalMessages) {
                                                totalEnviados++;
                                                totalExitosos =
                                                    response.totalExitosos;
                                                totalErrores =
                                                    response.totalErrores;

                                                // Actualizar los datos mostrados en la ventana emergente
                                                $("#totalEnviados").text(
                                                    totalEnviados
                                                );
                                                $("#totalExitosos").text(
                                                    totalExitosos
                                                );
                                                $("#totalErrores").text(
                                                    totalErrores
                                                );
                                                $("#progressBar").val(
                                                    calculateProgress(
                                                        totalEnviados,
                                                        totalMessages
                                                    )
                                                );
                                            }

                                            // Verificar si todos los mensajes han sido enviados
                                            if (
                                                totalEnviados >= totalMessages
                                            ) {
                                                clearInterval(interval);
                                                Swal.update({
                                                    title: "Envío completado",
                                                    html: `
                                                        <div style="font-size: 20px; text-align: center;">
                                                            <p><strong>Total Enviados:</strong> ${totalEnviados}</p>
                                                            <p><strong>Total Éxitos:</strong> ${totalExitosos}</p>
                                                            <p><strong>Total Errores:</strong> ${totalErrores}</p>
                                                        </div>
                                                        <a href="send-report" target="_blank" class="btn btn-primary" style="display: block; margin-top: 10px; text-align: center;">Ver envíos</a>
                                                    `,
                                                    icon:
                                                        totalErrores > 0
                                                            ? "error"
                                                            : "success",
                                                    showConfirmButton: true,
                                                });
                                            }
                                        }, 500);

                                        // Función para calcular el progreso
                                        function calculateProgress(
                                            totalEnviados,
                                            totalMessages
                                        ) {
                                            return Math.min(
                                                (totalEnviados /
                                                    totalMessages) *
                                                    100,
                                                100
                                            ); // Asegura que el valor no supere el 100
                                        }
                                    },
                                    error: function (xhr) {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            text:
                                                xhr.responseJSON.error ||
                                                "No se pudo enviar los mensajes.",
                                        });
                                    },
                                });
                            } else {
                                $("#contactsForSend").click();
                            }
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo obtener el resumen de envíos.",
                });
            },
        });
    });
});

$(document).ready(function () {
    $("#modalNuevoMensaje").on("hidden.bs.modal", function () {
        // Vuelve a abrir la alerta de resumen de envío
        $("#contactsForSend").click();
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
$(document).on("click", "#btonShowView", function () {
    // Obtén el ID del botón
    var id = $(this).data("id");

    // Realiza la solicitud AJAX
    $.ajax({
        url: "message/showExample/" + id,
        method: "GET",
        success: function (response) {
            // console.log(response);
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
            }).then(() => {
                // Cierra el modal de Sweet Alert y dispara el evento de clic
                $("#contactsForSend").click();
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
