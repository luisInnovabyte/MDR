<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

/**
 * Clase TipoDocumento
 * 
 * Modelo para gestionar los tipos de documentos del gestor documental para técnicos.
 * Maneja operaciones CRUD sobre la tabla tipo_documento.
 * 
 * @author MDR ERP Manager
 * @version 1.0
 * @date 2024-12-17
 */
class TipoDocumento
{
    private $conexion;
    private $registro;

    /**
     * Constructor
     * Inicializa la conexión PDO y el sistema de registro de actividad
     */
    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'TipoDocumento',
                '__construct',
                "Error configurando zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Listar todos los tipos de documento (activos e inactivos)
     * 
     * @return array Array con todos los tipos de documento
     */
    public function get_tipos_documento()
    {
        try {
            $sql = "SELECT 
                        id_tipo_documento,
                        codigo_tipo_documento,
                        nombre_tipo_documento,
                        descripcion_tipo_documento,
                        activo_tipo_documento,
                        created_at_tipo_documento,
                        updated_at_tipo_documento
                    FROM tipo_documento 
                    ORDER BY nombre_tipo_documento ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'get_tipos_documento',
                "Error al listar tipos de documento: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Listar solo tipos de documento activos
     * 
     * @return array Array con los tipos de documento disponibles (activos)
     */
    public function get_tipos_documento_disponibles()
    {
        try {
            $sql = "SELECT 
                        id_tipo_documento,
                        codigo_tipo_documento,
                        nombre_tipo_documento,
                        descripcion_tipo_documento,
                        activo_tipo_documento,
                        created_at_tipo_documento,
                        updated_at_tipo_documento
                    FROM tipo_documento 
                    WHERE activo_tipo_documento = 1
                    ORDER BY nombre_tipo_documento ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'get_tipos_documento_disponibles',
                "Error al listar tipos de documento disponibles: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Obtener un tipo de documento específico por ID
     * 
     * @param int $id_tipo_documento ID del tipo de documento
     * @return array|null Datos del tipo de documento o null si no existe
     */
    public function get_tipo_documentoxid($id_tipo_documento)
    {
        try {
            $sql = "SELECT 
                        id_tipo_documento,
                        codigo_tipo_documento,
                        nombre_tipo_documento,
                        descripcion_tipo_documento,
                        activo_tipo_documento,
                        created_at_tipo_documento,
                        updated_at_tipo_documento
                    FROM tipo_documento 
                    WHERE id_tipo_documento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_tipo_documento, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'get_tipo_documentoxid',
                "Error al obtener tipo de documento ID $id_tipo_documento: " . $e->getMessage(),
                'error'
            );
            return null;
        }
    }

    /**
     * Insertar un nuevo tipo de documento
     * 
     * @param string $codigo_tipo_documento Código alfanumérico único
     * @param string $nombre_tipo_documento Nombre descriptivo
     * @param string|null $descripcion_tipo_documento Descripción detallada (opcional)
     * @return int|false ID del nuevo registro o false si falla
     */
    public function insert_tipo_documento(
        $codigo_tipo_documento,
        $nombre_tipo_documento,
        $descripcion_tipo_documento = null
    ) {
        try {
            $sql = "INSERT INTO tipo_documento (
                        codigo_tipo_documento,
                        nombre_tipo_documento,
                        descripcion_tipo_documento,
                        activo_tipo_documento
                    ) VALUES (?, ?, ?, 1)";
            
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindValue(1, trim($codigo_tipo_documento), PDO::PARAM_STR);
            $stmt->bindValue(2, trim($nombre_tipo_documento), PDO::PARAM_STR);
            
            // Validar campo opcional
            if (!empty($descripcion_tipo_documento)) {
                $stmt->bindValue(3, trim($descripcion_tipo_documento), PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            $id_insertado = $this->conexion->lastInsertId();
            
            // Registrar actividad exitosa
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'insert_tipo_documento',
                "Tipo de documento creado exitosamente: $nombre_tipo_documento (ID: $id_insertado)",
                'success'
            );
            
            return $id_insertado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'insert_tipo_documento',
                "Error al insertar tipo de documento: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Actualizar un tipo de documento existente
     * 
     * @param int $id_tipo_documento ID del tipo de documento a actualizar
     * @param string $codigo_tipo_documento Código alfanumérico único
     * @param string $nombre_tipo_documento Nombre descriptivo
     * @param string|null $descripcion_tipo_documento Descripción detallada (opcional)
     * @return int|false Número de filas afectadas o false si falla
     */
    public function update_tipo_documento(
        $id_tipo_documento,
        $codigo_tipo_documento,
        $nombre_tipo_documento,
        $descripcion_tipo_documento = null
    ) {
        try {
            $sql = "UPDATE tipo_documento SET 
                        codigo_tipo_documento = ?,
                        nombre_tipo_documento = ?,
                        descripcion_tipo_documento = ?
                    WHERE id_tipo_documento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindValue(1, trim($codigo_tipo_documento), PDO::PARAM_STR);
            $stmt->bindValue(2, trim($nombre_tipo_documento), PDO::PARAM_STR);
            
            // Validar campo opcional
            if (!empty($descripcion_tipo_documento)) {
                $stmt->bindValue(3, trim($descripcion_tipo_documento), PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_tipo_documento, PDO::PARAM_INT);
            
            $stmt->execute();
            $filas_afectadas = $stmt->rowCount();
            
            // Registrar actividad exitosa
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'update_tipo_documento',
                "Tipo de documento actualizado: $nombre_tipo_documento (ID: $id_tipo_documento)",
                'success'
            );
            
            return $filas_afectadas;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'update_tipo_documento',
                "Error al actualizar tipo de documento ID $id_tipo_documento: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Desactivar (eliminar lógicamente) un tipo de documento
     * Cambia activo_tipo_documento de 1 a 0
     * 
     * @param int $id_tipo_documento ID del tipo de documento a desactivar
     * @return bool true si se desactivó correctamente, false en caso contrario
     */
    public function delete_tipo_documentoxid($id_tipo_documento)
    {
        try {
            $sql = "UPDATE tipo_documento 
                    SET activo_tipo_documento = 0 
                    WHERE id_tipo_documento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_tipo_documento, PDO::PARAM_INT);
            $stmt->execute();
            
            $eliminado = $stmt->rowCount() > 0;
            
            if ($eliminado) {
                $this->registro->registrarActividad(
                    'admin',
                    'TipoDocumento',
                    'delete_tipo_documentoxid',
                    "Tipo de documento desactivado ID: $id_tipo_documento",
                    'success'
                );
            }
            
            return $eliminado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'delete_tipo_documentoxid',
                "Error al desactivar tipo de documento ID $id_tipo_documento: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Activar un tipo de documento previamente desactivado
     * Cambia activo_tipo_documento de 0 a 1
     * 
     * @param int $id_tipo_documento ID del tipo de documento a activar
     * @return bool true si se activó correctamente, false en caso contrario
     */
    public function activar_tipo_documentoxid($id_tipo_documento)
    {
        try {
            $sql = "UPDATE tipo_documento 
                    SET activo_tipo_documento = 1 
                    WHERE id_tipo_documento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_tipo_documento, PDO::PARAM_INT);
            $stmt->execute();
            
            $activado = $stmt->rowCount() > 0;
            
            if ($activado) {
                $this->registro->registrarActividad(
                    'admin',
                    'TipoDocumento',
                    'activar_tipo_documentoxid',
                    "Tipo de documento activado ID: $id_tipo_documento",
                    'success'
                );
            }
            
            return $activado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'TipoDocumento',
                'activar_tipo_documentoxid',
                "Error al activar tipo de documento ID $id_tipo_documento: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Verificar si existe un tipo de documento con el mismo código
     * Útil para validar duplicados antes de insertar/actualizar
     * 
     * @param string $codigo_tipo_documento Código a verificar
     * @param int|null $id_tipo_documento ID a excluir de la búsqueda (para updates)
     * @return array ['existe' => bool, 'error' => string|null]
     */
    public function verificarTipoDocumento($codigo_tipo_documento, $id_tipo_documento = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total 
                    FROM tipo_documento 
                    WHERE LOWER(codigo_tipo_documento) = LOWER(?)";
            
            $params = [trim($codigo_tipo_documento)];
            
            // Si se proporciona ID, excluirlo de la búsqueda (útil para updates)
            if (!empty($id_tipo_documento)) {
                $sql .= " AND id_tipo_documento != ?";
                $params[] = $id_tipo_documento;
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
                'TipoDocumento',
                'verificarTipoDocumento',
                "Error al verificar tipo de documento: " . $e->getMessage(),
                'error'
            );
            
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>