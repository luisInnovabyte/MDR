<!doctype html>
<html lang="es" data-bs-theme="dark">
<!--start head-->

<head>
  <?php

  session_start();
  /* if (($_SERVER['REQUEST_METHOD'] == 'POST') ) {

    if (isset($_SESSION['usu_id'])) {
      {
        header('Location:../Home/index.php');

      }
  }
  } */
 // Verificar si la variable GET 'tokenidusu' está definida y no está vacía
if (!isset($_GET['tokenidusu']) || empty(trim($_GET['tokenidusu']))) {
  // Redirigir a la página deseada
  header('Location: ../Home/index.php');
  exit(); // Finalizar el script para evitar ejecución adicional
}
  ?>
  <?php include("../../config/template/mainHead.php"); ?>

</head>
<!--end head-->
  <body>


    <!--authentication-->

<div class="mx-3 mx-lg-0">
  <div class="card my-5 col-xl-9 col-xxl-8 mx-auto rounded-4 overflow-hidden shadow p-3 border-0">
    <div class="row g-3 align-items-center">
      
      <!-- LADO IZQUIERDO (Formulario) -->
      <div class="col-lg-6 d-flex">
        <div class="card-body p-5 w-100">
          <input type="hidden" id="token" value="<?php echo $_GET['tokenidusu']; ?>">
          <div class="text-center mb-4">
            <img src="../../public/img/logo-icon.png" class="mb-4 d-block mx-auto" width="200" alt="">
          </div>

          <h4 class="fw-bold text-dark">Generar Nueva Contraseña</h4>
          <p class="mb-0 text-muted">Por favor introduce la nueva contraseña</p>

          <div class="form-body mt-4">
            <form class="row g-3">
              <div class="col-12">
                <label class="form-label text-dark" for="NewPassword">Nueva Contraseña</label>
                <input type="text" class="form-control form-control-lg" id="NewPassword" placeholder="Introduce nueva contraseña">
              </div>
              <div class="col-12">
                <label class="form-label text-dark" for="ConfirmPassword">Confirmar Contraseña</label>
                <input type="text" class="form-control form-control-lg" id="ConfirmPassword" placeholder="Confirma la nueva contraseña">
              </div>
              <div class="col-12">
                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-primary btn-lg" id="changePasswordButton">Cambiar Contraseña</button>
                  <a href="../../view/Home/" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                </div>
              </div>    
            </form>
          </div>
        </div>
      </div>

      <!-- LADO DERECHO (Imagen decorativa) -->
      <div class="col-lg-6 d-lg-flex">
        <div class="p-3 rounded-4 w-100 d-flex align-items-center justify-content-center bg-primary bg-gradient">
          <img src="../../public/img/cambiar-pass.png" class="img-fluid" alt="">
        </div>
      </div>

    </div>
  </div>
</div>

      

   <!--plugins-->
   <?php include_once '../../config/template/mainJs.php' ?>

<script src="index.js"></script>

  
  </body>
</html>