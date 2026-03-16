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
     * Devuelve los KPIs globales de ventas con comparación de dos períodos.
     * - Total facturado (suma total_linea_ppto)
     * - Nº de presupuestos distintos
     * - Ticket promedio (total / nº presupuestos)
     * - Mes con más ingresos del año
     *
     * @param int $anyo_actual      Año actual
     * @param int $mes_actual       Mes actual (0 = todos)
     * @param int $anyo_comparar    Año a comparar (0 = sin comparación)
     * @param int $mes_comparar     Mes a comparar (0 = todos)
     * @return array
     */
    public function getKpisVentas($anyo_actual = 0, $mes_actual = 0, $anyo_comparar = 0, $mes_comparar = 0)
    {
        try {
            // KPI #1: Siempre año completo (mes=0)
            $datosActualAnyo = $this->consultarKpisPeriodo($anyo_actual, 0);
            
            // KPI #2: Siempre mes específico
            // Si no hay mes seleccionado (0), usar el mes actual del sistema
            $mesEspecifico = ($mes_actual > 0) ? $mes_actual : (int)date('n');
            $ingresosMesActual = $this->consultarIngresosMes($anyo_actual, $mesEspecifico);
            
            // KPIs #3 y #4: Datos del mes seleccionado (o año si no hay mes)
            $datosActualMes = null;
            if ($mes_actual > 0) {
                // Si hay mes seleccionado, consultar datos específicos del mes
                $datosActualMes = $this->consultarKpisPeriodo($anyo_actual, $mes_actual);
            }
            
            // Combinar datos
            $datosActual = $datosActualAnyo;
            $datosActual['ingresos_mes_actual'] = $ingresosMesActual;
            $datosActual['mes_especifico'] = $mesEspecifico;
            $datosActual['tiene_mes_seleccionado'] = ($mes_actual > 0);
            
            // Si hay mes seleccionado, agregar datos del mes para KPIs #3 y #4
            if ($datosActualMes) {
                $datosActual['num_presupuestos_mes'] = $datosActualMes['num_presupuestos'];
                $datosActual['ticket_promedio_mes'] = $datosActualMes['ticket_promedio'];
            } else {
                // Si no hay mes, usar los mismos valores del año
                $datosActual['num_presupuestos_mes'] = $datosActualAnyo['num_presupuestos'];
                $datosActual['ticket_promedio_mes'] = $datosActualAnyo['ticket_promedio'];
            }
            
            $resultado = [
                'actual' => $datosActual,
                'comparar' => null,
                'comparacion' => false
            ];
            
            // Si hay año a comparar, consultar y calcular diferencias
            if ($anyo_comparar > 0) {
                // KPI #1: Año completo del año a comparar
                $datosCompararAnyo = $this->consultarKpisPeriodo($anyo_comparar, 0);
                
                // KPI #2: Mismo mes específico del año a comparar
                $ingresosMesComparar = $this->consultarIngresosMes($anyo_comparar, $mesEspecifico);
                
                // KPIs #3 y #4: Datos del mes del año a comparar
                $datosCompararMes = null;
                if ($mes_actual > 0) {
                    // Si hay mes seleccionado, consultar datos del mismo mes del año anterior
                    $datosCompararMes = $this->consultarKpisPeriodo($anyo_comparar, $mes_actual);
                }
                
                // Combinar datos
                $datosComparar = $datosCompararAnyo;
                $datosComparar['ingresos_mes_actual'] = $ingresosMesComparar;
                $datosComparar['mes_especifico'] = $mesEspecifico;
                $datosComparar['tiene_mes_seleccionado'] = ($mes_actual > 0);
                
                // Si hay mes seleccionado, agregar datos del mes para KPIs #3 y #4
                if ($datosCompararMes) {
                    $datosComparar['num_presupuestos_mes'] = $datosCompararMes['num_presupuestos'];
                    $datosComparar['ticket_promedio_mes'] = $datosCompararMes['ticket_promedio'];
                } else {
                    $datosComparar['num_presupuestos_mes'] = $datosCompararAnyo['num_presupuestos'];
                    $datosComparar['ticket_promedio_mes'] = $datosCompararAnyo['ticket_promedio'];
                }
                
                $resultado['comparar'] = $datosComparar;
                $resultado['comparacion'] = true;
                
                // Calcular diferencias y porcentajes
                $resultado['diferencias'] = [
                    'total_facturado' => $this->calcularDiferencia(
                        $datosActual['total_facturado'],
                        $datosComparar['total_facturado']
                    ),
                    'ingresos_mes_actual' => $this->calcularDiferencia(
                        $ingresosMesActual,
                        $ingresosMesComparar
                    ),
                    'num_presupuestos_mes' => $this->calcularDiferencia(
                        $datosActual['num_presupuestos_mes'],
                        $datosComparar['num_presupuestos_mes']
                    ),
                    'num_presupuestos_anyo' => $this->calcularDiferencia(
                        $datosActual['num_presupuestos'],
                        $datosComparar['num_presupuestos']
                    ),
                    'ticket_promedio_mes' => $this->calcularDiferencia(
                        $datosActual['ticket_promedio_mes'],
                        $datosComparar['ticket_promedio_mes']
                    ),
                    'ticket_promedio_anyo' => $this->calcularDiferencia(
                        $datosActual['ticket_promedio'],
                        $datosComparar['ticket_promedio']
                    ),
                    'mes_top_total' => $this->calcularDiferencia(
                        $datosActual['mes_top_total'],
                        $datosComparar['mes_top_total']
                    )
                ];
            }
            
            return $resultado;
            
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
     * Consulta los KPIs para un período específico
     */
    private function consultarKpisPeriodo($anyo, $mes)
    {
        $sql = "SELECT
                    ROUND(SUM(total_linea_ppto), 2)              AS total_facturado,
                    COUNT(DISTINCT id_presupuesto)               AS num_presupuestos,
                    ROUND(
                        SUM(total_linea_ppto) /
                        NULLIF(COUNT(DISTINCT id_presupuesto), 0)
                    , 2)                                          AS ticket_promedio
                FROM vista_ventas_periodo
                WHERE 1=1";

        // Construir WHERE dinámicamente
        if ($anyo > 0) {
            $sql .= " AND anyo_presupuesto = :anyo";
        }
        if ($mes > 0) {
            $sql .= " AND mes_presupuesto = :mes";
        }

        $stmt = $this->conexion->prepare($sql);
        
        // Bind de parámetros
        if ($anyo > 0) {
            $stmt->bindValue(':anyo', (int)$anyo, PDO::PARAM_INT);
        }
        if ($mes > 0) {
            $stmt->bindValue(':mes', (int)$mes, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $kpis = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mes con más ingresos del período
        $sqlMesMejor = "SELECT
                            mes_presupuesto,
                            ROUND(SUM(total_linea_ppto), 2) AS total_mes
                        FROM vista_ventas_periodo
                        WHERE 1=1";

        // Construir WHERE dinámicamente
        if ($anyo > 0) {
            $sqlMesMejor .= " AND anyo_presupuesto = :anyo";
        }
        if ($mes > 0) {
            $sqlMesMejor .= " AND mes_presupuesto = :mes";
        }

        $sqlMesMejor .= " GROUP BY mes_presupuesto ORDER BY total_mes DESC LIMIT 1";

        $stmtMes = $this->conexion->prepare($sqlMesMejor);
        
        // Bind de parámetros
        if ($anyo > 0) {
            $stmtMes->bindValue(':anyo', (int)$anyo, PDO::PARAM_INT);
        }
        if ($mes > 0) {
            $stmtMes->bindValue(':mes', (int)$mes, PDO::PARAM_INT);
        }
        
        $stmtMes->execute();
        $mesMejor = $stmtMes->fetch(PDO::FETCH_ASSOC);

        $kpis['mes_top']       = $mesMejor['mes_presupuesto'] ?? null;
        $kpis['mes_top_total'] = $mesMejor['total_mes']       ?? 0;

        return $kpis;
    }
    
    /**
     * Consulta los ingresos de un mes específico de un año
     */
    private function consultarIngresosMes($anyo, $mes)
    {
        $sql = "SELECT ROUND(SUM(total_linea_ppto), 2) AS total_mes
                FROM vista_ventas_periodo
                WHERE anyo_presupuesto = ? AND mes_presupuesto = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int) $anyo, (int) $mes]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total_mes'] ?? 0;
    }
    
    /**
     * Calcula la diferencia absoluta, porcentaje y tendencia
     */
    private function calcularDiferencia($actual, $comparar)
    {
        $diferencia = $actual - $comparar;
        $porcentaje = 0;
        
        if ($comparar != 0) {
            $porcentaje = round(($diferencia / $comparar) * 100, 1);
        }
        
        return [
            'diferencia' => round($diferencia, 2),
            'porcentaje' => $porcentaje,
            'tendencia' => $diferencia >= 0 ? 'up' : 'down'
        ];
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
    public function getTopClientes($anyo, $limite = 0, $mes = 0)
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
                      ORDER BY total_facturado DESC";

            if ($limite > 0) {
                $sql .= ' LIMIT ?';
                $params[] = (int) $limite;
            }

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

    /**
     * Devuelve ingresos mensuales agrupados por familia (para gráfico lineal).
     * Limita a las top 6 familias y agrupa el resto en "Otras".
     *
     * @param int $anyo
     * @return array  [ 'Familia A' => [val1, val2, ..., val12], 'Familia B' => [...], ... ]
     */
    public function getIngresosPorFamiliaMensual($anyo)
    {
        try {
            // 1. Obtener top 6 familias por facturación total del año
            $sqlTop = "SELECT nombre_familia,
                              ROUND(SUM(total_linea_ppto), 2) AS total_anual
                       FROM vista_ventas_periodo
                       WHERE anyo_presupuesto = ?
                       GROUP BY nombre_familia
                       ORDER BY total_anual DESC
                       LIMIT 6";
            
            $stmtTop = $this->conexion->prepare($sqlTop);
            $stmtTop->bindValue(1, (int)$anyo, PDO::PARAM_INT);
            $stmtTop->execute();
            $topFamilias = $stmtTop->fetchAll(PDO::FETCH_COLUMN, 0); // Array de nombres
            
            // 2. Obtener ingresos mensuales para las top familias
            $resultado = [];
            
            foreach ($topFamilias as $familia) {
                $sqlMensual = "SELECT
                                   meses.mes,
                                   COALESCE(ROUND(SUM(v.total_linea_ppto), 2), 0) AS total
                               FROM (
                                   SELECT 1 AS mes UNION SELECT 2 UNION SELECT 3  UNION
                                   SELECT 4      UNION SELECT 5 UNION SELECT 6  UNION
                                   SELECT 7      UNION SELECT 8 UNION SELECT 9  UNION
                                   SELECT 10     UNION SELECT 11 UNION SELECT 12
                               ) AS meses
                               LEFT JOIN vista_ventas_periodo v
                                   ON v.mes_presupuesto = meses.mes
                                   AND v.anyo_presupuesto = ?
                                   AND v.nombre_familia = ?
                               GROUP BY meses.mes
                               ORDER BY meses.mes ASC";
                
                $stmtMensual = $this->conexion->prepare($sqlMensual);
                $stmtMensual->bindValue(1, (int)$anyo, PDO::PARAM_INT);
                $stmtMensual->bindValue(2, $familia, PDO::PARAM_STR);
                $stmtMensual->execute();
                
                $datos = $stmtMensual->fetchAll(PDO::FETCH_ASSOC);
                $valores = array_column($datos, 'total');
                
                $resultado[$familia] = $valores;
            }
            
            // 3. Calcular "Otras" (resto de familias)
            if (count($topFamilias) > 0) {
                $placeholders = str_repeat('?,', count($topFamilias) - 1) . '?';
                $sqlOtras = "SELECT
                                 meses.mes,
                                 COALESCE(ROUND(SUM(v.total_linea_ppto), 2), 0) AS total
                             FROM (
                                 SELECT 1 AS mes UNION SELECT 2 UNION SELECT 3  UNION
                                 SELECT 4      UNION SELECT 5 UNION SELECT 6  UNION
                                 SELECT 7      UNION SELECT 8 UNION SELECT 9  UNION
                                 SELECT 10     UNION SELECT 11 UNION SELECT 12
                             ) AS meses
                             LEFT JOIN vista_ventas_periodo v
                                 ON v.mes_presupuesto = meses.mes
                                 AND v.anyo_presupuesto = ?
                                 AND v.nombre_familia NOT IN ($placeholders)
                             GROUP BY meses.mes
                             ORDER BY meses.mes ASC";
                
                $stmtOtras = $this->conexion->prepare($sqlOtras);
                $stmtOtras->bindValue(1, (int)$anyo, PDO::PARAM_INT);
                
                foreach ($topFamilias as $idx => $familia) {
                    $stmtOtras->bindValue($idx + 2, $familia, PDO::PARAM_STR);
                }
                
                $stmtOtras->execute();
                $datosOtras = $stmtOtras->fetchAll(PDO::FETCH_ASSOC);
                $valoresOtras = array_column($datosOtras, 'total');
                
                // Solo incluir "Otras" si tiene valores significativos
                $totalOtras = array_sum($valoresOtras);
                if ($totalOtras > 0) {
                    $resultado['Otras'] = $valoresOtras;
                }
            }
            
            return $resultado;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeVentas',
                'getIngresosPorFamiliaMensual',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
