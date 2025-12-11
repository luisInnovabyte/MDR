<!-- Modal de Ayuda para Consulta de Garantías -->
<div class="modal fade" id="modalAyudaGarantias" tabindex="-1" aria-labelledby="modalAyudaGarantiasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaGarantiasLabel">
                    <i class="bi bi-shield-check me-2 fs-4"></i>
                    Ayuda - Consulta de Garantías
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- ¿Qué es esta pantalla? -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-eye me-2"></i>
                        ¿Qué es la Consulta de Garantías?
                    </h6>
                    <p class="text-muted">
                        Esta pantalla permite consultar el <strong>estado de garantía</strong> de cada artículo y sus elementos,
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
                                <li>Filtrar por estado de garantía (Vigente, Próximo a vencer, Vencida)</li>
                                <li>Buscar por fecha del próximo mantenimiento</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-eye me-1"></i>Visualización</h6>
                            <ul class="text-muted small">
                                <li>Expandir fila para ver detalles adicionales (si está habilitado)</li>
                                <li>Identificación visual de registros con garantías Vigentes o vencidas</li>
                                <li>Consulta de información técnica relacionada (marca, modelo, n° serie)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Información que se muestra -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Información mostrada por cada registro
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
                                <tr><td><strong>ID</strong></td><td>ID interno del elemento</td></tr>
                                <tr><td><strong>Artículo</strong></td><td>Nombre del artículo al que pertenece</td></tr>
                                <tr><td><strong>Código</strong></td><td>Código único del elemento</td></tr>
                                <tr><td><strong>Descripción</strong></td><td>Descripción técnica o interna</td></tr>
                                <tr><td><strong>Marca</strong></td><td>Marca del fabricante</td></tr>
                                <tr><td><strong>Modelo</strong></td><td>Modelo del equipo</td></tr>
                                <tr><td><strong>N° Serie</strong></td><td>Número de serie único</td></tr>
                                <tr><td><strong>Ubicación</strong></td><td>Posición actual en almacén o nave</td></tr>
                                <tr><td><strong>Estado Garantía</strong></td><td>Vigente, Próxima a vencer o Vencida</td></tr>
                                <tr><td><strong>Fin Garantía</strong></td><td>Fecha exacta de expiración de garantía</td></tr>
                                <tr><td><strong>Activo</strong></td><td>Indica si el elemento está activo en el sistema</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Funcionalidades -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-gear me-2"></i>
                        Funcionalidades disponibles
                    </h6>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-search me-1"></i>Filtros y búsqueda</h6>
                            <ul class="text-muted small">
                                <li>Buscar por artículo</li>
                                <li>Buscar por código</li>
                                <li>Buscar por marca o modelo</li>
                                <li>Buscar por número de serie</li>
                                <li>Filtrar por ubicación</li>
                                <li>Filtrar por estado de garantía</li>
                                <li>Filtrar por activo/inactivo</li>
                                <li>Buscar por fecha de fin de garantía</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-eye me-1"></i>Visualización</h6>
                            <ul class="text-muted small">
                                <li>Expandir detalles con el botón <i class="bi bi-plus-circle"></i></li>
                                <li>Identificar rápidamente equipos con garantía próxima a vencer</li>
                                <li>Revisar información de garantía sin riesgo a modificar datos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Estados visuales -->
                <div>
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-palette-fill me-2"></i>
                        Estados de garantía
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Badge</th><th>Significado</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><span class="badge bg-success">Vigente</span></td>
                                    <td>Cobertura activa</td>
                                </tr>
                                <tr>
                                    <td class="text-center"><span class="badge bg-warning">Próxima a vencer</span></td>
                                    <td>La garantía está por caducar pronto</td>
                                </tr>
                                <tr>
                                    <td class="text-center"><span class="badge bg-danger">Vencida</span></td>
                                    <td>La garantía ya expiró</td>
                                </tr>
                                <tr>
                                    <td class="text-center"><i class="bi bi-check-circle text-success fs-4"></i></td>
                                    <td>Elemento activo en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center"><i class="bi bi-x-circle text-danger fs-4"></i></td>
                                    <td>Elemento inactivo o dado de baja</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Pie -->
            <div class="modal-footer bg-light">
                <small class="text-muted me-auto">
                    <i class="bi bi-clock me-1"></i>
                    Sistema MDR - Consulta de Garantías - <?php echo date('d-m-Y'); ?>
                </small>
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>

        </div>
    </div>
</div>
