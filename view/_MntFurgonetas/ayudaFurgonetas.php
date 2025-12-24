<!-- Modal de Ayuda -->
<div class="modal fade" id="modalAyudaFurgonetas" tabindex="-1" aria-labelledby="modalAyudaFurgonetasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaFurgonetasLabel">
                    <i class="fa fa-question-circle me-2"></i>Ayuda - Mantenimiento de Furgonetas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-primary"><i class="fa fa-info-circle me-2"></i>Descripción General</h6>
                <p>
                    Este módulo permite gestionar el inventario de furgonetas de la empresa, incluyendo el registro de datos técnicos, 
                    seguimiento de mantenimientos, control de ITV y seguros, así como el historial de kilometraje.
                </p>

                <hr>

                <h6 class="text-primary"><i class="fa fa-list-ul me-2"></i>Funcionalidades Principales</h6>
                <ul>
                    <li><strong>Registro de Furgonetas:</strong> Alta de nuevas furgonetas con datos completos (matrícula, marca, modelo, año, etc.).</li>
                    <li><strong>Gestión de Documentación:</strong> Control de fechas de ITV, seguros, pólizas y compañías aseguradoras.</li>
                    <li><strong>Registro de Kilometraje:</strong> Seguimiento del kilometraje actual y histórico de cada vehículo.</li>
                    <li><strong>Mantenimientos:</strong> Registro detallado de revisiones, reparaciones y mantenimientos preventivos.</li>
                    <li><strong>Estados:</strong> Control del estado operativo (operativa, en taller, averiada, baja).</li>
                    <li><strong>Capacidades:</strong> Registro de capacidad de carga (kg y m³) de cada furgoneta.</li>
                </ul>

                <hr>

                <h6 class="text-primary"><i class="fa fa-search me-2"></i>Búsqueda y Filtros</h6>
                <p>La tabla permite filtrar furgonetas por:</p>
                <ul>
                    <li><strong>Matrícula:</strong> Búsqueda directa por matrícula del vehículo.</li>
                    <li><strong>Marca y Modelo:</strong> Filtros por fabricante y modelo.</li>
                    <li><strong>Año:</strong> Filtro por año de fabricación.</li>
                    <li><strong>Estado:</strong> Filtro por estado operativo (operativa, taller, averiada, baja).</li>
                    <li><strong>Activo/Inactivo:</strong> Filtro para ver solo furgonetas activas o todas.</li>
                </ul>
                <p class="text-muted small">
                    <i class="fa fa-lightbulb me-1"></i>Los filtros se pueden combinar para búsquedas más específicas.
                </p>

                <hr>

                <h6 class="text-primary"><i class="fa fa-table me-2"></i>Columnas de la Tabla</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="30%">Columna</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fa fa-plus-circle text-primary me-1"></i><strong>Expandir</strong></td>
                            <td>Click para ver detalles completos de la furgoneta (ITV, seguro, capacidades, etc.).</td>
                        </tr>
                        <tr>
                            <td><strong>ID</strong></td>
                            <td>Identificador único interno de la furgoneta.</td>
                        </tr>
                        <tr>
                            <td><strong>Matrícula</strong></td>
                            <td>Matrícula del vehículo (campo obligatorio y único).</td>
                        </tr>
                        <tr>
                            <td><strong>Marca/Modelo</strong></td>
                            <td>Fabricante y modelo del vehículo.</td>
                        </tr>
                        <tr>
                            <td><strong>Año</strong></td>
                            <td>Año de fabricación del vehículo.</td>
                        </tr>
                        <tr>
                            <td><strong>Estado</strong></td>
                            <td>Estado operativo actual (operativa, taller, averiada, baja).</td>
                        </tr>
                        <tr>
                            <td><strong>Act./Desac.</strong></td>
                            <td>Botón para activar o desactivar la furgoneta (soft delete).</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-edit text-warning me-1"></i><strong>Editar</strong></td>
                            <td>Acceso al formulario de edición completo de la furgoneta.</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-tachometer-alt text-info me-1"></i><strong>Kilometraje</strong></td>
                            <td>Acceso al módulo de registro y consulta de kilometraje.</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-wrench text-secondary me-1"></i><strong>Mantenimiento</strong></td>
                            <td>Acceso al módulo de gestión de mantenimientos y reparaciones.</td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <h6 class="text-primary"><i class="fa fa-cog me-2"></i>Acciones Disponibles</h6>
                
                <div class="mb-3">
                    <strong><i class="fa fa-plus-circle text-success me-2"></i>Nueva Furgoneta:</strong>
                    <p class="ms-4 mb-2">Abre el formulario para dar de alta una nueva furgoneta. Datos requeridos: matrícula (única).</p>
                </div>

                <div class="mb-3">
                    <strong><i class="fa fa-edit text-warning me-2"></i>Editar:</strong>
                    <p class="ms-4 mb-2">Accede al formulario completo con 5 secciones:
                        <br>1. Identificación (matrícula, marca, modelo, año, bastidor)
                        <br>2. ITV y Seguros (fechas, compañía, póliza)
                        <br>3. Capacidad (carga en kg y m³)
                        <br>4. Mantenimiento (taller habitual, revisiones, combustible)
                        <br>5. Observaciones
                    </p>
                </div>

                <div class="mb-3">
                    <strong><i class="fa fa-tachometer-alt text-info me-2"></i>Kilometraje:</strong>
                    <p class="ms-4 mb-2">Registra y consulta el historial de kilometraje, calcula promedios y alerta sobre próximas revisiones.</p>
                </div>

                <div class="mb-3">
                    <strong><i class="fa fa-wrench text-secondary me-2"></i>Mantenimiento:</strong>
                    <p class="ms-4 mb-2">Gestiona el historial completo de mantenimientos: revisiones, reparaciones, ITV, cambios de neumáticos, etc. Incluye costos y garantías.</p>
                </div>

                <div class="mb-3">
                    <strong><i class="fa fa-ban text-danger me-2"></i>Desactivar:</strong>
                    <p class="ms-4 mb-2">Desactiva la furgoneta sin eliminarla físicamente (soft delete). Se puede reactivar posteriormente.</p>
                </div>

                <hr>

                <h6 class="text-primary"><i class="fa fa-exclamation-triangle me-2"></i>Alertas y Notificaciones</h6>
                <ul>
                    <li><strong class="text-danger">ITV Vencida:</strong> Cuando la fecha de ITV ha pasado.</li>
                    <li><strong class="text-warning">ITV Próxima:</strong> Cuando faltan 30 días o menos para la ITV.</li>
                    <li><strong class="text-danger">Seguro Vencido:</strong> Cuando la fecha del seguro ha pasado.</li>
                    <li><strong class="text-warning">Seguro Próximo:</strong> Cuando faltan 30 días o menos para el vencimiento.</li>
                    <li><strong class="text-info">Revisión Pendiente:</strong> Cuando los kilómetros superan el intervalo de revisión configurado.</li>
                </ul>

                <hr>

                <h6 class="text-primary"><i class="fa fa-clipboard-check me-2"></i>Buenas Prácticas</h6>
                <ul>
                    <li>Mantener actualizado el kilometraje antes y después de cada uso.</li>
                    <li>Registrar todos los mantenimientos, incluso los pequeños.</li>
                    <li>Revisar periódicamente las fechas de ITV y seguros.</li>
                    <li>Utilizar el campo de observaciones para anotaciones importantes.</li>
                    <li>No eliminar furgonetas, solo desactivarlas para mantener el historial.</li>
                    <li>Configurar el intervalo de km entre revisiones para recibir alertas automáticas.</li>
                </ul>

                <hr>

                <div class="alert alert-info mb-0">
                    <i class="fa fa-info-circle me-2"></i><strong>Nota:</strong> Los datos de las furgonetas son críticos para la operación. 
                    Asegúrate de mantener toda la información actualizada, especialmente la documentación legal (ITV y seguros) 
                    y el historial de mantenimientos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
