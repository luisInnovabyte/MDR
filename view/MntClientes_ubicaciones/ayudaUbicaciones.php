<!-- Modal de Ayuda para Ubicaciones del Cliente -->
<div class="modal fade" id="modalAyudaUbicaciones" tabindex="-1" role="dialog" aria-labelledby="modalAyudaUbicacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 95%; width: 1400px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaUbicacionesLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Gestión de Ubicaciones del Cliente
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-info-circle me-2"></i>¿Qué son las Ubicaciones del Cliente?
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    Las <strong>ubicaciones del cliente</strong> son las distintas direcciones físicas donde 
                                    el cliente opera, recibe mercancía o realiza actividades comerciales.
                                </p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Ventaja:</strong> Puedes registrar múltiples ubicaciones para un mismo cliente: 
                                    sede principal, almacenes, sucursales, puntos de entrega, etc.
                                </div>

                                <h6 class="text-success mt-4">
                                    <i class="fas fa-star me-2"></i>Ubicación Principal
                                </h6>
                                <p>
                                    Cada cliente puede tener <strong>una ubicación marcada como "Principal"</strong>. 
                                    Esta será la dirección de referencia para entregas y facturación.
                                </p>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Si marcas una nueva ubicación como principal, la anterior 
                                    perderá automáticamente esta condición.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-table me-2"></i>Funciones de la Tabla
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-secondary">
                                            <i class="fas fa-plus-circle text-success me-2"></i>Botón "Nueva Ubicación"
                                        </h6>
                                        <p class="small">Crea una nueva ubicación para el cliente actual.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-edit text-info me-2"></i>Botón "Editar"
                                        </h6>
                                        <p class="small">Modifica los datos de una ubicación existente.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-trash text-danger me-2"></i>Activar/Desactivar
                                        </h6>
                                        <p class="small">Marca ubicaciones como activas/inactivas sin eliminarlas.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-search text-primary me-2"></i>Filtros de Búsqueda
                                        </h6>
                                        <p class="small">Utiliza los campos del pie de tabla para filtrar por cualquier criterio.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>Campos del Formulario
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Campo</th>
                                                <th>Obligatorio</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Nombre</strong></td>
                                                <td><span class="badge bg-danger">Sí</span></td>
                                                <td>Nombre identificativo de la ubicación</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dirección</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Calle, número, piso, etc.</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Código Postal</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Código postal de la ubicación</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Población</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Ciudad o localidad</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Provincia</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Provincia o región</td>
                                            </tr>
                                            <tr>
                                                <td><strong>País</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>País (por defecto: España)</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Persona Contacto</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Responsable en la ubicación</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teléfono</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Teléfono de la ubicación</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Email de contacto</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Principal</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Marca como ubicación principal</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Observaciones</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Notas adicionales</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-tips me-2"></i>Consejos de Uso
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-lightbulb me-2"></i>Buenas Prácticas
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Registra ubicaciones específicas: Almacén, Oficina, Sucursal, etc.
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Mantén actualizada la información de ubicaciones
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Usa el campo observaciones para horarios y acceso
                                                </li>
                                                <li class="mb-0">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Marca como principal la ubicación de entrega habitual
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-keyboard me-2"></i>Atajos de Teclado
                                            </h6>
                                            <ul class="list-unstyled mb-0 small">
                                                <li><kbd>Ctrl + S</kbd> = Guardar formulario (cuando esté activo)</li>
                                                <li><kbd>Esc</kbd> = Cerrar modales</li>
                                                <li><kbd>Tab</kbd> = Navegar entre campos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
              
            </div>
        </div>
    </div>
</div>
