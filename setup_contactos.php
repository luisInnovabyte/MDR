<?php
echo "<h2>Script de verificación y creación de tabla contacto_cliente</h2>";

try {
    require_once 'config/conexion.php';
    $conexion = new Conexion();
    $pdo = $conexion->getConexion();
    
    // Verificar si existe la tabla contacto_cliente
    $stmt = $pdo->query("SHOW TABLES LIKE 'contacto_cliente'");
    
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ La tabla 'contacto_cliente' NO existe.</p>";
        echo "<p>🔧 Ejecutando script de creación...</p>";
        
        // Leer y ejecutar el script SQL
        $sqlFile = __DIR__ . '/BD/crear_tabla_contacto_cliente.sql';
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            
            // Limpiar comentarios y ejecutar
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Eliminar comentarios /* */
            $sql = preg_replace('/^--.*$/m', '', $sql);       // Eliminar comentarios --
            $sql = preg_replace('/^\s*\/\*.*?\*\/\s*$/m', '', $sql); // Eliminar líneas de comentarios
            
            // Dividir en statements individuales y ejecutar
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^(\/\*|\s*$)/', $statement)) {
                    try {
                        $pdo->exec($statement);
                        echo "<p>✅ Ejecutado: " . substr($statement, 0, 50) . "...</p>";
                    } catch (PDOException $e) {
                        echo "<p>❌ Error ejecutando: " . $e->getMessage() . "</p>";
                    }
                }
            }
            
            // Verificar nuevamente si se creó
            $stmt = $pdo->query("SHOW TABLES LIKE 'contacto_cliente'");
            if ($stmt->rowCount() > 0) {
                echo "<p>✅ ¡Tabla 'contacto_cliente' creada exitosamente!</p>";
                
                // Insertar datos de prueba
                echo "<h3>Insertando datos de prueba...</h3>";
                
                // Primero obtener algunos IDs de clientes existentes
                $stmt = $pdo->query("SELECT id_cliente, nombre_cliente FROM cliente LIMIT 3");
                $clientes = $stmt->fetchAll();
                
                if (count($clientes) > 0) {
                    foreach ($clientes as $i => $cliente) {
                        try {
                            $insertSQL = "INSERT INTO contacto_cliente 
                                         (id_cliente, nombre_contacto_cliente, apellidos_contacto_cliente, 
                                          cargo_contacto_cliente, telefono_contacto_cliente, email_contacto_cliente, 
                                          principal_contacto_cliente) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                            
                            $stmt = $pdo->prepare($insertSQL);
                            $stmt->execute([
                                $cliente['id_cliente'],
                                'Contacto ' . ($i + 1),
                                'Apellido ' . ($i + 1),
                                'Cargo ' . ($i + 1),
                                '961' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                                'contacto' . ($i + 1) . '@' . strtolower(str_replace(' ', '', $cliente['nombre_cliente'])) . '.com',
                                ($i == 0) ? 1 : 0  // Primer contacto como principal
                            ]);
                            
                            echo "<p>✅ Contacto creado para: " . $cliente['nombre_cliente'] . "</p>";
                        } catch (Exception $e) {
                            echo "<p>⚠️ Error creando contacto para " . $cliente['nombre_cliente'] . ": " . $e->getMessage() . "</p>";
                        }
                    }
                } else {
                    echo "<p>⚠️ No hay clientes en la base de datos para crear contactos de prueba.</p>";
                }
                
            } else {
                echo "<p>❌ Error: La tabla no se pudo crear.</p>";
            }
            
        } else {
            echo "<p>❌ No se encontró el archivo SQL: " . $sqlFile . "</p>";
        }
        
    } else {
        echo "<p>✅ La tabla 'contacto_cliente' YA existe.</p>";
        
        // Mostrar estadísticas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM contacto_cliente");
        $total = $stmt->fetch();
        echo "<p>📊 Total de contactos: " . $total['total'] . "</p>";
        
        if ($total['total'] == 0) {
            echo "<p>⚠️ La tabla está vacía. ¿Deseas insertar datos de prueba?</p>";
            echo "<p><a href='?insertar_prueba=1'>Insertar datos de prueba</a></p>";
        }
    }
    
    // Si se solicitó insertar datos de prueba
    if (isset($_GET['insertar_prueba']) && $_GET['insertar_prueba'] == '1') {
        echo "<h3>Insertando datos de prueba...</h3>";
        
        $stmt = $pdo->query("SELECT id_cliente, nombre_cliente FROM cliente LIMIT 5");
        $clientes = $stmt->fetchAll();
        
        foreach ($clientes as $i => $cliente) {
            try {
                $insertSQL = "INSERT INTO contacto_cliente 
                             (id_cliente, nombre_contacto_cliente, apellidos_contacto_cliente, 
                              cargo_contacto_cliente, telefono_contacto_cliente, email_contacto_cliente, 
                              principal_contacto_cliente) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($insertSQL);
                $stmt->execute([
                    $cliente['id_cliente'],
                    'Contacto ' . ($i + 1),
                    'Apellido ' . ($i + 1),
                    'Cargo ' . ($i + 1),
                    '961' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'contacto' . ($i + 1) . '@' . strtolower(str_replace(' ', '', $cliente['nombre_cliente'])) . '.com',
                    ($i == 0) ? 1 : 0
                ]);
                
                echo "<p>✅ Contacto creado para: " . $cliente['nombre_cliente'] . "</p>";
            } catch (Exception $e) {
                echo "<p>⚠️ Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Próximos pasos</h3>";
    echo "<p>1. <a href='diagnostico_contactos.php'>Ejecutar diagnóstico completo</a></p>";
    echo "<p>2. <a href='test_controller_web.php' target='_blank'>Probar controlador directamente</a></p>";
    echo "<p>3. <a href='view/MntClientes_contacto/index.php'>Ir al módulo de Contactos Cliente</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}
?>