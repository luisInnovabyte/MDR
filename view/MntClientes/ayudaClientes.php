<?php
/**
 * Modal de ayuda para la gestión de clientes
 * Proporciona información contextual sobre las funcionalidades disponibles
 */
?>

<!-- Modal de Ayuda Clientes -->
<div class="modal fade" id="modalAyudaClientes" tabindex="-1" aria-labelledby="modalAyudaClientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <!-- Header del Modal -->
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-bold" id="modalAyudaClientesLabel">
                    <i class="bi bi-question-circle me-2"></i>Ayuda - Gestión de Clientes
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
                                        <strong>Nuevo Cliente</strong>
                                        <p class="small text-muted mb-0">Crear un nuevo registro de cliente con toda la información necesaria.</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-info btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-edit"></i>
                                    </div>
                                    <div>
                                        <strong>Editar Cliente</strong>
                                        <p class="small text-muted mb-0">Modificar la información existente de un cliente.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-primary btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <strong>Gestión de Contactos</strong>
                                        <p class="small text-muted mb-0">Administrar contactos y personas relacionadas con el cliente.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-danger btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                    <div>
                                        <strong>Desactivar</strong>
                                        <p class="small text-muted mb-0">Deshabilitar un cliente sin eliminarlo permanentemente.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="btn btn-success btn-sm me-3 flex-shrink-0">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </div>
                                    <div>
                                        <strong>Activar</strong>
                                        <p class="small text-muted mb-0">Rehabilitar un cliente previamente desactivado.</p>
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
                                    <p class="small text-muted mb-2">Use el campo de búsqueda superior para encontrar clientes por cualquier criterio.</p>
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
                        <!-- Sección: Información del formulario -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-form-check me-2"></i>Datos del Cliente
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">Campos Obligatorios</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Código del cliente (único)</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Nombre del cliente</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>NIF/CIF</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-info">Información de Contacto</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-geo-alt me-2"></i>Dirección principal</li>
                                        <li><i class="bi bi-telephone me-2"></i>Teléfono de contacto</li>
                                        <li><i class="bi bi-envelope me-2"></i>Email corporativo</li>
                                        <li><i class="bi bi-globe me-2"></i>Sitio web</li>
                                        <li><i class="bi bi-printer me-2"></i>Fax (opcional)</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-warning">Dirección de Facturación</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-receipt me-2"></i>Nombre para facturación</li>
                                        <li><i class="bi bi-geo-alt-fill me-2"></i>Dirección específica de facturación</li>
                                        <li><i class="bi bi-mailbox2 me-2"></i>CP y población de facturación</li>
                                        <li><i class="bi bi-map-fill me-2"></i>Provincia de facturación</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Estados y códigos -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-toggle-on me-2"></i>Estados y Códigos
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-check-circle text-success fa-2x me-2"></i>
                                        <span class="fw-bold">Cliente Activo</span>
                                    </div>
                                    <p class="small text-muted">El cliente está disponible para operaciones comerciales y facturación.</p>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-x-circle text-danger fa-2x me-2"></i>
                                        <span class="fw-bold">Cliente Inactivo</span>
                                    </div>
                                    <p class="small text-muted">El cliente está deshabilitado temporalmente.</p>
                                </div>

                                <div class="alert alert-warning py-2">
                                    <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> Los códigos de cliente deben ser únicos en el sistema.</small>
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
                                        <li><i class="bi bi-check2 text-success me-2"></i>Use códigos descriptivos para facilitar la búsqueda</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Mantenga actualizada la información de contacto</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Complete los datos de facturación si son diferentes</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Utilice observaciones para notas importantes</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Gestione los contactos asociados al cliente</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Revise periódicamente los datos fiscales</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer border-0 bg-light">
                <div class="d-flex justify-content-between w-100">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Versión del sistema: MDR v1.0 - Módulo de Clientes
                    </small>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para el modal -->
<style>
    #modalAyudaClientes .modal-content {
        border-radius: 15px;
    }
    
    #modalAyudaClientes .card {
        transition: transform 0.2s ease-in-out;
    }
    
    #modalAyudaClientes .card:hover {
        transform: translateY(-2px);
    }
    
    #modalAyudaClientes .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
</style>