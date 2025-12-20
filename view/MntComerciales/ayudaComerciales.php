<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - Mantenimiento de Personal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<!-- Botón para abrir el modal (demo) -->


<!-- Modal de Ayuda para Mantenimiento de Personal -->
<div class="modal fade" id="modalAyudaComerciales" tabindex="-1" role="dialog" aria-labelledby="modalAyudaComercialesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaComercialLabel">
                    <i class="bi bi-question-circle-fill mr-2" style="font-size: 1.4rem;"></i>
                    Ayuda - Mantenimiento de Personal
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
                        <i class="bi bi-chat-dots-fill mr-2"></i> ¿Qué es el Mantenimiento de Personal?
                    </h6>
                    <p class="text-muted">
                        El <strong>Mantenimiento de Personal</strong> permite gestionar toda la información del personal de la empresa que tendrá acceso al sistema.
                        En esta pantalla podrá dar de alta a empleados, asignarles un usuario para acceder a la aplicación, y mantener actualizados sus datos de contacto.
                    </p>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle mr-2"></i>
                        <strong>Importante:</strong> Cada empleado debe tener un usuario único asignado para poder acceder al sistema con sus credenciales.
                    </div>
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

                        <!-- Nombre -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingNombre">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                        <i class="bi bi-person-fill mr-2"></i> Nombre
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseNombre" class="collapse" role="tabpanel" aria-labelledby="headingNombre" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Nombre del empleado.
                                    <br><strong>Formato:</strong> Solo letras y espacios
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 50 caracteres
                                    <br><strong>Uso:</strong> Identificación del empleado junto con los apellidos
                                    <br><strong>Recomendación:</strong> Escribir el nombre completo sin abreviaturas
                                </div>
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingApellidos">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseApellidos" aria-expanded="false" aria-controls="collapseApellidos">
                                        <i class="bi bi-person-badge-fill mr-2"></i> Apellidos
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseApellidos" class="collapse" role="tabpanel" aria-labelledby="headingApellidos" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Apellidos del empleado.
                                    <br><strong>Formato:</strong> Solo letras y espacios
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 50 caracteres
                                    <br><strong>Uso:</strong> Complementa el nombre para la identificación completa
                                    <br><strong>Recomendación:</strong> Incluir ambos apellidos cuando sea posible
                                </div>
                            </div>
                        </div>

                        <!-- Móvil -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingMovil">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseMovil" aria-expanded="false" aria-controls="collapseMovil">
                                        <i class="bi bi-phone-fill mr-2"></i> Móvil
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseMovil" class="collapse" role="tabpanel" aria-labelledby="headingMovil" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Número de teléfono móvil del empleado.
                                    <br><strong>Formato:</strong> Solo números
                                    <br><strong>Validaciones:</strong> Máximo 14 posiciones (incluye prefijo internacional)
                                    <br><strong>Uso:</strong> Contacto directo y urgente con el empleado
                                    <br><strong>Ejemplo:</strong> 625123456 o +34625123456
                                    <br><strong>Recomendación:</strong> Incluir prefijo internacional si trabaja en el extranjero
                                </div>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingTelefono">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseTelefono" aria-expanded="false" aria-controls="collapseTelefono">
                                        <i class="bi bi-telephone-fill mr-2"></i> Teléfono
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseTelefono" class="collapse" role="tabpanel" aria-labelledby="headingTelefono" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Número de teléfono fijo del empleado.
                                    <br><strong>Formato:</strong> Solo números
                                    <br><strong>Validaciones:</strong> Máximo 14 posiciones
                                    <br><strong>Uso:</strong> Teléfono alternativo o de oficina
                                    <br><strong>Ejemplo:</strong> 918123456
                                    <br><strong>Nota:</strong> Puede ser el mismo número que el móvil si no tiene teléfono fijo
                                </div>
                            </div>
                        </div>

                        <!-- Usuario Asignado -->
                        <div class="card">
                            <div class="card-header" role="tab" id="headingUsuario">
                                <h5 class="mb-0">
                                    <a class="collapsed d-flex align-items-center" data-toggle="collapse" href="#collapseUsuario" aria-expanded="false" aria-controls="collapseUsuario">
                                        <i class="bi bi-person-check-fill mr-2"></i> Usuario Asignado
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseUsuario" class="collapse" role="tabpanel" aria-labelledby="headingUsuario" data-parent="#accordionCampos">
                                <div class="card-body">
                                    <strong>Campo obligatorio.</strong> Usuario del sistema asignado al empleado.
                                    <br><strong>Tipo:</strong> Lista desplegable (select)
                                    <br><strong>Función:</strong> Vincula al empleado con su cuenta de acceso al sistema
                                    <br><strong>Uso:</strong> Define qué credenciales utilizará el empleado para iniciar sesión
                                    <br><strong>Importante:</strong> Un usuario solo puede estar asignado a un empleado a la vez
                                    <br><strong>Opciones:</strong> Se muestran solo usuarios disponibles (no asignados a otros empleados)
                                    <br><strong>Recomendación:</strong> Verificar que el usuario seleccionado corresponde al empleado correcto
                                </div>
                            </div>
                        </div>

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
                                    <strong>Campo de sistema.</strong> Indica si el empleado está activo en el sistema.
                                    <br><strong>Estados posibles:</strong>
                                    <ul class="mt-2 mb-2">
                                        <li><i class="bi bi-check-circle text-success"></i> <strong>Activo (✓):</strong> Empleado en plantilla, puede acceder al sistema</li>
                                        <li><i class="bi bi-x-circle text-danger"></i> <strong>Inactivo (✗):</strong> Empleado dado de baja, sin acceso al sistema</li>
                                    </ul>
                                    <br><strong>Nota:</strong> Al desactivar un empleado, su usuario también queda deshabilitado automáticamente
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
                                    <strong>Control de disponibilidad.</strong> Cambia el estado del empleado entre activo e inactivo.
                                    <br><strong>Botón rojo (🗑️):</strong> Desactivar empleado - bloquea su acceso al sistema
                                    <br><strong>Empleado activo:</strong> Puede iniciar sesión y trabajar normalmente en la aplicación
                                    <br><strong>Empleado inactivo:</strong> Sin acceso al sistema, pero mantiene histórico de actividades
                                    <br><strong>Uso común:</strong> Dar de baja temporal o permanente a empleados
                                    <br><strong>Seguridad:</strong> No se eliminan datos, solo se deshabilita el acceso
                                    <br><strong>Importante:</strong> El usuario vinculado también se desactiva automáticamente
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
                                    <strong>Acción de modificación.</strong> Permite editar los datos del empleado.
                                    <br><strong>Botón azul (✏️):</strong> Abre el formulario de edición con datos precargados
                                    <br><strong>Campos editables:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Nombre</li>
                                        <li>Apellidos</li>
                                        <li>Móvil</li>
                                        <li>Teléfono</li>
                                        <li>Usuario asignado</li>
                                    </ul>
                                    <br><strong>Uso común:</strong> Actualizar datos de contacto, cambiar usuario asignado
                                    <br><strong>Importante:</strong> Si cambia el usuario, el anterior quedará disponible para asignar a otro empleado
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Sección: Flujo de Trabajo -->
                <div class="mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-diagram-3-fill mr-2"></i>
                        Flujo de Trabajo Recomendado
                    </h6>
                    <div class="alert alert-light border">
                        <ol class="mb-0">
                            <li><strong>Crear el usuario:</strong> Primero debe existir el usuario en el sistema (tabla de usuarios)</li>
                            <li><strong>Registrar empleado:</strong> Complete todos los datos personales y de contacto</li>
                            <li><strong>Asignar usuario:</strong> Vincule el usuario creado con el empleado</li>
                            <li><strong>Verificar acceso:</strong> El empleado ya puede iniciar sesión con sus credenciales</li>
                            <li><strong>Mantener actualizado:</strong> Revise periódicamente los datos de contacto</li>
                        </ol>
                    </div>
                    
                    <h6 class="text-secondary mt-3">Relación con el Sistema de Usuarios</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Personal</strong></td>
                                    <td>Datos personales y de contacto del empleado (nombre, apellidos, teléfonos)</td>
                                </tr>
                                <tr>
                                    <td><strong>Usuario</strong></td>
                                    <td>Credenciales de acceso al sistema (username, password, permisos)</td>
                                </tr>
                                <tr>
                                    <td><strong>Vinculación</strong></td>
                                    <td>Cada empleado debe tener un usuario único asignado para acceder</td>
                                </tr>
                                <tr>
                                    <td><strong>Unicidad</strong></td>
                                    <td>Un usuario solo puede estar asignado a un empleado a la vez</td>
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
                                Use el campo de búsqueda superior para encontrar empleados por nombre, apellidos o teléfono.
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
                                Filtre para ver solo empleados activos, inactivos o todos según necesidad.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Búsqueda por Usuario:</h6>
                            <p class="text-muted small">
                                Localice rápidamente qué empleado tiene asignado un usuario específico.
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
                                        Copia el listado de empleados en formato texto para compartir o documentar.
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
                                        Descarga un archivo Excel (.xlsx) con el directorio de personal y sus contactos.
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
                                        Genera un documento PDF con el listado completo de personal.
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
                                        Imprime directamente la tabla de empleados con formato optimizado.
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
                                    <td>Muestra información adicional del empleado (usuario asignado, etc.)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Empleado Activo</td>
                                    <td>El empleado está en plantilla y puede acceder al sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Empleado Inactivo</td>
                                    <td>El empleado está de baja y no tiene acceso al sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Desactivar Empleado</td>
                                    <td>Da de baja al empleado sin eliminar sus datos del sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Editar Empleado</td>
                                    <td>Permite modificar datos personales, contacto y usuario asignado</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-person-check" style="font-size: 1.5rem;"></i>
                                    </td>
                                    <td>Usuario Asignado</td>
                                    <td>Indica que el empleado tiene un usuario vinculado para acceder</td>
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
                            <li><strong>Datos completos:</strong> Asegúrese de registrar todos los datos de contacto del empleado</li>
                            <li><strong>Usuario único:</strong> Verifique que cada usuario esté asignado a un solo empleado</li>
                            <li><strong>Actualización periódica:</strong> Revise y actualice los números de teléfono regularmente</li>
                            <li><strong>No elimine:</strong> Use la desactivación en lugar de eliminar para conservar el histórico</li>
                            <li><strong>Validación de datos:</strong> Verifique que los números de teléfono sean correctos antes de guardar</li>
                            <li><strong>Bajas temporales:</strong> Desactive empleados en ausencia prolongada y reactive al regreso</li>
                            <li><strong>Seguridad:</strong> Al desactivar un empleado, su usuario se bloquea automáticamente</li>
                            <li><strong>Cambio de usuario:</strong> Si reasigna un usuario, el anterior queda libre para otro empleado</li>
                            <li><strong>Nombres completos:</strong> Use nombres y apellidos completos sin abreviaturas</li>
                            <li><strong>Coordinación:</strong> Coordine con IT para crear usuarios antes de dar de alta empleados</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle mr-2"></i>
                            Advertencias Importantes
                        </h6>
                        <ul class="mb-0">
                            <li>Un usuario solo puede estar asignado a un empleado a la vez</li>
                            <li>Al desactivar un empleado, pierde inmediatamente el acceso al sistema</li>
                            <li>Los datos históricos (presupuestos, actividades) se mantienen aunque el empleado esté inactivo</li>
                            <li>Verifique dos veces antes de cambiar el usuario asignado de un empleado</li>
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