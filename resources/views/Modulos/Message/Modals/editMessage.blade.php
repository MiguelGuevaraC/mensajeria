<div class="modal fade" id="modalNuevoMensajeE" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="color: white" class="modal-title" id="nuevoModalLabel"><strong>EDITAR MENSAJE</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroMensajeNuevoE" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-12">
                            <div id="messageForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                                <div class="form-group">
                                    <label for="titleE">TÍTULO</label>
                                    <textarea id="titleE" class="form-control" rows="2" placeholder="Escribe Aquí..."></textarea>
                                </div>
                
                                <div class="form-group">
                                    <label for="block1E">PÁRRAFO 1</label>
                                    <textarea id="block1E" class="form-control" rows="2" placeholder="Escribe Aquí..."></textarea>
                                </div>
                
                                <div class="form-group">
                                    <label for="block2E">PÁRRAFO 2</label>
                                    <textarea id="block2E" class="form-control" rows="2" placeholder="Escribe Aquí..."></textarea>
                                </div>
                
                                <div class="form-group">
                                    <label for="block3E">PÁRRAFO 3</label>
                                    <textarea id="block3E" class="form-control" rows="2" placeholder="Escribe Aquí..."></textarea>
                                </div>
                
                                <div class="form-group">
                                    <label for="block4E">PÁRRAFO 4</label>
                                    <textarea id="block4E" class="form-control" rows="2" placeholder="Escribe Aquí..."></textarea>
                                </div>
                                <input type="hidden" id="idE" name="idE" class="form-control">

                                <div class="form-group">
                                    <label for="fileUploadE">SUBIR ARCHIVO (JPG,PNG o PDF)</label>
                                    <input type="file" id="fileUploadE" name="fileUpload" class="form-control">
                                    <p>Opcional</p>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btonSaveMessageE" class="btn btn-success">Guardar</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

<!-- Importar las librerías necesarias -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Añadir el soporte de Bootstrap para los modales y las librerías -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
