<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Metodos
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_metodo()
    {
        try {
            $sql = "SELECT * FROM metodos_contacto";  // Es una vista que contiene el nombre de los metodos
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Consulta',
            "Se consultó el listado de metodos", 
            'info'
        );
        
            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las vacaciones
        
        } catch (PDOException $e) {
       
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Metodos',
                'Error',
                "Error al obtener metodos: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los metodos: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_metodoxid($idvacacion)
    {
        try {
            $sql = "SELECT * FROM metodos_contacto where id_metodo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idvacacion, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Consulta por id',
            "Se consultó el id de metodos " . $idmetodo, 
            'info'
        );

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Metodos',
                'Error',
                "Error al obtener el id de metodos " . $idmetodo . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar los metodos con id {$idmetodo}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_imagen_metodo($id_metodo)
    {
        try {
            $sql = "SELECT imagen_metodo FROM metodos_contacto WHERE id_metodo = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_metodo, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Consulta',
            "Se consultó el listado de metodos", 
            'info'
        );
        
            // Devuelvo los resultados de la consulta
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['imagen_metodo'] : null;
        
        } catch (PDOException $e) {
       
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Metodos',
                'Error',
                "Error al obtener metodos: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los metodos: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_metodoxid($idmetodo)
    {
        try {
            $sql = "UPDATE metodos_contacto set estado=0 where id_metodo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idmetodo, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de los metodos
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Desactivado el estado del metodo',
            "Se desactivó el estado con el id de metodo " . $idmetodo, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Metodos',
                'Error',
                "Error al desactivar el id de metodos " . $idmetodo . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar el metodo con id {$idmetodo}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_metodoxid($idmetodo)
    {
        try {
            $sql = "UPDATE metodos_contacto set estado=1 where id_metodo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idmetodo, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Activado el método',
            "Se activó el estado de el método con el id de método " . $idmetodo, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Métodos',
                'Error',
                "Error al activar el id de métodos " . $idmetodo . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el estado del id de método {$idmetodo}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_metodo($nombre, $permite_adjuntos)
    {
        try {
            $sql = "INSERT INTO metodos_contacto (nombre, permite_adjuntos) VALUES (?, ?)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR); // Se enlaza el valor de nombre
            $stmt->bindValue(2, $permite_adjuntos, PDO::PARAM_INT); // Se enlaza el valor del permite_adjuntos
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

                   // 🔹 Registrar consulta de los métodos
            $this->registro->registrarActividad(
                'admin',
                'Métodos',
                'Insertado el método',
                "Se insertó el método con el id de métodos " . $idInsert, 
                'info'
            );

            return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Métodos',
                'Error',
                "Error al insertar el método: ". $e->getMessage(),
                'error'
            );

            die("Error al insertar el método: " . $e->getMessage());
        }
    }

    public function update_metodo($idmetodo, $nombre, $permite_adjuntos)
    {
        try {
            $sql = "UPDATE metodos_contacto SET nombre = ?, permite_adjuntos = ?  WHERE id_metodo = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $permite_adjuntos, PDO::PARAM_INT);
            $stmt->bindValue(3, $idmetodo, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
                $this->registro->registrarActividad(
                    'admin',
                    'Metodos',
                    'Actualizado el método',
                    "Se actualizó el método con el id de métodos " . $idInsert, 
                    'info'
                );
        

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Métodos',
                'Error',
                "Error al actualizar el método: ". $e->getMessage(),
                'error'
            );

            die("Error al hacer update al método: " . $e->getMessage());
        }
    }

    public function update_imagen_metodo($id_metodo, $nombre_imagen)
{
    try {
        $sql = "UPDATE metodos_contacto SET imagen_metodo = ? WHERE id_metodo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre_imagen, PDO::PARAM_STR);
        $stmt->bindValue(2, $id_metodo, PDO::PARAM_INT);
        $stmt->execute();

        // Registro de actividad
        $this->registro->registrarActividad(
            'admin',
            'Metodos.php',
            'Actualizar imagen',
            "Se actualizó la imagen para el método ID: $id_metodo",
            'info'
        );
        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'update_imagen_metodo',
            "Error al actualizar imagen: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

public function delete_imagen_metodoxid($id_metodo, $imageName)
{
    try {
        $sql = "UPDATE metodos_contacto SET imagen_metodo = NULL WHERE id_metodo = ? AND imagen_metodo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_metodo, PDO::PARAM_INT);
        $stmt->bindValue(2, $imageName, PDO::PARAM_STR);
        $stmt->execute();

        // Registro de actividad (igual que en tu versión)
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Borrar imagen',
            "Se borró la imagen $imageName del método ID: $id_metodo",
            'info'
        );
        
        return ($stmt->rowCount() > 0); // Retorna true si se afectó alguna fila
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Borrar imagen',
            "Error al borrar la imagen $imageName del método ID: $id_metodo - " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

public function actualizar_imagen_metodoxid($id_metodo, $imagen_predeterminada)
{
    try {
        // Actualizar la imagen del método a la imagen predeterminada
        $sql = "UPDATE metodos_contacto SET imagen_metodo = ? WHERE id_metodo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $imagen_predeterminada, PDO::PARAM_STR);  // Asignar la imagen predeterminada
        $stmt->bindValue(2, $id_metodo, PDO::PARAM_INT);  // Identificador del método
        $stmt->execute();

        // Registro de actividad
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Actualizar imagen',
            "Se asignó la imagen predeterminada al método ID: $id_metodo",
            'info'
        );

        return ($stmt->rowCount() > 0); // Retorna true si se afectó alguna fila
    } catch (PDOException $e) {
        // En caso de error, registrar la actividad
        $this->registro->registrarActividad(
            'admin',
            'Metodos',
            'Actualizar imagen',
            "Error al asignar la imagen predeterminada al método ID: $id_metodo - " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

}
