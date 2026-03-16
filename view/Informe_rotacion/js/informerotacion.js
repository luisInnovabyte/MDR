/**
 * Informe de Rotación de Inventario - JavaScript
 * MDR ERP Manager
 */

const CTRL = '../../controller/informerotacion.php';
let tablaRotacion    = null;
let graficoTop       = null;
let graficoFamilias  = null;
let graficoTendencia = null;

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
    const diasStr = $('#filtroPeriodo').val();
    const dias    = (diasStr === null || diasStr === '') ? 90 : parseInt(diasStr, 10);
    const familia = $('#filtroFamilia').val() || '';
    const tipo    = $('#selectorGrafico').val() || 'top10';

    actualizarSubPeriodo(dias);
    cargarKPIs(dias, familia);
    cargarGrafico(tipo, dias, familia);
    inicializarTabla(dias, familia);
}

function cambiarGrafico() {
    const diasStr = $('#filtroPeriodo').val();
    const dias    = (diasStr === null || diasStr === '') ? 90 : parseInt(diasStr, 10);
    const familia = $('#filtroFamilia').val() || '';
    const tipo    = $('#selectorGrafico').val() || 'top10';
    cargarGrafico(tipo, dias, familia);
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
function cargarKPIs(dias, familia) {
    $('#kpi-total, #kpi-usados, #kpi-pct, #kpi-sin-uso').text('…');

    $.post(CTRL + '?op=kpis', { dias_periodo: dias, id_familia: familia || '' })
        .done(function (resp) {
            if (!resp || resp.error || !resp.total_articulos) {
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
// GRÁFICO — Dispatcher según tipo seleccionado
// ─────────────────────────────────────────────────────────────────────────────
function cargarGrafico(tipo, dias, familia) {
    $('#vistaTop, #vistaFamilias, #vistaTendencia').hide();
    switch (tipo) {
        case 'familias':  $('#vistaFamilias').show();  cargarGraficoFamilias(dias);     break;
        case 'tendencia': $('#vistaTendencia').show(); cargarGraficoTendencia();        break;
        default:          $('#vistaTop').show();       cargarGraficoTop(dias, familia); break;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// GRÁFICO — Top 10 artículos (barras horizontales)
// ─────────────────────────────────────────────────────────────────────────────
function cargarGraficoTop(dias, familia) {
    $.post(CTRL + '?op=top_articulos', { dias_periodo: dias, limite: 10, id_familia: familia || '' })
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
            { data: 'nombre_familia',            width: '13%' },
            { data: 'total_usos',                width: '7%',  className: 'text-center', type: 'num' },
            { data: 'total_unidades_alquiladas', width: '8%',  className: 'text-center', type: 'num' },
            { data: 'ultimo_uso',                width: '10%',
              render: function (data) {
                  // PHP ya envia la fecha formateada como DD/MM/YYYY o '—'
                  if (!data || data === '—') return '<span class="text-muted">—</span>';
                  return data;
              }
            },
            { data: 'dias_desde_ultimo_uso',     width: '8%',  className: 'text-center', type: 'num',
              render: function (data) {
                  if (data === null || data === '' || data === undefined) {
                      return '<span class="text-muted">—</span>';
                  }
                  return data;
              }
            },
            { data: 'tendencia',                 width: '8%',  className: 'text-center', orderable: false },
            { data: 'estado_rotacion',           width: '9%',  className: 'text-center', orderable: false }
        ],
        language: {
            sProcessing:   "Procesando...",
            sLengthMenu:   "Mostrar _MENU_ registros",
            sZeroRecords:  "No se encontraron resultados",
            sEmptyTable:   "Ningún dato disponible en esta tabla",
            sInfo:         "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty:    "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch:       "Buscar:",
            sUrl:          "",
            oPaginate: {
                sFirst:    "«",
                sLast:     "»",
                sNext:     "›",
                sPrevious: "‹"
            }
        },
        order:    [[3, 'desc']],
        pageLength: 25,
        responsive: true
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// GRÁFICO — Resumen por familia (donut)
// ─────────────────────────────────────────────────────────────────────────────
function cargarGraficoFamilias(dias) {
    $.post(CTRL + '?op=resumen_familias', { dias_periodo: dias })
        .done(function (resp) {
            if (!Array.isArray(resp) || resp.length === 0) {
                if (graficoFamilias) { graficoFamilias.destroy(); graficoFamilias = null; }
                return;
            }

            const labels  = resp.map(function (r) { return r.nombre_familia; });
            const datos   = resp.map(function (r) { return parseInt(r.total_usos) || 0; });
            const colores = generarColores(resp.length);

            const ctx = document.getElementById('chartFamilias').getContext('2d');

            // Poblar tabla de desglose (siempre, tanto en creación como en actualización)
            var total = datos.reduce(function (a, b) { return a + b; }, 0);
            var tbodyHtml = '';
            var sortedIdx = datos.map(function (v, i) { return i; })
                                 .sort(function (a, b) { return datos[b] - datos[a]; });
            sortedIdx.forEach(function (i) {
                var pct = total > 0 ? ((datos[i] / total) * 100).toFixed(1) : '0.0';
                var barW = total > 0 ? Math.round((datos[i] / total) * 100) : 0;
                tbodyHtml += '<tr>' +
                    '<td><span class="me-2" style="display:inline-block;width:12px;height:12px;border-radius:50%;background:' + colores[i % colores.length] + '"></span>' + labels[i] + '</td>' +
                    '<td class="text-center">' + datos[i].toLocaleString('es-ES') + '</td>' +
                    '<td class="text-center">' +
                        '<div class="d-flex align-items-center gap-1">' +
                            '<div class="flex-grow-1" style="background:#e9ecef;border-radius:4px;height:8px;">' +
                                '<div style="width:' + barW + '%;background:' + colores[i % colores.length] + ';height:8px;border-radius:4px;"></div>' +
                            '</div>' +
                            '<span class="text-nowrap" style="min-width:42px">' + pct + ' %</span>' +
                        '</div>' +
                    '</td>' +
                    '</tr>';
            });
            var footHtml = '<tr class="table-light fw-semibold">' +
                '<td>Total</td>' +
                '<td class="text-center">' + total.toLocaleString('es-ES') + '</td>' +
                '<td class="text-center">100 %</td>' +
                '</tr>';
            $('#tblFamiliasBody').html(tbodyHtml);
            $('#tblFamiliasFoot').html(footHtml);

            // Construir etiquetas de leyenda con % incluido
            var totalC = datos.reduce(function (a, b) { return a + b; }, 0);
            var labelsConPct = labels.map(function (lbl, i) {
                var pctL = totalC > 0 ? ((datos[i] / totalC) * 100).toFixed(1) : '0.0';
                return lbl + '  (' + pctL + ' %)';
            });

            if (graficoFamilias) {
                graficoFamilias.data.labels                      = labelsConPct;
                graficoFamilias.data.datasets[0].data            = datos;
                graficoFamilias.data.datasets[0].backgroundColor = colores;
                graficoFamilias.update();
                return;
            }

            graficoFamilias = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labelsConPct,
                    datasets: [{
                        data: datos,
                        backgroundColor: colores,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { font: { size: 11 }, padding: 12 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const tot = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                    const pct = tot > 0 ? ((ctx.parsed / tot) * 100).toFixed(1) : 0;
                                    return ' ' + ctx.parsed.toLocaleString('es-ES') + ' usos (' + pct + ' %)';
                                }
                            }
                        }
                    }
                }
            });
        });
}

// ─────────────────────────────────────────────────────────────────────────────
// GRÁFICO — Tendencia mensual (líneas: año actual vs año anterior)
// ─────────────────────────────────────────────────────────────────────────────
function cargarGraficoTendencia() {
    $.post(CTRL + '?op=tendencia_mensual', {})
        .done(function (resp) {
            if (!Array.isArray(resp) || resp.length === 0) {
                if (graficoTendencia) { graficoTendencia.destroy(); graficoTendencia = null; }
                return;
            }

            const hoy    = new Date();
            const meses  = [];
            const actual = [];
            const anyo   = [];
            const nombreMeses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

            // Generar los 12 meses como eje X
            for (let i = 11; i >= 0; i--) {
                const d = new Date(hoy.getFullYear(), hoy.getMonth() - i, 1);
                meses.push(nombreMeses[d.getMonth()] + ' ' + d.getFullYear());
                actual.push(0);
                anyo.push(0);
            }

            resp.forEach(function (row) {
                const partes = row.mes.split('-');
                const y = parseInt(partes[0]);
                const m = parseInt(partes[1]) - 1;
                const v = parseInt(row.total_presupuestos) || 0;

                for (let i = 0; i < 12; i++) {
                    // Año actual: últimos 12 meses
                    const da = new Date(hoy.getFullYear(), hoy.getMonth() - (11 - i), 1);
                    if (da.getFullYear() === y && da.getMonth() === m) { actual[i] += v; break; }
                    // Año anterior: mismos meses pero un año antes
                    const dp = new Date(hoy.getFullYear() - 1, hoy.getMonth() - (11 - i), 1);
                    if (dp.getFullYear() === y && dp.getMonth() === m) { anyo[i]   += v; break; }
                }
            });

            const ctx = document.getElementById('chartTendencia').getContext('2d');

            if (graficoTendencia) {
                graficoTendencia.data.labels           = meses;
                graficoTendencia.data.datasets[0].data = actual;
                graficoTendencia.data.datasets[1].data = anyo;
                graficoTendencia.update();
                return;
            }

            graficoTendencia = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [
                        {
                            label: 'Año actual',
                            data: actual,
                            borderColor:     'rgba(13, 110, 253, 1)',
                            backgroundColor: 'rgba(13, 110, 253, 0.08)',
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Año anterior',
                            data: anyo,
                            borderColor:     'rgba(108, 117, 125, 0.7)',
                            backgroundColor: 'transparent',
                            tension: 0.3,
                            fill: false,
                            borderDash: [5, 4],
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' presupuestos aprobados';
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────────────────────
function generarColores(n) {
    const base = [
        'rgba(13,110,253,0.8)',  'rgba(25,135,84,0.8)',   'rgba(220,53,69,0.8)',
        'rgba(255,193,7,0.85)',  'rgba(102,16,242,0.8)',  'rgba(13,202,240,0.8)',
        'rgba(253,126,20,0.8)',  'rgba(32,201,151,0.8)',  'rgba(214,51,132,0.8)',
        'rgba(108,117,125,0.8)','rgba(0,123,255,0.7)',   'rgba(40,167,69,0.7)',
        'rgba(255,102,0,0.8)',   'rgba(111,66,193,0.8)',  'rgba(23,162,184,0.8)'
    ];
    const result = [];
    for (let i = 0; i < n; i++) result.push(base[i % base.length]);
    return result;
}
