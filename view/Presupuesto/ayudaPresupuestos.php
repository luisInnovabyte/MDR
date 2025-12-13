<!-- Modal de Ayuda para Presupuestos -->
<div class="modal fade" id="modalAyudaPresupuestos" tabindex="-1" aria-labelledby="modalAyudaPresupuestosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 75%; width: 75%;">
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
                        ¿Qué es el Módulo de Presupuestos?
                    </h6>
                    <p class="text-muted">
                        El módulo de presupuestos permite crear, gestionar y dar seguimiento a cotizaciones comerciales. 
                        Incluye información detallada del cliente, evento, productos/servicios, formas de pago y estados de seguimiento.
                        El sistema calcula automáticamente días de validez, duración de eventos y alertas de vencimiento.
                    </p>
                </div>

                <!-- Sección: Columnas de la Tabla -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-table me-2"></i>
                        Columnas de la Tabla Principal
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20%;">Columna</th>
                                    <th style="width: 80%;">Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong><i class="bi bi-plus-circle text-primary me-1"></i> Botón Expandir</strong></td>
                                    <td>
                                        Botón integrado en la columna <strong>Número</strong> que despliega información detallada del presupuesto en 3 columnas:
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Columna 1:</strong> Datos del Presupuesto, Datos del Evento, Datos del Cliente</li>
                                            <li><strong>Columna 2:</strong> Observaciones, Contacto del Cliente, Método de Contacto</li>
                                            <li><strong>Columna 3:</strong> Estado del Presupuesto, Forma de Pago, Control (fechas de creación/actualización)</li>
                                        </ul>
                                        <span class="badge bg-info mt-2">El botón cambia de <i class="bi bi-plus-circle"></i> a <i class="bi bi-dash-circle"></i> al expandir</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Número</strong></td>
                                    <td>Número único identificador del presupuesto. <strong>Incluye botón de expansión integrado.</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Cliente</strong></td>
                                    <td>Nombre del cliente al que va dirigido el presupuesto.</td>
                                </tr>
                                <tr>
                                    <td><strong>Evento</strong></td>
                                    <td>Nombre descriptivo del evento asociado al presupuesto (boda, cumpleaños, corporativo, etc.).</td>
                                </tr>
                                <tr>
                                    <td><strong>F. Inicio</strong></td>
                                    <td>Fecha de inicio del evento. Formato: <code>dd/mm/yyyy</code></td>
                                </tr>
                                <tr>
                                    <td><strong>F. Fin</strong></td>
                                    <td>Fecha de finalización del evento. Formato: <code>dd/mm/yyyy</code></td>
                                </tr>
                             
                                <tr class="table-info">
                                    <td><strong><i class="bi bi-hourglass-split me-1"></i> Duración</strong></td>
                                    <td>
                                        <strong>DURACIÓN DEL EVENTO EN DÍAS.</strong> Calcula cuántos días durará el evento completo.
                                        <div class="mt-2">
                                            <span class="badge bg-info">Badge azul</span> con el número de días
                                        </div>
                                        <div class="alert alert-info mt-2 mb-0 small">
                                            <i class="bi bi-calculator me-1"></i>
                                            Se calcula automáticamente restando la <strong>fecha fin del evento</strong> menos la <strong>fecha inicio del evento</strong>.
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong><i class="bi bi-alarm me-1"></i> Días Inicio</strong></td>
                                    <td>
                                        <strong>DÍAS HASTA EL INICIO DEL EVENTO.</strong> Indica cuántos días faltan para que comience el evento.
                                        <div class="mt-2">
                                            <span class="badge bg-secondary me-2">Negativo (Gris)</span> = Evento ya comenzó o finalizó
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-danger me-2">≤ 7 días (Rojo)</span> = Evento muy próximo (¡urgente!)
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-primary me-2">&gt; 7 días (Azul)</span> = Evento futuro con tiempo
                                        </div>
                                        <div class="alert alert-info mt-2 mb-0 small">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            Se calcula automáticamente desde la fecha actual hasta la <strong>fecha de inicio del evento</strong>.
                                        </div>
                                        <div class="alert alert-warning mt-2 mb-0 small">
                                            <i class="bi bi-dash-circle me-1"></i>
                                            <strong>Nota importante:</strong> Si el evento ya ha comenzado o ha pasado, se mostrará el símbolo <strong>"-"</strong> en lugar del número de días.
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong><i class="bi bi-flag me-1"></i> Estado Evento</strong></td>
                                    <td>
                                        <strong>ESTADO AUTOMÁTICO DEL EVENTO</strong> según su relación temporal con la fecha actual.
                                        <div class="mt-2">
                                            <span class="badge bg-dark me-2">Evento finalizado</span> = El evento ya terminó
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-success me-2">Evento en curso</span> = El evento está sucediendo ahora
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-danger me-2">Evento hoy</span> = El evento comienza hoy
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark me-2">Evento próximo</span> = Faltan pocos días (≤7)
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-info me-2">Evento futuro</span> = Faltan más de 7 días
                                        </div>
                                        <div class="alert alert-success mt-2 mb-0 small">
                                            <i class="bi bi-stars me-1"></i>
                                            <strong>Se calcula automáticamente</strong> comparando la fecha actual con las fechas de inicio y fin del evento. 
                                            Permite identificar rápidamente eventos urgentes o activos.
                                        </div>
                                    </td>
                                </tr>

                                <tr class="table-warning">
                                    <td><strong><i class="bi bi-calendar-check me-1"></i> Días Val.</strong></td>
                                    <td>
                                        <strong>DÍAS DE VALIDEZ RESTANTES del presupuesto.</strong> Calcula cuántos días quedan hasta que expire el presupuesto.
                                        <div class="mt-2">
                                            <span class="badge bg-danger me-2">Negativo (Rojo)</span> = Presupuesto vencido
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark me-2">≤ 7 días (Amarillo)</span> = Próximo a vencer
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-success me-2">&gt; 7 días (Verde)</span> = Vigente con margen
                                        </div>
                                        <div class="alert alert-info mt-2 mb-0 small">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Se calcula automáticamente desde la <strong>fecha de validez del presupuesto</strong> hasta la fecha actual.
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td>Estado administrativo del presupuesto (Pendiente, Aprobado, Rechazado, etc.). Se muestra con el color definido en la tabla de estados.</td>
                                </tr>
                                <tr>
                                    <td><strong>Activo</strong></td>
                                    <td>
                                        Indica si el presupuesto está activo o inactivo:
                                        <div class="mt-2">
                                            <i class="bi bi-check-circle text-success fa-2x"></i> = Activo
                                        </div>
                                        <div class="mt-1">
                                            <i class="bi bi-x-circle text-danger fa-2x"></i> = Inactivo
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Act./Desac.</strong></td>
                                    <td>
                                        Botones para cambiar el estado activo/inactivo del presupuesto:
                                        <div class="mt-2">
                                            <button class="btn btn-danger btn-sm" disabled><i class="fa-solid fa-trash"></i></button> Desactivar presupuesto activo
                                        </div>
                                        <div class="mt-1">
                                            <button class="btn btn-success btn-sm" disabled><i class="bi bi-hand-thumbs-up-fill"></i></button> Activar presupuesto inactivo
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Edit.</strong></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" disabled><i class="fa-solid fa-edit"></i></button> 
                                        Botón para editar el presupuesto. Redirige al formulario de edición.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Información Detallada (Vista Expandida) -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-eye me-2"></i>
                        Vista Detallada (Al Expandir con el Botón +)
                    </h6>
                    <p class="text-muted">
                        Al hacer clic en el botón <i class="bi bi-plus-circle text-primary"></i> de la columna <strong>Número</strong>, 
                        se despliega una vista con <strong>toda la información del presupuesto organizada en 3 columnas</strong>:
                    </p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-primary text-white">
                                    <strong><i class="bi bi-1-circle me-2"></i>Columna 1</strong>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-primary"><i class="bi bi-file-text me-1"></i>Datos del Presupuesto</h6>
                                    <ul class="small">
                                        <li>ID Presupuesto</li>
                                        <li>Número</li>
                                        <li>Fecha Presupuesto</li>
                                        <li>Fecha Validez</li>
                                   
                                    </ul>

                                    <h6 class="text-success"><i class="bi bi-geo-alt me-1"></i>Datos del Evento</h6>
                                    <ul class="small">
                                        <li>Ubicación Completa del Evento</li>
                                        <li>Fecha Inicio Evento</li>
                                        <li>Fecha Fin Evento</li>
                                    </ul>

                                    <h6 class="text-info"><i class="bi bi-person me-1"></i>Datos del Cliente</h6>
                                    <ul class="small">
                                        <li>Dirección Completa</li>
                                        <li>Dirección de Facturación</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <strong><i class="bi bi-2-circle me-2"></i>Columna 2</strong>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-warning"><i class="bi bi-chat-square-text me-1"></i>Observaciones</h6>
                                    <ul class="small">
                                        <li>Observaciones Cabecera</li>
                                        <li>Observaciones Pie</li>
                                        <li>Mostrar Obs. Familias (Sí/No)</li>
                                        <li>Mostrar Obs. Artículos (Sí/No)</li>
                                        <li>Observaciones Internas</li>
                                    </ul>

                                    <h6 class="text-secondary"><i class="bi bi-person-lines-fill me-1"></i>Contacto del Cliente</h6>
                                    <ul class="small">
                                        <li>Nombre Completo del Contacto</li>
                                    </ul>

                                    <h6 class="text-dark"><i class="bi bi-telephone me-1"></i>Método de Contacto</h6>
                                    <ul class="small">
                                        <li>ID Método</li>
                                        <li>Nombre del Método</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white">
                                    <strong><i class="bi bi-3-circle me-2"></i>Columna 3</strong>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-danger"><i class="bi bi-flag me-1"></i>Estado del Presupuesto</h6>
                                    <ul class="small">
                                        <li>Nombre del Estado (con color personalizado)</li>
                                    </ul>

                                    <h6 class="text-success"><i class="bi bi-credit-card me-1"></i>Forma de Pago</h6>
                                    <ul class="small">
                                        <li>Código de Pago</li>
                                        <li>Nombre de Pago</li>
                                        <li>% Anticipo</li>
                                        <li>Días Anticipo</li>
                                        <li>% Final</li>
                                        <li>Días Final</li>
                                        <li>Descuento</li>
                                        <li>Tipo de Pago Presupuesto</li>
                                    </ul>

                                    <h6 class="text-muted"><i class="bi bi-info-circle me-1"></i>Control</h6>
                                    <ul class="small">
                                        <li>Activo (Sí/No)</li>
                                        <li>Fecha de Creación</li>
                                        <li>Fecha de Actualización</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Filtros -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-funnel me-2"></i>
                        Sistema de Filtros
                    </h6>
                    <ul>
                        <li><strong>Filtros por columna:</strong> En el pie de la tabla hay campos de búsqueda para filtrar por cualquier columna visible.</li>
                        <li><strong>Filtro de estado activo:</strong> Selector desplegable para filtrar presupuestos activos o inactivos.</li>
                        <li><strong>Búsqueda global:</strong> Campo de búsqueda general que busca en todas las columnas simultáneamente.</li>
                        <li><strong>Alerta de filtros activos:</strong> Cuando se aplican filtros, aparece una alerta amarilla mostrando qué filtros están activos con opción de limpiarlos todos.</li>
                    </ul>
                </div>

                <!-- Sección: Acciones Disponibles -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightning me-2"></i>
                        Acciones Disponibles
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="bi bi-plus-circle me-2"></i>Nuevo Presupuesto
                                    </h6>
                                    <p class="card-text small">
                                        Botón verde en la parte superior derecha. Abre el formulario para crear un nuevo presupuesto con todos los campos necesarios.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="bi bi-pencil me-2"></i>Editar Presupuesto
                                    </h6>
                                    <p class="card-text small">
                                        Botón azul en cada fila. Permite modificar todos los datos del presupuesto seleccionado.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-danger">
                                        <i class="bi bi-trash me-2"></i>Desactivar Presupuesto
                                    </h6>
                                    <p class="card-text small">
                                        Botón rojo que desactiva (oculta) el presupuesto sin eliminarlo de la base de datos. Se puede reactivar posteriormente.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="bi bi-hand-thumbs-up me-2"></i>Activar Presupuesto
                                    </h6>
                                    <p class="card-text small">
                                        Botón verde que reactiva un presupuesto previamente desactivado, haciéndolo visible y operativo nuevamente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Leyenda de Colores -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-palette me-2"></i>
                        Leyenda de Colores e Indicadores
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Elemento</th>
                                    <th>Color/Icono</th>
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Días Validez</strong></td>
                                    <td><span class="badge bg-danger">Rojo</span></td>
                                    <td>Presupuesto vencido (días negativos)</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Validez</strong></td>
                                    <td><span class="badge bg-warning text-dark">Amarillo</span></td>
                                    <td>Próximo a vencer (≤ 7 días)</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Validez</strong></td>
                                    <td><span class="badge bg-success">Verde</span></td>
                                    <td>Vigente con margen (&gt; 7 días)</td>
                                </tr>
                                <tr>
                                    <td><strong>Duración Evento</strong></td>
                                    <td><span class="badge bg-info">Azul</span></td>
                                    <td>Número de días que durará el evento</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Hasta Inicio</strong></td>
                                    <td><span class="badge bg-secondary">Gris</span></td>
                                    <td>Evento ya comenzó (días negativos)</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Hasta Inicio</strong></td>
                                    <td><span class="badge bg-danger">Rojo</span></td>
                                    <td>Evento muy próximo (≤ 7 días) ¡Urgente!</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Hasta Inicio</strong></td>
                                    <td><span class="badge bg-primary">Azul</span></td>
                                    <td>Evento futuro con tiempo (&gt; 7 días)</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Evento</strong></td>
                                    <td><span class="badge bg-dark">Negro</span></td>
                                    <td>Evento finalizado</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Evento</strong></td>
                                    <td><span class="badge bg-success">Verde</span></td>
                                    <td>Evento en curso (está sucediendo ahora)</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Evento</strong></td>
                                    <td><span class="badge bg-danger">Rojo</span></td>
                                    <td>Evento hoy (comienza hoy)</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Evento</strong></td>
                                    <td><span class="badge bg-warning text-dark">Amarillo</span></td>
                                    <td>Evento próximo (≤ 7 días)</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado Evento</strong></td>
                                    <td><span class="badge bg-info">Celeste</span></td>
                                    <td>Evento futuro (&gt; 7 días)</td>
                                </tr>
                                <tr>
                                    <td><strong>Presupuesto Activo</strong></td>
                                    <td><i class="bi bi-check-circle text-success fa-2x"></i></td>
                                    <td>Presupuesto activo y operativo</td>
                                </tr>
                                <tr>
                                    <td><strong>Presupuesto Inactivo</strong></td>
                                    <td><i class="bi bi-x-circle text-danger fa-2x"></i></td>
                                    <td>Presupuesto desactivado u oculto</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Consejos y Buenas Prácticas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb me-2"></i>
                        Consejos y Buenas Prácticas
                    </h6>
                    <div class="alert alert-info">
                        <ul class="mb-0">
                            <li><strong>Monitorea los días de validez:</strong> Presupuestos en amarillo o rojo necesitan seguimiento urgente.</li>
                            <li><strong>Revisa el estado del evento:</strong> Los badges de color te alertan sobre eventos próximos o en curso.</li>
                            <li><strong>Usa los filtros:</strong> Filtra por estado de evento o días hasta inicio para priorizar acciones.</li>
                            <li><strong>Vista expandida:</strong> Usa el botón + para ver todos los detalles sin abrir el formulario completo.</li>
                            <li><strong>Scroll horizontal:</strong> La tabla permite desplazamiento horizontal para ver todas las columnas en pantallas pequeñas.</li>
                            <li><strong>Actualiza estados:</strong> Mantén actualizado el estado administrativo del presupuesto para mejor seguimiento.</li>
                        </ul>
                    </div>
                </div>

                <!-- Sección: Estadísticas de Presupuestos -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estadísticas de Presupuestos
                    </h6>
                    <p class="text-muted">
                        El módulo incluye un sistema completo de estadísticas que se accede mediante el botón 
                        <button class="btn btn-sm btn-primary" disabled><i class="fas fa-chart-bar me-1"></i>Estadísticas</button>
                        en la parte superior de la tabla.
                    </p>

                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <strong><i class="fas fa-chart-line me-2"></i>Estadísticas Generales</strong>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">El panel muestra indicadores clave de rendimiento:</p>
                            <ul class="small">
                                <li><strong>Total Activos:</strong> Número total de presupuestos activos en el sistema</li>
                                <li><strong>Aprobados:</strong> Cantidad de presupuestos con estado aprobado</li>
                                <li><strong>En Proceso/Pendientes:</strong> Suma de presupuestos en proceso, pendientes de revisión y esperando respuesta</li>
                                <li><strong>Tasa de Conversión:</strong> Porcentaje calculado como (Aprobados / Total Activos) × 100</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <strong><i class="fas fa-chart-pie me-2"></i>Distribución por Estados</strong>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Muestra el desglose detallado de presupuestos por cada estado:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="small">
                                        <li><i class="fas fa-circle text-info me-1"></i> <strong>En Proceso:</strong> Presupuestos en elaboración</li>
                                        <li><i class="fas fa-circle text-warning me-1"></i> <strong>Pendiente Revisión:</strong> Esperando validación</li>
                                        <li><i class="fas fa-circle text-primary me-1"></i> <strong>Esperando Respuesta:</strong> Enviados al cliente</li>
                                        <li><i class="fas fa-circle text-success me-1"></i> <strong>Aprobados:</strong> Confirmados por el cliente</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="small">
                                        <li><i class="fas fa-circle text-danger me-1"></i> <strong>Rechazados:</strong> No aceptados</li>
                                        <li><i class="fas fa-circle text-secondary me-1"></i> <strong>Cancelados:</strong> Anulados</li>
                                        <li><i class="fas fa-circle text-success me-1"></i> <strong>Vigentes:</strong> Con validez activa</li>
                                        <li><i class="fas fa-circle text-warning me-1"></i> <strong>Por caducar:</strong> Caducan en 7 días o menos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <strong><i class="fas fa-calendar-alt me-2"></i>Estadísticas del Mes Actual</strong>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Métricas específicas del mes en curso:</p>
                            <ul class="small">
                                <li><strong>Total del Mes:</strong> Presupuestos creados en el mes actual</li>
                                <li><strong>Aceptados:</strong> Presupuestos aprobados este mes</li>
                                <li><strong>Pendientes:</strong> En proceso o esperando respuesta</li>
                                <li><strong>Rechazados:</strong> No aceptados durante el mes</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning text-dark">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Alertas y Eventos</strong>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Sistema de alertas para seguimiento urgente:</p>
                            <ul class="small">
                                <li><i class="fas fa-hourglass-half text-warning me-1"></i> <strong>Caduca hoy:</strong> Presupuestos que vencen en el día actual</li>
                                <li><i class="fas fa-times-circle text-danger me-1"></i> <strong>Caducados:</strong> Presupuestos cuya fecha de validez ya pasó</li>
                                <li><i class="fas fa-calendar-check text-info me-1"></i> <strong>Eventos próximos:</strong> Eventos que comienzan en los próximos 7 días</li>
                                <li><i class="fas fa-calendar-day text-success me-1"></i> <strong>Eventos en curso:</strong> Eventos que están sucediendo ahora</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Actualización:</strong> Las estadísticas se actualizan en tiempo real. 
                        Usa el botón <strong>"Actualizar"</strong> en el pie del modal para refrescar los datos manualmente.
                    </div>
                </div>

                <!-- Sección: Notas Técnicas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-gear me-2"></i>
                        Notas Técnicas
                    </h6>
                    <div class="alert alert-secondary">
                        <ul class="mb-0">
                            <li>Los cálculos de días son automáticos y se actualizan en tiempo real desde la vista de base de datos.</li>
                            <li>La columna ID está oculta pero se puede mostrar configurando la tabla.</li>
                            <li>El botón de expansión está integrado en la columna Número (no ocupa columna adicional).</li>
                            <li>Las fechas se muestran en formato europeo (dd/mm/yyyy).</li>
                            <li>El scroll horizontal se activa automáticamente cuando la tabla es más ancha que el contenedor.</li>
                            <li>Todos los cambios (activar/desactivar) se confirman con ventanas de alerta (SweetAlert2).</li>
                            <li>Las estadísticas se calculan mediante consultas SQL optimizadas que consideran solo presupuestos activos.</li>
                            <li>Los colores en las estadísticas coinciden con los de la tabla principal para facilitar la interpretación.</li>
                        </ul>
                    </div>
                </div>

            </div><!-- modal-body -->

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
