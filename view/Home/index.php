<!DOCTYPE html>
<html lang="es">

<head>
  <?php include_once '../../config/template/mainHead.php'; ?>
</head>

<body>

  <div class="d-flex align-items-center justify-content-center bg-br-primary ht-100v" style="background-image: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.3)), url('../../config/template/fondo_home.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white rounded shadow-base">
      <div class="signin-logo tx-center tx-28 tx-bold tx-inverse">
        <img src="../../config/template/Logo.png" alt="MDR Logo" style="height: 60px; width: auto; margin-bottom: 10px;">
      </div>
      <div class="tx-center mg-b-60">MDR ERP Manager</div>

      <form id="formLogin" name="formLogin" method="POST">

        <div class="form-group">
          <input type="text" class="form-control" name="email" id="email" placeholder="Introduce el email" autofocus>
          <div class="invalid-feedback small-invalid-feedback">Email invalido</div>
        </div><!-- form-group -->

        <div class="form-group">
          <div class="input-group">
            <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Introduce la contraseña">
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="button" id="verContrasena">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>
          <div class="invalid-feedback small-invalid-feedback">Contraseña no valida (1 letra + 2 números)</div>
        </div><!-- form-group -->

        <a href="../RecuperarPass" class="tx-info tx-center tx-12 d-block mg-t-10 mg-b-10">Recuperar contraseña</a>
        <button type="submit" id="enviar" name="enviar" class="btn btn-info btn-block">Entrar</button>

        <!-- <div class="mg-t-60 tx-center">Not yet a member? <a href="" class="tx-info">Sign Up</a></div> -->
        <!-- <div class="mg-t-40 tx-center"><a href="registro.php" class="tx-info">Registrarse</a></div> -->
        <!-- <div class="mg-t-40 tx-center"><a href="../MntLlamadas/index.php" class="tx-info">Regresar a mantenimiento llamadas</a></div> -->
      </form>

    </div><!-- login-wrapper -->
  </div><!-- d-flex -->

  <!-- <script src="../lib/jquery/jquery.min.js"></script>
  <script src="../lib/jquery-ui/ui/widgets/datepicker.js"></script>
  <script src="../lib/bootstrap/js/bootstrap.bundle.min.js"></script> -->

  <!-- ----------------------- -->
  <!--       mainJs.php        -->
  <!-- ----------------------- -->
  <?php include_once('../../config/template/mainJs.php') ?>
  <!------------------------------->
  <!--     END mainJs.php        -->
  <!-- ------------------------- -->


  <script type="text/javascript" src="login.js"></script>

</body>

</html>