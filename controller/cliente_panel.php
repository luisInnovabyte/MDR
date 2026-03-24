<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/ClientePanel.php";

$registro = new RegistroActividad();
$panel    = new ClientePanel();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ─── KPIs del cliente ─────────────────────────────────────────────────────
    case 'kpis':
        $id_cliente = isset($_POST['id_cliente']) ? (int) $_POST['id_cliente'] : 0;

        if ($id_cliente <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no válido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $panel->getKpisCliente($id_cliente);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $datos,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Presupuestos del cliente (DataTable) ─────────────────────────────────
    case 'presupuestos':
        $id_cliente = isset($_POST['id_cliente']) ? (int) $_POST['id_cliente'] : 0;

        if ($id_cliente <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no válido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $panel->getPresupuestosPorCliente($id_cliente);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = $row; // TODO: formatear columnas para DataTable (acciones, badges...)
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Documentos / Facturas del cliente (DataTable) ────────────────────────
    case 'documentos':
        $id_cliente = isset($_POST['id_cliente']) ? (int) $_POST['id_cliente'] : 0;

        if ($id_cliente <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no válido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $panel->getDocumentosPorCliente($id_cliente);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = $row; // TODO: formatear columnas para DataTable
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─── Pagos del cliente (DataTable) ────────────────────────────────────────
    case 'pagos':
        $id_cliente = isset($_POST['id_cliente']) ? (int) $_POST['id_cliente'] : 0;

        if ($id_cliente <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no válido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $panel->getPagosPorCliente($id_cliente);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = $row; // TODO: formatear columnas para DataTable
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
