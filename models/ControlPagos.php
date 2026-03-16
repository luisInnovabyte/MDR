<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class ControlPagos
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
                'ControlPagos',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Obtiene el listado global de control de pagos (presupuestos aprobados).
     * Admite filtros opcionales: solo_pendientes, fecha_evento_desde, fecha_evento_hasta.
     *
     * @param array $filtros
     * @return array
     */
    public function get_control_pagos($filtros = [])
    {
        try {
            $sql = "SELECT
                        id_presupuesto,
                        numero_presupuesto,
                        fecha_presupuesto,
                        fecha_inicio_evento_presupuesto,
                        fecha_fin_evento_presupuesto,
                        nombre_evento_presupuesto,
                        numero_pedido_cliente_presupuesto,
                        id_cliente,
                        nombre_completo_cliente,
                        nombre_estado_ppto,
                        codigo_estado_ppto,
                        color_estado_ppto,
                        nombre_forma_pago,
                        ROUND(total_presupuesto, 2)   AS total_presupuesto,
                        ROUND(total_pagado, 2)         AS total_pagado,
                        ROUND(total_conciliado, 2)     AS total_conciliado,
                        ROUND(saldo_pendiente, 2)      AS saldo_pendiente,
                        porcentaje_pagado,
                        fecha_ultimo_pago,
                        fecha_ultima_factura,
                        metodos_pago_usados,
                        num_pagos,
                        tipos_documentos,
                        created_at_presupuesto,
                        updated_at_presupuesto
                    FROM vista_control_pagos
                    WHERE 1=1";

            $params = [];

            // Filtro: solo presupuestos con saldo pendiente > 0
// Filtro: solo con saldo pendiente de facturar (Aprobado − Facturado > 0)
        if (!empty($filtros['solo_pdte_facturar'])) {
            $sql .= " AND ROUND(saldo_pendiente, 2) > 0";
        }
        // Filtro: solo con saldo pendiente de cobrar (Facturado − Pagado > 0)
        if (!empty($filtros['solo_pdte_cobrar'])) {
            $sql .= " AND ROUND(total_pagado - total_conciliado, 2) > 0";
        }
            if (!empty($filtros['fecha_evento_desde'])) {
                $sql .= " AND (fecha_inicio_evento_presupuesto >= ? OR fecha_inicio_evento_presupuesto IS NULL)";
                $params[] = $filtros['fecha_evento_desde'];
            }

            // Filtro: fecha inicio evento hasta
            if (!empty($filtros['fecha_evento_hasta'])) {
                $sql .= " AND (fecha_inicio_evento_presupuesto <= ? OR fecha_inicio_evento_presupuesto IS NULL)";
                $params[] = $filtros['fecha_evento_hasta'];
            }

            $sql .= " ORDER BY fecha_inicio_evento_presupuesto ASC, id_presupuesto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ControlPagos',
                'get_control_pagos',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve los KPIs globales del sistema de control de pagos.
     * Acepta los mismos filtros que get_control_pagos() para que los KPI
     * reflejen siempre los datos de las filas visibles en la tabla.
     *
     * @param array $filtros
     * @return array
     */
    public function get_resumen_global($filtros = [])
    {
        try {
            $sql = "SELECT
                        COUNT(*)                                                AS total_presupuestos,
                        ROUND(SUM(total_presupuesto), 2)                       AS suma_total_global,
                        ROUND(SUM(total_pagado), 2)                            AS suma_total_pagado,
                        ROUND(SUM(total_conciliado), 2)                        AS suma_total_conciliado,
                        ROUND(SUM(saldo_pendiente), 2)                         AS suma_total_pendiente,
                        COUNT(CASE WHEN saldo_pendiente = 0 AND total_presupuesto > 0 THEN 1 END) AS presupuestos_pagados_completo,
                        COUNT(CASE WHEN total_pagado > 0 AND saldo_pendiente > 0 THEN 1 END)      AS presupuestos_pago_parcial,
                        COUNT(CASE WHEN total_pagado = 0 THEN 1 END)           AS presupuestos_sin_pagos,
                        ROUND(
                            CASE WHEN SUM(total_pagado) > 0
                            THEN SUM(total_conciliado) / SUM(total_pagado) * 100
                            ELSE 0 END, 2
                        ) AS porcentaje_global_pagado
                    FROM vista_control_pagos
                    WHERE 1=1";

            $params = [];

            if (!empty($filtros['solo_pdte_facturar'])) {
                $sql .= " AND ROUND(saldo_pendiente, 2) > 0";
            }
            if (!empty($filtros['solo_pdte_cobrar'])) {
                $sql .= " AND ROUND(total_pagado - total_conciliado, 2) > 0";
            }
            if (!empty($filtros['fecha_evento_desde'])) {
                $sql .= " AND (fecha_inicio_evento_presupuesto >= ? OR fecha_inicio_evento_presupuesto IS NULL)";
                $params[] = $filtros['fecha_evento_desde'];
            }
            if (!empty($filtros['fecha_evento_hasta'])) {
                $sql .= " AND (fecha_inicio_evento_presupuesto <= ? OR fecha_inicio_evento_presupuesto IS NULL)";
                $params[] = $filtros['fecha_evento_hasta'];
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ControlPagos',
                'get_resumen_global',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Obtiene el desglose individual de pagos de un presupuesto concreto.
     *
     * @param int $id_presupuesto
     * @return array
     */
    public function get_detalle_pagos_presupuesto($id_presupuesto)
    {
        try {
            $sql = "SELECT
                        pp.id_pago_ppto,
                        pp.tipo_pago_ppto,
                        pp.importe_pago_ppto,
                        pp.porcentaje_pago_ppto,
                        pp.fecha_pago_ppto,
                        pp.fecha_valor_pago_ppto,
                        pp.estado_pago_ppto,
                        pp.referencia_pago_ppto,
                        pp.observaciones_pago_ppto,
                        mp.nombre_metodo_pago,
                        dp.tipo_documento_ppto,
                        dp.numero_documento_ppto,
                        dp.total_documento_ppto
                    FROM pago_presupuesto pp
                    LEFT JOIN metodo_pago mp  ON pp.id_metodo_pago    = mp.id_metodo_pago
                    LEFT JOIN documento_presupuesto dp ON pp.id_documento_ppto = dp.id_documento_ppto
                    WHERE pp.id_presupuesto     = ?
                      AND pp.activo_pago_ppto   = 1
                    ORDER BY pp.fecha_pago_ppto ASC, pp.id_pago_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ControlPagos',
                'get_detalle_pagos_presupuesto',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
