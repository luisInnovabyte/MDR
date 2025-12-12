<!-- Modal Nuevo/Editar Contacto Rápido desde Presupuesto -->
<div class="modal fade" id="modalContactoRapido" tabindex="-1" aria-labelledby="modalContactoRapidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalContactoRapidoLabel">
                    <i class="bi bi-person-plus-fill me-2"></i><span id="tituloModalContacto">Nuevo Contacto</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formContactoRapido">
                    <input type="hidden" id="id_contacto_cliente_modal" name="id_contacto_cliente">
                    <input type="hidden" id="id_cliente_modal" name="id_cliente">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre_contacto_cliente_modal" class="form-label">
                                Nombre: <span class="tx-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre_contacto_cliente_modal" name="nombre_contacto_cliente" required maxlength="100" placeholder="Ej: Juan">
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos_contacto_cliente_modal" class="form-label">Apellidos:</label>
                            <input type="text" class="form-control" id="apellidos_contacto_cliente_modal" name="apellidos_contacto_cliente" maxlength="150" placeholder="Ej: García López">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cargo_contacto_cliente_modal" class="form-label">Cargo:</label>
                            <input type="text" class="form-control" id="cargo_contacto_cliente_modal" name="cargo_contacto_cliente" maxlength="100" placeholder="Ej: Director de Eventos">
                        </div>
                        <div class="col-md-6">
                            <label for="departamento_contacto_cliente_modal" class="form-label">Departamento:</label>
                            <input type="text" class="form-control" id="departamento_contacto_cliente_modal" name="departamento_contacto_cliente" maxlength="100" placeholder="Ej: Producción">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefono_contacto_cliente_modal" class="form-label">Teléfono:</label>
                            <input type="text" class="form-control" id="telefono_contacto_cliente_modal" name="telefono_contacto_cliente" maxlength="50" placeholder="Ej: 924 123 456">
                        </div>
                        <div class="col-md-6">
                            <label for="movil_contacto_cliente_modal" class="form-label">Móvil:</label>
                            <input type="text" class="form-control" id="movil_contacto_cliente_modal" name="movil_contacto_cliente" maxlength="50" placeholder="Ej: 666 123 456">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email_contacto_cliente_modal" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email_contacto_cliente_modal" name="email_contacto_cliente" maxlength="255" placeholder="Ej: contacto@empresa.com">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="principal_contacto_cliente_modal" name="principal_contacto_cliente" value="1">
                                <label class="form-check-label" for="principal_contacto_cliente_modal">
                                    Marcar como contacto principal
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarContactoRapido">
                    <i class="bi bi-save me-2"></i>Guardar Contacto
                </button>
            </div>
        </div>
    </div>
</div>
