<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'mantenimientos';
require_once('../../config/template/verificarPermiso.php');

// ----------------------
//   Validar parámetros GET
// ----------------------
$modo = isset($_GET['modo']) ? $_GET['modo'] : '';

if (!in_array($modo, ['nuevo', 'editar'])) {
    header("Location: index.php");
    exit();
}

$id_furgoneta = null;
if ($modo === 'editar') {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: index.php");
        exit();
    }
    $id_furgoneta = intval($_GET['id']);
}

$titulo_pagina = ($modo === 'nuevo') ? 'Nueva Furgoneta' : 'Editar Furgoneta';
$icono_titulo = ($modo === 'nuevo') ? 'fa-plus-circle' : 'fa-edit';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <title><?php echo $titulo_pagina; ?> - MDR</title>
</head>

<body>
    <!-- LEFT PANEL -->
    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
    </div>

    <!-- HEAD PANEL -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div>

    <!-- RIGHT PANEL -->
    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div>

    <!-- MAIN PANEL -->
    <div class="br-mainpanel">
        
        <!-- Breadcrumb -->
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../../index.php">Inicio</a>
                <a class="breadcrumb-item" href="index.php">Furgonetas</a>
                <span class="breadcrumb-item active"><?php echo $titulo_pagina; ?></span>
            </nav>
        </div>

        <!-- Título con botones -->
        <div class="br-pagetitle d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fas <?php echo $icono_titulo; ?> tx-50 lh-0"></i>
                <div class="d-inline-block align-middle">
                    <h4 class="mg-b-0"><?php echo $titulo_pagina; ?></h4>
                    <p class="mg-b-0 tx-gray-600">Complete los datos del vehículo</p>
                </div>
                <button type="button" class="btn btn-link p-0 ms-2" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                </a>
            </div>
        </div>

        <!-- Contenido del formulario -->
        <div class="br-pagebody">
            <form id="formFurgoneta" class="needs-validation" novalidate>
                <input type="hidden" name="id_furgoneta" id="id_furgoneta" value="">

                <!-- SECCIÓN 1: Información Básica -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 tx-bold">
                            <i class="bi bi-info-circle me-2"></i>Información Básica del Vehículo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Matrícula -->
                            <div class="col-12 col-md-4">
                                <label for="matricula_furgoneta" class="form-label">
                                    Matrícula: <span class="tx-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="matricula_furgoneta" 
                                       id="matricula_furgoneta" 
                                       maxlength="20" 
                                       placeholder="Ej: 1234-ABC" 
                                       required>
                                <div class="invalid-feedback">
                                    La matrícula es obligatoria
                                </div>
                            </div>

                            <!-- Marca -->
                            <div class="col-12 col-md-4">
                                <label for="marca_furgoneta" class="form-label">Marca:</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="marca_furgoneta" 
                                       id="marca_furgoneta" 
                                       maxlength="100" 
                                       placeholder="Ej: Renault, Mercedes, Ford">
                            </div>

                            <!-- Modelo -->
                            <div class="col-12 col-md-4">
                                <label for="modelo_furgoneta" class="form-label">Modelo:</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="modelo_furgoneta" 
                                       id="modelo_furgoneta" 
                                       maxlength="100" 
                                       placeholder="Ej: Master, Sprinter">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Año -->
                            <div class="col-12 col-md-4">
                                <label for="anio_furgoneta" class="form-label">Año de fabricación:</label>
                                <input type="number" 
                                       class="form-control" 
                                       name="anio_furgoneta" 
                                       id="anio_furgoneta" 
                                       min="1900" 
                                       max="2100" 
                                       placeholder="Ej: 2020">
                            </div>

                            <!-- Número de Bastidor -->
                            <div class="col-12 col-md-4">
                                <label for="numero_bastidor_furgoneta" class="form-label">Número de Bastidor (VIN):</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="numero_bastidor_furgoneta" 
                                       id="numero_bastidor_furgoneta" 
                                       maxlength="50" 
                                       placeholder="Ej: WF0PXXWPDP2A12345">
                            </div>

                            <!-- Km entre revisiones -->
                            <div class="col-12 col-md-4">
                                <label for="kilometros_entre_revisiones_furgoneta" class="form-label">
                                    Kilómetros entre revisiones:
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       name="kilometros_entre_revisiones_furgoneta" 
                                       id="kilometros_entre_revisiones_furgoneta" 
                                       value="10000"
                                       min="0" 
                                       placeholder="10000">
                                <small class="form-text text-muted">Kilómetros entre revisiones preventivas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: ITV y Seguros -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0 tx-bold">
                            <i class="bi bi-shield-check me-2"></i>ITV y Seguros
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Fecha próxima ITV -->
                            <div class="col-12 col-md-4">
                                <label for="fecha_proxima_itv_furgoneta" class="form-label">
                                    Próxima ITV:
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       name="fecha_proxima_itv_furgoneta" 
                                       id="fecha_proxima_itv_furgoneta">
                            </div>

                            <!-- Fecha vencimiento seguro -->
                            <div class="col-12 col-md-4">
                                <label for="fecha_vencimiento_seguro_furgoneta" class="form-label">
                                    Vencimiento seguro:
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       name="fecha_vencimiento_seguro_furgoneta" 
                                       id="fecha_vencimiento_seguro_furgoneta">
                            </div>

                            <!-- Compañía seguro -->
                            <div class="col-12 col-md-4">
                                <label for="compania_seguro_furgoneta" class="form-label">
                                    Compañía de seguro:
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="compania_seguro_furgoneta" 
                                       id="compania_seguro_furgoneta" 
                                       maxlength="255" 
                                       placeholder="Ej: Mapfre, AXA">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Número de póliza -->
                            <div class="col-12 col-md-4">
                                <label for="numero_poliza_seguro_furgoneta" class="form-label">
                                    Número de póliza:
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="numero_poliza_seguro_furgoneta" 
                                       id="numero_poliza_seguro_furgoneta" 
                                       maxlength="100" 
                                       placeholder="Ej: POL-123456">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: Capacidad y Características -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0 tx-bold">
                            <i class="bi bi-box me-2"></i>Capacidad y Características
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Capacidad carga kg -->
                            <div class="col-12 col-md-4">
                                <label for="capacidad_carga_kg_furgoneta" class="form-label">
                                    Capacidad de carga (kg):
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       name="capacidad_carga_kg_furgoneta" 
                                       id="capacidad_carga_kg_furgoneta" 
                                       step="0.01"
                                       min="0" 
                                       placeholder="Ej: 1500.00">
                            </div>

                            <!-- Capacidad carga m3 -->
                            <div class="col-12 col-md-4">
                                <label for="capacidad_carga_m3_furgoneta" class="form-label">
                                    Capacidad de carga (m³):
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       name="capacidad_carga_m3_furgoneta" 
                                       id="capacidad_carga_m3_furgoneta" 
                                       step="0.01"
                                       min="0" 
                                       placeholder="Ej: 15.00">
                            </div>

                            <!-- Tipo de combustible -->
                            <div class="col-12 col-md-4">
                                <label for="tipo_combustible_furgoneta" class="form-label">
                                    Tipo de combustible:
                                </label>
                                <select class="form-control" 
                                        name="tipo_combustible_furgoneta" 
                                        id="tipo_combustible_furgoneta">
                                    <option value="">Seleccionar...</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Gasolina">Gasolina</option>
                                    <option value="Eléctrico">Eléctrico</option>
                                    <option value="Híbrido">Híbrido</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <!-- Consumo medio -->
                            <div class="col-12 col-md-4">
                                <label for="consumo_medio_furgoneta" class="form-label">
                                    Consumo medio (L/100km):
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       name="consumo_medio_furgoneta" 
                                       id="consumo_medio_furgoneta" 
                                       step="0.01"
                                       min="0" 
                                       placeholder="Ej: 8.50">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 4: Mantenimiento -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 tx-bold">
                            <i class="bi bi-tools me-2"></i>Mantenimiento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Taller habitual -->
                            <div class="col-12 col-md-6">
                                <label for="taller_habitual_furgoneta" class="form-label">
                                    Taller habitual:
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="taller_habitual_furgoneta" 
                                       id="taller_habitual_furgoneta" 
                                       maxlength="255" 
                                       placeholder="Nombre del taller">
                            </div>

                            <!-- Teléfono taller -->
                            <div class="col-12 col-md-6">
                                <label for="telefono_taller_furgoneta" class="form-label">
                                    Teléfono del taller:
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       name="telefono_taller_furgoneta" 
                                       id="telefono_taller_furgoneta" 
                                       maxlength="50" 
                                       placeholder="Ej: +34 912 345 678">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 5: Estado y Observaciones -->
                <div class="card mb-4 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0 tx-bold">
                            <i class="bi bi-file-text me-2"></i>Estado y Observaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Estado -->
                            <div class="col-12 col-md-4">
                                <label for="estado_furgoneta" class="form-label">
                                    Estado del vehículo:
                                </label>
                                <select class="form-control" 
                                        name="estado_furgoneta" 
                                        id="estado_furgoneta">
                                    <option value="operativa">Operativa</option>
                                    <option value="taller">Taller</option>
                                    <option value="baja">Baja</option>
                                </select>
                            </div>

                            <!-- Switch Activo (solo en modo editar) -->
                            <?php if ($modo === 'editar'): ?>
                            <div class="col-12 col-md-4">
                                <label for="activo_furgoneta" class="form-label d-block">Estado del registro:</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           role="switch" 
                                           id="activo_furgoneta" 
                                           name="activo_furgoneta" 
                                           value="1" 
                                           checked>
                                    <label class="form-check-label" for="activo_furgoneta" id="estado-text">
                                        Furgoneta Activa
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="row mt-3">
                            <!-- Observaciones -->
                            <div class="col-12">
                                <label for="observaciones_furgoneta" class="form-label">
                                    Observaciones:
                                </label>
                                <textarea class="form-control" 
                                          name="observaciones_furgoneta" 
                                          id="observaciones_furgoneta" 
                                          rows="4" 
                                          placeholder="Observaciones generales sobre el vehículo..."></textarea>
                                <small class="form-text text-muted">
                                    Máximo 65,535 caracteres
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card">
                    <div class="card-body text-center">
                        <button type="button" 
                                name="action" 
                                id="btnSalvarFurgoneta" 
                                class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>Guardar Furgoneta
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php include_once('../../config/template/mainFooter.php') ?>
    </div><!-- br-mainpanel -->

    <!-- Scripts de plantilla -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <!-- Scripts de componentes -->
    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    
    <!-- Script de validación (si existe) -->
    <script src="../../public/js/form-validator.js"></script>

    <!-- Script específico del formulario -->
    <script type="text/javascript" src="formularioFurgoneta.js"></script>

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Furgonetas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-card-text me-2"></i>Matrícula del Vehículo</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del vehículo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser única en el sistema</li>
                                <li><i class="fas fa-check text-success me-2"></i>Formato estándar español (ej: 1234ABC)</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-truck me-2"></i>Identificación del Vehículo</h6>
                            <p><strong>Datos técnicos del vehículo.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-tag text-info me-2"></i>Marca: Fabricante del vehículo</li>
                                <li><i class="fas fa-truck text-info me-2"></i>Modelo: Versión específica</li>
                                <li><i class="fas fa-calendar text-info me-2"></i>Año: Año de fabricación</li>
                                <li><i class="fas fa-palette text-info me-2"></i>Color: Color exterior del vehículo</li>
                                <li><i class="fas fa-barcode text-info me-2"></i>Bastidor: Número VIN del chasis (único)</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-speedometer2 me-2"></i>Especificaciones Técnicas</h6>
                            <p><strong>Características del motor y dimensiones.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-cogs text-info me-2"></i>Tipo Motor: Diesel, Gasolina, Eléctrico, Híbrido</li>
                                <li><i class="fas fa-tachometer-alt text-info me-2"></i>Cilindrada: Capacidad del motor en cm³</li>
                                <li><i class="fas fa-horse text-info me-2"></i>Potencia: Potencia en CV o kW</li>
                                <li><i class="fas fa-weight-hanging text-info me-2"></i>Peso: Peso total del vehículo</li>
                                <li><i class="fas fa-cube text-info me-2"></i>Capacidad Carga: Carga útil máxima en kg</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-file-earmark-text me-2"></i>Documentación y Seguro</h6>
                            <p><strong>Información legal y de seguros.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-calendar-check text-warning me-2"></i>Fecha Próxima ITV: Vencimiento de la inspección técnica</li>
                                <li><i class="fas fa-shield-alt text-success me-2"></i>Fecha Vencimiento Seguro: Expiración de la póliza</li>
                                <li><i class="fas fa-file-contract text-info me-2"></i>Número de Póliza: Identificador del seguro</li>
                                <li><i class="fas fa-building text-info me-2"></i>Compañía Aseguradora: Nombre de la aseguradora</li>
                            </ul>
                            <div class="alert alert-warning py-2 mt-2">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Sistema de Alertas:</strong>
                                    El sistema mostrará alertas visuales cuando las fechas estén próximas a vencer:
                                </small>
                                <ul class="list-unstyled small mb-0 mt-2 ms-3">
                                    <li><span class="badge bg-danger me-2">Rojo</span> Fecha vencida</li>
                                    <li><span class="badge bg-warning text-dark me-2">Amarillo</span> Vence en menos de 30 días</li>
                                    <li><span class="badge bg-success me-2">Verde</span> Vigente (más de 30 días)</li>
                                </ul>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-speedometer me-2"></i>Control de Kilometraje</h6>
                            <p><strong>Registro del uso del vehículo.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-road text-info me-2"></i>Kilometraje Actual: Odómetro del vehículo</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Se actualiza en cada registro de uso</li>
                                <li><i class="fas fa-chart-line text-info me-2"></i>Permite calcular consumos y planificar mantenimientos</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-traffic-cone me-2"></i>Estado del Vehículo</h6>
                            <p><strong>Disponibilidad operativa.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><span class="badge bg-success me-2">OPERATIVA</span> Disponible para asignación</li>
                                <li><span class="badge bg-warning text-dark me-2">EN TALLER</span> En mantenimiento o reparación</li>
                                <li><span class="badge bg-danger me-2">BAJA</span> Fuera de servicio permanente</li>
                            </ul>
                            <div class="alert alert-info py-2 mt-2">
                                <small>
                                    <i class="bi bi-info-circle me-1"></i>
                                    El estado determina la disponibilidad de la furgoneta para asignaciones de trabajo.
                                </small>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h6>
                            <p><strong>Notas adicionales sobre el vehículo.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-clipboard text-secondary me-2"></i>Información sobre equipamiento especial</li>
                                <li><i class="fas fa-wrench text-secondary me-2"></i>Historial de reparaciones importantes</li>
                                <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Problemas conocidos o restricciones de uso</li>
                                <li><i class="fas fa-star text-warning me-2"></i>Cualquier detalle relevante para su operación</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-2"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script adicional: Colapsar sidebar -->
    <script>
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>
</body>

</html>
