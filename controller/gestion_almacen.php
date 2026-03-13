<?php
// controller/gestion_almacen.php — Gestión de elementos en almacén (mobile-first)

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Elemento.php";
require_once "../models/Estado_elemento.php";

header('Content-Type: application/json; charset=utf-8');

$registro       = new RegistroActividad();
$modelElemento  = new Elemento();
$modelEstado    = new Estado_elemento();

$op = $_GET['op'] ?? $_POST['op'] ?? null;

switch ($op) {

    // ─────────────────────────────────────────────────────────────────────────
    // buscar — encuentra elemento por código/cód. barras y devuelve info + estados
    // ─────────────────────────────────────────────────────────────────────────
    case 'buscar':
        $codigo = strtoupper(trim($_POST['codigo_elemento'] ?? ''));

        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Código vacío'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $elemento = $modelElemento->get_elemento_by_codigo($codigo);

        if (!$elemento) {
            echo json_encode([
                'success' => false,
                'message' => 'Elemento no encontrado: ' . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8')
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Si el elemento está en PREP o ALQU, buscar el presupuesto asociado
        $presupuesto_activo = null;
        if (in_array($elemento['codigo_estado_elemento'], ['PREP', 'ALQU'])) {
            $presupuesto_activo = $modelElemento->get_presupuesto_activo_elemento($elemento['id_elemento']);
        }

        // Cargar todos los estados activos para el select del formulario
        $estados = $modelEstado->get_estado_elemento();

        echo json_encode([
            'success'            => true,
            'elemento'           => $elemento,
            'estados'            => $estados,
            'presupuesto_activo' => $presupuesto_activo
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ─────────────────────────────────────────────────────────────────────────
    // actualizar — guarda estado + próximo mantenimiento
    // ─────────────────────────────────────────────────────────────────────────
    case 'actualizar':
        $id_elemento        = !empty($_POST['id_elemento'])        ? (int)$_POST['id_elemento']        : null;
        $id_estado_elemento = !empty($_POST['id_estado_elemento'])  ? (int)$_POST['id_estado_elemento']  : null;
        $proximo_mant       = !empty($_POST['proximo_mantenimiento_elemento'])
            ? trim($_POST['proximo_mantenimiento_elemento'])
            : null;

        if (empty($id_elemento) || empty($id_estado_elemento)) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan datos obligatorios (id_elemento, id_estado_elemento)'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            $resultado = $modelElemento->update_datos_almacen($id_elemento, $id_estado_elemento, $proximo_mant);

            if ($resultado !== false) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Elemento actualizado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el elemento'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'gestion_almacen.php',
                'actualizar',
                'Error: ' . $e->getMessage(),
                'error'
            );
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
