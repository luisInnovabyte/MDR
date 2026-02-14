<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Comerciales
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
    }

    public function get_comercial()
    {
        try {
            $sql = "SELECT c.*, u.email FROM comerciales c LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los productos
        
        } catch (PDOException $e) {
       
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los comerciales: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_comercialxid($idcomercial)
    {
        try {
            $sql = "SELECT c.*, u.email FROM comerciales c LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario where c.id_comercial=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcomercial, PDO::PARAM_INT);
            $stmt->execute();

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Esto para desarrollo
            die("Error al mostrar el comercial {$idcomercial}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_comercialxid($idcomercial)
    {
        try {
            $sql = "UPDATE comerciales set activo=0 where id_comercial=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcomercial, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // Esto para desarrollo
            die("Error al eliminar el comercial {$idcomercial}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_comercialxid($idcomercial)
    {
        try {
            $sql = "UPDATE comerciales set activo=1 where id_comercial=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcomercial, PDO::PARAM_INT);
            $stmt->execute();

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // Esto para desarrollo
            die("Error al activar el comercial {$idcomercial}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }
    
    public function insert_comercial($nombre, $apellidos, $movil, $telefono, $id_usuario)
    {
        try {
            // Insertar nuevo comercial con relación a un usuario (id_usuario)
            $sql = "INSERT INTO comerciales (nombre, apellidos, movil, telefono, id_usuario, activo) VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(2, $apellidos, PDO::PARAM_STR); // Se enlaza el valor de apellidos
            $stmt->bindValue(3, $movil, PDO::PARAM_STR); // Se enlaza el valor del movil
            $stmt->bindValue(4, $telefono, PDO::PARAM_STR); // Se enlaza el valor del telefono
            $stmt->bindValue(5, $id_usuario, PDO::PARAM_INT); // Se enlaza el ID del usuario
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            return true; // Devuelve true si la inserción fue exitosa
            //return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {

            die("Error al insertar el comercial: " . $e->getMessage());
        }
    }

    public function update_comercial($idcomercial, $nombre, $apellidos, $movil, $telefono, $id_usuario)
    {
        try {
            // Actualizar datos del comercial, incluyendo su relación con el usuario (id_usuario)
            $sql = "UPDATE comerciales SET nombre = ?, apellidos = ?, movil = ?, telefono = ?, id_usuario = ? WHERE id_comercial = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $apellidos, PDO::PARAM_STR);
            $stmt->bindValue(3, $movil, PDO::PARAM_STR);
            $stmt->bindValue(4, $telefono, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_usuario, PDO::PARAM_INT); // Se enlaza el ID del usuario
            $stmt->bindValue(6, $idcomercial, PDO::PARAM_INT);
            $stmt->execute();

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {

            die("Error al hacer update al comercial: " . $e->getMessage());
        }
    }

    /**
     * Actualizar la firma digital de un comercial por su id_usuario
     * @param int $id_usuario - ID del usuario (relacionado con comerciales)
     * @param string $firma_base64 - Firma en formato base64 PNG
     * @return bool - true si se actualizó correctamente
     */
    public function update_firma_by_usuario($id_usuario, $firma_base64)
    {
        try {
            $sql = "UPDATE comerciales SET firma_comercial = ? WHERE id_usuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $firma_base64, PDO::PARAM_STR);
            $stmt->bindValue(2, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0; // true si se actualizó al menos un registro
        } catch (PDOException $e) {
            error_log("Error al actualizar firma del comercial: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener la firma digital de un comercial por su id_usuario
     * @param int $id_usuario - ID del usuario (relacionado con comerciales)
     * @return string|null - Firma en base64 o null si no existe
     */
    public function get_firma_by_usuario($id_usuario)
    {
        try {
            $sql = "SELECT firma_comercial FROM comerciales WHERE id_usuario = ? AND activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado['firma_comercial'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener firma del comercial: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si un usuario tiene un comercial asociado
     * @param int $id_usuario - ID del usuario
     * @return array|null - Datos del comercial o null si no existe
     */
    public function get_comercial_by_usuario($id_usuario)
    {
        try {
            $sql = "SELECT id_comercial, nombre, apellidos, firma_comercial 
                    FROM comerciales 
                    WHERE id_usuario = ? AND activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener comercial por usuario: " . $e->getMessage());
            return null;
        }
    }

}
