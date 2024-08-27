<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h2 class="modal-title" id="editarModalLabel"><strong>EDITAR USUARIO</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroUsuarioE" enctype="multipart/form-data">
                    <input type="hidden" id="idE">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Empresa -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="companyE" class="form-label">Empresa:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                    </div>
                                    <select name="companyE" id="companyE" class="form-control select2" required>
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
                                <label for="typeuserE" class="form-label">Tipo de Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users-cog"></i></span>
                                    </div>
                                    <select name="typeuserE" id="typeuserE" class="form-control select2" required>
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
                                <label for="usernameE" class="form-label">Nombre de Usuario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..."
                                        name="usernameE" id="usernameE" required>
                                </div>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        <!-- Contraseña -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="passE" class="form-label">Contraseña:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" placeholder="Escribe aquí..."
                                        name="passE" id="passE" minlength="8" maxlength="100">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <input type="checkbox" id="togglePasswordE">
                                            <label for="togglePasswordE" class="mb-0" style="cursor: pointer;">
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
                        document.getElementById('togglePasswordE').addEventListener('change', function () {
                            const passwordField = document.getElementById('passE');
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
