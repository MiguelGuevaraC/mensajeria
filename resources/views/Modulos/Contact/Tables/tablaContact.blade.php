<style>
    /* Contenedor centrado del checkbox */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Ocultar el checkbox nativo */
    .styled-checkbox {
        display: none;
    }

    /* Estilos visuales del checkbox personalizado */
    .checkbox-label {
        width: 15px;
        height: 15px;
        background-color: #f0f4f8;
        border-radius: 4px;
        border: 2px solid #8da9c4;
        cursor: pointer;
        transition: background-color 0.3s, box-shadow 0.2s, transform 0.2s;
    }

    /* Efecto hover con sombra suave */
    .checkbox-label:hover {
        background-color: #cfe0f5;
        box-shadow: 0 0 5px rgba(0, 0, 128, 0.2);
    }

    /* Estilo para checkbox marcado */
    .styled-checkbox:checked+.checkbox-label {
        background-color: #007bff;
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
    }

    /* Efecto de interacción cuando se presiona */
    .checkbox-label:active {
        transform: scale(0.95);
    }
</style>
<table id="tbContacts" class="table table-striped shadow-lg mt-4" style="width:100%">
    <thead>
        <tr class="custom-header-bg">
            <th scope="col">id</th>
            <th scope="col">Marcar</th>
            <th scope="col">Grupo</th>
            <th scope="col">Contacto</th>

            <th scope="col">Usuario</th>

            <th scope="col">F. Registro</th>

            <th scope="col">Acciones</th>
        </tr>
    </thead>

</table>
