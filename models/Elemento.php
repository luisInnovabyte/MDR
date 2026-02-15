<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Elemento
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            // Si no se puede establecer la zona horaria, registrar error pero continuar
            $this->registro->registrarActividad(
                'system',
                'Elemento',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_elementos()
    {
        try {
            $sql = "SELECT * FROM vista_elementos_completa 
                    ORDER BY codigo_articulo ASC, codigo_elemento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_elementos',
                "Error al listar elementos: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_elementos_by_articulo($id_articulo)
    {
        try {
            $sql = "SELECT * FROM vista_elementos_completa 
                    WHERE id_articulo = ? 
                    ORDER BY codigo_elemento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_elementos_by_articulo',
                "Error al listar elementos del artículo {$id_articulo}: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_elementos_by_estado($id_estado_elemento)
    {
        try {
            $sql = "SELECT * FROM vista_elementos_completa 
                    WHERE id_estado_elemento = ? 
                    ORDER BY codigo_articulo ASC, codigo_elemento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_estado_elemento, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_elementos_by_estado',
                "Error al listar elementos del estado {$id_estado_elemento}: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_elementos_disponibles()
    {
        try {
            $sql = "SELECT * FROM vista_elementos_completa 
                    WHERE permite_alquiler_estado_elemento = 1 
                    ORDER BY codigo_articulo ASC, codigo_elemento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_elementos_disponibles',
                "Error al listar elementos disponibles: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_elementoxid($id_elemento)
    {
        try {
            // Usar vista_elementos_completa que ya incluye todos los campos calculados
            // Esta vista muestra TODOS los elementos (activos e inactivos)
            $sql = "SELECT * FROM vista_elementos_completa 
                    WHERE id_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_elementoxid',
                "Error al mostrar el elemento {$id_elemento}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function activar_elementoxid($id_elemento)
    {
        try {
            $sql = "UPDATE elemento SET activo_elemento = 1 WHERE id_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'Activar',
                "Se activó el elemento con ID: $id_elemento",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'activar_elementoxid',
                "Error al activar el elemento {$id_elemento}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function desactivar_elementoxid($id_elemento)
    {
        try {
            $sql = "UPDATE elemento SET activo_elemento = 0 WHERE id_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'Desactivar',
                "Se desactivó el elemento con ID: $id_elemento",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'desactivar_elementoxid',
                "Error al desactivar el elemento {$id_elemento}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function insert_elemento(
        $id_articulo_elemento, 
        $id_marca_elemento, 
        $modelo_elemento, 
        $codigo_barras_elemento, 
        $descripcion_elemento, 
        $numero_serie_elemento, 
        $id_estado_elemento, 
        $nave_elemento, 
        $pasillo_columna_elemento, 
        $altura_elemento,
        $peso_elemento = null,
        $fecha_compra_elemento, 
        $precio_compra_elemento, 
        $fecha_alta_elemento, 
        $fecha_fin_garantia_elemento, 
        $proximo_mantenimiento_elemento, 
        $observaciones_elemento,
        $es_propio_elemento = true,
        $id_proveedor_compra_elemento = null,
        $id_proveedor_alquiler_elemento = null,
        $precio_dia_alquiler_elemento = null,
        $id_forma_pago_alquiler_elemento = null,
        $observaciones_alquiler_elemento = null
    ) {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            // El código_elemento se genera automáticamente por el trigger trg_elemento_before_insert
            $sql = "INSERT INTO elemento ( 
                        id_articulo_elemento, 
                        id_marca_elemento, 
                        modelo_elemento, 
                        codigo_barras_elemento, 
                        descripcion_elemento, 
                        numero_serie_elemento, 
                        id_estado_elemento, 
                        nave_elemento, 
                        pasillo_columna_elemento, 
                        altura_elemento,
                        peso_elemento,
                        fecha_compra_elemento, 
                        precio_compra_elemento, 
                        fecha_alta_elemento, 
                        fecha_fin_garantia_elemento, 
                        proximo_mantenimiento_elemento, 
                        observaciones_elemento,
                        es_propio_elemento,
                        id_proveedor_compra_elemento,
                        id_proveedor_alquiler_elemento,
                        precio_dia_alquiler_elemento,
                        id_forma_pago_alquiler_elemento,
                        observaciones_alquiler_elemento, 
                        activo_elemento, 
                        created_at_elemento, 
                        updated_at_elemento
                    ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $id_articulo_elemento, PDO::PARAM_INT); 
            $stmt->bindValue(2, $id_marca_elemento ?: null, PDO::PARAM_INT); 
            $stmt->bindValue(3, $modelo_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(4, $codigo_barras_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(5, $descripcion_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(6, $numero_serie_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(7, $id_estado_elemento ?: 1, PDO::PARAM_INT); 
            $stmt->bindValue(8, $nave_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(9, $pasillo_columna_elemento, PDO::PARAM_STR); 
            $stmt->bindValue(10, $altura_elemento, PDO::PARAM_STR);
            $stmt->bindValue(11, $peso_elemento ?: null, PDO::PARAM_STR); // DECIMAL como string
            $stmt->bindValue(12, $fecha_compra_elemento ?: null, PDO::PARAM_STR); 
            $stmt->bindValue(13, $precio_compra_elemento ?: 0.00, PDO::PARAM_STR); 
            $stmt->bindValue(14, $fecha_alta_elemento ?: null, PDO::PARAM_STR); 
            $stmt->bindValue(15, $fecha_fin_garantia_elemento ?: null, PDO::PARAM_STR); 
            $stmt->bindValue(16, $proximo_mantenimiento_elemento ?: null, PDO::PARAM_STR); 
            $stmt->bindValue(17, $observaciones_elemento, PDO::PARAM_STR);
            $stmt->bindValue(18, (int)$es_propio_elemento, PDO::PARAM_INT);
            $stmt->bindValue(19, $id_proveedor_compra_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(20, $id_proveedor_alquiler_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(21, $precio_dia_alquiler_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(22, $id_forma_pago_alquiler_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(23, $observaciones_alquiler_elemento, PDO::PARAM_STR); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'Insertar',
                "Se insertó el elemento con ID: $idInsert",
                'info'
            );

            return $idInsert;
            
        } catch (PDOException $e) {
            throw new Exception("Error SQL en insert_elemento: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en insert_elemento: " . $e->getMessage());
        }
    }

    public function update_elemento(
        $id_elemento, 
        $id_articulo_elemento, 
        $id_marca_elemento, 
        $modelo_elemento, 
        $codigo_barras_elemento, 
        $descripcion_elemento, 
        $numero_serie_elemento, 
        $id_estado_elemento, 
        $nave_elemento, 
        $pasillo_columna_elemento, 
        $altura_elemento,
        $peso_elemento = null,
        $fecha_compra_elemento, 
        $precio_compra_elemento, 
        $fecha_alta_elemento, 
        $fecha_fin_garantia_elemento, 
        $proximo_mantenimiento_elemento, 
        $observaciones_elemento,
        $es_propio_elemento = true,
        $id_proveedor_compra_elemento = null,
        $id_proveedor_alquiler_elemento = null,
        $precio_dia_alquiler_elemento = null,
        $id_forma_pago_alquiler_elemento = null,
        $observaciones_alquiler_elemento = null
    ) {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE elemento SET 
                        id_articulo_elemento = ?, 
                        id_marca_elemento = ?, 
                        modelo_elemento = ?, 
                        codigo_barras_elemento = ?, 
                        descripcion_elemento = ?, 
                        numero_serie_elemento = ?, 
                        id_estado_elemento = ?, 
                        nave_elemento = ?, 
                        pasillo_columna_elemento = ?, 
                        altura_elemento = ?,
                        peso_elemento = ?,
                        fecha_compra_elemento = ?, 
                        precio_compra_elemento = ?, 
                        fecha_alta_elemento = ?, 
                        fecha_fin_garantia_elemento = ?, 
                        proximo_mantenimiento_elemento = ?, 
                        observaciones_elemento = ?,
                        es_propio_elemento = ?,
                        id_proveedor_compra_elemento = ?,
                        id_proveedor_alquiler_elemento = ?,
                        precio_dia_alquiler_elemento = ?,
                        id_forma_pago_alquiler_elemento = ?,
                        observaciones_alquiler_elemento = ?, 
                        updated_at_elemento = NOW() 
                    WHERE id_elemento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $id_articulo_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_marca_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(3, $modelo_elemento, PDO::PARAM_STR);
            $stmt->bindValue(4, $codigo_barras_elemento, PDO::PARAM_STR);
            $stmt->bindValue(5, $descripcion_elemento, PDO::PARAM_STR);
            $stmt->bindValue(6, $numero_serie_elemento, PDO::PARAM_STR);
            $stmt->bindValue(7, $id_estado_elemento, PDO::PARAM_INT);
            $stmt->bindValue(8, $nave_elemento, PDO::PARAM_STR);
            $stmt->bindValue(9, $pasillo_columna_elemento, PDO::PARAM_STR);
            $stmt->bindValue(10, $altura_elemento, PDO::PARAM_STR);
            $stmt->bindValue(11, $peso_elemento ?: null, PDO::PARAM_STR); // DECIMAL como string
            $stmt->bindValue(12, $fecha_compra_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(13, $precio_compra_elemento ?: 0.00, PDO::PARAM_STR);
            $stmt->bindValue(14, $fecha_alta_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(15, $fecha_fin_garantia_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(16, $proximo_mantenimiento_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(17, $observaciones_elemento, PDO::PARAM_STR);
            $stmt->bindValue(18, (int)$es_propio_elemento, PDO::PARAM_INT);
            $stmt->bindValue(19, $id_proveedor_compra_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(20, $id_proveedor_alquiler_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(21, $precio_dia_alquiler_elemento ?: null, PDO::PARAM_STR);
            $stmt->bindValue(22, $id_forma_pago_alquiler_elemento ?: null, PDO::PARAM_INT);
            $stmt->bindValue(23, $observaciones_alquiler_elemento, PDO::PARAM_STR);
            $stmt->bindValue(24, $id_elemento, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'Actualizar',
                "Se actualizó el elemento con ID: $id_elemento",
                'info'
            );

            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_elemento: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_elemento: " . $e->getMessage());
        }
    }

    public function verificarCodigoBarras($codigo_barras_elemento, $id_elemento = null)
    {
        try {
            if (empty($codigo_barras_elemento)) {
                return ['existe' => false];
            }

            $sql = "SELECT COUNT(*) AS total FROM elemento WHERE codigo_barras_elemento = ?";
            $params = [trim($codigo_barras_elemento)];
    
            if (!empty($id_elemento)) {
                $sql .= " AND id_elemento != ?";
                $params[] = $id_elemento;
            }
    
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Elemento',
                    'verificarCodigoBarras',
                    "Error al verificar código de barras: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function verificarNumeroSerie($numero_serie_elemento, $id_elemento = null)
    {
        try {
            if (empty($numero_serie_elemento)) {
                return ['existe' => false];
            }

            $sql = "SELECT COUNT(*) AS total FROM elemento WHERE numero_serie_elemento = ?";
            $params = [trim($numero_serie_elemento)];
    
            if (!empty($id_elemento)) {
                $sql .= " AND id_elemento != ?";
                $params[] = $id_elemento;
            }
    
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Elemento',
                    'verificarNumeroSerie',
                    "Error al verificar número de serie: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function cambiar_estado_elemento($id_elemento, $id_estado_elemento_nuevo)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE elemento SET 
                        id_estado_elemento = ?, 
                        updated_at_elemento = NOW() 
                    WHERE id_elemento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_estado_elemento_nuevo, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_elemento, PDO::PARAM_INT);
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar el cambio de estado: " . $errorInfo[2]);
            }

            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'Cambiar Estado',
                "Se cambió el estado del elemento ID: $id_elemento al estado ID: $id_estado_elemento_nuevo",
                'info'
            );

            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception("Error SQL en cambiar_estado_elemento: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en cambiar_estado_elemento: " . $e->getMessage());
        }
    }

    public function getWarrantyEvents($month, $year)
    {
        try {
            // Verificar primero si la vista existe, sino usar consulta directa
            $sql = "SELECT 
                        e.id_elemento,
                        e.codigo_elemento,
                        e.descripcion_elemento,
                        e.modelo_elemento,
                        e.numero_serie_elemento,
                        e.fecha_fin_garantia_elemento,
                        CASE 
                            WHEN e.fecha_fin_garantia_elemento < CURDATE() THEN 'Vencida'
                            WHEN e.fecha_fin_garantia_elemento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Por vencer'
                            WHEN e.fecha_fin_garantia_elemento > DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Vigente'
                            ELSE 'Sin garantía'
                        END AS estado_garantia_elemento,
                        a.nombre_articulo,
                        f.nombre_familia,
                        m.nombre_marca,
                        g.nombre_grupo,
                        e.nave_elemento,
                        e.pasillo_columna_elemento,
                        e.altura_elemento
                    FROM elemento e
                    INNER JOIN articulo a ON e.id_articulo_elemento = a.id_articulo
                    INNER JOIN familia f ON a.id_familia = f.id_familia
                    LEFT JOIN grupo_articulo g ON f.id_grupo = g.id_grupo
                    LEFT JOIN marca m ON e.id_marca_elemento = m.id_marca
                    WHERE e.fecha_fin_garantia_elemento IS NOT NULL
                    AND e.activo_elemento = 1
                    AND MONTH(e.fecha_fin_garantia_elemento) = :month
                    AND YEAR(e.fecha_fin_garantia_elemento) = :year
                    ORDER BY e.fecha_fin_garantia_elemento";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para debugging
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'getWarrantyEvents',
                "Consulta ejecutada: Mes=$month, Año=$year, Registros encontrados: " . count($resultados),
                "info"
            );
            
            return $resultados;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'getWarrantyEvents',
                "Error al obtener eventos de garantía: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function getMaintenanceEvents($month, $year)
    {
        try {
            // Consulta directa a las tablas con cálculo de estado
            $sql = "SELECT 
                        e.id_elemento,
                        e.codigo_elemento,
                        e.descripcion_elemento,
                        e.modelo_elemento,
                        e.numero_serie_elemento,
                        e.proximo_mantenimiento_elemento,
                        CASE 
                            WHEN e.proximo_mantenimiento_elemento < CURDATE() THEN 'Atrasado'
                            WHEN e.proximo_mantenimiento_elemento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY) THEN 'Próximo'
                            WHEN e.proximo_mantenimiento_elemento > DATE_ADD(CURDATE(), INTERVAL 15 DAY) THEN 'Al día'
                            ELSE 'Sin programar'
                        END AS estado_mantenimiento_elemento,
                        a.nombre_articulo,
                        f.nombre_familia,
                        m.nombre_marca,
                        g.nombre_grupo,
                        e.nave_elemento,
                        e.pasillo_columna_elemento,
                        e.altura_elemento
                    FROM elemento e
                    INNER JOIN articulo a ON e.id_articulo_elemento = a.id_articulo
                    INNER JOIN familia f ON a.id_familia = f.id_familia
                    LEFT JOIN grupo_articulo g ON f.id_grupo = g.id_grupo
                    LEFT JOIN marca m ON e.id_marca_elemento = m.id_marca
                    WHERE e.proximo_mantenimiento_elemento IS NOT NULL
                    AND e.activo_elemento = 1
                    AND MONTH(e.proximo_mantenimiento_elemento) = :month
                    AND YEAR(e.proximo_mantenimiento_elemento) = :year
                    ORDER BY e.proximo_mantenimiento_elemento";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':month', $month, PDO::PARAM_INT);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para debugging
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'getMaintenanceEvents',
                "Consulta ejecutada: Mes=$month, Año=$year, Registros encontrados: " . count($resultados),
                "info"
            );
            
            return $resultados;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'getMaintenanceEvents',
                "Error al obtener eventos de mantenimiento: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    /**
     * Actualizar el peso de un elemento
     * 
     * Actualiza el campo peso_elemento de un elemento específico.
     * Este campo es la base para calcular el peso promedio de los artículos
     * normales (media aritmética) o el peso total de los KITs (suma de componentes).
     * 
     * @param int $id_elemento ID del elemento a actualizar
     * @param float|null $peso_kg Peso en kilogramos (null para limpiar el peso)
     * @return bool true si se actualizó correctamente, false si hay error
     */
    public function update_peso_elemento($id_elemento, $peso_kg)
    {
        try {
            $sql = "UPDATE elemento 
                    SET peso_elemento = ?,
                        updated_at_elemento = NOW()
                    WHERE id_elemento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            // Si el peso es null o vacío, insertar NULL
            if ($peso_kg === null || $peso_kg === '') {
                $stmt->bindValue(1, null, PDO::PARAM_NULL);
            } else {
                // Validar que sea un número positivo
                $peso_kg = floatval($peso_kg);
                if ($peso_kg < 0) {
                    $this->registro->registrarActividad(
                        'admin',
                        'Elemento',
                        'update_peso_elemento',
                        "Intento de actualizar peso negativo para elemento ID $id_elemento: $peso_kg kg",
                        'warning'
                    );
                    return false;
                }
                $stmt->bindValue(1, $peso_kg, PDO::PARAM_STR); // PDO::PARAM_STR para DECIMAL
            }
            
            $stmt->bindValue(2, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();
            
            $filas_afectadas = $stmt->rowCount();
            
            if ($filas_afectadas > 0) {
                $peso_texto = ($peso_kg === null || $peso_kg === '') ? 'NULL' : "$peso_kg kg";
                $this->registro->registrarActividad(
                    'admin',
                    'Elemento',
                    'update_peso_elemento',
                    "Peso actualizado para elemento ID $id_elemento: $peso_texto",
                    'info'
                );
                return true;
            } else {
                $this->registro->registrarActividad(
                    'admin',
                    'Elemento',
                    'update_peso_elemento',
                    "No se encontró el elemento ID $id_elemento para actualizar peso",
                    'warning'
                );
                return false;
            }
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'update_peso_elemento',
                "Error al actualizar peso del elemento ID $id_elemento: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener peso calculado de un artículo
     * 
     * Consulta la vista vista_articulo_peso que calcula el peso de forma diferente
     * según el tipo de artículo:
     * - Artículos normales (es_kit=0): Media aritmética de pesos de elementos
     * - Artículos KIT (es_kit=1): Suma de pesos de componentes × cantidades
     * 
     * Retorna información sobre:
     * - Peso calculado del artículo en kg
     * - Método de cálculo utilizado ('MEDIA' o 'SUMA')
     * - Si tiene datos de peso disponibles
     * 
     * @param int $id_articulo ID del artículo
     * @return array|false Array con datos del peso o false si hay error
     *                     Keys: peso_articulo_kg, metodo_calculo_peso, tiene_datos_peso
     */
    public function get_peso_articulo($id_articulo)
    {
        try {
            $sql = "SELECT 
                        id_articulo,
                        peso_articulo_kg,
                        metodo_calculo_peso,
                        tiene_datos_peso,
                        es_kit_articulo
                    FROM vista_articulo_peso
                    WHERE id_articulo = ?
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $peso = $resultado['peso_articulo_kg'] ?? 'N/A';
                $metodo = $resultado['metodo_calculo_peso'] ?? 'N/A';
                $this->registro->registrarActividad(
                    'admin',
                    'Elemento',
                    'get_peso_articulo',
                    "Peso obtenido para artículo ID $id_articulo: $peso kg (método: $metodo)",
                    'info'
                );
            } else {
                // Si no hay resultado, retornar estructura con valores null
                $resultado = [
                    'id_articulo' => $id_articulo,
                    'peso_articulo_kg' => null,
                    'metodo_calculo_peso' => null,
                    'tiene_datos_peso' => false,
                    'es_kit_articulo' => null
                ];
                
                $this->registro->registrarActividad(
                    'admin',
                    'Elemento',
                    'get_peso_articulo',
                    "No se encontraron datos de peso para artículo ID $id_articulo",
                    'info'
                );
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Elemento',
                'get_peso_articulo',
                "Error al obtener peso del artículo ID $id_articulo: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }
