<?php
// Script temporal para crear directorios con permisos en el servidor Linux
// ELIMINAR tras ejecutar

$base = __DIR__ . '/public/documentos';
$dirs = ['proformas', 'anticipos', 'facturas'];

echo "<pre>\n";
echo "Usuario PHP (get_current_user): " . get_current_user() . "\n";
echo "Proceso www-data (posix):       " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'n/a') . "\n";
echo "UID efectivo:                   " . (function_exists('posix_geteuid') ? posix_geteuid() : 'n/a') . "\n\n";

// Intentar chown + chmod vía exec (requiere que www-data tenga sudo sin pass para este comando)
foreach ($dirs as $nombre) {
    $dir = "$base/$nombre";
    echo "--- $dir ---\n";

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "  mkdir: creado\n";
    }

    // Intentar chown y chmod vía shell
    $out = []; $ret = 0;
    exec("sudo chown www-data:www-data " . escapeshellarg($dir) . " 2>&1", $out, $ret);
    echo "  sudo chown: " . ($ret === 0 ? 'OK' : 'FALLÓ (' . implode(' ', $out) . ')') . "\n";

    $out = []; $ret = 0;
    exec("sudo chmod 775 " . escapeshellarg($dir) . " 2>&1", $out, $ret);
    echo "  sudo chmod: " . ($ret === 0 ? 'OK' : 'FALLÓ (' . implode(' ', $out) . ')') . "\n";

    echo "  Permisos finales: " . substr(sprintf('%o', fileperms($dir)), -4) . "\n";
    echo "  Escribible por PHP: " . (is_writable($dir) ? '✅ SÍ' : '❌ NO') . "\n\n";
}

// Mostrar ls -la del directorio padre
echo "--- ls -la $base ---\n";
passthru("ls -la " . escapeshellarg($base));

echo "\n\n⚠️  Si sigue sin ser escribible, ejecuta en el servidor via SSH:\n";
echo "   sudo chown -R www-data:www-data /var/www/html/MDR/public/documentos/\n";
echo "   sudo chmod -R 775 /var/www/html/MDR/public/documentos/\n";
echo "</pre>";
echo "<p style='color:red'><strong>IMPORTANTE: Elimina este archivo (setup_dirs.php) una vez ejecutado.</strong></p>";
