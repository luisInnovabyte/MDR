<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class ClientePanel
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
                'ClientePanel',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Devuelve los KPIs consolidados de un cliente:
     * total_presupuestado, total_facturas_emitidas, total_cobrado, saldo_pendiente,
     * num_presupuestos, num_presupuestos_activos.
     *
     * @param int $id_cliente
     * @return array
     */
    public function getKpisCliente(int $id_cliente): array
    {
        try {
            $stmt = $this->conexion->prepare(
                "SELECT COALESCE(SUM(vt.total_con_iva), 0)
                 FROM   presupuesto p
                 INNER  JOIN v_presupuesto_totales vt
                        ON  vt.id_presupuesto = p.id_presupuesto
                        AND vt.numero_version_presupuesto = p.version_actual_presupuesto
                 WHERE  p.id_cliente = ? AND p.activo_presupuesto = 1"
            );
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $totalPresupuestado = (float) $stmt->fetchColumn();

            $stmt = $this->conexion->prepare(
                "SELECT COALESCE(SUM(dp.total_documento_ppto), 0)
                 FROM   v_documentos_presupuesto dp
                 WHERE  dp.id_cliente = ?
                   AND  dp.tipo_documento_ppto IN ('factura_anticipo','factura_final','factura_rectificativa')"
            );
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $totalFacturas = (float) $stmt->fetchColumn();

            $stmt = $this->conexion->prepare(
                "SELECT COALESCE(SUM(pp.importe_pago_ppto), 0)
                 FROM   v_pagos_presupuesto pp
                 WHERE  pp.id_cliente = ?
                   AND  pp.estado_pago_ppto IN ('recibido', 'conciliado')"
            );
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $totalCobrado = (float) $stmt->fetchColumn();

            $stmt = $this->conexion->prepare(
                "SELECT COUNT(*) FROM presupuesto WHERE id_cliente = ? AND activo_presupuesto = 1"
            );
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $numPresupuestos = (int) $stmt->fetchColumn();

            $this->registro->registrarActividad('admin', 'ClientePanel', 'getKpisCliente',
                "KPIs calculados para id_cliente=$id_cliente", 'info');

            return [
                'total_presupuestado'     => $totalPresupuestado,
                'total_facturas_emitidas' => $totalFacturas,
                'total_cobrado'           => $totalCobrado,
                'saldo_pendiente'         => max(0.0, $totalFacturas - $totalCobrado),
                'num_presupuestos'        => $numPresupuestos,
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad('admin', 'ClientePanel', 'getKpisCliente',
                "Error id_cliente=$id_cliente: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Devuelve los presupuestos de un cliente para el DataTable del panel.
     * Usar vista_presupuesto_completa WHERE id_cliente = ?
     *
     * @param int $id_cliente
     * @return array
     */
    public function getPresupuestosPorCliente(int $id_cliente): array
    {
        // TODO: implementar
        return [];
    }

    /**
     * Devuelve los documentos factura de un cliente.
     * Tipos: factura_anticipo, factura_final, factura_proforma, factura_rectificativa.
     * JOIN documento_presupuesto → presupuesto WHERE id_cliente = ?
     *
     * @param int $id_cliente
     * @return array
     */
    public function getDocumentosPorCliente(int $id_cliente): array
    {
        // TODO: implementar
        return [];
    }

    /**
     * Devuelve los pagos registrados para todos los presupuestos de un cliente.
     * JOIN pago_presupuesto → presupuesto WHERE id_cliente = ?
     *
     * @param int $id_cliente
     * @return array
     */
    public function getPagosPorCliente(int $id_cliente): array
    {
        // TODO: implementar
        return [];
    }
}
?>
