<?php
session_start();

$idRolUsuario = $_SESSION['id_rol'] ?? 0;

// Función para controlar los permisos de los menús
function puedeVerMenu($idRol, $modulo) {
    // 2: GESTOR
    // 3: ADMIN
    // 4: COMERCIAL
    $permisos = [
        'usuarios'       => [2, 3],                  // Porque rol 2 y 3 pueden acceder
        'logs'           => [2, 3],                  // Lo mismo
        'mantenimientos' => [2, 3, 4],
        'llamadas'       => [2, 3, 4],
        'comerciales'    => [3],                      // Solo admin
        'dashboard'      => [2, 3, 4],                      // Solo admin
        // Otros módulos que tengas, según corresponda
    ];
    return in_array($idRol, $permisos[$modulo] ?? []);
}

?>

<label class="sidebar-label pd-x-10 mg-t-20 op-3">Navigation</label>
<ul class="br-sideleft-menu">



  <?php if (puedeVerMenu($idRolUsuario, 'dashboard')): ?>
    <li class="br-menu-item">
        <a href="../Home/index.php" class="br-menu-link">
            <i class="menu-item-icon icon ion-ios-home tx-24"></i>
            <span class="menu-item-label">Home</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../Dashboard/index.php" class="sub-link">Inicio</a></li>
        </ul>
    </li>
    <?php endif; ?>



     <?php if (puedeVerMenu($idRolUsuario, 'mantenimientos')): ?>
    <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-settings tx-24"></i>
            <span class="menu-item-label">Mantenimientos</span>
        </a>
        <ul class="br-menu-sub">


            <li class="sub-item"><a href="../MntUnidad/index.php" class="sub-link">Unidades medida</a></li>
            <li class="sub-item"><a href="../MntImpuesto/index.php" class="sub-link">Impuestos</a></li>
            <li class="sub-item"><a href="../MntEstados_ppto/index.php" class="sub-link">Estados de Presupuesto</a></li>
            <li class="sub-item"><a href="../MntCoeficiente/index.php" class="sub-link">Coeficientes Reductores</a></li>
            <li class="sub-item"><a href="../MntObservaciones/index.php" class="sub-link">Observaciones generales</a></li>
            <li class="sub-item"><a href="../MntMetodos_pago/index.php" class="sub-link">Métodos de Pago</a></li>
            <li class="sub-item"><a href="../MntFormas_Pago/index.php" class="sub-link">Formas de Pago</a></li>
            <li class="sub-item"><a href="../MntTipos_documento/index.php" class="sub-link">Tipos de Documento</a></li>
            <li class="sub-item"><a href="../Documento/index.php" class="sub-link">Documentos</a></li>
            <li class="sub-item"><a href="../Documento/index_tecnico.php" class="sub-link">Gestor Documental</a></li>
            <li class="sub-item"><a href="../MntEmpresas/index.php" class="sub-link">Empresas</a></li>


            <li class="sub-item" style="pointer-events: none; color: #333; font-weight: bold; font-size: 12px; text-transform: uppercase; padding: 8px 15px; background-color: #f8f9fa; margin: 2px 0;">
                    <i class="fa fa-phone"></i> LLAMADAS
            </li>
            <li class="sub-item"><a href="../MntContactos/index.php" class="sub-link">Contactos</a></li>
            <li class="sub-item"><a href="../MntMetodosContacto/index.php" class="sub-link">Métodos Contacto</a></li>
            <li class="sub-item"><a href="../MntEstadosLlamada/index.php" class="sub-link">Estados Llamada</a></li>
            
            <!-- <li style="border-top: 1px solid #ddd; margin: 8px 0; padding: 0;"></li> -->
            <li class="sub-item" style="pointer-events: none; color: #333; font-weight: bold; font-size: 12px; text-transform: uppercase; padding: 8px 15px; background-color: #f8f9fa; margin: 2px 0;">
                📦 ARTÍCULOS
            </li>
<!--             
            <li class="sub-item"><a href="../MntFamilia/index.php" class="sub-link">Familias</a></li> -->
            
            <li class="sub-item"><a href="../MntEstados_elemento/index.php" class="sub-link">Estados elementos</a></li>
            <li class="sub-item"><a href="../MntGrupo_articulo/index.php" class="sub-link">Grupo</a></li>
            <li class="sub-item"><a href="../MntFamilia_unidad/index.php" class="sub-link">Familias</a></li>
            <li class="sub-item"><a href="../MntMarca/index.php" class="sub-link">Marcas</a></li>
            <li class="sub-item"><a href="../MntArticulos/index.php" class="sub-link">Artículos</a></li>
            <li class="sub-item"><a href="../MntElementos_consulta/index.php" class="sub-link">Elementos - consulta</a></li>
            
            
            <!-- <li class="sub-item"><a href="../MntFamilia_plus/index.php" class="sub-link">Familias Plus</a></li> -->
            <!-- <li class="sub-item"><a href="../MntFamilia_unidad_datatables/index.php" class="sub-link">Familias datatables</a></li> -->



 
            <li class="sub-item" style="pointer-events: none; color: #333; font-weight: bold; font-size: 12px; text-transform: uppercase; padding: 8px 15px; background-color: #f8f9fa; margin: 2px 0;">
                📦 USUARIOS-ROLES
            </li>
            <li class="sub-item"><a href="../Usuarios/index.php" class="sub-link">Usuarios</a></li>
            <li class="sub-item"><a href="../MntRoles/index.php" class="sub-link">Roles</a></li>
         
        </ul>
    </li>
    <?php endif; ?>




  

    <?php if (puedeVerMenu($idRolUsuario, 'llamadas')): ?>
    <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-telephone tx-24"></i>
            <span class="menu-item-label">Llamadas</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../MntLlamadas/index.php" class="sub-link">Llamadas</a></li>
            <!-- <li class="sub-item"><a href="../MntAdjuntosLlamadas/index.php" class="sub-link">Adjuntos Llamadas</a></li> -->
             <li class="sub-item"><a href="../MntComerciales/index.php" class="sub-link">Responsables atención</a></li>
                      <li class="sub-item"><a href="../MntComercialesVacaciones/index.php" class="sub-link">Responsables Vacaciones</a></li>
        </ul>
    </li>
    <?php endif; ?>


   <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-people tx-24"></i>
            <span class="menu-item-label">Clientes-Proveedores</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../MntProveedores/index.php" class="sub-link">Proveedores</a></li>
            <li class="sub-item"><a href="../MntClientes/index.php" class="sub-link">Clientes</a></li>
        </ul>
    </li>

   <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-people tx-24"></i>
            <span class="menu-item-label">Presupuestos</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../Presupuesto/index.php" class="sub-link">Mto. Presupuestos</a></li>
        </ul>
    </li>

   

    <?php if (puedeVerMenu($idRolUsuario, 'logs')): ?>
    <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-paper tx-24"></i>
            <span class="menu-item-label">Logs</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../Logs/index.php" class="sub-link">Logs</a></li>
            <li class="sub-item"><a href="../../assets/Reunion/sistema-gestion-explicacion.html" class="sub-link">Sistema Gestión</a></li>
        </ul>
        <!-- assets/Reunion/sistema-gestion-explicacion.html -->
    </li>
    <?php endif; ?>




     <?php if (puedeVerMenu($idRolUsuario, 'logs')): ?>
    <li class="br-menu-item">
        <a href="#" class="br-menu-link with-sub">
            <i class="menu-item-icon icon ion-ios-paper tx-24"></i>
            <span class="menu-item-label">Informes</span>
        </a>
        <ul class="br-menu-sub">
            <li class="sub-item"><a href="../Informe_vigencia/index.php" class="sub-link">Calendarios Garantías</a></li>
            <li class="sub-item"><a href="../Informe_mantenimiento/index.php" class="sub-link">Calendarios Mantenimientos</a></li>
            <li class="sub-item"><a href="../Informe_ppto/index.php" class="sub-link">Calendarios Presupuestos</a></li>
            <li class="sub-item"><a href="../Consulta_Garantias/index.php" class="sub-link">Consulta Garantías</a></li>
            <li class="sub-item"><a href="../Consulta_Mantenimientos/index.php" class="sub-link">Consulta Mantenimientos</a></li>
            
        </ul>
        <!-- assets/Reunion/sistema-gestion-explicacion.html -->
    </li>
    <?php endif; ?>

<!---->
<!---->
    <!-- <?php if (puedeVerMenu($idRolUsuario, 'doble_datatable')): ?> -->
    <!-- <li class="br-menu-item"> -->
        <!-- <a href="#" class="br-menu-link with-sub"> -->
            <!-- <i class="menu-item-icon icon ion-ios-filing-outline tx-24"></i> -->
            <!-- <span class="menu-item-label">Ejemplo doble datatable</span> -->
        <!-- </a> -->
        <!-- <ul class="br-menu-sub"> -->
            <!-- <li class="sub-item"><a href="../EjemploCategoriaProducto/index.php" class="sub-link">Ejemplo Categoria/Producto</a></li> -->
        <!-- </ul> -->
    <!-- </li> -->
    <!-- <?php endif; ?> -->

    <?php if (isset($_SESSION['sesion_iniciada']) && $_SESSION['sesion_iniciada'] === true): ?>
    <li class="br-menu-item">
        <a href="#" class="br-menu-link logout">
            <i class="menu-item-icon icon ion-log-out tx-24"></i>
            <span class="menu-item-label">Cerrar Sesión</span>
        </a>
    </li>
    <?php endif; ?>

</ul>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('a.logout').on('click', function (e) {
      e.preventDefault();
      $.ajax({
        url: '../../controller/login.php?op=cerrarSesion',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          if (response.success) {
            toastr.info(response.message, 'Sesión cerrada');
            setTimeout(() => {
              window.location.href = '../../view/Home';
            }, 2000);
          } else {
            toastr.warning('No se pudo cerrar sesión.');
          }
        },
        error: function () {
          toastr.error('Error al intentar cerrar sesión.');
        }
      });
    });
  });
</script>
