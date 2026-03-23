Nuevo case añadido al controller de presupuesto:

```php
    // =========================================================
    // CASE: get_fechas_evento
    // Obtiene las fechas del evento para inicializar líneas
    // =========================================================
    case "get_fechas_evento":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versión no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Obtener fechas desde el presupuesto
        $sql = "SELECT 
                    p.fecha_inicio_evento_presupuesto,
                    p.fecha_fin_evento_presupuesto
                FROM presupuesto_version pv
                INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                WHERE pv.id_version_presupuesto = ?";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'fecha_inicio_evento' => $datos['fecha_inicio_evento_presupuesto'],
                        'fecha_fin_evento' => $datos['fecha_fin_evento_presupuesto']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron fechas para esta versión'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener fechas: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
```

INSTRUCCIÓN: Añadir este case ANTES del cierre del switch en el archivo w:\MDR\controller\presupuesto.php
(Justo después del case "get_info_version" y antes de la línea `}` final del switch)
