<!-- Modal de Ayuda para Consulta de Mantenimientos -->
<div class="modal fade" id="modalAyudaMantenimientos" tabindex="-1" aria-labelledby="modalAyudaMantenimientosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaMantenimientosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Consulta de Mantenimientos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">

                <!-- ¿Qué es esta pantalla? -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-eye me-2"></i>
                        ¿Qué es la Consulta de Mantenimientos?
                    </h6>
                    <p class="text-muted">
                        Esta pantalla permite consultar el <strong>estado de mantenimiento</strong> de cada artículo y sus elementos,
                        mostrando datos clave como fechas de vencimiento, estado actual y datos identificativos del equipo.
                        Es una vista de <strong>solo lectura</strong>, destinada principalmente a técnicos.
                    </p>
                    <div class="alert alert-warning py-2">
                        <small>
                            <i class="bi bi-lock me-1"></i>
                            No es posible editar, crear ni eliminar registros desde esta pantalla.
                        </small>
                    </div>
                </div>

               

                <!-- Sección: Funcionalidades -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-gear me-2"></i>
                        Funcionalidades Disponibles
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-search me-1"></i>Búsqueda y Filtrado</h6>
                            <ul class="text-muted small">
                                <li>Buscar por ID, artículo o código</li>
                                <li>Filtrar por marca, modelo o número de serie</li>
                                <li>Filtrar por ubicación física</li>
                                <li>Filtrar por estado de mantenimiento (Atrasado, Al día, Próximo, Sin Programar)</li>
                                <li>Buscar por fecha del próximo mantenimiento</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-eye me-1"></i>Visualización</h6>
                            <ul class="text-muted small">
                                <li>Expandir fila para ver detalles adicionales (si está habilitado)</li>
                                <li>Identificación visual de registros con mantenimiento próximo o atrasado</li>
                                <li>Consulta de información técnica relacionada (marca, modelo, n° serie)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Información Visible -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Información Disponible en la Tabla
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ID</strong></td>
                                    <td>Identificador interno del registro</td>
                                </tr>
                                <tr>
                                    <td><strong>Artículo</strong></td>
                                    <td>Nombre o referencia del artículo al que pertenece el bien</td>
                                </tr>
                                <tr>
                                    <td><strong>Código</strong></td>
                                    <td>Código único asignado al bien</td>
                                </tr>
                                <tr>
                                    <td><strong>Descripción</strong></td>
                                    <td>Descripción técnica o comentario interno del bien</td>
                                </tr>
                                <tr>
                                    <td><strong>Marca</strong></td>
                                    <td>Marca del fabricante</td>
                                </tr>
                                <tr>
                                    <td><strong>Modelo</strong></td>
                                    <td>Modelo específico del bien</td>
                                </tr>
                                <tr>
                                    <td><strong>N° Serie</strong></td>
                                    <td>Número de serie único del equipo</td>
                                </tr>
                                <tr>
                                    <td><strong>Ubicación</strong></td>
                                    <td>Ubicación física (nave/almacén, pasillo, columna, nivel)</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Mantenimiento</strong></td>
                                    <td>Estado actual: Atrasado, Al día, Próximo a Vencer, Sin Programar</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Prox. Mantenimiento</strong></td>
                                    <td>Fecha programada para la siguiente intervención</td>
                                </tr>
                                <tr>
                                    <td><strong>Activo</strong></td>
                                    <td>Indica si el bien está activo en el sistema (1 = activo, 0 = inactivo)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Cómo Usar -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-book me-2"></i>
                        Cómo Usar esta Pantalla
                    </h6>
                    <ol class="text-muted">
                        <li><strong>Aplicar filtros:</strong> Usa los campos del pie de tabla para filtrar por cada columna.</li>
                        <li><strong>Detectar prioridades:</strong> Los registros con fecha próximo mantenimiento suelen destacarse visualmente.</li>
                        <li><strong>Ver detalles:</strong> Amplía la fila con el control correspondiente para ver información extra (si está disponible).</li>
                        <li><strong>Limpiar filtros:</strong> Si aparece la alerta de filtros activos, usa el botón "Limpiar filtros".</li>
                        <li><strong>Solo consulta:</strong> Esta pantalla no permite editar ni crear programaciones de mantenimiento.</li>
                    </ol>
                </div>

                <!-- Sección: Iconos y Estados -->
                <div class="help-section">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-palette-fill me-2"></i>
                        Iconos y Estados
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Icono/Badge</th>
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-danger">Atrasado</span>
                                    </td>
                                    <td>El mantenimiento programado está vencido y requiere atención.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-success">Al día</span>
                                    </td>
                                    <td>El equipo está dentro del ciclo de mantenimiento previsto.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">Próximo</span>
                                    </td>
                                    <td>El próximo mantenimiento está próximo a realizarse.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">Sin Programar</span>
                                    </td>
                                    <td>No hay una fecha programada para el mantenimiento.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success fa-2x"></i>
                                    </td>
                                    <td>Elemento activo en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Elemento inactivo o dado de baja</td>
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
                        Sistema MDR - Consulta de Mantenimientos (Solo Lectura) - <?php echo date('d-m-Y'); ?>
                    </small>
                </div>
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
