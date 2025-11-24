<!-- Modal con animación fade -->
<div class="modal fade" id="modalAdjuntos" tabindex="-1" aria-labelledby="modalMostrarAdjuntosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Adjuntos</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de adjuntos</a></h4>

                <form id="formAdjunto">
                    <!-- Campo oculto para ID adjunto -->
                    <input type="hidden" name="id_adjunto" id="id_adjunto">
                    
                    <!-- SECCIÓN 1: Llamada -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Llamada</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="llamada" class="form-label">Llamada: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <select id="id_llamada" name="id_llamada" class="form-control wd-300">
                                        <!-- Vacío, se llenará vía AJAX -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">
                                        Por favor seleccione una llamada.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Nombre Archivo -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">Nombre del Archivo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="nombre_archivo" class="form-label">Nombre Archivo: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="nombre_archivo" id="nombre_archivo" maxlength="255" placeholder="Nombre archivo...">
                                    <div class="invalid-feedback small-invalid-feedback">Nombre de archivo inválido...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Tipo -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0 tx-bold">Tipo de Archivo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="tipo" class="form-label">Tipo: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="tipo" id="tipo" maxlength="20" placeholder="Tipo de archivo...">
                                    <div class="invalid-feedback small-invalid-feedback">Nombre de tipo inválido...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Fecha Subida -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">Fecha de Subida</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="fecha_subida" class="form-label">Fecha de Subida: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input id="fecha_subida" name="fecha_subida" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa" readonly>
                                    </div>
                                    <div class="tx-8 tx-info" id="borrarFechaSubida">Borrar fecha de subida</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div><!-- modal-body -->

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- modal -->
