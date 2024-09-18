<!-- Modal CREAR -->
<div class="modal fade" id="modalNuevoCompany" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header  text-white">
                <h2 class="modal-title" id="nuevoModalLabel"><strong>REGISTRAR NUEVA EMPRESA</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroCompany" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Número de Documento -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="documentNumber" class="form-label">Número de Documento:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="documentNumber" id="documentNumber" required>
                                    <div class="input-group-append">
                                        <button id="search" name="search" class="btn btn-outline-secondary" type="button" style="color:rgb(0, 0, 0); background:white">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Razón Social -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="businessName" class="form-label">Razón Social:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="businessName" id="businessName" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Nombre Comercial -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="tradeName" class="form-label">Nombre Comercial:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="tradeName" id="tradeName">
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Dirección -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="address" class="form-label">Dirección:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="address" id="address" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Representante -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="representativeName" class="form-label">Nombre del Representante:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="representativeName" id="representativeName" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="telephone" class="form-label">Teléfono:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="telephone" id="telephone" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
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

<!-- Asegúrate de incluir Font Awesome para los íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
