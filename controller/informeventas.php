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
        $anyo_actual    = isset($_POST['anyo_actual'])    ? (int) $_POST['anyo_actual']    : 0;
        $mes_actual     = isset($_POST['mes_actual'])     ? (int) $_POST['mes_actual']     : 0;
        $anyo_comparar  = isset($_POST['anyo_comparar'])  ? (int) $_POST['anyo_comparar']  : 0;
        $mes_comparar   = isset($_POST['mes_comparar'])   ? (int) $_POST['mes_comparar']   : 0;

        $datos = $informe->getKpisVentas($anyo_actual, $mes_actual, $anyo_comparar, $mes_comparar);

        header('Content-Type: application/json');

        if (empty($datos)) {
            echo json_encode(['success' => false, 'message' => 'Sin datos'], JSON_UNESCAPED_UNICODE);
            break;
        }

        echo json_encode([
            'success' => true,
            'data'    => $datos,
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
        $anyo = isset($_POST['anyo']) ? (int) $_POST['anyo'] : 0;
        $mes  = isset($_POST['mes'])  ? (int) $_POST['mes']  : 0;

        $datos = $informe->getTopClientes($anyo, 0, $mes);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = [
                'id_cliente'              => (int) $row['id_cliente'],
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

    // ─── Gráfico mensual comparativo (barras) ─────────────────────────────────
    case 'grafico_mensual_comparativo':
        $anyo_actual   = isset($_POST['anyo_actual'])   ? (int) $_POST['anyo_actual']   : (int) date('Y');
        $anyo_comparar = isset($_POST['anyo_comparar']) ? (int) $_POST['anyo_comparar'] : 0;

        // Obtener datos del año actual
        $datosActual = $informe->getVentasPorMes($anyo_actual);
        $valoresActual = array_column($datosActual, 'total');
        
        // Obtener datos del año a comparar (si existe)
        $valoresComparar = null;
        if ($anyo_comparar > 0) {
            $datosComparar = $informe->getVentasPorMes($anyo_comparar);
            $valoresComparar = array_column($datosComparar, 'total');
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => [
                'actual'    => $valoresActual,
                'comparar'  => $valoresComparar,
                'anyo_actual'   => $anyo_actual,
                'anyo_comparar' => $anyo_comparar
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Gráfico por familias (líneas) ────────────────────────────────────────
    case 'grafico_familias':
        $anyo_actual   = isset($_POST['anyo_actual'])   ? (int) $_POST['anyo_actual']   : (int) date('Y');
        $anyo_comparar = isset($_POST['anyo_comparar']) ? (int) $_POST['anyo_comparar'] : 0;

        // Obtener datos del año actual
        $datosActual = $informe->getIngresosPorFamiliaMensual($anyo_actual);
        
        // Obtener datos del año a comparar (si existe)
        $datosComparar = null;
        if ($anyo_comparar > 0) {
            $datosComparar = $informe->getIngresosPorFamiliaMensual($anyo_comparar);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => [
                'actual'    => $datosActual,
                'comparar'  => $datosComparar,
                'anyo_actual'   => $anyo_actual,
                'anyo_comparar' => $anyo_comparar
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
