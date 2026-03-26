/**
 * fichaElemento.js
 * Lógica del módulo Ficha de Elementos
 */

'use strict';

// ─── Estado ─────────────────────────────────────────────────────────────────
let tablaPresupuestos = null;
let chartSalidas      = null;
let elementoActual    = null;
let todosElementos    = [];  // caché para el buscador

// ─── Init ────────────────────────────────────────────────────────────────────
$(document).ready(function () {
    cargarListaElementos();

    // Buscador en tiempo real
    $('#buscador-elemento').on('input', function () {
        filtrarLista($(this).val().trim().toLowerCase());
    });

    // Cuando se activa el tab de presupuestos → carga si hay elemento seleccionado
    $('#tab-presupuestos-btn').on('shown.bs.tab', function () {
        if (elementoActual) cargarPresupuestos(elementoActual);
    });

    // Cuando se activa el tab de gráfico → carga si hay elemento seleccionado
    $('#tab-grafico-btn').on('shown.bs.tab', function () {
        if (elementoActual) cargarGrafico(elementoActual);
    });
});

// ─── 1. Lista de elementos ────────────────────────────────────────────────────
function cargarListaElementos() {
    $('#spinner-lista').show();
    $('#lista-elementos').empty();

    $.post('../../controller/elemento.php?op=listar', {})
        .done(function (res) {
            $('#spinner-lista').hide();

            if (!res || !res.data || res.data.length === 0) {
                $('#lista-elementos').html('<p class="pd-15 tx-gray-400 tx-13">Sin elementos</p>');
                return;
            }

            todosElementos = res.data;
            renderLista(todosElementos);
        })
        .fail(function () {
            $('#spinner-lista').hide();
            $('#lista-elementos').html('<p class="pd-15 tx-danger tx-13">Error al cargar elementos</p>');
        });
}

function renderLista(elementos) {
    const $lista = $('#lista-elementos').empty();

    elementos.forEach(function (el) {
        const color = el.color_estado_elemento || '#6c757d';
        const $item = $(`
            <div class="el-item" data-id="${el.id_elemento}">
                <div class="el-nombre">
                    <span class="el-estado-dot" style="background:${color};"></span>
                    ${htmlEscape(el.nombre_articulo || el.descripcion_elemento || '—')}
                </div>
                <div class="el-codigo">${htmlEscape(el.codigo_elemento || '')}</div>
            </div>
        `);
        $item.on('click', function () {
            $('.el-item').removeClass('active');
            $(this).addClass('active');
            seleccionarElemento(el.id_elemento);
        });
        $lista.append($item);
    });
}

function filtrarLista(texto) {
    if (!texto) {
        renderLista(todosElementos);
        return;
    }
    const filtrados = todosElementos.filter(function (el) {
        const nombre  = (el.nombre_articulo || el.descripcion_elemento || '').toLowerCase();
        const codigo  = (el.codigo_elemento || '').toLowerCase();
        return nombre.includes(texto) || codigo.includes(texto);
    });
    renderLista(filtrados);
}

// ─── 2. Seleccionar elemento ──────────────────────────────────────────────────
function seleccionarElemento(id_elemento) {
    elementoActual = id_elemento;

    // Mostrar el panel ficha
    $('#ficha-placeholder').hide();
    $('#ficha-detalle').show();

    // Resetear tabs al primero
    $('#tab-info-btn').tab('show');

    // Obtener detalle del elemento
    $.post('../../controller/elemento.php?op=mostrar', { id_elemento: id_elemento })
        .done(function (data) {
            if (data && data.id_elemento) {
                renderInfo(data);
            }
        });
}

// ─── 3. Renderizar pestaña Info ───────────────────────────────────────────────
function renderInfo(d) {
    // Cabecera
    const nombre = d.nombre_articulo || d.descripcion_elemento || '—';
    $('#fh-nombre').text(nombre);
    $('#fh-codigo').text(d.codigo_elemento || '—');

    const color  = d.color_estado_elemento || '#6c757d';
    const estado = d.descripcion_estado_elemento || '—';
    $('#fh-estado-badge').text(estado).css('background-color', color);

    // Campos info
    $('#fi-descripcion').text(d.descripcion_elemento  || '—');
    $('#fi-jerarquia').text(d.jerarquia_completa_elemento || [d.nombre_familia, d.nombre_articulo].filter(Boolean).join(' › ') || '—');
    $('#fi-marca-modelo').text([d.nombre_marca, d.modelo_elemento].filter(Boolean).join(' / ') || '—');
    $('#fi-serie').text(d.numero_serie_elemento || '—');
    $('#fi-codbarras').text(d.codigo_barras_elemento || '—');
    $('#fi-propiedad').text(d.tipo_propiedad_elemento || '—');
    $('#fi-ubicacion').text(d.ubicacion_completa_elemento || '—');
    $('#fi-fecha-compra').text(formatFecha(d.fecha_compra_elemento));
    $('#fi-precio-compra').text(d.precio_compra_elemento != null ? formatMoneda(d.precio_compra_elemento) : '—');
    $('#fi-garantia').text(formatFecha(d.fecha_fin_garantia_elemento));
    $('#fi-mantenimiento').text(formatFecha(d.proximo_mantenimiento_elemento));
    $('#fi-dias').text(d.dias_en_servicio_elemento != null ? d.dias_en_servicio_elemento + ' días' : '—');
    $('#fi-prov-compra').text(d.nombre_proveedor_compra || '—');
    $('#fi-prov-alquiler').text(d.nombre_proveedor_alquiler || '—');
    $('#fi-observaciones').text(d.observaciones_elemento || '—');

    // Resetear contadores (se actualizan cuando el usuario abre cada tab)
    $('#cnt-presupuestos').text('—');

    // Si el tab de presupuestos ya está activo, recargar la tabla
    if ($('#tab-presupuestos-btn').hasClass('active') && elementoActual) {
        cargarPresupuestos(elementoActual);
    }
}

// ─── 4. Pestaña Presupuestos ──────────────────────────────────────────────────
function cargarPresupuestos(id_elemento) {

    // Si la tabla ya existe, solo cambia la URL y recarga
    if (tablaPresupuestos) {
        tablaPresupuestos.ajax
            .url('../../controller/elemento.php?op=historial_presupuestos&id_elemento=' + id_elemento)
            .load(function (json) {
                $('#cnt-presupuestos').text(json ? (json.recordsTotal || 0) : 0);
            });
        return;
    }

    // Primera inicialización
    tablaPresupuestos = $('#tblPresupuestos').DataTable({
        ajax: {
            url: '../../controller/elemento.php?op=historial_presupuestos&id_elemento=' + id_elemento,
            type: 'POST',
            dataSrc: function (json) {
                $('#cnt-presupuestos').text(json.recordsTotal || 0);
                return json.data || [];
            }
        },
        columns: [
            { data: 'numero_presupuesto',        title: 'Nº' },
            { data: 'nombre_evento_presupuesto',  title: 'Evento' },
            { data: 'nombre_cliente',             title: 'Cliente' },
            { data: 'fecha_salida',               title: 'Fecha salida' },
            { data: 'estado_badge',               title: 'Estado',  orderable: false }
        ],
        order: [[3, 'desc']],
        pageLength: 10,
        layout: {
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: {
                paging: { firstLast: true, numbers: true, previousNext: true }
            }
        },
        language: {
            paginate: { first: '«', last: '»', previous: '‹', next: '›' }
        }
    });
}

// ─── 5. Pestaña Gráfico ───────────────────────────────────────────────────────
function cargarGrafico(id_elemento) {
    $('#spinner-chart').show();
    $('#chart-empty').hide();

    $.ajax({
        url: '../../controller/elemento.php?op=salidas_por_mes',
        method: 'POST',
        dataType: 'json',
        data: { id_elemento: id_elemento },
        success: function (res) {
            $('#spinner-chart').hide();

            const datos = (res && res.success && Array.isArray(res.data)) ? res.data : [];

            // Destruir gráfico anterior
            if (chartSalidas) {
                chartSalidas.destroy();
                chartSalidas = null;
            }

            if (datos.length === 0) {
                $('#chart-empty').show();
                return;
            }

            const labels = datos.map(function (d) { return d.mes_label; });
            const values = datos.map(function (d) { return parseInt(d.num_presupuestos, 10); });

            const ctx = document.getElementById('chartSalidas').getContext('2d');
            chartSalidas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Presupuestos',
                        data: values,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.parsed.y + ' presupuesto' + (ctx.parsed.y !== 1 ? 's' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0 }
                        }
                    }
                }
            });
        },
        error: function () {
            $('#spinner-chart').hide();
        }
    });
}

// ─── Utilidades ───────────────────────────────────────────────────────────────
function formatFecha(val) {
    if (!val) return '—';
    const d = new Date(val);
    if (isNaN(d.getTime())) return val;
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatMoneda(val) {
    return parseFloat(val).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
}

function htmlEscape(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
