<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class InformeVentas
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
                'InformeVentas',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Devuelve los KPIs globales de ventas para un año dado.
     * - Total facturado (suma total_linea_ppto)
     * - Nº de presupuestos distintos
     * - Ticket promedio (total / nº presupuestos)
     * - Mes con más ingresos del año
     *
     * @param int $anyo  Año (ej: 2026). Si es 0 o null → todos los años.
     * @return array
     */
    public function getKpisVentas($anyo = 0, $mes = 0)
    {
        try {
            $sql = "SELECT
                        ROUND(SUM(total_linea_ppto), 2)              AS total_facturado,
                        COUNT(DISTINCT id_presupuesto)               AS num_presupuestos,
                        ROUND(
                            SUM(total_linea_ppto) /
                            NULLIF(COUNT(DISTINCT id_presupuesto), 0)
                        , 2)                                          AS ticket_promedio
                    FROM vista_ventas_periodo";

            $where  = [];
            $params = [];

            if ($anyo > 0) { $where[] = 'anyo_presupuesto = ?'; $params[] = (int) $anyo; }
            if ($mes  > 0) { $where[] = 'mes_presupuesto  = ?'; $params[] = (int) $mes;  }

            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $kpis = $stmt->fetch(PDO::FETCH_ASSOC);

            // Mes con más ingresos del año
            $sqlMesMejor = "SELECT
                                mes_presupuesto,
                                ROUND(SUM(total_linea_ppto), 2) AS total_mes
                            FROM vista_ventas_periodo";

            $whereMes  = [];
            $paramsMes = [];
            if ($anyo > 0) { $whereMes[] = 'anyo_presupuesto = ?'; $paramsMes[] = (int) $anyo; }
            if ($mes  > 0) { $whereMes[] = 'mes_presupuesto  = ?'; $paramsMes[] = (int) $mes;  }

            if (!empty($whereMes)) {
                $sqlMesMejor .= ' WHERE ' . implode(' AND ', $whereMes);
            }

            $sqlMesMejor .= " GROUP BY mes_presupuesto ORDER BY total_mes DESC LIMIT 1";

            $stmtMes = $this->conexion->prepare($sqlMesMejor);
            $stmtMes->execute($paramsMes);
            $mesMejor = $stmtMes->fetch(PDO::FETCH_ASSOC);

            $kpis['mes_top']       = $mesMejor['mes_presupuesto'] ?? null;
            $kpis['mes_top_total'] = $mesMejor['total_mes']       ?? 0;

            return $kpis;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getKpisVentas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve los 12 meses del año con el total de ventas en cada uno.
     * Si un mes no tiene datos devuelve 0 para que el gráfico no quede vacío.
     *
     * @param int $anyo
     * @return array  [ ['mes' => 1, 'total' => 1234.56], ... ] (12 filas)
     */
    public function getVentasPorMes($anyo)
    {
        try {
            // Tabla auxiliar de 1..12 para garantizar los 12 meses
            $sql = "SELECT
                        meses.mes,
                        COALESCE(ROUND(SUM(v.total_linea_ppto), 2), 0) AS total
                    FROM (
                        SELECT 1 AS mes UNION SELECT 2 UNION SELECT 3  UNION
                        SELECT 4      UNION SELECT 5 UNION SELECT 6  UNION
                        SELECT 7      UNION SELECT 8 UNION SELECT 9  UNION
                        SELECT 10     UNION SELECT 11 UNION SELECT 12
                    ) AS meses
                    LEFT JOIN vista_ventas_periodo v
                        ON v.mes_presupuesto  = meses.mes
                        AND v.anyo_presupuesto = ?
                    GROUP BY meses.mes
                    ORDER BY meses.mes ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, (int) $anyo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getVentasPorMes',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve el ranking de clientes por facturación en un año.
     *
     * @param int $anyo
     * @param int $limite  Nº de clientes a devolver (default: 10)
     * @return array
     */
    public function getTopClientes($anyo, $limite = 10, $mes = 0)
    {
        try {
            $sql = "SELECT
                        id_cliente,
                        nombre_completo_cliente,
                        COUNT(DISTINCT id_presupuesto)     AS num_presupuestos,
                        ROUND(SUM(total_linea_ppto), 2)   AS total_facturado
                    FROM vista_ventas_periodo";

            $where  = [];
            $params = [];

            if ($anyo > 0) { $where[] = 'anyo_presupuesto = ?'; $params[] = (int) $anyo; }
            if ($mes  > 0) { $where[] = 'mes_presupuesto  = ?'; $params[] = (int) $mes;  }

            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $sql .= " GROUP BY id_cliente, nombre_completo_cliente
                      ORDER BY total_facturado DESC
                      LIMIT ?";
            $params[] = (int) $limite;

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getTopClientes',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve el total facturado agrupado por familia de artículo.
     *
     * @param int $anyo
     * @return array
     */
    public function getVentasPorFamilia($anyo, $mes = 0)
    {
        try {
            $sql = "SELECT
                        id_familia,
                        nombre_familia,
                        codigo_familia,
                        COUNT(DISTINCT id_presupuesto)     AS num_presupuestos,
                        ROUND(SUM(total_linea_ppto), 2)   AS total_facturado,
                        ROUND(SUM(cantidad_linea_ppto), 0) AS total_unidades
                    FROM vista_ventas_periodo";

            $where  = [];
            $params = [];

            if ($anyo > 0) { $where[] = 'anyo_presupuesto = ?'; $params[] = (int) $anyo; }
            if ($mes  > 0) { $where[] = 'mes_presupuesto  = ?'; $params[] = (int) $mes;  }

            if (!empty($where)) {
                $sql .= ' WHERE ' . implode(' AND ', $where);
            }

            $sql .= " GROUP BY id_familia, nombre_familia, codigo_familia
                      ORDER BY total_facturado DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getVentasPorFamilia',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve los años disponibles en la vista (para el selector de año).
     *
     * @return array  [ ['anyo' => 2024], ['anyo' => 2025], ... ]
     */
    public function getAnyosDisponibles()
    {
        try {
            $sql = "SELECT DISTINCT anyo_presupuesto AS anyo
                    FROM vista_ventas_periodo
                    ORDER BY anyo DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getAnyosDisponibles',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
