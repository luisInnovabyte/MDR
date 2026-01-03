<!-- Modal Formulario de Kit -->
<div class="modal fade" id="modalFormularioKit" tabindex="-1" aria-labelledby="modalFormularioKitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFormularioKitLabel">
                    <i class="fas fa-box-open me-2"></i>Agregar Componente al KIT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmKit" name="frmKit" method="POST">
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Campo oculto: ID del registro (para edición) -->
                        <input type="hidden" id="id_kit" name="id_kit">
                        
                        <!-- Campo oculto: ID del artículo maestro (KIT) -->
                        <input type="hidden" id="id_articulo_maestro" name="id_articulo_maestro">

                        <!-- Fila 1: Artículo Componente -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="id_articulo_componente" class="form-label">
                                    <i class="fas fa-puzzle-piece me-2 text-primary"></i>
                                    <strong>Artículo Componente <span class="text-danger">*</span></strong>
                                </label>
                                <select class="form-select" id="id_articulo_componente" name="id_articulo_componente" required>
                                    <option value="">Seleccione un artículo...</option>
                                    <!-- Las opciones se cargarán dinámicamente vía AJAX -->
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Seleccione el artículo que desea agregar como componente del KIT
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Cantidad -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cantidad_kit" class="form-label">
                                    <i class="fas fa-sort-numeric-up me-2 text-primary"></i>
                                    <strong>Cantidad <span class="text-danger">*</span></strong>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="cantidad_kit" 
                                       name="cantidad_kit" 
                                       min="1" 
                                       step="1" 
                                       value="1" 
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Indique cuántas unidades de este artículo forman parte del KIT
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-euro-sign me-2 text-primary"></i>
                                    <strong>Precio Unitario</strong>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="precio_unitario_display" 
                                           readonly 
                                           disabled 
                                           placeholder="Seleccione un artículo">
                                    <span class="input-group-text">€</span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Precio de alquiler del artículo seleccionado
                                </div>
                            </div>
                        </div>

                        <!-- Fila 3: Subtotal calculado -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0" role="alert">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <strong><i class="fas fa-calculator me-2"></i>Subtotal:</strong>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <h5 class="mb-0" id="subtotal_display">0.00 €</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nota informativa -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning" role="alert">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Importante
                                    </h6>
                                    <ul class="mb-0">
                                        <li>No se pueden agregar artículos que también sean KITs</li>
                                        <li>No se puede agregar el mismo artículo dos veces al mismo KIT</li>
                                        <li>La cantidad debe ser mayor a 0</li>
                                        <li>El artículo componente debe estar activo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campos requeridos -->
                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <span class="text-danger">*</span> Campos obligatorios
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarKit">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
