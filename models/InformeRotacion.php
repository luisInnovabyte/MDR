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
    public function getKpisRotacion($diasPeriodo = 90)
    {
        try {
            // Total artículos activos
            $sqlTotal = "SELECT COUNT(*) AS total FROM articulo WHERE activo_articulo = 1";
            $stmtTotal = $this->conexion->prepare($sqlTotal);
            $stmtTotal->execute();
            $totalArticulos = (int) $stmtTotal->fetchColumn();

            // Artículos usados en el período
            $sql = "SELECT
                        COUNT(CASE WHEN total_usos > 0 THEN 1 END) AS usados,
                        COUNT(CASE WHEN total_usos = 0 THEN 1 END) AS sin_uso
                    FROM vista_rotacion_inventario";

            if ($diasPeriodo > 0) {
                // Contar artículos con usos dentro del período
                $sqlPeriodo = "SELECT
                                    SUM(CASE WHEN dias_desde_ultimo_uso <= ? OR total_usos > 0 AND dias_desde_ultimo_uso <= ? THEN 1 ELSE 0 END) AS usados,
                                    SUM(CASE WHEN total_usos = 0 OR dias_desde_ultimo_uso > ? THEN 1 ELSE 0 END) AS sin_uso
                               FROM vista_rotacion_inventario";
                $stmtP = $this->conexion->prepare($sqlPeriodo);
                $stmtP->bindValue(1, (int) $diasPeriodo, PDO::PARAM_INT);
                $stmtP->bindValue(2, (int) $diasPeriodo, PDO::PARAM_INT);
                $stmtP->bindValue(3, (int) $diasPeriodo, PDO::PARAM_INT);
                $stmtP->execute();
                $conteos = $stmtP->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmtP = $this->conexion->prepare($sql);
                $stmtP->execute();
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
    public function getTopArticulos($diasPeriodo = 0, $limite = 10)
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

            if ($diasPeriodo > 0) {
                $sql .= " AND (dias_desde_ultimo_uso IS NOT NULL AND dias_desde_ultimo_uso <= ?)";
            }

            $sql .= " ORDER BY total_usos DESC LIMIT ?";

            $stmt   = $this->conexion->prepare($sql);
            $params = [];

            if ($diasPeriodo > 0) {
                $params[] = (int) $diasPeriodo;
            }
            $params[] = (int) $limite;
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

            // Filtro por período (artículos usados dentro de N días)
            if (!empty($filtros['dias_periodo']) && $filtros['dias_periodo'] > 0) {
                $sql   .= " AND (
                                total_usos = 0
                                OR dias_desde_ultimo_uso <= ?
                            )";
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
}
?>
