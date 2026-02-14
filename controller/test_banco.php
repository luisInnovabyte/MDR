<?php
/**
 * Script de prueba para verificar datos bancarios
 * y generar PDF de presupuesto con TRANSFERENCIA
 */

require_once "../config/conexion.php";
require_once "../models/ImpresionPresupuesto.php";

try {
    $conexion = (new Conexion())->getConexion();
    
    // 1. Verificar datos bancarios en empresa principal
    echo "<h2>1. Verificando datos bancarios en empresa principal:</h2>";
    
    $sql = "SELECT 
                id_empresa,
                nombre_comercial_empresa,
                iban_empresa,
                swift_empresa,
                banco_empresa
            FROM empresa 
            WHERE empresa_ficticia_principal = 1 
            AND activo_empresa = 1
            LIMIT 1";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($empresa) {
        echo "<p><strong>Empresa:</strong> " . htmlspecialchars($empresa['nombre_comercial_empresa']) . "</p>";
        echo "<p><strong>IBAN:</strong> " . ($empresa['iban_empresa'] ?: '<em>NO CONFIGURADO</em>') . "</p>";
        echo "<p><strong>SWIFT:</strong> " . ($empresa['swift_empresa'] ?: '<em>NO CONFIGURADO</em>') . "</p>";
        echo "<p><strong>Banco:</strong> " . ($empresa['banco_empresa'] ?: '<em>NO CONFIGURADO</em>') . "</p>";
        
        if (empty($empresa['iban_empresa']) && empty($empresa['swift_empresa']) && empty($empresa['banco_empresa'])) {
            echo "<p style='color: orange;'><strong>⚠️ ADVERTENCIA:</strong> No hay datos bancarios configurados. Insertando datos de prueba...</p>";
            
            // Insertar datos de prueba
            $sql_update = "UPDATE empresa SET 
                            banco_empresa = 'BANCO SANTANDER',
                            iban_empresa = 'ES1234567890123456789012',
                            swift_empresa = 'BSCHESMMXXX'
                          WHERE id_empresa = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->execute([$empresa['id_empresa']]);
            
            echo "<p style='color: green;'>✅ Datos bancarios insertados correctamente</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ ERROR: No se encontró empresa principal</p>";
        exit;
    }
    
    // 2. Buscar presupuesto con forma de pago TRANSFERENCIA
    echo "<hr><h2>2. Buscando presupuestos con TRANSFERENCIA:</h2>";
    
    $sql = "SELECT 
                p.id_presupuesto,
                p.numero_presupuesto,
                p.nombre_evento_presupuesto,
                fp.nombre_forma_pago,
                m.nombre_metodo
            FROM presupuesto p
            LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_forma_pago
            LEFT JOIN metodo m ON p.id_metodo = m.id_metodo
            WHERE p.activo_presupuesto = 1
            AND LOWER(m.nombre_metodo) LIKE '%transferencia%'
            ORDER BY p.created_at_presupuesto DESC
            LIMIT 5";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $presupuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($presupuestos) > 0) {
        echo "<p>Presupuestos encontrados con TRANSFERENCIA:</p>";
        echo "<ul>";
        foreach ($presupuestos as $ppto) {
            echo "<li>";
            echo "Nº <strong>" . htmlspecialchars($ppto['numero_presupuesto']) . "</strong> - ";
            echo htmlspecialchars($ppto['nombre_evento_presupuesto']) . " - ";
            echo "Método: " . htmlspecialchars($ppto['nombre_metodo']);
            echo " <a href='impresionpresupuesto_m2_pdf_es.php?id=" . $ppto['id_presupuesto'] . "' target='_blank'>Ver PDF</a>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ No se encontraron presupuestos con TRANSFERENCIA</p>";
        
        // Buscar cualquier presupuesto activo
        echo "<p>Buscando presupuestos activos para modificar...</p>";
        
        $sql_any = "SELECT 
                        p.id_presupuesto,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        m.nombre_metodo
                    FROM presupuesto p
                    LEFT JOIN metodo m ON p.id_metodo = m.id_metodo
                    WHERE p.activo_presupuesto = 1
                    ORDER BY p.created_at_presupuesto DESC
                    LIMIT 1";
        
        $stmt_any = $conexion->prepare($sql_any);
        $stmt_any->execute();
        $ppto_any = $stmt_any->fetch(PDO::FETCH_ASSOC);
        
        if ($ppto_any) {
            echo "<p>Presupuesto encontrado: Nº <strong>" . htmlspecialchars($ppto_any['numero_presupuesto']) . "</strong></p>";
            echo "<p>Método actual: " . htmlspecialchars($ppto_any['nombre_metodo']) . "</p>";
            
            // Buscar método TRANSFERENCIA
            $sql_metodo = "SELECT id_metodo FROM metodo WHERE LOWER(nombre_metodo) LIKE '%transferencia%' LIMIT 1";
            $stmt_metodo = $conexion->prepare($sql_metodo);
            $stmt_metodo->execute();
            $metodo_transfer = $stmt_metodo->fetch(PDO::FETCH_ASSOC);
            
            if ($metodo_transfer) {
                echo "<p style='color: blue;'>Modificando presupuesto para usar TRANSFERENCIA...</p>";
                
                $sql_update_ppto = "UPDATE presupuesto SET id_metodo = ? WHERE id_presupuesto = ?";
                $stmt_update_ppto = $conexion->prepare($sql_update_ppto);
                $stmt_update_ppto->execute([$metodo_transfer['id_metodo'], $ppto_any['id_presupuesto']]);
                
                echo "<p style='color: green;'>✅ Presupuesto modificado correctamente</p>";
                echo "<p><a href='impresionpresupuesto_m2_pdf_es.php?id=" . $ppto_any['id_presupuesto'] . "' target='_blank'>Ver PDF actualizado</a></p>";
            } else {
                echo "<p style='color: red;'>❌ No se encontró método TRANSFERENCIA en la BD</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No hay presupuestos activos en la BD</p>";
        }
    }
    
    // 3. Test con modelo
    echo "<hr><h2>3. Test de modelo ImpresionPresupuesto:</h2>";
    
    $modelo = new ImpresionPresupuesto();
    $datos_empresa = $modelo->get_empresa_datos();
    
    if ($datos_empresa) {
        echo "<p style='color: green;'>✅ Modelo recupera datos correctamente</p>";
        echo "<p><strong>Campos bancarios en resultado:</strong></p>";
        echo "<ul>";
        echo "<li>iban_empresa: " . (isset($datos_empresa['iban_empresa']) ? '✅ Presente' : '❌ NO presente') . "</li>";
        echo "<li>swift_empresa: " . (isset($datos_empresa['swift_empresa']) ? '✅ Presente' : '❌ NO presente') . "</li>";
        echo "<li>banco_empresa: " . (isset($datos_empresa['banco_empresa']) ? '✅ Presente' : '❌ NO presente') . "</li>";
        echo "</ul>";
        
        if (isset($datos_empresa['iban_empresa'])) {
            echo "<p><strong>Valores:</strong></p>";
            echo "<ul>";
            echo "<li>IBAN: " . htmlspecialchars($datos_empresa['iban_empresa'] ?: 'VACÍO') . "</li>";
            echo "<li>SWIFT: " . htmlspecialchars($datos_empresa['swift_empresa'] ?: 'VACÍO') . "</li>";
            echo "<li>Banco: " . htmlspecialchars($datos_empresa['banco_empresa'] ?: 'VACÍO') . "</li>";
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ ERROR: Modelo no recupera datos</p>";
    }
    
    echo "<hr><h2>✅ Test completado</h2>";
    echo "<p><strong>Próximos pasos:</strong></p>";
    echo "<ol>";
    echo "<li>Verificar que los datos bancarios están configurados en empresa (arriba)</li>";
    echo "<li>Abrir un PDF de presupuesto con TRANSFERENCIA (links arriba)</li>";
    echo "<li>Verificar que aparece el bloque 'DATOS BANCARIOS PARA TRANSFERENCIA'</li>";
    echo "<li>Verificar que aparecen los 3 campos: Banco, IBAN, SWIFT</li>";
    echo "<li>Verificar que el IBAN está formateado con espacios cada 4 caracteres</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
