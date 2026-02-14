// Variables globales
let signaturePad;

$(document).ready(function () {
  // Manejo del botón cerrar sesión
  $('a.btn-danger').on('click', function (e) {
    e.preventDefault(); // Prevenir la redirección directa

    $.ajax({
      url: '../../controller/login.php?op=cerrarSesion',
      type: 'GET',
      dataType: 'json',
      success: function (response) {
        if (response.success) {
          toastr.info(response.message, 'Sesión cerrada');

          // Esperar unos segundos antes de redirigir
          setTimeout(() => {
            window.location.href = './index.php'; // Redirigir al login
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

  // ========================================
  // INICIALIZACIÓN DE FIRMA DIGITAL
  // ========================================
  
  // Verificar si el usuario es comercial y cargar firma
  verificarYCargarFirma();

  // Inicializar Signature Pad
  const canvas = document.getElementById('firma-canvas');
  if (canvas) {
    signaturePad = new SignaturePad(canvas, {
      backgroundColor: 'rgb(255, 255, 255)',
      penColor: 'rgb(0, 0, 0)'
    });

    // Ajustar el tamaño del canvas para que sea responsive
    function resizeCanvas() {
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
      signaturePad.clear(); // Limpiar después de redimensionar
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
  }

  // Botón limpiar firma
  $('#btn-limpiar-firma').on('click', function() {
    if (signaturePad) {
      signaturePad.clear();
      $('#firma-status').html('');
      toastr.info('Canvas limpiado');
    }
  });

  // Botón guardar firma
  $('#btn-guardar-firma').on('click', function() {
    guardarFirma();
  });
});

/**
 * Verificar si el usuario es comercial y mostrar/cargar la firma
 */
function verificarYCargarFirma() {
  $.ajax({
    url: '../../controller/ajax_obtener_firma.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        // El usuario es comercial, mostrar la sección de firma
        $('#firma-section').show();
        
        // Si tiene firma guardada, mostrar vista previa
        if (response.tiene_firma && response.firma_base64) {
          $('#firma-preview-img').attr('src', response.firma_base64);
          $('#firma-preview').show();
          $('#firma-status').html('<span class="tx-success"><i class="fa fa-check-circle"></i> Tiene firma guardada</span>');
        } else {
          $('#firma-status').html('<span class="tx-info"><i class="fa fa-info-circle"></i> No tiene firma guardada</span>');
        }
      } else {
        // El usuario no es comercial, ocultar la sección
        $('#firma-section').hide();
      }
    },
    error: function() {
      // En caso de error, ocultar la sección
      $('#firma-section').hide();
    }
  });
}

/**
 * Guardar la firma en la base de datos
 */
function guardarFirma() {
  if (!signaturePad) {
    toastr.error('Error: Canvas de firma no inicializado');
    return;
  }

  // Validar que haya algo dibujado
  if (signaturePad.isEmpty()) {
    toastr.warning('Por favor, dibuje su firma antes de guardar');
    return;
  }

  // Obtener la firma como base64 PNG
  const firmaBase64 = signaturePad.toDataURL('image/png');

  // Mostrar indicador de carga
  $('#btn-guardar-firma').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

  // Enviar al servidor
  $.ajax({
    url: '../../controller/ajax_guardar_firma.php',
    type: 'POST',
    data: {
      firma_base64: firmaBase64
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        toastr.success(response.message, 'Firma guardada');
        
        // Actualizar vista previa
        $('#firma-preview-img').attr('src', firmaBase64);
        $('#firma-preview').show();
        $('#firma-status').html('<span class="tx-success"><i class="fa fa-check-circle"></i> Firma guardada correctamente</span>');
        
        // Limpiar el canvas
        signaturePad.clear();
      } else {
        toastr.error(response.message, 'Error al guardar');
        $('#firma-status').html('<span class="tx-danger"><i class="fa fa-exclamation-circle"></i> ' + response.message + '</span>');
      }
    },
    error: function() {
      toastr.error('Error de comunicación con el servidor', 'Error');
      $('#firma-status').html('<span class="tx-danger"><i class="fa fa-exclamation-circle"></i> Error de comunicación</span>');
    },
    complete: function() {
      // Restaurar botón
      $('#btn-guardar-firma').prop('disabled', false).html('<i class="fa fa-save"></i> Guardar Firma');
    }
  });
}
