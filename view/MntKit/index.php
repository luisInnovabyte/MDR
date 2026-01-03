<?php 
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');
?>
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
            <a class="breadcrumb-item" href="../MntArticulos/index.php">Artículos</a>
            <span class="breadcrumb-item active">Composición del KIT</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Composición del KIT</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaKit" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div>
        
        <!-- Info del artículo KIT -->
        <div class="mt-2 mb-3" id="info-articulo-kit">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <!-- Icono principal -->
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                                <i class="bi bi-collection text-white" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Información del artículo KIT -->
                        <div class="col">
                            <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                <i class="bi bi-info-circle me-1"></i>Artículo KIT actual
                            </div>
                            <h5 class="mb-2 fw-bold text-white" id="nombre-articulo-kit">
                                Cargando...
                            </h5>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-upc-scan me-1"></i>Código:
                                    <span id="codigo-articulo-kit" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-hash me-1"></i>ID:
                                    <span id="id-articulo-kit" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-box-seam me-1"></i>Componentes:
                                    <span id="total-componentes-kit" class="badge bg-warning text-dark ms-1 fw-semibold">0</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-currency-euro me-1"></i>Precio total:
                                    <span id="precio-total-kit" class="badge bg-success text-white ms-1 fw-semibold">0.00 €</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Botón de acción -->
                        <div class="col-auto d-none d-md-block">
                            <a href="../MntArticulos/index.php" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- br-pagetitle -->

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <!-- Botones de acción -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div class="flex-grow-1 me-3"></div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-oblong btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFormularioKit">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Componente
                    </button>
                    <a href="../MntArticulos/index.php" class="btn btn-oblong btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Artículos
                    </a>
                </div>
            </div>

            <!-- Tabla de componentes del kit -->
            <div class="table-wrapper">
                <table id="kit_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Código Componente</th>
                            <th>Nombre Componente</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th>Estado</th>
                            <th>ACT./DESAC.</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar código" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar componente" class="form-control form-control-sm" /></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th class="d-none"><input type="text" placeholder="NO Buscar" class="form-control form-control-sm" /></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- table-wrapper -->
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

    <footer class="br-footer">
        <?php include_once('../../config/template/mainFooter.php') ?>
    </footer>
</div><!-- br-mainpanel -->

    <!-- #################################### -->
    <!-- MODAL DE AYUDA                       -->
    <!-- #################################### -->

    <?php include_once('ayudaKit.php') ?>

    <!-- #################################### -->
    <!-- FIN MODAL DE AYUDA                   -->
    <!-- #################################### -->

    <!-- #################################### -->
    <!-- MODAL FORMULARIO KIT                 -->
    <!-- #################################### -->

    <?php include_once('formularioKit.php') ?>

    <!-- #################################### -->
    <!-- FIN MODAL FORMULARIO KIT             -->
    <!-- #################################### -->


    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="mntkit.js"></script>
    <script type="text/javascript" src="formularioKit.js"></script>

   <script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>


</body>

</html>
