<?php
/**
 * db_copilot.php
 * Endpoint de solo lectura para acceso de GitHub Copilot a la base de datos.
 * 
 * USO:
 *   ?token=TOKEN_SECRETO&op=tablas
 *   ?token=TOKEN_SECRETO&op=vistas
 *   ?token=TOKEN_SECRETO&op=estructura&tabla=nombre_tabla
 *   ?token=TOKEN_SECRETO&op=datos&tabla=nombre_tabla&limit=20
 *   ?token=TOKEN_SECRETO&op=sql&query=SELECT ...
 * 
 * SEGURIDAD:
 *   - Solo operaciones de lectura (SELECT, SHOW, DESCRIBE)
 *   - Token requerido en cada petición
 *   - Solo accesible desde entorno local/desarrollo
 * 
 * @author  GitHub Copilot endpoint
 * @version 1.0
 * @date    2026-02-19
 */

// ── Token de acceso ─────────────────────────────────────────────────────────
define('COPILOT_TOKEN', 'mdr_copilot_X7k9pQ2nL5vR8wZ3');

// ── Cabeceras ────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');

// ── Dependencias ─────────────────────────────────────────────────────────────
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';

$registro = new RegistroActividad();

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Respuesta de error y fin de ejecución.
 */
function respError(string $mensaje, int $http = 400): never
{
    http_response_code($http);
    echo json_encode(['success' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Respuesta de éxito.
 */
function respOk(mixed $data): never
{
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Valida que la consulta sea de solo lectura.
 * Permite: SELECT, SHOW, DESCRIBE, EXPLAIN
 */
function esLectura(string $query): bool
{
    $q = strtoupper(ltrim($query));
    return preg_match('/^(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\b/', $q) === 1;
}

// ── Autenticación ─────────────────────────────────────────────────────────────
$token = $_GET['token'] ?? '';
if ($token !== COPILOT_TOKEN) {
    $registro->registrarActividad('system', 'db_copilot', 'auth', 'Token inválido o ausente', 'warning');
    respError('Token inválido o ausente.', 401);
}

// ── Operación solicitada ──────────────────────────────────────────────────────
$op = $_GET['op'] ?? '';
if (empty($op)) {
    respError('Parámetro "op" requerido. Opciones: tablas, vistas, estructura, datos, sql');
}

// ── Conexión a BD ─────────────────────────────────────────────────────────────
try {
    $pdo = (new Conexion())->getConexion();
    $pdo->exec("SET time_zone = 'Europe/Madrid'");
} catch (Exception $e) {
    $registro->registrarActividad('system', 'db_copilot', 'conexion', $e->getMessage(), 'error');
    respError('No se pudo conectar a la base de datos.', 500);
}

// ── Switch de operaciones ─────────────────────────────────────────────────────
switch ($op) {

    // ── Listar todas las tablas ───────────────────────────────────────────────
    case 'tablas':
        try {
            $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            $filas = $stmt->fetchAll(PDO::FETCH_NUM);
            $tablas = array_map(fn($f) => $f[0], $filas);
            $registro->registrarActividad('copilot', 'db_copilot', 'tablas', 'Listado de tablas solicitado', 'info');
            respOk(['total' => count($tablas), 'tablas' => $tablas]);
        } catch (PDOException $e) {
            respError('Error al obtener tablas: ' . $e->getMessage(), 500);
        }
        break;

    // ── Listar todas las vistas ───────────────────────────────────────────────
    case 'vistas':
        try {
            $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            $filas = $stmt->fetchAll(PDO::FETCH_NUM);
            $vistas = array_map(fn($f) => $f[0], $filas);
            $registro->registrarActividad('copilot', 'db_copilot', 'vistas', 'Listado de vistas solicitado', 'info');
            respOk(['total' => count($vistas), 'vistas' => $vistas]);
        } catch (PDOException $e) {
            respError('Error al obtener vistas: ' . $e->getMessage(), 500);
        }
        break;

    // ── Estructura de una tabla (DESCRIBE) ────────────────────────────────────
    case 'estructura':
        $tabla = $_GET['tabla'] ?? '';
        if (empty($tabla)) {
            respError('Parámetro "tabla" requerido para op=estructura');
        }

        // Validar que la tabla existe
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES 
                                   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
            $stmt->execute([$tabla]);
            if ((int) $stmt->fetchColumn() === 0) {
                respError("La tabla '$tabla' no existe en la base de datos.", 404);
            }
        } catch (PDOException $e) {
            respError('Error al verificar tabla: ' . $e->getMessage(), 500);
        }

        try {
            $stmt  = $pdo->query("DESCRIBE `$tabla`");
            $cols  = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener también los índices
            $stmtIdx = $pdo->query("SHOW INDEX FROM `$tabla`");
            $indices = $stmtIdx->fetchAll(PDO::FETCH_ASSOC);

            // Obtener FK
            $stmtFk = $pdo->prepare(
                "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME,
                        CONSTRAINT_NAME
                 FROM   information_schema.KEY_COLUMN_USAGE
                 WHERE  TABLE_SCHEMA = DATABASE()
                   AND  TABLE_NAME   = ?
                   AND  REFERENCED_TABLE_NAME IS NOT NULL"
            );
            $stmtFk->execute([$tabla]);
            $fks = $stmtFk->fetchAll(PDO::FETCH_ASSOC);

            $registro->registrarActividad('copilot', 'db_copilot', 'estructura', "Estructura de '$tabla' solicitada", 'info');
            respOk([
                'tabla'   => $tabla,
                'columnas' => $cols,
                'indices'  => $indices,
                'foreign_keys' => $fks,
            ]);
        } catch (PDOException $e) {
            respError('Error al obtener estructura: ' . $e->getMessage(), 500);
        }
        break;

    // ── Datos de una tabla ────────────────────────────────────────────────────
    case 'datos':
        $tabla = $_GET['tabla'] ?? '';
        $limit = min((int) ($_GET['limit'] ?? 20), 200); // Máximo 200 filas
        $where = $_GET['where'] ?? '';

        if (empty($tabla)) {
            respError('Parámetro "tabla" requerido para op=datos');
        }

        // Validar que la tabla o vista existe
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES 
                                   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
            $stmt->execute([$tabla]);
            if ((int) $stmt->fetchColumn() === 0) {
                respError("La tabla/vista '$tabla' no existe.", 404);
            }
        } catch (PDOException $e) {
            respError('Error al verificar tabla: ' . $e->getMessage(), 500);
        }

        try {
            // El WHERE es opcional y también debe ser seguro
            $sql = "SELECT * FROM `$tabla`";
            if (!empty($where)) {
                if (!esLectura('SELECT ' . $where) && str_contains(strtoupper($where), 'DROP')) {
                    respError('Cláusula WHERE no permitida.');
                }
                $sql .= " WHERE $where";
            }
            $sql .= " LIMIT $limit";

            $stmt  = $pdo->query($sql);
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Contar total sin límite
            $stmtC = $pdo->query("SELECT COUNT(*) FROM `$tabla`" . (!empty($where) ? " WHERE $where" : ''));
            $total = (int) $stmtC->fetchColumn();

            $registro->registrarActividad('copilot', 'db_copilot', 'datos', "Datos de '$tabla' solicitados (limit=$limit)", 'info');
            respOk([
                'tabla'       => $tabla,
                'total_reales' => $total,
                'limit'       => $limit,
                'filas'       => $filas,
            ]);
        } catch (PDOException $e) {
            respError('Error al obtener datos: ' . $e->getMessage(), 500);
        }
        break;

    // ── Consulta SQL libre (solo lectura) ─────────────────────────────────────
    case 'sql':
        $query = trim($_GET['query'] ?? $_POST['query'] ?? '');

        if (empty($query)) {
            respError('Parámetro "query" requerido para op=sql');
        }

        if (!esLectura($query)) {
            $registro->registrarActividad('copilot', 'db_copilot', 'sql_bloqueado', "Consulta no permitida: $query", 'warning');
            respError('Solo se permiten consultas de lectura (SELECT, SHOW, DESCRIBE, EXPLAIN).');
        }

        try {
            $stmt  = $pdo->query($query);
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $registro->registrarActividad('copilot', 'db_copilot', 'sql', 'Consulta personalizada ejecutada', 'info');
            respOk([
                'filas_retornadas' => count($filas),
                'resultado'        => $filas,
            ]);
        } catch (PDOException $e) {
            respError('Error en la consulta SQL: ' . $e->getMessage(), 500);
        }
        break;

    // ── Información general de la BD ──────────────────────────────────────────
    case 'info':
        try {
            $stmtDb  = $pdo->query("SELECT DATABASE() AS bd, VERSION() AS version, @@time_zone AS tz");
            $info    = $stmtDb->fetch(PDO::FETCH_ASSOC);

            $stmtCnt = $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'BASE TABLE'");
            $info['total_tablas'] = (int) $stmtCnt->fetchColumn();

            $stmtVw  = $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_TYPE = 'VIEW'");
            $info['total_vistas'] = (int) $stmtVw->fetchColumn();

            respOk($info);
        } catch (PDOException $e) {
            respError('Error al obtener info: ' . $e->getMessage(), 500);
        }
        break;

    // ── Operación no reconocida ───────────────────────────────────────────────
    default:
        respError("Operación '$op' no reconocida. Opciones disponibles: tablas, vistas, estructura, datos, sql, info");
}
