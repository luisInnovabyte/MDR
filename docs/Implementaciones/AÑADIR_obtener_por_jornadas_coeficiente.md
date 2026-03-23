Nuevo case añadido al controller de coeficiente:

```php
    // =========================================================
    // CASE: obtener_por_jornadas
    // Obtiene el coeficiente correspondiente según número de jornadas
    // =========================================================
    case "obtener_por_jornadas":
        $jornadas = $_POST["jornadas"] ?? null;
        
        if (!$jornadas || $jornadas < 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Número de jornadas no válido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        // Buscar coeficiente más cercano (por defecto buscar el mayor que no supere las jornadas)
        $sql = "SELECT 
                    id_coeficiente,
                    jornadas_coeficiente,
                    valor_coeficiente
                FROM coeficiente
                WHERE jornadas_coeficiente <= ?
                AND activo_coeficiente = 1
                ORDER BY jornadas_coeficiente DESC
                LIMIT 1";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $jornadas, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                // Si no hay coeficiente, devolver valor por defecto 1.00
                header('Content-Type: application/json');
                echo json_encode([
                    'id_coeficiente' => null,
                    'jornadas_coeficiente' => $jornadas,
                    'valor_coeficiente' => 1.00
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener coeficiente: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
```

INSTRUCCIÓN: Añadir este case en el archivo w:\MDR\controller\coeficiente.php dentro del switch principal
