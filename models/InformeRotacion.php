<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class InformeRotacion
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
                'InformeRotacion',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Devuelve los KPIs globales de rotación de inventario.
     * - Total artículos activos
     * - Artículos usados en el período (días)
     * - % de uso
     * - Artículos sin uso en el período
     *
     * @param int $diasPeriodo  Nº de días a analizar hacia atrás (90, 180, 365, 0=todos)
     * @return array
     */
    public function getKpisRotacion($diasPeriodo = 90, $idFamilia = null)
    {
        try {
            $familiaWhere  = '';
            $familiaParams = [];
            if (!empty($idFamilia)) {
                $familiaWhere  = ' AND id_familia = ?';
                $familiaParams = [(int) $idFamilia];
            }

            // Total artículos en el scope (toda familia o una concreta)
            $sqlTotal = "SELECT COUNT(*) FROM vista_rotacion_inventario WHERE 1 = 1" . $familiaWhere;
            $stmtTotal = $this->conexion->prepare($sqlTotal);
            $stmtTotal->execute($familiaParams);
            $totalArticulos = (int) $stmtTotal->fetchColumn();

            // Artículos usados/sin uso en el período
            $baseWhere = "WHERE 1 = 1" . $familiaWhere;

            if ($diasPeriodo > 0) {
                $sqlPeriodo = "SELECT
                                    SUM(CASE WHEN total_usos > 0 AND dias_desde_ultimo_uso <= ? THEN 1 ELSE 0 END) AS usados,
                                    SUM(CASE WHEN total_usos = 0 OR dias_desde_ultimo_uso > ?  THEN 1 ELSE 0 END) AS sin_uso
                               FROM vista_rotacion_inventario " . $baseWhere;
                $stmtP = $this->conexion->prepare($sqlPeriodo);
                $stmtP->execute(array_merge([(int) $diasPeriodo, (int) $diasPeriodo], $familiaParams));
                $conteos = $stmtP->fetch(PDO::FETCH_ASSOC);
            } else {
                $sql   = "SELECT
                        COUNT(CASE WHEN total_usos > 0 THEN 1 END) AS usados,
                        COUNT(CASE WHEN total_usos = 0 THEN 1 END) AS sin_uso
                    FROM vista_rotacion_inventario " . $baseWhere;
                $stmtP = $this->conexion->prepare($sql);
                $stmtP->execute($familiaParams);
                $conteos = $stmtP->fetch(PDO::FETCH_ASSOC);
            }

            $usados  = (int) ($conteos['usados']  ?? 0);
            $sinUso  = (int) ($conteos['sin_uso'] ?? 0);
            $pctUso  = $totalArticulos > 0
                ? round(($usados / $totalArticulos) * 100, 1)
                : 0;

            return [
                'total_articulos'    => $totalArticulos,
                'articulos_usados'   => $usados,
                'articulos_sin_uso'  => $sinUso,
                'porcentaje_uso'     => $pctUso,
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeRotacion',
                'getKpisRotacion',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve el Top N de artículos más alquilados.
     *
     * @param int $diasPeriodo  0 = sin límite de período
     * @param int $limite
     * @return array
     */
    public function getTopArticulos($diasPeriodo = 0, $limite = 10, $idFamilia = null)
    {
        try {
            $sql = "SELECT
                        id_articulo,
                        nombre_articulo,
                        nombre_familia,
                        total_usos,
                        total_unidades_alquiladas
                    FROM vista_rotacion_inventario
                    WHERE total_usos > 0";

            $params = [];

            if (!empty($idFamilia)) {
                $sql     .= " AND id_familia = ?";
                $params[] = (int) $idFamilia;
            }

            if ($diasPeriodo > 0) {
                $sql     .= " AND (dias_desde_ultimo_uso IS NOT NULL AND dias_desde_ultimo_uso <= ?)";
                $params[] = (int) $diasPeriodo;
            }

            $sql     .= " ORDER BY total_usos DESC LIMIT ?";
            $params[] = (int) $limite;

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeRotacion',
                'getTopArticulos',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve la tabla completa de rotación de todos los artículos.
     * Admite filtros por familia y por período.
     *
     * @param array $filtros  ['id_familia' => int, 'dias_periodo' => int]
     * @return array
     */
    public function getTablaRotacion($filtros = [])
    {
        try {
            $sql = "SELECT
                        id_articulo,
                        codigo_articulo,
                        nombre_articulo,
                        id_familia,
                        nombre_familia,
                        total_usos,
                        total_unidades_alquiladas,
                        ultimo_uso,
                        dias_desde_ultimo_uso
                    FROM vista_rotacion_inventario
                    WHERE 1 = 1";

            $params = [];

            // Filtro por familia
            if (!empty($filtros['id_familia'])) {
                $sql   .= " AND id_familia = ?";
                $params[] = (int) $filtros['id_familia'];
            }

            // Filtro por período: solo artículos usados dentro de N días
            // Con histórico (dias=0) se muestran todos los artículos
            if (!empty($filtros['dias_periodo']) && $filtros['dias_periodo'] > 0) {
                $sql   .= " AND total_usos > 0 AND dias_desde_ultimo_uso <= ?";
                $params[] = (int) $filtros['dias_periodo'];
            }

            $sql .= " ORDER BY total_usos DESC, nombre_articulo ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeRotacion',
                'getTablaRotacion',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Devuelve las familias disponibles (para el selector de filtro).
     *
     * @return array
     */
    public function getFamilias()
    {
        try {
            $sql = "SELECT DISTINCT id_familia, nombre_familia
                    FROM vista_rotacion_inventario
                    WHERE id_familia > 0
                    ORDER BY nombre_familia ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'InformeRotacion',
                'getFamilias',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Resumen de usos agrupado por familia (para gráfico donut).
     */
    public function getResumenFamilias($diasPeriodo = 0)
    {
        try {
            $sql = "SELECT
                        COALESCE(f.id_familia, 0)          AS id_familia,
                        COALESCE(f.nombre_familia, 'Sin familia') AS nombre_familia,
                        COUNT(DISTINCT p.id_presupuesto)   AS total_usos
                    FROM articulo a
                    LEFT JOIN familia f ON a.id_familia = f.id_familia
                    LEFT JOIN linea_presupuesto lp ON a.id_articulo = lp.id_articulo
                        AND lp.activo_linea_ppto = 1 AND lp.tipo_linea_ppto = 'articulo'
                    LEFT JOIN presupuesto_version pv ON lp.id_version_presupuesto = pv.id_version_presupuesto
                    LEFT JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                        AND p.activo_presupuesto = 1
                        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    LEFT JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                        AND ep.codigo_estado_ppto = 'APROB'
                    WHERE a.activo_articulo = 1";

            $params = [];
            if ($diasPeriodo > 0) {
                $sql .= " AND p.fecha_presupuesto >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
                $params[] = (int) $diasPeriodo;
            }

            $sql .= " GROUP BY f.id_familia, f.nombre_familia
                      HAVING total_usos > 0
                      ORDER BY total_usos DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'InformeRotacion', 'getResumenFamilias',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Evolución mensual de presupuestos aprobados (últimos 24 meses).
     * El JS separa en año actual vs año anterior.
     */
    public function getTendenciaMensual()
    {
        try {
            $sql = "SELECT
                        DATE_FORMAT(p.fecha_presupuesto, '%Y-%m') AS mes,
                        COUNT(DISTINCT p.id_presupuesto)           AS total_presupuestos
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                        AND ep.codigo_estado_ppto = 'APROB'
                    WHERE p.activo_presupuesto = 1
                    AND p.fecha_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)
                    GROUP BY DATE_FORMAT(p.fecha_presupuesto, '%Y-%m')
                    ORDER BY mes ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'InformeRotacion', 'getTendenciaMensual',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Comparativa de usos por artículo: período actual vs período anterior.
     * Devuelve [id_articulo => ['usos_actual' => N, 'usos_anterior' => N]]
     */
    public function getTendenciaArticulos($diasPeriodo = 90)
    {
        if ($diasPeriodo <= 0) return [];

        try {
            $sql = "SELECT
                        a.id_articulo,
                        COUNT(DISTINCT CASE
                            WHEN p.fecha_presupuesto >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                            THEN p.id_presupuesto END) AS usos_actual,
                        COUNT(DISTINCT CASE
                            WHEN p.fecha_presupuesto >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                            AND  p.fecha_presupuesto <  DATE_SUB(CURDATE(), INTERVAL ? DAY)
                            THEN p.id_presupuesto END) AS usos_anterior
                    FROM articulo a
                    LEFT JOIN linea_presupuesto lp ON a.id_articulo = lp.id_articulo
                        AND lp.activo_linea_ppto = 1 AND lp.tipo_linea_ppto = 'articulo'
                    LEFT JOIN presupuesto_version pv ON lp.id_version_presupuesto = pv.id_version_presupuesto
                    LEFT JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                        AND p.activo_presupuesto = 1
                        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    LEFT JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                        AND ep.codigo_estado_ppto = 'APROB'
                    WHERE a.activo_articulo = 1
                    GROUP BY a.id_articulo";

            $stmt  = $this->conexion->prepare($sql);
            $doble = $diasPeriodo * 2;
            $stmt->bindValue(1, (int) $diasPeriodo, PDO::PARAM_INT);
            $stmt->bindValue(2, (int) $doble,       PDO::PARAM_INT);
            $stmt->bindValue(3, (int) $diasPeriodo, PDO::PARAM_INT);
            $stmt->execute();

            $result = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $result[(int) $row['id_articulo']] = [
                    'usos_actual'   => (int) $row['usos_actual'],
                    'usos_anterior' => (int) $row['usos_anterior'],
                ];
            }
            return $result;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'InformeRotacion', 'getTendenciaArticulos',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }
}
?>
