'use strict';

let tabla = null;

$(document).ready(function () {
    inicializarTabla();
});

function inicializarTabla() {
    tabla = $('#tblAlbaranesCarga').DataTable({
        ajax: {
            url    : '../../controller/albaranesCarga.php?op=listar',
            type   : 'POST',
            dataSrc: 'data',
            error  : function (xhr, error, code) {
                console.error('Error AJAX AlbaranesCarga:', error, code);
                Swal.fire('Error', 'No se pudo cargar la tabla de albaranes.', 'error');
            },
        },
        columns: [
            { data: 'numero_presupuesto' },
            { data: 'nombre_evento_presupuesto' },
            { data: 'nombre_cliente' },
            { data: 'fecha_evento' },
            { data: 'fecha_montaje' },
            { data: 'fecha_desmontaje' },
            { data: 'opciones', orderable: false, className: 'text-center' },
        ],
        scrollX       : true,
        scrollCollapse: true,
        order         : [[3, 'asc']],
        language: {
            paginate: {
                first   : '<i class="bi bi-chevron-double-left"></i>',
                last    : '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next    : '<i class="bi bi-chevron-compact-right"></i>',
            },
        },
        responsive: false,
    });
}

/**
 * Abre el albarán indicado en una nueva pestaña mediante POST dinámico.
 * @param {number} idPresupuesto
 * @param {string} op  'albaran_carga_m2' | 'albaran_carga_resumido'
 */
function abrirAlbaran(idPresupuesto, op) {
    const url  = '../../controller/impresionpartetrabajo_m2_pdf_es.php?op=' + encodeURIComponent(op);
    const form = $('<form method="POST" target="_blank"></form>').attr('action', url);
    form.append($('<input type="hidden" name="id_presupuesto">').val(idPresupuesto));
    $('body').append(form);
    form[0].submit();
    form.remove();
}
