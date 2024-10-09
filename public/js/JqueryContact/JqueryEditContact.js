// Función que muestra el modal y realiza la petición AJAX
function editRol(id) {
    // Mostrar el modal
    $("#modalUpdateContact").modal("show");

    // Hacer una solicitud AJAX para obtener los datos del contacto por grupo
    $.ajax({
        url: "contactByGroup/" + id, // La ruta para obtener los datos del contacto
        method: "GET", // Tipo de solicitud
        success: function (response) {
            // Llenar los campos del formulario con los datos recibidos en la respuesta
            $("#idEContact").val(id); // ID oculto del contacto
            $("#contactId").val(response.contact.id); // ID oculto del contacto
            $("#documentNumber").val(response.contact.documentNumber);
            $("#names").val(response.contact.names);
            $("#telephone").val(response.contact.telephone);
            $("#address").val(response.contact.address);
            $("#concept").val(response.contact.concept);
            $("#amount").val(response.contact.amount);
            $("#dateReference").val(response.contact.dateReference);

            // Mostrar el modal una vez que los datos han sido cargados
            $("#modalUpdateContact").modal("show");
        },
        error: function (xhr) {
            // Manejar el error en caso de que ocurra
            console.error(
                "Error al obtener los datos del contacto: ",
                xhr.responseText
            );
            alert("Hubo un problema al cargar los datos del contacto.");
        },
    });
}
let idContactByGroupGlobal=null; // Variable global para almacenar el idContactByGroup

function addProgramming(idContactByGroup) {
    idContactByGroupGlobal = idContactByGroup; // Asignar a la variable global

    // Realizar la solicitud AJAX para obtener la información del contacto por grupo
    $.ajax({
        url: `showContactForAddProgramming/${idContactByGroup}`,
        type: "GET",
        success: function (contactResponse) {
            // Obtener los datos de contacto
            const grupo = contactResponse.contactByGroup.group_send.name;
            const nombre = contactResponse.contactByGroup.contact.names;
            const telefono = contactResponse.contactByGroup.contact.telephone;
            const programmingResponse = contactResponse.programmings;

            if (programmingResponse.length === 0) {
                Swal.fire({
                    icon: "info",
                    title: "Sin programaciones",
                    text: "No hay programaciones pendientes disponibles.",
                });
                return; // Detener el flujo aquí si no hay programaciones
            }

            // Crear las opciones del selector de programación
            let optionsHtml = "";
            programmingResponse.forEach(function (programming, index) {
                optionsHtml += `<option value="${programming.id}" ${index === 0 ? "selected" : ""}>${programming.dateProgram + " | " + programming.message_whasapp.title}</option>`;
            });

            // Reemplazar los datos del HTML con la respuesta del servidor
            const summaryHtml = `
            <div style="display: flex; flex-wrap: wrap; justify-content: space-around; margin-bottom: 10px;">
                <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
                    <i class="fas fa-users" style="font-size: 3vw; color: #3085d6;"></i>
                    <p style="margin: 5px 0;"><b>Grupo:</b><br>${grupo}</p>
                </div>
                <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
                    <i class="fas fa-user" style="font-size: 3vw; color: #3085d6;"></i>
                    <p style="margin: 5px 0;"><b>Nombre:</b><br>${nombre}</p>
                </div>
                <div style="text-align: center; flex: 1 1 150px; margin-bottom: 10px;">
                    <i class="fas fa-phone-alt" style="font-size: 3vw; color: #3085d6;"></i>
                    <p style="margin: 5px 0;"><b>Teléfono:</b><br>${telefono}</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <label for="selectProgramming"><b>Seleccionar programación:</b></label><br>
                <select id="selectProgramming" style="padding: 5px; width: 70%; margin-top: 10px;">
                    ${optionsHtml}
                </select>
            </div>
            `;

            // Mostrar SweetAlert con el resumen de contactos, el selector de programación y el botón Agregar
            Swal.fire({
                title: "Agregar a una Programación",
                html: `
                        <div style="padding:0px 15px;max-height: 300px; overflow-y: auto;">
                            ${summaryHtml}
                        </div>
                        <div style="text-align: center; margin-top: 20px;">
                            <button id="btnAddProgramming" class="swal2-confirm swal2-styled" style="background-color: #00b027; color: white; padding: 10px 20px; border-radius: 5px;">
                                Agregar
                            </button>
                        </div>
                    `,
                showCloseButton: true, // Muestra el botón de cerrar
                showCancelButton: false, // No mostrar el botón de cancelar
                showConfirmButton: false, // No mostrar el botón de confirmación
            });
        },
        error: function (xhr, status, error) {
            console.error(
                "Error en la solicitud AJAX de contacto por grupo:",
                error
            );
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "No se pudo obtener la información del contacto.",
            });
        },
    });
}

// Función separada para manejar el evento click del botón "Agregar"
$(document).on('click', '#btnAddProgramming', function () {
    const selectedProgrammingId = $('#selectProgramming').val();
 
    // Realizar la solicitud AJAX para agregar el detalle de programación
    $.ajax({
        url: `addDetailProgramming`,
        type: "GET", // Si usas GET
        data: {
            idContactByGroup: idContactByGroupGlobal, // Usar la variable global
            idProgramming: selectedProgrammingId
        },
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Agregado',
                text: 'El detalle de la programación fue agregado exitosamente.',
            });
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo agregar el detalle de la programación.',
            });
        }
    });
});
