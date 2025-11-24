<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class UnidadMedida
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            // Si no se puede establecer la zona horaria, registrar error pero continuar
            $this->registro->registrarActividad(
                'system',
                'UnidadMedida',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_unidades_disponibles()
    {
        try {
            $sql = "SELECT id_unidad, nombre_unidad, name_unidad, descr_unidad, simbolo_unidad 
                    FROM unidad_medida 
                    WHERE activo_unidad = 1 
                    ORDER BY nombre_unidad ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'UnidadMedida',
                'get_unidades_disponibles',
                "Error al listar las unidades de medida disponibles: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function get_todas_unidades_para_modal()
    {
        try {
            $sql = "SELECT id_unidad, nombre_unidad, name_unidad, descr_unidad, simbolo_unidad, activo_unidad 
                    FROM unidad_medida 
                    ORDER BY nombre_unidad ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'UnidadMedida',
                'get_todas_unidades_para_modal',
                "Error al listar todas las unidades de medida para modal: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function get_unidadxid($id_unidad)
    {
        try {
            $sql = "SELECT * FROM unidad_medida WHERE id_unidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_unidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'UnidadMedida',
                'get_unidadxid',
                "Error al mostrar la unidad de medida {$id_unidad}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }









    
}
?>