<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'usuarios'; ?>
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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Empresas</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Empresa</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Empresa</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaEmpresas" title="Ayuda sobre el formulario">
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
                
                <!-- Formulario de Empresa -->
                <form id="formEmpresa">
                    <!-- Campo oculto para ID de la empresa -->
                    <input type="hidden" name="id_empresa" id="id_empresa">

                    <!-- SECCIÓN: Identificación Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-building me-2"></i>Identificación Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="codigo_empresa" class="form-label">Código: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_empresa" id="codigo_empresa" maxlength="20" placeholder="Ej: EMP001, MDR01" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo</small>
                                </div>
                                <div class="col-12 col-md-8">
                                    <label for="nombre_empresa" class="form-label">Nombre de la Empresa: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" maxlength="255" placeholder="Razón social completa" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese el nombre de la empresa</div>
                                    <small class="form-text text-muted">Razón social completa</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_comercial_empresa" class="form-label">Nombre Comercial:</label>
                                    <input type="text" class="form-control" name="nombre_comercial_empresa" id="nombre_comercial_empresa" maxlength="255" placeholder="Nombre comercial si difiere de la razón social">
                                    <small class="form-text text-muted">Nombre comercial (opcional)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Tipo de Empresa -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-bookmark-check me-2"></i>Tipo de Empresa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Importante:</strong> Solo puede existir UNA empresa ficticia principal en el sistema.
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ficticia_empresa" name="ficticia_empresa" value="1">
                                        <label class="form-check-label" for="ficticia_empresa">
                                            Empresa Ficticia (solo para presupuestos)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Empresas ficticias NO facturan</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="empresa_ficticia_principal" name="empresa_ficticia_principal" value="1">
                                        <label class="form-check-label" for="empresa_ficticia_principal">
                                            Empresa Ficticia Principal
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Solo UNA empresa principal en el sistema</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos Fiscales -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-file-earmark-text me-2"></i>Datos Fiscales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nif_empresa" class="form-label">NIF: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nif_empresa" id="nif_empresa" maxlength="20" placeholder="Ej: B12345678" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un NIF válido</div>
                                    <small class="form-text text-muted">Número de identificación fiscal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="pais_fiscal_empresa" class="form-label">País:</label>
                                    <input type="text" class="form-control" name="pais_fiscal_empresa" id="pais_fiscal_empresa" maxlength="100" placeholder="España" value="España">
                                    <small class="form-text text-muted">País de domicilio fiscal</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="direccion_fiscal_empresa" class="form-label">Dirección Fiscal:</label>
                                    <input type="text" class="form-control" name="direccion_fiscal_empresa" id="direccion_fiscal_empresa" maxlength="255" placeholder="Dirección completa">
                                    <small class="form-text text-muted">Domicilio fiscal completo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="cp_fiscal_empresa" class="form-label">Código Postal:</label>
                                    <input type="text" class="form-control" name="cp_fiscal_empresa" id="cp_fiscal_empresa" maxlength="10" placeholder="28001">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="poblacion_fiscal_empresa" class="form-label">Población:</label>
                                    <input type="text" class="form-control" name="poblacion_fiscal_empresa" id="poblacion_fiscal_empresa" maxlength="100" placeholder="Madrid">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="provincia_fiscal_empresa" class="form-label">Provincia:</label>
                                    <input type="text" class="form-control" name="provincia_fiscal_empresa" id="provincia_fiscal_empresa" maxlength="100" placeholder="Madrid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Información de Contacto -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-telephone me-2"></i>Información de Contacto
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="telefono_empresa" class="form-label">Teléfono:</label>
                                    <input type="text" class="form-control" name="telefono_empresa" id="telefono_empresa" maxlength="20" placeholder="912345678">
                                    <small class="form-text text-muted">Teléfono principal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="movil_empresa" class="form-label">Móvil:</label>
                                    <input type="text" class="form-control" name="movil_empresa" id="movil_empresa" maxlength="20" placeholder="600123456">
                                    <small class="form-text text-muted">Teléfono móvil</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="email_empresa" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email_empresa" id="email_empresa" maxlength="100" placeholder="info@empresa.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    <small class="form-text text-muted">Email principal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="email_facturacion_empresa" class="form-label">Email Facturación:</label>
                                    <input type="email" class="form-control" name="email_facturacion_empresa" id="email_facturacion_empresa" maxlength="100" placeholder="facturacion@empresa.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    <small class="form-text text-muted">Email para facturación</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="web_empresa" class="form-label">Sitio Web:</label>
                                    <input type="url" class="form-control" name="web_empresa" id="web_empresa" maxlength="255" placeholder="https://www.empresa.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una URL válida</div>
                                    <small class="form-text text-muted">Página web de la empresa</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos Bancarios -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-bank me-2"></i>Datos Bancarios
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="iban_empresa" class="form-label">IBAN:</label>
                                    <input type="text" class="form-control" name="iban_empresa" id="iban_empresa" maxlength="34" placeholder="ES91 2100 0418 4502 0005 1332">
                                    <small class="form-text text-muted">Código IBAN</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="swift_empresa" class="form-label">SWIFT/BIC:</label>
                                    <input type="text" class="form-control" name="swift_empresa" id="swift_empresa" maxlength="11" placeholder="CAIXESBBXXX">
                                    <small class="form-text text-muted">Código SWIFT</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="banco_empresa" class="form-label">Banco:</label>
                                    <input type="text" class="form-control" name="banco_empresa" id="banco_empresa" maxlength="100" placeholder="CaixaBank">
                                    <small class="form-text text-muted">Nombre del banco</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Series de Numeración -->
                    <div class="card mb-4 border-dark">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-list-ol me-2"></i>Series de Numeración
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Formato: PRE, FAC, ABO (máximo 10 caracteres)
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="serie_presupuesto_empresa" class="form-label">Serie Presupuesto:</label>
                                    <input type="text" class="form-control" name="serie_presupuesto_empresa" id="serie_presupuesto_empresa" maxlength="10" placeholder="PRE" value="P">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="numero_actual_presupuesto_empresa" class="form-label">Número Actual:</label>
                                    <input type="number" class="form-control" name="numero_actual_presupuesto_empresa" id="numero_actual_presupuesto_empresa" value="0" min="0">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="dias_validez_presupuesto_empresa" class="form-label">Días Validez Presupuesto:</label>
                                    <input type="number" class="form-control" name="dias_validez_presupuesto_empresa" id="dias_validez_presupuesto_empresa" value="30" min="1" max="365">
                                    <small class="form-text text-muted">Días de validez por defecto para presupuestos (1-365)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="serie_factura_empresa" class="form-label">Serie Factura:</label>
                                    <input type="text" class="form-control" name="serie_factura_empresa" id="serie_factura_empresa" maxlength="10" placeholder="FAC" value="F">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="numero_actual_factura_empresa" class="form-label">Número Actual:</label>
                                    <input type="number" class="form-control" name="numero_actual_factura_empresa" id="numero_actual_factura_empresa" value="0" min="0">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="serie_abono_empresa" class="form-label">Serie Abono:</label>
                                    <input type="text" class="form-control" name="serie_abono_empresa" id="serie_abono_empresa" maxlength="10" placeholder="ABO" value="R">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="numero_actual_abono_empresa" class="form-label">Número Actual:</label>
                                    <input type="number" class="form-control" name="numero_actual_abono_empresa" id="numero_actual_abono_empresa" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: VeriFactu (Opcional - puede ocultarse con collapse) -->
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-shield-check me-2"></i>Datos VeriFactu (Solo empresas reales)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-octagon me-2"></i>
                                <strong>Atención:</strong> VeriFactu SOLO para empresas REALES que facturen.
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="verifactu_activo_empresa" name="verifactu_activo_empresa" value="1">
                                        <label class="form-check-label" for="verifactu_activo_empresa">
                                            VeriFactu Activo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_sistema_empresa" class="form-label">Sistema VeriFactu:</label>
                                    <select class="form-select" id="verifactu_sistema_empresa" name="verifactu_sistema_empresa">
                                        <option value="online" selected>Online</option>
                                        <option value="offline">Offline</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_nif_empresa" class="form-label">NIF VeriFactu:</label>
                                    <input type="text" class="form-control" name="verifactu_nif_empresa" id="verifactu_nif_empresa" maxlength="20">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_nombre_empresa" class="form-label">Nombre VeriFactu:</label>
                                    <input type="text" class="form-control" name="verifactu_nombre_empresa" id="verifactu_nombre_empresa" maxlength="255">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_nombre_comercial_empresa" class="form-label">Nombre Comercial VeriFactu:</label>
                                    <input type="text" class="form-control" name="verifactu_nombre_comercial_empresa" id="verifactu_nombre_comercial_empresa" maxlength="255">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_id_software_empresa" class="form-label">ID Software:</label>
                                    <input type="text" class="form-control" name="verifactu_id_software_empresa" id="verifactu_id_software_empresa" maxlength="100">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_nombre_software_empresa" class="form-label">Nombre Software:</label>
                                    <input type="text" class="form-control" name="verifactu_nombre_software_empresa" id="verifactu_nombre_software_empresa" maxlength="100">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_version_software_empresa" class="form-label">Versión Software:</label>
                                    <input type="text" class="form-control" name="verifactu_version_software_empresa" id="verifactu_version_software_empresa" maxlength="50">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="verifactu_numero_instalacion_empresa" class="form-label">Número de Instalación:</label>
                                    <input type="text" class="form-control" name="verifactu_numero_instalacion_empresa" id="verifactu_numero_instalacion_empresa" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Logotipos y Textos Legales -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-image me-2"></i>Logotipos y Textos Legales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="logotipo_empresa" class="form-label">Ruta Logotipo Principal:</label>
                                    <input type="text" class="form-control" name="logotipo_empresa" id="logotipo_empresa" maxlength="255" placeholder="/images/logos/empresa.png">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="logotipo_pie_empresa" class="form-label">Ruta Logotipo Secundario:</label>
                                    <input type="text" class="form-control" name="logotipo_pie_empresa" id="logotipo_pie_empresa" maxlength="255" placeholder="/images/logos/marca.png">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="texto_legal_factura_empresa" class="form-label">Texto Legal:</label>
                                    <textarea class="form-control" name="texto_legal_factura_empresa" id="texto_legal_factura_empresa" rows="3"></textarea>
                                    <small class="form-text text-muted">Texto legal para facturas</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="texto_pie_presupuesto_empresa" class="form-label">Texto Pie Presupuesto:</label>
                                    <textarea class="form-control" name="texto_pie_presupuesto_empresa" id="texto_pie_presupuesto_empresa" rows="2"></textarea>
                                    <small class="form-text text-muted">Texto pie para presupuestos</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="texto_pie_factura_empresa" class="form-label">Texto Pie Factura:</label>
                                    <textarea class="form-control" name="texto_pie_factura_empresa" id="texto_pie_factura_empresa" rows="2"></textarea>
                                    <small class="form-text text-muted">Texto pie para facturas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- J. OBSERVACIONES POR DEFECTO PRESUPUESTOS -->
                    <!-- ========================================== -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="bi bi-file-earmark-text me-2"></i>Observaciones por Defecto para Nuevos Presupuestos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Nota:</strong> Estos textos se cargarán automáticamente en las observaciones de cabecera
                                cuando se cree un <strong>nuevo presupuesto</strong>. Los presupuestos existentes no se modificarán.
                            </div>

                            <div class="row">
                                <!-- Observaciones Cabecera - Español -->
                                <div class="col-md-6 mb-3">
                                    <label for="observaciones_cabecera_presupuesto_empresa" class="form-label">
                                        <strong>Observaciones de Cabecera (Español)</strong>
                                        <i class="bi bi-flag-fill text-danger ms-1" title="Español"></i>
                                    </label>
                                    <textarea
                                        class="form-control"
                                        name="observaciones_cabecera_presupuesto_empresa"
                                        id="observaciones_cabecera_presupuesto_empresa"
                                        rows="4"
                                        placeholder="Ej: Montaje de material audiovisual en regimen de alquiler"></textarea>
                                    <small class="text-muted">
                                        Texto personalizado que aparece en la cabecera del presupuesto (sin fechas)
                                    </small>
                                </div>

                                <!-- Observaciones Cabecera - Inglés -->
                                <div class="col-md-6 mb-3">
                                    <label for="observaciones_cabecera_ingles_presupuesto_empresa" class="form-label">
                                        <strong>Observaciones de Cabecera (Inglés)</strong>
                                        <i class="bi bi-translate text-info ms-1" title="English"></i>
                                    </label>
                                    <textarea
                                        class="form-control"
                                        name="observaciones_cabecera_ingles_presupuesto_empresa"
                                        id="observaciones_cabecera_ingles_presupuesto_empresa"
                                        rows="4"
                                        placeholder="Ex: Audiovisual equipment assembly for rental"></textarea>
                                    <small class="text-muted">
                                        Versión en inglés de las observaciones de cabecera
                                    </small>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>Importante:</strong> Estos valores son plantillas que se copiarán a nuevos presupuestos.
                                Una vez creado el presupuesto, las modificaciones aquí no afectarán presupuestos existentes.
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Configuración de PDF -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-file-pdf me-2"></i>Configuración de PDF de Presupuestos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="mostrar_subtotales_fecha_presupuesto_empresa"
                                       name="mostrar_subtotales_fecha_presupuesto_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="mostrar_subtotales_fecha_presupuesto_empresa">
                                    <strong>Mostrar subtotales por fecha en PDF</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Al desmarcar esta opción, se ocultarán las líneas de "Subtotal Fecha XX/XX/XXXX" en los PDF de presupuestos.
                            </small>

                            <div class="mb-3">
                                <label for="cabecera_firma_presupuesto_empresa" class="form-label">
                                    <strong>Cabecera de Firma en PDF</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cabecera_firma_presupuesto_empresa"
                                       name="cabecera_firma_presupuesto_empresa"
                                       value="Departamento comercial"
                                       placeholder="Ej: Departamento comercial, Gerencia, etc."
                                       maxlength="255">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Texto que aparecerá en la cabecera de la casilla de firma de la empresa en el PDF.
                                </small>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="mostrar_cuenta_bancaria_pdf_presupuesto_empresa"
                                       name="mostrar_cuenta_bancaria_pdf_presupuesto_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="mostrar_cuenta_bancaria_pdf_presupuesto_empresa">
                                    <strong>Mostrar cuenta bancaria en PDF (pagos por transferencia)</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Si está activado y la forma de pago es "transferencia", se mostrará el bloque de datos bancarios (IBAN, SWIFT, Banco) en el PDF del presupuesto. Si está desactivado, no se mostrará aunque la forma de pago sea transferencia.
                            </small>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="obs_linea_alineadas_descripcion_empresa"
                                       name="obs_linea_alineadas_descripcion_empresa"
                                       value="1">
                                <label class="form-check-label" for="obs_linea_alineadas_descripcion_empresa">
                                    <strong>Observaciones de línea alineadas bajo columna Descripción en PDF</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Si está activado, las observaciones introducidas en cada línea de artículo del presupuesto se imprimirán en el PDF alineadas bajo la columna "Descripción". Si está desactivado (por defecto), se imprimen desde el margen izquierdo del documento.
                            </small>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="permitir_descuentos_lineas_empresa"
                                       name="permitir_descuentos_lineas_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="permitir_descuentos_lineas_empresa">
                                    <strong>Permitir descuentos en líneas de presupuesto</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Si está activado (por defecto), el campo % Descuento en las líneas de presupuesto es editable y la columna y fila de descuento aparecen en el PDF. Si está desactivado, el campo queda bloqueado a 0 y la columna / fila de descuento no se muestra en el PDF.
                            </small>
                        </div>
                    </div>

                    <!-- SECCIÓN: Configuración de PDF de ALBARANES DE CARGA -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-truck me-2"></i>Configuración de PDF de ALBARANES DE CARGA
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="mostrar_kits_albaran_empresa"
                                       name="mostrar_kits_albaran_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="mostrar_kits_albaran_empresa">
                                    <strong>Mostrar KITs detallados</strong>
                                </label>
                                <small class="text-muted d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Muestra el desglose de componentes de los KITs en el PDF del albarán de carga.
                                </small>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="mostrar_obs_familias_articulos_albaran_empresa"
                                       name="mostrar_obs_familias_articulos_albaran_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="mostrar_obs_familias_articulos_albaran_empresa">
                                    <strong>Mostrar observaciones técnicas</strong>
                                </label>
                                <small class="text-muted d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Muestra las observaciones de familias y artículos en el PDF del albarán de carga.
                                </small>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="mostrar_obs_pie_albaran_empresa"
                                       name="mostrar_obs_pie_albaran_empresa"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="mostrar_obs_pie_albaran_empresa">
                                    <strong>Mostrar observaciones de pie</strong>
                                </label>
                                <small class="text-muted d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Muestra las observaciones de pie del presupuesto en el PDF del albarán de carga.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Estado de la Empresa -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-toggle-on me-2"></i>Estado de la Empresa
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Campo oculto para enviar el valor real -->
                                    <input type="hidden" name="activo_empresa" id="activo_empresa_hidden" value="1">
                                    
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_empresa_display" checked disabled>
                                        <label class="form-check-label" for="activo_empresa_display">
                                            <strong id="estado_texto">Empresa Activa</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Las empresas nuevas siempre se crean activas. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarEmpresa" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Empresa
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

    <!-- Modal de Ayuda -->
    <?php include "ayudaEmpresas.php"; ?>

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="formularioEmpresa.js?v=<?php echo time(); ?>"></script>

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
                if ($(this).scrollTop() > 300) {
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
                }, 800);
                return false;
            });

            // Hacer scroll al final del formulario
            $('#scrollToBottom').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 800);
                return false;
            });
        });
    </script>

</body>

</html>
