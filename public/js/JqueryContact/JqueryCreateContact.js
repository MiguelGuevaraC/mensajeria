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
        method: "GET",
        success: function (response) {
            var table = $("#tbContacts").DataTable();
            table.column(1).search(null, true, false).draw();
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
        // Realiza la solicitud Ajax a summarySend
        $.ajax({
            url: "summarySend", // Asegúrate de que esta URL apunte correctamente a tu API
            method: "GET",
            success: function (response) {
                // Extrae los datos de la respuesta
                const groups = response.arrayGroups; // Datos devueltos con nombre del grupo y cantidad
                const totalGroups = response.countTotalgroupSends; // Total de grupos
                const totalContacts = response.countTotalContact; // Total de contactos

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
                    </div>`;

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

                tableContent += `</tbody></table>`;


                Swal.fire({
                    title: "Resumen de Envío",
                    html: summaryHtml + tableContent,
                    width: "600px",
                    showCancelButton: true,
                    confirmButtonText: "Enviar Mensajes",
                    confirmButtonColor: "green",
                    cancelButtonColor: "#d33",
                    didRender: () => {
                        // Hacer la tabla responsive
                        $(".swal2-popup").css("overflow-x", "auto");
                        $(".viewGroupBtn").on("click", function () {
                            const groupId = $(this).data("id");
                        
                            // Aquí realizarías una solicitud Ajax para obtener los contactos del grupo usando el ID
                            $.ajax({
                                url: `contactsForGroup/${groupId}`, // Cambia esta URL según tu implementación
                                method: 'GET',
                                success: function(response) {
                                    const contacts = response.arrayContactsByGroup; // Suponiendo que la respuesta tiene un array de contactos
                        
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
                        
                                    contacts.forEach(contact => {
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
                                        width: '600px',
                                        showCancelButton: true,
                                        confirmButtonText: 'Cerrar',
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        didClose: () => {
                                            // Cuando se cierra el modal, abrir el resumen
                                            $("#contactsForSend").click(); // Llama al evento original
                                        },
                                        didRender: () => {
                                            // Acción para el botón "Eliminar"
                                            $(".deleteContactBtn").on("click", function () {
                                                const contactId = $(this).data("id");
                                                Swal.fire({
                                                    title: '¿Estás seguro?',
                                                    text: 'Este contacto será deshabilitado.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Deshabilitar',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        // Llamar a la API para deshabilitar el contacto
                                                        $.ajax({
                                                            url: `stateSend/${contactId}`, // Cambia esta URL según tu implementación
                                                            method: 'GET',
                                                            success: function() {
                                                                Swal.fire('Deshabilitado', 'Se desmarcó con Éxito', 'success');
                                                                $("#tbContacts").DataTable().ajax.reload();
                                                            },
                                                            error: function() {
                                                                Swal.fire('Error', 'No se pudo deshabilitar el contacto.', 'error');
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                        }
                                    });
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se pudo obtener los contactos del grupo.',
                                    });
                                }
                            });
                        });

                        // Acción para el botón "Deshabilitar"
                        $(".deleteGroupBtn").on("click", function () {
                            const groupId = $(this).data("id");
                            Swal.fire({
                                title: '¿Estás seguro?',
                                text: 'Este grupo será deshabilitado.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Deshabilitar',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Llamar a la API para deshabilitar el grupo
                                    $.ajax({
                                        url: `disabledSendByGroup/${groupId}`, // Cambia esta URL según tu implementación
                                        method: 'GET',
                                        success: function() {
                                            Swal.fire('Deshabilitado', 'Grupo deshabilitado con éxito', 'success');
                                            $("#tbContacts").DataTable().ajax.reload(); // Recargar la tabla si es necesario
                                        },
                                        error: function() {
                                            Swal.fire('Error', 'No se pudo deshabilitar el grupo.', 'error');
                                        }
                                    });
                                }
                            });
                        });
                        
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar solicitud AJAX a la API sendApi
                        $.ajax({
                            url: 'sendApi', // Cambia esta URL si es necesario
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token en tu meta
                            },
                            data: {
                                // Agrega aquí los datos que necesitas enviar
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: 'Mensajes enviados con éxito.',
                                });
                                // Aquí puedes hacer otras acciones, como recargar tablas o actualizar el UI
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.error || 'No se pudo enviar los mensajes.',
                                });
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


