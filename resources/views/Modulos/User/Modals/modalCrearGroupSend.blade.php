
<!-- Modal CREAR -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h2 class="modal-title" id="nuevoModalLabel"><strong>AGREGAR NUEVO USUARIO</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroUsuario" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Empresa -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="company" class="form-label">Empresa:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                    </div>
                                    <select name="company" id="company" class="form-control select2"
                                        required>
                                        <option value="">Selecciona una Empresa</option>
                                        <!-- Opciones se cargarán dinámicamente aquí -->
                                    </select>
                                </div>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        <!-- Tipo de Usuario -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="typeuser" class="form-label">Tipo de Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users-cog"></i></span>
                                    </div>
                                    <select name="typeuser" id="typeuser" class="form-control select2" required>
                                        <option value="">Selecciona un tipo de usuario</option>
                                        <!-- Opciones se cargarán dinámicamente aquí -->
                                    </select>
                                </div>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        <!-- Nombre de Usuario -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="username" class="form-label">Nombre de Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..."
                                        name="username" id="username" required>
                                </div>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        <!-- Contraseña -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="pass" class="form-label">Contraseña:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" placeholder="Escribe aquí..."
                                        name="pass" id="pass" required minlength="8" maxlength="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <input type="checkbox" id="togglePassword">
                                            <label for="togglePassword" class="mb-0" style="cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text text-muted" style="color: white !important;">
                                    La contraseña debe tener al menos 8 caracteres y contener una letra mayúscula, una letra minúscula, y un número.
                                </small>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        
                        <script>
                        document.getElementById('togglePassword').addEventListener('change', function () {
                            const passwordField = document.getElementById('pass');
                            const passwordIcon = this.nextElementSibling.firstElementChild;
                            if (this.checked) {
                                passwordField.type = 'text';
                                passwordIcon.classList.remove('fa-eye');
                                passwordIcon.classList.add('fa-eye-slash');
                            } else {
                                passwordField.type = 'password';
                                passwordIcon.classList.remove('fa-eye-slash');
                                passwordIcon.classList.add('fa-eye');
                            }
                        });
                        </script>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .select2-selection__rendered {
    color: black !important; /* Asegura que el texto seleccionado sea negro */
}
</style>