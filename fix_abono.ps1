$f='w:\MDR\controller\impresion_factura_abono.php'
$lines=[System.IO.File]::ReadAllLines($f,[Text.Encoding]::UTF8)
Write-Host "Lines: $($lines.Count)"
for($i=627;$i-le634;$i++){Write-Host "$($i+1): $($lines[$i])"}

# ─── 1. Cambiar texto del comentario ───────────────────────────────────────
$t = $t.Replace('TOTAL A DEVOLVER (fondo rojo)', 'TOTAL (fondo rojo)')

# ─── 2. Cambiar etiqueta de la celda ───────────────────────────────────────
$t = $t.Replace("'TOTAL A DEVOLVER:'", "'TOTAL:'")

# ─── 3. Añadir SetFont B8 antes de la celda de importe ─────────────────────
$after_label = "    `$pdf->Cell(`$w_label,  10, 'TOTAL:', 1, 0, 'R', true);"
$new_font_line = $CRLF + "    `$pdf->SetFont('helvetica', 'B', 8);"
$t = $t.Replace($after_label, $after_label + $new_font_line)

# ─── 4. Eliminar bloque bancario usando IndexOf (sin regex) ─────────────────
# Anclaje inicio: $mostrar_banco (único en el fichero)
$start_anchor = 'mostrar_banco = '
$end_anchor   = '    $pdf->SetAutoPageBreak(true, 25);'

$idx_mostrar = $t.IndexOf($start_anchor)
if ($idx_mostrar -lt 0) { Write-Host "ERROR: no se encuentra mostrar_banco"; exit 1 }

# Retroceder para encontrar el doble CRLF (línea en blanco) justo antes del comentario
$blank_pair = $CRLF + $CRLF
$idx_blank = $t.LastIndexOf($blank_pair, $idx_mostrar)
if ($idx_blank -lt 0) { Write-Host "ERROR: no se encuentra linea en blanco antes del bloque"; exit 1 }

# Posición del final del bloque (primera ocurrencia de SetAutoPageBreak después del bloque)
$idx_end = $t.IndexOf($end_anchor, $idx_mostrar)
if ($idx_end -lt 0) { Write-Host "ERROR: no se encuentra SetAutoPageBreak"; exit 1 }

# Sustituir: conservar hasta idx_blank, luego salto + SetAutoPageBreak...
$t = $t.Substring(0, $idx_blank) + $CRLF + $CRLF + $t.Substring($idx_end)

[System.IO.File]::WriteAllText($f, $t, $enc)
Write-Host "OK: cambios aplicados correctamente"
