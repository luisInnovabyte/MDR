<!-- Modal con animación fade -->
<div class="modal fade" id="modalMantenimientoUsuario" tabindex="-1" aria-labelledby="modalMostrarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Usuario</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="tituloUsuario"><a href="" class="tx-inverse hover-primary">Mantenimiento de usuarios</a></h4>
                <form id="formUsuario">
                    <input type="hidden" name="id_usuario" id="id_usuario"> <!-- Campo oculto para el id del usuario -->

                    <!-- Tarjeta para los datos del usuario -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Datos del Usuario</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- Nombre -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombreUsuario" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" maxlength="90" placeholder="Nombre Completo" autofocus>
                                        <div class="invalid-feedback small-invalid-feedback">Nombre debe tener entre 3 y 90 caracteres</div>
                                    </div><!--form-group -->
                                </div><!-- col-md-6 -->

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emailUsuario" class="form-label">Email: <span class="tx-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email" maxlength="90" placeholder="Correo electrónico">
                                        <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    </div><!--form-group -->
                                </div><!-- col-md-6 -->
                            </div><!-- row -->

                            <div class="row mb-3">
                                <!-- Contraseña -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contrasenaUsuario" class="form-label">Contraseña: <span class="tx-danger">*</span></label>
                                        <input type="password" class="form-control" name="contrasena" id="contrasena" maxlength="90" placeholder="Contraseña">
                                        <div class="invalid-feedback small-invalid-feedback">Contraseña debe tener al menos 6 caracteres</div>
                                    </div><!--form-group -->
                                </div><!-- col-md-6 -->

                                <!-- Confirmar Contraseña -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirmarContrasenaUsuario" class="form-label">Confirmar Contraseña: <span class="tx-danger">*</span></label>
                                        <input type="password" class="form-control" name="confirmar_contrasena" id="confirmarContrasena" maxlength="90" placeholder="Confirmar Contraseña">
                                        <div class="invalid-feedback small-invalid-feedback">Las contraseñas no coinciden</div>
                                    </div><!--form-group -->
                                </div><!-- col-md-6 -->
                            </div><!-- row -->
                        </div><!-- card-body -->
                    </div><!-- card -->

                    <!-- Tarjeta para el rol del usuario -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">Rol del Usuario</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="id_rol" class="form-label">Rol: <span class="tx-danger">*</span></label>
                                        <select id="id_rol" name="id_rol" class="form-control" required>
                                            <!-- Vacío, se llenará vía AJAX -->
                                        </select>
                                        <div class="invalid-feedback small-invalid-feedback">
                                            Por favor seleccione un rol.
                                        </div>
                                    </div><!--form-group -->
                                </div><!-- col-md-12 -->
                            </div><!-- row -->
                        </div><!-- card-body -->
                    </div><!-- card -->

                </form>
            </div><!-- modal-body -->
            <div class="modal-footer">
                <button type="button" name="action" id="btnSalvarUsuario" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div><!-- modal-dialog -->
</div><!-- modal -->