$(document).ready(function () {
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
            window.location.href = './view/Home'; // Redirección correcta en JS
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
});
