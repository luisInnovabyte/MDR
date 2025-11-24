<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Estados
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_estado()
    {
        try {
            $sql = "SELECT * FROM estados_llamada";  // Es una vista que contiene el nombre de las llamadas
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Consulta',
            "Se consultó el listado de estados", 
            'info'
        );
        
            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las vacaciones
        
        } catch (PDOException $e) {
       
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al obtener estados: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los estados: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_estadoxid($idestado)
    {
        try {
            $sql = "SELECT * FROM estados_llamada where id_estado=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idestado, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Estados',
            'Consulta por id',
            "Se consultó el id de estados " . $idestado, 
            'info'
        );

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al obtener el id de estados " . $idestado . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar los estados con id {$idestado}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_estadoxid($idestado)
    {
        try {
            $sql = "UPDATE estados_llamada set activo_estado=0 where id_estado=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idestado, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de los metodos
        $this->registro->registrarActividad(
            'admin',
            'Estados',
            'Desactivado el estado predeterminado',
            "Se desactivó el estado con el id de estado " . $idestado, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al desactivar el estado por defecto id de estados " . $idestado . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar el estado con id {$idestado}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_estadoxid($idestado)
    {
        try {
            $sql = "UPDATE estados_llamada set activo_estado=1 where id_estado=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idestado, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Estados',
            'Activado el estado por defecto',
            "Se activó el estado por defecto de el estado con el id de estado " . $idestado, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al activar el id de estados por defecto " . $idestado . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el estado del id de estado por defecto {$idestado}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_estado($desc_estado, $peso_estado, $defecto_estado = 0)
    {
        try {
            $sql = "INSERT INTO estados_llamada (desc_estado, defecto_estado, activo_estado, peso_estado) VALUES (?, ?, 1, ?)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $desc_estado, PDO::PARAM_STR); // Se enlaza el valor de desc_estado
            $stmt->bindValue(2, $defecto_estado, PDO::PARAM_INT);
            $stmt->bindValue(3, $peso_estado, PDO::PARAM_INT); // Se enlaza el valor del peso_estado
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

                   // 🔹 Registrar consulta de los métodos
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Insertado el estado',
                "Se insertó el estado con el id de estados " . $idInsert, 
                'info'
            );

            return true; // Devuelve true si la inserción fue exitosa
            //return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al insertar el estado: ". $e->getMessage(),
                'error'
            );

            die("Error al insertar el estado: " . $e->getMessage());
        }
    }

    public function update_estado($idestado, $desc_estado, $peso_estado, $defecto_estado)
{
    try {
        $sql = "UPDATE estados_llamada SET desc_estado = ?, peso_estado = ?, defecto_estado = ? WHERE id_estado = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $desc_estado, PDO::PARAM_STR);
        $stmt->bindValue(2, $peso_estado, PDO::PARAM_INT);
        $stmt->bindValue(3, $defecto_estado, PDO::PARAM_INT);
        $stmt->bindValue(4, $idestado, PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad(
            'admin',
            'Estados',
            'Actualizado el estado',
            "Se actualizó el estado con el id de estados " . $idestado, 
            'info'
        );

        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Estados',
            'Error',
            "Error al actualizar el estado: ". $e->getMessage(),
            'error'
        );
        die("Error al hacer update al estado: " . $e->getMessage());
    }
}

     // Método para comprobar si ya hay un estado predeterminado
     public function comprobarPredeterminado() {
        try {
            $sql = "SELECT id_estado FROM estados_llamada WHERE defecto_estado = 1 LIMIT 1"; 
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return [
                    "hasPredeterminado" => true,
                    "id_estado" => $row['id_estado']
                ];
            }
            return [
                "hasPredeterminado" => false
            ];
        } catch (PDOException $e) {
            // Registrar el error
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al comprobar el estado predeterminado: ". $e->getMessage(),
                'error'
            );
            return [
                "hasPredeterminado" => false,
                "error" => $e->getMessage() // Devuelve el error para debugging
            ];
        }
    }

    // Método para quitar el estado predeterminado
    public function quitarPredeterminado($id_estado) {
        try {
            $sql = "UPDATE estados_llamada SET defecto_estado = 0 WHERE id_estado = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_estado, PDO::PARAM_INT); // Enlaza el ID del estado
            $stmt->execute();
        
            // Registrar la actividad de quitar el predeterminado
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Quitar estado predeterminado',
                "Se quitó el estado predeterminado con el id de estados " . $id_estado,
                'info'
            );
        } catch (PDOException $e) {
            // Registrar el error
            $this->registro->registrarActividad(
                'admin',
                'Estados',
                'Error',
                "Error al quitar el estado predeterminado: ". $e->getMessage(),
                'error'
            );
            die("Error al quitar el estado predeterminado: " . $e->getMessage());
        }
    }


}
