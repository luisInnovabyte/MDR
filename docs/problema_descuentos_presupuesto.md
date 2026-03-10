# Problema: Descuentos de línea ignorados en cálculos SQL cuando empresa no lo permite

**Fecha detección**: 2026-03-10  
**Rama**: `pagos`  
**Commit solución**: `da9ed80`

---

## Contexto

La tabla `empresa` tiene el flag `permitir_descuentos_lineas_empresa` (TINYINT DEFAULT 1):

- `1` → Se permite editar el campo `% Dto` en las líneas de presupuesto. La columna de descuento aparece en el PDF y los cálculos aplican el porcentaje almacenado en `descuento_linea_ppto`.
- `0` → El campo queda bloqueado a 0 en la UI, la columna de descuento no aparece en el PDF, y los cálculos **deben ignorar** cualquier descuento que pueda existir en `descuento_linea_ppto`.

Este flag siempre se lee de la **empresa ficticia principal** (`WHERE empresa_ficticia_principal = 1 AND activo_empresa = 1`), no de `presupuesto.id_empresa` (que puede ser NULL).

---

## Problema detectado

Los KPIs de Base Imponible e IVA en la vista `lineasPresupuesto` mostraban valores incorrectos para presupuestos cuya empresa tenía `permitir_descuentos_lineas_empresa = 0`.

**Ejemplo real (versión 17, P-00007/2026)**:

| Campo | Valor incorrecto | Valor correcto |
|-------|-----------------|----------------|
| Base Imponible | 18.837,63 € | 19.049,00 € |
| IVA (21%) | 3.955,90 € | 4.000,29 € |

**Diferencia**: 211,37 € — exactamente el importe del descuento del 10 % aplicado a la línea 177 (artículo "Equipo de megafonía", coeficiente 4,75):

```
445 × 0,90 × 4,75 = 1.902,38 €  ← lo que calculaba la vista (con dto 10%)
445 × 1,00 × 4,75 = 2.113,75 €  ← lo correcto (sin dto, porque empresa no permite)
Diferencia        =   211,37 €
```

---

## Causa raíz

La vista SQL `v_linea_presupuesto_calculada` **siempre** multiplicaba por el factor `(1 - descuento_linea_ppto / 100)`, sin comprobar si la empresa permitía o no aplicar descuentos:

```sql
-- ANTES (incorrecto): aplica descuento siempre
(`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
 * (1 - (`lp`.`descuento_linea_ppto` / 100))
 * `lp`.`valor_coeficiente_linea_ppto`) AS `base_imponible`
```

Los PDFs y la UI sí respetaban el flag, pero lo hacían **en PHP**, recalculando los valores a posteriori. Esto creaba una inconsistencia: la vista devolvía un valor distinto al que usaban PDF y UI para renderizar.

---

## Dónde se aplica el flag correctamente (antes del fix)

| Componente | Respeta el flag | Mecanismo |
|---|---|---|
| `lineasPresupuesto.js` (UI) | ✅ | Campo readonly vía `window.permitirDescuentosEmpresa` |
| `impresionpresupuesto_m2_pdf_es.php` | ✅ | Recalcula `base_imponible` en PHP si `!$permitir_descuentos` |
| `impresionpresupuesto_m2_pdf_en.php` | ✅ | Ídem |
| `impresionpresupuestohotel_m2_pdf_es.php` | ✅ | Ídem |
| `impresion_factura_final.php` | ✅ | Ídem |
| `v_linea_presupuesto_calculada` (vista SQL) | ❌ | **No comprobaba el flag** |
| `v_presupuesto_totales` (vista SQL) | ❌ (derivado) | Usa SUM sobre `v_linea_presupuesto_calculada` |

---

## Solución aplicada

**Migración**: `BD/migrations/20260310_03_fix_vista_lineas_descuento_empresa.sql`

Se añadió un `LEFT JOIN` a la empresa ficticia principal en el `FROM` de `v_linea_presupuesto_calculada`:

```sql
-- JOIN añadido al FROM
LEFT JOIN `empresa` `e`
  ON (`e`.`empresa_ficticia_principal` = 1 AND `e`.`activo_empresa` = 1)
```

Y se sustituyó **en todos los campos calculados** el factor de descuento por una expresión condicional:

```sql
-- ANTES (incorrecto)
(1 - (`lp`.`descuento_linea_ppto` / 100))

-- DESPUÉS (correcto)
(1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
          `lp`.`descuento_linea_ppto`, 0) / 100))
```

El `COALESCE(..., 1)` garantiza comportamiento permisivo si el JOIN no encuentra empresa (backward compatibility).

**Campos afectados en la vista**:
- `subtotal_sin_coeficiente`
- `base_imponible`
- `importe_iva`
- `total_linea`
- `importe_descuento_linea_ppto_hotel`
- `TotalImporte_descuento_linea_ppto_hotel`
- `importe_iva_linea_ppto_hotel`
- `TotalImporte_iva_linea_ppto_hotel`

> **Nota**: `base_imponible_hotel` y `precio_unitario_linea_ppto_hotel` no usan `descuento_linea_ppto` — aplican el descuento de cliente (`porcentaje_descuento_cliente`), que es un mecanismo diferente y no se ve afectado.

---

## Patrón del bug — dónde puede replicarse

Cualquier vista SQL o query PHP que calcule importes a partir de `linea_presupuesto` y **no compruebe** `empresa.permitir_descuentos_lineas_empresa` puede producir resultados erróneos cuando el flag = 0.

### Puntos a revisar si aparece el mismo síntoma

1. **Vistas SQL derivadas de `v_linea_presupuesto_calculada`** — si se crea alguna vista nueva que haga SUM de `base_imponible`, heredará el valor ya corregido ✅ (siempre que use la vista, no la tabla directamente).

2. **Queries directas sobre `linea_presupuesto`** — cualquier SELECT que calcule `precio * cantidad * (1 - descuento/100)` sin JOIN a empresa debería seguir este patrón:
    ```sql
    SELECT 
      lp.cantidad_linea_ppto
      * lp.precio_unitario_linea_ppto
      * (1 - (IF(COALESCE(e.permitir_descuentos_lineas_empresa, 1) = 1,
                 lp.descuento_linea_ppto, 0) / 100))
    FROM linea_presupuesto lp
    ...
    LEFT JOIN empresa e ON (e.empresa_ficticia_principal = 1 AND e.activo_empresa = 1)
    ```

3. **Modelos PHP con cálculos manuales** — buscar cualquier cálculo del tipo:
    ```php
    $base = $cantidad * $precio * (1 - $descuento / 100);
    ```
    y verificar que exista una comprobación de `$permitir_descuentos` antes.

4. **`PagoPresupuesto::get_resumen_financiero()`** — este método ya tiene la lógica correcta implementada manualmente con un CASE sobre `e.permitir_descuentos_lineas_empresa`.

### Cómo leer el flag en PHP (patrón correcto)

```php
// Via modelo ImpresionPresupuesto (para PDFs y controladores)
$datos_empresa = $impresion->get_empresa_datos();
$permitir_descuentos = !isset($datos_empresa['permitir_descuentos_lineas_empresa'])
    ? true
    : ($datos_empresa['permitir_descuentos_lineas_empresa'] == 1);

// Via controller empresas.php?op=get_config_pdf (para JS)
// Devuelve: { success: true, permitir_descuentos_lineas_empresa: 0|1 }
```

### Cómo leer el flag en SQL (patrón correcto)

```sql
-- Siempre JOIN a empresa ficticia principal, no a presupuesto.id_empresa
LEFT JOIN empresa e ON (e.empresa_ficticia_principal = 1 AND e.activo_empresa = 1)

-- Usar COALESCE para backward compatibility (si no hay empresa, asumir permitido)
IF(COALESCE(e.permitir_descuentos_lineas_empresa, 1) = 1, lp.descuento_linea_ppto, 0)
```

---

## Diagnóstico

Para verificar si una versión de presupuesto está afectada, comparar los totales de la vista con los del PDF:

```sql
SELECT 
  id_version_presupuesto,
  ROUND(SUM(base_imponible), 2) AS base_vista,
  ROUND(SUM(importe_iva), 2)    AS iva_vista
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = ?
  AND activo_linea_ppto = 1
GROUP BY id_version_presupuesto;
```

Si `base_vista` coincide con lo que muestra el PDF → la vista está bien.  
Si difiere por exactamente la suma de `precio × coef × descuento%` de las líneas con descuento → el bug era la causa.
