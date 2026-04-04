<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class FacturaAgrupada
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
                'FacturaAgrupada',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // =========================================================
    // CONSULTAS / LISTADOS
    // =========================================================

    /**
     * Obtiene todas las facturas agrupadas activas con datos completos de la vista.
     */
    public function get_facturas_agrupadas()
    {
        try {
            $sql = "SELECT v.*,
                           (SELECT GROUP_CONCAT(p.numero_presupuesto ORDER BY fap3.orden_fap SEPARATOR ', ')
                            FROM factura_agrupada_presupuesto fap3
                            JOIN presupuesto p ON fap3.id_presupuesto = p.id_presupuesto
                            WHERE fap3.id_factura_agrupada = v.id_factura_agrupada
                              AND fap3.activo_fap = 1) AS numeros_presupuestos_agrupada,
                           (SELECT fa2.id_factura_agrupada
                            FROM factura_agrupada fa2
                            WHERE fa2.id_factura_agrupada_ref = v.id_factura_agrupada
                              AND fa2.is_abono_agrupada = 1
                              AND fa2.activo_factura_agrupada = 1
                            LIMIT 1) AS id_abono_asociado,
                           (SELECT fa2.numero_factura_agrupada
                            FROM factura_agrupada fa2
                            WHERE fa2.id_factura_agrupada_ref = v.id_factura_agrupada
                              AND fa2.is_abono_agrupada = 1
                              AND fa2.activo_factura_agrupada = 1
                            LIMIT 1) AS numero_abono_asociado
                    FROM vista_factura_agrupada_completa v
                    WHERE v.activo_factura_agrupada = 1
                       OR (
                           v.activo_factura_agrupada = 0
                           AND v.is_abono_agrupada = 0
                           AND EXISTS (
                               SELECT 1 FROM factura_agrupada fa2
                               WHERE fa2.id_factura_agrupada_ref = v.id_factura_agrupada
                                 AND fa2.is_abono_agrupada = 1
                                 AND fa2.activo_factura_agrupada = 1
                           )
                       )
                    ORDER BY v.fecha_factura_agrupada DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_facturas_agrupadas',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Facturas agrupadas vinculadas a un presupuesto concreto.
     * Incluye FA activas, abonos y FA originales abonadas (activo=0 con abono asociado).
     */
    public function get_facturas_agrupadas_por_presupuesto($id_presupuesto)
    {
        try {
            $sql = "SELECT fa.id_factura_agrupada,
                           fa.numero_factura_agrupada,
                           fa.fecha_factura_agrupada,
                           fa.is_abono_agrupada,
                           fa.activo_factura_agrupada,
                           fa.id_factura_agrupada_ref,
                           COALESCE(e.nombre_empresa, '') AS nombre_empresa,
                           (SELECT fa2.id_factura_agrupada
                            FROM factura_agrupada fa2
                            WHERE fa2.id_factura_agrupada_ref = fa.id_factura_agrupada
                              AND fa2.is_abono_agrupada = 1
                              AND fa2.activo_factura_agrupada = 1
                            LIMIT 1) AS id_abono_asociado,
                           (SELECT fa2.numero_factura_agrupada
                            FROM factura_agrupada fa2
                            WHERE fa2.id_factura_agrupada_ref = fa.id_factura_agrupada
                              AND fa2.is_abono_agrupada = 1
                              AND fa2.activo_factura_agrupada = 1
                            LIMIT 1) AS numero_abono_asociado
                    FROM factura_agrupada fa
                    JOIN factura_agrupada_presupuesto fap
                         ON fap.id_factura_agrupada = fa.id_factura_agrupada
                    LEFT JOIN empresa e ON e.id_empresa = fa.id_empresa
                    WHERE fap.id_presupuesto = ?
                      AND fap.activo_fap = 1
                    ORDER BY fa.fecha_factura_agrupada DESC, fa.id_factura_agrupada DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_facturas_agrupadas_por_presupuesto',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Facturas agrupadas de un cliente concreto (activas).
     */
    public function get_facturas_agrupadas_by_cliente($id_cliente)
    {
        try {
            $sql = "SELECT * FROM vista_factura_agrupada_completa
                    WHERE id_cliente = ?
                      AND activo_factura_agrupada = 1
                    ORDER BY fecha_factura_agrupada DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_facturas_agrupadas_by_cliente',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Devuelve una factura agrupada por su ID.
     */
    public function get_factura_agrupadaxid($id_factura_agrupada)
    {
        try {
            $sql = "SELECT * FROM vista_factura_agrupada_completa
                    WHERE id_factura_agrupada = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_factura_agrupadaxid',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Líneas de relación presupuesto <-> factura agrupada con totales.
     */
    public function get_presupuestos_factura_agrupada($id_factura_agrupada)
    {
        try {
            $sql = "SELECT
                        fap.id_fap,
                        fap.id_presupuesto,
                        fap.total_base_fap,
                        fap.total_iva_fap,
                        fap.total_bruto_fap,
                        fap.total_anticipos_reales_fap,
                        fap.total_proformas_fap,
                        fap.resto_fap,
                        fap.orden_fap,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.fecha_presupuesto
                    FROM factura_agrupada_presupuesto fap
                    INNER JOIN presupuesto p ON p.id_presupuesto = fap.id_presupuesto
                    WHERE fap.id_factura_agrupada = ?
                      AND fap.activo_fap = 1
                    ORDER BY fap.orden_fap ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_presupuestos_factura_agrupada',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Presupuestos disponibles para agrupar de un cliente:
     *  - Activos
     *  - Sin factura_final activa en documento_presupuesto
     *  - No incluidos en otra factura agrupada activa
     *  - No cancelados (estado activo)
     * Enriquece con totales de v_presupuesto_totales y anticipos reales.
     */
    public function get_presupuestos_disponibles($id_cliente)
    {
        try {
            $sql = "SELECT
                        p.id_presupuesto,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.fecha_presupuesto,
                        p.id_empresa,
                        e.nombre_empresa,
                        vt.total_base_imponible,
                        vt.total_iva,
                        vt.total_con_iva,
                        COALESCE(ant.total_anticipos, 0)                              AS total_anticipos_reales,
                        COALESCE(prf.total_proformas, 0)                              AS total_proformas,
                        (vt.total_con_iva - COALESCE(ant.total_anticipos, 0))         AS resto_pendiente
                    FROM presupuesto p
                    LEFT JOIN empresa e ON e.id_empresa = p.id_empresa
                    LEFT JOIN v_presupuesto_totales vt ON vt.id_presupuesto = p.id_presupuesto
                                                      AND vt.estado_version_presupuesto = 'aprobado'
                    -- Suma de anticipos reales activos
                    LEFT JOIN (
                        SELECT dp.id_presupuesto,
                               SUM(dp.total_documento_ppto) AS total_anticipos
                        FROM documento_presupuesto dp
                        WHERE dp.tipo_documento_ppto = 'factura_anticipo'
                          AND dp.activo_documento_ppto = 1
                        GROUP BY dp.id_presupuesto
                    ) ant ON ant.id_presupuesto = p.id_presupuesto
                    -- Suma de proformas activas (para mostrar nota de anticipo en wizard)
                    LEFT JOIN (
                        SELECT dp.id_presupuesto,
                               SUM(dp.total_documento_ppto) AS total_proformas
                        FROM documento_presupuesto dp
                        WHERE dp.tipo_documento_ppto = 'factura_proforma'
                          AND dp.activo_documento_ppto = 1
                        GROUP BY dp.id_presupuesto
                    ) prf ON prf.id_presupuesto = p.id_presupuesto
                    WHERE p.id_cliente = ?
                      AND p.activo_presupuesto = 1
                      -- Condición: no facturado al 100% (ni por factura_final ni por anticipos reales)
                      AND NOT EXISTS (
                          SELECT 1 FROM documento_presupuesto dp2
                          WHERE dp2.id_presupuesto = p.id_presupuesto
                            AND dp2.tipo_documento_ppto = 'factura_final'
                            AND dp2.activo_documento_ppto = 1
                      )
                      AND vt.total_con_iva > COALESCE(ant.total_anticipos, 0)
                      -- Condición: no incluido en ninguna factura agrupada activa no abonada
                      AND NOT EXISTS (
                          SELECT 1 FROM factura_agrupada_presupuesto fap
                          INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
                          WHERE fap.id_presupuesto = p.id_presupuesto
                            AND fap.activo_fap = 1
                            AND fa.activo_factura_agrupada = 1
                            AND fa.is_abono_agrupada = 0
                      )
                    ORDER BY p.fecha_presupuesto DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_presupuestos_disponibles',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    // =========================================================
    // VALIDACIONES
    // =========================================================

    /**
     * Valida que un array de IDs de presupuesto puede formar una factura agrupada.
     * Comprueba: mismo cliente, misma empresa, mínimo 2, no ya agrupados, no factura_final activa.
     * Si $id_empresa_real > 0, verifica que ningún presupuesto tenga factura activa con empresa distinta.
     * Devuelve ['valido' => bool, 'errores' => [], 'advertencias' => [], 'id_cliente' => int, 'id_empresa' => int]
     */
    public function validar_presupuestos(array $ids_presupuesto, int $id_empresa_real = 0)
    {
        $errores      = [];
        $advertencias = [];
        $id_cliente   = null;
        $id_empresa   = null;

        if (count($ids_presupuesto) < 2) {
            return [
                'valido'       => false,
                'errores'      => ['Debe seleccionar al menos 2 presupuestos.'],
                'advertencias' => [],
                'id_cliente'   => null,
                'id_empresa'   => null,
            ];
        }

        // Sanitizar IDs
        $ids_int = array_map('intval', $ids_presupuesto);
        $placeholders = implode(',', array_fill(0, count($ids_int), '?'));

        try {
            // Obtener datos de los presupuestos seleccionados
            $sql = "SELECT p.id_presupuesto, p.numero_presupuesto, p.id_cliente, p.id_empresa,
                           p.activo_presupuesto
                    FROM presupuesto p
                    WHERE p.id_presupuesto IN ($placeholders)";

            $stmt = $this->conexion->prepare($sql);
            foreach ($ids_int as $k => $id) {
                $stmt->bindValue($k + 1, $id, PDO::PARAM_INT);
            }
            $stmt->execute();
            $presupuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($presupuestos) !== count($ids_int)) {
                $errores[] = 'Uno o más presupuestos no existen en la base de datos.';
                return ['valido' => false, 'errores' => $errores, 'advertencias' => $advertencias, 'id_cliente' => null, 'id_empresa' => null];
            }

            // Verificar activos
            foreach ($presupuestos as $p) {
                if (!$p['activo_presupuesto']) {
                    $errores[] = "El presupuesto {$p['numero_presupuesto']} está inactivo.";
                }
            }

            // Verificar mismo cliente
            $clientes  = array_unique(array_column($presupuestos, 'id_cliente'));
            $empresas  = array_unique(array_column($presupuestos, 'id_empresa'));

            if (count($clientes) > 1) {
                $errores[] = 'Todos los presupuestos deben pertenecer al mismo cliente.';
            } else {
                $id_cliente = (int) $clientes[0];
            }

            // Filtrar nulls: un presupuesto sin empresa asignada es compatible con cualquier empresa.
            // El conflicto real se detecta más abajo comparando la empresa de los documentos existentes.
            $empresas_asignadas = array_values(array_filter($empresas, fn($e) => $e !== null));
            if (count(array_unique($empresas_asignadas)) > 1) {
                $errores[] = 'Todos los presupuestos deben pertenecer a la misma empresa emisora.';
            } else {
                $id_empresa = !empty($empresas_asignadas) ? (int) $empresas_asignadas[0] : null;
            }

            // Verificar que no tienen factura_final activa
            $sql_ff = "SELECT dp.id_presupuesto, p.numero_presupuesto
                       FROM documento_presupuesto dp
                       INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
                       WHERE dp.id_presupuesto IN ($placeholders)
                         AND dp.tipo_documento_ppto = 'factura_final'
                         AND dp.activo_documento_ppto = 1";

            $stmt_ff = $this->conexion->prepare($sql_ff);
            foreach ($ids_int as $k => $id) {
                $stmt_ff->bindValue($k + 1, $id, PDO::PARAM_INT);
            }
            $stmt_ff->execute();
            $con_ff = $stmt_ff->fetchAll(PDO::FETCH_ASSOC);
            foreach ($con_ff as $row) {
                $errores[] = "El presupuesto {$row['numero_presupuesto']} ya tiene una factura final activa.";
            }

            // Verificar que no están ya en otra factura agrupada activa
            $sql_fa = "SELECT fap.id_presupuesto, p.numero_presupuesto
                       FROM factura_agrupada_presupuesto fap
                       INNER JOIN presupuesto p ON p.id_presupuesto = fap.id_presupuesto
                       INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
                       WHERE fap.id_presupuesto IN ($placeholders)
                         AND fap.activo_fap = 1
                         AND fa.activo_factura_agrupada = 1
                         AND fa.is_abono_agrupada = 0";

            $stmt_fa = $this->conexion->prepare($sql_fa);
            foreach ($ids_int as $k => $id) {
                $stmt_fa->bindValue($k + 1, $id, PDO::PARAM_INT);
            }
            $stmt_fa->execute();
            $ya_agrupados = $stmt_fa->fetchAll(PDO::FETCH_ASSOC);
            foreach ($ya_agrupados as $row) {
                $errores[] = "El presupuesto {$row['numero_presupuesto']} ya pertenece a otra factura agrupada activa.";
            }

            // Verificar coherencia de empresa real: si ya hay facturas en algún presupuesto,
            // deben pertenecer a la misma empresa real que se ha seleccionado
            if ($id_empresa_real > 0) {
                $sql_emp = "SELECT DISTINCT dp.id_empresa, p.numero_presupuesto
                            FROM documento_presupuesto dp
                            INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
                            WHERE dp.id_presupuesto IN ($placeholders)
                              AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
                              AND dp.activo_documento_ppto = 1
                              AND dp.id_empresa != ?";

                $stmt_emp = $this->conexion->prepare($sql_emp);
                foreach ($ids_int as $k => $id) {
                    $stmt_emp->bindValue($k + 1, $id, PDO::PARAM_INT);
                }
                $stmt_emp->bindValue(count($ids_int) + 1, $id_empresa_real, PDO::PARAM_INT);
                $stmt_emp->execute();
                $conflictos_empresa = $stmt_emp->fetchAll(PDO::FETCH_ASSOC);
                foreach ($conflictos_empresa as $row) {
                    $errores[] = "El presupuesto {$row['numero_presupuesto']} ya tiene facturas emitidas con una empresa distinta a la seleccionada.";
                }
            }

            return [
                'valido'            => empty($errores),
                'errores'           => $errores,
                'advertencias'      => $advertencias,
                'id_cliente'        => $id_cliente,
                'id_empresa'        => $id_empresa,
                'empresa_requerida' => ($id_empresa === null && empty($errores)),
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'validar_presupuestos',
                "Error: " . $e->getMessage(), 'error'
            );
            return [
                'valido'       => false,
                'errores'      => ['Error interno al validar presupuestos.'],
                'advertencias' => [],
                'id_cliente'   => null,
                'id_empresa'   => null,
            ];
        }
    }

    // =========================================================
    // CREACIÓN — TRANSACCIÓN COMPLETA
    // =========================================================

    /**
     * Crea una factura agrupada en una única transacción:
     *  1. Obtiene número correlativo (SP)
     *  2. Calcula totales consolidados
     *  3. INSERT factura_agrupada
     *  4. Para cada presupuesto: INSERT factura_agrupada_presupuesto
     *  5. Actualiza contador (SP)
     *
     * $datos = [
     *   'ids_presupuesto'   => [1,2,3],
     *   'id_empresa'        => int,
     *   'id_cliente'        => int,
     *   'fecha'             => 'YYYY-MM-DD',   // opcional, default hoy
     *   'observaciones'     => string,          // opcional
     *   'codigo_empresa'    => string,           // para el SP de numeración
     * ]
     *
     * Devuelve ['success'=>true, 'id'=>int, 'numero'=>string] o ['success'=>false, 'message'=>string]
     */
    public function insert_factura_agrupada_transaccion(array $datos)
    {
        try {
            $ids_presupuesto = array_map('intval', $datos['ids_presupuesto']);
            $id_empresa      = (int) $datos['id_empresa'];
            $id_cliente      = (int) $datos['id_cliente'];
            $fecha           = !empty($datos['fecha']) ? $datos['fecha'] : date('Y-m-d');
            $observaciones   = !empty($datos['observaciones']) ? trim($datos['observaciones']) : null;
            $codigo_empresa  = $datos['codigo_empresa'];

            $this->conexion->beginTransaction();

            // --- 1. Obtener número correlativo ---
            $this->conexion->exec("CALL sp_obtener_siguiente_numero('$codigo_empresa', 'factura', @numero_completo)");
            $row_num = $this->conexion->query("SELECT @numero_completo AS numero")->fetch(PDO::FETCH_ASSOC);
            $numero_factura = $row_num['numero'];
            $serie_factura  = strpos($numero_factura, '-') !== false
                ? substr($numero_factura, 0, strpos($numero_factura, '-'))
                : substr($numero_factura, 0, 1); // Ej: 'FE' de 'FE-0003/2026'

            // --- 2. Calcular totales consolidados de los presupuestos ---
            $placeholders = implode(',', array_fill(0, count($ids_presupuesto), '?'));

            $sql_totales = "SELECT
                                SUM(vt.total_base_imponible)                          AS total_base,
                                SUM(vt.total_iva)                                      AS total_iva,
                                SUM(vt.total_con_iva)                                  AS total_bruto,
                                SUM(COALESCE(ant.total_anticipos, 0))                  AS total_anticipos,
                                SUM(COALESCE(prf.total_proformas, 0))                  AS total_proformas
                            FROM v_presupuesto_totales vt
                            LEFT JOIN (
                                SELECT dp.id_presupuesto, SUM(dp.total_documento_ppto) AS total_anticipos
                                FROM documento_presupuesto dp
                                WHERE dp.tipo_documento_ppto = 'factura_anticipo'
                                  AND dp.activo_documento_ppto = 1
                                GROUP BY dp.id_presupuesto
                            ) ant ON ant.id_presupuesto = vt.id_presupuesto
                            LEFT JOIN (
                                SELECT dp.id_presupuesto, SUM(dp.total_documento_ppto) AS total_proformas
                                FROM documento_presupuesto dp
                                WHERE dp.tipo_documento_ppto = 'factura_proforma'
                                  AND dp.activo_documento_ppto = 1
                                GROUP BY dp.id_presupuesto
                            ) prf ON prf.id_presupuesto = vt.id_presupuesto
                            WHERE vt.id_presupuesto IN ($placeholders)
                              AND vt.estado_version_presupuesto = 'aprobado'";

            $stmt_t = $this->conexion->prepare($sql_totales);
            foreach ($ids_presupuesto as $k => $id) {
                $stmt_t->bindValue($k + 1, $id, PDO::PARAM_INT);
            }
            $stmt_t->execute();
            $totales = $stmt_t->fetch(PDO::FETCH_ASSOC);

            $total_base      = round((float)($totales['total_base']      ?? 0), 2);
            $total_iva       = round((float)($totales['total_iva']       ?? 0), 2);
            $total_bruto     = round((float)($totales['total_bruto']     ?? 0), 2);
            $total_anticipos = round((float)($totales['total_anticipos'] ?? 0), 2);
            $total_proformas = round((float)($totales['total_proformas'] ?? 0), 2);
            $total_a_cobrar  = round($total_bruto - $total_anticipos, 2);

            // Añadir nota de proformas a las observaciones si las hay
            if ($total_proformas > 0) {
                $nota_proformas = 'Se ha recibido un anticipo de ' . number_format($total_proformas, 2, ',', '.') . '€ en concepto de factura/s proforma previas.';
                $observaciones  = !empty($observaciones)
                    ? trim($observaciones) . "\n\n" . $nota_proformas
                    : $nota_proformas;
            }

            // --- 3. INSERT factura_agrupada ---
            $sql_ins = "INSERT INTO factura_agrupada (
                            numero_factura_agrupada,
                            serie_factura_agrupada,
                            id_empresa,
                            id_cliente,
                            fecha_factura_agrupada,
                            observaciones_agrupada,
                            total_base_agrupada,
                            total_iva_agrupada,
                            total_bruto_agrupada,
                            total_anticipos_agrupada,
                            total_proformas_agrupada,
                            total_a_cobrar_agrupada,
                            is_abono_agrupada,
                            activo_factura_agrupada,
                            created_at_factura_agrupada
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, NOW())";

            $stmt_ins = $this->conexion->prepare($sql_ins);
            $stmt_ins->bindValue(1,  $numero_factura, PDO::PARAM_STR);
            $stmt_ins->bindValue(2,  $serie_factura,  PDO::PARAM_STR);
            $stmt_ins->bindValue(3,  $id_empresa,     PDO::PARAM_INT);
            $stmt_ins->bindValue(4,  $id_cliente,     PDO::PARAM_INT);
            $stmt_ins->bindValue(5,  $fecha,          PDO::PARAM_STR);
            $stmt_ins->bindValue(6,  $observaciones,  !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt_ins->bindValue(7,  $total_base,      PDO::PARAM_STR);
            $stmt_ins->bindValue(8,  $total_iva,       PDO::PARAM_STR);
            $stmt_ins->bindValue(9,  $total_bruto,     PDO::PARAM_STR);
            $stmt_ins->bindValue(10, $total_anticipos, PDO::PARAM_STR);
            $stmt_ins->bindValue(11, $total_proformas, PDO::PARAM_STR);
            $stmt_ins->bindValue(12, $total_a_cobrar,  PDO::PARAM_STR);
            $stmt_ins->execute();

            $id_factura_agrupada = (int) $this->conexion->lastInsertId();

            // --- 4. Insertar relación por cada presupuesto ---
            foreach ($ids_presupuesto as $orden => $id_presupuesto) {
                // Totales individuales.
                // Se ancla en una subquery de un solo valor para garantizar
                // siempre una fila, incluso si el presupuesto no tiene líneas
                // (no aparecería en v_presupuesto_totales → anticipos ignorados).
                $sql_ind = "SELECT
                                COALESCE(vt.total_base_imponible, 0) AS total_base_imponible,
                                COALESCE(vt.total_iva, 0)            AS total_iva,
                                COALESCE(vt.total_con_iva, 0)        AS total_con_iva,
                                COALESCE(ant.total_anticipos, 0)     AS total_anticipos_reales,
                                COALESCE(prf.total_proformas, 0)     AS total_proformas
                            FROM (SELECT ? AS id_presupuesto) AS base
                            LEFT JOIN v_presupuesto_totales vt
                                ON vt.id_presupuesto = base.id_presupuesto
                               AND vt.estado_version_presupuesto = 'aprobado'
                            LEFT JOIN (
                                SELECT id_presupuesto, SUM(total_documento_ppto) AS total_anticipos
                                FROM documento_presupuesto
                                WHERE tipo_documento_ppto = 'factura_anticipo'
                                  AND activo_documento_ppto = 1
                                GROUP BY id_presupuesto
                            ) ant ON ant.id_presupuesto = base.id_presupuesto
                            LEFT JOIN (
                                SELECT id_presupuesto, SUM(total_documento_ppto) AS total_proformas
                                FROM documento_presupuesto
                                WHERE tipo_documento_ppto = 'factura_proforma'
                                  AND activo_documento_ppto = 1
                                GROUP BY id_presupuesto
                            ) prf ON prf.id_presupuesto = base.id_presupuesto";

                $stmt_ind = $this->conexion->prepare($sql_ind);
                $stmt_ind->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
                $stmt_ind->execute();
                $ind = $stmt_ind->fetch(PDO::FETCH_ASSOC);

                $ind_base      = round((float)($ind['total_base_imponible']   ?? 0), 2);
                $ind_iva       = round((float)($ind['total_iva']              ?? 0), 2);
                $ind_bruto     = round((float)($ind['total_con_iva']          ?? 0), 2);
                $ind_anticipo  = round((float)($ind['total_anticipos_reales'] ?? 0), 2);
                $ind_proformas = round((float)($ind['total_proformas']        ?? 0), 2);
                $ind_resto     = max(0, round($ind_bruto - $ind_anticipo, 2));

                $sql_fap = "INSERT INTO factura_agrupada_presupuesto (
                                id_factura_agrupada,
                                id_presupuesto,
                                total_base_fap,
                                total_iva_fap,
                                total_bruto_fap,
                                total_anticipos_reales_fap,
                                total_proformas_fap,
                                resto_fap,
                                orden_fap,
                                activo_fap,
                                created_at_fap
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

                $stmt_fap = $this->conexion->prepare($sql_fap);
                $stmt_fap->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
                $stmt_fap->bindValue(2, $id_presupuesto,      PDO::PARAM_INT);
                $stmt_fap->bindValue(3, $ind_base,            PDO::PARAM_STR);
                $stmt_fap->bindValue(4, $ind_iva,             PDO::PARAM_STR);
                $stmt_fap->bindValue(5, $ind_bruto,           PDO::PARAM_STR);
                $stmt_fap->bindValue(6, $ind_anticipo,        PDO::PARAM_STR);
                $stmt_fap->bindValue(7, $ind_proformas,       PDO::PARAM_STR);
                $stmt_fap->bindValue(8, $ind_resto,           PDO::PARAM_STR);
                $stmt_fap->bindValue(9, $orden + 1,           PDO::PARAM_INT);
                $stmt_fap->execute();

                // --- 4c. Anular proformas activas del presupuesto (registrando qué FA las anuló) ---
                if ($ind_proformas > 0) {
                    $sql_anular_pf = "UPDATE documento_presupuesto
                                      SET activo_documento_ppto = 0,
                                          anulada_por_fa_id = ?,
                                          updated_at_documento_ppto = NOW()
                                      WHERE id_presupuesto = ?
                                        AND tipo_documento_ppto = 'factura_proforma'
                                        AND activo_documento_ppto = 1";
                    $stmt_pf = $this->conexion->prepare($sql_anular_pf);
                    $stmt_pf->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
                    $stmt_pf->bindValue(2, $id_presupuesto,      PDO::PARAM_INT);
                    $stmt_pf->execute();
                }

                // --- 4b. Registrar pago pendiente en pago_presupuesto ---
                $tipo_pago = ($ind_anticipo > 0) ? 'resto' : 'total';
                $pct_pago  = ($ind_bruto > 0) ? round($ind_resto / $ind_bruto * 100, 2) : 0;

                $sql_pago = "INSERT INTO pago_presupuesto (
                                id_presupuesto,
                                id_factura_agrupada,
                                id_empresa_pago,
                                tipo_pago_ppto,
                                importe_pago_ppto,
                                porcentaje_pago_ppto,
                                fecha_pago_ppto,
                                estado_pago_ppto
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";

                $stmt_pago = $this->conexion->prepare($sql_pago);
                $stmt_pago->bindValue(1, $id_presupuesto,      PDO::PARAM_INT);
                $stmt_pago->bindValue(2, $id_factura_agrupada, PDO::PARAM_INT);
                $stmt_pago->bindValue(3, $id_empresa,          !empty($id_empresa) ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmt_pago->bindValue(4, $tipo_pago,           PDO::PARAM_STR);
                $stmt_pago->bindValue(5, $ind_resto,           PDO::PARAM_STR);
                $stmt_pago->bindValue(6, $pct_pago,            PDO::PARAM_STR);
                $stmt_pago->bindValue(7, $fecha,               PDO::PARAM_STR);
                $stmt_pago->execute();
            }

            // --- 5. Actualizar contador (SP) ---
            $this->conexion->exec("CALL sp_actualizar_contador_empresa($id_empresa, 'factura')");

            $this->conexion->commit();

            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'insert_factura_agrupada_transaccion',
                "Factura agrupada creada: $numero_factura (ID: $id_factura_agrupada) — " . count($ids_presupuesto) . " presupuestos",
                'info'
            );

            return [
                'success' => true,
                'id'      => $id_factura_agrupada,
                'numero'  => $numero_factura,
            ];

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'insert_factura_agrupada_transaccion',
                "Error: " . $e->getMessage(), 'error'
            );
            return [
                'success'  => false,
                'message'  => 'Error al crear la factura agrupada.',
            ];
        }
    }

    // =========================================================
    // ABONO / RECTIFICATIVA
    // =========================================================

    /**
     * Genera un abono de una factura agrupada existente:
     *  1. SP número serie 'abono'
     *  2. INSERT nueva factura_agrupada con is_abono=1, id_ref = original
     *  3. Copia relaciones de presupuesto (activo_fap=1) con importes negativos
     *  4. Desactiva la factura original (activo=0)
     *  5. Actualiza contador SP
     *
     * Devuelve ['success'=>true, 'id'=>int, 'numero'=>string] o ['success'=>false, 'message'=>string]
     */
    public function insert_abono_agrupada($id_factura_agrupada_original, $motivo, $codigo_empresa)
    {
        try {
            // Obtener factura original
            $original = $this->get_factura_agrupadaxid($id_factura_agrupada_original);
            if (!$original) {
                return ['success' => false, 'message' => 'Factura agrupada original no encontrada.'];
            }
            if ($original['is_abono_agrupada']) {
                return ['success' => false, 'message' => 'No se puede abonar una factura que ya es un abono.'];
            }

            $this->conexion->beginTransaction();

            // --- 1. Número de abono ---
            $this->conexion->exec("CALL sp_obtener_siguiente_numero('$codigo_empresa', 'abono', @numero_abono)");
            $row_num = $this->conexion->query("SELECT @numero_abono AS numero")->fetch(PDO::FETCH_ASSOC);
            $numero_abono = $row_num['numero'];
            $serie_abono  = strpos($numero_abono, '-') !== false
                ? substr($numero_abono, 0, strpos($numero_abono, '-'))
                : substr($numero_abono, 0, 1); // Ej: 'R' de 'R-0001/2026'

            // --- 2. Importes negativos (abono) ---
            $base_abono      = round(-(float)$original['total_base_agrupada'],      2);
            $iva_abono       = round(-(float)$original['total_iva_agrupada'],       2);
            $bruto_abono     = round(-(float)$original['total_bruto_agrupada'],     2);
            $anticipos_abono = round(-(float)$original['total_anticipos_agrupada'], 2);
            $proformas_abono = round(-(float)$original['total_proformas_agrupada'], 2);
            $cobrar_abono    = round(-(float)$original['total_a_cobrar_agrupada'],  2);

            // --- 3. INSERT abono ---
            $sql_ab = "INSERT INTO factura_agrupada (
                            numero_factura_agrupada,
                            serie_factura_agrupada,
                            id_empresa,
                            id_cliente,
                            fecha_factura_agrupada,
                            observaciones_agrupada,
                            total_base_agrupada,
                            total_iva_agrupada,
                            total_bruto_agrupada,
                            total_anticipos_agrupada,
                            total_proformas_agrupada,
                            total_a_cobrar_agrupada,
                            is_abono_agrupada,
                            id_factura_agrupada_ref,
                            motivo_abono_agrupada,
                            activo_factura_agrupada,
                            created_at_factura_agrupada
                        ) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, 1, NOW())";

            $stmt_ab = $this->conexion->prepare($sql_ab);
            $stmt_ab->bindValue(1,  $numero_abono,                PDO::PARAM_STR);
            $stmt_ab->bindValue(2,  $serie_abono,                 PDO::PARAM_STR);
            $stmt_ab->bindValue(3,  (int)$original['id_empresa'], PDO::PARAM_INT);
            $stmt_ab->bindValue(4,  (int)$original['id_cliente'], PDO::PARAM_INT);
            $stmt_ab->bindValue(5,  !empty($original['observaciones_agrupada']) ? $original['observaciones_agrupada'] : null,
                                        !empty($original['observaciones_agrupada']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt_ab->bindValue(6,  $base_abono,                   PDO::PARAM_STR);
            $stmt_ab->bindValue(7,  $iva_abono,                    PDO::PARAM_STR);
            $stmt_ab->bindValue(8,  $bruto_abono,                  PDO::PARAM_STR);
            $stmt_ab->bindValue(9,  $anticipos_abono,              PDO::PARAM_STR);
            $stmt_ab->bindValue(10, $proformas_abono,              PDO::PARAM_STR);
            $stmt_ab->bindValue(11, $cobrar_abono,                 PDO::PARAM_STR);
            $stmt_ab->bindValue(12, $id_factura_agrupada_original, PDO::PARAM_INT);
            $stmt_ab->bindValue(13, trim($motivo),                 PDO::PARAM_STR);
            $stmt_ab->execute();

            $id_abono = (int) $this->conexion->lastInsertId();

            // --- 4. Copiar relaciones con importes negativos ---
            $sql_lineas = "SELECT * FROM factura_agrupada_presupuesto
                           WHERE id_factura_agrupada = ? AND activo_fap = 1";
            $stmt_lineas = $this->conexion->prepare($sql_lineas);
            $stmt_lineas->bindValue(1, $id_factura_agrupada_original, PDO::PARAM_INT);
            $stmt_lineas->execute();
            $lineas = $stmt_lineas->fetchAll(PDO::FETCH_ASSOC);

            $sql_fap = "INSERT INTO factura_agrupada_presupuesto (
                            id_factura_agrupada, id_presupuesto,
                            total_base_fap, total_iva_fap, total_bruto_fap,
                            total_anticipos_reales_fap, total_proformas_fap, resto_fap,
                            orden_fap, activo_fap, created_at_fap
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";

            foreach ($lineas as $linea) {
                $stmt_fap2 = $this->conexion->prepare($sql_fap);
                $stmt_fap2->bindValue(1, $id_abono,                                              PDO::PARAM_INT);
                $stmt_fap2->bindValue(2, (int)$linea['id_presupuesto'],                          PDO::PARAM_INT);
                $stmt_fap2->bindValue(3, round(-(float)$linea['total_base_fap'],             2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(4, round(-(float)$linea['total_iva_fap'],              2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(5, round(-(float)$linea['total_bruto_fap'],            2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(6, round(-(float)$linea['total_anticipos_reales_fap'], 2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(7, round(-(float)$linea['total_proformas_fap'],        2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(8, round(-(float)$linea['resto_fap'],                  2), PDO::PARAM_STR);
                $stmt_fap2->bindValue(9, (int)$linea['orden_fap'],                               PDO::PARAM_INT);
                $stmt_fap2->execute();
            }

            // --- 5. Reactivar proformas que fueron anuladas por esta FA ---
            $sql_reactiv_pf = "UPDATE documento_presupuesto
                               SET activo_documento_ppto = 1,
                                   anulada_por_fa_id = NULL,
                                   updated_at_documento_ppto = NOW()
                               WHERE anulada_por_fa_id = ?";
            $stmt_rpf = $this->conexion->prepare($sql_reactiv_pf);
            $stmt_rpf->bindValue(1, $id_factura_agrupada_original, PDO::PARAM_INT);
            $stmt_rpf->execute();

            // --- 5b. Desactivar factura original ---
            $sql_deact = "UPDATE factura_agrupada
                          SET activo_factura_agrupada = 0, updated_at_factura_agrupada = NOW()
                          WHERE id_factura_agrupada = ?";
            $stmt_deact = $this->conexion->prepare($sql_deact);
            $stmt_deact->bindValue(1, $id_factura_agrupada_original, PDO::PARAM_INT);
            $stmt_deact->execute();

            // --- 5c. Anular pagos pendientes de la FA original ---
            $sql_anular_pagos = "UPDATE pago_presupuesto
                                 SET estado_pago_ppto = 'anulado',
                                     updated_at_pago_ppto = NOW()
                                 WHERE id_factura_agrupada = ?
                                   AND activo_pago_ppto = 1
                                   AND estado_pago_ppto = 'pendiente'";
            $stmt_ap = $this->conexion->prepare($sql_anular_pagos);
            $stmt_ap->bindValue(1, $id_factura_agrupada_original, PDO::PARAM_INT);
            $stmt_ap->execute();

            // --- 6. Actualizar contador ---
            $this->conexion->exec("CALL sp_actualizar_contador_empresa(" . (int)$original['id_empresa'] . ", 'abono')");

            $this->conexion->commit();

            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'insert_abono_agrupada',
                "Abono $numero_abono (ID: $id_abono) generado para factura agrupada ID: $id_factura_agrupada_original",
                'info'
            );

            return [
                'success' => true,
                'id'      => $id_abono,
                'numero'  => $numero_abono,
            ];

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'insert_abono_agrupada',
                "Error: " . $e->getMessage(), 'error'
            );
            return [
                'success' => false,
                'message' => 'Error al generar el abono.',
            ];
        }
    }

    // =========================================================
    // ACTUALIZACIONES SIMPLES
    // =========================================================

    /**
     * Soft delete: desactiva una factura agrupada.
     */
    public function delete_factura_agrupadaxid($id_factura_agrupada)
    {
        try {
            $sql = "UPDATE factura_agrupada
                    SET activo_factura_agrupada = 0,
                        updated_at_factura_agrupada = NOW()
                    WHERE id_factura_agrupada = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'delete_factura_agrupadaxid',
                "Factura agrupada desactivada ID: $id_factura_agrupada", 'info'
            );

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'delete_factura_agrupadaxid',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Guarda/actualiza la ruta del PDF generado.
     */
    public function update_pdf_path($id_factura_agrupada, $pdf_path)
    {
        try {
            $sql = "UPDATE factura_agrupada
                    SET pdf_path_agrupada = ?,
                        updated_at_factura_agrupada = NOW()
                    WHERE id_factura_agrupada = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $pdf_path,            PDO::PARAM_STR);
            $stmt->bindValue(2, $id_factura_agrupada, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'update_pdf_path',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // =========================================================
    // AUXILIARES PARA PDF
    // =========================================================

    /**
     * Devuelve datos completos de empresa y cliente para la cabecera del PDF.
     */
    public function get_datos_cabecera_agrupada($id_factura_agrupada)
    {
        try {
            $sql = "SELECT
                        fa.id_factura_agrupada,
                        fa.numero_factura_agrupada,
                        fa.serie_factura_agrupada,
                        fa.fecha_factura_agrupada,
                        fa.observaciones_agrupada,
                        fa.total_base_agrupada,
                        fa.total_iva_agrupada,
                        fa.total_bruto_agrupada,
                        fa.total_anticipos_agrupada,
                        fa.total_proformas_agrupada,
                        fa.total_a_cobrar_agrupada,
                        fa.is_abono_agrupada,
                        fa.motivo_abono_agrupada,
                        fa.pdf_path_agrupada,
                        -- Empresa
                        e.id_empresa,
                        e.nombre_empresa,
                        e.nombre_comercial_empresa,
                        e.nif_empresa,
                        e.direccion_fiscal_empresa,
                        e.poblacion_fiscal_empresa,
                        e.cp_fiscal_empresa,
                        e.provincia_fiscal_empresa,
                        e.pais_fiscal_empresa,
                        e.email_empresa,
                        e.telefono_empresa,
                        e.logotipo_empresa,
                        e.texto_pie_factura_empresa,
                        e.texto_legal_factura_empresa,
                        e.iban_empresa,
                        e.swift_empresa,
                        e.banco_empresa,
                        e.mostrar_cuenta_bancaria_pdf_presupuesto_empresa,
                        e.permitir_descuentos_lineas_empresa,
                        e.mostrar_subtotales_fecha_presupuesto_empresa,
                        -- Cliente
                        c.id_cliente,
                        c.nombre_cliente,
                        c.nif_cliente,
                        c.email_cliente,
                        c.telefono_cliente,
                        c.direccion_cliente,
                        c.poblacion_cliente,
                        c.cp_cliente,
                        c.provincia_cliente,
                        -- Forma de pago del primer presupuesto incluido
                        (SELECT fp.nombre_pago
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN forma_pago fp ON fp.id_pago = p2.id_forma_pago
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS nombre_pago,
                        (SELECT mp.nombre_metodo_pago
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN forma_pago fp ON fp.id_pago = p2.id_forma_pago
                         LEFT JOIN metodo_pago mp ON mp.id_metodo_pago = fp.id_metodo_pago
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS nombre_metodo_pago,
                        -- Contacto del primer presupuesto incluido
                        (SELECT cc.nombre_contacto_cliente
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN contacto_cliente cc ON cc.id_contacto_cliente = p2.id_contacto_cliente
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                           AND p2.id_contacto_cliente IS NOT NULL
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS nombre_contacto_cliente,
                        (SELECT cc.apellidos_contacto_cliente
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN contacto_cliente cc ON cc.id_contacto_cliente = p2.id_contacto_cliente
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                           AND p2.id_contacto_cliente IS NOT NULL
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS apellidos_contacto_cliente,
                        (SELECT cc.telefono_contacto_cliente
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN contacto_cliente cc ON cc.id_contacto_cliente = p2.id_contacto_cliente
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                           AND p2.id_contacto_cliente IS NOT NULL
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS telefono_contacto_cliente,
                        (SELECT cc.email_contacto_cliente
                         FROM factura_agrupada_presupuesto fap2
                         INNER JOIN presupuesto p2 ON p2.id_presupuesto = fap2.id_presupuesto
                         LEFT JOIN contacto_cliente cc ON cc.id_contacto_cliente = p2.id_contacto_cliente
                         WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
                           AND fap2.activo_fap = 1
                           AND p2.id_contacto_cliente IS NOT NULL
                         ORDER BY fap2.orden_fap ASC LIMIT 1) AS email_contacto_cliente,
                        -- Número de la FA original (solo si es abono)
                        fa.id_factura_agrupada_ref,
                        (SELECT fa_orig.numero_factura_agrupada
                         FROM factura_agrupada fa_orig
                         WHERE fa_orig.id_factura_agrupada = fa.id_factura_agrupada_ref
                         LIMIT 1) AS numero_factura_agrupada_original
                    FROM factura_agrupada fa
                    INNER JOIN empresa e  ON e.id_empresa = fa.id_empresa
                    INNER JOIN cliente c  ON c.id_cliente = fa.id_cliente
                    WHERE fa.id_factura_agrupada = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_factura_agrupada, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_datos_cabecera_agrupada',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Devuelve las líneas de presupuesto para el PDF, incluyendo líneas de artículo
     * agrupadas por presupuesto.
     * Usa v_linea_presupuesto_calculada para cada presupuesto incluido.
     */
    public function get_lineas_pdf_agrupada($id_factura_agrupada)
    {
        try {
            // Obtener presupuestos incluidos
            $presupuestos = $this->get_presupuestos_factura_agrupada($id_factura_agrupada);
            if (empty($presupuestos)) {
                return [];
            }

            $resultado = [];
            foreach ($presupuestos as $ppto) {
                $id_ppto = (int) $ppto['id_presupuesto'];

                $sql_lineas = "SELECT vlc.*
                               FROM v_linea_presupuesto_calculada vlc
                               WHERE vlc.id_presupuesto = ?
                               ORDER BY vlc.orden_linea_ppto ASC";
                $stmt_l = $this->conexion->prepare($sql_lineas);
                $stmt_l->bindValue(1, $id_ppto, PDO::PARAM_INT);
                $stmt_l->execute();
                $lineas = $stmt_l->fetchAll(PDO::FETCH_ASSOC);

                // Facturas anticipo activas (referencia + importes base/iva/total para PDF)
                $sql_ant = "SELECT numero_documento_ppto,
                                   COALESCE(subtotal_documento_ppto, 0)   AS subtotal_documento_ppto,
                                   COALESCE(total_iva_documento_ppto, 0)  AS total_iva_documento_ppto,
                                   COALESCE(total_documento_ppto, 0)      AS total_documento_ppto
                            FROM documento_presupuesto
                            WHERE id_presupuesto = ?
                              AND tipo_documento_ppto = 'factura_anticipo'
                              AND activo_documento_ppto = 1
                            ORDER BY fecha_emision_documento_ppto ASC";
                $stmt_a = $this->conexion->prepare($sql_ant);
                $stmt_a->bindValue(1, $id_ppto, PDO::PARAM_INT);
                $stmt_a->execute();
                $ppto['anticipos_documentos'] = $stmt_a->fetchAll(PDO::FETCH_ASSOC);

                $resultado[] = [
                    'presupuesto' => $ppto,
                    'lineas'      => $lineas,
                ];
            }

            return $resultado;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_lineas_pdf_agrupada',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Devuelve el código de empresa para el SP de numeración a partir del id_empresa.
     */
    public function get_codigo_empresa($id_empresa)
    {
        try {
            $sql = "SELECT codigo_empresa FROM empresa WHERE id_empresa = ? AND activo_empresa = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['codigo_empresa'] : null;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_codigo_empresa',
                "Error: " . $e->getMessage(), 'error'
            );
            return null;
        }
    }

    /**
     * Detecta si algún presupuesto del cliente ya tiene una factura activa
     * (proforma, anticipo o final) y devuelve la empresa real asociada.
     * Si hay más de una empresa real distinta, devuelve la primera encontrada.
     * Devuelve array con datos de la empresa o null si no hay ninguna bloqueada.
     */
    public function detectar_empresa_real_cliente($id_cliente)
    {
        try {
            $sql = "SELECT e.id_empresa, e.nombre_empresa, e.nombre_comercial_empresa,
                           e.nif_empresa, e.codigo_empresa
                    FROM documento_presupuesto dp
                    INNER JOIN empresa e ON e.id_empresa = dp.id_empresa
                    INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
                    WHERE p.id_cliente = ?
                      AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
                      AND dp.activo_documento_ppto = 1
                      AND e.ficticia_empresa = 0
                      AND e.activo_empresa = 1
                    ORDER BY dp.created_at_documento_ppto DESC
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'detectar_empresa_real_cliente',
                "Error: " . $e->getMessage(), 'error'
            );
            return null;
        }
    }

    /**
     * Detecta si alguno de los presupuestos seleccionados ya tiene una factura activa
     * (proforma, anticipo o final) y devuelve la empresa real asociada (la más reciente).
     * Usado en el wizard para bloquear la empresa una vez seleccionados los presupuestos.
     *
     * @param array $ids_presupuesto  Array de IDs de presupuesto seleccionados
     * @return array|null  Datos de la empresa o null si ninguno tiene factura previa
     */
    public function detectar_empresa_real_presupuestos(array $ids_presupuesto)
    {
        if (empty($ids_presupuesto)) {
            return null;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($ids_presupuesto), '?'));
            $sql = "SELECT e.id_empresa, e.nombre_empresa, e.nombre_comercial_empresa,
                           e.nif_empresa, e.codigo_empresa
                    FROM documento_presupuesto dp
                    INNER JOIN empresa e ON e.id_empresa = dp.id_empresa
                    WHERE dp.id_presupuesto IN ($placeholders)
                      AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
                      AND dp.activo_documento_ppto = 1
                      AND e.ficticia_empresa = 0
                      AND e.activo_empresa = 1
                    ORDER BY dp.created_at_documento_ppto DESC
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $i = 1;
            foreach ($ids_presupuesto as $id) {
                $stmt->bindValue($i++, (int)$id, PDO::PARAM_INT);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'detectar_empresa_real_presupuestos',
                "Error: " . $e->getMessage(), 'error'
            );
            return null;
        }
    }

    /**
     * Devuelve todas las empresas reales activas (ficticia_empresa = 0).
     */
    public function get_empresas_reales_activas()
    {
        try {
            $sql = "SELECT id_empresa, codigo_empresa, nombre_empresa,
                           nombre_comercial_empresa, nif_empresa
                    FROM empresa
                    WHERE ficticia_empresa = 0
                      AND activo_empresa = 1
                    ORDER BY nombre_empresa ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'FacturaAgrupada', 'get_empresas_reales_activas',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }
}
?>
