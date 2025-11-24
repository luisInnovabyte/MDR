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
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="">Home</a>
            </nav>
        </div><!-- br-pageheader -->
        <div class="br-pagetitle">
            <i class="icon icon ion-ios-bookmarks-outline"></i>
            <div>
                <h4>Home</h4>
                <p class="mg-b-0">Sistema integral para la gestión de alquiler de equipos audiovisuales</p>
            </div>
        </div><!-- d-flex -->

        <!-- ----------------------- -->
        <!--          BODY           -->
        <!-- ----------------------- -->


        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <!-- ESte div se hace para poder centrarlo  -->
                
                <!-- Logo centrado de MDR -->
                <div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
                    <div class="text-center">
                        <img src="../../config/template/Logo.png" alt="MDR ERP Manager" class="img-fluid" style="max-width: 600px; width: 100%; height: auto;">
                        <h2 class="mt-4 mb-2" style="color: #333; font-weight: 300;">MDR ERP Manager</h2>
                        <p class="text-muted" style="font-size: 18px;">Sistema de Gestión para Alquiler de equipos audiovisuales</p>
                    </div>
                </div>

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