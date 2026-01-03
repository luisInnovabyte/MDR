<!-- Modal de Ayuda para Kit -->
<div class="modal fade" id="modalAyudaKit" tabindex="-1" aria-labelledby="modalAyudaKitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalAyudaKitLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Gestión de Composición de KIT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- Introducción -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>¿Qué es la Composición de KIT?</h6>
                            <p>
                                Esta pantalla permite gestionar los componentes que forman parte de un KIT de artículos. 
                                Un KIT es un conjunto de artículos individuales que se alquilan como una unidad completa.
                            </p>
                            <div class="alert alert-info" role="alert">
                                <strong><i class="fas fa-lightbulb me-2"></i>Ejemplo:</strong> 
                                Un KIT de "Iluminación Básica" puede estar compuesto por: 2 Focos LED + 1 Trípode + 1 Cable de extensión.
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Información del KIT -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-box me-2"></i>Información del KIT</h6>
                            <p>En la parte superior se muestra:</p>
                            <ul>
                                <li><strong>Nombre del KIT:</strong> Nombre identificativo del artículo KIT</li>
                                <li><strong>Código:</strong> Código único del artículo en el sistema</li>
                                <li><strong>ID Artículo:</strong> Identificador numérico único</li>
                                <li><strong>Total Componentes:</strong> Cantidad total de artículos que componen el KIT</li>
                                <li><strong>Precio Total:</strong> Suma de los precios de todos los componentes</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sección: Agregar Componente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-plus-circle me-2"></i>Agregar Componente</h6>
                            <ol>
                                <li>Haz clic en el botón <span class="badge bg-primary"><i class="fas fa-plus"></i> Agregar Componente</span></li>
                                <li>Selecciona el artículo que deseas agregar al KIT</li>
                                <li>Indica la cantidad de unidades de ese artículo</li>
                                <li>Haz clic en <span class="badge bg-primary">Guardar</span></li>
                            </ol>
                            <div class="alert alert-warning" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Importante:</strong>
                                <ul class="mb-0">
                                    <li>No puedes agregar el mismo artículo maestro como componente (evitar recursividad)</li>
                                    <li>No puedes agregar artículos que también son KITs como componentes</li>
                                    <li>No puedes agregar el mismo componente dos veces al mismo KIT</li>
                                    <li>La cantidad debe ser mayor a 0</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Editar Componente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-edit me-2"></i>Editar Componente</h6>
                            <ol>
                                <li>Localiza el componente en la tabla</li>
                                <li>Haz clic en el botón <span class="badge bg-info"><i class="fas fa-edit"></i></span> de la columna "Editar"</li>
                                <li>Modifica la cantidad según sea necesario</li>
                                <li>Haz clic en <span class="badge bg-primary">Guardar</span></li>
                            </ol>
                            <div class="alert alert-info" role="alert">
                                <strong><i class="fas fa-info-circle me-2"></i>Nota:</strong> 
                                No se puede cambiar el artículo componente durante la edición. Si necesitas cambiar el artículo, 
                                elimina el componente actual y agrega uno nuevo.
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Eliminar Componente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-trash me-2"></i>Eliminar Componente</h6>
                            <ol>
                                <li>Localiza el componente en la tabla</li>
                                <li>Haz clic en el botón <span class="badge bg-danger"><i class="fas fa-trash"></i></span> de la columna "Eliminar"</li>
                                <li>Confirma la eliminación en el mensaje que aparece</li>
                            </ol>
                            <div class="alert alert-danger" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i>Atención:</strong> 
                                Esta acción es irreversible. El componente será eliminado permanentemente del KIT.
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Tabla de Componentes -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-table me-2"></i>Columnas de la Tabla</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Columna</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Código</strong></td>
                                        <td>Código único del artículo componente</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre</strong></td>
                                        <td>Nombre descriptivo del artículo componente</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cantidad</strong></td>
                                        <td>Cantidad de unidades de este artículo en el KIT</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Precio Unitario</strong></td>
                                        <td>Precio de alquiler de una unidad del artículo</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Subtotal</strong></td>
                                        <td>Precio unitario × Cantidad (contribución al precio total del KIT)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado</strong></td>
                                        <td>Estado del registro (activo/inactivo)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Eliminar</strong></td>
                                        <td>Botón para eliminar el componente del KIT</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Editar</strong></td>
                                        <td>Botón para editar la cantidad del componente</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sección: Búsqueda y Filtros -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-search me-2"></i>Búsqueda y Filtros</h6>
                            <ul>
                                <li><strong>Búsqueda General:</strong> Utiliza el campo de búsqueda en la parte superior derecha para buscar por cualquier campo visible</li>
                                <li><strong>Ordenamiento:</strong> Haz clic en los encabezados de las columnas para ordenar ascendente o descendentemente</li>
                                <li><strong>Paginación:</strong> Navega entre páginas usando los controles en la parte inferior de la tabla</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sección: Reglas de Negocio -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-gavel me-2"></i>Reglas de Negocio</h6>
                            <div class="alert alert-secondary" role="alert">
                                <ul class="mb-0">
                                    <li><strong>Un artículo KIT no puede contener a sí mismo</strong> como componente (validación de recursividad)</li>
                                    <li><strong>Los componentes no pueden ser KITs</strong> a su vez (solo artículos simples)</li>
                                    <li><strong>No se permiten componentes duplicados</strong> en el mismo KIT</li>
                                    <li><strong>La cantidad mínima es 1</strong> unidad por componente</li>
                                    <li><strong>El artículo maestro debe estar marcado como KIT</strong> en el registro de artículos</li>
                                    <li><strong>Al eliminar un componente</strong>, se elimina permanentemente (no soft delete)</li>
                                    <li><strong>Si se elimina el artículo maestro</strong>, se eliminan en cascada todos sus componentes</li>
                                    <li><strong>No se puede eliminar un artículo componente</strong> si está siendo usado en uno o más KITs</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Navegación -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-map-signs me-2"></i>Navegación</h6>
                            <ul>
                                <li><strong>Volver a Artículos:</strong> Haz clic en "Artículos" en el breadcrumb superior para regresar al listado de artículos</li>
                                <li><strong>Dashboard:</strong> Haz clic en "Dashboard" en el breadcrumb para ir al panel principal</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sección: Consejos -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-lightbulb me-2"></i>Consejos</h6>
                            <div class="alert alert-success" role="alert">
                                <ul class="mb-0">
                                    <li>Planifica bien la composición del KIT antes de crearlo</li>
                                    <li>Verifica que todos los componentes estén disponibles en inventario</li>
                                    <li>Revisa el precio total del KIT para asegurar competitividad comercial</li>
                                    <li>Mantén actualizada la composición del KIT según cambios en el catálogo</li>
                                    <li>Documenta cualquier dependencia especial entre componentes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
