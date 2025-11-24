<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Categorias
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_categoria()
    {
        try {
            $sql = "SELECT * FROM categorias";  // Es una vista que contiene el nombre de las llamadas
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Consulta',
            "Se consultó el listado de categorias", 
            'info'
        );
        
            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las vacaciones
        
        } catch (PDOException $e) {
       
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Error',
                "Error al obtener categorias: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar las categorias: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_categoriaxid($idcategoria)
    {
        try {
            $sql = "SELECT * FROM categorias where id=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcategoria, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Consulta por id',
            "Se consultó el id de categorias " . $idcategoria, 
            'info'
        );

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Error',
                "Error al obtener el id de categorias " . $idcategoria . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar las categorias con id {$idcategoria}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_categoriaxid($idcategoria)
    {
        try {
            $sql = "UPDATE categorias set activo_estado=0 where id=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcategoria, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de los metodos
        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Desactivada la categoria predeterminado',
            "Se desactivó la categoria con el id de categoria " . $idcategoria, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Error',
                "Error al desactivar la categoria por defecto id de categorias " . $idcategoria . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar la categoria con id {$idcategoria}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_categoriaxid($idcategoria)
    {
        try {
            $sql = "UPDATE categorias set activo_estado=1 where id=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcategoria, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Activada la categoría por defecto',
            "Se activó la categoría por defecto de la categoría con el id de categoría " . $idcategoria, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Error',
                "Error al activar el id de categorias por defecto " . $idcategoria . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar la categoria del id de categoria por defecto {$idcategoria}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_categoria($nombre, $fecha)
    {
        try {
            $sql = "INSERT INTO categorias (nombre, fecha) VALUES (?, ?)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR); // Se enlaza el valor de desc_estado
            $stmt->bindValue(2, $fecha, PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

                   // 🔹 Registrar consulta de los métodos
            $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Insertada la categoría',
                "Se insertó la categoría con el id de categorias " . $idInsert, 
                'info'
            );

            return true; // Devuelve true si la inserción fue exitosa
            //return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Categorias',
                'Error',
                "Error al insertar la categoría: ". $e->getMessage(),
                'error'
            );

            die("Error al insertar la categoría: " . $e->getMessage());
        }
    }

    public function update_categoria($idcategoria, $nombre, $fecha)
{
    try {
        $sql = "UPDATE categorias SET nombre = ?, fecha = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
        $stmt->bindValue(2, $fecha, PDO::PARAM_STR);
        $stmt->bindValue(3, $idcategoria, PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Actualizada la categoria',
            "Se actualizó la categoria con el id de categorias " . $idcategoria, 
            'info'
        );

        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Categorias',
            'Error',
            "Error al actualizar la categoria: ". $e->getMessage(),
            'error'
        );
        die("Error al hacer update a la categoria: " . $e->getMessage());
    }
  }
}
