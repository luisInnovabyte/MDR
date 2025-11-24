<div class="modal fade" id="modalContacto" tabindex="-1" aria-labelledby="modalMostrarContactosLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Contactos</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de contacto</a></h4>
                <form id="formContacto">
                    <input type="hidden" name="id_contacto" id="id_contacto">

                    <!-- TARJETA 1: INFORMACIÓN DE CONTACTO -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Información de Contacto</h5>
                        </div>
                        <div class="card-body">
                            <!-- LLAMADA -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="llamada" class="form-label">Llamada: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <select id="id_llamada" name="id_llamada" class="form-control" required></select>
                                    <div class="invalid-feedback small-invalid-feedback">Por favor seleccione una llamada.</div>
                                </div>
                            </div>

                            <!-- FECHA/HORA CONTACTO -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="fecha_hora_contacto" class="form-label">Fecha/Hora Contacto: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="input-group" id="fecha_hora_contacto_wrapper">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-calendar tx-16 lh-0 op-6"></i></div>
                                        </div>
                                        <input id="fecha_hora_contacto" name="fecha_hora_contacto" type="text" class="form-control" tabindex="-1" placeholder="dd-mm-aaaa hh:mm" readonly required>
                                    </div>
                                    <div class="tx-8 tx-info" id="borrarFechaHoraContacto">Borrar fecha/hora</div>
                                </div>
                            </div>

                            <!-- MÉTODO DE CONTACTO -->
                            <div class="form-group row mb-0">
                                <div class="col-12 col-lg-3">
                                    <label for="id_metodo" class="form-label">Método: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <select id="id_metodo" name="id_metodo" class="form-control wd-300" required>
                                        <option value="">Seleccione un método...</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Por favor seleccione un método.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TARJETA 2: DETALLES ADICIONALES -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">Detalles Adicionales</h5>
                        </div>
                        <div class="card-body">
                            <!-- OBSERVACIONES -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="observaciones" class="form-label">Observaciones:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div id="observaciones" name="observaciones"></div>
                                    <div class="text-muted small">Opcional</div>
                                </div>
                            </div>

                            <!-- FECHA VISITA CERRADA -->
                            <div class="form-group row mb-0">
                                <div class="col-12 col-lg-3">
                                    <label for="fecha_visita_cerrada" class="form-label">Fecha Visita Cerrada:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="input-group" id="fecha_visita_cerrada_wrapper">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-calendar tx-16 lh-0 op-6"></i></div>
                                        </div>
                                        <input id="fecha_visita_cerrada" name="fecha_visita_cerrada" type="text" class="form-control" placeholder="dd-mm-aaaa hh:mm" readonly>
                                    </div>
                                    <div class="tx-8 tx-info" id="borrarFechaVisitaCerrada">Borrar fecha/hora</div>
                                    <div class="text-muted small">Opcional</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
