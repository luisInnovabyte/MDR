<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Coeficiente
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
                'Coeficiente',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_coeficiente()
    {
        try {
            $sql = "SELECT * FROM coeficiente";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de los coeficientes
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Consulta',
                "Se consultó el listado de coeficientes", 
                'info'
            );
        
            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los coeficientes
        
        } catch (PDOException $e) {
       
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al obtener coeficientes: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los coeficientes: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener coeficientes: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_coeficientexid($idcoeficiente)
    {
        try {
            $sql = "SELECT * FROM coeficiente where id_coeficiente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcoeficiente, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar consulta del coeficiente por id
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Consulta por id',
                "Se consultó el coeficiente con id " . $idcoeficiente, 
                'info'
            );

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al obtener el coeficiente con id " . $idcoeficiente . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar el coeficiente con id {$idcoeficiente}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener coeficiente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_coeficientexid($idcoeficiente)
    {
        try {
            $sql = "UPDATE coeficiente set activo_coeficiente=0 where id_coeficiente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcoeficiente, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar desactivación del coeficiente
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Desactivado coeficiente',
                "Se desactivó el coeficiente con id " . $idcoeficiente, 
                'info'
            );

            return $stmt->rowCount() > 0; // Retorna true si se desactivó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al desactivar el coeficiente con id " . $idcoeficiente . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar el coeficiente con id {$idcoeficiente}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al eliminar coeficiente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_coeficientexid($idcoeficiente)
    {
        try {
            $sql = "UPDATE coeficiente set activo_coeficiente=1 where id_coeficiente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcoeficiente, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar activación del coeficiente
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Activado coeficiente',
                "Se activó el coeficiente con id " . $idcoeficiente, 
                'info'
            );

            return $stmt->rowCount() > 0; // Retorna true si se activó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al activar el coeficiente con id " . $idcoeficiente . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el coeficiente con id {$idcoeficiente}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al activar coeficiente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_coeficiente($jornadas_coeficiente, $valor_coeficiente, $observaciones_coeficiente = '')
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO coeficiente (jornadas_coeficiente, valor_coeficiente, observaciones_coeficiente, activo_coeficiente) VALUES (?, ?, ?, 1)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $jornadas_coeficiente, PDO::PARAM_INT); // Se enlaza el valor de jornadas_coeficiente
            $stmt->bindValue(2, $valor_coeficiente, PDO::PARAM_STR); // Se enlaza el valor de valor_coeficiente
            $stmt->bindValue(3, $observaciones_coeficiente, PDO::PARAM_STR); // Se enlaza el valor de observaciones_coeficiente
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // 🔹 Registrar inserción del coeficiente
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Insertado coeficiente',
                "Se insertó el coeficiente con id " . $idInsert, 
                'info'
            );

            return true; // Devuelve true si la inserción fue exitosa
            //return $idInsert; // Devuelve el ID del coeficiente insertado
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al insertar el coeficiente: ". $e->getMessage(),
                'error'
            );

            die("Error al insertar el coeficiente: " . $e->getMessage());
        }
    }

    public function update_coeficiente($idcoeficiente, $jornadas_coeficiente, $valor_coeficiente, $observaciones_coeficiente)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE coeficiente SET jornadas_coeficiente = ?, valor_coeficiente = ?, observaciones_coeficiente = ? WHERE id_coeficiente = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $jornadas_coeficiente, PDO::PARAM_INT);
            $stmt->bindValue(2, $valor_coeficiente, PDO::PARAM_STR);
            $stmt->bindValue(3, $observaciones_coeficiente, PDO::PARAM_STR);
            $stmt->bindValue(4, $idcoeficiente, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Actualizado coeficiente',
                "Se actualizó el coeficiente con id " . $idcoeficiente, 
                'info'
            );

            return true;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al actualizar el coeficiente: ". $e->getMessage(),
                'error'
            );
            die("Error al actualizar el coeficiente: " . $e->getMessage());
        }
    }

    // Método para verificar si ya existe un coeficiente con las mismas jornadas
    public function verificarJornadasExistentes($jornadas_coeficiente, $id_coeficiente = null) {
        try {
            if ($id_coeficiente !== null) {
                // Para updates - excluir el ID actual
                $sql = "SELECT id_coeficiente FROM coeficiente WHERE jornadas_coeficiente = ? AND id_coeficiente != ? LIMIT 1";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindValue(1, $jornadas_coeficiente, PDO::PARAM_INT);
                $stmt->bindValue(2, $id_coeficiente, PDO::PARAM_INT);
            } else {
                // Para inserts - verificar si existe
                $sql = "SELECT id_coeficiente FROM coeficiente WHERE jornadas_coeficiente = ? LIMIT 1"; 
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindValue(1, $jornadas_coeficiente, PDO::PARAM_INT);
            }
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return [
                    "existe" => true,
                    "id_coeficiente" => $row['id_coeficiente']
                ];
            }
            return [
                "existe" => false
            ];
        } catch (PDOException $e) {
            // Registrar el error
            $this->registro->registrarActividad(
                'admin',
                'Coeficiente',
                'Error',
                "Error al verificar jornadas existentes: ". $e->getMessage(),
                'error'
            );
            return [
                "existe" => false,
                "error" => $e->getMessage() // Devuelve el error para debugging
            ];
        }
    }
}