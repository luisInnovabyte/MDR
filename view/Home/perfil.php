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
  
  <!-- Signature Pad Library -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
  
  <style>
    .firma-container {
      margin-top: 20px;
      padding: 15px;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      background-color: #f8f9fa;
    }
    
    .firma-canvas {
      border: 2px solid #6c757d;
      border-radius: 5px;
      background-color: white;
      touch-action: none;
      width: 100%;
      cursor: crosshair;
    }
    
    .firma-buttons {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }
    
    .firma-preview {
      margin-top: 15px;
      padding: 10px;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      background-color: white;
      text-align: center;
    }
    
    .firma-preview img {
      max-width: 100%;
      height: auto;
      border: 1px solid #dee2e6;
    }
  </style>
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

      <!-- Sección de Firma Digital -->
      <div class="firma-container" id="firma-section" style="display: none;">
        <div class="form-group">
          <label><strong>Firma Digital:</strong></label>
          <p class="tx-12 tx-gray-600 mg-b-10">Dibuje su firma en el recuadro. Se utilizará en los presupuestos impresos.</p>
          
          <canvas id="firma-canvas" class="firma-canvas" width="260" height="150"></canvas>
          
          <div class="firma-buttons">
            <button type="button" id="btn-limpiar-firma" class="btn btn-secondary btn-sm">
              <i class="fa fa-eraser"></i> Limpiar
            </button>
            <button type="button" id="btn-guardar-firma" class="btn btn-primary btn-sm">
              <i class="fa fa-save"></i> Guardar Firma
            </button>
          </div>
          
          <div id="firma-status" class="mg-t-10 tx-12"></div>
        </div>
        
        <!-- Vista previa de firma existente -->
        <div id="firma-preview" class="firma-preview" style="display: none;">
          <p class="tx-12 tx-semibold mg-b-5">Firma actual:</p>
          <img id="firma-preview-img" src="" alt="Firma actual">
        </div>
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
