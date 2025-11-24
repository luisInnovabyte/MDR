<?php
session_start();
if (!isset($_SESSION['sesion_iniciada']) || !$_SESSION['sesion_iniciada']) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <?php include_once('../../config/template/mainHead.php') ?>
</head>

<body>

  <div class="d-flex align-items-center justify-content-center bg-br-primary ht-100v">

    <div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white rounded shadow-base">
      <div class="signin-logo tx-center tx-28 tx-bold tx-inverse">
        <span class="tx-normal">[</span> Ra <span class="tx-info">82</span> <span class="tx-normal">]</span>
      </div>
      <div class="tx-center mg-b-20">Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></div>

      <div class="form-group">
        <label><strong>Email:</strong></label>
        <div class="tx-dark"><?= htmlspecialchars($_SESSION['email']) ?></div>
      </div>

      <div class="form-group">
        <label><strong>Fecha de creación:</strong></label>
        <div class="tx-dark"><?= htmlspecialchars($_SESSION['fecha_crea']) ?></div>
      </div>

      <div class="form-group tx-center mg-t-30">
        <a href="logout.php" class="btn btn-danger btn-block">Cerrar Sesión</a>
      </div>

      <div class="mg-t-40 tx-center"><a href="../MntLlamadas/index.php" class="tx-info">Regresar a mantenimiento llamadas</a></div>

    </div><!-- login-wrapper -->
  </div><!-- d-flex -->

  <?php include_once('../../config/template/mainJs.php') ?>
  <script type="text/javascript" src="perfil.js"></script>

</body>

</html>
