<!-- Modal de Ayuda para Elementos del Artículo -->
<div class="modal fade" id="modalAyudaElementos" tabindex="-1" aria-labelledby="modalAyudaElementosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%; width: 1400px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaElementosLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Gestión de Elementos del Artículo
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
                                    <i class="fas fa-info-circle me-2"></i>¿Qué son los Elementos?
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    Los <strong>elementos</strong> son las unidades físicas individuales que componen un artículo del catálogo. 
                                    Cada elemento representa un equipo específico con su propio código, número de serie y estado.
                                </p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Ejemplo:</strong> Si tienes 5 cámaras Sony A7III, cada cámara física es un "elemento" 
                                    individual con su código único (ej: CAM001-001, CAM001-002, CAM001-003, etc.)
                                </div>

                                <h6 class="text-success mt-4">
                                    <i class="fas fa-barcode me-2"></i>Código Automático
                                </h6>
                                <p>
                                    El sistema genera automáticamente el <strong>código del elemento</strong> basándose en el código del artículo 
                                    más un número correlativo (formato: CODIGO_ARTICULO-001).
                                </p>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> El código se genera al guardar. No es necesario introducirlo manualmente.
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
                                            <i class="fas fa-plus-circle text-success me-2"></i>Botón "Nuevo Elemento"
                                        </h6>
                                        <p class="small">Crea un nuevo elemento para el artículo actual.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-edit text-info me-2"></i>Botón "Editar"
                                        </h6>
                                        <p class="small">Modifica los datos de un elemento existente.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-trash text-danger me-2"></i>Activar/Desactivar
                                        </h6>
                                        <p class="small">Marca elementos como activos/inactivos sin eliminarlos.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-filter text-primary me-2"></i>Filtros de Búsqueda
                                        </h6>
                                        <p class="small">Utiliza los campos del pie de tabla para filtrar por cualquier criterio.</p>

                                        <h6 class="text-secondary">
                                            <i class="fas fa-circle text-warning me-2"></i>Estados con Color
                                        </h6>
                                        <p class="small">Cada estado se muestra con un color distintivo (verde=disponible, azul=alquilado, etc.)</p>
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
                                    <i class="fas fa-box me-2"></i>Campos del Formulario
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
                                                <td><strong>Descripción</strong></td>
                                                <td><span class="badge bg-danger">Sí</span></td>
                                                <td>Descripción del elemento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Código</strong></td>
                                                <td><span class="badge bg-success">Auto</span></td>
                                                <td>Se genera automáticamente</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Marca</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Marca del elemento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Modelo</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Modelo específico</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Código de Barras</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Código de barras único</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Número de Serie</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Número de serie del fabricante</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado</strong></td>
                                                <td><span class="badge bg-info">Default</span></td>
                                                <td>Estado actual (Disponible por defecto)</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ubicación</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Ubicación física del elemento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Compra</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Fecha de adquisición</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Precio Compra</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Precio pagado por el elemento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Proveedor</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Proveedor que lo vendió</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha Alta</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Fecha puesta en servicio</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fin Garantía</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Fecha fin de garantía</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Próximo Mant.</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Fecha próximo mantenimiento</td>
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
                                                    Registra siempre el número de serie del fabricante si está disponible
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Utiliza códigos de barras para localización rápida
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Mantén actualizado el estado del elemento
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Registra fechas de garantía y mantenimiento
                                                </li>
                                                <li class="mb-0">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Indica la ubicación física para facilitar localización
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-palette me-2"></i>Estados de Elementos
                                            </h6>
                                            <ul class="list-unstyled mb-0 small">
                                                <li class="mb-1"><span class="badge" style="background-color: #4CAF50">Disponible</span> - Listo para alquilar</li>
                                                <li class="mb-1"><span class="badge" style="background-color: #2196F3">Alquilado</span> - Actualmente en alquiler</li>
                                                <li class="mb-1"><span class="badge" style="background-color: #FF9800">En reparación</span> - En proceso de reparación</li>
                                                <li class="mb-1"><span class="badge" style="background-color: #F44336">Dado de baja</span> - Fuera de servicio</li>
                                                <li class="mb-1"><span class="badge" style="background-color: #9C27B0">De terceros</span> - Equipo de alquiler externo</li>
                                                <li class="mb-1"><span class="badge" style="background-color: #FFC107">Mantenimiento</span> - En mantenimiento programado</li>
                                            </ul>
                                        </div>

                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-magic me-2"></i>Sincronización Automática Estado-Activación
                                            </h6>
                                            <p class="mb-2">
                                                El sistema mantiene <strong>sincronizados automáticamente</strong> el estado del elemento 
                                                y su activación/desactivación. No necesitas preocuparte por la coherencia, el sistema lo gestiona por ti:
                                            </p>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="card bg-light border-0 mb-2">
                                                        <div class="card-body py-2 px-3">
                                                            <h6 class="text-primary mb-2">
                                                                <i class="fas fa-arrow-right me-2"></i>Si cambias el Estado:
                                                            </h6>
                                                            <ul class="list-unstyled mb-0 small">
                                                                <li class="mb-1">
                                                                    <span class="badge bg-danger me-1">Dado de baja</span>
                                                                    → El elemento se <strong>desactiva automáticamente</strong>
                                                                </li>
                                                                <li class="mb-0">
                                                                    <span class="badge bg-success me-1">Cualquier otro</span>
                                                                    → El elemento se <strong>activa automáticamente</strong>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="card bg-light border-0 mb-2">
                                                        <div class="card-body py-2 px-3">
                                                            <h6 class="text-info mb-2">
                                                                <i class="fas fa-arrow-left me-2"></i>Si Activas/Desactivas:
                                                            </h6>
                                                            <ul class="list-unstyled mb-0 small">
                                                                <li class="mb-1">
                                                                    <i class="fas fa-toggle-off text-danger me-1"></i>
                                                                    <strong>Desactivar</strong> → Cambia a <span class="badge bg-danger">Dado de baja</span>
                                                                </li>
                                                                <li class="mb-0">
                                                                    <i class="fas fa-toggle-on text-success me-1"></i>
                                                                    <strong>Activar</strong> → Cambia a <span class="badge bg-success">Disponible</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3 p-2 bg-white rounded border border-success">
                                                <small class="text-muted">
                                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                                    <strong>Ejemplo práctico:</strong> Si tienes un elemento "Disponible" y lo desactivas, 
                                                    automáticamente su estado cambiará a "Dado de baja". Si luego lo reactivas, 
                                                    volverá a "Disponible". ¡No necesitas cambiar ambos campos manualmente!
                                                </small>
                                            </div>
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
                    <i class="fas fa-times me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
