<!-- Modal con animación fade -->
<div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Gestión de Roles</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo">
                    <a href="#" class="tx-inverse hover-primary">Formulario de Rol</a>
                </h4>

                <!-- Formulario de Rol -->
                <form id="formRol">
                    <!-- Campo oculto para ID del rol -->
                    <input type="hidden" name="id_rol" id="id_rol">

                    <!-- SECCIÓN 1: Nombre del Rol -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Nombre del Rol</h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="nombre_rol" class="form-label">Nombre del rol: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="nombre_rol" id="nombre_rol" maxlength="50" placeholder="Nombre del rol...">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 50 caracteres)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" name="action" id="btnSalvarRol" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Guardar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
