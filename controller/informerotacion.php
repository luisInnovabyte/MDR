<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/InformeRotacion.php";

$registro = new RegistroActividad();
$informe  = new InformeRotacion();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ─── KPIs globales de rotación ────────────────────────────────────────────
    case 'kpis':
        $diasPeriodo = isset($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 90;
        $idFamilia   = !empty($_POST['id_familia'])  ? (int) $_POST['id_familia']  : null;

        $datos = $informe->getKpisRotacion($diasPeriodo, $idFamilia);

        header('Content-Type: application/json');

        if (empty($datos)) {
            echo json_encode(['error' => 'Sin datos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // ─── Top artículos (para gráfico de barras horizontal) ───────────────────
    case 'top_articulos':
        $diasPeriodo = isset($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 0;
        $limite      = isset($_POST['limite'])       ? (int) $_POST['limite']       : 10;
        $idFamilia   = !empty($_POST['id_familia'])  ? (int) $_POST['id_familia']  : null;

        $datos = $informe->getTopArticulos($diasPeriodo, $limite, $idFamilia);

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // ─── Tabla completa de rotación (para DataTable) ──────────────────────────
    case 'tabla_rotacion':
        $filtros = [
            'id_familia'   => !empty($_POST['id_familia'])   ? (int) $_POST['id_familia']   : null,
            'dias_periodo' => !empty($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 0,
        ];

        $datos = $informe->getTablaRotacion($filtros);

        // Comparativa tendencia (sólo si hay período definido)
        $tendencia = [];
        if (!empty($filtros['dias_periodo']) && $filtros['dias_periodo'] > 0) {
            $tendencia = $informe->getTendenciaArticulos($filtros['dias_periodo']);
        }

        $data  = [];
        $hoy = new DateTime();

        foreach ($datos as $row) {
            $diasDesdeUso = $row['dias_desde_ultimo_uso'];
            $ultimoUso    = $row['ultimo_uso'];

            // Semáforo de actividad
            if ($row['total_usos'] == 0) {
                $badge = '<span class="badge bg-secondary">Nunca usado</span>';
            } elseif ($diasDesdeUso !== null && $diasDesdeUso <= 30) {
                $badge = '<span class="badge bg-success">Activo</span>';
            } elseif ($diasDesdeUso !== null && $diasDesdeUso <= 90) {
                $badge = '<span class="badge bg-warning text-dark">Moderado</span>';
            } else {
                $badge = '<span class="badge bg-danger">Inactivo</span>';
            }

            // Tendencia
            $id = (int) $row['id_articulo'];
            if (empty($tendencia) || !isset($tendencia[$id])) {
                $iconoTendencia = '<span class="text-muted small">—</span>';
            } else {
                $t = $tendencia[$id];
                if ($t['usos_actual'] > $t['usos_anterior']) {
                    $iconoTendencia = '<span class="text-success fw-bold fs-5" title="Subiendo: '.$t['usos_actual'].' vs '.$t['usos_anterior'].' usos">▲</span>';
                } elseif ($t['usos_actual'] < $t['usos_anterior']) {
                    $iconoTendencia = '<span class="text-danger fw-bold fs-5" title="Bajando: '.$t['usos_actual'].' vs '.$t['usos_anterior'].' usos">▼</span>';
                } else {
                    $iconoTendencia = '<span class="text-secondary fs-5" title="Estable: '.$t['usos_actual'].' usos">→</span>';
                }
            }

            $data[] = [
                'codigo_articulo'           => htmlspecialchars($row['codigo_articulo'], ENT_QUOTES, 'UTF-8'),
                'nombre_articulo'           => htmlspecialchars($row['nombre_articulo'], ENT_QUOTES, 'UTF-8'),
                'nombre_familia'            => htmlspecialchars($row['nombre_familia'],  ENT_QUOTES, 'UTF-8'),
                'total_usos'                => (int) $row['total_usos'],
                'total_unidades_alquiladas' => (int) $row['total_unidades_alquiladas'],
                'ultimo_uso'                => $ultimoUso ?? '—',
                'dias_desde_ultimo_uso'     => $diasDesdeUso !== null ? (int) $diasDesdeUso : '—',
                'tendencia'                 => $iconoTendencia,
                'estado_rotacion'           => $badge,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Familias disponibles (para el selector de filtro) ───────────────────
    case 'familias':
        $familias = $informe->getFamilias();

        header('Content-Type: application/json');
        echo json_encode($familias, JSON_UNESCAPED_UNICODE);
        break;

    // ─── Resumen por familia (para gráfico donut) ─────────────────────────────
    case 'resumen_familias':
        $diasPeriodo = isset($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 0;
        $datos = $informe->getResumenFamilias($diasPeriodo);

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    // ─── Tendencia mensual (para gráfico de líneas) ───────────────────────────
    case 'tendencia_mensual':
        $datos = $informe->getTendenciaMensual();

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
