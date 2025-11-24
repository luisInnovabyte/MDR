<!-- Modal con animación fade -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header pd-y-20 pd-x-25">
        <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Gestión de Usuarios</h6>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body pd-25">
        <h4 class="lh-3 mg-b-20" id="mdltitulo">
          <a href="#" class="tx-inverse hover-primary">Formulario de Usuario</a>
        </h4>

        <!-- Formulario de Usuario -->
        <form id="formUsuario">
          <!-- Campo oculto para ID del usuario -->
          <input type="hidden" name="id_usuario" id="id_usuario">

          <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0 tx-bold">Datos del Usuario</h5>
            </div>
            <div class="card-body">
              <div class="row gy-3">

                <!-- Nombre -->
                <div class="col-12">
                  <label for="nombre" class="form-label">Nombre completo: <span class="tx-danger">*</span></label>
                  <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" placeholder="Ej: Carlos López" required>
                  <div class="invalid-feedback">Este campo es obligatorio.</div>
                </div>

                <!-- Email -->
                <div class="col-12">
                  <label for="email" class="form-label">Correo electrónico: <span class="tx-danger">*</span></label>
                  <input type="email" class="form-control" id="emailUsuario" name="emailUsuario" placeholder="Ej: correo@dominio.com" required>
                  <div class="invalid-feedback">Correo inválido.</div>
                </div>

                <!-- Contraseña -->
                <div class="col-12 col-lg-6">
                  <label for="contrasena" class="form-label">Contraseña: <span class="tx-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="contrasena" name="contrasena" maxlength="30" placeholder="********">
                    <button class="btn btn-outline-primary" type="button" id="verContraseña">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                  </div>
                  <div class="invalid-feedback small-invalid-feedback">La contraseña debe tener al menos 10 caracteres, contener mayúsculas, números y caracteres especiales (@$!%*?&).</div>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="col-12 col-lg-6">
                  <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña: <span class="tx-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" maxlength="30" placeholder="Repite la contraseña">
                    <button class="btn btn-outline-primary" type="button" id="verConfirmarContraseña">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                  </div>
                  <div class="invalid-feedback small-invalid-feedback">Las contraseñas no coinciden.</div>
                </div>

                <!-- Rol -->
                <div class="col-12">
                  <label for="id_rol" class="form-label">Rol del Usuario: <span class="tx-danger">*</span></label>
                  <select class="form-select" id="id_rol" name="id_rol" required>
                    <!-- Aquí se cargarán las opciones dinámicamente -->
                  </select>
                  <div class="invalid-feedback">Selecciona un rol válido.</div>
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" id="btnGuardarUsuario" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Guardar</button>
        <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
