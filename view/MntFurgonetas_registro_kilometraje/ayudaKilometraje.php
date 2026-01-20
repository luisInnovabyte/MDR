<!-- Modal de Ayuda para Registro de Kilometraje -->
<div class="modal fade" id="modalAyudaKilometraje" tabindex="-1" aria-labelledby="modalAyudaKilometrajeLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%; width: 1400px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaKilometrajeLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Registro de Kilometraje de Furgonetas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-info-circle me-2"></i>¿Qué es el Registro de Kilometraje?
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    El <strong>registro de kilometraje</strong> permite llevar un control histórico de los kilómetros 
                                    recorridos por la furgoneta, calculando automáticamente estadísticas como kilómetros recorridos 
                                    entre registros, días transcurridos y promedio diario de kilómetros.
                                </p>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Ventaja:</strong> Mantén un control preciso del uso de cada vehículo para planificar 
                                    mantenimientos preventivos y optimizar costos operativos.
                                </div>

                                <h6 class="text-success mt-4">
                                    <i class="fas fa-tag me-2"></i>Tipos de Registro
                                </h6>
                                <ul>
                                    <li><strong>Manual:</strong> Registro manual rutinario del kilometraje</li>
                                    <li><strong>Revisión:</strong> Registro asociado a un mantenimiento preventivo</li>
                                    <li><strong>ITV:</strong> Registro tomado durante la Inspección Técnica</li>
                                    <li><strong>Evento:</strong> Registro por un evento especial (viaje largo, entrega importante, etc.)</li>
                                </ul>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Registra el kilometraje periódicamente para obtener estadísticas 
                                    precisas sobre el uso del vehículo.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-table me-2"></i>Funciones de la Tabla
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-secondary">
                                            <i class="fas fa-plus-circle text-success me-2"></i>Botón "Nuevo Registro"
                                        </h6>
                                        <p class="small">Crea un nuevo registro de kilometraje para la furgoneta.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-edit text-info me-2"></i>Botón "Editar"
                                        </h6>
                                        <p class="small">Modifica los datos de un registro existente (fecha, kilometraje, observaciones).</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-chevron-right text-primary me-2"></i>Botón Expandir (▶)
                                        </h6>
                                        <p class="small">Muestra información detallada del registro en una fila desplegable.</p>
                                        
                                        <h6 class="text-secondary">
                                            <i class="fas fa-search text-primary me-2"></i>Filtros de Búsqueda
                                        </h6>
                                        <p class="small">Utiliza los campos del pie de tabla para filtrar por fecha, kilometraje, tipo u observaciones.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-calculator me-2"></i>Campos Calculados Automáticamente
                                </h6>
                            </div>
                            <div class="card-body">
                                <h6 class="text-info">
                                    <i class="fas fa-arrow-up-right me-2"></i>KM Recorridos
                                </h6>
                                <p class="small mb-3">
                                    Se calcula automáticamente restando el kilometraje del registro anterior. 
                                    Aparece como <span class="badge bg-secondary">-</span> en el primer registro.
                                </p>

                                <h6 class="text-info">
                                    <i class="fas fa-clock me-2"></i>Días Transcurridos
                                </h6>
                                <p class="small mb-3">
                                    Calcula los días entre el registro actual y el anterior. 
                                    Aparece como <span class="text-muted fst-italic">-</span> si es el primer registro o mismo día.
                                </p>

                                <h6 class="text-info">
                                    <i class="fas fa-graph-up me-2"></i>KM/Día (Promedio)
                                </h6>
                                <p class="small mb-3">
                                    Divide los kilómetros recorridos entre los días transcurridos. 
                                    Aparece como <span class="text-muted fst-italic">-</span> si no hay suficiente información.
                                </p>

                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Nota:</strong> Estos valores se actualizan automáticamente con cada nuevo registro.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-filter me-2"></i>Uso de Filtros
                                </h6>
                            </div>
                            <div class="card-body">
                                <h6 class="text-secondary">Filtro por Tipo (Radio Buttons)</h6>
                                <p class="small">Puedes activar los filtros de tipo desplegando el acordeón "Filtros de Kilometraje":</p>
                                <ul class="small">
                                    <li><strong>Todos:</strong> Muestra todos los registros</li>
                                    <li><strong>Manual:</strong> Solo registros manuales</li>
                                    <li><strong>Revisión:</strong> Solo revisiones</li>
                                    <li><strong>ITV:</strong> Solo inspecciones técnicas</li>
                                    <li><strong>Evento:</strong> Solo eventos especiales</li>
                                </ul>

                                <h6 class="text-secondary mt-3">Búsqueda en Columnas</h6>
                                <p class="small">Utiliza los campos al pie de cada columna para búsquedas específicas:</p>
                                <ul class="small">
                                    <li><strong>Fecha:</strong> Busca por fecha específica</li>
                                    <li><strong>Kilometraje:</strong> Filtra por rango de kilómetros</li>
                                    <li><strong>Tipo:</strong> Selector desplegable para filtrar</li>
                                    <li><strong>Observaciones:</strong> Busca texto en las notas</li>
                                </ul>

                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-broom me-2"></i>
                                    <strong>Limpiar Filtros:</strong> Usa el botón "Limpiar filtros" para resetear todas las búsquedas.
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 mt-3">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-lightbulb me-2"></i>Consejos y Buenas Prácticas
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Registra el kilometraje <strong>al menos semanalmente</strong> para obtener promedios precisos</li>
                                    <li>Asocia registros con <strong>tipo "Revisión"</strong> cuando lleves el vehículo al taller</li>
                                    <li>Usa <strong>observaciones</strong> para anotar detalles importantes del uso del vehículo</li>
                                    <li>Los registros de tipo <strong>"Evento"</strong> son útiles para viajes largos o entregas especiales</li>
                                    <li>Revisa los <strong>promedios de KM/día</strong> para detectar cambios en el uso del vehículo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de ejemplo visual -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-image me-2"></i>Ejemplo de Registro de Kilometraje
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Kilometraje</th>
                                                <th>KM Recorridos</th>
                                                <th>Días</th>
                                                <th>KM/Día</th>
                                                <th>Tipo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>01/01/2025</td>
                                                <td>50,000 km</td>
                                                <td><span class="text-muted">-</span></td>
                                                <td><span class="text-muted">-</span></td>
                                                <td><span class="text-muted">-</span></td>
                                                <td><span class="badge bg-primary">Manual</span></td>
                                            </tr>
                                            <tr>
                                                <td>08/01/2025</td>
                                                <td>50,350 km</td>
                                                <td><span class="badge bg-success">350 km</span></td>
                                                <td>7 días</td>
                                                <td>50.0 km/día</td>
                                                <td><span class="badge bg-primary">Manual</span></td>
                                            </tr>
                                            <tr>
                                                <td>15/01/2025</td>
                                                <td>50,780 km</td>
                                                <td><span class="badge bg-success">430 km</span></td>
                                                <td>7 días</td>
                                                <td>61.4 km/día</td>
                                                <td><span class="badge bg-warning">Revisión</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Este ejemplo muestra cómo se calculan automáticamente los valores en cada nuevo registro.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
