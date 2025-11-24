<!-- Modal Adjuntos -->
<div class="modal fade" id="modalAdjuntos" tabindex="-1" aria-labelledby="modalAdjuntosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Aumenté el tamaño a modal-lg para mejor visualización -->
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white pd-y-15 pd-x-20">
                <h5 class="modal-title tx-14 mg-b-0 tx-uppercase tx-bold">
                    <i class="fas fa-paperclip me-2"></i>Adjuntar Archivo
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Cuerpo -->
            <div class="modal-body pd-20">
                <form id="formAdjuntos" enctype="multipart/form-data" autocomplete="off">
                    <!-- Campo oculto para ID de llamada -->
                    <input type="hidden" name="id_llamada_adjunto" id="id_llamada_adjunto">
                    
                    <!-- Grupo: Selección de archivo -->
                    <div class="row no-gutters mg-t-20">
                        <div class="col-4 col-lg-2">
                            <label for="archivo_adjunto" class="form-label">Archivo <span class="tx-danger">*</span></label>
                        </div>
                        <div class="col-8 col-sm-10">
                            <div class="d-flex align-items-center">
                                <input type="file" 
                                       class="form-control flex-grow-1"
                                       id="archivo_adjunto" 
                                       name="archivo_adjunto[]"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       required
                                       multiple
                                       style="min-width: 200px;"> 
                                <!--
                                <button type="button" 
                                        id="btnLimpiarAdjunto" 
                                        class="btn btn-outline-danger ms-2 flex-shrink-0">
                                    <i class="fa fa-times"></i> Quitar
                                </button>
                                -->
                            </div>
                            <div class="invalid-feedback small-invalid-feedback">
                                Archivo con formato válido (PDF, JPG, PNG) y menos de 5MB
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección de Previews -->
                    <div class="row no-gutters mg-t-20">
                        <div class="col-4 col-lg-2">
                            <label class="form-label">Vista previa</label>
                        </div>
                        <div class="col-8 col-sm-10">
                            <div id="previewAdjuntos" class="d-flex flex-wrap gap-3 mt-2">
                                <!-- Aquí se mostrarán las miniaturas de los archivos seleccionados -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Pie de modal -->
            <div class="modal-footer pd-15">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="submit" 
                        id="btnGuardarAdjunto" 
                        class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>