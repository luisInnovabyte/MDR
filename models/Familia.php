<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_familia VARCHAR(20) NOT NULL UNIQUE,
//     nombre_familia VARCHAR(100) NOT NULL,
//     descr_familia VARCHAR(255),
//     activo_familia BOOLEAN DEFAULT TRUE,
//     created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class Familia
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_familia()
    {
        try {
            $sql = "SELECT * FROM familia";  //Es una vista que contiene las familias de productos
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las familias
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las familias: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'get_familia',
                "Error al listar las familias: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_familia_disponible()
    {
        try {
            $sql = "SELECT * FROM familia WHERE activo_familia = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los usuarios
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los productos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'get_familia_disponible',
                "Error al listar las familias disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener familias: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_familiaxid($id_familia)
    {
        try {
            $sql = "SELECT * FROM familia where id_familia=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_familia, PDO::PARAM_INT);
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
                'Familia',
                'get_familiaxid',
                "Error al mostrar la familia {$id_familia}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_familiaxid($id_familia)
    {
        try {
            $sql = "UPDATE familia set activo_familia=0 where id_familia=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_familia, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'Desactivar',
                "Se desactivó la familia con ID: $id_familia",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'delete_familiaxid',
                "Error al desactivar la familia {$id_familia}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_familiaxid($id_familia)
    {
        try {
            $sql = "UPDATE familia set activo_familia=1 where id_familia=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_familia, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'Activar',
                "Se activo la familia con ID: $id_familia",
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
                'Familia',
                'activar_familiaxid',
                "Error al activar la familia {$id_familia}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }




//  id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_familia VARCHAR(20) NOT NULL UNIQUE,
//     nombre_familia VARCHAR(100) NOT NULL,
//     descr_familia VARCHAR(255),
//     activo_familia BOOLEAN DEFAULT TRUE,
//     created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null)
    {
        try {

            $sql = "INSERT INTO familia (codigo_familia, nombre_familia, name_familia, descr_familia, activo_familia, imagen_familia, id_unidad_familia, created_at_familia, updated_at_familia) 
                                 VALUES (?, ?, ?, ?, 1, ?, ?, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_familia, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $nombre_familia, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $name_familia, PDO::PARAM_STR); // Se enlaza el valor del nombre en inglés
            $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR); // Se enlaza el valor de la descripción
            $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR); // Se enlaza el valor de la imagen
            $stmt->bindValue(6, $id_unidad_familia, $id_unidad_familia === null ? PDO::PARAM_NULL : PDO::PARAM_INT); // Se enlaza el valor de la unidad de familia
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'Insertar',
                "Se inserto la familia con ID: $idInsert",
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
                'Familia',
                'insert_familia',
                "Error al insertar la familia: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }


//  id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_familia VARCHAR(20) NOT NULL UNIQUE,
//     nombre_familia VARCHAR(100) NOT NULL,
//     descr_familia VARCHAR(255),
//     activo_familia BOOLEAN DEFAULT TRUE,
//     created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    
    public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null){
        try {
            $sql = "UPDATE familia SET nombre_familia = ?, codigo_familia = ?, name_familia = ?, descr_familia = ?, imagen_familia = ?, id_unidad_familia = ?, 
            updated_at_familia = NOW() WHERE id_familia = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre_familia, PDO::PARAM_STR);
            $stmt->bindValue(2, $codigo_familia, PDO::PARAM_STR);
            $stmt->bindValue(3, $name_familia, PDO::PARAM_STR); // Nuevo campo: nombre en inglés
            $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR);
            $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR);
            $stmt->bindValue(6, $id_unidad_familia, $id_unidad_familia === null ? PDO::PARAM_NULL : PDO::PARAM_INT); // Se enlaza el valor de la unidad de familia
            $stmt->bindValue(7, $id_familia, PDO::PARAM_INT);

            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'Actualizar',
                "Se actualizó la familia con ID: $id_familia",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Familia',
                'update_familia',
                "Error al actualizar la familia:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarFamilia($nombre_familia, $codigo_familia = null, $id_familia = null, $name_familia = null, )
    {
        try {
            // Consulta SQL base - verificamos por nombre, código y nombre en inglés
            $sql = "SELECT COUNT(*) AS total FROM familia WHERE (LOWER(nombre_familia) = LOWER(?) OR LOWER(codigo_familia) = LOWER(?) OR LOWER(name_familia) = LOWER(?))";
            $params = [trim($nombre_familia), trim($codigo_familia), trim($name_familia)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_familia)) {
                $sql .= " AND id_familia != ?";
                $params[] = $id_familia;
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
                    'Familia',
                    'verificarFamilia',
                    "Error al verificar existencia de la familia: " . $e->getMessage(),
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
