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
                <a class="breadcrumb-item" href="index.php">Home</a>
                <a class="breadcrumb-item active" href="#">Mantenimientos</a>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <i class="fa fa-cogs tx-24"></i>
            <div>
                <h4>Mantenimientos</h4>
                <p class="mg-b-0">Accesos directos a la configuración del sistema</p>
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

                <!-- Sección: Configuración General -->
                <div class="row mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-cog"></i> Configuración General
                        </h5>
                    </div>
                </div>

                <div class="row row-sm">
                    
                    <!-- Unidades de Medida -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntUnidad/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-ruler-combined tx-50 tx-primary mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Unidades Medida</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Gestión de unidades</p>
                            </div>
                        </div>
                    </div>

                    <!-- Impuestos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntImpuesto/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-calculator tx-50 tx-success mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Impuestos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Configuración de IVA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estados de Presupuesto -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntEstados_ppto/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-clipboard-list tx-50 tx-warning mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Estados Presupuesto</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Estados de cotizaciones</p>
                            </div>
                        </div>
                    </div>

                    <!-- Coeficientes Reductores -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntCoeficiente/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-chart-line tx-50 tx-info mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Coeficientes</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Reductores de precios</p>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntObservaciones/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-comment-dots tx-50 mg-b-10" style="color: #7c4dff;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Observaciones</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Notas generales</p>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntMetodos_pago/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-credit-card tx-50 mg-b-10" style="color: #e91e63;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Métodos de Pago</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Tipos de pago</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formas de Pago -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntFormas_Pago/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-money-bill-wave tx-50 mg-b-10" style="color: #ff9800;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Formas de Pago</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Condiciones de pago</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empresas -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntEmpresas/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-building tx-50 mg-b-10" style="color: #ff9800;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Empresas</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Gestión de empresas</p>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

                <!-- Sección: Llamadas -->
                <div class="row mg-t-40 mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-phone"></i> Gestión de Llamadas
                        </h5>
                    </div>
                </div>

                <div class="row row-sm">
                    
                    <!-- Contactos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntContactos/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-address-book tx-50 tx-success mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Contactos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Registro de contactos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos Contacto -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntMetodosContacto/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-comments tx-50 tx-primary mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Métodos Contacto</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Vías de comunicación</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estados Llamada -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntEstadosLlamada/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-flag tx-50 tx-warning mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Estados Llamada</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Estados de seguimiento</p>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

                <!-- Sección: Artículos -->
                <div class="row mg-t-40 mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-cubes"></i> Gestión de Artículos
                        </h5>
                    </div>
                </div>

                <div class="row row-sm">
                    
                    <!-- Estados Elementos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntEstados_elemento/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-heartbeat tx-50 tx-info mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Estados Elementos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Estados de equipos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Grupos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntGrupo_articulo/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-layer-group tx-50 mg-b-10" style="color: #7c4dff;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Grupos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Agrupación de artículos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Familias -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntFamilia_unidad/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-folder-open tx-50 mg-b-10" style="color: #ff9800;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Familias</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Familias de productos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Marcas -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntMarca/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-tag tx-50 mg-b-10" style="color: #e91e63;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Marcas</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Marcas de equipos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Artículos -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntArticulos/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-tags tx-50 tx-danger mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Artículos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Catálogo completo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Elementos Consulta -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntElementos_consulta/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-search tx-50 mg-b-10" style="color: #009688;"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Elementos</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Consulta de elementos</p>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

                <!-- Sección: Usuarios y Roles -->
                <div class="row mg-t-40 mg-b-30">
                    <div class="col-12">
                        <h5 class="tx-gray-800 mg-b-20">
                            <i class="fa fa-users"></i> Usuarios y Roles
                        </h5>
                    </div>
                </div>

                <div class="row row-sm">
                    
                    <!-- Usuarios -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../Usuarios/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-user-plus tx-50 tx-primary mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Usuarios</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Gestión de usuarios</p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="col-lg-2 col-md-4 col-sm-6 mg-b-20">
                        <div class="card shadow-base bd-0 text-center" style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                             onclick="window.location.href='../MntRoles/index.php'"
                             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                            <div class="card-body pd-25">
                                <i class="fa fa-key tx-50 tx-warning mg-b-10"></i>
                                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-semibold mg-b-5">Roles</h6>
                                <p class="tx-11 tx-gray-500 mg-b-0">Permisos del sistema</p>
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
