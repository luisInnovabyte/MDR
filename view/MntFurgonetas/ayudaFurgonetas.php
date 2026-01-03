<?php
/**
 * Modal de ayuda para la gestión de furgonetas
 * Proporciona información contextual sobre las funcionalidades disponibles
 */
?>

<!-- Modal de Ayuda Furgonetas -->
<div class="modal fade" id="modalAyudaFurgonetas" tabindex="-1" aria-labelledby="modalAyudaFurgonetasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <!-- Header del Modal -->
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-bold" id="modalAyudaFurgonetasLabel">
                    <i class="bi bi-question-circle me-2"></i>Ayuda - Gestión de Furgonetas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Body del Modal -->
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <!-- Sección: Funciones principales -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-gear-fill me-2"></i>Funciones Principales
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-success btn-sm me-3 flex-shrink-0">
                                        <i class="bi bi-plus-circle"></i>
                                    </div>
                                    <div>
                                        <strong>Nueva Furgoneta</strong>
                                        <p class="small text-muted mb-0">Registrar una nueva furgoneta en el sistema con todos sus datos técnicos y documentales.</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-info btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-edit"></i>
                                    </div>
                                    <div>
                                        <strong>Editar Furgoneta</strong>
                                        <p class="small text-muted mb-0">Actualizar información técnica, documentación o estado del vehículo.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-warning btn-sm me-3 flex-shrink-0">
                                        <i class="bi bi-eye"></i>
                                    </div>
                                    <div>
                                        <strong>Ver Detalles</strong>
                                        <p class="small text-muted mb-0">Expandir registro para ver toda la información completa del vehículo (click en <i class="bi bi-plus-circle"></i>).</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-danger btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                    <div>
                                        <strong>Desactivar</strong>
                                        <p class="small text-muted mb-0">Dar de baja una furgoneta sin eliminarla del sistema.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="btn btn-success btn-sm me-3 flex-shrink-0">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </div>
                                    <div>
                                        <strong>Activar</strong>
                                        <p class="small text-muted mb-0">Reactivar una furgoneta previamente dada de baja.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Filtros y búsqueda -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-funnel-fill me-2"></i>Filtros y Búsqueda
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-search me-2"></i>Búsqueda Global</strong>
                                    <p class="small text-muted mb-2">Use el campo de búsqueda superior para encontrar furgonetas por matrícula, modelo, marca o cualquier dato.</p>
                                </div>
                                
                                <div class="mb-3">
                                    <strong><i class="bi bi-filter me-2"></i>Filtros por Columna</strong>
                                    <p class="small text-muted mb-2">Utilice los campos en la parte inferior de la tabla para filtrar por columnas específicas.</p>
                                </div>

                                <div class="alert alert-info py-2">
                                    <small><i class="bi bi-lightbulb me-1"></i><strong>Tip:</strong> Los filtros se pueden combinar para búsquedas más precisas.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <!-- Sección: Estados y alertas -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-traffic-cone me-2"></i>Estados de Furgonetas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-success me-2">OPERATIVA</span>
                                        <span class="fw-bold">En Servicio</span>
                                    </div>
                                    <p class="small text-muted">La furgoneta está disponible y puede ser asignada a trabajos.</p>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-warning me-2">EN TALLER</span>
                                        <span class="fw-bold">En Mantenimiento</span>
                                    </div>
                                    <p class="small text-muted">La furgoneta está temporalmente fuera de servicio por reparación o mantenimiento.</p>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-danger me-2">BAJA</span>
                                        <span class="fw-bold">Dada de Baja</span>
                                    </div>
                                    <p class="small text-muted">La furgoneta está permanentemente fuera de servicio.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Alertas de vencimiento -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Alertas de Vencimiento
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold"><i class="bi bi-calendar-check me-2"></i>ITV y Seguro</h6>
                                    <p class="small mb-2">Sistema de alertas visuales por color:</p>
                                    <ul class="list-unstyled small">
                                        <li><span class="badge bg-danger me-2">Rojo</span> Vencido (fecha pasada)</li>
                                        <li><span class="badge bg-warning text-dark me-2">Amarillo</span> Próximo a vencer (≤30 días)</li>
                                        <li><span class="badge bg-success me-2">Verde</span> Vigente (>30 días)</li>
                                    </ul>
                                </div>

                                <div class="alert alert-warning py-2">
                                    <small>
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Importante:</strong> Las fechas se muestran en formato DD/MM/YYYY (europeo).
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Información del formulario -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-form-check me-2"></i>Datos de la Furgoneta
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">Campos Obligatorios</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Matrícula del vehículo (único)</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-info">Identificación del Vehículo</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-truck me-2"></i>Marca y modelo</li>
                                        <li><i class="bi bi-calendar3 me-2"></i>Año de fabricación</li>
                                        <li><i class="bi bi-palette me-2"></i>Color del vehículo</li>
                                        <li><i class="bi bi-card-text me-2"></i>Número de bastidor (VIN)</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-warning">Documentación</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-calendar-check me-2"></i>Fecha próxima ITV</li>
                                        <li><i class="bi bi-shield-check me-2"></i>Fecha vencimiento seguro</li>
                                        <li><i class="bi bi-file-earmark-text me-2"></i>Número de póliza</li>
                                        <li><i class="bi bi-building me-2"></i>Compañía aseguradora</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Consejos adicionales -->
                <div class="mt-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-primary mb-3">
                                <i class="bi bi-lightbulb me-2"></i>Consejos Útiles
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Actualice las fechas de ITV y seguro regularmente</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Mantenga el estado del vehículo actualizado</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Revise las alertas de vencimiento periódicamente</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Use observaciones para notas de mantenimiento</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Registre el kilometraje actual del vehículo</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Verifique la documentación antes de asignar trabajos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: MDR ERP v1.0 - Última actualización: 23-12-2025
                    </small>
                </div>
                 <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para el modal -->
<style>
    #modalAyudaFurgonetas .modal-content {
        border-radius: 15px;
    }
    
    #modalAyudaFurgonetas .card {
        transition: transform 0.2s ease-in-out;
    }
    
    #modalAyudaFurgonetas .card:hover {
        transform: translateY(-2px);
    }
    
    #modalAyudaFurgonetas .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
</style>
