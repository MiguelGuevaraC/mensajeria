<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditarGroupSend" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h2 class="modal-title" id="editarModalLabel"><strong>EDITAR GRUPO</strong></h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroGroupSendE" enctype="multipart/form-data">
                    <input type="hidden" id="idE">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <!-- Nombre Grupo -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="nameE" class="form-label">Nombre Grupo:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Escribe aquí..."
                                        name="nameE" id="nameE" required>
                                </div>
                                <div class="error-message mt-2"></div>
                            </div>
                        </div>
                        <!-- Comentario -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="commentE" class="form-label">Comentario:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    </div>
                                    <textarea class="form-control" placeholder="Escribe aquí..." name="commentE" id="commentE" required></textarea>
                                </div>
                                <div class="error-message mt-2"></div>
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
