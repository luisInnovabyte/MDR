<!-- Modal de Ayuda para Coeficientes Reductores -->
<div class="modal fade" id="modalAyudaCoeficientes" tabindex="-1" aria-labelledby="modalAyudaCoeficientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaCoeficientesLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Coeficientes Reductores
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
    
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Coeficientes Reductores? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-calculator me-2"></i>
                        ¿Qué son los Coeficientes Reductores?
                    </h6>
                    <p class="text-muted">
                        Los coeficientes reductores son factores de descuento que se aplican automáticamente según el número 
                        de jornadas alquiladas. Permiten cobrar menos días de los realmente alquilados como incentivo por 
                        volumen, optimizando la competitividad comercial en alquileres de mayor duración.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Número de Jornadas -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingJornadas">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJornadas" aria-expanded="false" aria-controls="collapseJornadas">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    Número de Jornadas *
                                </button>
                            </h2>
                            <div id="collapseJornadas" class="accordion-collapse collapse" aria-labelledby="headingJornadas" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Número de jornadas alquiladas para aplicar este coeficiente.
                                    <br><strong>Ejemplos:</strong> 1, 2, 5, 10, 15, 30
                                    <br><strong>Validaciones:</strong> Número entero positivo, debe ser único (no puede repetirse)
                                    <br><strong>Uso:</strong> Determina cuándo aplicar automáticamente este reductor
                                </div>
                            </div>
                        </div>

                        <!-- Campo Días a Facturar -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDias">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDias" aria-expanded="false" aria-controls="collapseDias">
                                    <i class="bi bi-currency-euro me-2"></i>
                                    Días a Facturar *
                                </button>
                            </h2>
                            <div id="collapseDias" class="accordion-collapse collapse" aria-labelledby="headingDias" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Número de días que se facturarán realmente.
                                    <br><strong>Ejemplos:</strong> 8.50, 4.75, 9.25, 14.00
                                    <br><strong>Validaciones:</strong> Número decimal positivo, máximo 2 decimales
                                    <br><strong>Nota:</strong> Normalmente menor que las jornadas alquiladas (descuento por volumen)
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Coeficiente
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el coeficiente está disponible para usar.
                                    <br><strong>Activo:</strong> Se aplica automáticamente en cálculos de alquileres
                                    <br><strong>Inactivo:</strong> No se considera en los cálculos automáticos
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
                                    <strong>Campo opcional.</strong> Descripción del propósito y política de descuento.
                                    <br><strong>Uso:</strong> Documentar política comercial, casos especiales, justificación
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Coeficientes -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Coeficientes Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Descuentos Básicos:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• 1 jornada → 1.00 día (sin descuento)</li>
                                <li>• 2 jornadas → 1.75 días (descuento 0.25)</li>
                                <li>• 3 jornadas → 2.50 días (descuento 0.50)</li>
                                <li>• 5 jornadas → 4.25 días (descuento 0.75)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Descuentos por Volumen:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• 10 jornadas → 8.50 días (descuento 1.50)</li>
                                <li>• 15 jornadas → 12.75 días (descuento 2.25)</li>
                                <li>• 20 jornadas → 17.00 días (descuento 3.00)</li>
                                <li>• 30 jornadas → 25.50 días (descuento 4.50)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Funcionamiento del Sistema -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-gear-fill me-2"></i>
                        Funcionamiento del Sistema
                    </h6>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            ¿Cómo funciona automáticamente?
                        </h6>
                        <ul class="mb-0">
                            <li><strong>Detección automática:</strong> El sistema identifica el número de jornadas alquiladas</li>
                            <li><strong>Búsqueda de coeficiente:</strong> Localiza el coeficiente activo para esas jornadas</li>
                            <li><strong>Aplicación del descuento:</strong> Calcula automáticamente los días a facturar</li>
                            <li><strong>Facturación optimizada:</strong> Cobra menos días manteniendo la competitividad</li>
                        </ul>
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
                            <li>Cada número de jornadas debe ser único en el sistema</li>
                            <li>Configure descuentos progresivos (más jornadas = mayor descuento)</li>
                            <li>Use máximo 2 decimales para evitar errores de cálculo</li>
                            <li>Documente la política comercial en observaciones</li>
                            <li>Revise periódicamente los coeficientes</li>
                        
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
                                Use el campo de búsqueda superior para encontrar coeficientes por cualquier dato.
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
                                    <td>Coeficiente Activo</td>
                                    <td>Disponible para cálculos automáticos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Coeficiente Inactivo</td>
                                    <td>No se considera en cálculos automáticos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary">10</span>
                                    </td>
                                    <td>Número de Jornadas</td>
                                    <td>Jornadas para las que se aplica este coeficiente</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-success">8.50</span>
                                    </td>
                                    <td>Días a Facturar</td>
                                    <td>Días que realmente se cobrarán</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del coeficiente</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el coeficiente (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el coeficiente (solo si está inactivo)</td>
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
                <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: SMM v1.0 - Última actualización: 24-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button>
            </div>
    </div>
</div>