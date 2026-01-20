Nuevo case añadido al controller de artículo:

```php
    // =========================================================
    // CASE: listar_para_presupuesto
    // Lista artículos disponibles para añadir a presupuestos
    // Incluye artículos y KITs (para mostrar) pero solo artículos son seleccionables
    // =========================================================
    case "listar_para_presupuesto":
        $datos = $articulo->get_articulos_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_articulo" => $row["id_articulo"],
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "descripcion_articulo" => $row["descripcion_articulo"] ?? '',
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"] ?? 0.00,
                "porcentaje_iva" => $row["porcentaje_iva"] ?? 21.00,
                "es_kit" => $row["es_kit"] ?? 0,
                "activo_articulo" => $row["activo_articulo"]
            );
        }
        
        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
```

INSTRUCCIÓN: Añadir este case en el archivo w:\MDR\controller\articulo.php dentro del switch principal
