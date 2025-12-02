    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'llamadas'; ?>
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
                <a class="breadcrumb-item active" href="#">Llamadas</a>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <i class="fa fa-phone tx-24"></i>
            <div>
                <h4>Gestión de Llamadas</h4>
                <p class="mg-b-0">Accesos directos a la gestión de llamadas y responsables</p>
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

                <!-- Sección: Llamadas -->
                <div class="row mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-phone-volume"></i> Gestión de Llamadas
                        </h5>
                    </div>
                </div>

                <div class="row row-sm justify-content-center">
                    
                    <!-- Llamadas -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntLlamadas/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-phone-alt tx-60 tx-primary mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Llamadas</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Registro-seguimiento</p>
                            </div>
                        </div>
                    </div>

                    <!-- Responsables Atención -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntComerciales/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-user-tie tx-60 tx-success mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Responsables</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Gestión de responsables</p>
                            </div>
                        </div>
                    </div>

                    <!-- Responsables Vacaciones -->
                    <div class="col-lg-3 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntComercialesVacaciones/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="fa fa-calendar-times tx-60 tx-warning mg-b-15"></i>
                                <h5 class="tx-uppercase tx-13 tx-spacing-1 tx-semibold mg-b-10">Vacaciones</h5>
                                <p class="tx-12 tx-gray-500 mg-b-0">Gestión de vacaciones</p>
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
