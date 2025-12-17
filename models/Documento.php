<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

// CREATE TABLE documento (
//     id_documento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     titulo_documento VARCHAR(255) NOT NULL,
//     descripcion_documento TEXT,
//     ruta_documento VARCHAR(500) NOT NULL COMMENT 'Ruta relativa del archivo PDF',
//     id_tipo_documento_documento INT UNSIGNED NOT NULL,
//     fecha_publicacion_documento DATE,
//     activo_documento BOOLEAN DEFAULT TRUE,
//     fecha_creacion_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     fecha_modificacion_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     
//     FOREIGN KEY (id_tipo_documento_documento) REFERENCES tipo_documento(id_tipo_documento)
//         ON DELETE RESTRICT ON UPDATE CASCADE,
//     INDEX idx_tipo_documento(id_tipo_documento_documento),
//     INDEX idx_activo_documento(activo_documento)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

class Documento
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }

    public function get_documento()
    {
        try {
            $sql = "SELECT d.*, td.codigo_tipo_documento, td.nombre_tipo_documento 
                    FROM documento d
                    LEFT JOIN tipo_documento td ON d.id_tipo_documento_documento = td.id_tipo_documento
                    ORDER BY td.nombre_tipo_documento ASC, d.titulo_documento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'get_documento',
                "Error al listar los documentos: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_documento_disponible()
    {
        try {
            $sql = "SELECT d.*, td.codigo_tipo_documento, td.nombre_tipo_documento 
                    FROM documento d
                    LEFT JOIN tipo_documento td ON d.id_tipo_documento_documento = td.id_tipo_documento
                    WHERE d.activo_documento = 1
                    ORDER BY td.nombre_tipo_documento ASC, d.titulo_documento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'get_documento_disponible',
                "Error al listar los documentos disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_documentoxid($id_documento)
    {
        try {
            $sql = "SELECT * FROM documento WHERE id_documento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'get_documentoxid',
                "Error al mostrar el documento {$id_documento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function get_documentos_por_tipo($id_tipo_documento_documento)
    {
        try {
            $sql = "SELECT d.*, td.codigo_tipo_documento, td.nombre_tipo_documento 
                    FROM documento d
                    LEFT JOIN tipo_documento td ON d.id_tipo_documento_documento = td.id_tipo_documento
                    WHERE d.id_tipo_documento_documento = ?
                    ORDER BY d.titulo_documento ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_tipo_documento_documento, PDO::PARAM_INT);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'get_documentos_por_tipo',
                "Error al listar los documentos del tipo {$id_tipo_documento_documento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_documentoxid($id_documento)
    {
        try {
            $sql = "UPDATE documento SET activo_documento = 0 WHERE id_documento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'Desactivar',
                "Se desactivó el documento con ID: $id_documento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'delete_documentoxid',
                "Error al desactivar el documento {$id_documento}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_documentoxid($id_documento)
    {
        try {
            $sql = "UPDATE documento SET activo_documento = 1 WHERE id_documento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'Activar',
                "Se activó el documento con ID: $id_documento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'activar_documentoxid',
                "Error al activar el documento {$id_documento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_documento($titulo_documento, $ruta_documento, $id_tipo_documento_documento, $descripcion_documento = null, $fecha_publicacion_documento = null)
    {
        try {
            $sql = "INSERT INTO documento (titulo_documento, descripcion_documento, ruta_documento, id_tipo_documento_documento, fecha_publicacion_documento, activo_documento, fecha_creacion_documento, fecha_modificacion_documento) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $titulo_documento, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion_documento, $descripcion_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $ruta_documento, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_tipo_documento_documento, PDO::PARAM_INT);
            $stmt->bindValue(5, $fecha_publicacion_documento, $fecha_publicacion_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'Insertar',
                "Se insertó el documento con ID: $idInsert",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            // Registrar error con más detalles
            error_log("Error PDO en insert_documento: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'insert_documento',
                "Error al insertar el documento: " . $e->getMessage() . " | Code: " . $e->getCode(),
                'error'
            );

            return false;
        }
    }

    public function update_documento($id_documento, $titulo_documento, $ruta_documento, $id_tipo_documento_documento, $descripcion_documento = null, $fecha_publicacion_documento = null)
    {
        try {
            $sql = "UPDATE documento SET titulo_documento = ?, descripcion_documento = ?, ruta_documento = ?, id_tipo_documento_documento = ?, fecha_publicacion_documento = ?, fecha_modificacion_documento = NOW() WHERE id_documento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $titulo_documento, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion_documento, $descripcion_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $ruta_documento, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_tipo_documento_documento, PDO::PARAM_INT);
            $stmt->bindValue(5, $fecha_publicacion_documento, $fecha_publicacion_documento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(6, $id_documento, PDO::PARAM_INT);

            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'Actualizar',
                "Se actualizó el documento con ID: $id_documento",
                'info'
            );

            return true;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Documento',
                'update_documento',
                "Error al actualizar el documento: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

}
