<!-- Modal de Ayuda para Estados de Elemento -->
<div class="modal fade" id="modalAyudaEstadosElemento" tabindex="-1" aria-labelledby="modalAyudaEstadosElementoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaEstadosElementoLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Estados de Elemento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Estados de Elemento? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-flag me-2"></i>
                        ¿Qué son los Estados de Elemento?
                    </h6>
                    <p class="text-muted">
                        Los estados de elemento definen las diferentes situaciones en las que se puede encontrar un elemento físico
                        del inventario (equipos, materiales, dispositivos, etc.). Permiten un control preciso sobre la disponibilidad,
                        ubicación y condición de cada elemento, facilitando la gestión del alquiler y mantenimiento.
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
                                    <strong>Campo obligatorio.</strong> Código único identificativo del estado de elemento.
                                    <br><strong>Ejemplos:</strong> DISP (Disponible), ALQU (Alquilado), REPA (En reparación), BAJA (Dado de baja), TERC (De terceros)
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 20 caracteres, debe ser único
                                    <br><strong>Formato recomendado:</strong> Letras mayúsculas, sin espacios
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-tags me-2"></i>
                                    Descripción del Estado *
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Descripción clara del estado de elemento.
                                    <br><strong>Ejemplos:</strong> Disponible, Alquilado, En reparación, Dado de baja, En mantenimiento, De terceros, En depósito, En tránsito
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 50 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Color -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingColor">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseColor" aria-expanded="false" aria-controls="collapseColor">
                                    <i class="bi bi-palette me-2"></i>
                                    Color del Estado
                                </button>
                            </h2>
                            <div id="collapseColor" class="accordion-collapse collapse" aria-labelledby="headingColor" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Color en formato hexadecimal para identificación visual.
                                    <br><strong>Ejemplos:</strong> #4CAF50 (verde - Disponible), #2196F3 (azul - Alquilado), #FF9800 (naranja - En reparación), #F44336 (rojo - Baja)
                                    <br><strong>Formato:</strong> #RRGGBB (6 dígitos hexadecimales precedidos de #)
                                    <br><strong>Uso:</strong> Se muestra en badges, etiquetas y elementos visuales del inventario
                                </div>
                            </div>
                        </div>

                        <!-- Campo Permite Alquiler -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPermiteAlquiler">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePermiteAlquiler" aria-expanded="false" aria-controls="collapsePermiteAlquiler">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Permite Alquiler
                                </button>
                            </h2>
                            <div id="collapsePermiteAlquiler" class="accordion-collapse collapse" aria-labelledby="headingPermiteAlquiler" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo importante.</strong> Define si los elementos en este estado pueden ser alquilados.
                                    <br><strong>Sí permite:</strong> El elemento está disponible para incluir en presupuestos y alquileres
                                    <br><strong>No permite:</strong> El elemento no puede ser alquilado (ej: en reparación, dado de baja)
                                    <br><strong>Uso crítico:</strong> Controla la lógica de negocio para asignación de elementos a proyectos
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Sí permiten:</strong> Disponible, De terceros</li>
                                        <li><strong>No permiten:</strong> Alquilado, En reparación, Dado de baja, En mantenimiento</li>
                                    </ul>
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
                                    <br><strong>Activo:</strong> Aparece en formularios y puede ser seleccionado para elementos
                                    <br><strong>Inactivo:</strong> No está disponible para asignar a elementos nuevos
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
                                    <br><strong>Uso:</strong> Aclarar cuándo usar este estado, criterios, acciones requeridas, políticas
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
                            <h6 class="text-secondary">Estados que permiten alquiler:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• <span class="badge" style="background-color: #4CAF50;">DISP</span> - Disponible</li>
                                <li>• <span class="badge" style="background-color: #9C27B0;">TERC</span> - De terceros</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Estados que NO permiten alquiler:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• <span class="badge" style="background-color: #2196F3;">ALQU</span> - Alquilado</li>
                                <li>• <span class="badge" style="background-color: #FF9800;">REPA</span> - En reparación</li>
                                <li>• <span class="badge" style="background-color: #F44336;">BAJA</span> - Dado de baja</li>
                                <li>• <span class="badge" style="background-color: #FFC107;">MANT</span> - En mantenimiento</li>
                                <li>• <span class="badge" style="background-color: #607D8B;">DEPO</span> - En depósito</li>
                                <li>• <span class="badge" style="background-color: #00BCD4;">TRAN</span> - En tránsito</li>
                            </ul>
                        </div>
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
                            <li>Use códigos cortos (4 letras máximo) y descriptivos</li>
                            <li>Asigne colores coherentes con el significado (verde=bueno, rojo=problema, amarillo=atención)</li>
                            <li><strong>Configure "Permite alquiler" correctamente</strong> para controlar la disponibilidad de elementos</li>
                            <li>Mantenga estados simples y claros (5-8 estados máximo)</li>
                            <li>No elimine estados en uso, mejor desactívelos temporalmente</li>
                            <li>Documente en observaciones las políticas de cada estado</li>
                            <li>Revise periódicamente los elementos en cada estado</li>
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
                            <h6 class="text-secondary">Filtro Permite Alquiler:</h6>
                            <p class="text-muted small">
                                Filtre estados que permiten o no permiten alquiler de elementos.
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
                                    <td>Disponible para asignar a elementos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Estado Inactivo</td>
                                    <td>No disponible para nuevos elementos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-success">SÍ</span>
                                    </td>
                                    <td>Permite Alquiler</td>
                                    <td>Los elementos en este estado pueden ser alquilados</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-danger">NO</span>
                                    </td>
                                    <td>No Permite Alquiler</td>
                                    <td>Los elementos en este estado NO pueden ser alquilados</td>
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
                         Versión del sistema: MDR v1.1 - Última actualización: 24-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
