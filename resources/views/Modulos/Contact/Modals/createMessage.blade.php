<div class="modal fade" id="modalNuevoMensaje" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="color: white" class="modal-title" id="nuevoModalLabel"><strong>CREAR MENSAJE</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="registroMensajeNuevo" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"> <!-- Primera columna -->
                            <div class="form-group">
                                <label for="title">TÍTULO</label>
                                <textarea id="title" class="form-control" rows="2" placeholder="Escribe Aquí...">{{$message->title ?? ''}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="block1">PÁRRAFO 1</label>
                                <textarea id="block1" class="form-control" rows="2" placeholder="Escribe Aquí...">{{$message->block1 ?? ''}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="block2">PÁRRAFO 2</label>
                                <textarea id="block2" class="form-control" rows="2" placeholder="Escribe Aquí...">{{$message->block2 ?? ''}}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6"> <!-- Segunda columna -->
                            <div class="form-group">
                                <label for="block3">PÁRRAFO 3</label>
                                <textarea id="block3" class="form-control" rows="2" placeholder="Escribe Aquí...">{{$message->block3 ?? ''}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="block4">PÁRRAFO 4</label>
                                <textarea id="block4" class="form-control" rows="2" placeholder="Escribe Aquí...">{{$message->block4 ?? ''}}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="fileUpload">SUBIR ARCHIVO (JPG, PNG o PDF)</label>
                                <input type="file" id="fileUpload" name="fileUpload" class="form-control">
                                <p>Opcional</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btonSaveMessage" class="btn btn-success">Guardar</button>
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
