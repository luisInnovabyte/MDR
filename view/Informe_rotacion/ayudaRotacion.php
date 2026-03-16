<!-- Modal de Ayuda - Informe de Rotación de Inventario -->
<div class="modal fade" id="modalAyudaRotacion" tabindex="-1" aria-labelledby="modalAyudaRotacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaRotacionLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda — Informe de Rotación de Inventario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- ¿Qué es este informe? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        ¿Qué es el Informe de Rotación de Inventario?
                    </h6>
                    <p class="text-muted">
                        Este informe muestra <strong>con qué frecuencia se alquila cada artículo del catálogo</strong>
                        en el período seleccionado. Es una herramienta de gestión que permite al gerente identificar
                        qué equipos generan mayor actividad comercial, cuáles están infrautilizados y cuáles nunca
                        han salido del almacén.
                    </p>
                    <div class="alert alert-info py-2 mb-0">
                        <small>
                            <i class="bi bi-lightbulb me-1"></i>
                            <strong>Para qué sirve:</strong> detectar exceso de stock, planificar compras, 
                            identificar equipos candidatos a baja o a mayor promoción comercial.
                        </small>
                    </div>
                </div>

                <!-- Semáforo de estados -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-traffic-light me-2"></i>
                        Sistema de Semáforo — ¿Qué significa cada estado?
                    </h6>
                    <p class="text-muted small mb-2">
                        Cada artículo recibe automáticamente un color según los días transcurridos desde su último alquiler
                        dentro del período analizado:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Estado</th>
                                    <th>Criterio</th>
                                    <th>Interpretación</th>
                                    <th>Acción recomendada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>Último uso hace <strong>≤ 30 días</strong></td>
                                    <td>Equipo con alta demanda, se alquila habitualmente</td>
                                    <td>Asegurar disponibilidad; valorar ampliar el stock</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">Moderado</span></td>
                                    <td>Último uso hace entre <strong>31 y 90 días</strong></td>
                                    <td>Rotación regular pero no frecuente</td>
                                    <td>Vigilar tendencia; puede ser estacional</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Inactivo</span></td>
                                    <td>Último uso hace <strong>> 90 días</strong></td>
                                    <td>El equipo lleva más de 3 meses sin salir del almacén</td>
                                    <td>Revisar si sigue en catálogo; evaluar promoción o baja</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">Nunca usado</span></td>
                                    <td><strong>Sin historial</strong> de alquiler</td>
                                    <td>El artículo nunca ha aparecido en ningún presupuesto aprobado</td>
                                    <td>Verificar si está correctamente catalogado; considerar baja</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-2 py-2 mb-0">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Importante:</strong> el semáforo se calcula sobre el período seleccionado en el filtro.
                            Si se elige "Histórico completo", el umbral de 90 días se aplica desde el último alquiler
                            registrado hasta hoy, independientemente del año.
                        </small>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-card-checklist me-2"></i>
                        Cómo interpretar los indicadores (KPIs)
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-secondary">
                                    <i class="fas fa-boxes me-1"></i> Artículos Activos en catálogo
                                </p>
                                <p class="text-muted small mb-0">
                                    Total de artículos que actualmente están marcados como activos en la base de datos.
                                    Es el denominador base para todos los porcentajes.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-success">
                                    <i class="fas fa-check-circle me-1"></i> Usados en período
                                </p>
                                <p class="text-muted small mb-0">
                                    Artículos que han aparecido en al menos un presupuesto aprobado dentro del período
                                    filtrado. Mide la cartera activa desde el punto de vista comercial.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-primary">
                                    <i class="fas fa-percentage me-1"></i> % Uso
                                </p>
                                <p class="text-muted small mb-0">
                                    Porcentaje del catálogo activo que ha generado al menos un alquiler en el período.
                                    Cuanto más alto, mejor aprovechado está el inventario.
                                    <br><span class="text-secondary">Ejemplo: 65 % significa que 35 % del catálogo
                                    no ha generado ingresos en el período.</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-danger">
                                    <i class="fas fa-exclamation-circle me-1"></i> Sin uso en período
                                </p>
                                <p class="text-muted small mb-0">
                                    Artículos del catálogo que no han generado ningún alquiler en el período seleccionado.
                                    Son los candidatos prioritarios a revisar, ofertar o dar de baja.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-bar-chart-line-fill me-2"></i>
                        Los tres gráficos del Análisis de Inventario
                    </h6>
                    <div class="accordion" id="accordionGraficos">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTop10">
                                    <i class="bi bi-trophy me-2 text-warning"></i> Top 10 más alquilados
                                </button>
                            </h2>
                            <div id="collapseTop10" class="accordion-collapse collapse" data-bs-parent="#accordionGraficos">
                                <div class="accordion-body text-muted small">
                                    Barras horizontales que muestran los 10 artículos con más presupuestos aprobados
                                    en el período activo. Permite de un vistazo saber cuál es el equipo "estrella"
                                    del negocio. Se filtra por familia si se ha seleccionado una en el desplegable.
                                    <br><br>
                                    <strong>Cómo leerlo:</strong> cuanto más larga la barra, más veces ha salido ese artículo
                                    en presupuestos aprobados. El eje X muestra el número absoluto de usos.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDonut">
                                    <i class="bi bi-pie-chart me-2 text-info"></i> Resumen por familia
                                </button>
                            </h2>
                            <div id="collapseDonut" class="accordion-collapse collapse" data-bs-parent="#accordionGraficos">
                                <div class="accordion-body text-muted small">
                                    Gráfico de rosco (donut) que distribuye el total de usos entre las distintas familias
                                    de artículos. Debajo del gráfico se muestra una tabla con el desglose numérico y el
                                    porcentaje exacto que representa cada familia sobre el total.
                                    <br><br>
                                    <strong>Cómo leerlo:</strong> las familias con mayor porcentaje son las que más peso
                                    tienen en el negocio. Si una familia representa más del 50 % podría indicar dependencia
                                    excesiva de esa categoría.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTendencia">
                                    <i class="bi bi-graph-up-arrow me-2 text-success"></i> Tendencia mensual
                                </button>
                            </h2>
                            <div id="collapseTendencia" class="accordion-collapse collapse" data-bs-parent="#accordionGraficos">
                                <div class="accordion-body text-muted small">
                                    Gráfico de líneas que compara los presupuestos aprobados mes a mes entre el
                                    <strong>año actual</strong> (línea azul continua) y el <strong>año anterior</strong>
                                    (línea gris discontinua). Cubre siempre los últimos 12 meses naturales.
                                    <br><br>
                                    <strong>Cómo leerlo:</strong> si la línea azul está por encima de la gris, el negocio
                                    crece respecto al año pasado. Picos coinciden con épocas de alta demanda
                                    (eventos, festivales, temporada alta).
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tabla de detalle — columnas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-table me-2"></i>
                        Columnas de la tabla "Detalle de Rotación por Artículo"
                    </h6>
                    <p class="text-muted small mb-2">
                        La tabla inferior muestra una fila por cada artículo del catálogo activo.
                        A continuación se explica qué significa cada columna:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width:14%">Columna</th>
                                    <th>Descripción</th>
                                    <th style="width:28%">Cómo interpretarlo</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr>
                                    <td class="fw-semibold">Código</td>
                                    <td>Código único del artículo en el catálogo (ej. <em>SPOT-LED-200</em>).</td>
                                    <td>Permite localizar el artículo en el sistema rápidamente.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Artículo</td>
                                    <td>Nombre descriptivo del artículo tal como aparece en el catálogo y presupuestos.</td>
                                    <td>Identifica el equipo de forma legible.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Familia</td>
                                    <td>Categoría a la que pertenece el artículo (ej. Iluminación, Sonido, Vídeo…).</td>
                                    <td>Agrupa equipos del mismo tipo para facilitar el análisis por categoría.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Nº Usos</td>
                                    <td>
                                        Número de <strong>presupuestos aprobados</strong> en los que aparece este artículo
                                        dentro del período seleccionado.
                                    </td>
                                    <td>
                                        Indica cuántos trabajos distintos han necesitado este equipo.
                                        <strong>Alto = alta demanda</strong>; bajo o cero = poca rotación.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Uds. totales</td>
                                    <td>
                                        Suma de unidades del artículo pedidas en todos los presupuestos aprobados
                                        del período. Si en un presupuesto se piden 3 unidades, cuentan 3.
                                    </td>
                                    <td>
                                        Complementa el Nº Usos: un artículo con 5 usos y 30 unidades indica que
                                        normalmente se piden muchas unidades a la vez.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Último uso</td>
                                    <td>
                                        Fecha del presupuesto más reciente (aprobado) en que aparece este artículo,
                                        en formato <strong>DD/MM/AAAA</strong>.
                                    </td>
                                    <td>
                                        Una fecha muy antigua (o el guión <em>—</em>) indica que el artículo lleva mucho
                                        tiempo sin usarse.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Días sin uso</td>
                                    <td>
                                        Número de días transcurridos desde el "Último uso" hasta hoy.
                                        Si el artículo nunca ha sido usado, muestra <em>—</em>.
                                    </td>
                                    <td>
                                        Es el valor numérico en que se basa el semáforo de estado.
                                        A mayor número, más tiempo lleva el equipo parado en almacén.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tendencia</td>
                                    <td>
                                        Icono visual que compara el uso del artículo en los últimos 30 días
                                        con los 30 días anteriores:
                                        <ul class="mb-0 mt-1">
                                            <li>🔼 <strong>Subiendo</strong> — más usos recientes que antes</li>
                                            <li>🔽 <strong>Bajando</strong> — menos usos recientes</li>
                                            <li>➡️ <strong>Estable</strong> — uso similar en ambos períodos</li>
                                        </ul>
                                    </td>
                                    <td>
                                        Detecta cambios de demanda recientes. Un artículo que sube puede necesitar
                                        más stock; uno que baja puede estar siendo sustituido.
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Estado</td>
                                    <td>
                                        Semáforo de rotación calculado automáticamente según los días transcurridos
                                        desde el último uso (ver explicación del semáforo más arriba).
                                    </td>
                                    <td>
                                        Resumen visual del nivel de actividad del artículo. Permite escanear
                                        rápidamente toda la tabla buscando artículos en rojo o gris.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-funnel-fill me-2"></i>
                        Uso de los filtros
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1"><i class="bi bi-calendar3 me-1"></i> Filtro de Período</p>
                                <p class="text-muted small mb-0">
                                    Acota el análisis a una ventana temporal:
                                    <ul class="mt-1 mb-0 text-muted small">
                                        <li><strong>Últimos 90 días</strong> (por defecto) — trimestre actual</li>
                                        <li><strong>Últimos 180 días</strong> — semestre</li>
                                        <li><strong>Último año</strong> — 365 días naturales</li>
                                        <li><strong>Histórico completo</strong> — toda la vida del dato en el sistema</li>
                                    </ul>
                                    Al cambiar el período, todos los KPIs, gráficos y la tabla se recalculan.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1"><i class="bi bi-folder2 me-1"></i> Filtro de Familia</p>
                                <p class="text-muted small mb-0">
                                    Limita el análisis a una categoría de artículos concreta.
                                    Útil cuando el gerente quiere analizar solo, por ejemplo, el rendimiento
                                    de todos los artículos de Iluminación o Sonido de forma independiente.
                                    <br><br>
                                    Seleccionar "Todas las familias" muestra el inventario completo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preguntas frecuentes -->
                <div class="help-section mb-2">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-chat-left-text-fill me-2"></i>
                        Preguntas frecuentes
                    </h6>
                    <div class="accordion" id="accordionFAQ">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    ¿Un artículo "Inactivo" significa que está estropeado?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    No. El estado hace referencia únicamente a la <strong>frecuencia de alquiler</strong>,
                                    no al estado físico del equipo. Un artículo marcado como Inactivo significa que
                                    no ha aparecido en ningún presupuesto aprobado en el período analizado.
                                    Puede estar perfectamente operativo pero sin demanda comercial.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    ¿Por qué un artículo aparece con "Nº Usos = 0" si sé que se ha alquilado?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    El informe solo tiene en cuenta presupuestos en estado <strong>Aprobado</strong>.
                                    Si el alquiler quedó en presupuesto pendiente, rechazado o cancelado, no computa
                                    en este informe. También puede ocurrir que el período filtrado no cubra la fecha
                                    de ese alquiler: pruebe a ampliar el período a "Histórico completo".
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    ¿Con qué frecuencia se actualiza este informe?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    Los datos son <strong>en tiempo real</strong>: cada vez que se abre la pantalla
                                    o se pulsa el botón "Actualizar", se recalculan contra la base de datos.
                                    No hay caché ni datos preaggregados: lo que muestra el informe refleja exactamente
                                    el estado actual de los presupuestos aprobados en el sistema.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    ¿Puedo exportar estos datos?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    Sí. La barra de botones sobre la tabla permite exportar a
                                    <strong>Excel</strong>, <strong>PDF</strong>, copiar al portapapeles o imprimir.
                                    También puede usar "Visibilidad de columnas" para ocultar las columnas que no
                                    necesite antes de exportar, y el buscador para filtrar artículos concretos.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div><!-- modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
