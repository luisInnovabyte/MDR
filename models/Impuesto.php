<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE impuesto (
//  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
//  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
//  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
//  descr_impuesto VARCHAR(255),
//  activo_impuesto boolean default true, 
//  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);



class Impuesto
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
                'Impuesto',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_impuesto()
    {
        try {
            $sql = "SELECT * FROM impuesto";  //Es una vista que contiene las familias de productos
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las familias
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las familias: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'get_impuesto',
                "Error al listar los impuestos: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_impuesto_disponible()
    {
        try {
            $sql = "SELECT * FROM impuesto WHERE activo_impuesto = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los usuarios
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los productos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'get_impuesto_disponible',
                "Error al listar los impuestos disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener familias: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_impuestoxid($id_impuesto)
    {
        try {
            $sql = "SELECT * FROM impuesto where id_impuesto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_impuesto, PDO::PARAM_INT);
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
                'Impuesto',
                'get_impuestoxid',
                "Error al mostrar el impuesto {$id_impuesto}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_impuestoxid($id_impuesto)
    {
        try {
            $sql = "UPDATE impuesto set activo_impuesto=0 where id_impuesto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_impuesto, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'Desactivar',
                "Se desactivó el impuesto con ID: $id_impuesto",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'delete_impuestoxid',
                "Error al desactivar el impuesto {$id_impuesto}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_impuestoxid($id_impuesto)
    {
        try {
            $sql = "UPDATE impuesto set activo_impuesto=1 where id_impuesto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_impuesto, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'Activar',
                "Se activo el impuesto con ID: $id_impuesto",
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
                'Impuesto',
                'activar_impuestoxid',
                "Error al activar el impuesto {$id_impuesto}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    //CREATE TABLE impuesto (
//  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
//  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
//  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
//  descr_impuesto VARCHAR(255),
//  activo_impuesto boolean default true, 
//  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);


    public function insert_impuesto($tipo_impuesto, $tasa_impuesto, $descr_impuesto)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO impuesto (tipo_impuesto, tasa_impuesto, descr_impuesto, activo_impuesto, created_at_impuesto, updated_at_impuesto) 
                                 VALUES (?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $tipo_impuesto, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $tasa_impuesto, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $descr_impuesto, PDO::PARAM_STR); // Se enlaza el valor del nombre en inglés
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'Insertar',
                "Se inserto el impuesto con ID: $idInsert",
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
                'Impuesto',
                'insert_impuesto',
                "Error al insertar el impuesto: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }



    //CREATE TABLE impuesto (
//  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
//  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
//  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
//  descr_impuesto VARCHAR(255),
//  activo_impuesto boolean default true, 
//  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);

    
    public function update_impuesto($id_impuesto, $tipo_impuesto, $tasa_impuesto, $descr_impuesto){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE impuesto SET tipo_impuesto = ?, tasa_impuesto = ?, descr_impuesto = ?, updated_at_impuesto = NOW() WHERE id_impuesto = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $tipo_impuesto, PDO::PARAM_STR);
            $stmt->bindValue(2, $tasa_impuesto, PDO::PARAM_STR);
            $stmt->bindValue(3, $descr_impuesto, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_impuesto, PDO::PARAM_INT);
          
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'Actualizar',
                "Se actualizó el impuesto con ID: $id_impuesto",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Impuesto',
                'update_impuesto',
                "Error al actualizar el impuesto:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarImpuesto($tipo_impuesto, $tasa_impuesto = null, $id_impuesto = null)
    {
        try {
            // Consulta SQL base - verificamos por tipo de impuesto o tasa
            $sql = "SELECT COUNT(*) AS total FROM impuesto WHERE (LOWER(tipo_impuesto) = LOWER(?) OR tasa_impuesto = ?)";
            $params = [$tipo_impuesto, $tasa_impuesto];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_impuesto)) {
                $sql .= " AND id_impuesto != ?";
                $params[] = $id_impuesto;
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
                    'Impuesto',
                    'verificarImpuesto',
                    "Error al verificar existencia del impuesto: " . $e->getMessage(),
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
