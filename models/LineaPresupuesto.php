<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class LineaPresupuesto
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        // 1. Inicializar conexión PDO
        $this->conexion = (new Conexion())->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'LineaPresupuesto',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // =========================================================
    // MÉTODO DEBUG: Obtener conexión (solo para debug)
    // =========================================================
    public function getConexion()
    {
        return $this->conexion;
    }

    // =========================================================
    // MÉTODO 1: Listar líneas de una versión (usando VISTA)
    // =========================================================
    /**
     * Obtiene todas las líneas de una versión de presupuesto con cálculos
     * 
     * @param int $id_version_presupuesto ID de la versión
     * @return array Líneas con todos los cálculos realizados
     */
    public function get_lineas_version($id_version_presupuesto)
    {
        try {
            // Usar VISTA para obtener datos con cálculos
            $sql = "SELECT * FROM v_linea_presupuesto_calculada 
                    WHERE id_version_presupuesto = ? 
                    AND activo_linea_ppto = 1 
                    ORDER BY orden_linea_ppto ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id_version_presupuesto]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'LineaPresupuesto',
                'get_lineas_version',
                "Error al obtener líneas: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =========================================================
    // MÉTODO 2: Obtener totales de una versión (usando VISTA)
    // =========================================================
    /**
     * Obtiene los totales (PIE) de una versión de presupuesto
     * 
     * @param int $id_version_presupuesto ID de la versión
     * @return array|false Totales del presupuesto o false si error
     */
    public function get_totales_version($id_version_presupuesto)
    {
        try {
            // Usar VISTA de totales
            $sql = "SELECT * FROM v_presupuesto_totales 
                    WHERE id_version_presupuesto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id_version_presupuesto]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'LineaPresupuesto',
                'get_totales_version',
                "Error al obtener totales: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 3: Obtener una línea por ID (usando VISTA)
    // =========================================================
    /**
     * Obtiene una línea específica con todos sus cálculos
     * 
     * @param int $id_linea_ppto ID de la línea
     * @return array|false Datos de la línea o false si no existe
     */
    public function get_lineaxid($id_linea_ppto)
    {
        try {
            // Usar VISTA para obtener datos calculados
            $sql = "SELECT * FROM v_linea_presupuesto_calculada 
                    WHERE id_linea_ppto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$id_linea_ppto]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'LineaPresupuesto',
                'get_lineaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 4: Insertar nueva línea (usando TABLA)
    // =========================================================
    /**
     * Inserta una nueva línea de presupuesto
     * 
     * @param array $datos Array asociativo con los datos de la línea
     * @return int|false ID de la línea insertada o false si error
     */
    public function insert_linea($datos)
    {
        try {
            $sql = "INSERT INTO linea_presupuesto (
                id_version_presupuesto,
                id_articulo,
                id_linea_padre,
                id_ubicacion,
                id_coeficiente,
                id_impuesto,
                numero_linea_ppto,
                tipo_linea_ppto,
                nivel_jerarquia,
                orden_linea_ppto,
                codigo_linea_ppto,
                descripcion_linea_ppto,
                cantidad_linea_ppto,
                precio_unitario_linea_ppto,
                descuento_linea_ppto,
                jornadas_linea_ppto,
                valor_coeficiente_linea_ppto,
                porcentaje_iva_linea_ppto,
                fecha_montaje_linea_ppto,
                fecha_desmontaje_linea_ppto,
                fecha_inicio_linea_ppto,
                fecha_fin_linea_ppto,
                observaciones_linea_ppto,
                mostrar_obs_articulo_linea_ppto,
                ocultar_detalle_kit_linea_ppto,
                mostrar_en_presupuesto,
                es_opcional,
                activo_linea_ppto
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?
            )";

            $stmt = $this->conexion->prepare($sql);
            
            $stmt->execute([
                $datos['id_version_presupuesto'],
                $datos['id_articulo'] ?? null,
                $datos['id_linea_padre'] ?? null,
                $datos['id_ubicacion'] ?? null,
                $datos['id_coeficiente'] ?? null,
                $datos['id_impuesto'] ?? null,
                $datos['numero_linea_ppto'],
                $datos['tipo_linea_ppto'] ?? 'articulo',
                $datos['nivel_jerarquia'] ?? 0,
                $datos['orden_linea_ppto'] ?? 0,
                $datos['codigo_linea_ppto'] ?? null,
                $datos['descripcion_linea_ppto'],
                $datos['cantidad_linea_ppto'] ?? 1.00,
                $datos['precio_unitario_linea_ppto'] ?? 0.00,
                $datos['descuento_linea_ppto'] ?? 0.00,
                $datos['jornadas_linea_ppto'] ?? null,
                $datos['valor_coeficiente_linea_ppto'] ?? null,
                $datos['porcentaje_iva_linea_ppto'] ?? 21.00,
                $datos['fecha_montaje_linea_ppto'] ?? null,
                $datos['fecha_desmontaje_linea_ppto'] ?? null,
                $datos['fecha_inicio_linea_ppto'] ?? null,
                $datos['fecha_fin_linea_ppto'] ?? null,
                $datos['observaciones_linea_ppto'] ?? null,
                $datos['mostrar_obs_articulo_linea_ppto'] ?? 1,
                $datos['ocultar_detalle_kit_linea_ppto'] ?? 0,
                $datos['mostrar_en_presupuesto'] ?? 1,
                $datos['es_opcional'] ?? 0,
                $datos['activo_linea_ppto'] ?? 1
            ]);

            $id_linea = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                $_SESSION['usuario'] ?? 'system',
                'LineaPresupuesto',
                'insert_linea',
                "Línea insertada ID: {$id_linea}",
                'success'
            );

            return $id_linea;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                $_SESSION['usuario'] ?? 'system',
                'LineaPresupuesto',
                'insert_linea',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 5: Actualizar línea (usando TABLA)
    // =========================================================
    /**
     * Actualiza una línea de presupuesto existente
     * 
     * @param int $id_linea_ppto ID de la línea
     * @param array $datos Array asociativo con los datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function update_linea($id_linea_ppto, $datos)
    {
        try {
            $sql = "UPDATE linea_presupuesto SET
                id_articulo = ?,
                id_linea_padre = ?,
                id_ubicacion = ?,
                id_coeficiente = ?,
                id_impuesto = ?,
                numero_linea_ppto = ?,
                tipo_linea_ppto = ?,
                nivel_jerarquia = ?,
                orden_linea_ppto = ?,
                codigo_linea_ppto = ?,
                descripcion_linea_ppto = ?,
                cantidad_linea_ppto = ?,
                precio_unitario_linea_ppto = ?,
                descuento_linea_ppto = ?,
                jornadas_linea_ppto = ?,
                valor_coeficiente_linea_ppto = ?,
                porcentaje_iva_linea_ppto = ?,
                fecha_montaje_linea_ppto = ?,
                fecha_desmontaje_linea_ppto = ?,
                fecha_inicio_linea_ppto = ?,
                fecha_fin_linea_ppto = ?,
                observaciones_linea_ppto = ?,
                mostrar_obs_articulo_linea_ppto = ?,
                ocultar_detalle_kit_linea_ppto = ?,
                mostrar_en_presupuesto = ?,
                es_opcional = ?
                WHERE id_linea_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            
            $resultado = $stmt->execute([
                $datos['id_articulo'] ?? null,
                $datos['id_linea_padre'] ?? null,
                $datos['id_ubicacion'] ?? null,
                $datos['id_coeficiente'] ?? null,
                $datos['id_impuesto'] ?? null,
                $datos['numero_linea_ppto'],
                $datos['tipo_linea_ppto'] ?? 'articulo',
                $datos['nivel_jerarquia'] ?? 0,
                $datos['orden_linea_ppto'] ?? 0,
                $datos['codigo_linea_ppto'] ?? null,
                $datos['descripcion_linea_ppto'],
                $datos['cantidad_linea_ppto'] ?? 1.00,
                $datos['precio_unitario_linea_ppto'] ?? 0.00,
                $datos['descuento_linea_ppto'] ?? 0.00,
                $datos['jornadas_linea_ppto'] ?? null,
                $datos['valor_coeficiente_linea_ppto'] ?? null,
                $datos['porcentaje_iva_linea_ppto'] ?? 21.00,
                $datos['fecha_montaje_linea_ppto'] ?? null,
                $datos['fecha_desmontaje_linea_ppto'] ?? null,
                $datos['fecha_inicio_linea_ppto'] ?? null,
                $datos['fecha_fin_linea_ppto'] ?? null,
                $datos['observaciones_linea_ppto'] ?? null,
                $datos['mostrar_obs_articulo_linea_ppto'] ?? 1,
                $datos['ocultar_detalle_kit_linea_ppto'] ?? 0,
                $datos['mostrar_en_presupuesto'] ?? 1,
                $datos['es_opcional'] ?? 0,
                $id_linea_ppto
            ]);

            if ($resultado) {
                $this->registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'system',
                    'LineaPresupuesto',
                    'update_linea',
                    "Línea actualizada ID: {$id_linea_ppto}",
                    'success'
                );
            }

            return $resultado;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                $_SESSION['usuario'] ?? 'system',
                'LineaPresupuesto',
                'update_linea',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 6: Desactivar línea (soft delete usando TABLA)
    // =========================================================
    /**
     * Desactiva una línea de presupuesto (no la elimina físicamente)
     * 
     * @param int $id_linea_ppto ID de la línea
     * @return bool True si se desactivó correctamente
     */
    public function delete_lineaxid($id_linea_ppto)
    {
        try {
            $sql = "UPDATE linea_presupuesto 
                    SET activo_linea_ppto = 0 
                    WHERE id_linea_ppto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $resultado = $stmt->execute([$id_linea_ppto]);

            if ($resultado) {
                $this->registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'system',
                    'LineaPresupuesto',
                    'delete_lineaxid',
                    "Línea desactivada ID: {$id_linea_ppto}",
                    'warning'
                );
            }

            return $resultado;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                $_SESSION['usuario'] ?? 'system',
                'LineaPresupuesto',
                'delete_lineaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 7: Activar línea (usando TABLA)
    // =========================================================
    /**
     * Reactiva una línea de presupuesto previamente desactivada
     * 
     * @param int $id_linea_ppto ID de la línea
     * @return bool True si se activó correctamente
     */
    public function activar_lineaxid($id_linea_ppto)
    {
        try {
            $sql = "UPDATE linea_presupuesto 
                    SET activo_linea_ppto = 1 
                    WHERE id_linea_ppto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $resultado = $stmt->execute([$id_linea_ppto]);

            if ($resultado) {
                $this->registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'system',
                    'LineaPresupuesto',
                    'activar_lineaxid',
                    "Línea activada ID: {$id_linea_ppto}",
                    'success'
                );
            }

            return $resultado;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                $_SESSION['usuario'] ?? 'system',
                'LineaPresupuesto',
                'activar_lineaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =========================================================
    // MÉTODO 8: Validar totales del presupuesto
    // =========================================================
    /**
     * Valida que los totales cuadren correctamente
     * 
     * @param int $id_version_presupuesto ID de la versión
     * @return array Array con resultado de validación
     */
    public function validar_totales($id_version_presupuesto)
    {
        try {
            $totales = $this->get_totales_version($id_version_presupuesto);
            
            if (!$totales) {
                return [
                    'valido' => false,
                    'mensaje' => 'No se pudieron obtener los totales'
                ];
            }

            // Sumar todas las bases
            $suma_bases = $totales['base_iva_21'] + 
                          $totales['base_iva_10'] + 
                          $totales['base_iva_4'] + 
                          $totales['base_iva_0'] +
                          $totales['base_iva_otros'];
            
            // Tolerancia de 1 céntimo por redondeos
            $diferencia = abs($suma_bases - $totales['total_base_imponible']);
            
            if ($diferencia > 0.01) {
                $this->registro->registrarActividad(
                    'system',
                    'LineaPresupuesto',
                    'validar_totales',
                    "ERROR: Totales no cuadran en versión {$id_version_presupuesto}. Diferencia: {$diferencia}",
                    'error'
                );
                
                return [
                    'valido' => false,
                    'mensaje' => 'Los totales no cuadran',
                    'diferencia' => $diferencia
                ];
            }
            
            return [
                'valido' => true,
                'mensaje' => 'Totales correctos'
            ];

        } catch (Exception $e) {
            return [
                'valido' => false,
                'mensaje' => 'Error en validación: ' . $e->getMessage()
            ];
        }
    }
}
?>