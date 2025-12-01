<!-- Modal de Ayuda para Consulta de Elementos -->
<div class="modal fade" id="modalAyudaElementos" tabindex="-1" aria-labelledby="modalAyudaElementosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaElementosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Consulta de Elementos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué es esta pantalla? -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-eye me-2"></i>
                        ¿Qué es la Consulta de Elementos?
                    </h6>
                    <p class="text-muted">
                        Esta es una pantalla de <strong>solo consulta</strong> diseñada para que los técnicos puedan 
                        visualizar rápidamente información sobre todos los elementos del inventario sin posibilidad de 
                        modificar datos. Aquí puedes:
                    </p>
                    <ul class="text-muted">
                        <li>Ver todos los elementos de todos los artículos</li>
                        <li>Consultar ubicaciones, estados y características técnicas</li>
                        <li>Filtrar y buscar elementos específicos</li>
                        <li>Ver elementos agrupados por artículo</li>
                    </ul>
                    <div class="alert alert-warning py-2">
                        <small>
                            <strong><i class="bi bi-lock me-1"></i>Nota importante:</strong>
                            Esta pantalla es de <strong>SOLO LECTURA</strong>. No se pueden crear, editar ni eliminar elementos desde aquí.
                            Para realizar cambios, contacta con el administrador del sistema.
                        </small>
                    </div>
                </div>

                <!-- Sección: ¿Qué son los Elementos? -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-box-seam me-2"></i>
                        ¿Qué son los Elementos?
                    </h6>
                    <p class="text-muted">
                        Los elementos son las <strong>unidades físicas individuales</strong> de cada artículo. Por ejemplo:
                    </p>
                    <ul class="text-muted">
                        <li><strong>Artículo:</strong> "Micrófono inalámbrico Shure SM58"</li>
                        <li><strong>Elementos:</strong> 
                            <ul>
                                <li>Micrófono #001 (N° serie: ABC123) - En almacén</li>
                                <li>Micrófono #002 (N° serie: ABC124) - Alquilado</li>
                                <li>Micrófono #003 (N° serie: ABC125) - En reparación</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="alert alert-info py-2">
                        <small>
                            <strong>Jerarquía del sistema:</strong>
                            <br><strong>GRUPO</strong> → <strong>FAMILIA</strong> → <strong>ARTÍCULO</strong> → <strong>ELEMENTO</strong> (unidad física)
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
                                <li>Buscar por código de elemento</li>
                                <li>Filtrar por artículo</li>
                                <li>Filtrar por marca</li>
                                <li>Filtrar por modelo</li>
                                <li>Filtrar por número de serie</li>
                                <li>Filtrar por estado</li>
                                <li>Buscar por ubicación</li>
                                <li>Filtrar por activo/inactivo</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary"><i class="bi bi-eye me-1"></i>Visualización</h6>
                            <ul class="text-muted small">
                                <li>Ver detalles expandidos (botón <i class="bi bi-plus-circle"></i>)</li>
                                <li>Elementos agrupados por artículo</li>
                                <li>Vista de ubicación detallada</li>
                                <li>Información de garantía y mantenimiento</li>
                                <li>Observaciones técnicas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Información Visible -->
                <div class="help-section mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Información Disponible
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
                                    <td><strong>Artículo</strong></td>
                                    <td>Nombre del artículo al que pertenece el elemento</td>
                                </tr>
                                <tr>
                                    <td><strong>Código</strong></td>
                                    <td>Código único del elemento (ej: 0001-001)</td>
                                </tr>
                                <tr>
                                    <td><strong>Descripción</strong></td>
                                    <td>Descripción detallada del elemento</td>
                                </tr>
                                <tr>
                                    <td><strong>Marca</strong></td>
                                    <td>Fabricante del elemento</td>
                                </tr>
                                <tr>
                                    <td><strong>Modelo</strong></td>
                                    <td>Modelo específico del elemento</td>
                                </tr>
                                <tr>
                                    <td><strong>N° Serie</strong></td>
                                    <td>Número de serie único del fabricante</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td>Estado actual (Disponible, Alquilado, En reparación, etc.)</td>
                                </tr>
                                <tr>
                                    <td><strong>Ubicación</strong></td>
                                    <td>Nave/Almacén | Pasillo/Columna | Altura/Nivel</td>
                                </tr>
                                <tr>
                                    <td><strong>Activo</strong></td>
                                    <td>Indica si el elemento está activo o inactivo</td>
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
                        <li><strong>Buscar elementos:</strong> Usa los campos de filtro en la parte inferior de cada columna</li>
                        <li><strong>Ver detalles:</strong> Haz clic en el icono <i class="bi bi-plus-circle text-primary"></i> al inicio de cada fila para ver información completa</li>
                        <li><strong>Limpiar filtros:</strong> Usa el botón "Limpiar filtros" si aparece el aviso de filtros activos</li>
                        <li><strong>Agrupar por artículo:</strong> La tabla agrupa automáticamente los elementos por artículo</li>
                        <li><strong>Consultar ubicación:</strong> Revisa la columna "Ubicación" para saber dónde está cada elemento</li>
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
                                        <i class="bi bi-check-circle text-success fa-2x"></i>
                                    </td>
                                    <td>Elemento Activo - Disponible en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Elemento Inactivo - No disponible</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: #4CAF50;">Disponible</span>
                                    </td>
                                    <td>El elemento está disponible para alquiler</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: #2196F3;">Alquilado</span>
                                    </td>
                                    <td>El elemento está actualmente alquilado</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: #FF9800;">En reparación</span>
                                    </td>
                                    <td>El elemento requiere reparación</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: #F44336;">Dado de baja</span>
                                    </td>
                                    <td>El elemento ha sido dado de baja</td>
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
                        Sistema MDR - Consulta de Elementos (Solo Lectura) - <?php echo date('d-m-Y'); ?>
                    </small>
                </div>
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
