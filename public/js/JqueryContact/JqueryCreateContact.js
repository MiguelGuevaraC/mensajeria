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
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF
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
        var idMensaje='';
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
                <div style="display: flex; justify-content: space-around; margin-bottom: 15px;">
                    <div style="text-align: center;">
                        <i class="fas fa-users" style="font-size: 24px; color: #3085d6;"></i>
                        <p style="margin: 5px 0;">${totalGroups} Grupos</p>
                    </div>
                    <div style="text-align: center;">
                        <i class="fas fa-paper-plane" style="font-size: 24px; color: #3085d6;"></i>
                        <p style="margin: 5px 0;">${totalContacts} Envíos</p>
                    </div>
                </div>
 
            `;

                // Construir tabla con datos
                let tableContent = `
                    <table style="width:100%; text-align: left; border-collapse: collapse;" class="swal2-table">
                        <thead>
                            <tr>
                                <th style="padding: 8px; border: 1px solid #ddd;">Grupo</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Cantidad</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>`;

                groups.forEach((group) => {
                    tableContent += `
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">${group.groupName}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">${group.contactCount}</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">
                                <button class="viewGroupBtn" data-id="${group.idGroupSend}" style="padding: 5px 10px; background-color: #3085d6; color: white; border: none; border-radius: 4px; cursor: pointer;">Ver</button>
                                <button class="deleteGroupBtn" data-id="${group.idGroupSend}" style="padding: 5px 10px; background-color: #d33; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });

                tableContent += `</tbody></table><br>`;
                tableContent += `
                <div class="form-group mb-4">
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
    data-id="${messages[0]?.id || ''}">
    Ver Mensaje
</button>


                        </div>
                    </div>
                    <div class="error-message mt-2"></div>
                </div>`;

                Swal.fire({
                    title: "Resumen de Envío",
                    html: summaryHtml + tableContent,
                    width: "600px",
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

                        return { mensajeId: mensajeId }; // Retorna el valor para usarlo en el then
                    },
                    didRender: () => {
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
                                        <table style="width:100%; text-align: left; border-collapse: collapse;" class="swal2-table">
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

                                    contactsTableContent += `</tbody></table>`;

                                    // Mostrar SweetAlert con los envíos del grupo
                                    Swal.fire({
                                        title: `Grupo: ${response.groupName}`,
                                        html: contactsTableContent,
                                        width: "600px",
                                        showCancelButton: true,
                                        confirmButtonText: "Cerrar",
                                        confirmButtonColor: "#3085d6",
                                        cancelButtonColor: "#d33",
                                        didClose: () => {
                                            // Cuando se cierra el modal, abrir el resumen
                                            $("#contactsForSend").click(); // Llama al evento original
                                        },
                                        didRender: () => {
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
                                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF
                                                                },
                                                                success: function () {
                                                                    Swal.fire(
                                                                        "Deshabilitado",
                                                                        "Se desmarcó con Éxito",
                                                                        "success"
                                                                    );
                                                                    $("#tbContacts").DataTable().ajax.reload(); // Recargar la tabla
                                                                    $(".viewGroupBtn").click(); // Hacer clic en el botón del grupo
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
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF
                                        },
                                        success: function () {
                                            Swal.fire(
                                                "Deshabilitado",
                                                "Grupo deshabilitado con éxito",
                                                "success"
                                            );
                                            $("#tbContacts").DataTable().ajax.reload(); // Recargar la tabla si es necesario
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
                        idMensaje= result.value.mensajeId;
                        Swal.fire({
                            title: "¿Confirmar el envío?",
                            html: `
                                <div style="text-align: center;">
                                    <i class="fas fa-paper-plane" style="font-size: 24px; color: #3085d6;"></i>
                                    <p style="margin: 5px 0;">Se han registrado ${totalContacts} envíos</p>
                                    <p style="color: #555;">¿Estás seguro de que deseas proceder?</p>
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
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Asegúrate de tener el token en tu meta
                                    },
                                    data: {
                                        message_id: idMensaje,
                                    },
                                    beforeSend: function () {
                                        // Inicializar las variables antes de enviar
                                        totalEnviados = 0;
                                        totalExitosos = 0;
                                        totalErrores = 0;
                                
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
                                            }
                                        });
                                    },
                                    success: function (response) {
                                        let totalMessages = response.totalEnviados; // Total de mensajes a enviar
                                        const interval = setInterval(() => {
                                            // Simulación de envío de mensajes
                                            if (totalEnviados < totalMessages) {
                                                totalEnviados++;
                                                totalExitosos += (totalEnviados % 2 === 0) ? 1 : 0; // Simulación de éxitos
                                                totalErrores += (totalEnviados % 3 === 0) ? 1 : 0; // Simulación de errores
                                
                                                // Actualizar el contenido de la ventana emergente
                                                $('#totalEnviados').text(totalEnviados);
                                                $('#totalExitosos').text(totalExitosos);
                                                $('#totalErrores').text(totalErrores);
                                                $('#progressBar').val(calculateProgress(totalEnviados, totalMessages));
                                            }
                                
                                            // Verificar si todos los mensajes han sido enviados
                                            if (totalEnviados >= totalMessages) {
                                                clearInterval(interval);
                                                Swal.update({
                                                    title: 'Envío completado',
                                                    html: `
                                                        <div style="font-size: 20px; text-align: center;">
                                                            <p><strong>Total Enviados:</strong> ${totalEnviados}</p>
                                                            <p><strong>Total Éxitos:</strong> ${totalExitosos}</p>
                                                            <p><strong>Total Errores:</strong> ${totalErrores}</p>
                                                        </div>
                                                        <a href="send-report" target="_blank" class="btn btn-primary" style="display: block; margin-top: 10px; text-align: center;">Ver envíos</a>
                                                    `,
                                                    icon: totalErrores > 0 ? 'error' : 'success',
                                                    showConfirmButton: true,
                                                });
                                            }
                                        }, 500);
                                
                                        function calculateProgress(totalEnviados, totalMessages) {
                                            return Math.min((totalEnviados / totalMessages) * 100, 100); // Asegura que el valor no supere el 100
                                        }
                                    },
                                    error: function (xhr) {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            text: xhr.responseJSON.error || "No se pudo enviar los mensajes.",
                                        });
                                    }
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
