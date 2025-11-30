<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Gestión de Llamadas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<!-- Botón para abrir el modal (demo) -->

<!-- Modal de Ayuda para Gestión de Llamadas -->
<div class="modal fade" id="modalAyudaLlamadas" tabindex="-1" role="dialog" aria-labelledby="modalAyudaLlamadasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaLlamadasLabel">
                    <i class="bi bi-question-circle-fill mr-2" style="font-size: 1.4rem;"></i>
                    Ayuda - Gestión de Llamadas
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
                        <i class="bi bi-telephone-fill mr-2"></i> ¿Qué es la Gestión de Llamadas?
                    </h6>
                    <p class="text-muted">
                        El sistema de gestión de llamadas permite registrar, organizar y hacer seguimiento de todas las 
                        comunicaciones con clientes y comerciales. Facilita el control de citas, estados de contacto y 
                        la coordinación del equipo comercial mediante un registro centralizado y ordenado.
                    </p>
                </div>

                <!-- Sección: Campos -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data mr-2"></i> Campos del Sistema
                    </h6>

                    <div id="accordionCampos" role="tablist">

                        <!-- Método -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingMetodo">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseMetodo" aria-expanded="false" aria-controls="collapseMetodo">
                                        <i class="bi bi-chat-dots mr-2"></i> Método de Contacto
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseMetodo" class="collapse" role="tabpanel" aria-labelledby="headingMetodo" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Indica el medio utilizado para la comunicación.
                                    <br><strong>Opciones comunes:</strong> Teléfono, Email, WhatsApp, Presencial
                                    <br><strong>Uso:</strong> Permite filtrar y analizar qué canales son más efectivos
                                    <br><strong>Icono:</strong> Se muestra con un indicador visual en la tabla
                                </div>
                            </div>
                        </div>

                        <!-- Comunicante -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingComunicante">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseComunicante" aria-expanded="false" aria-controls="collapseComunicante">
                                        <i class="bi bi-person-fill mr-2"></i> Nombre del Comunicante
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseComunicante" class="collapse" role="tabpanel" aria-labelledby="headingComunicante" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Nombre de la persona que realiza o recibe la llamada.
                                    <br><strong>Ejemplos:</strong> Luis Rodríguez, María García, Juan Pérez
                                    <br><strong>Uso:</strong> Identificar quién es el contacto principal
                                    <br><strong>Validaciones:</strong> Texto alfanumérico, mínimo 3 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Comercial -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingComercial">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseComercial" aria-expanded="false" aria-controls="collapseComercial">
                                        <i class="bi bi-briefcase-fill mr-2"></i> ID Comercial Asignado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseComercial" class="collapse" role="tabpanel" aria-labelledby="headingComercial" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Identificador del comercial responsable.
                                    <br><strong>Ejemplos:</strong> Alejandro, Carlos, Ana
                                    <br><strong>Uso:</strong> Asignar seguimiento y responsabilidad
                                    <br><strong>Nota:</strong> Permite filtrar llamadas por comercial
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingEstado">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                        <i class="bi bi-tag-fill mr-2"></i> Estado de la Llamada
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseEstado" class="collapse" role="tabpanel" aria-labelledby="headingEstado" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Situación actual de la comunicación.
                                    <br><strong>Estados típicos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>Cita Cerrada:</strong> Reunión confirmada</li>
                                        <li><strong>Pendiente de Respuesta:</strong> Esperando feedback</li>
                                        <li><strong>No Contactado:</strong> No se logró comunicación</li>
                                        <li><strong>Interesado:</strong> Cliente muestra interés</li>
                                        <li><strong>No Interesado:</strong> Cliente descarta la oferta</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingFecha">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseFecha" aria-expanded="false" aria-controls="collapseFecha">
                                        <i class="bi bi-calendar-event mr-2"></i> Fecha de Recepción
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFecha" class="collapse" role="tabpanel" aria-labelledby="headingFecha" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Fecha y hora en que se registró la llamada.
                                    <br><strong>Formato:</strong> DD-MM-YYYY HH:MM:SS
                                    <br><strong>Ejemplo:</strong> 09-06-2025 00:00:00
                                    <br><strong>Uso:</strong> Ordenar cronológicamente y analizar tiempos de respuesta
                                    <br><strong>Ordenación:</strong> Por defecto se ordena descendente (más recientes primero)
                                </div>
                            </div>
                        </div>

                        <!-- Semáforo -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingSemaforo">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseSemaforo" aria-expanded="false" aria-controls="collapseSemaforo">
                                        <i class="bi bi-circle-fill mr-2"></i> Semáforo de Prioridad
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseSemaforo" class="collapse" role="tabpanel" aria-labelledby="headingSemaforo" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Indicador visual.</strong> Código de colores para prioridad o urgencia.
                                    <br><strong>Colores:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><span class="badge badge-danger">●</span> <strong>Rojo:</strong> Urgente/Alta prioridad</li>
                                        <li><span class="badge badge-warning">●</span> <strong>Amarillo:</strong> Media prioridad</li>
                                        <li><span class="badge badge-success">●</span> <strong>Verde:</strong> Baja prioridad/Completada</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Activo -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingActivo">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseActivo" aria-expanded="false" aria-controls="collapseActivo">
                                        <i class="bi bi-check-circle mr-2"></i> Estado Activo/Inactivo
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseActivo" class="collapse" role="tabpanel" aria-labelledby="headingActivo" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Control de visibilidad.</strong> Indica si la llamada está activa en el sistema.
                                    <br><strong>Activo (✓):</strong> Llamada visible y en seguimiento
                                    <br><strong>Inactivo (✗):</strong> Llamada archivada o descartada
                                    <br><strong>Uso:</strong> Permite ocultar llamadas antiguas sin eliminarlas
                                </div>
                            </div>
                        </div>

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
                                Use el campo de búsqueda superior para encontrar llamadas por cualquier dato.
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
                                Use los botones superiores para filtrar por estado: Todas, Activas, Inactivas.
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
                                        Copia todos los datos visibles en formato texto para pegar en otras aplicaciones.
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
                                        Descarga un archivo Excel (.xlsx) con todos los registros filtrados.
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
                                        Genera un documento PDF profesional con los datos visibles.
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
                                        Imprime directamente la tabla con un formato optimizado.
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
                        Iconos y Estados en la Tabla
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Icono/Badge</th>
                                    <th>Descripción</th>
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Llamada Activa</td>
                                    <td>La llamada está activa en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Llamada Inactiva</td>
                                    <td>La llamada está archivada</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-whatsapp text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: WhatsApp</td>
                                    <td>Contacto vía aplicación de mensajería</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-telephone-fill text-primary" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Teléfono</td>
                                    <td>Llamada telefónica directa</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-envelope-fill text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Email</td>
                                    <td>Correo electrónico</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-person-fill text-info" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Presencial</td>
                                    <td>Reunión cara a cara</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la llamada</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la llamada (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la llamada (solo si está inactiva)</td>
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
                            <li>Registre las llamadas inmediatamente después del contacto</li>
                            <li>Use estados específicos para facilitar el seguimiento</li>
                            <li>Actualice el semáforo según la prioridad real</li>
                            <li>Archive llamadas antiguas cambiándolas a inactivas</li>
                            <li>Revise diariamente las llamadas con estado "Cita Cerrada"</li>
                            <li>Use los filtros para análisis por comercial o período</li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: SGL v1.0 - Última actualización: 24-11-2025
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