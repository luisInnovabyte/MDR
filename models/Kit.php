<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Kit
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
                'Kit',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Obtener todos los kits con información completa
     * Usa la vista vista_kit_completa
     */
    public function get_kits()
    {
        try {
            $sql = "SELECT * FROM vista_kit_completa 
                    ORDER BY codigo_articulo_maestro ASC, codigo_articulo_componente ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_kits',
                "Error al listar kits: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    /**
     * Obtener todos los componentes de un kit específico (artículo maestro)
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @return array Lista de componentes del kit
     */
    public function get_kits_by_articulo_maestro($id_articulo_maestro)
    {
        try {
            $sql = "SELECT * FROM vista_kit_completa 
                    WHERE id_articulo_maestro = ? 
                    ORDER BY nombre_articulo_componente ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_kits_by_articulo_maestro',
                "Error al listar componentes del kit {$id_articulo_maestro}: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    /**
     * Obtener información del artículo maestro (KIT)
     * @param int $id_articulo ID del artículo
     * @return array|false Datos del artículo o false si no existe
     */
    public function get_articulo_maestro($id_articulo)
    {
        try {
            $sql = "SELECT 
                        id_articulo,
                        codigo_articulo,
                        nombre_articulo,
                        name_articulo,
                        precio_alquiler_articulo,
                        es_kit_articulo,
                        activo_articulo
                    FROM articulo 
                    WHERE id_articulo = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_articulo_maestro',
                "Error al obtener artículo maestro {$id_articulo}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Obtener todos los kits activos
     */
    public function get_kits_disponibles()
    {
        try {
            $sql = "SELECT * FROM vista_kit_completa 
                    WHERE activo_kit = 1 
                    AND activo_articulo_maestro = 1
                    AND activo_articulo_componente = 1
                    ORDER BY codigo_articulo_maestro ASC, codigo_articulo_componente ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_kits_disponibles',
                "Error al listar kits disponibles: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    /**
     * Obtener un kit por su ID
     * @param int $id_kit ID del kit
     * @return array|false Datos del kit o false si no existe
     */
    public function get_kitxid($id_kit)
    {
        try {
            $sql = "SELECT * FROM vista_kit_completa 
                    WHERE id_kit = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_kit, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_kitxid',
                "Error al mostrar el kit {$id_kit}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Activar un kit (soft delete)
     * @param int $id_kit ID del kit
     * @return bool True si se activó correctamente
     */
    public function activar_kitxid($id_kit)
    {
        try {
            $sql = "UPDATE kit SET activo_kit = 1, updated_at_kit = NOW() WHERE id_kit = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_kit, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'Activar',
                "Se activó el kit con ID: $id_kit",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'activar_kitxid',
                "Error al activar el kit {$id_kit}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Desactivar un kit (soft delete)
     * @param int $id_kit ID del kit
     * @return bool True si se desactivó correctamente
     */
    public function desactivar_kitxid($id_kit)
    {
        try {
            $sql = "UPDATE kit SET activo_kit = 0, updated_at_kit = NOW() WHERE id_kit = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_kit, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'Desactivar',
                "Se desactivó el kit con ID: $id_kit",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'desactivar_kitxid',
                "Error al desactivar el kit {$id_kit}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Insertar un nuevo componente en un kit
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @param int $id_articulo_componente ID del artículo componente
     * @param int $cantidad_kit Cantidad del componente en el kit
     * @return int|false ID del kit insertado o false si hubo error
     */
    public function insert_kit(
        $id_articulo_maestro,
        $id_articulo_componente,
        $cantidad_kit
    ) {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            // El trigger trg_kit_before_insert validará:
            // - Auto-referencia
            // - Que el maestro sea un kit
            // - Que el componente NO sea un kit
            // - Cantidad positiva
            $sql = "INSERT INTO kit ( 
                        id_articulo_maestro, 
                        id_articulo_componente, 
                        cantidad_kit,
                        activo_kit, 
                        created_at_kit, 
                        updated_at_kit
                    ) 
                    VALUES (?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT); 
            $stmt->bindValue(2, $id_articulo_componente, PDO::PARAM_INT); 
            $stmt->bindValue(3, $cantidad_kit, PDO::PARAM_INT); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'Insertar',
                "Se insertó componente en kit con ID: $idInsert (Maestro: $id_articulo_maestro, Componente: $id_articulo_componente, Cantidad: $cantidad_kit)",
                'info'
            );

            return $idInsert;
            
        } catch (PDOException $e) {
            // Capturar errores de triggers
            $errorMsg = $e->getMessage();
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'insert_kit',
                "Error al insertar componente en kit: " . $errorMsg,
                "error"
            );
            throw new Exception("Error al insertar componente en kit: " . $errorMsg);
        } catch (Exception $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'insert_kit',
                "Error general al insertar componente en kit: " . $e->getMessage(),
                "error"
            );
            throw new Exception("Error general al insertar componente en kit: " . $e->getMessage());
        }
    }

    /**
     * Actualizar un componente de un kit
     * @param int $id_kit ID del kit
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @param int $id_articulo_componente ID del artículo componente
     * @param int $cantidad_kit Cantidad del componente en el kit
     * @return int|false Número de filas afectadas o false si hubo error
     */
    public function update_kit(
        $id_kit,
        $id_articulo_maestro,
        $id_articulo_componente,
        $cantidad_kit
    ) {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            // El trigger trg_kit_before_update validará las mismas reglas que el insert
            $sql = "UPDATE kit SET 
                        id_articulo_maestro = ?, 
                        id_articulo_componente = ?, 
                        cantidad_kit = ?,
                        updated_at_kit = NOW()
                    WHERE id_kit = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
            
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT); 
            $stmt->bindValue(2, $id_articulo_componente, PDO::PARAM_INT); 
            $stmt->bindValue(3, $cantidad_kit, PDO::PARAM_INT); 
            $stmt->bindValue(4, $id_kit, PDO::PARAM_INT); 
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
            
            $filasAfectadas = $stmt->rowCount();

            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'Actualizar',
                "Se actualizó el kit con ID: $id_kit (Maestro: $id_articulo_maestro, Componente: $id_articulo_componente, Cantidad: $cantidad_kit)",
                'info'
            );

            return $filasAfectadas;
            
        } catch (PDOException $e) {
            // Capturar errores de triggers
            $errorMsg = $e->getMessage();
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'update_kit',
                "Error al actualizar componente en kit: " . $errorMsg,
                "error"
            );
            throw new Exception("Error al actualizar componente en kit: " . $errorMsg);
        } catch (Exception $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'update_kit',
                "Error general al actualizar componente en kit: " . $e->getMessage(),
                "error"
            );
            throw new Exception("Error general al actualizar componente en kit: " . $e->getMessage());
        }
    }

    /**
     * Eliminar físicamente un componente de un kit
     * @param int $id_kit ID del kit
     * @return bool True si se eliminó correctamente
     */
    public function delete_kitxid($id_kit)
    {
        try {
            // En este caso hacemos DELETE físico porque los componentes de un kit
            // pueden eliminarse sin necesidad de mantener historial
            $sql = "DELETE FROM kit WHERE id_kit = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_kit, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'Eliminar',
                "Se eliminó el componente del kit con ID: $id_kit",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'delete_kitxid',
                "Error al eliminar el componente del kit {$id_kit}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Verificar si un componente ya existe en un kit
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @param int $id_articulo_componente ID del artículo componente
     * @param int|null $id_kit ID del kit (opcional, para excluir en actualizaciones)
     * @return array Resultado con 'existe' => true/false
     */
    public function verificar_componente_en_kit($id_articulo_maestro, $id_articulo_componente, $id_kit = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM kit 
                    WHERE id_articulo_maestro = ? 
                    AND id_articulo_componente = ?";
            $params = [$id_articulo_maestro, $id_articulo_componente];
            
            // Excluir el propio registro en edición
            if (!empty($id_kit)) {
                $sql .= " AND id_kit != ?";
                $params[] = $id_kit;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'verificar_componente_en_kit',
                "Error al verificar componente: " . $e->getMessage(),
                "error"
            );
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener artículos disponibles para agregar como componentes
     * Excluye el artículo maestro y los artículos que ya son kits
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @return array Lista de artículos disponibles
     */
    public function get_articulos_disponibles_para_kit($id_articulo_maestro)
    {
        try {
            $sql = "SELECT 
                        id_articulo,
                        codigo_articulo,
                        nombre_articulo,
                        name_articulo,
                        precio_alquiler_articulo
                    FROM articulo 
                    WHERE activo_articulo = 1 
                    AND es_kit_articulo = 0
                    AND id_articulo != ?
                    ORDER BY nombre_articulo ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_articulos_disponibles_para_kit',
                "Error al listar artículos disponibles: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    /**
     * Obtener el precio total calculado de un kit
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @return float|false Precio total del kit o false si hubo error
     */
    public function get_precio_total_kit($id_articulo_maestro)
    {
        try {
            $sql = "SELECT SUM(k.cantidad_kit * a.precio_alquiler_articulo) AS precio_total
                    FROM kit k
                    INNER JOIN articulo a ON k.id_articulo_componente = a.id_articulo
                    WHERE k.id_articulo_maestro = ?
                    AND k.activo_kit = 1
                    AND a.activo_articulo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['precio_total'] ?? 0.00;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_precio_total_kit',
                "Error al calcular precio total del kit {$id_articulo_maestro}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    /**
     * Obtener el número total de componentes de un kit
     * @param int $id_articulo_maestro ID del artículo maestro (el KIT)
     * @return int Número de componentes
     */
    public function get_total_componentes_kit($id_articulo_maestro)
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM kit
                    WHERE id_articulo_maestro = ?
                    AND activo_kit = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo_maestro, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] ?? 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Kit',
                'get_total_componentes_kit',
                "Error al contar componentes del kit {$id_articulo_maestro}: " . $e->getMessage(),
                "error"
            );
            return 0;
        }
    }
}

?>
