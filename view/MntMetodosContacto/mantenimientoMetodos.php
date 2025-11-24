<!-- Modal con animación fade -->
<div class="modal fade" id="modalMetodo" tabindex="-1" aria-labelledby="modalMostrarMetodosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Métodos</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo">Mantenimiento de métodos</h4>

                <form id="formMetodo" enctype="multipart/form-data">
                    <!-- id metodo -->
                    <input type="hidden" name="id_metodo" id="id_metodo">

                    <!-- SECCIÓN 1: Nombre -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Nombre</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="nombre" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="90" placeholder="Nombre...">
                                    <div class="invalid-feedback small-invalid-feedback">
                                        Solo letras y espacios (mínimo 3 caracteres y máximo de 50)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Imagen -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">Imagen</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mg-t-20">
                                <div class="col-5 col-lg-3">
                                    <label for="imagen_metodo" class="form-label">Imagen <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="custom-file d-flex">
                                        <input type="file" class="form-control" id="imagen_metodo" name="imagen_metodo" accept="image/jpg,image/jpeg,image/png">
                                        <!--
                                        <button type="button" id="btnLimpiarImagen" class="btn btn-outline-danger ms-2">
                                            <i class="fa fa-times"></i> Quitar
                                        </button>
                                        -->
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Imagen con formato válido y menos de 2 MB</div>
                                    <div id="previewImagen" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Permite Adjuntos -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">Permite Adjuntos</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters mg-t-20">
                                <div class="col-5 col-sm-3">
                                    <label for="permite_adjuntos" class="form-label">Permite Adjuntos:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="checkbox" name="permite_adjuntos" id="permite_adjuntos">
                                    <span>Permite adjuntos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="submit" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">
                            Salvar
                        </button>
                        <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>

                </form> <!-- ✅ Cierre correcto del form -->
            </div>
        </div>
    </div>
</div>
