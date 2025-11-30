<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

// CREATE TABLE documento_elemento (
//     id_documento_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     id_elemento INT UNSIGNED NOT NULL,
//     descripcion_documento_elemento TEXT,
//     tipo_documento_elemento VARCHAR(100),
//     archivo_documento VARCHAR(500) NOT NULL,
//     privado_documento BOOLEAN DEFAULT FALSE,
//     observaciones_documento TEXT,
//     activo_documento BOOLEAN DEFAULT TRUE,
//     created_at_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

class Documento_elemento
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }

    public function get_documento_elemento()
    {
        try {
            $sql = "SELECT * FROM documento_elemento";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'get_documento_elemento',
                "Error al listar los documentos de elementos: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_documento_elemento_disponible()
    {
        try {
            $sql = "SELECT * FROM documento_elemento WHERE activo_documento = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'get_documento_elemento_disponible',
                "Error al listar los documentos de elementos disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_documento_elementoxid($id_documento_elemento)
    {
        try {
            $sql = "SELECT * FROM documento_elemento WHERE id_documento_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_elemento, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'get_documento_elementoxid',
                "Error al mostrar el documento de elemento {$id_documento_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function get_documentos_por_elemento($id_elemento)
    {
        try {
            $sql = "SELECT * FROM documento_elemento WHERE id_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'get_documentos_por_elemento',
                "Error al listar los documentos del elemento {$id_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_documento_elementoxid($id_documento_elemento)
    {
        try {
            $sql = "UPDATE documento_elemento SET activo_documento = 0 WHERE id_documento_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'Desactivar',
                "Se desactivó el documento de elemento con ID: $id_documento_elemento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'delete_documento_elementoxid',
                "Error al desactivar el documento de elemento {$id_documento_elemento}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_documento_elementoxid($id_documento_elemento)
    {
        try {
            $sql = "UPDATE documento_elemento SET activo_documento = 1 WHERE id_documento_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'Activar',
                "Se activó el documento de elemento con ID: $id_documento_elemento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'activar_documento_elementoxid',
                "Error al activar el documento de elemento {$id_documento_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_documento_elemento($id_elemento, $archivo_documento, $descripcion_documento_elemento = null, $tipo_documento_elemento = null, $privado_documento = false, $observaciones_documento = null)
    {
        try {
            $sql = "INSERT INTO documento_elemento (id_elemento, descripcion_documento_elemento, tipo_documento_elemento, archivo_documento, privado_documento, observaciones_documento, activo_documento, created_at_documento, updated_at_documento) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $descripcion_documento_elemento, $descripcion_documento_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $tipo_documento_elemento, $tipo_documento_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(4, $archivo_documento, PDO::PARAM_STR);
            $stmt->bindValue(5, $privado_documento, PDO::PARAM_BOOL);
            $stmt->bindValue(6, $observaciones_documento, $observaciones_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'Insertar',
                "Se insertó el documento de elemento con ID: $idInsert",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            // Registrar error con más detalles
            error_log("Error PDO en insert_documento_elemento: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'insert_documento_elemento',
                "Error al insertar el documento de elemento: " . $e->getMessage() . " | Code: " . $e->getCode(),
                'error'
            );

            return false;
        }
    }

    public function update_documento_elemento($id_documento_elemento, $id_elemento, $archivo_documento, $descripcion_documento_elemento = null, $tipo_documento_elemento = null, $privado_documento = false, $observaciones_documento = null)
    {
        try {
            $sql = "UPDATE documento_elemento SET id_elemento = ?, descripcion_documento_elemento = ?, tipo_documento_elemento = ?, archivo_documento = ?, privado_documento = ?, observaciones_documento = ?, updated_at_documento = NOW() WHERE id_documento_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $descripcion_documento_elemento, $descripcion_documento_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $tipo_documento_elemento, $tipo_documento_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(4, $archivo_documento, PDO::PARAM_STR);
            $stmt->bindValue(5, $privado_documento, PDO::PARAM_BOOL);
            $stmt->bindValue(6, $observaciones_documento, $observaciones_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(7, $id_documento_elemento, PDO::PARAM_INT);

            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'Actualizar',
                "Se actualizó el documento de elemento con ID: $id_documento_elemento",
                'info'
            );

            return true;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento_elemento',
                'update_documento_elemento',
                "Error al actualizar el documento de elemento: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

}
