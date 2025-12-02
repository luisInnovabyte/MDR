    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'dashboard'; ?>
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
        <!-- <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="">Home</a>
            </nav>
        </div>br-pageheader -->
        <!-- <div class="br-pagetitle">
            <i class="icon icon ion-ios-bookmarks-outline"></i>
            <div>
                <h4>Home</h4>
                <p class="mg-b-0">Sistema integral para la gestión de alquiler de equipos audiovisuales</p>
            </div>
        </div>d-flex -->

        <!-- ----------------------- -->
        <!--          BODY           -->
        <!-- ----------------------- -->


        <div class="br-pagebody">
            
        
        
        
        <div class="br-section-wrapper">
                <!-- ESte div se hace para poder centrarlo  -->
                
                <!-- Logo centrado de MDR -->
                <div class="d-flex justify-content-center align-items-center" style="min-height: 30vh;">
                    <div class="text-center">
                        <img src="../../config/template/Logo.png" alt="MDR ERP Manager" class="img-fluid" style="max-width: 400px; width: 100%; height: auto;">
                        <h2 class="mt-3 mb-2" style="color: #333; font-weight: 300;">MDR ERP Manager</h2>
                        <p class="text-muted" style="font-size: 16px;">Sistema de Gestión para Alquiler de equipos audiovisuales</p>
                    </div>
                </div>

                <!-- Accesos directos a módulos principales -->
                <div class="row mg-t-30">
                    <div class="col-12">
                        <h4 class="tx-gray-800 mg-b-30 text-center">Accesos Directos</h4>
                    </div>
                </div>

                <div class="row row-sm justify-content-center">
                    
                    <!-- Mantenimientos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='dash_mantenimiento.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="icon ion-ios-settings tx-60 tx-primary mg-b-10"></i>
                                <h5 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Mantenimientos</h5>
                                <p class="tx-11 tx-gray-500 mg-b-0">Configuración del sistema</p>
                            </div>
                        </div>
                    </div>

                    <!-- Llamadas -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='dash_llamadas.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="icon ion-ios-telephone tx-60 tx-success mg-b-10"></i>
                                <h5 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Llamadas</h5>
                                <p class="tx-11 tx-gray-500 mg-b-0">Gestión de llamadas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Clientes-Proveedores -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='dash_clientes.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="icon ion-ios-people tx-60 tx-warning mg-b-10"></i>
                                <h5 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Clientes-Proveedores</h5>
                                <p class="tx-11 tx-gray-500 mg-b-0">Gestión de contactos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Logs -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Logs/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="icon ion-ios-paper tx-60 tx-info mg-b-10"></i>
                                <h5 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Logs</h5>
                                <p class="tx-11 tx-gray-500 mg-b-0">Historial del sistema</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informes -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='dash_informes.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-30">
                                <i class="icon ion-ios-paper-outline tx-60 tx-danger mg-b-10"></i>
                                <h5 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Informes</h5>
                                <p class="tx-11 tx-gray-500 mg-b-0">Calendarios-reportes</p>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

            <div class="row row-sm mg-t-20 ">

            </div> <!-- row -->

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

    <!-- Google Charts loader -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript" src="chart.js"></script>

</body>

</html>