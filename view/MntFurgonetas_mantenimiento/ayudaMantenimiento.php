<!-- Modal de Ayuda para Mantenimiento de Furgonetas -->
<div class="modal fade" id="modalAyudaMantenimiento" tabindex="-1" aria-labelledby="modalAyudaMantenimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%; width: 1400px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaMantenimientoLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Gestión de Mantenimiento de Furgonetas
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
                                    <i class="fas fa-info-circle me-2"></i>¿Qué es el Historial de Mantenimiento?
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    El <strong>historial de mantenimiento</strong> permite registrar todas las intervenciones realizadas 
                                    en la furgoneta: revisiones, reparaciones, ITVs, cambios de neumáticos, etc.
                                </p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Ventaja:</strong> Mantén un registro completo de todos los trabajos realizados en el vehículo, 
                                    incluyendo costos, kilometraje y garantías.
                                </div>

                                <h6 class="text-success mt-4">
                                    <i class="fas fa-wrench me-2"></i>Tipos de Mantenimiento
                                </h6>
                                <ul>
                                    <li><strong>Revisión:</strong> Mantenimientos preventivos programados</li>
                                    <li><strong>Reparación:</strong> Arreglos por averías o desgaste</li>
                                    <li><strong>ITV:</strong> Inspección Técnica de Vehículos</li>
                                    <li><strong>Neumáticos:</strong> Cambio o reparación de neumáticos</li>
                                    <li><strong>Otros:</strong> Otros trabajos no clasificados</li>
                                </ul>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Registra siempre el kilometraje en el momento del servicio 
                                    para llevar un control preciso del mantenimiento.
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
                                            <i class="fas fa-plus-circle text-success me-2"></i>Botón "Nuevo Mantenimiento"
                                        </h6>
                                        <p class="small">Registra un nuevo mantenimiento o servicio realizado a la furgoneta.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-edit text-info me-2"></i>Botón "Editar"
                                        </h6>
                                        <p class="small">Modifica los datos de un mantenimiento registrado.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-trash text-danger me-2"></i>Activar/Desactivar
                                        </h6>
                                        <p class="small">Marca registros como activos/inactivos sin eliminarlos.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-search text-primary me-2"></i>Filtros de Búsqueda
                                        </h6>
                                        <p class="small">Utiliza los campos del pie de tabla para filtrar por tipo, fecha, costo, etc.</p>
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
                                    <i class="fas fa-wrench me-2"></i>Campos del Formulario
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
                                                <td><strong>Fecha</strong></td>
                                                <td><span class="badge bg-danger">Sí</span></td>
                                                <td>Fecha en la que se realizó el servicio</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tipo</strong></td>
                                                <td><span class="badge bg-danger">Sí</span></td>
                                                <td>Tipo de mantenimiento realizado</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Descripción</strong></td>
                                                <td><span class="badge bg-danger">Sí</span></td>
                                                <td>Detalle del trabajo realizado</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kilometraje</strong></td>
                                                <td><span class="badge bg-warning">Recomendado</span></td>
                                                <td>Kilómetros del vehículo en ese momento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Costo</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Importe del servicio realizado</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nº Factura</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Número de factura del taller</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Taller</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Nombre del establecimiento</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Teléfono Taller</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Contacto del taller</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Dirección Taller</strong></td>
                                                <td><span class="badge bg-secondary">No</span></td>
                                                <td>Ubicación del taller</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h6 class="text-warning mt-4">
                                    <i class="fas fa-clipboard-check me-2"></i>Campos Especiales para ITV
                                </h6>
                                <div class="alert alert-light">
                                    <ul class="mb-0">
                                        <li><strong>Resultado ITV:</strong> Favorable, Desfavorable o Negativa</li>
                                        <li><strong>Fecha Próxima ITV:</strong> Cuándo toca la siguiente inspección</li>
                                    </ul>
                                </div>

                                <h6 class="text-info mt-3">
                                    <i class="fas fa-shield-alt me-2"></i>Garantía y Observaciones
                                </h6>
                                <div class="alert alert-light">
                                    <ul class="mb-0">
                                        <li><strong>Garantía Hasta:</strong> Fecha límite de la garantía del trabajo</li>
                                        <li><strong>Observaciones:</strong> Notas adicionales sobre el servicio</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-chart-line me-2"></i>Información Adicional
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    El sistema calcula automáticamente estadísticas como:
                                </p>
                                <ul class="small">
                                    <li>Costo total de mantenimientos</li>
                                    <li>Días desde el último servicio</li>
                                    <li>Kilómetros recorridos desde cada intervención</li>
                                    <li>Estado de las garantías</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
