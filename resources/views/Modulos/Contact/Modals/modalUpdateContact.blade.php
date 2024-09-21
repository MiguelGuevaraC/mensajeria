<!-- Modal Actualizar Contacto -->
<div class="modal fade" id="modalUpdateContact" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h2 class="modal-title" id="updateModalLabel"><strong>ACTUALIZAR CONTACTO</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateContactForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Permite enviar la solicitud PUT -->
                    <!-- Campo oculto para el ID del contacto -->
                    <input type="hidden" name="id" id="contactId">

                    <div class="row">
                        <!-- Número de Documento -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="documentNumber" class="form-label">Número de Documento:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">#</span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="documentNumber" id="documentNumber" >
                                </div>
                            </div>
                        </div>

                        <!-- Nombres -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="names" class="form-label">Nombres:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="names" id="names" required >
                                </div>
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
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="address" id="address" >
                                </div>
                            </div>
                        </div>

                        <!-- Concepto -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="concept" class="form-label">Concepto:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..." name="concept" id="concept" >
                                </div>
                            </div>
                        </div>

                        <!-- Monto -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="amount" class="form-label">Monto:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" class="form-control" placeholder="Escribe aquí..." name="amount" id="amount" step="0.01" >
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de Referencia -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="dateReference" class="form-label">Fecha de Referencia:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="dateReference" id="dateReference" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="idEContact">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
