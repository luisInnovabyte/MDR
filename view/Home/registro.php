<!DOCTYPE html>
<html lang="es">

<head>
  <?php include_once('../../config/template/mainHead.php') ?>
</head>

<body>

  <div class="d-flex align-items-center justify-content-center bg-br-primary ht-100v">

    <div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white rounded shadow-base">
      <div class="signin-logo tx-center tx-28 tx-bold tx-inverse"><span class="tx-normal">[</span> Ra <span class="tx-info">82</span> <span class="tx-normal">]</span></div>
      <div class="tx-center mg-b-60">Ejemplo de CRUD Completo</div>

      <form id="formRegistro" name="formRegistro" method="POST">

        <div class="form-group">
          <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required>
        </div><!-- form-group -->

        <div class="form-group">
          <input type="email" class="form-control" name="email" id="email" placeholder="Correo electrónico" required>
        </div><!-- form-group -->

        <div class="form-group">
          <div class="input-group">
            <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña" required>
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="button" id="verContrasena">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>
        </div><!-- form-group -->

        <div class="form-group">
          <div class="input-group">
            <input type="password" class="form-control" name="confirmar_contrasena" id="confirmar_contrasena" placeholder="Repite la contraseña" required>
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="button" id="verConfirmarContrasena">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            </div>
          </div>
        </div><!-- form-group -->

        <button type="submit" id="enviar" class="btn btn-info btn-block">Registrarse</button>
        <div class="mg-t-40 tx-center">
          <a href="index.php" class="tx-info">¿Ya tienes una cuenta? Inicia sesión</a>
        </div>

      </form>

    </div><!-- login-wrapper -->
  </div><!-- d-flex -->

  <!-- ----------------------- -->
  <!--       mainJs.php        -->
  <!-- ----------------------- -->
  <?php include_once('../../config/template/mainJs.php') ?>
  <!------------------------------->

  <script type="text/javascript" src="registro.js"></script>

</body>

</html>
