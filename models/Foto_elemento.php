<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

// CREATE TABLE foto_elemento (
//     id_foto_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     id_elemento INT UNSIGNED NOT NULL,
//     descripcion_foto_elemento TEXT,
//     archivo_foto VARCHAR(500) NOT NULL,
//     privado_foto BOOLEAN DEFAULT FALSE,
//     observaciones_foto TEXT,
//     activo_foto BOOLEAN DEFAULT TRUE,
//     created_at_foto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_foto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

class Foto_elemento
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }

    public function get_foto_elemento()
    {
        try {
            $sql = "SELECT * FROM foto_elemento";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'get_foto_elemento',
                "Error al listar las fotos de elementos: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_foto_elemento_disponible()
    {
        try {
            $sql = "SELECT * FROM foto_elemento WHERE activo_foto = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'get_foto_elemento_disponible',
                "Error al listar las fotos de elementos disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_foto_elementoxid($id_foto_elemento)
    {
        try {
            $sql = "SELECT * FROM foto_elemento WHERE id_foto_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_foto_elemento, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'get_foto_elementoxid',
                "Error al mostrar la foto de elemento {$id_foto_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function get_fotos_por_elemento($id_elemento)
    {
        try {
            $sql = "SELECT * FROM foto_elemento WHERE id_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'get_fotos_por_elemento',
                "Error al listar las fotos del elemento {$id_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_foto_elementoxid($id_foto_elemento)
    {
        try {
            $sql = "UPDATE foto_elemento SET activo_foto = 0 WHERE id_foto_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_foto_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'Desactivar',
                "Se desactivó la foto de elemento con ID: $id_foto_elemento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'delete_foto_elementoxid',
                "Error al desactivar la foto de elemento {$id_foto_elemento}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_foto_elementoxid($id_foto_elemento)
    {
        try {
            $sql = "UPDATE foto_elemento SET activo_foto = 1 WHERE id_foto_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_foto_elemento, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'Activar',
                "Se activó la foto de elemento con ID: $id_foto_elemento",
                'info'
            );

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'activar_foto_elementoxid',
                "Error al activar la foto de elemento {$id_foto_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_foto_elemento($id_elemento, $archivo_foto, $descripcion_foto_elemento = null, $privado_foto = false, $observaciones_foto = null)
    {
        try {
            $sql = "INSERT INTO foto_elemento (id_elemento, descripcion_foto_elemento, archivo_foto, privado_foto, observaciones_foto, activo_foto, created_at_foto, updated_at_foto) 
                    VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $descripcion_foto_elemento, $descripcion_foto_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $archivo_foto, PDO::PARAM_STR);
            $stmt->bindValue(4, $privado_foto, PDO::PARAM_BOOL);
            $stmt->bindValue(5, $observaciones_foto, $observaciones_foto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'Insertar',
                "Se insertó la foto de elemento con ID: $idInsert",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            // Registrar error con más detalles
            error_log("Error PDO en insert_foto_elemento: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'insert_foto_elemento',
                "Error al insertar la foto de elemento: " . $e->getMessage() . " | Code: " . $e->getCode(),
                'error'
            );

            return false;
        }
    }

    public function update_foto_elemento($id_foto_elemento, $id_elemento, $archivo_foto, $descripcion_foto_elemento = null, $privado_foto = false, $observaciones_foto = null)
    {
        try {
            $sql = "UPDATE foto_elemento SET id_elemento = ?, descripcion_foto_elemento = ?, archivo_foto = ?, privado_foto = ?, observaciones_foto = ?, updated_at_foto = NOW() WHERE id_foto_elemento = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_elemento, PDO::PARAM_INT);
            $stmt->bindValue(2, $descripcion_foto_elemento, $descripcion_foto_elemento === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(3, $archivo_foto, PDO::PARAM_STR);
            $stmt->bindValue(4, $privado_foto, PDO::PARAM_BOOL);
            $stmt->bindValue(5, $observaciones_foto, $observaciones_foto === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(6, $id_foto_elemento, PDO::PARAM_INT);

            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'Actualizar',
                "Se actualizó la foto de elemento con ID: $id_foto_elemento",
                'info'
            );

            return true;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Foto_elemento',
                'update_foto_elemento',
                "Error al actualizar la foto de elemento: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

}
