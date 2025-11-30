<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Mantenimiento de Contactos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<!-- Botón para abrir el modal (demo) -->
<div class="text-center mb-3">
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalAyudaContactos">
        <i class="bi bi-question-circle-fill mr-2"></i>
        Ver Modal de Ayuda - Contactos
    </button>
</div>

<!-- Modal de Ayuda para Mantenimie nto de Contactos -->
<div class="modal fade" id="modalAyudaContactos" tabindex="-1" role="dialog" aria-labelledby="modalAyudaContactosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaContactosLabel">
                    <i class="bi bi-question-circle-fill mr-2" style="font-size: 1.4rem;"></i>
                    Ayuda - Mantenimiento de Contactos
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
                        <i class="bi bi-person-lines-fill mr-2"></i> ¿Qué es el Mantenimiento de Contactos?
                    </h6>
                    <p class="text-muted">
                        El módulo de mantenimiento de contactos permite gestionar la base de datos de clientes y prospectos. 
                        Registre información detallada, historial de comunicaciones, métodos de contacto preferidos y estados 
                        de cada relación comercial para un seguimiento efectivo y personalizado.
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
                                    <strong>Icono interactivo.</strong> Permite expandir la fila para ver información adicional del contacto.
                                    <br><strong>Acción:</strong> Haga clic para mostrar/ocultar detalles complementarios
                                    <br><strong>Contenido expandible:</strong> Dirección completa, observaciones, historial de interacciones, notas internas
                                    <br><strong>Uso:</strong> Visualizar información extendida sin saturar la vista principal de la tabla
                                </div>
                            </div>
                        </div>

                        <!-- Nombre Comunicante -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingComunicante">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseComunicante" aria-expanded="false" aria-controls="collapseComunicante">
                                        <i class="bi bi-person-fill mr-2"></i> Nombre Comunicante
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseComunicante" class="collapse" role="tabpanel" aria-labelledby="headingComunicante" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Nombre completo de la persona de contacto.
                                    <br><strong>Ejemplo:</strong> Tomás Jiménez
                                    <br><strong>Uso:</strong> Identificación principal del contacto en todo el sistema
                                    <br><strong>Validaciones:</strong> Texto alfanumérico, mínimo 3 caracteres, máximo 100
                                    <br><strong>Ordenación:</strong> Permite ordenar alfabéticamente para búsquedas rápidas
                                    <br><strong>Búsqueda:</strong> Campo indexado para búsquedas instantáneas
                                </div>
                            </div>
                        </div>

                        <!-- Fecha Hora Contacto -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingFecha">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseFecha" aria-expanded="false" aria-controls="collapseFecha">
                                        <i class="bi bi-calendar-event mr-2"></i> Fecha Hora Contacto
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFecha" class="collapse" role="tabpanel" aria-labelledby="headingFecha" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Fecha y hora del último contacto o registro inicial.
                                    <br><strong>Formato:</strong> DD-MM-YYYY HH:MM:SS
                                    <br><strong>Ejemplo:</strong> 13-07-2025 14:32:00
                                    <br><strong>Uso principal:</strong> Seguimiento temporal de interacciones, identificar contactos inactivos
                                    <br><strong>Actualización automática:</strong> Se actualiza cada vez que hay una nueva comunicación
                                    <br><strong>Ordenación:</strong> Por defecto descendente (contactos más recientes primero)
                                    <br><strong>Análisis:</strong> Permite identificar clientes que requieren seguimiento por inactividad
                                </div>
                            </div>
                        </div>

                        <!-- Método -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingMetodo">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseMetodo" aria-expanded="false" aria-controls="collapseMetodo">
                                        <i class="bi bi-chat-dots mr-2"></i> Método
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseMetodo" class="collapse" role="tabpanel" aria-labelledby="headingMetodo" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Canal o medio de contacto preferido por el cliente.
                                    <br><strong>Opciones disponibles:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li><i class="bi bi-whatsapp text-success"></i> <strong>WhatsApp:</strong> Mensajería instantánea (más usado actualmente)</li>
                                        <li><i class="bi bi-telephone-fill text-primary"></i> <strong>Teléfono:</strong> Llamada telefónica directa</li>
                                        <li><i class="bi bi-envelope-fill text-danger"></i> <strong>Email:</strong> Correo electrónico formal</li>
                                        <li><i class="bi bi-person-fill text-info"></i> <strong>Presencial:</strong> Reunión cara a cara</li>
                                    </ul>
                                    <strong>Uso:</strong> Determina cómo contactar al cliente de forma efectiva
                                    <br><strong>Personalización:</strong> Respetar las preferencias mejora la tasa de respuesta
                                    <br><strong>Análisis:</strong> Identificar tendencias de comunicación por segmento de clientes
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingEstado">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                        <i class="bi bi-tag-fill mr-2"></i> Estado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseEstado" class="collapse" role="tabpanel" aria-labelledby="headingEstado" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Situación actual de la relación comercial con el contacto.
                                    <br><strong>Estados del contacto:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li><i class="bi bi-check-circle text-success"></i> <strong>Activo:</strong> Cliente con relación comercial vigente</li>
                                        <li><i class="bi bi-hourglass-split text-warning"></i> <strong>Prospecto:</strong> Cliente potencial en evaluación</li>
                                        <li><i class="bi bi-star-fill text-primary"></i> <strong>Lead Calificado:</strong> Alto potencial de conversión</li>
                                        <li><i class="bi bi-pause-circle text-secondary"></i> <strong>Inactivo:</strong> Sin interacción reciente</li>
                                        <li><i class="bi bi-x-circle text-danger"></i> <strong>No Interesado:</strong> Descartó servicios</li>
                                        <li><i class="bi bi-check-circle-fill text-success"></i> <strong>Cliente VIP:</strong> Cliente prioritario de alto valor</li>
                                    </ul>
                                    <strong>Uso:</strong> Priorizar acciones comerciales según la fase del embudo de ventas
                                    <br><strong>Workflow:</strong> Automatizar tareas según cambios de estado
                                </div>
                            </div>
                        </div>

                        <!-- Act/Des Estado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingActDes">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseActDes" aria-expanded="false" aria-controls="collapseActDes">
                                        <i class="bi bi-toggle-on mr-2"></i> Act/Des Estado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseActDes" class="collapse" role="tabpanel" aria-labelledby="headingActDes" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Control de visibilidad.</strong> Activa o desactiva el contacto en el sistema.
                                    <br><strong>Botón rojo (🗑️):</strong> Desactivar contacto - archiva sin eliminar permanentemente
                                    <br><strong>Contacto activo:</strong> Visible en búsquedas y listados principales
                                    <br><strong>Contacto inactivo:</strong> Movido a histórico, no aparece en operaciones diarias
                                    <br><strong>Uso:</strong> Limpiar la base de datos activa de contactos obsoletos
                                    <br><strong>Reversible:</strong> Los contactos desactivados pueden reactivarse cuando sea necesario
                                    <br><strong>Beneficio:</strong> Mantiene el sistema organizado sin perder información histórica
                                    <br><strong>Auditoría:</strong> Se registra quién y cuándo desactivó el contacto
                                </div>
                            </div>
                        </div>

                        <!-- Editar -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingEdit">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseEdit" aria-expanded="false" aria-controls="collapseEdit">
                                        <i class="bi bi-pencil-square mr-2"></i> Edit.
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseEdit" class="collapse" role="tabpanel" aria-labelledby="headingEdit" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Acción de modificación.</strong> Permite editar todos los datos del contacto.
                                    <br><strong>Botón azul (✏️):</strong> Abre el formulario de edición precargado con datos actuales
                                    <br><strong>Campos editables:</strong> Nombre, teléfonos, emails, dirección, método preferido, estado, observaciones
                                    <br><strong>Uso común:</strong> 
                                    <ul class="mt-2 mb-0">
                                        <li>Actualizar información de contacto</li>
                                        <li>Cambiar estado según evolución comercial</li>
                                        <li>Añadir nuevas observaciones o notas</li>
                                        <li>Corregir errores en datos registrados</li>
                                    </ul>
                                    <br><strong>Seguridad:</strong> Los cambios se registran en log de auditoría con usuario y timestamp
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
                                Use el campo de búsqueda superior para encontrar contactos por nombre, teléfono, email o cualquier dato registrado.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Ordenar Columnas:</h6>
                            <p class="text-muted small">
                                Haga clic en las cabeceras de columna para ordenar ascendente o descendente según sus necesidades.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro por Método:</h6>
                            <p class="text-muted small">
                                Filtre contactos según su canal preferido: WhatsApp, Teléfono, Email o Presencial.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro por Estado:</h6>
                            <p class="text-muted small">
                                Encuentre rápidamente contactos según su estado: Activo, Prospecto, Lead, Inactivo, VIP.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro por Fecha:</h6>
                            <p class="text-muted small">
                                Identifique contactos recientes o que requieren seguimiento por inactividad prolongada.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Exportar Resultados:</h6>
                            <p class="text-muted small">
                                Exporte los contactos filtrados a Excel, PDF o copie al portapapeles para análisis externo.
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
                                        Copia todos los contactos visibles en formato texto para pegar en otras aplicaciones o compartir.
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
                                        Descarga un archivo Excel (.xlsx) con todos los contactos filtrados para análisis o respaldo.
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
                                        Genera un documento PDF profesional con el listado de contactos para reportes o presentaciones.
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
                                        Imprime directamente la tabla de contactos con un formato optimizado y legible.
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
                                    <td>Muestra información adicional: dirección, observaciones, historial</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-whatsapp text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: WhatsApp</td>
                                    <td>Contacto preferido vía aplicación de mensajería</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-telephone-fill text-primary" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Teléfono</td>
                                    <td>Contacto preferido por llamada telefónica directa</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-envelope-fill text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Email</td>
                                    <td>Contacto preferido por correo electrónico</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-person-fill text-info" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método: Presencial</td>
                                    <td>Contacto preferido mediante reunión cara a cara</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Estado Activo/Exitoso</td>
                                    <td>Contacto activo con relación comercial vigente</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Desactivar Contacto</td>
                                    <td>Archiva el contacto sin eliminarlo permanentemente</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Editar Contacto</td>
                                    <td>Permite modificar todos los datos del contacto</td>
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
                            <li><strong>Actualización constante:</strong> Mantenga los datos de contacto actualizados tras cada interacción</li>
                            <li><strong>Método preferido:</strong> Respete siempre el canal de comunicación preferido del cliente</li>
                            <li><strong>Estados claros:</strong> Use estados específicos para facilitar el seguimiento del embudo comercial</li>
                            <li><strong>Observaciones detalladas:</strong> Documente preferencias, restricciones y contexto relevante</li>
                            <li><strong>Revisión periódica:</strong> Identifique contactos inactivos que requieran seguimiento</li>
                            <li><strong>Segmentación:</strong> Use filtros para crear listas específicas según campañas o productos</li>
                            <li><strong>Exportación regular:</strong> Haga respaldos periódicos de su base de contactos</li>
                            <li><strong>Limpieza de datos:</strong> Archive contactos obsoletos para mantener la base depurada</li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: SMC v1.0 - Última actualización: 24-11-2025
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