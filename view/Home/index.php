<!DOCTYPE html>
<html lang="es">

<head>
  <?php include_once '../../config/template/mainHead.php'; ?>
</head>

<body>

  <div class="d-flex align-items-center justify-content-center ht-100v" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); position: relative; overflow: hidden;">
    
    <!-- Elementos decorativos de fondo -->
    <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%); animation: pulse 15s ease-in-out infinite;"></div>
    <div style="position: absolute; top: 20%; right: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(96, 165, 250, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px);"></div>
    <div style="position: absolute; bottom: 10%; left: 15%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%); border-radius: 50%; filter: blur(80px);"></div>

    <div class="login-wrapper" style="width: 440px; max-width: 95%; padding: 3rem 2.5rem; position: relative; z-index: 10;">
      <!-- Tarjeta con efecto glassmorphism -->
      <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 20px; overflow: hidden;">
        
        <!-- Encabezado con gradiente -->
        <div class="card-header border-0 text-center py-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
          <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                 style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2);">
              <img src="../../config/template/Logo.png" alt="AssetTrack Logo" style="height: 50px; width: auto;">
            </div>
          </div>
          <h4 class="text-white fw-bold mb-1">AssetTrack ERP Manager</h4>
          <p class="text-white-50 mb-0 small">Sistema de Gestión Empresarial</p>
        </div>

        <!-- Cuerpo del formulario -->
        <div class="card-body px-4 py-4">
          <div class="text-center mb-4">
            <h5 class="fw-bold mb-1" style="color: #2a5298;">Iniciar Sesión</h5>
            <p class="text-muted small mb-0">Ingresa tus credenciales para continuar</p>
          </div>

          <form id="formLogin" name="formLogin" method="POST">

            <div class="form-group mb-3">
              <label for="email" class="form-label small fw-semibold text-muted mb-2">
                <i class="bi bi-envelope me-1"></i>Correo electrónico
              </label>
              <input type="text" class="form-control form-control-lg" name="email" id="email" 
                     placeholder="tu@email.com" autofocus
                     style="border-radius: 10px; border: 2px solid #e9ecef; padding: 0.75rem 1rem;">
              <div class="invalid-feedback small">Por favor, introduce un email válido</div>
            </div>

            <div class="form-group mb-3">
              <label for="contrasena" class="form-label small fw-semibold text-muted mb-2">
                <i class="bi bi-lock me-1"></i>Contraseña
              </label>
              <div class="input-group">
                <input type="password" class="form-control form-control-lg" name="contrasena" id="contrasena" 
                       placeholder="••••••••"
                       style="border-radius: 10px 0 0 10px; border: 2px solid #e9ecef; padding: 0.75rem 1rem; border-right: none;">
                <button class="btn btn-outline-secondary" type="button" id="verContrasena"
                        style="border-radius: 0 10px 10px 0; border: 2px solid #e9ecef; border-left: none; background: white;">
                  <i class="fa fa-eye text-muted" aria-hidden="true"></i>
                </button>
              </div>
              <div class="invalid-feedback small">Contraseña no válida (mínimo 1 letra + 2 números)</div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="recordarme">
                <label class="form-check-label small text-muted" for="recordarme">
                  Recordarme
                </label>
              </div>
              <a href="../RecuperarPass" class="small text-decoration-none" style="color: #2a5298;">
                ¿Olvidaste tu contraseña?
              </a>
            </div>

            <button type="submit" id="enviar" name="enviar" class="btn btn-lg w-100 text-white fw-semibold mb-3" 
                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none; border-radius: 10px; padding: 0.75rem;">
              <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>

            <div class="text-center">
              <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>Conexión segura y cifrada
              </small>
            </div>

          </form>
        </div>

        <!-- Pie del card -->
        <div class="card-footer border-0 bg-light text-center py-3">
          <small class="text-muted">
            © 2026 AssetTrack. Todos los derechos reservados.
          </small>
        </div>

      </div>
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

  <style>
    @keyframes pulse {
      0%, 100% {
        transform: scale(1) rotate(0deg);
        opacity: 0.5;
      }
      50% {
        transform: scale(1.1) rotate(180deg);
        opacity: 0.8;
      }
    }
    
    /* Efecto hover en inputs */
    .form-control:focus {
      border-color: #3b82f6 !important;
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15) !important;
      transform: translateY(-2px);
      transition: all 0.3s ease;
    }
    
    /* Efecto hover en botón */
    #enviar:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(30, 60, 114, 0.4);
      transition: all 0.3s ease;
    }
    
    /* Animación de entrada del card */
    .card {
      animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

</body>

</html>