<div class="modal fade" id="modalMantenimiento" tabindex="-1" aria-labelledby="modalMostrarComercialLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Empleado</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de empleados</a></h4>
                
                <form id="formComercial">
                    <!-- ID COMERCIAL -->
                    <input type="hidden" name="id_comercial" id="id_comercial">

                    <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Información Básica</h5>
                        </div>
                        <div class="card-body">
                            <!-- NOMBRE -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="nombre" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="90" placeholder="Nombre..." autofocus>
                                    <div class="invalid-feedback small-invalid-feedback">Solo letras y espacios (mínimo 3 caracteres y máximo de 50)</div>
                                </div>
                            </div>

                            <!-- APELLIDOS -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="apellidos" class="form-label">Apellidos: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="apellidos" id="apellidos" maxlength="90" placeholder="Apellidos...">
                                    <div class="invalid-feedback small-invalid-feedback">Solo letras y espacios (mínimo 3 caracteres y máximo de 50)</div>
                                </div>
                            </div>

                            <!-- MÓVIL -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="movil" class="form-label">Móvil: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="movil" id="movil" maxlength="14" placeholder="Móvil">
                                    <div class="invalid-feedback small-invalid-feedback">Solo números (máximo 14 pos)</div>
                                </div>
                            </div>

                            <!-- TELÉFONO -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="telefono" class="form-label">Teléfono: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="telefono" id="telefono" maxlength="14" placeholder="Teléfono">
                                    <div class="invalid-feedback small-invalid-feedback">Solo números (máximo 14 pos)</div>
                                </div>
                            </div>

                            <!-- NUEVO SELECT: ID USUARIO -->
                            <div class="form-group row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="id_usuario" class="form-label">Usuario asignado: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <select name="id_usuario" id="id_usuario" class="form-control" required>
                                        <!-- Aquí van las opciones dinámicas -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Por favor seleccione un usuario.</div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SECCIÓN 2: FIRMA DIGITAL -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">📝 Firma Digital</h5>
                        </div>
                        <div class="card-body">
                            <p class="tx-12 tx-gray-600 mg-b-15">La firma se utilizará en los presupuestos impresos. El empleado también puede editarla desde su perfil.</p>
                            
                            <!-- Canvas de firma -->
                            <div class="row">
                                <div class="col-12">
                                    <canvas id="firma-canvas-admin" class="firma-canvas-admin" width="500" height="180"></canvas>
                                </div>
                            </div>
                            
                            <!-- Botones de acción -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" id="btn-limpiar-firma-admin" class="btn btn-secondary btn-sm">
                                        <i class="fa fa-eraser"></i> Limpiar Firma
                                    </button>
                                    <button type="button" id="btn-eliminar-firma-admin" class="btn btn-danger btn-sm">
                                        <i class="fa fa-trash"></i> Eliminar Firma
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Estado de la firma -->
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="firma-status-admin" class="tx-12"></div>
                                </div>
                            </div>
                            
                            <!-- Vista previa de firma existente -->
                            <div class="row mt-3" id="firma-preview-admin" style="display: none;">
                                <div class="col-12">
                                    <p class="tx-12 tx-semibold mg-b-5">Firma guardada:</p>
                                    <div class="border rounded p-2 bg-white text-center">
                                        <img id="firma-preview-img-admin" src="" alt="Firma actual" style="max-width: 100%; height: auto; max-height: 150px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                        <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- modal-body -->
        </div><!-- modal-content -->
    </div><!-- modal-dialog -->
</div><!-- modal -->

<!-- Estilos para el canvas de firma en admin -->
<style>
    .firma-canvas-admin {
        border: 2px solid #6c757d;
        border-radius: 5px;
        background-color: white;
        touch-action: none;
        width: 100%;
        cursor: crosshair;
    }
</style>
