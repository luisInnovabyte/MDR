<div class="modal fade" id="modalMantenimiento" tabindex="-1" aria-labelledby="modalMostrarVacacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Vacaciones</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de vacaciones</a></h4>
                <form id="formVacacion">
                    <!-- ID VACACION -->
                    <input type="hidden" name="id_vacacion" id="id_vacacion">

                    <!-- SECCIÓN 1: COMERCIAL -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Comercial</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="comercial" class="form-label">Comercial: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <select id="id_comercial" name="id_comercial" class="form-control">
                                        <!-- Se llenará dinámicamente con AJAX -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">
                                        Por favor seleccione un comercial.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: FECHAS -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">Fechas de vacaciones</h5>
                        </div>
                        <div class="card-body">
                            <!-- FECHA INICIO -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="fecha_inicio_modal" class="form-label">Fecha de inicio: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input id="fecha_inicio" name="fecha_inicio" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa" readonly>
                                    </div>
                                    <div class="tx-8 tx-info" id="borrarFechaInicioCarrera">Borrar fecha de inicio de carrera</div>
                                </div>
                            </div>

                            <!-- FECHA FIN -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="fecha_fin_modal" class="form-label">Fecha de fin: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                            </div>
                                        </div>
                                        <input id="fecha_fin" name="fecha_fin" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa" readonly>
                                    </div>
                                    <div class="tx-8 tx-info" id="borrarFechaFinCarrera">Borrar fecha de fin de carrera</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: DESCRIPCIÓN -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">Descripción</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="descripcion" class="form-label">Descripción: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="90" placeholder="Descripción...">
                                    <div class="invalid-feedback small-invalid-feedback">
                                        Solo letras y espacios (mínimo 3 caracteres y máximo de 90)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div><!-- modal-body -->

            <div class="modal-footer">
                <button type="button" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- modal -->
