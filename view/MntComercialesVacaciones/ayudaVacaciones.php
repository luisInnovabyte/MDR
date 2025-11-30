<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Mantenimiento de Vacaciones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<!-- Botón para abrir el modal (demo) -->

<!-- Modal de Ayuda para Mantenimiento de Vacaciones -->
<div class="modal fade" id="modalAyudaVacaciones" tabindex="-1" role="dialog" aria-labelledby="modalAyudaVacacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaVacacionesLabel">
                    <i class="bi bi-question-circle-fill mr-2" style="font-size: 1.4rem;"></i>
                    Ayuda - Mantenimiento de Vacaciones de Comerciales
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar" style="opacity:1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Sección: Introducción -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-chat-dots-fill mr-2"></i> ¿Qué es el Mantenimiento de Vacaciones?
                    </h6>
                    <p class="text-muted">
                        La gestión de vacaciones de comerciales permite registrar, actualizar y controlar los periodos en los 
                        que cada comercial estará ausente. Esto evita asignaciones erróneas, asegura una correcta planificación del equipo 
                        y garantiza que las llamadas, clientes y tareas no se asignen a un comercial que no está disponible.
                    </p>
                </div>

                <!-- Sección: Campos -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data mr-2"></i> Campos del Sistema
                    </h6>

                    <div id="accordionCampos" role="tablist">

                        <!-- Indicador Expandir -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingExpandir">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseExpandir" aria-expanded="false" aria-controls="collapseExpandir">
                                        <i class="bi bi-plus-circle mr-2"></i> Indicador de Detalles (⊕)
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseExpandir" class="collapse" role="tabpanel" aria-labelledby="headingExpandir" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Icono interactivo.</strong> Permite expandir la fila para ver información adicional del periodo de vacaciones.
                                    <br><strong>Acción:</strong> Haga clic para mostrar/ocultar detalles complementarios
                                    <br><strong>Contenido expandible:</strong> Observaciones, motivos especiales, días totales, histórico
                                    <br><strong>Uso:</strong> Visualizar información extendida sin saturar la vista principal de la tabla
                                </div>
                            </div>
                        </div>

                        <!-- Comercial -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingComercial">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseComercial" aria-expanded="false" aria-controls="collapseComercial">
                                        <i class="bi bi-person-fill mr-2"></i> Comercial
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseComercial" class="collapse" role="tabpanel" aria-labelledby="headingComercial" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Nombre del comercial que tomará vacaciones.
                                    <br><strong>Tipo:</strong> Lista desplegable de comerciales activos en el sistema
                                    <br><strong>Uso:</strong> Identificación clara del empleado ausente
                                    <br><strong>Validaciones:</strong> Debe existir en el catálogo de comerciales
                                    <br><strong>Impacto:</strong> Durante las fechas indicadas, el comercial no recibirá asignaciones
                                    <br><strong>Recomendación:</strong> Verifique que el nombre sea correcto antes de guardar
                                </div>
                            </div>
                        </div>

                        <!-- Fecha Inicio -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingFechaInicio">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseFechaInicio" aria-expanded="false" aria-controls="collapseFechaInicio">
                                        <i class="bi bi-calendar-check mr-2"></i> Fecha Inicio
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFechaInicio" class="collapse" role="tabpanel" aria-labelledby="headingFechaInicio" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Fecha en que comienzan las vacaciones.
                                    <br><strong>Formato:</strong> DD/MM/AAAA (día/mes/año)
                                    <br><strong>Selector:</strong> Calendario interactivo para facilitar selección
                                    <br><strong>Uso:</strong> Marca el primer día de ausencia del comercial
                                    <br><strong>Validaciones:</strong> Debe ser igual o posterior a la fecha actual
                                    <br><strong>Restricción:</strong> No puede ser posterior a la fecha fin
                                    <br><strong>Importante:</strong> El comercial no estará disponible desde este día (inclusive)
                                </div>
                            </div>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingFechaFin">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseFechaFin" aria-expanded="false" aria-controls="collapseFechaFin">
                                        <i class="bi bi-calendar-x mr-2"></i> Fecha Fin
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFechaFin" class="collapse" role="tabpanel" aria-labelledby="headingFechaFin" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Fecha en que finalizan las vacaciones.
                                    <br><strong>Formato:</strong> DD/MM/AAAA (día/mes/año)
                                    <br><strong>Selector:</strong> Calendario interactivo para facilitar selección
                                    <br><strong>Uso:</strong> Marca el último día de ausencia del comercial
                                    <br><strong>Validaciones:</strong> Debe ser igual o posterior a la fecha inicio
                                    <br><strong>Cálculo automático:</strong> El sistema calcula días totales entre ambas fechas
                                    <br><strong>Importante:</strong> El comercial vuelve a estar disponible al día siguiente
                                </div>
                            </div>
                        </div>

                        <!-- Tiene Vacaciones -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingTieneVacaciones">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseTieneVacaciones" aria-expanded="false" aria-controls="collapseTieneVacaciones">
                                        <i class="bi bi-check-circle mr-2"></i> Tiene Vacaciones?
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseTieneVacaciones" class="collapse" role="tabpanel" aria-labelledby="headingTieneVacaciones" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo informativo.</strong> Indica si el comercial está actualmente de vacaciones.
                                    <br><strong>Estados posibles:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li><i class="bi bi-check-circle text-success"></i> <strong>Sí (✓):</strong> El comercial está actualmente de vacaciones</li>
                                        <li><i class="bi bi-x-circle text-danger"></i> <strong>No (✗):</strong> El comercial está disponible o las vacaciones son futuras/pasadas</li>
                                    </ul>
                                    <strong>Cálculo automático:</strong> El sistema verifica si la fecha actual está dentro del periodo
                                    <br><strong>Uso:</strong> Identificación rápida de quién está ausente hoy
                                    <br><strong>Impacto:</strong> Los comerciales con "Sí" no aparecen en asignaciones automáticas
                                    <br><strong>Actualización:</strong> Se actualiza automáticamente cada día
                                </div>
                            </div>
                        </div>

                        <!-- Activar/Desactivar -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingActDes">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseActDes" aria-expanded="false" aria-controls="collapseActDes">
                                        <i class="bi bi-toggle-on mr-2"></i> Activar/Desactivar
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseActDes" class="collapse" role="tabpanel" aria-labelledby="headingActDes" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Control de disponibilidad.</strong> Cambia el estado del registro de vacaciones.
                                    <br><strong>Botón rojo (🗑️):</strong> Desactivar/Cancelar periodo de vacaciones
                                    <br><strong>Vacaciones activas:</strong> El sistema considera el periodo al hacer asignaciones
                                    <br><strong>Vacaciones inactivas:</strong> Periodo cancelado, el comercial vuelve a estar disponible
                                    <br><strong>Uso común:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Cancelar vacaciones cuando el comercial regresa anticipadamente</li>
                                        <li>Desactivar periodos erróneos sin eliminar el registro histórico</li>
                                        <li>Anular vacaciones por cambios en la planificación</li>
                                    </ul>
                                    <br><strong>Seguridad:</strong> Los registros desactivados se mantienen en el histórico
                                </div>
                            </div>
                        </div>

                        <!-- Editar -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingEdit">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseEdit" aria-expanded="false" aria-controls="collapseEdit">
                                        <i class="bi bi-pencil-square mr-2"></i> Editar
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseEdit" class="collapse" role="tabpanel" aria-labelledby="headingEdit" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Acción de modificación.</strong> Permite editar los datos del periodo de vacaciones.
                                    <br><strong>Botón azul (✏️):</strong> Abre el formulario de edición con datos precargados
                                    <br><strong>Campos editables:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Comercial asignado</li>
                                        <li>Fecha de inicio</li>
                                        <li>Fecha de fin</li>
                                        <li>Observaciones o notas</li>
                                        <li>Estado (activo/inactivo)</li>
                                    </ul>
                                    <br><strong>Uso común:</strong> Corregir fechas, cambiar comercial, actualizar duración
                                    <br><strong>Importante:</strong> Modificar fechas recalcula automáticamente los días totales
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Sección: Escenarios de Uso -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-list-check mr-2"></i>
                        Escenarios Comunes de Vacaciones
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Escenario</th>
                                    <th>Icono</th>
                                    <th>Descripción</th>
                                    <th>Acción Recomendada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Vacaciones programadas</td>
                                    <td class="text-center">📅</td>
                                    <td>Periodo de descanso planificado con anticipación</td>
                                    <td>Registrar con fecha inicio y fin definidas</td>
                                </tr>
                                <tr>
                                    <td>Ausencia médica</td>
                                    <td class="text-center">🏥</td>
                                    <td>Baja por enfermedad o tratamiento</td>
                                    <td>Registrar y agregar observación del motivo</td>
                                </tr>
                                <tr>
                                    <td>Permiso especial</td>
                                    <td class="text-center">📋</td>
                                    <td>Ausencia por asuntos personales o familiares</td>
                                    <td>Documentar en observaciones y definir fechas exactas</td>
                                </tr>
                                <tr>
                                    <td>Vacaciones extendidas</td>
                                    <td class="text-center">✈️</td>
                                    <td>Periodo largo (2+ semanas)</td>
                                    <td>Planificar redistribución de cartera con anticipación</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Filtros y Búsqueda -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-funnel-fill mr-2"></i>
                        Cómo usar Filtros y Búsqueda
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Búsqueda General:</h6>
                            <p class="text-muted small">
                                Use el campo de búsqueda superior para encontrar periodos por nombre de comercial.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Ordenar Columnas:</h6>
                            <p class="text-muted small">
                                Haga clic en las cabeceras para ordenar por comercial, fecha o estado.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro por Estado:</h6>
                            <p class="text-muted small">
                                Filtre para ver solo vacaciones activas, pasadas o futuras según necesidad.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Limpiar Filtros:</h6>
                            <p class="text-muted small">
                                Use el botón "Limpiar Filtros" para restablecer la vista a todos los periodos.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sección: Herramientas de Exportación -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-download mr-2"></i>
                        Herramientas de Exportación
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-file-earmark-text mr-2"></i>
                                        Copiar al Portapapeles
                                    </h6>
                                    <p class="card-text small text-muted mb-0">
                                        Copia el calendario de vacaciones en formato texto para compartir con el equipo.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-file-earmark-excel mr-2"></i>
                                        Exportar a Excel
                                    </h6>
                                    <p class="card-text small text-muted mb-0">
                                        Descarga un archivo Excel (.xlsx) con el calendario completo de vacaciones.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-file-earmark-pdf mr-2"></i>
                                        Exportar a PDF
                                    </h6>
                                    <p class="card-text small text-muted mb-0">
                                        Genera un documento PDF con el registro de periodos de ausencia.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-printer mr-2"></i>
                                        Imprimir
                                    </h6>
                                    <p class="card-text small text-muted mb-0">
                                        Imprime directamente la tabla de vacaciones con formato optimizado.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Iconos y Estados -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-palette-fill mr-2"></i>
                        Iconos y Acciones en la Tabla
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Icono/Botón</th>
                                    <th>Descripción</th>
                                    <th>Acción/Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-plus-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Expandir Detalles</td>
                                    <td>Muestra observaciones y detalles completos del periodo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Está de Vacaciones</td>
                                    <td>El comercial está actualmente ausente en este periodo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>No está de Vacaciones</td>
                                    <td>El comercial está disponible (vacaciones futuras o pasadas)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">📅</td>
                                    <td>Icono Vacaciones Programadas</td>
                                    <td>Representación visual de periodo planificado</td>
                                </tr>
                                <tr>
                                    <td class="text-center">🏥</td>
                                    <td>Icono Ausencia Médica</td>
                                    <td>Representación visual de baja por salud</td>
                                </tr>
                                <tr>
                                    <td class="text-center">📋</td>
                                    <td>Icono Permiso Especial</td>
                                    <td>Representación visual de ausencia autorizada</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Desactivar Periodo</td>
                                    <td>Cancela las vacaciones sin eliminar el registro histórico</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Editar Periodo</td>
                                    <td>Permite modificar comercial, fechas y observaciones</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Sección: Consejos de Uso -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-star-fill mr-2"></i>
                        Consejos de Uso
                    </h6>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-lightbulb mr-2"></i>
                            Mejores Prácticas
                        </h6>
                        <ul class="mb-0">
                            <li><strong>Planifique con anticipación:</strong> Registre las vacaciones con al menos 2 semanas de antelación</li>
                            <li><strong>Verifique disponibilidad:</strong> Asegúrese de que no haya solapamientos críticos en el equipo</li>
                            <li><strong>Documente motivos:</strong> Use el campo de observaciones para justificar ausencias especiales</li>
                            <li><strong>No elimine registros:</strong> Desactive en lugar de eliminar para conservar el historial</li>
                            <li><strong>Actualice cambios:</strong> Si un comercial regresa antes, edite la fecha fin inmediatamente</li>
                            <li><strong>Revise periódicamente:</strong> Mantenga el calendario actualizado semanalmente</li>
                            <li><strong>Comunique al equipo:</strong> Notifique las ausencias largas a todos los afectados</li>
                            <li><strong>Redistribuya carga:</strong> Planifique la reasignación temporal de clientes antes de la ausencia</li>
                            <li><strong>Calendario visual:</strong> Exporte a PDF para tener una vista mensual del equipo</li>
                        </ul>
                    </div>
                </div>

            </div>

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
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>