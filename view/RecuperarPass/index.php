<html lang="es" data-bs-theme="light">

<!--start head-->

<head>
  <?php

  session_start();
  if (isset($_SESSION['usu_id'])) {


    //header('Location:../Home/index.php');
  }
  ?>
  <?php include("../../config/template/mainHead.php"); ?>

</head>
<!--end head-->

<body>


  <!--authentication-->
  <?php

  if (isset($_GET['correoUsu'])) {

  ?>
    <input type="hidden" id="getCorreo" value="<?php echo $_GET['correoUsu'] ?>">

  <?php
  }
  ?>
  <div class="mx-3 mx-lg-0">
    <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden shadow p-3 border-0">
      <div class="row g-3 align-items-center">
        <!-- LADO IZQUIERDO -->
        <div class="col-lg-6 d-flex">
          <div class="card-body p-5 w-100">
            <div class="text-center mb-4">

              <!-- ***** -->
              <span class="tx-40 tx-medium">[</span>
              <span class="tx-40 tx-medium">MDR</span>
              <span class="tx-40 tx-medium">]</span>

              <div class="tx-center mg-b-60">GESTIÓN COMERCIAL de LLAMADAS</div>
              <!-- ***** -->

              <!-- <img src="../../public/img/logo-icon.png" width="200" alt=""> -->
            </div>

            <div id="emailInput">
              <h4 class="fw-bold mb-2">¿Contraseña olvidada?</h4>
              <p class="text-muted">Introduce tu dirección de correo electrónico para enviarte un mensaje de confirmación</p>

              <form class="row g-3 mt-4">
                <div class="col-12">
                  <label class="form-label">Email</label>
                  <input type="email" id="email_recuperar" class="form-control form-control-lg" placeholder="ejemplo@dominio.com">
                </div>
                <div class="col-12">
                  <div class="d-grid gap-2">
                    <button type="button" id="btnEnviarConfirmacion" class="btn btn-primary btn-lg">Enviar</button>
                    <a href="../../view/Home/" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                  </div>
                </div>
              </form>
            </div>

            <!-- VERIFICACIÓN -->
            <div id="modalVerification" class="mt-4" style="display: none;">
              <div class="border rounded-4 p-4 bg-light">
                <h6 class="text-center mb-3">Introduce el código para verificar</h6>
                <label class="form-label text-muted">Código de verificación</label>
                <div class="d-flex justify-content-center gap-2 mb-3">
                  <input type="text" id="code1" class="form-control text-center" maxlength="1" style="width: 50px;">
                  <input type="text" id="code2" class="form-control text-center" maxlength="1" style="width: 50px;">
                  <input type="text" id="code3" class="form-control text-center" maxlength="1" style="width: 50px;">
                  <input type="text" id="code4" class="form-control text-center" maxlength="1" style="width: 50px;">
                  <input type="text" id="code5" class="form-control text-center" maxlength="1" style="width: 50px;">
                </div>
                <div class="d-grid gap-2">
                  <button type="button" id="btnEnviarCodigo" class="btn btn-success btn-lg">Confirmar código</button>
                  <a href="../../view/Home/" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                </div>
                <div class="text-center mt-3">
                  <a href="javascript:actualizar()">¿No recibiste el correo electrónico?</a>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- LADO DERECHO -->
        <div class="col-lg-6 d-lg-flex">
          <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-primary bg-gradient">
            <img src="../../public/img/boxed-forgot-password.png" class="img-fluid" alt="">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--authentication-->

  <!--plugins-->
  <?php include_once '../../config/template/mainJs.php' ?>

  <script src="index.js"></script>

</body>

</html>