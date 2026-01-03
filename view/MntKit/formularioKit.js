$(document).ready(function() {
    // Actualizar precio unitario al seleccionar artículo
    $('#id_articulo_componente').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const precio = selectedOption.data('precio') || 0;
        $('#precio_unitario_display').val(parseFloat(precio).toFixed(2));
        calcularSubtotal();
    });

    // Actualizar subtotal al cambiar cantidad
    $('#cantidad_kit').on('input', function() {
        calcularSubtotal();
    });

    // Función para calcular subtotal
    function calcularSubtotal() {
        const precio = parseFloat($('#precio_unitario_display').val()) || 0;
        const cantidad = parseFloat($('#cantidad_kit').val()) || 0;
        const subtotal = precio * cantidad;
        $('#subtotal_display').text(subtotal.toFixed(2) + ' €');
    }

    // Limpiar formulario al cerrar modal
    $('#modalFormularioKit').on('hidden.bs.modal', function() {
        $('#frmKit')[0].reset();
        $('#precio_unitario_display').val('');
        $('#subtotal_display').text('0.00 €');
    });
});
