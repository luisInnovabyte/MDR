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

// Variables para navegación de gráficos
let graficoActivo = 0; // 0 = barras, 1 = familias
let chartVentasMensuales = null;
let chartFamiliasActual = null;
let chartFamiliasComparar = null;
let chartFamiliaDonut = null;
let chartGaugeKpiAnyo = null;
let chartGaugeKpiMes = null;
let tablaFamiliaIniciada = false; // lazy init para la tabla de desglose

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
    return parseInt($('#filtroAnyoActual').val()) || 0;
}

function mesActual() {
    return parseInt($('#filtroMesActual').val()) || 0;
}

function anyoComparar() {
    return parseInt($('#filtroAnyoComparar').val()) || 0;
}

// ─── Document ready ───────────────────────────────────────────────────────────
$(document).ready(function () {
    cargarAnyos();          // Popula el selector y luego lanza todo
    
    // Event listeners para navegación de gráficos
    $('#btnGraficoPrev').on('click', function() {
        cambiarGrafico(-1);
    });
    
    $('#btnGraficoNext').on('click', function() {
        cambiarGrafico(1);
    });

    // ─── Toggle Desglose / Gráfico Donut Familias ─────────────────────────────
    $('#btnDesgloseFamilia').on('click', function () {
        const enGrafico = !$('#containerGraficoDonut').hasClass('d-none');
        if (enGrafico) {
            $('#containerGraficoDonut').addClass('d-none');
            $('#containerTableFamilia').removeClass('d-none');
            $(this).html('<i class="fas fa-chart-pie me-1"></i>Gráfico');
            if (!tablaFamiliaIniciada) {
                inicializarTablaFamilia(anyoActual(), mesActual());
                tablaFamiliaIniciada = true;
            }
        } else {
            $('#containerTableFamilia').addClass('d-none');
            $('#containerGraficoDonut').removeClass('d-none');
            $(this).html('<i class="fas fa-table me-1"></i>Desglose');
        }
    });

    // ─── Child-row: Top Clientes ───────────────────────────────────────────────
    $(document).on('click', '#tblTopClientes .btn-expand-cliente', function () {
        if (!tablaClientes) return;
        const tr  = $(this).closest('tr');
        const row = tablaClientes.row(tr);
        const btn = $(this);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            btn.html('<i class="bi bi-plus-circle"></i>');
        } else {
            row.child(formatClienteDetalle(row.data())).show();
            tr.addClass('shown');
            btn.html('<i class="bi bi-dash-circle"></i>');
        }
    });
});

// ─── Cargar años disponibles ──────────────────────────────────────────────────
function cargarAnyos() {
    $.post(CTRL + '?op=anyos_disponibles')
        .done(function (res) {
            if (!res.success || !res.data) return;
            
            const selectActual = $('#filtroAnyoActual');
            const selectComparar = $('#filtroAnyoComparar');
            const esteAnyo = new Date().getFullYear();
            
            // Poblar selector de Año Actual
            res.data.forEach(function (fila) {
                const selected = fila.anyo == esteAnyo ? 'selected' : '';
                selectActual.append(
                    '<option value="' + fila.anyo + '" ' + selected + '>' + fila.anyo + '</option>'
                );
            });
            
            // Dejar "Todos" seleccionado por defecto (no preseleccionar mes)
            // El KPI #2 usará automáticamente el mes actual del sistema
            $('#filtroMesActual').val(0);
            
            // Poblar selector de Año a Comparar (con Año Anterior por defecto)
            const anyoAnterior = esteAnyo - 1;
            res.data.forEach(function (fila) {
                const selected = fila.anyo == anyoAnterior ? 'selected' : '';
                selectComparar.append(
                    '<option value="' + fila.anyo + '" ' + selected + '>' + fila.anyo + '</option>'
                );
            });
            
            // Si no existe el año actual, seleccionar el primero disponible
            if (res.data.length > 0 && !selectActual.val()) {
                selectActual.find('option:first').prop('selected', true);
            }
            
            // Configurar eventos para habilitar/deshabilitar controles
            configurarEventosFiltros();
            
            // Lanzar carga inicial
            recargarTodo();
        })
        .fail(function () {
            // Dejar "Todos" seleccionado por defecto
            $('#filtroMesActual').val(0);
            
            recargarTodo(); // Cargar de todos modos aunque falle la lista de años
        });
}

// ─── Configurar eventos de filtros ────────────────────────────────────────────
function configurarEventosFiltros() {
    const esteAnyo = new Date().getFullYear();
    const esteMes = new Date().getMonth() + 1; // getMonth() devuelve 0-11
    
    // Evento: Al cambiar Año Actual, deshabilitar meses futuros si es el año actual
    $('#filtroAnyoActual').on('change', function() {
        const anyoSeleccionado = parseInt($(this).val());
        const selectMes = $('#filtroMesActual');
        
        // Habilitar todas las opciones primero
        selectMes.find('option').prop('disabled', false);
        
        // Si es el año actual, deshabilitar meses futuros
        if (anyoSeleccionado === esteAnyo) {
            selectMes.find('option').each(function() {
                const mesOption = parseInt($(this).val());
                if (mesOption > esteMes && mesOption !== 0) {
                    $(this).prop('disabled', true);
                }
            });
            
            // Si el mes seleccionado está deshabilitado, cambiar a "Todos"
            if (parseInt(selectMes.val()) > esteMes) {
                selectMes.val(0);
            }
        }
    });
    
    // Ya no necesitamos configurar el selector de Mes a Comparar (se usa automáticamente el mes actual)
    
    // Disparar evento inicial para deshabilitar meses futuros si corresponde
    $('#filtroAnyoActual').trigger('change');
}

// ─── Cargar todo (KPIs + gráfico + tablas) ───────────────────────────────────
function recargarTodo() {
    const anyo = anyoActual();
    const mes  = mesActual();
    cargarKPIs(anyo, mes);
    cargarGraficoActual();         // carga el gráfico activo según navegación
    inicializarTablaClientes(anyo, mes);
    // Tabla familia: resetear lazy-init y recargar solo si está visible
    tablaFamiliaIniciada = false;
    if (!$('#containerTableFamilia').hasClass('d-none')) {
        inicializarTablaFamilia(anyo, mes);
        tablaFamiliaIniciada = true;
    }
    cargarGraficoDonutFamilias(anyo, mes);
}

// ─── Navegación entre gráficos ────────────────────────────────────────────────
function cambiarGrafico(direccion) {
    graficoActivo = (graficoActivo + direccion + 2) % 2;
    actualizarVistaGrafico();
    cargarGraficoActual();
}

function actualizarVistaGrafico() {
    if (graficoActivo === 0) {
        // Mostrar gráfico de barras
        $('#containerGraficoBarras').removeClass('d-none');
        $('#containerGraficoFamilias').addClass('d-none');
        $('#lblTituloGrafico').html('<i class="fas fa-bar-chart me-2"></i>Ingresos por Mes');
    } else {
        // Mostrar gráfico de familias
        $('#containerGraficoBarras').addClass('d-none');
        $('#containerGraficoFamilias').removeClass('d-none');
        $('#lblTituloGrafico').html('<i class="fas fa-chart-line me-2"></i>Ingresos por Familia');
    }
}

function cargarGraficoActual() {
    const anyoActual = parseInt($('#filtroAnyoActual').val()) || new Date().getFullYear();
    const anyoComp = anyoComparar();
    
    if (graficoActivo === 0) {
        cargarGraficoBarras(anyoActual, anyoComp);
    } else {
        cargarGraficoFamilias(anyoActual, anyoComp);
    }
}

// ─── KPIs ─────────────────────────────────────────────────────────────────────
function cargarKPIs(anyo, mes) {
    const anyoComp = anyoComparar();
    // El backend se encarga de usar el mes actual del sistema si mes=0
    const mesComp = mes || 0;
    
    $.post(CTRL + '?op=kpis', {
        anyo_actual: anyo || 0,
        mes_actual: mes || 0,
        anyo_comparar: anyoComp || 0,
        mes_comparar: mesComp
    })
        .done(function (res) {
            if (!res.success || !res.data) return;
            const d = res.data;
            renderizarVelocimetrosKPIs(d, anyo);
            
            // KPI 3: Presupuestos (mes como principal, año como contexto)
            const numPptosMes = d.actual.num_presupuestos_mes || 0;
            const numPptosAnyo = d.actual.num_presupuestos || 0;
            const tieneMes = d.actual.tiene_mes_seleccionado;
            
            if (d.comparacion) {
                const diffMes = d.diferencias.num_presupuestos_mes;
                const diffAnyo = d.diferencias.num_presupuestos_anyo;
                const iconoMes = diffMes.tendencia === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
                const colorMes = diffMes.tendencia === 'up' ? '#198754' : '#dc3545';
                const signoMes = diffMes.porcentaje >= 0 ? '+' : '';
                
                // Valor principal con flecha y porcentaje
                $('#kpi-pptos').html(
                    numPptosMes + ' ' +
                    '<i class="fas ' + iconoMes + '" style="color:' + colorMes + '; font-size:0.7em; margin-left:5px"></i> ' +
                    '<span style="color:' + colorMes + '; font-weight:600; font-size:0.7em">' + signoMes + diffMes.porcentaje + '%</span>'
                );
                
                let subtitulo = '';
                if (tieneMes) {
                    // Mostrar valor comparado del mes
                    const mesNombre = MESES[d.actual.mes_especifico - 1];
                    subtitulo = mesNombre + ' ' + anyoComp + ': ' + (d.comparar.num_presupuestos_mes || 0);
                }
                
                // Agregar información del año completo en línea separada
                const iconoAnyo = diffAnyo.tendencia === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
                const colorAnyo = diffAnyo.tendencia === 'up' ? '#198754' : '#dc3545';
                const signoAnyo = diffAnyo.porcentaje >= 0 ? '+' : '';
                
                if (subtitulo) subtitulo += '<br>';
                subtitulo += '<span class="text-muted" style="font-size:0.9em">Año: ' + numPptosAnyo + '</span> ' +
                    '<i class="fas ' + iconoAnyo + '" style="color:' + colorAnyo + '; font-size:0.8em"></i> ' +
                    '<span style="color:' + colorAnyo + '; font-weight:600; font-size:0.85em">' + signoAnyo + diffAnyo.porcentaje + '%</span>';
                
                $('#kpi-pptos-sub').html(subtitulo);
            } else {
                // Sin comparación
                $('#kpi-pptos').text(numPptosMes);
                
                let subtitulo = tieneMes ? 'Mes seleccionado' : 'Año completo';
                if (tieneMes) {
                    subtitulo += ' | <span class="text-muted">Año: ' + numPptosAnyo + '</span>';
                }
                $('#kpi-pptos-sub').html(subtitulo);
            }
            
            // KPI 4: Ticket Promedio (mes como principal, año como contexto)
            const ticketMes = d.actual.ticket_promedio_mes || 0;
            const ticketAnyo = d.actual.ticket_promedio || 0;
            
            if (d.comparacion) {
                const diffMes = d.diferencias.ticket_promedio_mes;
                const diffAnyo = d.diferencias.ticket_promedio_anyo;
                const iconoMes = diffMes.tendencia === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
                const colorMes = diffMes.tendencia === 'up' ? '#198754' : '#dc3545';
                const signoMes = diffMes.porcentaje >= 0 ? '+' : '';
                
                // Valor principal con flecha y porcentaje
                $('#kpi-ticket').html(
                    formatEuro(ticketMes) + ' ' +
                    '<i class="fas ' + iconoMes + '" style="color:' + colorMes + '; font-size:0.7em; margin-left:5px"></i> ' +
                    '<span style="color:' + colorMes + '; font-weight:600; font-size:0.7em">' + signoMes + diffMes.porcentaje + '%</span>'
                );
                
                let subtitulo = '';
                if (tieneMes) {
                    // Mostrar valor comparado del mes
                    const mesNombre = MESES[d.actual.mes_especifico - 1];
                    subtitulo = mesNombre + ' ' + anyoComp + ': ' + formatEuro(d.comparar.ticket_promedio_mes || 0);
                }
                
                // Agregar información del año completo en línea separada
                const iconoAnyo = diffAnyo.tendencia === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
                const colorAnyo = diffAnyo.tendencia === 'up' ? '#198754' : '#dc3545';
                const signoAnyo = diffAnyo.porcentaje >= 0 ? '+' : '';
                
                if (subtitulo) subtitulo += '<br>';
                subtitulo += '<span class="text-muted" style="font-size:0.9em">Año: ' + formatEuro(ticketAnyo) + '</span> ' +
                    '<i class="fas ' + iconoAnyo + '" style="color:' + colorAnyo + '; font-size:0.8em"></i> ' +
                    '<span style="color:' + colorAnyo + '; font-weight:600; font-size:0.85em">' + signoAnyo + diffAnyo.porcentaje + '%</span>';
                
                $('#kpi-ticket-sub').html(subtitulo);
            } else {
                // Sin comparación
                $('#kpi-ticket').text(formatEuro(ticketMes));
                
                let subtitulo = tieneMes ? 'Mes seleccionado' : 'Año completo';
                if (tieneMes) {
                    subtitulo += ' | <span class="text-muted">Año: ' + formatEuro(ticketAnyo) + '</span>';
                }
                $('#kpi-ticket-sub').html(subtitulo);
            }
        })
        .fail(function () {
            console.warn('No se pudieron cargar los KPIs de ventas.');
        });
}

function crearOActualizarVelocimetro(chartRef, canvasId, maximo, completado) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return chartRef;

    const maxVal = Math.max(0, Number(maximo) || 0);
    const completadoVal = Math.max(0, Math.min(maxVal, Number(completado) || 0));
    const restanteVal = Math.max(0, maxVal - completadoVal);

    const datos = maxVal > 0 ? [completadoVal, restanteVal] : [0, 1];
    const colores = maxVal > 0
        ? ['rgba(25, 135, 84, 0.9)', 'rgba(220, 53, 69, 0.3)']
        : ['rgba(108, 117, 125, 0.35)', 'rgba(108, 117, 125, 0.1)'];
    const labels = ['completado', 'pendiente'];

    if (chartRef) {
        chartRef.destroy();
    }

    chartRef = new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: datos,
                backgroundColor: colores,
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            circumference: 180,
            rotation: 270,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function (ctx) {
                            const label = ctx.label || 'seccion';
                            const valor = Number(ctx.parsed) || 0;
                            return label + ': ' + formatEuro(valor);
                        },
                    },
                },
            },
        },
    });

    return chartRef;
}

function renderizarVelocimetrosKPIs(d, anyo) {
    const mesEspecifico = d.actual.mes_especifico || (new Date().getMonth() + 1);
    const mesNombre = MESES[mesEspecifico - 1] || 'Mes';
    const periodoAnyo = 'Año ' + anyo;
    const periodoMes = mesNombre + ' ' + anyo;
    const anyoComp = anyoComparar();
    const hayComparacion = !!(d.comparacion && d.comparar && anyoComp > 0);

    const totalPresupuestoAnyo = Number(d.actual.total_presupuesto_anyo || 0);
    const totalPagadoAnyo = Number(d.actual.total_pagado_anyo || 0);
    const totalPendienteAnyo = Number(d.actual.total_pendiente_anyo || 0);

    chartGaugeKpiAnyo = crearOActualizarVelocimetro(
        chartGaugeKpiAnyo,
        'gaugeKpiAnyo',
        totalPresupuestoAnyo,
        totalPagadoAnyo
    );

    $('#gaugeKpiAnyoPeriodo').text(periodoAnyo);

    $('#gaugeKpiAnyoInfo').html(
        construirLineaGauge(
            'Aprovado',
            totalPresupuestoAnyo,
            hayComparacion ? Number(d.comparar.total_presupuesto_anyo || 0) : null,
            hayComparacion ? ('Año ' + anyoComp) : ''
        ) +
        construirLineaGauge(
            'Pendiente',
            totalPendienteAnyo,
            hayComparacion ? Number(d.comparar.total_pendiente_anyo || 0) : null,
            hayComparacion ? ('Año ' + anyoComp) : ''
        ) +
        construirLineaGauge(
            'Completado',
            totalPagadoAnyo,
            hayComparacion ? Number(d.comparar.total_pagado_anyo || 0) : null,
            hayComparacion ? ('Año ' + anyoComp) : ''
        )
    );

    const totalPresupuestoMes = Number(d.actual.total_presupuesto_mes || 0);
    const totalPagadoMes = Number(d.actual.total_pagado_mes || 0);
    const totalPendienteMes = Number(d.actual.total_pendiente_mes || 0);

    chartGaugeKpiMes = crearOActualizarVelocimetro(
        chartGaugeKpiMes,
        'gaugeKpiMes',
        totalPresupuestoMes,
        totalPagadoMes
    );

    $('#gaugeKpiMesPeriodo').text(periodoMes);

    $('#gaugeKpiMesInfo').html(
        construirLineaGauge(
            'Aprovado',
            totalPresupuestoMes,
            hayComparacion ? Number(d.comparar.total_presupuesto_mes || 0) : null,
            hayComparacion ? (mesNombre + ' ' + anyoComp) : ''
        ) +
        construirLineaGauge(
            'Pendiente',
            totalPendienteMes,
            hayComparacion ? Number(d.comparar.total_pendiente_mes || 0) : null,
            hayComparacion ? (mesNombre + ' ' + anyoComp) : ''
        ) +
        construirLineaGauge(
            'Completado',
            totalPagadoMes,
            hayComparacion ? Number(d.comparar.total_pagado_mes || 0) : null,
            hayComparacion ? (mesNombre + ' ' + anyoComp) : ''
        )
    );
}

function construirLineaGauge(etiqueta, actual, comparar, etiquetaComparar) {
    const valorActual = Number(actual || 0);

    if (comparar === null || comparar === undefined) {
        return '<div class="kpi-gauge-row">' +
            '<span class="kpi-gauge-label">' + etiqueta + '</span>' +
            '<span class="kpi-gauge-current">' + formatEuro(valorActual) + '</span>' +
            '</div>';
    }

    const valorComparar = Number(comparar || 0);
    const variacion = calcularVariacionPorcentual(valorActual, valorComparar);
    const clase = variacion >= 0 ? 'up' : 'down';
    const signo = variacion >= 0 ? '+' : '';

    return '<div class="kpi-gauge-row">' +
        '<span class="kpi-gauge-label">' + etiqueta + '</span>' +
        '<span class="kpi-gauge-current">' + formatEuro(valorActual) + '</span>' +
        '<span class="kpi-gauge-delta ' + clase + '">' + signo + variacion.toFixed(1) + '%</span>' +
        '<span class="kpi-gauge-compare">' + etiquetaComparar + ': ' + formatEuro(valorComparar) + '</span>' +
        '</div>';
}

function calcularVariacionPorcentual(actual, comparar) {
    const a = Number(actual || 0);
    const b = Number(comparar || 0);

    if (b === 0) {
        return a === 0 ? 0 : 100;
    }

    return ((a - b) / Math.abs(b)) * 100;
}

// ─── Gráfico mensual ──────────────────────────────────────────────────────────
// ─── Gráfico de Barras Compuestas (stacked) ──────────────────────────────────
function cargarGraficoBarras(anyoActual, anyoComp) {
    $.post(CTRL + '?op=grafico_mensual_comparativo', {
        anyo_actual: anyoActual,
        anyo_comparar: anyoComp || 0
    })
        .done(function (res) {
            if (!res.success || !res.data) return;

            if (chartVentasMensuales) {
                chartVentasMensuales.destroy();
                chartVentasMensuales = null;
            }

            const d = res.data;
            const hayComp = (anyoComp > 0 && d.comparar);

            // Precalcular pendiente = presupuesto - pagado (nunca negativo)
            function pendiente(presupuesto, pagado) {
                return presupuesto.map(function (p, i) {
                    return Math.max(0, (parseFloat(p) || 0) - (parseFloat(pagado[i]) || 0));
                });
            }

            const actualPend  = pendiente(d.actual.presupuesto, d.actual.pagado);
            const compPend    = hayComp ? pendiente(d.comparar.presupuesto, d.comparar.pagado) : null;

            // Para el tooltip: recuperar total_presupuesto por índice de mes
            const actualPpto  = d.actual.presupuesto.map(function (v) { return parseFloat(v) || 0; });
            const compPpto    = hayComp ? d.comparar.presupuesto.map(function (v) { return parseFloat(v) || 0; }) : null;

            const datasets = [];

            // ── Stack año actual ──────────────────────────────────────────
            datasets.push({
                label: anyoActual + ' – Completado',
                data: d.actual.pagado,
                backgroundColor: 'rgba(13, 110, 253, 0.82)',
                borderColor:     'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                stack: 'anyo_actual'
            });
            datasets.push({
                label: anyoActual + ' – Aprovado',
                data: actualPend,
                // Guardamos el array de totales para el tooltip
                _ppto: actualPpto,
                backgroundColor: 'rgba(13, 110, 253, 0.20)',
                borderColor:     'rgba(13, 110, 253, 0.45)',
                borderWidth: 1,
                stack: 'anyo_actual'
            });

            // ── Stack año comparar (si existe) ────────────────────────────
            if (hayComp) {
                datasets.push({
                    label: anyoComp + ' – Completado',
                    data: d.comparar.pagado,
                    backgroundColor: 'rgba(108, 117, 125, 0.82)',
                    borderColor:     'rgba(108, 117, 125, 1)',
                    borderWidth: 1,
                    stack: 'anyo_comp'
                });
                datasets.push({
                    label: anyoComp + ' – Aprovado',
                    data: compPend,
                    _ppto: compPpto,
                    backgroundColor: 'rgba(108, 117, 125, 0.20)',
                    borderColor:     'rgba(108, 117, 125, 0.45)',
                    borderWidth: 1,
                    stack: 'anyo_comp'
                });
            }

            const ctx = document.getElementById('chartVentasMensuales').getContext('2d');
            chartVentasMensuales = new Chart(ctx, {
                type: 'bar',
                data: { labels: MESES, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (ctxItem) {
                                    const val = parseFloat(ctxItem.raw) || 0;
                                    if (val === 0) return null; // ocultar segmentos vacíos
                                    const ds    = ctxItem.dataset;
                                    const isAprovado = ds.label && ds.label.indexOf('Aprovado') !== -1;
                                    if (isAprovado && ds._ppto) {
                                        // Mostrar total aprovado (presupuesto completo), no solo la diferencia
                                        const totalPpto = ds._ppto[ctxItem.dataIndex] || 0;
                                        return ds.label + ': ' + formatEuro(totalPpto);
                                    }
                                    return ds.label + ': ' + formatEuro(val);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { stacked: true },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function (val) {
                                    return val.toLocaleString('es-ES') + ' €';
                                }
                            }
                        }
                    }
                }
            });
        })
        .fail(function () {
            console.warn('No se pudo cargar el gráfico de barras comparativo.');
        });
}

// ─── Gráfico Lineal por Familias ─────────────────────────────────────────────
function cargarGraficoFamilias(anyoActual, anyoComp) {
    $.post(CTRL + '?op=grafico_familias', {
        anyo_actual: anyoActual,
        anyo_comparar: anyoComp || 0
    })
        .done(function (res) {
            if (!res.success || !res.data) return;

            // Destruir gráficos anteriores si existen
            if (chartFamiliasActual) {
                chartFamiliasActual.destroy();
                chartFamiliasActual = null;
            }
            if (chartFamiliasComparar) {
                chartFamiliasComparar.destroy();
                chartFamiliasComparar = null;
            }

            // Gráfico 1: Año actual
            if (res.data.actual && Object.keys(res.data.actual).length > 0) {
                $('#lblTituloFamiliaActual').text('Año ' + anyoActual);
                chartFamiliasActual = crearGraficoLinealFamilias('chartFamiliasActual', res.data.actual);
            }

            // Gráfico 2: Año a comparar (si existe)
            if (anyoComp > 0 && res.data.comparar && Object.keys(res.data.comparar).length > 0) {
                $('#colGraficoComparar').removeClass('d-none');
                $('#colGraficoActual').removeClass('col-12').addClass('col-md-6');
                $('#colGraficoComparar').removeClass('col-12').addClass('col-md-6');
                $('#lblTituloFamiliaComparar').text('Año ' + anyoComp);
                chartFamiliasComparar = crearGraficoLinealFamilias('chartFamiliasComparar', res.data.comparar);
            } else {
                $('#colGraficoComparar').addClass('d-none');
                $('#colGraficoActual').removeClass('col-md-6').addClass('col-12');
            }
        })
        .fail(function () {
            console.warn('No se pudo cargar el gráfico de familias.');
        });
}

function crearGraficoLinealFamilias(canvasId, datos) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    
    // Preparar datasets (uno por familia)
    const familias = Object.keys(datos);
    const colores = generarColoresFamilias(familias.length);
    
    const datasets = familias.map((familia, idx) => ({
        label: familia,
        data: datos[familia], // Array de 12 valores (meses)
        borderColor: colores[idx],
        backgroundColor: colores[idx],
        fill: false,
        tension: 0.4,
        borderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 5
    }));
    
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: MESES,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ' (Aprovado): ' + formatEuro(ctx.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (val) {
                            return val.toLocaleString('es-ES') + ' €';
                        }
                    }
                }
            }
        }
    });
}

function generarColoresFamilias(cantidad) {
    // Paleta de colores para familias
    const paleta = [
        '#0d6efd', // Azul Bootstrap
        '#198754', // Verde Bootstrap
        '#fd7e14', // Naranja Bootstrap
        '#dc3545', // Rojo Bootstrap
        '#6610f2', // Índigo Bootstrap
        '#6c757d', // Gris Bootstrap
        '#20c997', // Teal
        '#ffc107', // Amarillo
        '#d63384', // Rosa
        '#0dcaf0'  // Cyan
    ];
    return paleta.slice(0, cantidad);
}

// ─── Formato child-row: detalle del cliente ──────────────────────────────────
function formatClienteDetalle(d) {
    const val = (v) => (v !== null && v !== undefined && v !== '')
        ? v
        : '<span class="text-muted">-</span>';

    const ticketMedio = (d._total_num && d.num_presupuestos > 0)
        ? formatEuro(d._total_num / d.num_presupuestos)
        : '<span class="text-muted">-</span>';

    const urlCliente = `../../view/MntClientes/formularioCliente.php?modo=editar&id=${d.id_cliente}`;

    return `
    <div class="card border-0 shadow-sm mx-2 my-2">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
            <span><i class="bi bi-person-fill me-2"></i><strong>Detalle del Cliente</strong></span>
            <a href="${urlCliente}" class="btn btn-sm btn-light" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i>Ver ficha
            </a>
        </div>
        <div class="card-body py-3">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width:150px">Nº Presupuestos</td>
                    <td><span class="badge bg-info text-dark">${val(d.num_presupuestos)}</span></td>
                </tr>
                <tr>
                    <td class="text-muted">Total Facturado</td>
                    <td class="fw-bold text-success">${val(d.total_facturado)}</td>
                </tr>
                <tr>
                    <td class="text-muted">Ticket Medio</td>
                    <td>${ticketMedio}</td>
                </tr>
            </table>
        </div>
    </div>`;
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
            data:   { anyo: anyo, mes: mes || 0 },
            dataSrc: 'data',
            error: function () {
                Swal.fire('Error', 'No se pudo cargar el ranking de clientes.', 'error');
            },
        },
        columns: [
            {
                orderable:      false,
                data:           null,
                defaultContent: '<button class="btn btn-sm btn-outline-primary btn-expand-cliente p-0" style="width:28px;height:28px;line-height:1"><i class="bi bi-plus-circle"></i></button>',
                className:      'text-center align-middle',
            },
            {
                data:      'nombre_completo_cliente',
                className: 'align-middle',
            },
            {
                data:      null,
                className: 'text-end pe-3 align-middle',
                orderable: false,
                render:    function (data, type, row, meta) {
                    const pos = meta.row + 1;
                    if (pos === 1) return '<span class="badge" style="background:#FFD700;color:#000">1</span>';
                    if (pos === 2) return '<span class="badge" style="background:#C0C0C0;color:#000">2</span>';
                    if (pos === 3) return '<span class="badge" style="background:#CD7F32;color:#fff">3</span>';
                    return '<span class="badge bg-secondary">' + pos + '</span>';
                },
            },
        ],
        searching:  true,
        info:       true,
        paging:     true,
        pageLength: 15,
        ordering:   false,
        layout: {
            topStart: {
                buttons: [
                    { extend: 'copyHtml5',  text: '<i class="fa-solid fa-copy"></i>',       titleAttr: 'Copy'  },
                    { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel"></i>', titleAttr: 'Excel' },
                    { extend: 'pdfHtml5',   text: '<i class="fa-solid fa-file-pdf"></i>',   titleAttr: 'PDF'   },
                    { extend: 'print',      text: '<i class="fa-solid fa-print"></i>',      titleAttr: 'Print' },
                ],
            },
            topEnd:    'search',
            bottomStart: 'info',
            bottomEnd: { paging: { firstLast: true, numbers: true, previousNext: true } },
        },
        language: {
            emptyTable: 'Sin datos para el período seleccionado',
            search:     'Buscar:',
            lengthMenu: 'Mostrar _MENU_ clientes',
            info:       'Mostrando _START_-_END_ de _TOTAL_ clientes',
            paginate: {
                first:    '«',
                previous: '‹',
                next:     '›',
                last:     '»',
            },
        },
    });
}

// ─── Gráfico Donut: Ventas por Familia ───────────────────────────────────────
function cargarGraficoDonutFamilias(anyo, mes) {
    // Limpiar estado previo
    if (chartFamiliaDonut) {
        chartFamiliaDonut.destroy();
        chartFamiliaDonut = null;
    }
    $('#leyendaFamiliaDonut').empty();
    $('#detalleFamiliaDonut').addClass('d-none').find('#detalleFamiliaContenido').empty();

    $.post(CTRL + '?op=por_familia', { anyo: anyo || 0, mes: mes || 0 })
        .done(function (res) {
            if (!res.data || res.data.length === 0) return;

            const datos  = res.data;
            const labels = datos.map(function (r) { return r.nombre_familia; });
            const totales = datos.map(function (r) { return parseFloat(r._total_num) || 0; });
            const pcts    = datos.map(function (r) { return r._pct; });

            const COLORES = [
                '#0d6efd','#198754','#fd7e14','#dc3545','#6f42c1',
                '#20c997','#0dcaf0','#ffc107','#6c757d','#e83e8c',
                '#17a2b8','#28a745','#fd7e14','#6610f2','#343a40',
            ];
            const bgColors = labels.map(function (_, i) { return COLORES[i % COLORES.length]; });

            const ctx = document.getElementById('chartFamiliaDonut');
            if (!ctx) return;

            chartFamiliaDonut = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data:            totales,
                        backgroundColor: bgColors,
                        borderWidth:     2,
                        borderColor:     '#fff',
                        hoverOffset:     8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const total = ctx.parsed;
                                    const pct   = pcts[ctx.dataIndex];
                                    return '  ' + formatEuro(total) + '  (' + pct + ' %)';
                                },
                            },
                        },
                    },
                    onClick: function (evt, elements) {
                        if (!elements || elements.length === 0) {
                            $('#detalleFamiliaDonut').addClass('d-none');
                            return;
                        }
                        const idx = elements[0].index;
                        const fila = datos[idx];
                        const color = bgColors[idx];

                        $('#detalleFamiliaContenido').html(
                            '<div class="d-flex align-items-center gap-2 mb-2">' +
                                '<span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:' + color + '"></span>' +
                                '<strong>' + fila.nombre_familia + '</strong>' +
                            '</div>' +
                            '<div class="row g-2 text-center">' +
                                '<div class="col-4">' +
                                    '<div class="small text-muted">Total Facturado</div>' +
                                    '<div class="fw-bold text-success">' + fila.total_facturado + '</div>' +
                                '</div>' +
                                '<div class="col-4">' +
                                    '<div class="small text-muted">Presupuestos</div>' +
                                    '<div class="fw-bold"><span class="badge bg-info text-dark">' + fila.num_presupuestos + '</span></div>' +
                                '</div>' +
                                '<div class="col-4">' +
                                    '<div class="small text-muted">Unidades</div>' +
                                    '<div class="fw-bold"><span class="badge bg-secondary">' + fila.total_unidades + '</span></div>' +
                                '</div>' +
                            '</div>' +
                            '<div class="mt-1 text-center small text-muted">' + fila._pct + ' % del total</div>'
                        );
                        $('#detalleFamiliaDonut').removeClass('d-none');
                    },
                },
            });

            // Leyenda personalizada
            let html = '';
            labels.forEach(function (label, i) {
                html +=
                    '<div class="d-flex align-items-center gap-2 mb-1">' +
                        '<span style="flex-shrink:0;width:12px;height:12px;border-radius:2px;background:' + bgColors[i] + '"></span>' +
                        '<span class="text-truncate" style="max-width:140px" title="' + label + '">' + label + '</span>' +
                        '<span class="ms-auto text-muted">' + pcts[i] + '%</span>' +
                    '</div>';
            });
            $('#leyendaFamiliaDonut').html(html);

            // Mostrar automáticamente el detalle de la familia con mayor valor
            const idxMax = totales.indexOf(Math.max.apply(null, totales));
            const filaMax = datos[idxMax];
            const colorMax = bgColors[idxMax];
            $('#detalleFamiliaContenido').html(
                '<div class="d-flex align-items-center gap-2 mb-2">' +
                    '<span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:' + colorMax + '"></span>' +
                    '<strong>' + filaMax.nombre_familia + '</strong>' +
                '</div>' +
                '<div class="row g-2 text-center">' +
                    '<div class="col-4">' +
                        '<div class="small text-muted">Total Facturado</div>' +
                        '<div class="fw-bold text-success">' + filaMax.total_facturado + '</div>' +
                    '</div>' +
                    '<div class="col-4">' +
                        '<div class="small text-muted">Presupuestos</div>' +
                        '<div class="fw-bold"><span class="badge bg-info text-dark">' + filaMax.num_presupuestos + '</span></div>' +
                    '</div>' +
                    '<div class="col-4">' +
                        '<div class="small text-muted">Unidades</div>' +
                        '<div class="fw-bold"><span class="badge bg-secondary">' + filaMax.total_unidades + '</span></div>' +
                    '</div>' +
                '</div>' +
                '<div class="mt-1 text-center small text-muted">' + filaMax._pct + ' % del total</div>'
            );
            $('#detalleFamiliaDonut').removeClass('d-none');
        })
        .fail(function () {
            console.warn('No se pudo cargar el gráfico donut de familias.');
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

// ─── Actualizar KPI con comparación ───────────────────────────────────────────
function actualizarKPI(kpiId, valorActual, diferencia, valorComparar, anyoComp, esMoneda) {
    const formato = esMoneda ? formatEuro : (v => v);
    
    // Actualizar valor principal con flecha y porcentaje
    if (diferencia && valorComparar !== null && anyoComp > 0) {
        const icono = diferencia.tendencia === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
        const color = diferencia.tendencia === 'up' ? '#198754' : '#dc3545';
        const signo = diferencia.porcentaje >= 0 ? '+' : '';
        
        $('#kpi-' + kpiId).html(
            formato(valorActual) + ' ' +
            '<i class="fas ' + icono + '" style="color:' + color + '; font-size:0.7em; margin-left:5px"></i> ' +
            '<span style="color:' + color + '; font-weight:600; font-size:0.7em">' + signo + diferencia.porcentaje + '%</span>'
        );
        
        // Subtítulo: valor comparado con año
        $('#kpi-' + kpiId + '-sub').html(
            anyoComp + ': ' + formato(valorComparar)
        );
    } else {
        // Sin comparación
        $('#kpi-' + kpiId).text(formato(valorActual));
        
        // Mostrar texto genérico
        if (kpiId === 'total') {
            $('#kpi-' + kpiId + '-sub').text('Año completo');
        } else if (kpiId === 'pptos') {
            $('#kpi-' + kpiId + '-sub').text('Año completo');
        } else if (kpiId === 'ticket') {
            $('#kpi-' + kpiId + '-sub').text('Año completo');
        }
        // Nota: 'mes' se maneja directamente en cargarKPIs() con lógica especial
    }
}

// ─── Acciones de filtros ──────────────────────────────────────────────────────
function aplicarFiltros() {
    recargarTodo();
}

function limpiarFiltros() {
    const esteAnyo = new Date().getFullYear();
    const anyoAnterior = esteAnyo - 1;
    
    // Restaurar valores por defecto
    $('#filtroAnyoActual').val(esteAnyo);
    $('#filtroMesActual').val(0); // "Todos" - KPI #2 usará mes actual automáticamente
    $('#filtroAnyoComparar').val(anyoAnterior);
    
    // Reconfigurar eventos
    $('#filtroAnyoActual').trigger('change');
    
    recargarTodo();
}
