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
                                    <div class="btn btn-outline-primary btn-sm me-3 flex-shrink-0">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div>
                                        <strong>Nuevo Cliente</strong>
                                        <p class="small text-muted mb-0">Crear un nuevo registro de cliente con toda la información necesaria. Se abre el formulario completo de alta.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-secondary btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
                                    <div>
                                        <strong>Menú Acciones</strong>
                                        <p class="small text-muted mb-0">Cada fila dispone de un botón <span class="badge bg-secondary"><i class="fa-solid fa-ellipsis-vertical"></i></span> que despliega todas las acciones disponibles para ese cliente:</p>
                                        <ul class="small text-muted mt-1 mb-0 ps-3">
                                            <li><i class="fa-solid fa-pen-to-square me-1"></i><strong>Editar</strong> — Modificar los datos del cliente.</li>
                                            <li><i class="fas fa-users me-1"></i><strong>Contactos</strong> — Gestionar las personas de contacto asociadas.</li>
                                            <li><i class="bi bi-geo-alt-fill me-1"></i><strong>Ubicaciones</strong> — Gestionar las ubicaciones o sedes del cliente.</li>
                                            <li><i class="fa-solid fa-ban me-1 text-danger"></i><strong>Desactivar</strong> — Deshabilitar el cliente sin eliminarlo (soft delete).</li>
                                            <li><i class="bi bi-hand-thumbs-up-fill me-1 text-success"></i><strong>Activar</strong> — Rehabilitar un cliente previamente desactivado.</li>
                                        </ul>
                                        <div class="alert alert-info py-1 mt-2 mb-0">
                                            <small><i class="bi bi-info-circle me-1"></i>Las opciones <em>Desactivar</em> y <em>Activar</em> aparecen de forma alternativa según el estado actual del cliente.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="me-3 flex-shrink-0" style="width:30px; text-align:center;">
                                        <i class="bi bi-plus-circle-fill text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <strong>Ver más detalles</strong>
                                        <p class="small text-muted mb-0">Haz clic en el botón <span class="badge bg-light text-dark border"><i class="bi bi-plus"></i></span> de la primera columna para expandir una fila y ver todos los datos del cliente: dirección, facturación, forma de pago, observaciones y fechas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Contactos del cliente -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="fas fa-users me-2"></i>Contactos del Cliente
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-2">Cada cliente puede tener múltiples personas de contacto asociadas (responsable comercial, responsable de compras, etc.).</p>
                                <p class="small text-muted mb-2">Accede a la gestión de contactos desde el menú <span class="badge bg-secondary"><i class="fa-solid fa-ellipsis-vertical"></i></span> → <strong>Contactos</strong>.</p>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="bi bi-check2 text-success me-2"></i>Nombre y apellidos del contacto</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Cargo o departamento</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Teléfono directo y email personal</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>El contador <span class="badge bg-success"><i class="bi bi-people-fill"></i></span> de la tabla muestra cuántos contactos activos tiene el cliente</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sección: Ubicaciones del cliente -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones del Cliente
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-2">Registra las distintas sedes, almacenes o lugares de entrega de un cliente. Útil cuando los trabajos o servicios se realizan en direcciones diferentes a la principal.</p>
                                <p class="small text-muted mb-2">Accede a la gestión de ubicaciones desde el menú <span class="badge bg-secondary"><i class="fa-solid fa-ellipsis-vertical"></i></span> → <strong>Ubicaciones</strong>.</p>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="bi bi-check2 text-success me-2"></i>Nombre descriptivo de la ubicación</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Dirección completa y código postal</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Persona de contacto en esa sede</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i>Notas u observaciones específicas del lugar</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sección: Filtros y búsqueda -->
                        <div class="card border-0 shadow-sm mt-4">
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

                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">
                                        <i class="bi bi-percent me-2"></i>Descuento Habitual del Cliente
                                    </h6>
                                    <p class="small mb-2">
                                        Configure un porcentaje de descuento habitual para este cliente (0.00% - 100.00%).
                                    </p>
                                    
                                    <div class="alert alert-info py-2 mb-2">
                                        <small>
                                            <i class="bi bi-info-circle me-1"></i>
                                            <strong>Categorías de descuento:</strong>
                                        </small>
                                        <ul class="list-unstyled small mb-0 mt-2">
                                            <li><span class="badge bg-secondary me-2">0%</span> Sin descuento</li>
                                            <li><span class="badge bg-info me-2">≤ 5%</span> Descuento bajo</li>
                                            <li><span class="badge bg-warning me-2">≤ 15%</span> Descuento medio</li>
                                            <li><span class="badge bg-success me-2">&gt; 15%</span> Descuento alto</li>
                                        </ul>
                                    </div>

                                    <div class="alert alert-warning py-2 mb-2">
                                        <small>
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            <strong>Aplicación del descuento:</strong>
                                            El descuento solo se aplicará en las familias de productos donde se haya señalado que se pueden aplicar descuentos.
                                        </small>
                                    </div>

                                    <div class="alert alert-primary py-2">
                                        <small>
                                            <i class="bi bi-file-earmark-text me-1"></i>
                                            <strong>En presupuestos:</strong>
                                            Este valor se mostrará automáticamente en la cabecera de los presupuestos del cliente y podrá ser modificado si es necesario.
                                        </small>
                                    </div>
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
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: SMM v1.0 - Última actualización: 21-02-2026
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