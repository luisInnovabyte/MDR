<?php
session_start();

$nombreUsuario = 'Visitante';
$correoUsuario = 'anonimo@dominio.com';
$rolUsuario = 'Sin rol';
$idRolUsuario = 0;

if (isset($_SESSION['sesion_iniciada']) && $_SESSION['sesion_iniciada'] === true) {
    $nombreUsuario = $_SESSION['nombre'];
    $correoUsuario = $_SESSION['email'];
    $rolUsuario = $_SESSION['nombre_rol'];  // CORRECTO
    $idRolUsuario = $_SESSION['id_rol'];
}

// Asignar color según rol
$colorRol = 'btn-secondary'; // default
switch (strtolower($rolUsuario)) {
    case 'administrador':
        $colorRol = 'btn-danger';
        break;
    case 'usuario':
        $colorRol = 'btn-primary';
        break;
    case 'moderador':
        $colorRol = 'btn-warning';
        break;
    case 'invitado':
        $colorRol = 'btn-success';
        break;
}

?>

<div class="br-header-left">
    <div class="navicon-left hidden-md-down"><a id="btnLeftMenu" href=""><i class="icon ion-navicon-round"></i></a>
    </div>
    <div class="navicon-left hidden-lg-up"><a id="btnLeftMenuMobile" href=""><i
                class="icon ion-navicon-round"></i></a></div>

    <!-- Esto El el search del CMS original -->
    <!--<div class="input-group hidden-xs-down wd-170 transition">
        <input id="searchbox" type="text" class="form-control" placeholder="Search">
        <span class="input-group-btn">
            <button class="btn btn-secondary" type="button"><i class="fa fa-search"></i></button>
        </span>
    </div>--><!-- input-group -->
    <!-- FIN: Esto El el search del CMS original -->
</div><!-- br-header-left -->

<div class="br-header-right">
    <nav class="nav">
        <!-- INICIO: Íconos de mensaje ocultos -->
        <!--
        <div class="dropdown">
            <a href="" class="nav-link pd-x-7 pos-relative" data-toggle="dropdown">
                <i class="icon ion-ios-email-outline tx-24"></i>
                <span class="square-8 bg-danger pos-absolute t-15 r-0 rounded-circle"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-header">
                <div class="dropdown-menu-label">
                    <label>Messages</label>
                    <a href="">+ Add New Message</a>
                </div>
                <div class="media-list">
                    <a href="" class="media-list-link">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <div>
                                    <p>Donna Seay</p>
                                    <span>2 minutes ago</span>
                                </div>
                                <p>A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring.</p>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <div>
                                    <p>Samantha Francis</p>
                                    <span>3 hours ago</span>
                                </div>
                                <p>My entire soul, like these sweet mornings of spring.</p>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <div>
                                    <p>Robert Walker</p>
                                    <span>5 hours ago</span>
                                </div>
                                <p>I should be incapable of drawing a single stroke at the present moment...</p>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <div>
                                    <p>Larry Smith</p>
                                    <span>Yesterday</span>
                                </div>
                                <p>When, while the lovely valley teems with vapour around me, and the meridian sun strikes...</p>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-footer">
                        <a href=""><i class="fa fa-angle-down"></i> Show All Messages</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!-- INICIO: Íconos de notificaciones ocultos -->
        <!--
        <div class="dropdown">
            <a href="" class="nav-link pd-x-7 pos-relative" data-toggle="dropdown">
                <i class="icon ion-ios-bell-outline tx-24"></i>
                <span class="square-8 bg-danger pos-absolute t-15 r-5 rounded-circle"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-header">
                <div class="dropdown-menu-label">
                    <label>Notifications</label>
                    <a href="">Mark All as Read</a>
                </div>
                <div class="media-list">
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <p class="noti-text"><strong>Suzzeth Bungaos</strong> tagged you and 18 others in a post.</p>
                                <span>October 03, 2017 8:45am</span>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <p class="noti-text"><strong>Mellisa Brown</strong> appreciated your work <strong>The Social Network</strong></p>
                                <span>October 02, 2017 12:44am</span>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <p class="noti-text">20+ new items added are for sale in your <strong>Sale Group</strong></p>
                                <span>October 01, 2017 10:20pm</span>
                            </div>
                        </div>
                    </a>
                    <a href="" class="media-list-link read">
                        <div class="media">
                            <img src="" alt="">
                            <div class="media-body">
                                <p class="noti-text"><strong>Julius Erving</strong> wants to connect with you on your conversation with <strong>Ronnie Mara</strong></p>
                                <span>October 01, 2017 6:08pm</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-footer">
                        <a href=""><i class="fa fa-angle-down"></i> Show All Notifications</a>
                    </div>
                </div>
            </div>
        </div>
        -->

        <!-- Botón con el rol y color -->
        <div class="nav-item d-flex align-items-center mr-3">
            <button type="button" class="btn <?php echo $colorRol; ?> btn-sm" disabled>
                <?php echo htmlspecialchars(ucfirst($rolUsuario)); ?>
            </button>
        </div>

        <div class="dropdown">
            <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
                <span class="logged-name hidden-md-down"><?php echo htmlspecialchars($nombreUsuario); ?></span>
                <img src="" class="wd-32 rounded-circle" alt="">
                <span class="square-10 bg-success"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-header wd-250">
                <div class="tx-center">
                    <!-- <a href=""><img src="<?php echo htmlspecialchars($imagenUsuario); ?>" class="wd-80 rounded-circle" alt="User Image"></a> -->
                    <h6 class="logged-fullname"><?php echo htmlspecialchars($nombreUsuario); ?></h6>
                    <p><?php echo htmlspecialchars($correoUsuario); ?></p>
                </div>
                <hr>
                <?php if (isset($_SESSION['sesion_iniciada']) && $_SESSION['sesion_iniciada'] === true): ?>
                    <li class="br-menu-item">
                        <a href="#" class="br-menu-link logout">
                            <i class="menu-item-icon icon ion-power tx-24"></i>
                            <span class="menu-item-label">Cerrar Sesión</span>
                        </a>
                    </li>
                <?php endif; ?>
                <!-- <div class="tx-center">
                    <span class="profile-earning-label">Earnings After Taxes</span>
                    <h3 class="profile-earning-amount">$13,230 <i class="icon ion-ios-arrow-thin-up tx-success"></i></h3>
                    <span class="profile-earning-text">Based on list price.</span>
                </div> -->
                <hr>
                <!-- <ul class="list-unstyled user-profile-nav">
                    <li><a href="../Home/perfil.php"><i class="icon ion-ios-person"></i> Edit Profile</a></li>
                    <li><a href=""><i class="icon ion-ios-gear"></i> Settings</a></li>
                    <li><a href=""><i class="icon ion-ios-download"></i> Downloads</a></li>
                    <li><a href=""><i class="icon ion-ios-star"></i> Favorites</a></li>
                    <li><a href=""><i class="icon ion-ios-folder"></i> Collections</a></li>
                </ul> -->
            </div>
        </div>
    </nav>

    <!-- INICIO: Ícono de chat oculto -->
    <!--
    <div class="navicon-right">
        <a id="btnRightMenu" href="" class="pos-relative">
            <i class="icon ion-ios-chatboxes-outline"></i>
            <span class="square-8 bg-danger pos-absolute t-10 r--5 rounded-circle"></span>
        </a>
    </div>
    -->
</div>