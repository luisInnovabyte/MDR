<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Mantenimiento de Métodos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<!-- Botón para abrir el modal (demo) -->


<!-- Modal de Ayuda para Mantenimiento de Métodos -->
<div class="modal fade" id="modalAyudaEstadosLlamada" tabindex="-1" role="dialog" aria-labelledby="modalAyudaEstadosLlamadaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaEstadosLlamadaLabel">
                    <i class="bi bi-question-circle-fill mr-2" style="font-size: 1.4rem;"></i>
                    Ayuda - Mantenimiento de Métodos
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
                        <i class="bi bi-chat-dots-fill mr-2"></i> ¿Qué es el Mantenimiento de Estados LLamada?
                    </h6>
                    <p class="text-muted">
                        El mantenimiento de estado de llamadas permite actualizar, corregir y gestionar la información de cada llamada registrada,
                         asegurando que el sistema refleje con precisión su situación actual.
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
                                    <strong>Icono interactivo.</strong> Permite expandir la fila para ver información adicional del método.
                                    <br><strong>Acción:</strong> Haga clic para mostrar/ocultar detalles complementarios
                                    <br><strong>Contenido expandible:</strong> Descripción detallada, configuraciones técnicas, estadísticas de uso
                                    <br><strong>Uso:</strong> Visualizar información extendida sin saturar la vista principal de la tabla
                                </div>
                            </div>
                        </div>

                        <!-- Descripcion estado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingNombre">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                        <i class="bi bi-tag-fill mr-2"></i> Descripcion de Estado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseNombre" class="collapse" role="tabpanel" aria-labelledby="headingNombre" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> 
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li>Recibida Sin Atención</li>
                                        <li>Con Contacto</li>
                                        <li>Cita Cerrada</li>
                                        <li>Perdida</li>
                                        <li>En Espera</li>
                                    </ul>
                                    <strong>Uso:</strong> Identificación clara del canal en todo el sistema
                                    <br><strong>Validaciones:</strong> Texto alfanumérico, mínimo 3 caracteres, máximo 50
                                    <br><strong>Unicidad:</strong> Cada nombre debe ser único en el sistema
                                    <br><strong>Recomendación:</strong> Use nombres descriptivos y profesionales
                                </div>
                            </div>
                        </div>

                        <!-- Peso estado -->
                    
                        <div class="card">
                            <div class="card-header" role="tab" id="headingPesoEstado">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapsePesoEstado" aria-expanded="false" aria-controls="collapsePesoEstado">
                                        <i class="bi bi-speedometer2 mr-2"></i> Peso de los Estados
                                    </a>
                                </h5>
                            </div>
                            <div id="collapsePesoEstado" class="collapse" role="tabpanel" aria-labelledby="headingPesoEstado" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo informativo.</strong> 
                                    <br><strong>Descripción:</strong> El <em>peso del estado</em> indica la prioridad o importancia de cada llamada dentro del sistema. 
                                    Por ejemplo, una llamada <strong>pendiente</strong> puede tener mayor peso que una llamada <strong>atendida</strong>, ayudando al equipo a identificar rápidamente cuáles requieren atención inmediata.
                                    <br><strong>Uso práctico:</strong> Ordenar o filtrar llamadas según urgencia o relevancia.
                                    <br><strong>Recomendación:</strong> Mantenga coherencia en los valores de peso para reflejar correctamente la prioridad.
                                </div>
                            </div>
                        </div>
                        <!-- Peso de Estado -->

                        <!-- Estado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingEstado">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                        <i class="bi bi-check-circle mr-2"></i> Estado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseEstado" class="collapse" role="tabpanel" aria-labelledby="headingEstado" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Indica si el método está disponible para usar.
                                    <br><strong>Estados posibles:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li><i class="bi bi-check-circle text-success"></i> <strong>Activo (✓):</strong> Método disponible para selección en formularios</li>
                                        <li><i class="bi bi-x-circle text-danger"></i> <strong>Inactivo (✗):</strong> Método deshabilitado, no aparece en opciones</li>
                                    </ul>
                                    <strong>Uso:</strong> Controlar qué canales están disponibles operativamente
                                    <br><strong>Ejemplo práctico:</strong> Si deja de usar un canal, desactívelo sin eliminarlo
                                    <br><strong>Impacto:</strong> Los métodos inactivos no aparecen en formularios de contactos ni llamadas
                                    <br><strong>Reversible:</strong> Puede reactivarse en cualquier momento
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
                                    <strong>Control de disponibilidad.</strong> Cambia el estado del método entre activo e inactivo.
                                    <br><strong>Botón rojo (🗑️):</strong> Desactivar método - deja de estar disponible en el sistema
                                    <br><strong>Método activo:</strong> Aparece como opción en formularios de contactos, llamadas y comunicaciones
                                    <br><strong>Método inactivo:</strong> Oculto de opciones pero conserva histórico de uso
                                    <br><strong>Uso común:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Desactivar canales que la empresa dejó de usar</li>
                                        <li>Deshabilitar temporalmente un método por mantenimiento</li>
                                        <li>Activar nuevos canales cuando estén operativos</li>
                                    </ul>
                                    <br><strong>Seguridad:</strong> Los registros históricos con métodos inactivos se mantienen intactos
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
                                    <strong>Acción de modificación.</strong> Permite editar los datos del método de contacto.
                                    <br><strong>Botón azul (✏️):</strong> Abre el formulario de edición con datos precargados
                                    <br><strong>Campos editables:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Nombre del método</li>
                                        <li>Estado (activo/inactivo)</li>
                                        <li>Icono o imagen representativa</li>
                                        <li>Descripción detallada</li>
                                        <li>Configuraciones adicionales</li>
                                    </ul>
                                    <br><strong>Uso común:</strong> Actualizar nombres, cambiar iconos, corregir descripciones
                                    <br><strong>Importante:</strong> Los cambios afectan la visualización pero no modifican registros históricos
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
                                Use el campo de búsqueda superior para encontrar métodos por nombre o descripción.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Ordenar Columnas:</h6>
                            <p class="text-muted small">
                                Haga clic en las cabeceras de columna para ordenar alfabéticamente o por estado.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro por Estado:</h6>
                            <p class="text-muted small">
                                Filtre para ver solo métodos activos, inactivos o todos según necesidad.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Limpiar Filtros:</h6>
                            <p class="text-muted small">
                                Use el botón "Limpiar Filtros" para restablecer la vista a todos los métodos.
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
                                        Copia el listado de métodos en formato texto para compartir o documentar.
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
                                        Descarga un archivo Excel (.xlsx) con la configuración de métodos.
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
                                        Genera un documento PDF con el catálogo de métodos disponibles.
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
                                        Imprime directamente la tabla de métodos con formato optimizado.
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
                                    <td>Muestra descripción completa y configuración del método</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método Activo</td>
                                    <td>El método está disponible para usar en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Método Inactivo</td>
                                    <td>El método está deshabilitado y no aparece en opciones</td>
                                </tr>
                                <tr>
                                    <td class="text-center">📧</td>
                                    <td>Icono Correo Electrónico</td>
                                    <td>Representación visual del método email</td>
                                </tr>
                                <tr>
                                    <td class="text-center">📞</td>
                                    <td>Icono Llamada Telefónica</td>
                                    <td>Representación visual del método teléfono</td>
                                </tr>
                                <tr>
                                    <td class="text-center">💬</td>
                                    <td>Icono WhatsApp Business</td>
                                    <td>Representación visual del método WhatsApp</td>
                                </tr>
                                <tr>
                                    <td class="text-center">👤</td>
                                    <td>Icono Presencial</td>
                                    <td>Representación visual del método presencial</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Desactivar Método</td>
                                    <td>Deshabilita el método sin eliminarlo del sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Editar Método</td>
                                    <td>Permite modificar nombre, icono y configuración</td>
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
                            <li><strong>Nombres claros:</strong> Use nombres descriptivos y profesionales para cada método</li>
                            <li><strong>Iconos distintivos:</strong> Asigne iconos reconocibles que faciliten la identificación visual</li>
                            <li><strong>Revise periódicamente:</strong> Desactive métodos que ya no utilice para mantener el catálogo limpio</li>
                            <li><strong>No elimine métodos:</strong> Desactive en lugar de eliminar para conservar el historial</li>
                            <li><strong>Documente cambios:</strong> Use el campo de descripción para justificar configuraciones</li>
                            <li><strong>Active según capacidad:</strong> Solo active métodos que realmente pueda gestionar operativamente</li>
                            <li><strong>Consistencia:</strong> Mantenga coherencia en la nomenclatura de métodos similares</li>
                            <li><strong>Capacitación:</strong> Asegúrese de que el equipo conozca todos los métodos activos</li>
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