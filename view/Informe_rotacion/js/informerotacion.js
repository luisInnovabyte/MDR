/**
 * Informe de Rotación de Inventario - JavaScript
 * MDR ERP Manager
 */

const CTRL = '../../controller/informerotacion.php';
let tablaRotacion = null;
let graficoTop = null;

// ─────────────────────────────────────────────────────────────────────────────
// INICIALIZACIÓN
// ─────────────────────────────────────────────────────────────────────────────
$(document).ready(function () {
    cargarFamilias();
    recargarTodo();
});

// ─────────────────────────────────────────────────────────────────────────────
// FILTROS
// ─────────────────────────────────────────────────────────────────────────────
function aplicarFiltros() {
    recargarTodo();
}

function limpiarFiltros() {
    $('#filtroPeriodo').val('90');
    $('#filtroFamilia').val('');
    recargarTodo();
}

function recargarTodo() {
    const dias     = parseInt($('#filtroPeriodo').val()) || 90;
    const familia  = $('#filtroFamilia').val() || '';

    actualizarSubPeriodo(dias);
    cargarKPIs(dias);
    cargarGrafico(dias);
    inicializarTabla(dias, familia);
}

function actualizarSubPeriodo(dias) {
    let label;
    switch (dias) {
        case 90:  label = 'en los últimos 90 días'; break;
        case 180: label = 'en los últimos 180 días'; break;
        case 365: label = 'en el último año';       break;
        case 0:   label = 'histórico completo';     break;
        default:  label = `en los últimos ${dias} días`;
    }
    $('#kpi-periodo-sub').text(label);
}

// ─────────────────────────────────────────────────────────────────────────────
// FAMILIAS (desplegable)
// ─────────────────────────────────────────────────────────────────────────────
function cargarFamilias() {
    $.post(CTRL + '?op=familias', {})
        .done(function (resp) {
            const $sel = $('#filtroFamilia');
            $sel.find('option:not(:first)').remove();
            if (Array.isArray(resp)) {
                resp.forEach(function (f) {
                    $sel.append(
                        $('<option>').val(f.id_familia).text(f.nombre_familia)
                    );
                });
            }
        });
}

// ─────────────────────────────────────────────────────────────────────────────
// KPIs
// ─────────────────────────────────────────────────────────────────────────────
function cargarKPIs(dias) {
    $('#kpi-total, #kpi-usados, #kpi-pct, #kpi-sin-uso').text('…');

    $.post(CTRL + '?op=kpis', { dias_periodo: dias })
        .done(function (resp) {
            if (!resp || resp.error) {
                $('#kpi-total, #kpi-usados, #kpi-pct, #kpi-sin-uso').text('—');
                return;
            }
            $('#kpi-total').text(Number(resp.total_articulos).toLocaleString('es-ES'));
            $('#kpi-usados').text(Number(resp.articulos_usados).toLocaleString('es-ES'));
            $('#kpi-sin-uso').text(Number(resp.articulos_sin_uso).toLocaleString('es-ES'));

            const pct = parseFloat(resp.porcentaje_uso) || 0;
            $('#kpi-pct').text(pct.toFixed(1) + ' %');
        })
        .fail(function () {
            $('#kpi-total, #kpi-usados, #kpi-pct, #kpi-sin-uso').text('Error');
        });
}

// ─────────────────────────────────────────────────────────────────────────────
// GRÁFICO — Top 10 artículos (barras horizontales)
// ─────────────────────────────────────────────────────────────────────────────
function cargarGrafico(dias) {
    $.post(CTRL + '?op=top_articulos', { dias_periodo: dias, limite: 10 })
        .done(function (resp) {
            if (!Array.isArray(resp) || resp.length === 0) {
                if (graficoTop) { graficoTop.destroy(); graficoTop = null; }
                return;
            }

            const labels = resp.map(function (r) { return r.nombre_articulo; });
            const datos  = resp.map(function (r) { return parseInt(r.total_usos) || 0; });

            const ctx = document.getElementById('chartTopArticulos').getContext('2d');

            if (graficoTop) {
                graficoTop.data.labels            = labels;
                graficoTop.data.datasets[0].data  = datos;
                graficoTop.update();
                return;
            }

            graficoTop = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Número de alquileres',
                        data: datos,
                        backgroundColor: 'rgba(13, 110, 253, 0.75)',
                        borderColor:     'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',          // barras horizontales
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.parsed.x + ' usos';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(0,0,0,0.06)' }
                        },
                        y: {
                            ticks: {
                                font: { size: 11 },
                                callback: function (val, idx) {
                                    const lbl = this.getLabelForValue(val);
                                    return lbl.length > 28 ? lbl.substring(0, 26) + '…' : lbl;
                                }
                            }
                        }
                    }
                }
            });
        });
}

// ─────────────────────────────────────────────────────────────────────────────
// TABLA PRINCIPAL
// ─────────────────────────────────────────────────────────────────────────────
function inicializarTabla(dias, familia) {
    const postData = { dias_periodo: dias, id_familia: familia };

    if (tablaRotacion) {
        tablaRotacion.destroy();
        tablaRotacion = null;
        $('#tblRotacion tbody').empty();
    }

    tablaRotacion = $('#tblRotacion').DataTable({
        processing: true,
        serverSide: false,          // datos cargados de una vez
        ajax: {
            url:  CTRL + '?op=tabla_rotacion',
            type: 'POST',
            data: postData,
            dataSrc: 'data'
        },
        columns: [
            { data: 'codigo_articulo',          width: '10%' },
            { data: 'nombre_articulo' },
            { data: 'nombre_familia',            width: '14%' },
            { data: 'total_usos',                width: '8%',  className: 'text-center', type: 'num' },
            { data: 'total_unidades_alquiladas', width: '9%',  className: 'text-center', type: 'num' },
            { data: 'ultimo_uso',                width: '11%',
              render: function (data) {
                  return data ? data : '<span class="text-muted">—</span>';
              }
            },
            { data: 'dias_desde_ultimo_uso',     width: '9%',  className: 'text-center', type: 'num',
              render: function (data) {
                  if (data === null || data === '' || data === undefined) {
                      return '<span class="text-muted">—</span>';
                  }
                  return data;
              }
            },
            { data: 'estado_rotacion',           width: '10%', className: 'text-center',
              orderable: false
            }
        ],
        language: {
            url: '../../public/lib/DataTables/es-ES.json'
        },
        order:    [[3, 'desc']],     // ordenar por usos descendente por defecto
        pageLength: 25,
        responsive: true
    });
}
