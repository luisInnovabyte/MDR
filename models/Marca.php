<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


// CREATE TABLE marca (
//     id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_marca VARCHAR(20) NOT NULL UNIQUE,
//     nombre_marca VARCHAR(100) NOT NULL,
//     name_marca VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
//     descr_marca VARCHAR(255),
//     activo_marca BOOLEAN DEFAULT TRUE,
//     created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class Marca
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            // Si no se puede establecer la zona horaria, registrar error pero continuar
            $this->registro->registrarActividad(
                'system',
                'Marca',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_marca()
    {
        try {
            $sql = "SELECT * FROM marca";  //Es una vista que contiene las marcas de productos
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las familias
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las familias: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'get_marca',
                "Error al listar las marcas: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_marca_disponible()
    {
        try {
            $sql = "SELECT * FROM marca WHERE activo_marca = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los usuarios
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los productos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'get_marca_disponible',
                "Error al listar las marcas disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener familias: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_marcaxid($id_marca)
    {
        try {
            $sql = "SELECT * FROM marca where id_marca=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_marca, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un solo usuario (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'get_marcaxid',
                "Error al mostrar la marca {$id_marca}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_marcaxid($id_marca)
    {
        try {
            $sql = "UPDATE marca set activo_marca=0 where id_marca=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_marca, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'Desactivar',
                "Se desactivó la marca con ID: $id_marca",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'delete_marcaxid',
                "Error al desactivar la marca {$id_marca}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_marcaxid($id_marca)
    {
        try {
            $sql = "UPDATE marca set activo_marca=1 where id_marca=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_marca, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'Activar',
                "Se activo la marca con ID: $id_marca",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'activar_marcaxid',
                "Error al activar la marca {$id_marca}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }




// CREATE TABLE marca (
//     id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_marca VARCHAR(20) NOT NULL UNIQUE,
//     nombre_marca VARCHAR(100) NOT NULL,
//     name_marca VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
//     descr_marca VARCHAR(255),
//     activo_marca BOOLEAN DEFAULT TRUE,
//     created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function insert_marca($codigo_marca, $nombre_marca, $name_marca, $descr_marca)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO marca (codigo_marca, nombre_marca, name_marca, descr_marca, activo_marca, created_at_marca, updated_at_marca) 
                                 VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_marca, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $nombre_marca, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $name_marca, PDO::PARAM_STR); // Se enlaza el valor del nombre en inglés
            $stmt->bindValue(4, $descr_marca, PDO::PARAM_STR); // Se enlaza el valor de la descripción
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'Insertar',
                "Se inserto la marca con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'insert_marca',
                "Error al insertar la marca: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }


// CREATE TABLE marca (
//     id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_marca VARCHAR(20) NOT NULL UNIQUE,
//     nombre_marca VARCHAR(100) NOT NULL,
//     name_marca VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
//     descr_marca VARCHAR(255),
//     activo_marca BOOLEAN DEFAULT TRUE,
//     created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function update_marca($id_marca, $codigo_marca, $nombre_marca, $name_marca, $descr_marca){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE marca SET codigo_marca = ?, nombre_marca = ?, name_marca = ?, descr_marca = ?, updated_at_marca = NOW() WHERE id_marca = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_marca, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_marca, PDO::PARAM_STR);
            $stmt->bindValue(3, $name_marca, PDO::PARAM_STR); // Nuevo campo: nombre en inglés
            $stmt->bindValue(4, $descr_marca, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_marca, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'Actualizar',
                "Se actualizó la marca con ID: $id_marca",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Marca',
                'update_marca',
                "Error al actualizar la marca:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarMarca($nombre_marca, $codigo_marca = null, $id_marca = null, $name_marca = null)
    {
        try {
            // Consulta SQL base - verificamos por nombre, código y nombre en inglés
            $sql = "SELECT COUNT(*) AS total FROM marca WHERE (LOWER(nombre_marca) = LOWER(?) OR LOWER(codigo_marca) = LOWER(?) OR LOWER(name_marca) = LOWER(?))";
            $params = [trim($nombre_marca), trim($codigo_marca), trim($name_marca)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_marca)) {
                $sql .= " AND id_marca != ?";
                $params[] = $id_marca;
            }
    
            // Ejecución de la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            // Registro de error - Versión exacta que solicitaste mantener
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Marca',
                    'verificarMarca',
                    "Error al verificar existencia de la marca: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

}
