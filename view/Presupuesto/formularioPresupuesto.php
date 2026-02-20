<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'mantenimientos'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">

<!-- ---------------------- -->
<!--      MainHead.php      -->
<!-- ---------------------- -->

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<!-- ---------------------- -->
<!--  END MainHead.php      -->
<!-- ---------------------- -->

<body>

    <!-- ########## START: LEFT PANEL ########## -->

    <!-- ---------------------- -->
    <!--      MainLogo.php      -->
    <!-- ---------------------- -->

    <?php require_once('../../config/template/mainLogo.php') ?>

    <!-- ---------------------- -->
    <!--   END MainLogo.php     -->
    <!-- ---------------------- -->

    <div class="br-sideleft sideleft-scrollbar">
        <!-- ---------------------- -->
        <!--   MainSideBar.php      -->
        <!-- ---------------------- -->
        <?php require_once('../../config/template/mainSidebar.php') ?>

        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <!-- ---------------------- -->
        <!-- END MainSideBar.php    -->
        <!-- ---------------------- -->
        <br>
    </div><!-- br-sideleft -->
    <!-- ########## END: LEFT PANEL ########## -->


    <!-- ########## START: HEAD PANEL ########## -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div><!-- br-header -->
    <!-- ########## END: HEAD PANEL ########## -->


    <!-- ########## START: RIGHT PANEL ########## -->
    <div class="br-sideright">
        <!-- ---------------------- -->
        <!--   mainRightPanel.php      -->
        <!-- ---------------------- -->
        <?php include_once('../../config/template/mainRightPanel.php') ?>
        <!-- ------------------------- -->
        <!-- END MainRightPanel.php    -->
        <!-- ------------------------- -->

    </div><!-- br-sideright -->
    <!-- ########## END: RIGHT PANEL ########## -->

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <a class="breadcrumb-item" href="index.php">Mantenimiento Presupuestos</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Presupuesto</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Presupuesto</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <!-- Botón de regreso -->
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Presupuesto -->
                <form id="formPresupuesto">
                    <!-- Campo oculto para ID del presupuesto -->
                    <input type="hidden" name="id_presupuesto" id="id_presupuesto">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-file-invoice me-2"></i>Información Básica del Presupuesto
                            </h5>
                        </div>
                        <div class="card-body">
                            
                          <!-- Información de la Empresa -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-8">
                                    <label for="nombre_empresa_info" class="form-label">
                                        Empresa emisora del presupuesto:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Empresa ficticia que emite este presupuesto. Solo informativo."></i>
                                    </label>
                                    <input type="text" class="form-control bg-light" id="nombre_empresa_info" placeholder="Cargando..." readonly>
                                    <small class="form-text text-muted">Empresa configurada para emisión de presupuestos</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="dias_validez_info" class="form-label">
                                        Días de validez configurados:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Días de validez por defecto configurados en la empresa. Solo informativo."></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light text-center" id="dias_validez_info" placeholder="--" readonly>
                                        <span class="input-group-text bg-light">días</span>
                                    </div>
                                    <small class="form-text text-muted">Configurado en la empresa</small>
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label for="numero_presupuesto" class="form-label">Número: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="numero_presupuesto" id="numero_presupuesto" placeholder="Se generará automáticamente" readonly>
                                    <small class="form-text text-muted">Se asigna automáticamente al guardar</small>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="fecha_presupuesto" class="form-label">Fecha: <span class="tx-danger">*</span></label>
                                    <input type="date" class="form-control" name="fecha_presupuesto" id="fecha_presupuesto" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una fecha válida</div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="fecha_validez_presupuesto" class="form-label">
                                        Validez:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Se calcula automáticamente según los días de validez configurados en la empresa. Puede modificarse."></i>
                                    </label>
                                    <input type="date" class="form-control" name="fecha_validez_presupuesto" id="fecha_validez_presupuesto">
                                    <div class="invalid-feedback small-invalid-feedback">Debe ser mayor o igual a la fecha del presupuesto</div>
                                    <small class="form-text text-muted">Fecha hasta la que es válido el presupuesto (modificable)</small>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_presupuesto" id="activo_presupuesto" checked disabled>
                                        <label class="form-check-label" for="activo_presupuesto">
                                            <span id="estado-text">Activo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                          
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_cliente" class="form-label">
                                        Cliente: <span class="tx-danger">*</span>
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Solo se muestran los clientes marcados como activos en la tabla de clientes."></i>
                                    </label>
                                    <select class="form-control" name="id_cliente" id="id_cliente" required>
                                        <option value="">Seleccione un cliente</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Debe seleccionar un cliente</div>
                                    <small class="form-text text-muted">Cliente al que se emite el presupuesto</small>
                                    
                                    <!-- Información de dirección del cliente -->
                                    <div id="info-direccion-cliente" style="display: none;" class="mt-2">
                                        <div class="alert alert-light border-start border-primary border-4 mb-0 py-2" role="alert">
                                            <h6 class="alert-heading tx-11 tx-semibold mb-1">
                                                <i class="bi bi-geo-alt-fill me-1"></i>Dirección del Cliente
                                            </h6>
                                            <div class="tx-10">
                                                <div><strong>NIF:</strong> <span id="nif_cliente_info">-</span></div>
                                                <div><strong>Dirección:</strong> <span id="direccion_cliente_info">-</span></div>
                                                <div><strong>CP:</strong> <span id="cp_cliente_info">-</span> | <strong>Población:</strong> <span id="poblacion_cliente_info">-</span></div>
                                                <div><strong>Provincia:</strong> <span id="provincia_cliente_info">-</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- *** PUNTO 17: Alerta Cliente Exento de IVA *** -->
                                    <div id="alerta_cliente_exento_iva" style="display: none;" class="mt-2">
                                        <div class="alert alert-warning border-start border-warning border-4 mb-0 py-2" role="alert">
                                            <h6 class="alert-heading tx-11 tx-semibold mb-1">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Cliente Exento de IVA
                                            </h6>
                                            <div class="tx-10">
                                                Operaciones intracomunitarias - El IVA se aplicará al 0%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="id_contacto_cliente" class="form-label">
                                        Contacto del cliente:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Solo se muestran los contactos marcados como activos en la tabla de contactos."></i>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-control" name="id_contacto_cliente" id="id_contacto_cliente">
                                            <option value="">Seleccione primero un cliente</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary btn-disabled" id="btnNuevoContacto" data-enabled="false" title="Agregar nuevo contacto">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-disabled" id="btnEditarContacto" data-enabled="false" title="Editar contacto">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">Contacto específico del cliente (opcional)</small>
                                    
                                    <!-- Información del contacto del cliente -->
                                    <div id="info-contacto-cliente" style="display: none;" class="mt-2">
                                        <div class="alert alert-light border-start border-info border-4 mb-0 py-2" role="alert">
                                            <h6 class="alert-heading tx-11 tx-semibold mb-1">
                                                <i class="bi bi-person-fill me-1"></i>Datos del Contacto
                                            </h6>
                                            <div class="tx-10">
                                                <div><strong>Cargo:</strong> <span id="cargo_contacto_info">-</span></div>
                                                <div><strong>Departamento:</strong> <span id="departamento_contacto_info">-</span></div>
                                                <div><strong>Teléfono:</strong> <span id="telefono_contacto_info">-</span> | <strong>Móvil:</strong> <span id="movil_contacto_info">-</span></div>
                                                <div><strong>Email:</strong> <span id="email_contacto_info">-</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-light border rounded">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-tag me-2 text-secondary"></i>
                                            Estado presupuesto:
                                            <small class="text-muted fw-normal ms-1">(gestionado automáticamente)</small>
                                        </label>
                                        <div class="mt-1">
                                            <span id="badge_estado_ppto"
                                                  class="badge fs-6 p-2"
                                                  style="background-color:#0000ff; color:#fff;">
                                                BORRADOR
                                            </span>
                                        </div>
                                        <input type="hidden" name="id_estado_ppto" id="id_estado_ppto" value="1">
                                        <small class="form-text text-muted mt-1 d-block">
                                            El estado se actualiza al cambiar el estado de una versión.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="id_forma_pago" class="form-label">Forma de pago:</label>
                                    <select class="form-control" name="id_forma_pago" id="id_forma_pago">
                                        <option value="">Seleccione una forma de pago</option>
                                    </select>
                                    <small class="form-text text-muted">Condiciones de pago del presupuesto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_metodo" class="form-label">
                                        Método de envío del presupuesto:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Método por el cual el cliente desea recibir el presupuesto"></i>
                                    </label>
                                    <select class="form-control" name="id_metodo" id="id_metodo">
                                        <option value="">Seleccione un método</option>
                                    </select>
                                    <small class="form-text text-muted">Cómo se enviará el presupuesto al cliente (Email, WhatsApp, etc.)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos del Evento -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-calendar-event me-2"></i>Datos del Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_evento_presupuesto" class="form-label">Nombre del evento:</label>
                                    <input type="text" class="form-control" name="nombre_evento_presupuesto" id="nombre_evento_presupuesto" maxlength="255" placeholder="Ej: Concierto Anual 2025">
                                    <small class="form-text text-muted">Nombre del evento o proyecto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="direccion_evento_presupuesto" class="form-label">Dirección del evento:</label>
                                    <input type="text" class="form-control" name="direccion_evento_presupuesto" id="direccion_evento_presupuesto" maxlength="100" placeholder="Ej: Calle Principal 123">
                                    <small class="form-text text-muted">Dirección donde se realizará el evento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="poblacion_evento_presupuesto" class="form-label">Población:</label>
                                    <input type="text" class="form-control" name="poblacion_evento_presupuesto" id="poblacion_evento_presupuesto" maxlength="80" placeholder="Ej: Badajoz">
                                    <small class="form-text text-muted">Ciudad o población del evento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="cp_evento_presupuesto" class="form-label">Código postal:</label>
                                    <input type="text" class="form-control" name="cp_evento_presupuesto" id="cp_evento_presupuesto" maxlength="10" placeholder="Ej: 06006">
                                    <small class="form-text text-muted">CP del evento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="provincia_evento_presupuesto" class="form-label">Provincia:</label>
                                    <input type="text" class="form-control" name="provincia_evento_presupuesto" id="provincia_evento_presupuesto" maxlength="80" placeholder="Ej: Badajoz">
                                    <small class="form-text text-muted">Provincia del evento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-info bg-opacity-10 border border-info rounded">
                                        <label for="fecha_inicio_evento_presupuesto" class="form-label fw-bold">
                                            <i class="fas fa-calendar-day text-info me-2"></i>
                                            Fecha inicio evento:
                                            <span class="badge bg-info ms-2">IMPORTANTE</span>
                                        </label>
                                        <input type="date" class="form-control" name="fecha_inicio_evento_presupuesto" id="fecha_inicio_evento_presupuesto">
                                        <small class="form-text text-muted">
                                            <strong>Fecha de inicio del evento/servicio - Clave para cálculos automáticos</strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-info bg-opacity-10 border border-info rounded">
                                        <label for="fecha_fin_evento_presupuesto" class="form-label fw-bold">
                                            <i class="fas fa-calendar-check text-info me-2"></i>
                                            Fecha fin evento:
                                            <span class="badge bg-info ms-2">IMPORTANTE</span>
                                        </label>
                                        <input type="date" class="form-control" name="fecha_fin_evento_presupuesto" id="fecha_fin_evento_presupuesto">
                                        <div class="invalid-feedback small-invalid-feedback">Debe ser mayor o igual a la fecha de inicio del evento</div>
                                        <small class="form-text text-muted">
                                            <strong>Fecha de finalización del evento/servicio - Define la duración</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="numero_pedido_cliente_presupuesto" class="form-label">Número de pedido del cliente:</label>
                                    <input type="text" class="form-control" name="numero_pedido_cliente_presupuesto" id="numero_pedido_cliente_presupuesto" maxlength="80" placeholder="Ej: PED-2025-001">
                                    <small class="form-text text-muted">Número de pedido proporcionado por el cliente (si aplica)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-2 rounded">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="aplicar_coeficientes_presupuesto" id="aplicar_coeficientes_presupuesto" checked>
                                            <label class="form-check-label fw-bold" for="aplicar_coeficientes_presupuesto">
                                                <i class="fas fa-calculator text-warning me-2"></i>
                                                Aplicar coeficientes reductores por días
                                                <i class="bi bi-info-circle text-info ms-2" data-bs-toggle="tooltip" title="Si está activado, se aplicarán los coeficientes reductores según la duración del evento. Si está desactivado, se usará el precio base sin reducciones."></i>
                                            </label>
                                            <small class="form-text text-muted d-block mt-2">
                                                <strong>Activado:</strong> Aplica descuentos automáticos según los días de alquiler.<br>
                                                <strong>Desactivado:</strong> Usa el precio base sin aplicar coeficientes.
                                            </small>
                                            <div id="nota_coeficientes" class="alert alert-info mt-3 mb-0 py-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Nota:</strong> Se tendrá en cuenta lo reflejado en artículos y familias.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="p-3 bg-success bg-opacity-10 border border-success border-2 rounded">
                                        <label for="descuento_presupuesto" class="form-label fw-bold">
                                            <i class="fas fa-percent text-success me-2"></i>
                                            Descuento del presupuesto (%)
                                            <i class="bi bi-info-circle text-info ms-2" data-bs-toggle="tooltip" title="Porcentaje de descuento aplicado a este presupuesto. Se hereda del descuento habitual del cliente pero puede modificarse."></i>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="descuento_presupuesto" id="descuento_presupuesto" 
                                                   min="0" max="100" step="0.01" value="0.00" placeholder="0.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            <span id="info_descuento_cliente" class="d-none">
                                                <i class="fas fa-user text-primary"></i>
                                                <strong>Descuento habitual del cliente:</strong> <span id="valor_descuento_cliente">0.00</span>%
                                            </span>
                                            <span id="mensaje_descuento" class="text-muted">Seleccione un cliente para ver su descuento habitual.</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-comment-alt me-2"></i>Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_cabecera_presupuesto" class="form-label">Observaciones de cabecera (Español):</label>
                                    <textarea class="form-control" name="observaciones_cabecera_presupuesto" id="observaciones_cabecera_presupuesto" rows="3" placeholder="Observaciones que aparecerán al inicio del presupuesto..."></textarea>
                                    <small class="form-text text-muted">Observaciones iniciales del presupuesto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_cabecera_ingles_presupuesto" class="form-label">
                                        Observaciones de cabecera (Inglés):
                                        <i class="bi bi-translate text-info" data-bs-toggle="tooltip" title="Versión en inglés de las observaciones de cabecera"></i>
                                    </label>
                                    <textarea class="form-control" name="observaciones_cabecera_ingles_presupuesto" id="observaciones_cabecera_ingles_presupuesto" rows="3" placeholder="Header observations in English..."></textarea>
                                    <small class="form-text text-muted">Observaciones iniciales del presupuesto en inglés</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_pie_presupuesto" class="form-label">Observaciones de pie (Español):</label>
                                    <textarea class="form-control" name="observaciones_pie_presupuesto" id="observaciones_pie_presupuesto" rows="3" placeholder="Observaciones que aparecerán al final del presupuesto..."></textarea>
                                    <small class="form-text text-muted">Observaciones específicas adicionales al pie</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_pie_ingles_presupuesto" class="form-label">
                                        Observaciones de pie (Inglés):
                                        <i class="bi bi-translate text-info" data-bs-toggle="tooltip" title="Versión en inglés de las observaciones de pie"></i>
                                    </label>
                                    <textarea class="form-control" name="observaciones_pie_ingles_presupuesto" id="observaciones_pie_ingles_presupuesto" rows="3" placeholder="Footer observations in English..."></textarea>
                                    <small class="form-text text-muted">Observaciones específicas adicionales al pie en inglés</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="destacar_observaciones_pie_presupuesto" id="destacar_observaciones_pie_presupuesto" checked>
                                        <label class="form-check-label" for="destacar_observaciones_pie_presupuesto">
                                            <i class="fas fa-star text-warning me-2"></i>Destacar observaciones de pie en el PDF
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si está activo, las observaciones de pie aparecerán destacadas con líneas decorativas y centradas. 
                                        Si está desactivado, se mostrarán integradas con las demás observaciones y alineadas a la izquierda.
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_internas_presupuesto" class="form-label">Observaciones internas:</label>
                                    <textarea class="form-control" name="observaciones_internas_presupuesto" id="observaciones_internas_presupuesto" rows="3" placeholder="Notas internas (no se imprimirán en el PDF)..."></textarea>
                                    <small class="form-text text-muted">Notas internas, no se imprimen en el PDF</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_obs_familias_presupuesto" id="mostrar_obs_familias_presupuesto" checked>
                                        <label class="form-check-label" for="mostrar_obs_familias_presupuesto">
                                            Mostrar observaciones de familias
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Si está activo, muestra observaciones de las familias usadas</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_obs_articulos_presupuesto" id="mostrar_obs_articulos_presupuesto" checked>
                                        <label class="form-check-label" for="mostrar_obs_articulos_presupuesto">
                                            Mostrar observaciones de artículos
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Si está activo, muestra observaciones de los artículos usados</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarPresupuesto" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Presupuesto
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>

                </form>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Presupuestos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-invoice me-2"></i>Información Básica</h6>
                            <p><strong>Campos obligatorios:</strong> Número de presupuesto, fecha, cliente y estado.</p>
                            
                            <!-- Destacado importante -->
                            <div class="alert alert-warning border-start border-warning border-4 mb-3" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">
                                            <strong>Estado del Presupuesto</strong>
                                            <span class="badge bg-warning text-dark ms-2">MUY IMPORTANTE</span>
                                        </h6>
                                        <p class="mb-0">El estado define el ciclo de vida del presupuesto y es fundamental para el control del flujo de trabajo.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Número de presupuesto: único en el sistema</li>
                                <li><i class="fas fa-check text-success me-2"></i>Fecha: fecha de emisión del presupuesto</li>
                                <li><i class="fas fa-check text-success me-2"></i>Cliente: obligatorio seleccionar uno</li>
                                <li><i class="fas fa-info text-info me-2"></i>Contacto del cliente: se carga automáticamente al seleccionar el cliente (opcional)</li>
                                <li><i class="fas fa-credit-card text-info me-2"></i>Forma de pago: se carga automáticamente la forma habitual del cliente (puede modificarse)</li>
                                <li><i class="fas fa-paper-plane text-info me-2"></i>Método de envío: indica cómo el cliente desea recibir el presupuesto (Email, WhatsApp, etc.)</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-calendar-event me-2"></i>Datos del Evento</h6>
                            <p><strong>Información del evento.</strong> Campos opcionales para detallar el evento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Nombre y lugar del evento son opcionales</li>
                                <li><i class="fas fa-info text-info me-2"></i>Las fechas de inicio y fin definen la duración del evento</li>
                                <li><i class="fas fa-info text-info me-2"></i>Número de pedido del cliente es referencia externa</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-comment-alt me-2"></i>Observaciones</h6>
                            <p><strong>Notas y comentarios.</strong> Información adicional del presupuesto.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones de cabecera (Español): aparecen al inicio del PDF</li>
                                <li><i class="fas fa-globe text-info me-2"></i>Observaciones de cabecera (Inglés): versión en inglés para clientes internacionales</li>
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones de pie (Español): aparecen al final del PDF</li>
                                <li><i class="fas fa-globe text-info me-2"></i>Observaciones de pie (Inglés): versión en inglés para clientes internacionales</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Observaciones internas: no se imprimen, solo para uso interno</li>
                                <li><i class="fas fa-check text-success me-2"></i>Los switches controlan la visualización de observaciones de familias y artículos</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Contacto Rápido -->
    <?php include_once('contacto_presupuesto.php'); ?>

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    
    <style>
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    
    <script type="text/javascript" src="formularioPresupuesto.js"></script>

    <!-- Botones flotantes para navegación -->
    <!-- Botón para ir al inicio del formulario -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 140px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al inicio del formulario">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Botón para ir al final del formulario -->
    <button id="scrollToBottom" class="btn btn-primary" style="position: fixed; bottom: 80px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al final del formulario">
        <i class="fas fa-arrow-down"></i>
    </button>

    <!-- Script para botones flotantes de navegación -->
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar botones según scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#scrollToTop').fadeIn();
                    $('#scrollToBottom').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                    $('#scrollToBottom').fadeOut();
                }
            });

            // Hacer scroll al inicio del formulario
            $('#scrollToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
                return false;
            });

            // Hacer scroll al final del formulario
            $('#scrollToBottom').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 500);
                return false;
            });
        });
    </script>

</body>

</html>
