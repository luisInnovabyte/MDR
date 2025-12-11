<!-- Modal de Ayuda para Presupuestos -->
<div class="modal fade" id="modalAyudaPresupuestos" tabindex="-1" aria-labelledby="modalAyudaPresupuestosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaPresupuestosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Presupuestos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Presupuestos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        ¿Qué son los Presupuestos en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Los presupuestos son documentos comerciales que detallan los costos estimados de productos o servicios
                        para un cliente. El sistema permite crear, gestionar y dar seguimiento a presupuestos desde su creación
                        hasta su aceptación o rechazo.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Número de Presupuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNumero">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNumero" aria-expanded="false" aria-controls="collapseNumero">
                                    <i class="bi bi-hash me-2"></i>
                                    Número de Presupuesto *
                                </button>
                            </h2>
                            <div id="collapseNumero" class="accordion-collapse collapse" aria-labelledby="headingNumero" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Identificador único del presupuesto.
                                    <br><strong>Ejemplos:</strong> P2025-0001, PPTO-001, 2025/001
                                    <br><strong>Validaciones:</strong> Máximo 50 caracteres, debe ser único en el sistema
                                </div>
                            </div>
                        </div>

                        <!-- Campo Cliente -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCliente">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCliente" aria-expanded="false" aria-controls="collapseCliente">
                                    <i class="bi bi-person me-2"></i>
                                    Cliente *
                                </button>
                            </h2>
                            <div id="collapseCliente" class="accordion-collapse collapse" aria-labelledby="headingCliente" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Cliente al que se emite el presupuesto.
                                    <br><strong>Nota:</strong> Solo aparecen clientes activos en el sistema
                                    <br><strong>Importante:</strong> Asegúrese de seleccionar el cliente correcto antes de guardar
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Presupuesto *
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Estado actual del presupuesto.
                                    <br><strong>Estados comunes:</strong> Pendiente, Enviado, Aceptado, Rechazado
                                    <br><strong>Nota:</strong> El estado ayuda a llevar control del proceso comercial
                                </div>
                            </div>
                        </div>

                        <!-- Campo Fecha Presupuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFecha">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFecha" aria-expanded="false" aria-controls="collapseFecha">
                                    <i class="bi bi-calendar me-2"></i>
                                    Fecha de Presupuesto *
                                </button>
                            </h2>
                            <div id="collapseFecha" class="accordion-collapse collapse" aria-labelledby="headingFecha" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Fecha de emisión del presupuesto.
                                    <br><strong>Formato:</strong> DD/MM/AAAA
                                    <br><strong>Por defecto:</strong> Se establece la fecha actual al crear un nuevo presupuesto
                                </div>
                            </div>
                        </div>

                        <!-- Campo Fecha Validez -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingValidez">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseValidez" aria-expanded="false" aria-controls="collapseValidez">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Fecha de Validez
                                </button>
                            </h2>
                            <div id="collapseValidez" class="accordion-collapse collapse" aria-labelledby="headingValidez" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Fecha hasta la que es válido el presupuesto.
                                    <br><strong>Uso:</strong> Indica al cliente hasta cuándo están vigentes los precios
                                    <br><strong>Ejemplo:</strong> Presupuesto válido hasta el 31/12/2025
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre Evento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEvento">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEvento" aria-expanded="false" aria-controls="collapseEvento">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Nombre del Evento
                                </button>
                            </h2>
                            <div id="collapseEvento" class="accordion-collapse collapse" aria-labelledby="headingEvento" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Nombre del evento o proyecto.
                                    <br><strong>Ejemplos:</strong> Concierto Anual 2025, Boda María y Juan, Conferencia Tech
                                    <br><strong>Máximo:</strong> 255 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Campo Ubicación Evento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingUbicacion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUbicacion" aria-expanded="false" aria-controls="collapseUbicacion">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Ubicación del Evento
                                </button>
                            </h2>
                            <div id="collapseUbicacion" class="accordion-collapse collapse" aria-labelledby="headingUbicacion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campos opcionales.</strong> Ubicación detallada donde se realizará el evento.
                                    <br><strong>Dirección:</strong> Calle, número, local (máx. 100 caracteres)
                                    <br><strong>Población:</strong> Ciudad o pueblo (máx. 80 caracteres)
                                    <br><strong>Código Postal:</strong> CP del evento (máx. 10 caracteres)
                                    <br><strong>Provincia:</strong> Provincia del evento (máx. 80 caracteres)
                                    <br><strong>Ejemplos:</strong> Calle Mayor 123, Badajoz, 06006, Badajoz
                                </div>
                            </div>
                        </div>

                        <!-- Campo Fechas Evento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFechasEvento">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFechasEvento" aria-expanded="false" aria-controls="collapseFechasEvento">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    Fechas de Inicio y Fin del Evento
                                </button>
                            </h2>
                            <div id="collapseFechasEvento" class="accordion-collapse collapse" aria-labelledby="headingFechasEvento" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campos opcionales.</strong> Fechas de inicio y finalización del evento/servicio.
                                    <br><strong>Uso:</strong> Para calcular la duración del evento y planificar logística
                                    <br><strong>Nota:</strong> La fecha de fin debe ser igual o posterior a la fecha de inicio
                                </div>
                            </div>
                        </div>

                        <!-- Campo Observaciones -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObservaciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservaciones" aria-expanded="false" aria-controls="collapseObservaciones">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Observaciones
                                </button>
                            </h2>
                            <div id="collapseObservaciones" class="accordion-collapse collapse" aria-labelledby="headingObservaciones" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campos opcionales.</strong> Notas y comentarios sobre el presupuesto.
                                    <br><strong>Observaciones de cabecera:</strong> Aparecen al inicio del PDF del presupuesto
                                    <br><strong>Observaciones de pie:</strong> Aparecen al final del PDF del presupuesto
                                    <br><strong>Observaciones internas:</strong> No se imprimen, solo para uso interno
                                    <br><strong>Switches:</strong> Controlan si se muestran observaciones de familias y artículos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Funcionalidades de la Tabla -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-table me-2"></i>
                        Funcionalidades de la Tabla de Presupuestos
                    </h6>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-plus-circle text-success me-2"></i>
                            <strong>Nuevo Presupuesto:</strong> Crea un nuevo registro de presupuesto
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-pencil text-info me-2"></i>
                            <strong>Editar:</strong> Modifica los datos de un presupuesto existente
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-trash text-danger me-2"></i>
                            <strong>Desactivar:</strong> Desactiva el presupuesto (no lo elimina)
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-arrow-clockwise text-success me-2"></i>
                            <strong>Activar:</strong> Reactiva un presupuesto desactivado
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-search me-2"></i>
                            <strong>Buscar:</strong> Filtra presupuestos por diferentes criterios
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-eye text-primary me-2"></i>
                            <strong>Ver detalles:</strong> Haz clic en el botón + para ver información completa
                        </li>
                    </ul>
                </div>

                <!-- Sección: Consejos y Buenas Prácticas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb me-2"></i>
                        Consejos y Buenas Prácticas
                    </h6>
                    
                    <div class="alert alert-info" role="alert">
                        <ul class="mb-0">
                            <li>Utilice números de presupuesto secuenciales para facilitar el seguimiento</li>
                            <li>Incluya siempre las fechas del evento para una mejor planificación</li>
                            <li>Utilice las observaciones internas para notas que no deben ver los clientes</li>
                            <li>Revise el estado del presupuesto regularmente para mantener actualizado el proceso</li>
                            <li>Use la fecha de validez para presupuestos con precios sujetos a cambios</li>
                        </ul>
                    </div>
                </div>

                <!-- Sección: Estados del Presupuesto -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Estados Típicos del Presupuesto
                    </h6>
                    
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <i class="bi bi-hourglass-split text-warning me-1"></i>
                                        Pendiente
                                    </h6>
                                    <small class="text-muted">Presupuesto creado pero aún no enviado al cliente</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <i class="bi bi-send text-info me-1"></i>
                                        Enviado
                                    </h6>
                                    <small class="text-muted">Presupuesto enviado al cliente, esperando respuesta</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <i class="bi bi-check-circle text-success me-1"></i>
                                        Aceptado
                                    </h6>
                                    <small class="text-muted">Cliente aceptó el presupuesto</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1">
                                        <i class="bi bi-x-circle text-danger me-1"></i>
                                        Rechazado
                                    </h6>
                                    <small class="text-muted">Cliente rechazó el presupuesto</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Preguntas Frecuentes -->
                <div class="help-section">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-question-octagon me-2"></i>
                        Preguntas Frecuentes
                    </h6>
                    
                    <div class="accordion" id="accordionFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFAQ1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFAQ1" aria-expanded="false" aria-controls="collapseFAQ1">
                                    ¿Puedo editar un presupuesto después de enviarlo?
                                </button>
                            </h2>
                            <div id="collapseFAQ1" class="accordion-collapse collapse" aria-labelledby="headingFAQ1" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    Sí, puede editar un presupuesto en cualquier momento. Sin embargo, es recomendable
                                    crear un nuevo presupuesto si el original ya fue aceptado o si los cambios son significativos.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFAQ2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFAQ2" aria-expanded="false" aria-controls="collapseFAQ2">
                                    ¿Qué sucede si desactivo un presupuesto?
                                </button>
                            </h2>
                            <div id="collapseFAQ2" class="accordion-collapse collapse" aria-labelledby="headingFAQ2" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    Al desactivar un presupuesto, este ya no aparecerá en las búsquedas activas, pero
                                    puede reactivarlo en cualquier momento. Los datos no se eliminan del sistema.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFAQ3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFAQ3" aria-expanded="false" aria-controls="collapseFAQ3">
                                    ¿Cómo se calcula la duración del evento?
                                </button>
                            </h2>
                            <div id="collapseFAQ3" class="accordion-collapse collapse" aria-labelledby="headingFAQ3" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    Si especifica las fechas de inicio y fin del evento, el sistema calcula automáticamente
                                    la duración en días. Esta información es útil para planificar recursos y logística.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
