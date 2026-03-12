<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/InformeVentas.php";

$registro = new RegistroActividad();
$informe  = new InformeVentas();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ─── KPIs globales ────────────────────────────────────────────────────────
    case 'kpis':
        $anyo = isset($_POST['anyo']) ? (int) $_POST['anyo'] : 0;
        $mes  = isset($_POST['mes'])  ? (int) $_POST['mes']  : 0;

        $datos = $informe->getKpisVentas($anyo, $mes);

        header('Content-Type: application/json');

        if (empty($datos)) {
            echo json_encode(['success' => false, 'message' => 'Sin datos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        echo json_encode([
            'success' => true,
            'data'    => [
                'total_facturado'  => $datos['total_facturado'] ?? 0,
                'num_presupuestos' => $datos['num_presupuestos'] ?? 0,
                'ticket_promedio'  => $datos['ticket_promedio']  ?? 0,
                'mes_top'          => $datos['mes_top']          ?? null,
                'mes_top_total'    => $datos['mes_top_total']    ?? 0,
            ],
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Datos para gráfico mensual ───────────────────────────────────────────
    case 'grafico_mensual':
        $anyo = isset($_POST['anyo']) ? (int) $_POST['anyo'] : (int) date('Y');

        $filas = $informe->getVentasPorMes($anyo);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $filas,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Top clientes (para DataTable) ────────────────────────────────────────
    case 'top_clientes':
        $anyo   = isset($_POST['anyo'])   ? (int) $_POST['anyo']   : 0;
        $mes    = isset($_POST['mes'])    ? (int) $_POST['mes']    : 0;
        $limite = isset($_POST['limite']) ? (int) $_POST['limite'] : 10;

        $datos = $informe->getTopClientes($anyo, $limite, $mes);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = [
                'nombre_completo_cliente' => htmlspecialchars($row['nombre_completo_cliente'], ENT_QUOTES, 'UTF-8'),
                'num_presupuestos'        => (int) $row['num_presupuestos'],
                'total_facturado'         => number_format((float) $row['total_facturado'], 2, ',', '.') . ' €',
                '_total_num'              => (float) $row['total_facturado'],
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

    // ─── Ventas por familia (para DataTable) ──────────────────────────────────
    case 'por_familia':
        $anyo = isset($_POST['anyo']) ? (int) $_POST['anyo'] : 0;
        $mes  = isset($_POST['mes'])  ? (int) $_POST['mes']  : 0;

        $datos = $informe->getVentasPorFamilia($anyo, $mes);
        $data  = [];

        // Calcular total global para la columna de porcentaje
        $totalGlobal = array_sum(array_column($datos, 'total_facturado'));

        foreach ($datos as $row) {
            $pct    = $totalGlobal > 0
                ? round(($row['total_facturado'] / $totalGlobal) * 100, 1)
                : 0;

            $data[] = [
                'nombre_familia'   => htmlspecialchars($row['nombre_familia'], ENT_QUOTES, 'UTF-8'),
                'num_presupuestos' => (int) $row['num_presupuestos'],
                'total_unidades'   => (int) $row['total_unidades'],
                'total_facturado'  => number_format((float) $row['total_facturado'], 2, ',', '.') . ' €',
                'porcentaje'       => $pct . ' %',
                '_total_num'       => (float) $row['total_facturado'],
                '_pct'             => $pct,
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

    // ─── Años disponibles (para el select de año) ─────────────────────────────
    case 'anyos_disponibles':
        $anyos = $informe->getAnyosDisponibles();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $anyos,
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
