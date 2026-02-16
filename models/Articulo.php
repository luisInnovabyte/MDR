<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';



class Articulo
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_articulo()
    {
        try {
            $sql = "SELECT * FROM vista_articulo_completa";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los artículos
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los artículos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'get_articulo',
                "Error al listar los artículos: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_articulo_con_elementos()
    {
        try {
            $sql = "SELECT 
                        vac.*, 
                        COUNT(CASE WHEN e.activo_elemento = 1 THEN 1 END) as total_elementos,
                        MAX(vap.peso_articulo_kg) as peso_articulo_kg,
                        MAX(vap.metodo_calculo) as metodo_calculo,
                        MAX(vap.tiene_datos_peso) as tiene_datos_peso,
                        MAX(vap.items_con_peso) as items_con_peso,
                        MAX(vap.total_items) as total_items
                    FROM vista_articulo_completa vac
                    LEFT JOIN elemento e ON vac.id_articulo = e.id_articulo_elemento
                    LEFT JOIN vista_articulo_peso vap ON vac.id_articulo = vap.id_articulo
                    GROUP BY vac.id_articulo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'get_articulo_con_elementos',
                "Error al listar los artículos con elementos: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function get_articulo_disponible()
    {
        try {
            $sql = "SELECT * FROM vista_articulo_completa WHERE activo_articulo = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los artículos disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los artículos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'get_articulo_disponible',
                "Error al listar los artículos disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_articuloxid($id_articulo)
    {
        try {
            $sql = "SELECT 
                        a.*,
                        ap.peso_articulo_kg AS peso_medio_kg,
                        ap.items_con_peso AS elementos_con_peso,
                        ap.total_items AS total_elementos,
                        ap.metodo_calculo,
                        ap.tiene_datos_peso
                    FROM vista_articulo_completa a
                    LEFT JOIN vista_articulo_peso ap ON a.id_articulo = ap.id_articulo
                    WHERE a.id_articulo = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un solo artículo (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el artículo {$id_articulo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'get_articuloxid',
                "Error al mostrar el artículo {$id_articulo}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function total_articulo()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM articulo";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'total_articulo',
                "Error al contar total de artículos: " . $e->getMessage(),
                "error"
            );
            
            return 0;
        }
    }

    public function total_articulo_activo()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM articulo WHERE activo_articulo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'total_articulo_activo',
                "Error al contar total de artículos activos: " . $e->getMessage(),
                "error"
            );
            
            return 0;
        }
    }

    public function total_articulo_activo_kit()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM articulo WHERE activo_articulo = 1 AND es_kit_articulo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'total_articulo_activo_kit',
                "Error al contar total de artículos activos que son kits: " . $e->getMessage(),
                "error"
            );
            
            return 0;
        }
    }

    public function total_articulo_activo_coeficiente()
    {
        try {
            $sql = "SELECT COUNT(*) as total
                    FROM articulo a
                    JOIN familia f ON a.id_familia = f.id_familia
                    WHERE a.activo_articulo = 1
                    AND (
                        a.coeficiente_articulo = 1 
                        OR (a.coeficiente_articulo IS NULL AND f.coeficiente_familia = 1)
                    )";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
            
        } catch (PDOException $e) {
                $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'total_articulo_activo_coeficiente',
                "Error al contar total de artículos activos que son coeficientes: " . $e->getMessage(),
                "error"
            );
            return 0;
        }
    }

// ...existing code...




    public function delete_articuloxid($id_articulo)
    {
        try {
            $sql = "UPDATE articulo set activo_articulo=0 where id_articulo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'Desactivar',
                "Se desactivó el artículo con ID: $id_articulo",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un artículo, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el artículo {$id_articulo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'delete_articuloxid',
                "Error al desactivar el artículo {$id_articulo}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_articuloxid($id_articulo)
    {
        try {
            $sql = "UPDATE articulo set activo_articulo=1 where id_articulo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'Activar',
                "Se activo el artículo con ID: $id_articulo",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo artículo (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un artículo, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el artículo {$id_articulo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'activar_articuloxid',
                "Error al activar el artículo {$id_articulo}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }



    public function insert_articulo(
        $id_familia,
        $id_unidad,
        $codigo_articulo,
        $nombre_articulo,
        $name_articulo,
        $imagen_articulo = '',
        $precio_alquiler_articulo = 0.00,
        $coeficiente_articulo = null,
        $es_kit_articulo = 0,
        $control_total_articulo = 0,
        $no_facturar_articulo = 0,
        $notas_presupuesto_articulo = '',
        $notes_budget_articulo = '',
        $orden_obs_articulo = 200,
        $observaciones_articulo = '',
        $permitir_descuentos_articulo = 1,
        $id_impuesto = null
    )
    {
        try {

            $sql = "INSERT INTO articulo (
                        id_familia, 
                        id_unidad, 
                        codigo_articulo, 
                        nombre_articulo, 
                        name_articulo, 
                        imagen_articulo, 
                        precio_alquiler_articulo, 
                        coeficiente_articulo, 
                        es_kit_articulo, 
                        control_total_articulo, 
                        no_facturar_articulo, 
                        notas_presupuesto_articulo, 
                        notes_budget_articulo, 
                        orden_obs_articulo, 
                        observaciones_articulo, 
                        permitir_descuentos_articulo,
                        id_impuesto,
                        activo_articulo, 
                        created_at_articulo, 
                        updated_at_articulo
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_familia, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_unidad, $id_unidad === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(3, $codigo_articulo, PDO::PARAM_STR);
            $stmt->bindValue(4, $nombre_articulo, PDO::PARAM_STR);
            $stmt->bindValue(5, $name_articulo, PDO::PARAM_STR);
            $stmt->bindValue(6, $imagen_articulo, PDO::PARAM_STR);
            $stmt->bindValue(7, $precio_alquiler_articulo, PDO::PARAM_STR);
            $stmt->bindValue(8, $coeficiente_articulo, $coeficiente_articulo === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(9, $es_kit_articulo, PDO::PARAM_INT);
            $stmt->bindValue(10, $control_total_articulo, PDO::PARAM_INT);
            $stmt->bindValue(11, $no_facturar_articulo, PDO::PARAM_INT);
            $stmt->bindValue(12, $notas_presupuesto_articulo, PDO::PARAM_STR);
            $stmt->bindValue(13, $notes_budget_articulo, PDO::PARAM_STR);
            $stmt->bindValue(14, $orden_obs_articulo, PDO::PARAM_INT);
            $stmt->bindValue(15, $observaciones_articulo, PDO::PARAM_STR);
            $stmt->bindValue(16, $permitir_descuentos_articulo, PDO::PARAM_INT);
            $stmt->bindValue(17, $id_impuesto, $id_impuesto === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'Insertar',
                "Se inserto el artículo con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del artículo insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el artículo: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'insert_articulo',
                "Error al insertar el artículo: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }


   
    public function update_articulo(
        $id_articulo,
        $id_familia,
        $id_unidad,
        $codigo_articulo,
        $nombre_articulo,
        $name_articulo,
        $imagen_articulo = '',
        $precio_alquiler_articulo = 0.00,
        $coeficiente_articulo = null,
        $es_kit_articulo = 0,
        $control_total_articulo = 0,
        $no_facturar_articulo = 0,
        $notas_presupuesto_articulo = '',
        $notes_budget_articulo = '',
        $orden_obs_articulo = 200,
        $observaciones_articulo = '',
        $permitir_descuentos_articulo = 1,
        $id_impuesto = null
    ){
        try {
            $sql = "UPDATE articulo SET 
                        id_familia = ?, 
                        id_unidad = ?, 
                        codigo_articulo = ?, 
                        nombre_articulo = ?, 
                        name_articulo = ?, 
                        imagen_articulo = ?, 
                        precio_alquiler_articulo = ?, 
                        coeficiente_articulo = ?, 
                        es_kit_articulo = ?, 
                        control_total_articulo = ?, 
                        no_facturar_articulo = ?, 
                        notas_presupuesto_articulo = ?, 
                        notes_budget_articulo = ?, 
                        orden_obs_articulo = ?, 
                        observaciones_articulo = ?, 
                        permitir_descuentos_articulo = ?,
                        id_impuesto = ?,
                        updated_at_articulo = NOW() 
                    WHERE id_articulo = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_familia, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_unidad, $id_unidad === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(3, $codigo_articulo, PDO::PARAM_STR);
            $stmt->bindValue(4, $nombre_articulo, PDO::PARAM_STR);
            $stmt->bindValue(5, $name_articulo, PDO::PARAM_STR);
            $stmt->bindValue(6, $imagen_articulo, PDO::PARAM_STR);
            $stmt->bindValue(7, $precio_alquiler_articulo, PDO::PARAM_STR);
            $stmt->bindValue(8, $coeficiente_articulo, $coeficiente_articulo === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(9, $es_kit_articulo, PDO::PARAM_INT);
            $stmt->bindValue(10, $control_total_articulo, PDO::PARAM_INT);
            $stmt->bindValue(11, $no_facturar_articulo, PDO::PARAM_INT);
            $stmt->bindValue(12, $notas_presupuesto_articulo, PDO::PARAM_STR);
            $stmt->bindValue(13, $notes_budget_articulo, PDO::PARAM_STR);
            $stmt->bindValue(14, $orden_obs_articulo, PDO::PARAM_INT);
            $stmt->bindValue(15, $observaciones_articulo, PDO::PARAM_STR);
            $stmt->bindValue(16, $permitir_descuentos_articulo, PDO::PARAM_INT);
            $stmt->bindValue(17, $id_impuesto, $id_impuesto === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(18, $id_articulo, PDO::PARAM_INT);

            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'Actualizar',
                "Se actualizó el artículo con ID: $id_articulo",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al artículo: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Articulo',
                'update_articulo',
                "Error al actualizar el artículo:" . $e->getMessage(),
                'error'
            );
            
            return false; // Devolver false en caso de error
        }
    }

    public function verificarArticulo($nombre_articulo, $codigo_articulo = null, $id_articulo = null, $name_articulo = null)
    {
        try {
            // Consulta SQL base - verificamos por nombre, código y nombre en inglés
            $sql = "SELECT COUNT(*) AS total FROM articulo WHERE (LOWER(nombre_articulo) = LOWER(?) OR LOWER(codigo_articulo) = LOWER(?) OR LOWER(name_articulo) = LOWER(?))";
            $params = [trim($nombre_articulo), trim($codigo_articulo), trim($name_articulo)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_articulo)) {
                $sql .= " AND id_articulo != ?";
                $params[] = $id_articulo;
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
                    'Articulo',
                    'verificarArticulo',
                    "Error al verificar existencia del artículo: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el estado del coeficiente para un artículo
     * Retorna:
     * 1 = Artículo con coeficiente propio activo
     * 2 = Artículo que NO permite coeficientes
     * 3 = Usa coeficiente de la familia (heredado)
     * 4 = Familia sin coeficientes configurados
     */
    public function get_estado_coeficiente_articulo($id_articulo)
    {
        try {
            // Obtener datos del artículo
            $sql = "SELECT coeficiente_articulo, id_familia 
                    FROM articulo 
                    WHERE id_articulo = ? 
                    AND activo_articulo = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();
            $articulo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$articulo) {
                return [
                    'success' => false,
                    'message' => 'Artículo no encontrado',
                    'estado_coeficiente' => null
                ];
            }
            
            $coeficiente_articulo = $articulo['coeficiente_articulo'];
            $id_familia = $articulo['id_familia'];
            
            // Evaluar según la lógica solicitada
            if ($coeficiente_articulo == 2) {
                // Artículo que NO permite coeficientes
                return [
                    'success' => true,
                    'estado_coeficiente' => 2,
                    'mensaje' => 'Este artículo NO permite aplicar coeficientes'
                ];
            } elseif ($coeficiente_articulo == 1) {
                // Artículo con coeficiente propio activo
                return [
                    'success' => true,
                    'estado_coeficiente' => 1,
                    'mensaje' => 'Artículo con coeficiente propio activo'
                ];
            } else {
                // coeficiente_articulo == 0, consultar familia
                $sql_familia = "SELECT coeficiente_familia 
                                FROM familia 
                                WHERE id_familia = ? 
                                AND activo_familia = 1";
                
                $stmt_familia = $this->conexion->prepare($sql_familia);
                $stmt_familia->bindValue(1, $id_familia, PDO::PARAM_INT);
                $stmt_familia->execute();
                $familia = $stmt_familia->fetch(PDO::FETCH_ASSOC);
                
                if (!$familia) {
                    return [
                        'success' => false,
                        'message' => 'Familia no encontrada',
                        'estado_coeficiente' => null
                    ];
                }
                
                if ($familia['coeficiente_familia'] == 1) {
                    // Usa coeficiente de la familia
                    return [
                        'success' => true,
                        'estado_coeficiente' => 3,
                        'mensaje' => 'Usa coeficiente heredado de la familia'
                    ];
                } else {
                    // Familia sin coeficientes configurados
                    return [
                        'success' => true,
                        'estado_coeficiente' => 4,
                        'mensaje' => 'Familia sin coeficientes configurados'
                    ];
                }
            }
            
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Articulo',
                    'get_estado_coeficiente_articulo',
                    "Error al obtener estado de coeficiente: " . $e->getMessage(),
                    'error'
                );
            }
            
            return [
                'success' => false,
                'message' => 'Error al consultar estado de coeficiente',
                'error' => $e->getMessage()
            ];
        }
    }

}
