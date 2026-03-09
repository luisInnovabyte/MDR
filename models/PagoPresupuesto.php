<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

/**
 * Modelo PagoPresupuesto
 *
 * Gestiona los pagos parciales o totales asociados a un presupuesto.
 *
 * Tipos de pago (ENUM tabla):
 *  - anticipo      → pago parcial adelantado
 *  - total         → pago completo del presupuesto
 *  - resto         → pago del importe restante tras anticipos
 *  - devolucion    → importe devuelto al cliente
 *
 * Estados de pago (ENUM tabla):
 *  - pendiente     → registrado pero no confirmado
 *  - recibido      → cobrado (estado por defecto al registrar)
 *  - conciliado    → verificado con extracto bancario
 *  - anulado       → anulado (por abono u otro motivo)
 */
class PagoPresupuesto
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
                'PagoPresupuesto',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // ══════════════════════════════════════════════════════════
    //  LECTURA
    // ══════════════════════════════════════════════════════════

    /**
     * Obtiene todos los pagos activos de un presupuesto.
     *
     * @param int $id_presupuesto
     * @return array
     */
    public function get_pagos_presupuesto(int $id_presupuesto): array
    {
        try {
            $sql = "SELECT *
                    FROM   v_pagos_presupuesto
                    WHERE  id_presupuesto = ?
                      AND  activo_pago_ppto = 1
                    ORDER  BY fecha_pago_ppto ASC, id_pago_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_pagos_presupuesto',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Obtiene todos los pagos (incluidos inactivos/anulados) de un presupuesto.
     *
     * @param int $id_presupuesto
     * @return array
     */
    public function get_pagos_presupuesto_todos(int $id_presupuesto): array
    {
        try {
            $sql = "SELECT *
                    FROM   v_pagos_presupuesto
                    WHERE  id_presupuesto = ?
                    ORDER  BY fecha_pago_ppto ASC, id_pago_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_pagos_presupuesto_todos',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Obtiene un pago por su ID.
     *
     * @param int $id_pago_ppto
     * @return array|false
     */
    public function get_pagoxid(int $id_pago_ppto)
    {
        try {
            $sql = "SELECT *
                    FROM   v_pagos_presupuesto
                    WHERE  id_pago_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_pagoxid',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Obtiene el pago activo vinculado a un documento concreto.
     *
     * @param int $id_documento_ppto
     * @return array|false
     */
    public function get_pago_vinculado_documento(int $id_documento_ppto)
    {
        try {
            $sql = "SELECT *
                    FROM   pago_presupuesto
                    WHERE  id_documento_ppto  = ?
                      AND  activo_pago_ppto   = 1
                    LIMIT  1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_pago_vinculado_documento',
                "Error id_documento=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  CÁLCULOS
    // ══════════════════════════════════════════════════════════

    /**
     * Calcula el total pagado (no anulado) de un presupuesto.
     * Las devoluciones restan.
     *
     * @param int $id_presupuesto
     * @return float
     */
    public function get_total_pagado(int $id_presupuesto): float
    {
        try {
            $sql = "SELECT
                        SUM(
                            CASE
                                WHEN tipo_pago_ppto = 'devolucion'
                                THEN -importe_pago_ppto
                                ELSE  importe_pago_ppto
                            END
                        ) AS total_pagado
                    FROM   pago_presupuesto
                    WHERE  id_presupuesto  = ?
                      AND  activo_pago_ppto = 1
                      AND  estado_pago_ppto != 'anulado'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($row['total_pagado'] ?? 0);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_total_pagado',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return 0.0;
        }
    }

    /**
     * Devuelve la empresa bloqueada basándose en los propios pagos.
     * Si total_pagado <= 0 → false (empresa libre).
     * Si total_pagado > 0  → empresa del primer pago activo no-anulado con id_empresa_pago IS NOT NULL.
     *
     * @param int $id_presupuesto
     * @return array|false  Fila de empresa o false
     */
    public function obtener_empresa_bloqueada_por_pagos(int $id_presupuesto)
    {
        // Si no hay saldo pagado, la empresa está libre
        if ($this->get_total_pagado($id_presupuesto) <= 0) {
            return false;
        }

        try {
            $sql = "SELECT e.*
                    FROM   pago_presupuesto pp
                    JOIN   empresa e ON pp.id_empresa_pago = e.id_empresa
                    WHERE  pp.id_presupuesto   = ?
                      AND  pp.id_empresa_pago  IS NOT NULL
                      AND  pp.activo_pago_ppto = 1
                      AND  pp.estado_pago_ppto != 'anulado'
                      AND  e.ficticia_empresa  = 0
                    ORDER  BY pp.created_at_pago_ppto ASC
                    LIMIT  1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: false;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'obtener_empresa_bloqueada_por_pagos',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Calcula el saldo pendiente de cobro.
     * = total_con_iva (vista) - total_pagado
     *
     * @param int $id_presupuesto
     * @return float
     */
    public function get_saldo_pendiente(int $id_presupuesto): float
    {
        // Delega en get_resumen_financiero para garantizar la misma lógica
        // de cálculo (empresa_ficticia_principal, permitir_descuentos, etc.)
        // que el PDF y la interfaz.
        return $this->get_resumen_financiero($id_presupuesto)['saldo_pendiente'];
    }

    /**
     * Calcula el porcentaje que representa un importe sobre el total del presupuesto.
     *
     * @param int   $id_presupuesto
     * @param float $importe
     * @return float  Porcentaje (0-100)
     */
    public function calcular_porcentaje_pago(int $id_presupuesto, float $importe): float
    {
        // Delega en get_resumen_financiero para usar el mismo total que el PDF.
        $resumen = $this->get_resumen_financiero($id_presupuesto);
        $total   = $resumen['total_presupuesto'];

        if ($total <= 0) {
            return 0.0;
        }

        return round(($importe / $total) * 100, 4);
    }

    /**
     * Obtiene el resumen financiero de un presupuesto.
     *
     * @param int $id_presupuesto
     * @return array {total_presupuesto, total_pagado, saldo_pendiente, porcentaje_pagado}
     */
    public function get_resumen_financiero(int $id_presupuesto): array
    {
        try {
            // Calcula el total con la misma lógica condicional que el PDF:
            //  - Si empresa_ficticia_principal.permitir_descuentos_lineas_empresa = 1
            //    → usa base_imponible de la vista (con dto aplicado)
            //  - Si = 0 → recalcula tarifa pura sin aplicar descuento_linea_ppto
            // IMPORTANTE: el PDF siempre usa la empresa ficticia principal
            // (get_empresa_datos → WHERE empresa_ficticia_principal=1), ignorando
            // presupuesto.id_empresa. Este query replica exactamente ese comportamiento.
            $sql = "SELECT
                        ROUND(SUM(CASE
                            WHEN e.permitir_descuentos_lineas_empresa IS NULL
                              OR e.permitir_descuentos_lineas_empresa = 1
                            THEN vlpc.base_imponible
                            ELSE CASE
                                WHEN vlpc.aplicar_coeficiente_linea_ppto = 1
                                     AND vlpc.valor_coeficiente_linea_ppto > 0
                                THEN vlpc.cantidad_linea_ppto * vlpc.precio_unitario_linea_ppto * vlpc.valor_coeficiente_linea_ppto
                                ELSE vlpc.dias_linea * vlpc.cantidad_linea_ppto * vlpc.precio_unitario_linea_ppto
                            END
                        END), 2) AS total_base,
                        ROUND(SUM(CASE
                            WHEN e.permitir_descuentos_lineas_empresa IS NULL
                              OR e.permitir_descuentos_lineas_empresa = 1
                            THEN vlpc.importe_iva
                            ELSE (CASE
                                WHEN vlpc.aplicar_coeficiente_linea_ppto = 1
                                     AND vlpc.valor_coeficiente_linea_ppto > 0
                                THEN vlpc.cantidad_linea_ppto * vlpc.precio_unitario_linea_ppto * vlpc.valor_coeficiente_linea_ppto
                                ELSE vlpc.dias_linea * vlpc.cantidad_linea_ppto * vlpc.precio_unitario_linea_ppto
                            END) * (vlpc.porcentaje_iva_linea_ppto / 100)
                        END), 2) AS total_iva
                    FROM v_linea_presupuesto_calculada vlpc
                    JOIN presupuesto p
                         ON p.id_presupuesto             = vlpc.id_presupuesto
                        AND p.version_actual_presupuesto = vlpc.numero_version_presupuesto
                    LEFT JOIN empresa e
                         ON e.empresa_ficticia_principal = 1
                        AND e.activo_empresa             = 1
                    WHERE p.id_presupuesto         = ?
                      AND vlpc.activo_linea_ppto    = 1
                      AND vlpc.mostrar_en_presupuesto = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_presupuesto = round((float)($row['total_base'] ?? 0) + (float)($row['total_iva'] ?? 0), 2);

            $total_pagado   = $this->get_total_pagado($id_presupuesto);
            $saldo_pendiente = round($total_presupuesto - $total_pagado, 2);
            $porcentaje_pagado = $total_presupuesto > 0
                ? round(($total_pagado / $total_presupuesto) * 100, 2)
                : 0.0;

            return [
                'total_presupuesto' => $total_presupuesto,
                'total_pagado'      => $total_pagado,
                'saldo_pendiente'   => $saldo_pendiente,
                'porcentaje_pagado' => $porcentaje_pagado,
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'get_resumen_financiero',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return [
                'total_presupuesto' => 0.0,
                'total_pagado'      => 0.0,
                'saldo_pendiente'   => 0.0,
                'porcentaje_pagado' => 0.0,
            ];
        }
    }

    // ══════════════════════════════════════════════════════════
    //  ESCRITURA
    // ══════════════════════════════════════════════════════════

    /**
     * Registra un nuevo pago.
     *
     * @param array $datos {
     *   id_presupuesto      int       (obligatorio)
     *   tipo_pago_ppto      string    anticipo|total|resto|devolucion (obligatorio)
     *   importe_pago_ppto   float     (obligatorio)
     *   fecha_pago_ppto     string    YYYY-MM-DD (obligatorio)
     *   id_metodo_pago      int       (obligatorio)
     *   id_documento_ppto   int       (opcional, si hay factura asociada)
     *   referencia_pago_ppto string   (opcional)
     *   fecha_valor_pago_ppto string  (opcional, YYYY-MM-DD)
     *   estado_pago_ppto    string    (opcional, default 'recibido')
     *   observaciones_pago_ppto string (opcional)
     * }
     * @return int|false  ID del pago creado o false en error
     */
    public function insert_pago(array $datos)
    {
        try {
            // Calcular porcentaje automáticamente
            $porcentaje = $this->calcular_porcentaje_pago(
                $datos['id_presupuesto'],
                (float)$datos['importe_pago_ppto']
            );

            $sql = "INSERT INTO pago_presupuesto (
                        id_presupuesto,
                        id_documento_ppto,
                        id_empresa_pago,
                        tipo_pago_ppto,
                        importe_pago_ppto,
                        porcentaje_pago_ppto,
                        id_metodo_pago,
                        referencia_pago_ppto,
                        fecha_pago_ppto,
                        fecha_valor_pago_ppto,
                        estado_pago_ppto,
                        observaciones_pago_ppto
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1,  $datos['id_presupuesto'],    PDO::PARAM_INT);
            $stmt->bindValue(2,  $datos['id_documento_ppto']  ?? null,  !empty($datos['id_documento_ppto'])  ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(3,  $datos['id_empresa_pago']    ?? null,  !empty($datos['id_empresa_pago'])    ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(4,  $datos['tipo_pago_ppto'],    PDO::PARAM_STR);
            $stmt->bindValue(5,  $datos['importe_pago_ppto'], PDO::PARAM_STR);
            $stmt->bindValue(6,  $porcentaje,                 PDO::PARAM_STR);
            $stmt->bindValue(7,  $datos['id_metodo_pago'],    PDO::PARAM_INT);
            $stmt->bindValue(8,  $datos['referencia_pago_ppto']   ?? null, !empty($datos['referencia_pago_ppto'])   ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9,  $datos['fecha_pago_ppto'],   PDO::PARAM_STR);
            $stmt->bindValue(10, $datos['fecha_valor_pago_ppto']  ?? null, !empty($datos['fecha_valor_pago_ppto'])  ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, $datos['estado_pago_ppto']   ?? 'recibido', PDO::PARAM_STR);
            $stmt->bindValue(12, $datos['observaciones_pago_ppto'] ?? null, !empty($datos['observaciones_pago_ppto']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            $id = (int)$this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'insert_pago',
                "Pago registrado ID=$id tipo={$datos['tipo_pago_ppto']} importe={$datos['importe_pago_ppto']} presupuesto={$datos['id_presupuesto']}", 'info'
            );

            return $id;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'insert_pago',
                "Error presupuesto=" . ($datos['id_presupuesto'] ?? '?') . ": " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Actualiza un pago existente.
     *
     * @param int   $id_pago_ppto
     * @param array $datos  Mismo formato que insert_pago (todos los campos son opcionales en update)
     * @return bool
     */
    public function update_pago(int $id_pago_ppto, array $datos): bool
    {
        try {
            // Si cambia el importe, recalcular porcentaje
            if (isset($datos['importe_pago_ppto']) && isset($datos['id_presupuesto'])) {
                $datos['porcentaje_pago_ppto'] = $this->calcular_porcentaje_pago(
                    $datos['id_presupuesto'],
                    (float)$datos['importe_pago_ppto']
                );
            }

            $campos_set = [];
            $params     = [];

            $map = [
                'id_documento_ppto'       => PDO::PARAM_INT,
                'id_empresa_pago'         => PDO::PARAM_INT,
                'tipo_pago_ppto'          => PDO::PARAM_STR,
                'importe_pago_ppto'       => PDO::PARAM_STR,
                'porcentaje_pago_ppto'    => PDO::PARAM_STR,
                'id_metodo_pago'          => PDO::PARAM_INT,
                'referencia_pago_ppto'    => PDO::PARAM_STR,
                'fecha_pago_ppto'         => PDO::PARAM_STR,
                'fecha_valor_pago_ppto'   => PDO::PARAM_STR,
                'estado_pago_ppto'        => PDO::PARAM_STR,
                'observaciones_pago_ppto' => PDO::PARAM_STR,
            ];

            foreach ($map as $campo => $tipo_pdo) {
                if (array_key_exists($campo, $datos)) {
                    $campos_set[] = "$campo = ?";
                    $params[]     = [$datos[$campo], $tipo_pdo];
                }
            }

            if (empty($campos_set)) {
                return false;
            }

            $campos_set[] = "updated_at_pago_ppto = NOW()";

            $sql = "UPDATE pago_presupuesto SET " . implode(', ', $campos_set) .
                   " WHERE id_pago_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $i = 1;
            foreach ($params as [$valor, $tipo_pdo]) {
                $stmt->bindValue($i++, $valor, $tipo_pdo);
            }
            $stmt->bindValue($i, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->registro->registrarActividad(
                    'admin', 'PagoPresupuesto', 'update_pago',
                    "Pago actualizado ID=$id_pago_ppto", 'info'
                );
            }

            return $ok;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'update_pago',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Soft delete de un pago (activo_pago_ppto = 0).
     *
     * @param int $id_pago_ppto
     * @return bool
     */
    public function delete_pagoxid(int $id_pago_ppto): bool
    {
        try {
            $sql = "UPDATE pago_presupuesto
                    SET    activo_pago_ppto     = 0,
                           updated_at_pago_ppto = NOW()
                    WHERE  id_pago_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->registro->registrarActividad(
                    'admin', 'PagoPresupuesto', 'delete_pagoxid',
                    "Pago desactivado ID=$id_pago_ppto", 'info'
                );
            }

            return $ok;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'delete_pagoxid',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Reactiva un pago previamente desactivado.
     *
     * @param int $id_pago_ppto
     * @return bool
     */
    public function activar_pagoxid(int $id_pago_ppto): bool
    {
        try {
            $sql = "UPDATE pago_presupuesto
                    SET    activo_pago_ppto     = 1,
                           updated_at_pago_ppto = NOW()
                    WHERE  id_pago_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'activar_pagoxid',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Anula un pago por ser objeto de un abono.
     * Estado pasa a 'anulado' (NO se hace soft delete: debe quedar en histórico).
     *
     * @param int $id_pago_ppto
     * @return bool
     */
    public function anular_pago_por_abono(int $id_pago_ppto): bool
    {
        try {
            $sql = "UPDATE pago_presupuesto
                    SET    estado_pago_ppto     = 'anulado',
                           updated_at_pago_ppto = NOW()
                    WHERE  id_pago_ppto       = ?
                      AND  activo_pago_ppto   = 1
                      AND  estado_pago_ppto  != 'anulado'";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->registro->registrarActividad(
                    'admin', 'PagoPresupuesto', 'anular_pago_por_abono',
                    "Pago anulado por abono ID=$id_pago_ppto", 'info'
                );
            }

            return $ok;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'anular_pago_por_abono',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Marca un pago como conciliado con extracto bancario.
     *
     * @param int $id_pago_ppto
     * @return bool
     */
    public function conciliar_pago(int $id_pago_ppto): bool
    {
        try {
            $sql = "UPDATE pago_presupuesto
                    SET    estado_pago_ppto     = 'conciliado',
                           updated_at_pago_ppto = NOW()
                    WHERE  id_pago_ppto      = ?
                      AND  activo_pago_ppto  = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago_ppto, PDO::PARAM_INT);
            $stmt->execute();

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->registro->registrarActividad(
                    'admin', 'PagoPresupuesto', 'conciliar_pago',
                    "Pago conciliado ID=$id_pago_ppto", 'info'
                );
            }

            return $ok;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'PagoPresupuesto', 'conciliar_pago',
                "Error id=$id_pago_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }
}
