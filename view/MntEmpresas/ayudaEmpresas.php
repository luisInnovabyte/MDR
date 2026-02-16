<?php
/**
 * Modal de ayuda para la gestión de empresas
 * Proporciona información contextual sobre las funcionalidades disponibles
 */
?>

<!-- Modal de Ayuda Empresas -->
<div class="modal fade" id="modalAyudaEmpresas" tabindex="-1" aria-labelledby="modalAyudaEmpresasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <!-- Header del Modal -->
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title fw-bold" id="modalAyudaEmpresasLabel">
                    <i class="bi bi-question-circle me-2"></i>Ayuda - Gestión de Empresas
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
                                        <strong>Nueva Empresa</strong>
                                        <p class="small text-muted mb-0">Crear un nuevo registro de empresa emisora de documentos.</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-info btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-edit"></i>
                                    </div>
                                    <div>
                                        <strong>Editar Empresa</strong>
                                        <p class="small text-muted mb-0">Modificar la información existente de una empresa.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-warning btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <strong>Verificar Empresa</strong>
                                        <p class="small text-muted mb-0">Comprobar existencia y disponibilidad de código o NIF.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <div class="btn btn-danger btn-sm me-3 flex-shrink-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                    <div>
                                        <strong>Desactivar</strong>
                                        <p class="small text-muted mb-0">Deshabilitar una empresa sin eliminarla permanentemente.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="btn btn-success btn-sm me-3 flex-shrink-0">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </div>
                                    <div>
                                        <strong>Activar</strong>
                                        <p class="small text-muted mb-0">Rehabilitar una empresa previamente desactivada.</p>
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
                                    <p class="small text-muted mb-2">Use el campo de búsqueda superior para encontrar empresas por cualquier criterio.</p>
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
                                    <i class="bi bi-form-check me-2"></i>Datos de la Empresa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">Campos Obligatorios</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Código de empresa (único)</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Nombre de la empresa</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>NIF de la empresa</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-info">Tipos de Empresa</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-building me-2"></i><strong>Empresa Real:</strong> Factura legalmente</li>
                                        <li><i class="bi bi-bookmark-check me-2"></i><strong>Empresa Ficticia:</strong> Solo para presupuestos</li>
                                        <li><i class="bi bi-star-fill me-2"></i><strong>Ficticia Principal:</strong> Emisora por defecto</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-warning">Datos Fiscales y Contacto</h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-geo-alt me-2"></i>Dirección fiscal completa</li>
                                        <li><i class="bi bi-telephone me-2"></i>Teléfono y móvil de contacto</li>
                                        <li><i class="bi bi-envelope me-2"></i>Email general y de facturación</li>
                                        <li><i class="bi bi-globe me-2"></i>Sitio web corporativo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Nuevos campos y dónde se usan -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-diagram-3-fill me-2"></i>Nuevos Campos y Uso en el Programa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary">Observaciones por Defecto (ES/EN)</h6>
                                    <ul class="list-unstyled small mb-0">
                                        <li><i class="bi bi-dot me-1"></i><strong>Se configura en:</strong> Mantenimiento de Empresas → sección "Observaciones por Defecto para Nuevos Presupuestos".</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Se utiliza en:</strong> Alta de <strong>nuevo presupuesto</strong>, precargando las observaciones de cabecera.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Importante:</strong> Solo afecta presupuestos nuevos; no modifica presupuestos ya creados.</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">PDF de Presupuestos</h6>
                                    <ul class="list-unstyled small mb-0">
                                        <li><i class="bi bi-dot me-1"></i><strong>Mostrar subtotales por fecha:</strong> controla si se imprime la línea "Subtotal Fecha ..." en el PDF.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Cabecera de firma:</strong> define el texto del bloque de firma de empresa en el PDF de presupuesto.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Se utiliza en:</strong> impresión/exportación PDF de presupuestos.</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-success">PDF de Albaranes de Carga</h6>
                                    <ul class="list-unstyled small mb-0">
                                        <li><i class="bi bi-dot me-1"></i><strong>Mostrar KITs detallados:</strong> incluye el desglose de componentes.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Mostrar observaciones técnicas:</strong> incluye observaciones de familias y artículos.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Mostrar observaciones de pie:</strong> incluye observaciones finales en el albarán.</li>
                                        <li><i class="bi bi-dot me-1"></i><strong>Se utiliza en:</strong> generación PDF del albarán de carga.</li>
                                    </ul>
                                </div>

                                <div class="alert alert-secondary py-2 mb-0">
                                    <small><i class="bi bi-sliders me-1"></i><strong>Avanzado:</strong> La configuración JSON de PDF está reservada para parametrizaciones técnicas y futuras ampliaciones.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Conceptos importantes -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0 fw-bold text-primary">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Conceptos Importantes
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-file-earmark-text text-success fa-2x me-2"></i>
                                        <span class="fw-bold">Plantillas por Empresa</span>
                                    </div>
                                    <p class="small text-muted">Las observaciones por defecto se guardan a nivel de empresa para estandarizar la creación de nuevos presupuestos.</p>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-file-pdf text-info fa-2x me-2"></i>
                                        <span class="fw-bold">Salida de Documentos PDF</span>
                                    </div>
                                    <p class="small text-muted">Los nuevos switches controlan el contenido visible en PDF de presupuestos y albaranes de carga.</p>
                                </div>

                                <div class="alert alert-warning py-2">
                                    <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> Cambiar estos campos impacta documentos nuevos que se generen o impriman desde ese momento.</small>
                                </div>

                                <div class="alert alert-danger py-2">
                                    <small><i class="bi bi-exclamation-octagon me-1"></i><strong>Crítico:</strong> Si desactiva opciones de contenido, el PDF puede omitir información operativa relevante para montaje/carga.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Reglas de negocio -->
                <div class="mt-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-primary mb-3">
                                <i class="bi bi-info-circle me-2"></i>Reglas de Negocio
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Código y NIF deben ser únicos en el sistema</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Solo UNA empresa ficticia principal permitida</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Empresas reales requieren datos fiscales completos</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Las observaciones por defecto se aplican solo al crear presupuestos nuevos</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Subtotales por fecha y cabecera de firma afectan la salida PDF de presupuesto</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Los 3 switches de albarán afectan solo el PDF de albarán de carga</li>
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
                                        <li><i class="bi bi-check2 text-success me-2"></i>Use códigos descriptivos y cortos (EMP001, TOLDOS)</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Complete todos los datos bancarios para cobros</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Configure series antes de emitir documentos</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Revise plantillas por idioma antes de crear nuevos presupuestos</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled small">
                                        <li><i class="bi bi-check2 text-success me-2"></i>Mantenga actualizados logotipos y textos legales</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Verifique que email de facturación sea correcto</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>No modifique series con documentos emitidos</li>
                                        <li><i class="bi bi-check2 text-success me-2"></i>Revise la vista previa PDF tras cambiar subtotales/firma/albarán</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Estructura de datos -->
                <div class="mt-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0 fw-bold text-primary">
                                <i class="bi bi-diagram-3 me-2"></i>Estructura de Datos de la Empresa
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-primary small">Identificación</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Código de empresa</li>
                                        <li>• Nombre legal</li>
                                        <li>• Nombre comercial</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-info small">Datos Fiscales</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• NIF</li>
                                        <li>• Dirección fiscal</li>
                                        <li>• CP, Población, Provincia</li>
                                        <li>• País</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-success small">Contacto</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Teléfono y móvil</li>
                                        <li>• Email general</li>
                                        <li>• Email facturación</li>
                                        <li>• Sitio web</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-secondary small">Datos Bancarios</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• IBAN</li>
                                        <li>• SWIFT/BIC</li>
                                        <li>• Nombre del banco</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-dark small">Series</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Presupuesto</li>
                                        <li>• Factura</li>
                                        <li>• Abono</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-danger small">PDF y Albarán</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Observaciones cabecera ES/EN</li>
                                        <li>• Subtotales por fecha (PDF ppto)</li>
                                        <li>• Cabecera firma (PDF ppto)</li>
                                        <li>• KITs y observaciones (PDF albarán)</li>
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
                        Versión del sistema: SMM v1.0 - Última actualización: 16-02-2026
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
    #modalAyudaEmpresas .modal-content {
        border-radius: 15px;
    }
    
    #modalAyudaEmpresas .card {
        transition: transform 0.2s ease-in-out;
    }
    
    #modalAyudaEmpresas .card:hover {
        transform: translateY(-2px);
    }
    
    #modalAyudaEmpresas .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
</style>
