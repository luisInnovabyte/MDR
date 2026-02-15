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
  <title>Debug - Logs de Firma - MDR</title>
  <style>
    .log-container {
      font-family: 'Courier New', monospace;
      font-size: 13px;
      background-color: #1e1e1e;
      color: #d4d4d4;
      padding: 20px;
      border-radius: 8px;
      max-height: 600px;
      overflow-y: auto;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
    .log-line {
      padding: 2px 0;
      border-bottom: 1px solid #333;
    }
    .log-INFO { color: #4EC9B0; }
    .log-ERROR { color: #f48771; background-color: #442222; }
    .log-WARNING { color: #f9d423; }
    .log-SUCCESS { color: #7ec699; font-weight: bold; }
    .timestamp { color: #858585; }
    .log-type { 
      font-weight: bold;
      padding: 2px 6px;
      border-radius: 3px;
      margin: 0 5px;
    }
    .btn-refresh {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
    }
  </style>
</head>

<body>
  <?php include_once('../../config/template/mainHeader.php') ?>
  <?php include_once('../../config/template/mainSidebar.php') ?>

  <div class="br-mainpanel">
    <div class="br-pageheader pd-y-15 pd-l-20">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <a class="breadcrumb-item" href="perfil.php">Perfil</a>
            <span class="breadcrumb-item active">Debug - Logs de Firma</span>
          </nav>
          <h4 class="br-pageheader-title mg-t-5">Debug - Logs de Firma Digital</h4>
        </div>
        <button class="btn btn-primary btn-sm" onclick="location.reload()">
          <i class="fa fa-refresh"></i> Actualizar
        </button>
      </div>
    </div>

    <div class="br-pagebody">
      <div class="br-section-wrapper">
        <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-20">
          <i class="fa fa-bug"></i> Registro de Debug - <?= date('d/m/Y') ?>
        </h6>

        <div class="alert alert-info">
          <strong><i class="fa fa-info-circle"></i> Información:</strong><br>
          Este archivo muestra los logs de debug relacionados con la funcionalidad de firma digital.<br>
          - <strong>Ubicación:</strong> public/logs/firma_debug_<?= date('Y-m-d') ?>.log<br>
          - <strong>Auto-actualiza:</strong> Recarga la página para ver nuevos logs
        </div>

        <?php
        $log_file = __DIR__ . '/../../public/logs/firma_debug_' . date('Y-m-d') . '.log';
        
        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
            
            if (!empty($log_content)) {
                echo '<div class="log-container">';
                
                $lines = explode("\n", $log_content);
                $lines = array_reverse($lines); // Mostrar más recientes primero
                
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    // Colorear según el tipo de log
                    $class = '';
                    if (strpos($line, '[ERROR]') !== false) {
                        $class = 'log-ERROR';
                    } elseif (strpos($line, '[WARNING]') !== false) {
                        $class = 'log-WARNING';
                    } elseif (strpos($line, '[SUCCESS]') !== false) {
                        $class = 'log-SUCCESS';
                    } else {
                        $class = 'log-INFO';
                    }
                    
                    echo '<div class="log-line ' . $class . '">' . htmlspecialchars($line) . '</div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> El archivo de log está vacío.
                      </div>';
            }
        } else {
            echo '<div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> No se encontró el archivo de log para hoy.<br>
                    <strong>Ruta esperada:</strong> ' . htmlspecialchars($log_file) . '<br>
                    <em>El archivo se creará automáticamente cuando se ejecute una acción relacionada con firma.</em>
                  </div>';
        }
        ?>

        <div class="mg-t-20">
          <h6 class="tx-gray-800 tx-uppercase tx-bold tx-14 mg-b-10">
            <i class="fa fa-lightbulb-o"></i> Acciones Recomendadas
          </h6>
          <ol class="tx-13">
            <li>Ve a <a href="perfil.php">tu perfil</a> y dibuja/guarda una firma</li>
            <li>Vuelve a esta página para ver los logs generados</li>
            <li>Crea un presupuesto y genera el PDF</li>
            <li>Revisa los logs para verificar si la firma se obtuvo correctamente</li>
          </ol>
        </div>

        <div class="mg-t-20">
          <a href="perfil.php" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Volver al Perfil
          </a>
          <a href="../Dashboard/index.php" class="btn btn-secondary">
            <i class="fa fa-home"></i> Ir al Dashboard
          </a>
        </div>

      </div>
    </div>

    <?php include_once('../../config/template/mainFooter.php') ?>
  </div>

  <?php include_once('../../config/template/mainJs.php') ?>
  
  <script>
    // Auto-recargar cada 10 segundos si el usuario quiere
    let autoRefresh = false;
    
    $(document).ready(function() {
      // Agregar botón de auto-refresh
      $('.br-pageheader').append(
        '<button class="btn btn-sm btn-outline-primary mg-l-5" id="toggle-auto-refresh">' +
        '<i class="fa fa-clock-o"></i> Auto-refresh OFF' +
        '</button>'
      );
      
      $('#toggle-auto-refresh').on('click', function() {
        autoRefresh = !autoRefresh;
        $(this).html(
          '<i class="fa fa-clock-o"></i> Auto-refresh ' + (autoRefresh ? 'ON' : 'OFF')
        );
        $(this).toggleClass('btn-outline-primary btn-success');
        
        if (autoRefresh) {
          startAutoRefresh();
        }
      });
      
      function startAutoRefresh() {
        if (autoRefresh) {
          setTimeout(function() {
            location.reload();
          }, 10000); // 10 segundos
        }
      }
    });
  </script>
</body>
</html>
