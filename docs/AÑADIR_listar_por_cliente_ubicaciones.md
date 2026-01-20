Nuevo case añadido al controller de ubicaciones:

```php
    // =========================================================
    // CASE: listar_por_cliente
    // Lista ubicaciones de un cliente específico
    // =========================================================
    case "listar_por_cliente":
        $id_cliente = $_POST["id_cliente"] ?? null;
        
        if (!$id_cliente) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de cliente no proporcionado',
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        // Obtener ubicaciones del cliente
        $sql = "SELECT 
                    id_ubicacion,
                    nombre_ubicacion,
                    direccion_ubicacion,
                    poblacion_ubicacion,
                    provincia_ubicacion,
                    cp_ubicacion
                FROM cliente_ubicacion
                WHERE id_cliente = ?
                AND activo_ubicacion = 1
                ORDER BY nombre_ubicacion ASC";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones: ' . $e->getMessage(),
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
```

INSTRUCCIÓN: Añadir este case en el archivo w:\MDR\controller\ubicaciones.php dentro del switch principal
