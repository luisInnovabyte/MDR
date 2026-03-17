<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class SalidaAlmacen
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'SalidaAlmacen',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // ================================================================
    // PICKING — BÚSQUEDA Y GESTIÓN DE SALIDAS
    // ================================================================

    /**
     * Busca un presupuesto por su número.
     * Devuelve cabecera + versión activa + necesidades de artículos.
     */
    public function buscar_presupuesto_por_numero($numero)
    {
        try {
            // QUERY 1: cabecera del presupuesto + cliente (sin join a versiones)
            $sql1 = "SELECT 
                        p.id_presupuesto,
                        p.numero_presupuesto,
                        p.estado_general_presupuesto,
                        p.version_actual_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.fecha_inicio_evento_presupuesto,
                        p.fecha_fin_evento_presupuesto,
                        p.id_cliente,
                        COALESCE(c.nombre_cliente, '') AS nombre_cliente
                    FROM presupuesto p
                    LEFT JOIN cliente c ON p.id_cliente = c.id_cliente
                    WHERE p.numero_presupuesto = ?
                      AND p.activo_presupuesto = 1
                    LIMIT 1";
            $stmt1 = $this->conexion->prepare($sql1);
            $stmt1->bindValue(1, trim($numero), PDO::PARAM_STR);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return false;
            }

            if (empty($row['nombre_cliente'])) {
                $row['nombre_cliente'] = 'Cliente (id=' . ($row['id_cliente'] ?? '?') . ')';
            }

            // QUERY 2: obtener la versión activa más reciente
            $sql2 = "SELECT id_version_presupuesto
                     FROM presupuesto_version
                     WHERE id_presupuesto = ?
                       AND activo_version = 1
                     ORDER BY numero_version_presupuesto DESC
                     LIMIT 1";
            $stmt2 = $this->conexion->prepare($sql2);
            $stmt2->bindValue(1, $row['id_presupuesto'], PDO::PARAM_INT);
            $stmt2->execute();
            $version = $stmt2->fetch(PDO::FETCH_ASSOC);

            $row['id_version_presupuesto'] = $version ? $version['id_version_presupuesto'] : null;

            return $row;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'buscar_presupuesto_por_numero',
                "Error SQL: " . $e->getMessage() . " | numero buscado: " . $numero,
                'error'
            );
            return false;
        }
    }

    /**
     * Diagnóstico RAW: devuelve datos brutos del presupuesto y sus versiones.
     * Solo para depuración temporal.
     */
    public function debug_presupuesto($numero)
    {
        try {
            $out = [];
            // Cabecera
            $stmt = $this->conexion->prepare(
                "SELECT id_presupuesto, numero_presupuesto, activo_presupuesto,
                        estado_general_presupuesto, version_actual_presupuesto, id_cliente
                 FROM presupuesto WHERE numero_presupuesto = ? LIMIT 1"
            );
            $stmt->bindValue(1, trim($numero), PDO::PARAM_STR);
            $stmt->execute();
            $out['presupuesto'] = $stmt->fetch(PDO::FETCH_ASSOC);
            // Versiones
            if ($out['presupuesto']) {
                $stmt2 = $this->conexion->prepare(
                    "SELECT id_version_presupuesto, numero_version_presupuesto,
                            estado_version_presupuesto, activo_version
                     FROM presupuesto_version
                     WHERE id_presupuesto = ?
                     ORDER BY numero_version_presupuesto"
                );
                $stmt2->bindValue(1, $out['presupuesto']['id_presupuesto'], PDO::PARAM_INT);
                $stmt2->execute();
                $out['versiones'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                // Cliente
                $stmt3 = $this->conexion->prepare(
                    "SELECT id_cliente, nombre_cliente, activo_cliente
                     FROM cliente WHERE id_cliente = ? LIMIT 1"
                );
                $stmt3->bindValue(1, $out['presupuesto']['id_cliente'], PDO::PARAM_INT);
                $stmt3->execute();
                $out['cliente'] = $stmt3->fetch(PDO::FETCH_ASSOC);
            }
            return $out;
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Consulta diagnóstico: devuelve estado básico del presupuesto sin JOIN de versión.
     * Usado para dar mensajes de error específicos cuando buscar_presupuesto_por_numero falla.
     */
    public function get_presupuesto_diagnostico($numero)
    {
        try {
            // 1. Comprobar si el presupuesto existe (con o sin activo)
            $sql = "SELECT p.id_presupuesto, p.numero_presupuesto, p.activo_presupuesto,
                           p.estado_general_presupuesto, p.version_actual_presupuesto,
                           p.id_cliente,
                           (SELECT pv.activo_version FROM presupuesto_version pv
                            WHERE pv.id_presupuesto = p.id_presupuesto
                            AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                            LIMIT 1) AS version_activa,
                           (SELECT COUNT(*) FROM cliente c WHERE c.id_cliente = p.id_cliente) AS cliente_existe
                    FROM presupuesto p
                    WHERE p.numero_presupuesto = ?
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, trim($numero), PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Devuelve las líneas de tipo articulo/kit de una versión de presupuesto
     * con la cantidad requerida y la ubicación planificada.
     */
    public function get_necesidades_presupuesto($id_version)
    {
        try {
            // Agrupamos por id_articulo para que un mismo artículo que aparezca
            // en varias líneas del presupuesto (distintas fechas, etc.) se muestre
            // una sola vez con la cantidad total. Así el contador es consistente
            // con get_progreso_salida(), que también agrega por id_articulo.
            $sql = "SELECT
                        lp.id_articulo,
                        SUM(lp.cantidad_linea_ppto) AS cantidad_linea_ppto,
                        a.nombre_articulo,
                        a.codigo_articulo,
                        GROUP_CONCAT(
                            DISTINCT cu.nombre_ubicacion
                            ORDER BY cu.nombre_ubicacion
                            SEPARATOR ', '
                        ) AS nombre_ubicacion
                    FROM linea_presupuesto lp
                    INNER JOIN articulo a ON lp.id_articulo = a.id_articulo
                    LEFT JOIN cliente_ubicacion cu ON lp.id_ubicacion = cu.id_ubicacion
                    WHERE lp.id_version_presupuesto = ?
                    AND lp.tipo_linea_ppto IN ('articulo','kit')
                    AND lp.activo_linea_ppto = 1
                    AND lp.id_articulo IS NOT NULL
                    AND a.mostrar_parte_trabajo_articulo = 1
                    GROUP BY lp.id_articulo, a.nombre_articulo, a.codigo_articulo
                    ORDER BY MIN(lp.orden_linea_ppto) ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_necesidades_presupuesto',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Busca si existe una salida en estado 'en_proceso' para un presupuesto dado.
     * Evita crear salidas duplicadas.
     */
    public function get_salida_activa($id_presupuesto)
    {
        try {
            $sql = "SELECT * FROM salida_almacen 
                    WHERE id_presupuesto = ? 
                    AND estado_salida = 'en_proceso'
                    AND activo_salida_almacen = 1
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_salida_activa',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Crea una nueva salida de almacén.
     * Devuelve el id_salida_almacen creado.
     */
    public function iniciar_salida($id_presupuesto, $id_version, $id_usuario, $numero_presupuesto)
    {
        try {
            $sql = "INSERT INTO salida_almacen 
                        (id_presupuesto, id_version_presupuesto, id_usuario_salida, 
                         numero_presupuesto_salida, estado_salida, fecha_inicio_salida)
                    VALUES (?, ?, ?, ?, 'en_proceso', NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_version, PDO::PARAM_INT);
            $stmt->bindValue(3, $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(4, $numero_presupuesto, PDO::PARAM_STR);
            $stmt->execute();
            $id = (int)$this->conexion->lastInsertId();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'iniciar_salida',
                "Salida iniciada ID: $id para presupuesto $numero_presupuesto",
                'info'
            );
            return $id;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'iniciar_salida',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ================================================================
    // PICKING — ESCANEO DE ELEMENTOS (método central)
    // ================================================================

    /**
     * Procesa el escaneo de un elemento por su codigo_elemento.
     *
     * Retorna array con:
     *   tipo => correcto | backup | ya_asignado | articulo_no_pertenece |
     *           no_disponible | elemento_no_encontrado
     *   elemento  => datos del elemento
     *   ubicacion_actual => si ya_asignado, la ubicación actual
     *   progreso  => estado actualizado del picking
     */
    public function escanear_elemento($id_salida, $codigo_elemento, $es_backup = false)
    {
        try {
            // 1. Buscar el elemento
            $sqlElem = "SELECT e.id_elemento, e.codigo_elemento, e.descripcion_elemento,
                               e.id_articulo_elemento AS id_articulo,
                               a.nombre_articulo, a.codigo_articulo,
                               ee.codigo_estado_elemento, ee.descripcion_estado_elemento,
                               ee.permite_alquiler_estado_elemento
                        FROM elemento e
                        INNER JOIN articulo a ON e.id_articulo_elemento = a.id_articulo
                        INNER JOIN estado_elemento ee ON e.id_estado_elemento = ee.id_estado_elemento
                        WHERE e.codigo_elemento = ? AND e.activo_elemento = 1";
            $stmtE = $this->conexion->prepare($sqlElem);
            $stmtE->bindValue(1, trim($codigo_elemento), PDO::PARAM_STR);
            $stmtE->execute();
            $elemento = $stmtE->fetch(PDO::FETCH_ASSOC);

            if (!$elemento) {
                return [
                    'success' => false,
                    'tipo' => 'elemento_no_encontrado',
                    'mensaje' => "Elemento '$codigo_elemento' no encontrado en el sistema."
                ];
            }

            // 2. ¿Ya está asignado en esta salida?
            $lineaExistente = $this->get_linea_salida_por_elemento($id_salida, $elemento['id_elemento']);
            if ($lineaExistente) {
                $ubicacionActual = $this->get_ubicacion_actual($lineaExistente['id_linea_salida']);
                return [
                    'success' => true,
                    'tipo' => 'ya_asignado',
                    'mensaje' => "Este elemento ya está asignado a esta salida.",
                    'elemento' => $elemento,
                    'linea_salida' => $lineaExistente,
                    'ubicacion_actual' => $ubicacionActual
                ];
            }

            // 3. Verificar estado disponible
            // 3a. Elemento alquilado → permitir reubicación sin crear nueva línea de salida
            if ($elemento['codigo_estado_elemento'] === 'ALQU') {
                $lineaAlqu = $this->get_linea_salida_por_elemento_presupuesto($id_salida, $elemento['id_elemento']);
                $ubicacionActual = $lineaAlqu ? $this->get_ubicacion_actual($lineaAlqu['id_linea_salida']) : null;
                return [
                    'success' => true,
                    'tipo' => 'ya_alquilado',
                    'mensaje' => "Este elemento ya está alquilado.",
                    'elemento' => $elemento,
                    'linea_salida' => $lineaAlqu ?: null,
                    'ubicacion_actual' => $ubicacionActual
                ];
            }
            $estadosValidos = ['DISP', 'TERC'];
            if (!in_array($elemento['codigo_estado_elemento'], $estadosValidos)) {
                return [
                    'success' => false,
                    'tipo' => 'no_disponible',
                    'mensaje' => "El elemento está en estado '{$elemento['descripcion_estado_elemento']}' y no puede ser preparado.",
                    'elemento' => $elemento
                ];
            }

            // 4. Verificar que el artículo pertenece al presupuesto
            $sqlNecesidad = "SELECT lp.id_linea_ppto, lp.id_ubicacion, cu.nombre_ubicacion,
                                    lp.cantidad_linea_ppto
                             FROM salida_almacen sa
                             INNER JOIN linea_presupuesto lp 
                                 ON lp.id_version_presupuesto = sa.id_version_presupuesto
                                 AND lp.id_articulo = ?
                                 AND lp.tipo_linea_ppto IN ('articulo','kit')
                                 AND lp.activo_linea_ppto = 1
                             LEFT JOIN cliente_ubicacion cu ON lp.id_ubicacion = cu.id_ubicacion
                             WHERE sa.id_salida_almacen = ?
                             LIMIT 1";
            $stmtN = $this->conexion->prepare($sqlNecesidad);
            $stmtN->bindValue(1, $elemento['id_articulo'], PDO::PARAM_INT);
            $stmtN->bindValue(2, $id_salida, PDO::PARAM_INT);
            $stmtN->execute();
            $necesidad = $stmtN->fetch(PDO::FETCH_ASSOC);

            if (!$necesidad) {
                // Si ya viene marcado como backup, permitir igualmente el registro
                // con id_linea_ppto = NULL (material de repuesto fuera de presupuesto)
                if (!$es_backup) {
                    return [
                        'success' => true,
                        'tipo' => 'articulo_no_pertenece_preguntar',
                        'mensaje' => "'{$elemento['nombre_articulo']}' no está en el presupuesto. ¿Es material de repuesto?",
                        'elemento' => $elemento
                    ];
                }
                // Backup fuera de presupuesto: $necesidad queda null, se usa más abajo
            }

            // 5. Si NO es backup, comprobar si quedan unidades por cubrir
            if (!$es_backup) {
                $sqlConteo = "SELECT COUNT(*) AS total FROM linea_salida_almacen
                              WHERE id_salida_almacen = ? 
                              AND id_articulo = ? 
                              AND es_backup_linea_salida = 0
                              AND activo_linea_salida = 1";
                $stmtC = $this->conexion->prepare($sqlConteo);
                $stmtC->bindValue(1, $id_salida, PDO::PARAM_INT);
                $stmtC->bindValue(2, $elemento['id_articulo'], PDO::PARAM_INT);
                $stmtC->execute();
                $conteo = $stmtC->fetch(PDO::FETCH_ASSOC);
                if ($conteo['total'] >= $necesidad['cantidad_linea_ppto']) {
                    // Artículo ya completo — indicar para preguntar si es backup
                    return [
                        'success' => true,
                        'tipo' => 'cantidad_completada',
                        'mensaje' => "Ya se han escaneado todas las unidades necesarias de '{$elemento['nombre_articulo']}'. ¿Añadir como backup?",
                        'elemento' => $elemento
                    ];
                }
            }

            // 6. Obtener orden de escaneo
            $sqlOrden = "SELECT COALESCE(MAX(orden_escaneo),0)+1 AS siguiente FROM linea_salida_almacen WHERE id_salida_almacen = ?";
            $stmtO = $this->conexion->prepare($sqlOrden);
            $stmtO->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtO->execute();
            $orden = $stmtO->fetch(PDO::FETCH_ASSOC)['siguiente'];

            // 7. INSERT en linea_salida_almacen
            $sqlIns = "INSERT INTO linea_salida_almacen
                           (id_salida_almacen, id_elemento, id_articulo, id_linea_ppto,
                            es_backup_linea_salida, orden_escaneo, fecha_escaneo_linea_salida)
                       VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmtI = $this->conexion->prepare($sqlIns);
            $stmtI->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtI->bindValue(2, $elemento['id_elemento'], PDO::PARAM_INT);
            $stmtI->bindValue(3, $elemento['id_articulo'], PDO::PARAM_INT);
            $stmtI->bindValue(
                4,
                $necesidad['id_linea_ppto'] ?? null,
                !empty($necesidad['id_linea_ppto']) ? PDO::PARAM_INT : PDO::PARAM_NULL
            );
            $stmtI->bindValue(5, $es_backup ? 1 : 0, PDO::PARAM_INT);
            $stmtI->bindValue(6, $orden, PDO::PARAM_INT);
            $stmtI->execute();
            $id_linea_salida = (int)$this->conexion->lastInsertId();

            // 8. Registrar primer movimiento automático (si hay ubicación en la línea del presupuesto)
            if (!empty($necesidad['id_ubicacion'] ?? null)) {
                $this->registrar_movimiento(
                    $id_linea_salida,
                    null,
                    $necesidad['id_ubicacion'],
                    $this->get_usuario_salida($id_salida),
                    null
                );
            }

            // 9. Cambiar estado del elemento a PREP
            $this->cambiar_estado_elemento($elemento['id_elemento'], 'PREP');

            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'escanear_elemento',
                "Elemento {$codigo_elemento} escaneado en salida $id_salida" . ($es_backup ? " (BACKUP)" : ""),
                'info'
            );

            $tipo = $es_backup ? 'backup' : 'correcto';
            return [
                'success' => true,
                'tipo' => $tipo,
                'mensaje' => $es_backup
                    ? "Elemento añadido como backup."
                    : "Elemento escaneado correctamente.",
                'elemento' => $elemento,
                'id_linea_salida' => $id_linea_salida,
                'progreso' => $this->get_progreso_salida($id_salida)
            ];
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'escanear_elemento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return ['success' => false, 'tipo' => 'error', 'mensaje' => "Error interno al procesar el escaneo."];
        }
    }

    // ================================================================
    // PICKING — PROGRESO Y LISTADOS
    // ================================================================

    /**
     * Devuelve el progreso del picking agrupado por artículo.
     */
    public function get_progreso_salida($id_salida)
    {
        try {
            // Consulta directa en lugar de la vista para evitar el bug de doble conteo:
            // la vista hace JOIN lp × lsa por id_articulo, multiplicando cantidad_escaneada
            // cuando un artículo aparece en varias líneas del presupuesto.
            $sql = "SELECT
                        lp_g.id_salida_almacen,
                        lp_g.id_articulo,
                        lp_g.nombre_articulo,
                        lp_g.codigo_articulo,
                        lp_g.cantidad_requerida,
                        COALESCE(lsa_g.cantidad_escaneada, 0) AS cantidad_escaneada,
                        COALESCE(lsa_g.cantidad_backup, 0)    AS cantidad_backup,
                        CASE WHEN COALESCE(lsa_g.cantidad_escaneada, 0) >= lp_g.cantidad_requerida
                             THEN 1 ELSE 0 END AS esta_completo
                    FROM (
                        SELECT sa.id_salida_almacen,
                               lp.id_articulo,
                               a.nombre_articulo,
                               a.codigo_articulo,
                               SUM(lp.cantidad_linea_ppto) AS cantidad_requerida
                        FROM salida_almacen sa
                        JOIN presupuesto_version pv ON sa.id_version_presupuesto = pv.id_version_presupuesto
                        JOIN linea_presupuesto lp
                            ON  lp.id_version_presupuesto = pv.id_version_presupuesto
                            AND lp.tipo_linea_ppto IN ('articulo','kit')
                            AND lp.activo_linea_ppto = 1
                        JOIN articulo a ON lp.id_articulo = a.id_articulo
                        WHERE sa.id_salida_almacen = ? AND sa.activo_salida_almacen = 1
                        GROUP BY sa.id_salida_almacen, lp.id_articulo, a.nombre_articulo,
                                 a.codigo_articulo
                    ) lp_g
                    LEFT JOIN (
                        SELECT lsa.id_articulo,
                               SUM(CASE WHEN lsa.es_backup_linea_salida = 0 AND lsa.activo_linea_salida = 1 THEN 1 ELSE 0 END) AS cantidad_escaneada,
                               SUM(CASE WHEN lsa.es_backup_linea_salida = 1 AND lsa.activo_linea_salida = 1 THEN 1 ELSE 0 END) AS cantidad_backup
                        FROM linea_salida_almacen lsa
                        INNER JOIN salida_almacen sa2 ON lsa.id_salida_almacen = sa2.id_salida_almacen
                        WHERE sa2.id_presupuesto = (
                            SELECT id_presupuesto FROM salida_almacen WHERE id_salida_almacen = ?
                        )
                          AND sa2.activo_salida_almacen = 1
                        GROUP BY lsa.id_articulo
                    ) lsa_g ON lp_g.id_articulo = lsa_g.id_articulo
                    ORDER BY lp_g.nombre_articulo ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_salida, PDO::PARAM_INT);
            $stmt->execute();
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalRequerido = array_sum(array_column($filas, 'cantidad_requerida'));
            $totalEscaneado = array_sum(array_column($filas, 'cantidad_escaneada'));
            $completo = $totalEscaneado >= $totalRequerido && $totalRequerido > 0;

            return [
                'por_articulo' => $filas,
                'total_requerido' => (int)$totalRequerido,
                'total_escaneado' => (int)$totalEscaneado,
                'total_backup' => (int)array_sum(array_column($filas, 'cantidad_backup')),
                'completo' => $completo
            ];
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_progreso_salida',
                "Error: " . $e->getMessage(),
                'error'
            );
            return ['por_articulo' => [], 'total_requerido' => 0, 'total_escaneado' => 0, 'completo' => false];
        }
    }

    /**
     * Lista todos los elementos escaneados en una salida con su ubicación actual.
     */
    public function get_elementos_escaneados($id_salida)
    {
        try {
            $sql = "SELECT v.*, e.numero_serie_elemento
                    FROM vista_ubicacion_actual_elemento v
                    JOIN elemento e ON v.id_elemento = e.id_elemento
                    WHERE v.id_salida_almacen = ?
                    ORDER BY v.nombre_articulo ASC, v.codigo_elemento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_elementos_escaneados',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve la cabecera de una salida por ID.
     */
    public function get_salidaxid($id_salida)
    {
        try {
            $sql = "SELECT sa.*, u.nombre AS nombre_usuario
                    FROM salida_almacen sa
                    LEFT JOIN usuarios u ON sa.id_usuario_salida = u.id_usuario
                    WHERE sa.id_salida_almacen = ? AND sa.activo_salida_almacen = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_salidaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Devuelve el historial de salidas de un presupuesto.
     */
    public function get_salidas_por_presupuesto($id_presupuesto)
    {
        try {
            $sql = "SELECT sa.*, u.nombre AS nombre_usuario
                    FROM salida_almacen sa
                    LEFT JOIN usuarios u ON sa.id_usuario_salida = u.id_usuario
                    WHERE sa.id_presupuesto = ? AND sa.activo_salida_almacen = 1
                    ORDER BY sa.created_at_salida_almacen DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_salidas_por_presupuesto',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // ================================================================
    // PICKING — COMPLETAR / CANCELAR
    // ================================================================

    /**
     * Completa la salida: cambia estado a completada y los elementos a ALQU.
     */
    public function completar_salida($id_salida)
    {
        try {
            $this->conexion->beginTransaction();

            // Cambiar todos los elementos PREP → ALQU
            $sqlElems = "SELECT id_elemento FROM linea_salida_almacen 
                         WHERE id_salida_almacen = ? AND activo_linea_salida = 1";
            $stmtElems = $this->conexion->prepare($sqlElems);
            $stmtElems->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtElems->execute();
            $elementos = $stmtElems->fetchAll(PDO::FETCH_ASSOC);

            foreach ($elementos as $elem) {
                $this->cambiar_estado_elemento($elem['id_elemento'], 'ALQU');
            }

            // Actualizar estado y fecha fin de la salida
            $sqlUpd = "UPDATE salida_almacen SET 
                           estado_salida = 'completada',
                           fecha_fin_salida = NOW(),
                           updated_at_salida_almacen = NOW()
                       WHERE id_salida_almacen = ?";
            $stmtUpd = $this->conexion->prepare($sqlUpd);
            $stmtUpd->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtUpd->execute();

            $this->conexion->commit();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'completar_salida',
                "Salida $id_salida completada. " . count($elementos) . " elementos → ALQU",
                'info'
            );
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollback();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'completar_salida',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Cancela la salida: revierte estados de elementos a DISP y elimina líneas y movimientos.
     */
    public function cancelar_salida($id_salida)
    {
        try {
            $this->conexion->beginTransaction();

            // Obtener elementos para revertir estado
            $sqlElems = "SELECT id_elemento FROM linea_salida_almacen 
                         WHERE id_salida_almacen = ? AND activo_linea_salida = 1";
            $stmtElems = $this->conexion->prepare($sqlElems);
            $stmtElems->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtElems->execute();
            $elementos = $stmtElems->fetchAll(PDO::FETCH_ASSOC);

            // Soft delete movimientos
            $sqlDelMov = "UPDATE movimiento_elemento_salida mes
                          INNER JOIN linea_salida_almacen lsa ON mes.id_linea_salida = lsa.id_linea_salida
                          SET mes.activo_movimiento = 0, mes.updated_at_movimiento = NOW()
                          WHERE lsa.id_salida_almacen = ?";
            $stmtDM = $this->conexion->prepare($sqlDelMov);
            $stmtDM->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtDM->execute();

            // Soft delete líneas
            $sqlDelLin = "UPDATE linea_salida_almacen SET activo_linea_salida = 0, updated_at_linea_salida = NOW()
                          WHERE id_salida_almacen = ?";
            $stmtDL = $this->conexion->prepare($sqlDelLin);
            $stmtDL->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtDL->execute();

            // Revertir elementos a DISP
            foreach ($elementos as $elem) {
                $this->cambiar_estado_elemento($elem['id_elemento'], 'DISP');
            }

            // Marcar salida como cancelada
            $sqlUpd = "UPDATE salida_almacen SET 
                           estado_salida = 'cancelada',
                           updated_at_salida_almacen = NOW()
                       WHERE id_salida_almacen = ?";
            $stmtUpd = $this->conexion->prepare($sqlUpd);
            $stmtUpd->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmtUpd->execute();

            $this->conexion->commit();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'cancelar_salida',
                "Salida $id_salida cancelada. " . count($elementos) . " elementos → DISP",
                'info'
            );
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollback();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'cancelar_salida',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ================================================================
    // UBICACIONES Y MOVIMIENTOS
    // ================================================================

    /**
     * Devuelve la linea_salida_almacen de un elemento si ya está en esta salida.
     */
    public function get_linea_salida_por_elemento($id_salida, $id_elemento)
    {
        try {
            $sql = "SELECT * FROM linea_salida_almacen 
                    WHERE id_salida_almacen = ? AND id_elemento = ? AND activo_linea_salida = 1
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_linea_salida_por_elemento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Devuelve la ubicación actual de un elemento (último movimiento).
     */
    public function get_ubicacion_actual($id_linea_salida)
    {
        try {
            $sql = "SELECT mes.id_movimiento, mes.id_ubicacion_origen, mes.id_ubicacion_destino,
                           mes.fecha_movimiento, cu.nombre_ubicacion, cu.id_ubicacion,
                           u.nombre AS nombre_usuario
                    FROM movimiento_elemento_salida mes
                    LEFT JOIN cliente_ubicacion cu ON mes.id_ubicacion_destino = cu.id_ubicacion
                    LEFT JOIN usuarios u ON mes.id_usuario_movimiento = u.id_usuario
                    WHERE mes.id_linea_salida = ? AND mes.activo_movimiento = 1
                    ORDER BY mes.fecha_movimiento DESC
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_linea_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_ubicacion_actual',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Registra un movimiento físico de un elemento.
     * id_ubicacion_origen puede ser NULL (primera colocación desde almacén).
     */
    public function registrar_movimiento($id_linea_salida, $id_ubicacion_origen, $id_ubicacion_destino, $id_usuario, $observaciones = null)
    {
        try {
            $sql = "INSERT INTO movimiento_elemento_salida
                        (id_linea_salida, id_ubicacion_origen, id_ubicacion_destino,
                         id_usuario_movimiento, fecha_movimiento, observaciones_movimiento)
                    VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_linea_salida, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_ubicacion_origen, $id_ubicacion_origen ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(3, $id_ubicacion_destino, PDO::PARAM_INT);
            $stmt->bindValue(4, $id_usuario, PDO::PARAM_INT);
            $stmt->bindValue(5, $observaciones, $observaciones ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();
            $id = (int)$this->conexion->lastInsertId();
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'registrar_movimiento',
                "Movimiento registrado ID: $id en linea_salida $id_linea_salida → ubicacion $id_ubicacion_destino",
                'info'
            );
            return $id;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'registrar_movimiento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Devuelve el historial completo de movimientos de un elemento en una salida.
     */
    public function get_historial_movimientos($id_linea_salida)
    {
        try {
            $sql = "SELECT mes.*,
                           cu_orig.nombre_ubicacion AS nombre_origen,
                           cu_dest.nombre_ubicacion AS nombre_destino,
                           u.nombre AS nombre_usuario
                    FROM movimiento_elemento_salida mes
                    LEFT JOIN cliente_ubicacion cu_orig ON mes.id_ubicacion_origen = cu_orig.id_ubicacion
                    LEFT JOIN cliente_ubicacion cu_dest ON mes.id_ubicacion_destino = cu_dest.id_ubicacion
                    LEFT JOIN usuarios u ON mes.id_usuario_movimiento = u.id_usuario
                    WHERE mes.id_linea_salida = ? AND mes.activo_movimiento = 1
                    ORDER BY mes.fecha_movimiento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_linea_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_historial_movimientos',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Mapa de ubicaciones: todos los elementos de la salida con su ubicación actual.
     * Usado para el panel de control del evento.
     */
    public function get_mapa_ubicaciones($id_salida)
    {
        try {
            $sql = "SELECT * FROM vista_ubicacion_actual_elemento 
                    WHERE id_salida_almacen = ?
                    ORDER BY nombre_ubicacion_actual ASC, nombre_articulo ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_mapa_ubicaciones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve las ubicaciones disponibles del cliente asociado a una salida.
     */
    public function get_ubicaciones_del_presupuesto($id_salida)
    {
        try {
            $sql = "SELECT cu.id_ubicacion, cu.nombre_ubicacion, cu.direccion_ubicacion
                    FROM cliente_ubicacion cu
                    INNER JOIN presupuesto p ON p.id_cliente = cu.id_cliente
                    INNER JOIN salida_almacen sa ON sa.id_presupuesto = p.id_presupuesto
                    WHERE sa.id_salida_almacen = ? AND cu.activo_ubicacion = 1
                    ORDER BY cu.nombre_ubicacion ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_ubicaciones_del_presupuesto',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // ================================================================
    // HELPERS PRIVADOS
    // ================================================================

    private function cambiar_estado_elemento($id_elemento, $codigo_estado)
    {
        $sqlId = "SELECT id_estado_elemento FROM estado_elemento WHERE codigo_estado_elemento = ? LIMIT 1";
        $stmtId = $this->conexion->prepare($sqlId);
        $stmtId->bindValue(1, $codigo_estado, PDO::PARAM_STR);
        $stmtId->execute();
        $estado = $stmtId->fetch(PDO::FETCH_ASSOC);
        if (!$estado) return false;

        $sql = "UPDATE elemento SET id_estado_elemento = ?, updated_at_elemento = NOW() WHERE id_elemento = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $estado['id_estado_elemento'], PDO::PARAM_INT);
        $stmt->bindValue(2, $id_elemento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function get_linea_salida_por_elemento_presupuesto($id_salida_actual, $id_elemento)
    {
        try {
            $sql = "SELECT lsa.* FROM linea_salida_almacen lsa
                    INNER JOIN salida_almacen sa ON lsa.id_salida_almacen = sa.id_salida_almacen
                    WHERE lsa.id_elemento = ?
                      AND sa.id_presupuesto = (
                          SELECT id_presupuesto FROM salida_almacen WHERE id_salida_almacen = ?
                      )
                      AND lsa.activo_linea_salida = 1
                    ORDER BY lsa.fecha_escaneo_linea_salida DESC
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_salida_actual, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'SalidaAlmacen',
                'get_linea_salida_por_elemento_presupuesto',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    private function get_usuario_salida($id_salida)
    {
        $sql = "SELECT id_usuario_salida FROM salida_almacen WHERE id_salida_almacen = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_salida, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_usuario_salida'] : 1;
    }
}
