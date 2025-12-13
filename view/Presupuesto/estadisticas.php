<!-- ========================================
     MODAL DE ESTADÍSTICAS DE PRESUPUESTOS
     ======================================== -->
<div class="modal fade" id="modalEstadisticasPresupuestos" tabindex="-1" role="dialog" aria-labelledby="modalEstadisticasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <!-- Encabezado del modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEstadisticasLabel">
                    <i class="fas fa-chart-bar me-2"></i>
                    Estadísticas de Presupuestos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <!-- Estadísticas Generales -->
                <section class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-chart-line me-2"></i>Estadísticas Generales
                    </h6>
                    <div class="row justify-content-center">
                        <!-- Total Presupuestos -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-1" id="modal-stat-total">-</h3>
                                    <p class="text-muted mb-0 small">Total Activos</p>
                                </div>
                            </div>
                        </div>

                        <!-- Aprobados -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="mb-1 text-success" id="modal-stat-aprobados">-</h3>
                                    <p class="text-muted mb-0 small">Aprobados</p>
                                </div>
                            </div>
                        </div>

                        <!-- En Proceso + Pendientes + Esperando -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h3 class="mb-1 text-warning" id="modal-stat-pendientes">-</h3>
                                    <p class="text-muted mb-0 small">En Proceso/Pendientes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tasa de Conversión -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                    <h3 class="mb-1 text-info" id="modal-stat-conversion">-</h3>
                                    <p class="text-muted mb-0 small">Tasa Conversión</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Distribución por Estados -->
                <section class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Distribución por Estados
                    </h6>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-info me-2"></i>En Proceso</span>
                                        <strong id="modal-dist-proceso">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-warning me-2"></i>Pendiente Revisión</span>
                                        <strong id="modal-dist-pendiente">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-primary me-2"></i>Esperando Respuesta</span>
                                        <strong id="modal-dist-esperando">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-circle text-success me-2"></i>Aprobados</span>
                                        <strong id="modal-dist-aprobados">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-danger me-2"></i>Rechazados</span>
                                        <strong id="modal-dist-rechazados">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-secondary me-2"></i>Cancelados</span>
                                        <strong id="modal-dist-cancelados">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-success me-2"></i>Vigentes</span>
                                        <strong id="modal-dist-vigentes">-</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><i class="fas fa-circle text-warning me-2"></i>Por caducar (7 días)</span>
                                        <strong id="modal-dist-por-caducar">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Estadísticas del Mes Actual -->
                <section class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>Mes Actual
                    </h6>
                    <div class="row justify-content-center">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1 small">Total del Mes</p>
                                    <h4 class="mb-0" id="modal-mes-total">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1 small">Aceptados</p>
                                    <h4 class="mb-0 text-success" id="modal-mes-aceptados">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1 small">Pendientes</p>
                                    <h4 class="mb-0 text-warning" id="modal-mes-pendientes">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1 small">Rechazados</p>
                                    <h4 class="mb-0 text-danger" id="modal-mes-rechazados">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Alertas y Eventos -->
                <section class="mb-3">
                    <h6 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>Alertas y Eventos
                    </h6>
                    <div class="row justify-content-center">
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <p class="mb-1"><i class="fas fa-hourglass-half text-warning me-2"></i>Caduca hoy</p>
                                    <h4 class="mb-0" id="modal-alert-caduca-hoy">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <p class="mb-1"><i class="fas fa-times-circle text-danger me-2"></i>Caducados</p>
                                    <h4 class="mb-0" id="modal-alert-caducados">-</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <p class="mb-1"><i class="fas fa-calendar-check text-info me-2"></i>Eventos próximos (7 días)</p>
                                    <h4 class="mb-0" id="modal-alert-eventos-proximos">-</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Información de Debug (oculta por defecto) -->
                <section id="modal-debug-section" style="display: none;">
                    <h6 class="text-danger border-bottom pb-2 mb-3">
                        <i class="fas fa-bug me-2"></i>Información de Debug
                    </h6>
                    <div class="alert alert-info">
                        <p class="mb-2"><strong>Ver consola del navegador para más detalles (F12)</strong></p>
                        <button type="button" class="btn btn-sm btn-info" onclick="console.table(window.lastStatsResponse)">
                            Mostrar datos completos en consola
                        </button>
                    </div>
                </section>
            </div>

            <!-- Pie del modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="window.cargarEstadisticasModal()">
                    <i class="fas fa-sync-alt me-1"></i>Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados para el modal de estadísticas */
    #modalEstadisticasPresupuestos .modal-dialog {
        max-width: 75vw !important;
        width: 75vw !important;
        margin: 1.75rem auto;
    }
    
    #modalEstadisticasPresupuestos .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    #modalEstadisticasPresupuestos .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    #modalEstadisticasPresupuestos .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
