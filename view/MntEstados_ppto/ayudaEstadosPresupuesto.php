<!-- Modal de Ayuda para Estados de Presupuesto -->
<div class="modal fade" id="modalAyudaEstadosPresupuesto" tabindex="-1" aria-labelledby="modalAyudaEstadosPresupuestoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaEstadosPresupuestoLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Estados de Presupuesto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Estados de Presupuesto? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-flag me-2"></i>
                        ¿Qué son los Estados de Presupuesto?
                    </h6>
                    <p class="text-muted">
                        Los estados de presupuesto definen las diferentes fases por las que puede pasar una cotización 
                        o presupuesto en el flujo de trabajo comercial. Permiten un seguimiento visual y organizado 
                        del progreso desde la creación hasta la finalización del proceso de venta.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Código -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCodigo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCodigo" aria-expanded="false" aria-controls="collapseCodigo">
                                    <i class="bi bi-upc me-2"></i>
                                    Código del Estado *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo del estado de presupuesto.
                                    <br><strong>Ejemplos:</strong> PEND, APROB, RECH, PROC, REV, FINAL
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 20 caracteres, debe ser único
                                    <br><strong>Formato recomendado:</strong> Letras mayúsculas, sin espacios
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tags me-2"></i>
                                    Nombre del Estado *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Nombre descriptivo del estado de presupuesto.
                                    <br><strong>Ejemplos:</strong> Pendiente, Aprobado, Rechazado, En Proceso, En Revisión
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Color -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingColor">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseColor" aria-expanded="false" aria-controls="collapseColor">
                                    <i class="bi bi-palette me-2"></i>
                                    Color del Estado *
                                </button>
                            </h2>
                            <div id="collapseColor" class="accordion-collapse collapse" aria-labelledby="headingColor" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Color en formato hexadecimal para identificación visual.
                                    <br><strong>Ejemplos:</strong> #28a745 (verde), #dc3545 (rojo), #ffc107 (amarillo), #007bff (azul)
                                    <br><strong>Formato:</strong> #RRGGBB (6 dígitos hexadecimales precedidos de #)
                                    <br><strong>Uso:</strong> Se muestra en badges, etiquetas y elementos visuales
                                </div>
                            </div>
                        </div>

                        <!-- Campo Orden -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOrden">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrden" aria-expanded="false" aria-controls="collapseOrden">
                                    <i class="bi bi-sort-numeric-up me-2"></i>
                                    Orden de Visualización
                                </button>
                            </h2>
                            <div id="collapseOrden" class="accordion-collapse collapse" aria-labelledby="headingOrden" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Número que determina el orden de aparición en listados.
                                    <br><strong>Ejemplos:</strong> 10, 20, 30, 40, 50 (permite intercalar nuevos estados)
                                    <br><strong>Validaciones:</strong> Número entero positivo entre 1 y 999
                                    <br><strong>Estrategia recomendada:</strong> Use valores de 10 en 10 (10, 20, 30...) para poder insertar estados intermedios en el futuro
                                    <br><strong>Ejemplo práctico:</strong> Borrador=10, Pendiente=20. Si necesita agregar "En Proceso" entre ambos, use 15
                                    <br><strong>Uso:</strong> Organiza el flujo lógico de estados en dropdowns
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Registro
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el estado está disponible para usar.
                                    <br><strong>Activo:</strong> Aparece en formularios y puede ser seleccionado
                                    <br><strong>Inactivo:</strong> No está disponible para nuevos presupuestos
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
                                    <strong>Campo opcional.</strong> Descripción adicional del estado y su propósito.
                                    <br><strong>Uso:</strong> Aclarar cuándo usar este estado, criterios, acciones requeridas
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Estados -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Estados Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Estados Iniciales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• <span class="badge" style="background-color: #6c757d;">Borrador</span> - En creación</li>
                                <li>• <span class="badge" style="background-color: #ffc107;">Pendiente</span> - Esperando revisión</li>
                                <li>• <span class="badge" style="background-color: #17a2b8;">En Proceso</span> - Siendo evaluado</li>
                                <li>• <span class="badge" style="background-color: #fd7e14;">En Revisión</span> - Cambios solicitados</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Estados Finales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• <span class="badge" style="background-color: #28a745;">Aprobado</span> - Aceptado por cliente</li>
                                <li>• <span class="badge" style="background-color: #dc3545;">Rechazado</span> - No aceptado</li>
                                <li>• <span class="badge" style="background-color: #6f42c1;">Facturado</span> - Convertido en factura</li>
                                <li>• <span class="badge" style="background-color: #343a40;">Cancelado</span> - Anulado</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Estrategia de Numeración para Orden -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-sort-numeric-up me-2"></i>
                        Estrategia de Numeración para Orden
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Numeración Recomendada:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Borrador = 10</li>
                                <li>• Pendiente = 20</li>
                                <li>• En Proceso = 30</li>
                                <li>• Aprobado = 40</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Inserción de Nuevos Estados:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Entre Borrador (10) y Pendiente (20)</li>
                                <li>• Nuevo "En Proceso" = 15</li>
                                <li>• Se coloca automáticamente entre ambos</li>
                                <li>• No requiere reorganizar números existentes</li>
                            </ul>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="bi bi-lightbulb me-2"></i>
                            ¿Por qué usar incrementos de 10?
                        </h6>
                        <p class="mb-0">
                            Esta estrategia permite flexibilidad futura. Si necesita agregar un nuevo estado entre dos existentes, 
                            simplemente use un número intermedio (ej: 15 entre 10 y 20) sin necesidad de reorganizar toda la secuencia.
                        </p>
                    </div>
                </div>

                <!-- Sección: Consejos de Uso -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-star-fill me-2"></i>
                        Consejos de Uso
                    </h6>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Mejores Prácticas
                        </h6>
                        <ul class="mb-0">
                            <li>Use códigos cortos y mnemotécnicos para facilitar la identificación</li>
                            <li>Asigne colores intuitivos (verde=bueno, rojo=malo, amarillo=atención)</li>
                            <li><strong>Configure el orden en incrementos de 10</strong> (10, 20, 30...) para facilitar inserción de nuevos estados</li>
                            <li>No elimine estados en uso, mejor desactívelos temporalmente</li>
                            <li>Use observaciones para documentar cuándo aplicar cada estado</li>
                            <li>Revise periódicamente los flujos para optimizar procesos</li>
                            <li>Mantenga un número razonable de estados (5-8 máximo)</li>
                        </ul>
                    </div>
                </div>

                <!-- Sección: Filtros y Búsqueda -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-funnel-fill me-2"></i>
                        Cómo usar Filtros y Búsqueda
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Búsqueda General:</h6>
                            <p class="text-muted small">
                                Use el campo de búsqueda superior para encontrar estados por cualquier dato.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtros por Columna:</h6>
                            <p class="text-muted small">
                                Use los campos del pie de tabla para filtrar por columnas específicas.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro de Estado:</h6>
                            <p class="text-muted small">
                                Use los botones superiores para filtrar por estado: Todos, Activos, Inactivos.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Limpiar Filtros:</h6>
                            <p class="text-muted small">
                                Use el botón "Limpiar Filtros" para restablecer todos los filtros aplicados.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sección: Iconos y Estados -->
                <div class="help-section">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-palette-fill me-2"></i>
                        Iconos y Estados en la Tabla
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Icono/Badge</th>
                                    <th>Descripción</th>
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success fa-2x"></i>
                                    </td>
                                    <td>Estado Activo</td>
                                    <td>Disponible para usar en presupuestos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Estado Inactivo</td>
                                    <td>No disponible para nuevos presupuestos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: #007bff; color: white;">Estado</span>
                                    </td>
                                    <td>Color del Estado</td>
                                    <td>Muestra el color asignado para identificación visual</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">5</span>
                                    </td>
                                    <td>Orden de Estado</td>
                                    <td>Número que indica el orden en el flujo de trabajo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del estado</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el estado (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el estado (solo si está inactivo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge rounded-circle bg-success text-white" style="font-size:0.75rem;">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </td>
                                    <td>Mostrar Detalles</td>
                                    <td>Expande la fila para ver observaciones adicionales</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-start flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                         Versión del sistema: MDR v1.1 - Última actualización: 16-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>