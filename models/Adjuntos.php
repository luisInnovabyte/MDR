<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Adjuntos
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

   public function get_adjunto()
    {
        try {

            /*
            $sql = "SELECT * FROM vista_adjuntos_con_comunicante";  // Es una vista que contiene el nombre de los metodos
            */

            $sql = "SELECT 
                        a.id_adjunto,
                        a.id_llamada,
                        a.nombre_archivo,
                        a.tipo,
                        a.fecha_subida,
                        a.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = a.id_llamada) AS nombre_comunicante
                    FROM adjunto_llamada a ";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de los adjuntos
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Consulta',
                "Se consultó el listado de adjuntos", 
                'info'
            );

            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los adjuntos

        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al obtener adjuntos: " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar los adjuntos: " . $e->getMessage());

            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener adjuntos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_adjuntoxid($idAdjunto)
    {
        try {

            /*
            $sql = "SELECT * FROM vista_adjuntos_con_comunicante where id_adjunto=?";
            */

            $sql = "SELECT 
                        a.id_adjunto,
                        a.id_llamada,
                        a.nombre_archivo,
                        a.tipo,
                        a.fecha_subida,
                        a.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = a.id_llamada) AS nombre_comunicante
                    FROM adjunto_llamada a
                    WHERE a.id_adjunto = ? and a.estado = 1";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idAdjunto, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar consulta del adjunto por id
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Consulta por id',
                "Se consultó el id de adjuntos " . $idAdjunto, 
                'info'
            );

            return $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al obtener el id de adjuntos " . $idAdjunto . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar los adjuntos con id {$idAdjunto}: " . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener adjuntos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    // ESTE DELETE DESACTIVA
    public function delete_adjuntoxid($idAdjunto)
    {
        try {
            $sql = "UPDATE adjunto_llamada set estado=0 where id_adjunto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idAdjunto, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de los metodos
        $this->registro->registrarActividad(
            'admin',
            'Adjuntos',
            'Desactivado el estado de adjuntos',
            "Se desactivó el estado con el id de adjuntos " . $idAdjunto, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al desactivar el id de adjuntos " . $idAdjunto . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar el adjunto con id {$idAdjunto}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_adjuntoxid($idAdjunto)
    {
        try {
            $sql = "UPDATE adjunto_llamada set estado=1 where id_adjunto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idAdjunto, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Adjuntos',
            'Activado el adjunto',
            "Se activó el estado de el adjunto con el id de adjunto " . $idAdjunto, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al activar el id de adjuntos " . $idAdjunto . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el estado del id de adjunto {$idAdjunto}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_adjunto($id_llamada, $documentos)
{
    try {
        $pdo = $this->conexion;
        $pdo->beginTransaction();
        
        $sql = "INSERT INTO adjunto_llamada (id_llamada, nombre_archivo, tipo, fecha_subida) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        $idsInsertados = [];
        
        foreach ($documentos as $doc) {
            $stmt->bindValue(1, $id_llamada, PDO::PARAM_INT);
            $stmt->bindValue(2, $doc['nombre_guardado'], PDO::PARAM_STR);
            $stmt->bindValue(3, $doc['tipo'], PDO::PARAM_STR);
            $stmt->bindValue(4, $doc['fecha'], PDO::PARAM_STR);
            $stmt->execute();
            
            $idInsert = $pdo->lastInsertId();
            $idsInsertados[] = $idInsert;
            
            // Registrar cada inserción
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Insertado adjunto',
                "Se insertó adjunto ID: $idInsert para llamada ID: $id_llamada",
                'info'
            );
        }
        
        $pdo->commit();
        return $idsInsertados; // Devuelve array de IDs insertados
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        
        $this->registro->registrarActividad(
            'admin',
            'Adjuntos',
            'Error',
            "Error al insertar adjuntos para llamada ID: $id_llamada - " . $e->getMessage(),
            'error'
        );
        
        throw new Exception("Error al insertar adjuntos: " . $e->getMessage());
    }
}

    public function update_adjunto($id_adjunto, $id_llamada, $nombre_archivo, $tipo, $fecha_subida)
    {
        try {
            $sql = "UPDATE adjunto_llamada SET id_llamada = ?, nombre_archivo = ?, tipo = ?, fecha_subida = ? WHERE id_adjunto = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_llamada, PDO::PARAM_INT);
            $stmt->bindValue(2, $nombre_archivo, PDO::PARAM_STR);
            $stmt->bindValue(3, $tipo, PDO::PARAM_STR);
            $stmt->bindValue(4, $fecha_subida, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_adjunto, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
                $this->registro->registrarActividad(
                    'admin',
                    'Adjuntos',
                    'Actualizado el adjunto',
                    "Se actualizó el adjunto con el id de adjuntos " . $idInsert, 
                    'info'
                );
        

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al actualizar el adjunto: ". $e->getMessage(),
                'error'
            );

            die("Error al hacer update al adjunto: " . $e->getMessage());
        }
    }

    public function obtenerAdjuntosPorIdLlamada($idLlamada)
    {
        try {

            /* 
            $sql = "SELECT * FROM vista_adjuntos_con_comunicante WHERE id_llamada = ?";
            */

            $sql = "SELECT 
                        a.id_adjunto,
                        a.id_llamada,
                        a.nombre_archivo,
                        a.tipo,
                        a.fecha_subida,
                        a.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = a.id_llamada) AS nombre_comunicante
                    FROM adjunto_llamada a
                    WHERE a.id_llamada = ? and a.estado = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $idLlamada, PDO::PARAM_INT);
            $stmt->execute();

            // Registrar la consulta
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Consulta por llamada',
                "Se consultaron adjuntos para la llamada ID: " . $idLlamada,
                'info'
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los registros encontrados

        } catch (PDOException $e) {
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al obtener adjuntos para la llamada ID: " . $idLlamada . " | " . $e->getMessage(),
                'error'
            );

            // Para desarrollo
            die("Error al obtener adjuntos para la llamada {$idLlamada}: " . $e->getMessage());

            // Para producción (alternativa):
            // error_log("Error al obtener adjuntos: " . $e->getMessage());
            // return []; // Devuelve array vacío para manejo elegante
        }
    }


    // ESTE DELETE ELIMINA
    public function delete_adjunto($id_adjunto)
    {
        try {
            $sql = "DELETE FROM adjunto_llamada WHERE id_adjunto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_adjunto, PDO::PARAM_INT);
            $stmt->execute();

            // Registrar actividad
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Eliminación de adjunto',
                "Se eliminó el registro del adjunto con ID: $id_adjunto", 
                'info'
            );

            return true;

        } catch (PDOException $e) {
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al eliminar adjunto ID: $id_adjunto | " . $e->getMessage(),
                'error'
            );

            throw new Exception("Error al eliminar adjunto en BD: " . $e->getMessage());
        }
    }

    // RECOGER INFORMACIÓN DE EL ADJUNTO QUE VOY A BORRAR
    public function get_adjunto_info($id_adjunto)
    {
        try {
            $sql = "SELECT nombre_archivo FROM adjunto_llamada WHERE id_adjunto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_adjunto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Adjuntos',
                'Error',
                "Error al obtener info del adjunto ID: $id_adjunto | " . $e->getMessage(),
                'error'
            );

            throw new Exception("Error al obtener información del adjunto: " . $e->getMessage());
        }
    }

}
