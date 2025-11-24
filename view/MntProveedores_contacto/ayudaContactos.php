<!-- Modal de Ayuda para Contactos del Proveedor -->
<div class="modal fade" id="modalAyudaContactos" tabindex="-1" aria-labelledby="modalAyudaContactosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%; width: 1400px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaContactosLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Gestión de Contactos del Proveedor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-info-circle me-2"></i>¿Qué son los Contactos del Proveedor?
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    Los <strong>contactos del proveedor</strong> son las personas específicas dentro de la empresa proveedora 
                                    con las que mantienes comunicación para temas comerciales, técnicos o administrativos.
                                </p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Ventaja:</strong> En lugar de tener solo un "contacto general", puedes registrar múltiples 
                                    personas según su función: ventas, soporte técnico, facturación, etc.
                                </div>

                                <h6 class="text-success mt-4">
                                    <i class="fas fa-star me-2"></i>Contacto Principal
                                </h6>
                                <p>
                                    Cada proveedor puede tener <strong>un único contacto marcado como "Principal"</strong>. 
                                    Este será el contacto de referencia para comunicaciones importantes.
                                </p>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Si marcas un nuevo contacto como principal, el anterior 
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
                                            <i class="fas fa-plus-circle text-success me-2"></i>Botón "Nuevo Contacto"
                                        </h6>
                                        <p class="small">Crea un nuevo contacto para el proveedor actual.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-edit text-info me-2"></i>Botón "Editar"
                                        </h6>
                                        <p class="small">Modifica los datos de un contacto existente.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-trash text-danger me-2"></i>Activar/Desactivar
                                        </h6>
                                        <p class="small">Marca contactos como activos/inactivos sin eliminarlos.</p>
                                        
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
                                    <i class="fas fa-user me-2"></i>Campos del Formulario
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
                                                <td>Nombre de pila del contacto</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Apellidos</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Apellidos completos</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Cargo</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Posición en la empresa</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Departamento</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Área de trabajo</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teléfono</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Teléfono fijo o directo</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Móvil</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Teléfono móvil</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Dirección de correo</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Extensión</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Extensión telefónica</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Principal</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Marca como contacto principal</td>
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
                                                    Registra contactos específicos por área: Ventas, SAT, Administración
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Mantén actualizada la información de contacto
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Usa el campo observaciones para notas importantes
                                                </li>
                                                <li class="mb-0">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Marca como principal al contacto más relevante
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
                <button type="button" class="btn btn-primary" onclick="window.open('../../docs/manual-contactos-proveedor.pdf', '_blank')">
                    <i class="fas fa-download me-2"></i>Descargar Manual PDF
                </button>
            </div>
        </div>
    </div>
</div>