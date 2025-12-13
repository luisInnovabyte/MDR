    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'logs'; ?>
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
                <a class="breadcrumb-item" href="index.php">Home</a>
                <a class="breadcrumb-item active" href="#">Informes</a>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <i class="fa fa-chart-bar tx-24"></i>
            <div>
                <h4>Informes y Calendarios</h4>
                <p class="mg-b-0">Accesos directos a informes y calendarios del sistema</p>
            </div>

             <div class="mg-l-auto">
                <a href="index.php" class="btn btn-secondary btn-oblong tx-11 tx-uppercase tx-mont tx-medium">
                    <i class="fa fa-arrow-left mg-r-10"></i> Volver al Dashboard
                </a>
            </div>
            
        </div><!-- d-flex -->

        <!-- ----------------------- -->
        <!--          BODY           -->
        <!-- ----------------------- -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- Sección: Calendarios -->
                <div class="row mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-calendar-alt"></i> Informes - Calendarios de Control
                        </h5>
                    </div>
                </div>

                <div class="row row-sm justify-content-center">
                    
                    <!-- Calendarios Garantías -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Informe_vigencia/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-shield-alt tx-60 tx-primary mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Calendarios Garantías</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Control de garantías</p>
                            </div>
                        </div>
                    </div>

                    <!-- Calendarios Mantenimientos -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Informe_mantenimiento/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-tools tx-60 tx-success mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Calendarios Mantenimientos</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Control mantenimientos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Consulta Garantías -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Consulta_Garantias/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-list-alt tx-60 tx-warning mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Consulta Garantías</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Fechas fin de garantías</p>
                            </div>
                        </div>
                    </div>

                    <!-- Consulta Mantenimientos -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Consulta_Mantenimientos/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-clipboard-list tx-60 tx-info mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Consulta Mantenimientos</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Fechas de mantenimientos</p>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <!-- ------------------------ -->
        <!--           FIN            -->
        <!--           BODY           -->
        <!-- ------------------------ -->

        <!-- ----------------------- -->
        <!--     mainFooter.php      -->
        <!-- ----------------------- -->
        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
        <!-- ------------------------- -->
        <!--   END mainFooter.php      -->
        <!-- ------------------------- -->

    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <!------------------------------->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->

</body>

</html>
