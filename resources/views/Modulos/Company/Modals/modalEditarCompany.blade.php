<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditarCompany" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h2 class="modal-title" id="editarModalLabel"><strong>EDITAR EMPRESA</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editarCompany" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Indica que se realizará una actualización -->
                    <input type="hidden" name="companyId" id="companyId"> <!-- Campo oculto para el ID de la empresa -->
                    <div class="row">
                        <!-- Número de Documento -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="documentNumberEdit" class="form-label">Número de Documento:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="documentNumber" id="documentNumberEdit" required>
                                    <div class="input-group-append">
                                        <button id="searchEdit" class="btn btn-outline-secondary" type="button" style="color:rgb(0, 0, 0); background:white">
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
                                <label for="businessNameEdit" class="form-label">Razón Social:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="businessName" id="businessNameEdit" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Nombre Comercial -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="tradeNameEdit" class="form-label">Nombre Comercial:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="tradeName" id="tradeNameEdit">
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Dirección -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="addressEdit" class="form-label">Dirección:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="address" id="addressEdit">
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Representante -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="representativeNameEdit" class="form-label">Nombre del Representante:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="representativeName" id="representativeNameEdit" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="telephoneEdit" class="form-label">Teléfono:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="telephone" id="telephoneEdit" required>
                                </div>
                                <div class="error-messageGrupo mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Asegúrate de incluir Font Awesome para los íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
