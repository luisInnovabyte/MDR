<?php
session_start();

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/SalidaAlmacen.php";

header('Content-Type: application/json; charset=utf-8');

$registro = new RegistroActividad();
$model = new SalidaAlmacen();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ---------------------------------------------------------------
    // debug_buscar — TEMPORAL: vuelca datos brutos del presupuesto en BD
    // ---------------------------------------------------------------
    case 'debug_buscar':
        $numero = trim($_POST['numero_presupuesto'] ?? '');
        header('Content-Type: application/json');
        echo json_encode($model->debug_presupuesto($numero), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;

    // buscar_presupuesto
    // POST: numero_presupuesto
    // Devuelve cabecera + necesidades + salida activa si existe
    // ---------------------------------------------------------------
    case 'buscar_presupuesto':
        $numero = htmlspecialchars(trim($_POST['numero_presupuesto'] ?? ''), ENT_QUOTES, 'UTF-8');
        if (empty($numero)) {
            echo json_encode(['success' => false, 'message' => 'Número de presupuesto requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ppto = $model->buscar_presupuesto_por_numero($numero);
        if (!$ppto) {
            // Diagnóstico: averiguar por qué no se encontró
            $diag = $model->get_presupuesto_diagnostico($numero);
            if (!$diag) {
                $msg = "Presupuesto '$numero' no existe en el sistema.";
            } elseif (!$diag['activo_presupuesto']) {
                $msg = "Presupuesto '$numero' está desactivado.";
            } elseif (!in_array($diag['estado_general_presupuesto'], ['aprobado', 'enviado'])) {
                $msg = "Presupuesto '$numero' está en estado '{$diag['estado_general_presupuesto']}'. Solo aprobados o enviados.";
            } elseif (!$diag['version_activa']) {
                $msg = "La versión v{$diag['version_actual_presupuesto']} de '$numero' no está activa.";
            } elseif (!$diag['cliente_existe']) {
                $msg = "El cliente (id={$diag['id_cliente']}) vinculado al presupuesto no existe en la BD.";
            } else {
                $msg = "Error desconocido. Número buscado: [" . $numero . "] (len=" . strlen($numero) . "). Revisa los logs.";
            }
            echo json_encode(['success' => false, 'message' => $msg], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Verificar estado (solo presupuestos aprobados o enviados)
        $estadosPermitidos = ['aprobado', 'enviado'];
        if (!in_array($ppto['estado_general_presupuesto'], $estadosPermitidos)) {
            echo json_encode([
                'success' => false,
                'message' => "El presupuesto está en estado '{$ppto['estado_general_presupuesto']}'. Solo se pueden preparar presupuestos aprobados o enviados."
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $necesidades = $model->get_necesidades_presupuesto($ppto['id_version_presupuesto']);
        $salidaActiva = $model->get_salida_activa($ppto['id_presupuesto']);

        echo json_encode([
            'success' => true,
            'presupuesto' => $ppto,
            'necesidades' => $necesidades,
            'salida_activa' => $salidaActiva
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // iniciar_salida
    // POST: id_presupuesto, id_version_presupuesto, id_usuario, numero_presupuesto
    // ---------------------------------------------------------------
    case 'iniciar_salida':
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        $id_version     = (int)($_POST['id_version_presupuesto'] ?? 0);
        $id_usuario     = (int)($_SESSION['id_usuario'] ?? 0);
        $numero         = htmlspecialchars(trim($_POST['numero_presupuesto'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (!$id_usuario) {
            echo json_encode(['success' => false, 'message' => 'Sesión no válida.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (!$id_presupuesto || !$id_version) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Evitar duplicar salida en_proceso
        $existente = $model->get_salida_activa($id_presupuesto);
        if ($existente) {
            echo json_encode([
                'success' => true,
                'message' => 'Ya existía una salida en proceso para este presupuesto.',
                'id_salida_almacen' => $existente['id_salida_almacen'],
                'ya_existia' => true
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $id_salida = $model->iniciar_salida($id_presupuesto, $id_version, $id_usuario, $numero);
        if ($id_salida) {
            echo json_encode([
                'success' => true,
                'message' => 'Sesión de picking iniciada.',
                'id_salida_almacen' => $id_salida,
                'ya_existia' => false
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al iniciar la sesión.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ---------------------------------------------------------------
    // escanear
    // POST: id_salida_almacen, codigo_elemento, es_backup (0|1)
    // ---------------------------------------------------------------
    case 'escanear':
        $id_salida      = (int)($_POST['id_salida_almacen'] ?? 0);
        $codigo         = htmlspecialchars(trim($_POST['codigo_elemento'] ?? ''), ENT_QUOTES, 'UTF-8');
        $es_backup      = (int)($_POST['es_backup'] ?? 0) === 1;

        if (!$id_salida || empty($codigo)) {
            echo json_encode(['success' => false, 'tipo' => 'error', 'message' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $model->escanear_elemento($id_salida, $codigo, $es_backup);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // progreso
    // POST: id_salida_almacen
    // ---------------------------------------------------------------
    case 'progreso':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        if (!$id_salida) {
            echo json_encode(['success' => false, 'message' => 'ID de salida requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $progreso = $model->get_progreso_salida($id_salida);
        echo json_encode(['success' => true, 'progreso' => $progreso], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // elementos_escaneados
    // POST: id_salida_almacen
    // ---------------------------------------------------------------
    case 'elementos_escaneados':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        if (!$id_salida) {
            echo json_encode(['success' => false, 'message' => 'ID de salida requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $elementos = $model->get_elementos_escaneados($id_salida);
        echo json_encode(['success' => true, 'elementos' => $elementos], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // completar
    // POST: id_salida_almacen
    // ---------------------------------------------------------------
    case 'completar':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        if (!$id_salida) {
            echo json_encode(['success' => false, 'message' => 'ID requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Verificar que esté completo antes de confirmar
        $progreso = $model->get_progreso_salida($id_salida);
        if (!$progreso['completo']) {
            echo json_encode([
                'success' => false,
                'message' => "Faltan {$progreso['total_requerido']} unidades por escanear ({$progreso['total_escaneado']} de {$progreso['total_requerido']})."
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $model->completar_salida($id_salida);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Salida completada. Todos los elementos marcados como ALQU.' : 'Error al completar la salida.'
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // cancelar
    // POST: id_salida_almacen
    // ---------------------------------------------------------------
    case 'cancelar':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        if (!$id_salida) {
            echo json_encode(['success' => false, 'message' => 'ID requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $model->cancelar_salida($id_salida);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Salida cancelada. Elementos devueltos a estado DISP.' : 'Error al cancelar.'
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // listar
    // POST: id_presupuesto (opcional, si se quiere filtrar)
    // ---------------------------------------------------------------
    case 'listar':
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        if ($id_presupuesto) {
            $salidas = $model->get_salidas_por_presupuesto($id_presupuesto);
        } else {
            $salidas = [];
        }
        echo json_encode([
            'draw' => intval($_POST['draw'] ?? 1),
            'recordsTotal' => count($salidas),
            'recordsFiltered' => count($salidas),
            'data' => $salidas
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // registrar_movimiento
    // POST: id_salida_almacen, id_elemento, id_ubicacion_destino, id_usuario, observaciones
    // ---------------------------------------------------------------
    case 'registrar_movimiento':
        $id_salida             = (int)($_POST['id_salida_almacen'] ?? 0);
        $id_elemento           = (int)($_POST['id_elemento'] ?? 0);
        $id_ubicacion_destino  = (int)($_POST['id_ubicacion_destino'] ?? 0);
        $id_usuario            = (int)($_SESSION['id_usuario'] ?? 0);
        $observaciones         = htmlspecialchars(trim($_POST['observaciones'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (!$id_salida || !$id_elemento || !$id_ubicacion_destino) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Buscar la línea del elemento en esta salida
        $linea = $model->get_linea_salida_por_elemento($id_salida, $id_elemento);
        if (!$linea) {
            echo json_encode(['success' => false, 'message' => 'El elemento no forma parte de esta salida.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Resolver origen (última ubicación conocida)
        $ubicActual = $model->get_ubicacion_actual($linea['id_linea_salida']);
        $id_origen = $ubicActual ? $ubicActual['id_ubicacion'] : null;

        $id_mov = $model->registrar_movimiento(
            $linea['id_linea_salida'],
            $id_origen,
            $id_ubicacion_destino,
            $id_usuario,
            !empty($observaciones) ? $observaciones : null
        );

        echo json_encode([
            'success' => (bool)$id_mov,
            'message' => $id_mov ? 'Movimiento registrado.' : 'Error al registrar movimiento.',
            'id_movimiento' => $id_mov
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // get_ubicacion_actual
    // POST: id_salida_almacen, id_elemento
    // ---------------------------------------------------------------
    case 'get_ubicacion_actual':
        $id_salida   = (int)($_POST['id_salida_almacen'] ?? 0);
        $id_elemento = (int)($_POST['id_elemento'] ?? 0);

        if (!$id_salida || !$id_elemento) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $linea = $model->get_linea_salida_por_elemento($id_salida, $id_elemento);
        if (!$linea) {
            echo json_encode(['success' => false, 'message' => 'Elemento no encontrado en esta salida.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ubicacion = $model->get_ubicacion_actual($linea['id_linea_salida']);
        echo json_encode([
            'success' => true,
            'ubicacion_actual' => $ubicacion,
            'linea_salida' => $linea
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // get_historial_movimientos
    // POST: id_salida_almacen, id_elemento
    // ---------------------------------------------------------------
    case 'get_historial_movimientos':
        $id_salida   = (int)($_POST['id_salida_almacen'] ?? 0);
        $id_elemento = (int)($_POST['id_elemento'] ?? 0);

        if (!$id_salida || !$id_elemento) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $linea = $model->get_linea_salida_por_elemento($id_salida, $id_elemento);
        if (!$linea) {
            echo json_encode(['success' => false, 'message' => 'Elemento no encontrado en esta salida.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $historial = $model->get_historial_movimientos($linea['id_linea_salida']);
        echo json_encode(['success' => true, 'historial' => $historial], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // mapa_ubicaciones
    // POST: id_salida_almacen
    // ---------------------------------------------------------------
    case 'mapa_ubicaciones':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        if (!$id_salida) {
            echo json_encode(['success' => false, 'message' => 'ID requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $mapa = $model->get_mapa_ubicaciones($id_salida);
        $ubicaciones = $model->get_ubicaciones_del_presupuesto($id_salida);
        echo json_encode([
            'success' => true,
            'mapa' => $mapa,
            'ubicaciones_disponibles' => $ubicaciones
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ---------------------------------------------------------------
    // comparar — compara pool de códigos contra necesidades del presupuesto
    // POST: id_salida_almacen, codigos[] (array de códigos de elemento)
    // Devuelve: { success, correctos[], sobran[], no_relacionados[], faltan[] }
    // ---------------------------------------------------------------
    case 'comparar':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        $codigos   = $_POST['codigos'] ?? [];
        if (!$id_salida || empty($codigos)) {
            echo json_encode(['success' => false, 'message' => 'Parámetros requeridos: id_salida_almacen, codigos[]'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $resultado = $model->comparar_pool($id_salida, (array)$codigos);
        if (isset($resultado['error'])) {
            echo json_encode(['success' => false, 'message' => $resultado['error']], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array_merge(['success' => true], $resultado), JSON_UNESCAPED_UNICODE);
        }
        break;

    // ---------------------------------------------------------------
    // confirmar — persiste el pool validado en linea_salida_almacen
    // POST: id_salida_almacen, pool (JSON string: [{codigo_elemento, modo, id_linea_ppto?},...])
    // Devuelve: { success, message }
    // ---------------------------------------------------------------
    case 'confirmar':
        $id_salida = (int)($_POST['id_salida_almacen'] ?? 0);
        $pool_json = $_POST['pool'] ?? '';
        if (!$id_salida || empty($pool_json)) {
            echo json_encode(['success' => false, 'message' => 'Parámetros requeridos: id_salida_almacen, pool'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $pool = json_decode($pool_json, true);
        if (!is_array($pool)) {
            echo json_encode(['success' => false, 'message' => 'Pool JSON inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $ok = $model->confirmar_pool($id_salida, $pool);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Picking confirmado correctamente' : 'Error al confirmar el pool'
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode(['success' => false, 'message' => "Operación '$op' no reconocida."], JSON_UNESCAPED_UNICODE);
        break;
}