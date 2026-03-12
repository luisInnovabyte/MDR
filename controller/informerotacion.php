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

        $datos = $informe->getKpisRotacion($diasPeriodo);

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

    // ─── Top artículos (para gráfico de barras horizontal) ───────────────────
    case 'top_articulos':
        $diasPeriodo = isset($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 0;
        $limite      = isset($_POST['limite'])       ? (int) $_POST['limite']       : 10;

        $datos = $informe->getTopArticulos($diasPeriodo, $limite);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $datos,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Tabla completa de rotación (para DataTable) ──────────────────────────
    case 'tabla_rotacion':
        $filtros = [
            'id_familia'   => !empty($_POST['id_familia'])   ? (int) $_POST['id_familia']   : null,
            'dias_periodo' => !empty($_POST['dias_periodo']) ? (int) $_POST['dias_periodo'] : 0,
        ];

        $datos = $informe->getTablaRotacion($filtros);
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

            $data[] = [
                'codigo_articulo'          => htmlspecialchars($row['codigo_articulo'], ENT_QUOTES, 'UTF-8'),
                'nombre_articulo'          => htmlspecialchars($row['nombre_articulo'], ENT_QUOTES, 'UTF-8'),
                'nombre_familia'           => htmlspecialchars($row['nombre_familia'],  ENT_QUOTES, 'UTF-8'),
                'total_usos'               => (int) $row['total_usos'],
                'total_unidades_alquiladas'=> (int) $row['total_unidades_alquiladas'],
                'ultimo_uso'               => $ultimoUso ?? '—',
                'dias_desde_ultimo_uso'    => $diasDesdeUso !== null ? (int) $diasDesdeUso : '—',
                'estado_rotacion'          => $badge,
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
        echo json_encode([
            'success' => true,
            'data'    => $familias,
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
