<?php
// Script temporal para verificar configuración PHP
echo "<h2>Configuración de subida de archivos</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Configuración</th><th>Valor</th></tr>";
echo "<tr><td>file_uploads</td><td>" . ini_get('file_uploads') . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "</table>";

echo "<h3>Recomendaciones:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize debería ser al menos 20M</li>";
echo "<li>post_max_size debería ser mayor que upload_max_filesize (25M recomendado)</li>";
echo "<li>memory_limit debería ser mayor que post_max_size (128M es común)</li>";
echo "</ul>";
?>
