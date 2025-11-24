<div class="modal fade" id="modalEstado" tabindex="-1" aria-labelledby="modalMostrarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Estados</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de estados</a></h4>
                <form id="formEstado">
                    <!-- ID estado (campo oculto) -->
                    <input type="hidden" name="id_estado" id="id_estado">
                    
                    <!-- SECCIÓN 1: Descripción Estado -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Descripción de Estado</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="desc_estado" class="form-label">Descripción: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="desc_estado" id="desc_estado" maxlength="100" placeholder="Descripción de estado...">
                                    <div class="invalid-feedback small-invalid-feedback">Solo letras y espacios (mínimo 3 caracteres y máximo de 100)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Peso Estado -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">Peso de Estado</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="peso_estado" class="form-label">Peso de estado: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="peso_estado" id="peso_estado" maxlength="14" placeholder="Peso estado">
                                    <div class="invalid-feedback small-invalid-feedback">Solo números, formato incorrecto</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Estado Predeterminado -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">Estado Predeterminado</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="predeterminado" class="form-label">Estado Predeterminado:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="predeterminado" name="predeterminado">
                                        <label class="form-check-label" for="predeterminado">
                                            Marcar como predeterminado
                                        </label>
                                    </div>
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
