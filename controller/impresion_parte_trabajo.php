<?php
/**
 * impresion_parte_trabajo.php
 *
 * Wrapper de albarán de carga para técnicos.
 * Registra el documento en `documento_presupuesto` y devuelve la URL
 * al generador PDF existente (impresionpartetrabajo_m2_pdf_es.php).
 *
 * POST op=generar_url
 *   - id_presupuesto  (requerido)
 *   - numero_version  (opcional)
 *
 * POST op=stream
 *   - id_documento_ppto  (requerido)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/funciones.php";
require_once __DIR__ . "/../models/DocumentoPresupuesto.php";

$registro = new RegistroActividad();
$docModel = new DocumentoPresupuesto();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════
    // GENERAR_URL — registra documento y devuelve URL al generador PDF
    // ══════════════════════════════════════════════════════════
    case "generar_url":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        $numero_version = !empty($_POST['numero_version']) ? (int)$_POST['numero_version'] : null;

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            // Registrar documento parte de trabajo
            $datos_doc = [
                'id_presupuesto'  => $id_presupuesto,
                'tipo_documento_ppto' => 'parte_trabajo',
                'numero_version'  => $numero_version,
            ];

            $id_doc = $docModel->insert_documento($datos_doc);

            if (!$id_doc) {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el documento'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Construir URL al generador existente
            $params = http_build_query([
                'op'              => 'cli_esp',
            ]);
            $post_params = [
                'id_presupuesto' => $id_presupuesto,
                'numero_version' => $numero_version ?? '',
                'id_documento_ppto' => $id_doc,
            ];

            $url_generador = 'controller/impresionpartetrabajo_m2_pdf_es.php?' . $params;

            $registro->registrarActividad(
                'admin',
                'impresion_parte_trabajo.php',
                'generar_url',
                "Parte trabajo doc ID: $id_doc para presupuesto: $id_presupuesto",
                'info'
            );

            echo json_encode([
                'success'           => true,
                'id_documento_ppto' => $id_doc,
                'url_pdf'           => $url_generador,
                'post_params'       => $post_params,
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin', 'impresion_parte_trabajo.php', 'generar_url',
                "Error: " . $e->getMessage(), 'error'
            );
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════
    // LISTA — documentos parte_trabajo de un presupuesto
    // ══════════════════════════════════════════════════════════
    case "listar":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $docs = $docModel->get_documentos_por_tipo($id_presupuesto, 'parte_trabajo');
        echo json_encode(['success' => true, 'data' => $docs], JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Operación '$op' no reconocida"], JSON_UNESCAPED_UNICODE);
        break;
}
