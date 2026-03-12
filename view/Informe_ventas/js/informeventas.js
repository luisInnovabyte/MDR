/**
 * informeventas.js
 * MDR ERP Manager — Informe de Ventas por Período
 * Vista: view/Informe_ventas/index.php
 */

'use strict';

// ─── Estado global ────────────────────────────────────────────────────────────
const CTRL = '../../controller/informeventas.php';
let tablaClientes = null;
let tablaFamilia  = null;
let graficoBars   = null;

const MESES = [
    'Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];

// ─── Utils ────────────────────────────────────────────────────────────────────
function formatEuro(valor) {
    if (valor === null || valor === undefined || valor === '') return '—';
    return Number(valor).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }) + ' €';
}

function anyoActual() {
    return parseInt($('#filtroAnyo').val()) || 0;
}

function mesActual() {
    return parseInt($('#filtroMes').val()) || 0;
}

// ─── Document ready ───────────────────────────────────────────────────────────
$(document).ready(function () {
    cargarAnyos();          // Popula el selector y luego lanza todo
});

// ─── Cargar años disponibles ──────────────────────────────────────────────────
function cargarAnyos() {
    $.post(CTRL + '?op=anyos_disponibles')
        .done(function (res) {
            if (!res.success || !res.data) return;
            const select = $('#filtroAnyo');
            // Añadir opción para cada año
            res.data.forEach(function (fila) {
                const esteAnyo = new Date().getFullYear();
                const selected = fila.anyo == esteAnyo ? 'selected' : '';
                select.append(
                    '<option value="' + fila.anyo + '" ' + selected + '>' + fila.anyo + '</option>'
                );
            });
            // Selecciona el año actual si existe; si no, el primero disponible
            if (res.data.length > 0 && !select.val()) {
                select.find('option:first').prop('selected', true);
            }
            // Lanzar carga inicial
            recargarTodo();
        })
        .fail(function () {
            recargarTodo(); // Cargar de todos modos aunque falle la lista de años
        });
}

// ─── Cargar todo (KPIs + gráfico + tablas) ───────────────────────────────────
function recargarTodo() {
    const anyo = anyoActual();
    const mes  = mesActual();
    cargarKPIs(anyo, mes);
    cargarGrafico(anyo);           // el gráfico siempre muestra el año completo para contexto
    inicializarTablaClientes(anyo, mes);
    inicializarTablaFamilia(anyo, mes);
}

// ─── KPIs ─────────────────────────────────────────────────────────────────────
function cargarKPIs(anyo, mes) {
    $.post(CTRL + '?op=kpis', { anyo: anyo, mes: mes || 0 })
        .done(function (res) {
            if (!res.success || !res.data) return;
            const d = res.data;

            $('#kpi-total').text(formatEuro(d.total_facturado));
            $('#kpi-total-sub').text('en el período seleccionado');

            $('#kpi-pptos').text(d.num_presupuestos || 0);

            $('#kpi-ticket').text(formatEuro(d.ticket_promedio));

            if (d.mes_top) {
                $('#kpi-mes-top').text(MESES[d.mes_top - 1] || '—');
                $('#kpi-mes-top-sub').text(formatEuro(d.mes_top_total));
            } else {
                $('#kpi-mes-top').text('N/D');
                $('#kpi-mes-top-sub').text('Sin datos');
            }
        })
        .fail(function () {
            console.warn('No se pudieron cargar los KPIs de ventas.');
        });
}

// ─── Gráfico mensual ──────────────────────────────────────────────────────────
function cargarGrafico(anyo) {
    $.post(CTRL + '?op=grafico_mensual', { anyo: anyo })
        .done(function (res) {
            if (!res.success || !res.data) return;

            const labels = res.data.map(function (fila) { return MESES[fila.mes - 1]; });
            const totales = res.data.map(function (fila) { return parseFloat(fila.total) || 0; });

            $('#lblGraficoAnyo').text(anyo > 0 ? 'Año ' + anyo : 'Todos los años');

            if (graficoBars) {
                graficoBars.data.labels         = labels;
                graficoBars.data.datasets[0].data = totales;
                graficoBars.update();
                return;
            }

            const ctx = document.getElementById('chartVentasMensuales').getContext('2d');
            graficoBars = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ingresos (€)',
                        data: totales,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor:     'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + formatEuro(ctx.parsed.y);
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (val) {
                                    return val.toLocaleString('es-ES') + ' €';
                                },
                            },
                        },
                    },
                },
            });
        })
        .fail(function () {
            console.warn('No se pudo cargar el gráfico mensual.');
        });
}

// ─── DataTable Top Clientes ───────────────────────────────────────────────────
function inicializarTablaClientes(anyo, mes) {
    if (tablaClientes) {
        tablaClientes.destroy();
        $('#tblTopClientes tbody').empty();
    }

    tablaClientes = $('#tblTopClientes').DataTable({
        ajax: {
            url:    CTRL + '?op=top_clientes',
            type:   'POST',
            data:   { anyo: anyo, mes: mes || 0, limite: 10 },
            dataSrc: 'data',
            error: function () {
                Swal.fire('Error', 'No se pudo cargar el ranking de clientes.', 'error');
            },
        },
        columns: [
            { data: 'nombre_completo_cliente' },
            { data: 'num_presupuestos', className: 'text-center' },
            { data: 'total_facturado',  className: 'text-end' },
        ],
        searching:  false,
        info:       false,
        paging:     false,
        order:      [[2, 'desc']],
        language: {
            emptyTable: 'Sin datos para el período seleccionado',
        },
    });
}

// ─── DataTable Por Familia ────────────────────────────────────────────────────
function inicializarTablaFamilia(anyo, mes) {
    if (tablaFamilia) {
        tablaFamilia.destroy();
        $('#tblPorFamilia tbody').empty();
    }

    tablaFamilia = $('#tblPorFamilia').DataTable({
        ajax: {
            url:    CTRL + '?op=por_familia',
            type:   'POST',
            data:   { anyo: anyo, mes: mes || 0 },
            dataSrc: 'data',
            error: function () {
                Swal.fire('Error', 'No se pudo cargar los datos por familia.', 'error');
            },
        },
        columns: [
            { data: 'nombre_familia' },
            { data: 'num_presupuestos', className: 'text-center' },
            { data: 'total_unidades',   className: 'text-center' },
            { data: 'total_facturado',  className: 'text-end' },
            {
                data: '_pct',
                className: 'text-end',
                render: function (data) {
                    return data + ' %';
                },
            },
        ],
        searching: false,
        info:      false,
        paging:    false,
        order:     [[3, 'desc']],
        language: {
            emptyTable: 'Sin datos para el período seleccionado',
        },
    });
}

// ─── Acciones de filtros ──────────────────────────────────────────────────────
function aplicarFiltros() {
    recargarTodo();
}

function limpiarFiltros() {
    $('#filtroAnyo').val(0);
    $('#filtroMes').val(0);
    recargarTodo();
}
