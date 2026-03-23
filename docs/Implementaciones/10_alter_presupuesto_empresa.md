# Modificación Tabla Presupuesto - Campo Empresa Emisora

## Objetivo

Añadir el campo `id_empresa` a la tabla `presupuesto` para vincular cada presupuesto con la empresa que lo emite, soportando tanto empresas ficticias (para presupuestos) como empresas reales (para facturación).

---

## Script SQL

```sql
-- ========================================================
-- MODIFICACIÓN TABLA PRESUPUESTO - Añadir Empresa Emisora
-- ========================================================
-- Descripción: Añade el campo id_empresa para vincular cada
--              presupuesto con la empresa que lo emite
-- Fecha: 2025-01-18
-- Autor: Luis - MDR ERP Manager
-- ========================================================

ALTER TABLE presupuesto
    -- Campo empresa emisora
    ADD COLUMN id_empresa INT UNSIGNED 
        COMMENT 'Empresa que emite el presupuesto (ficticia o real)'
        AFTER id_presupuesto,
    
    -- Foreign Key a tabla empresa
    ADD CONSTRAINT fk_presupuesto_empresa 
        FOREIGN KEY (id_empresa) 
        REFERENCES empresa(id_empresa) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- Índice para optimizar consultas
    ADD INDEX idx_id_empresa_presupuesto (id_empresa);
```

---

## Propósito

Este campo permite identificar qué empresa (ficticia o real) emite cada presupuesto. Es fundamental para:

### 1. **Presupuestos con Empresa Ficticia**
- Usar empresas ficticias sin CIF real para presupuestos
- Evitar compromisos legales antes de la aprobación
- Mantener flexibilidad comercial

### 2. **Facturación con Empresa Real**
- Migrar a empresa real cuando el cliente aprueba
- Cumplir normativa fiscal española (VeriFact)
- Facturar con datos legales correctos

### 3. **Multi-empresa**
- Soportar varios grupos empresariales
- Diferentes razones sociales según el servicio
- Gestión centralizada desde un solo sistema

### 4. **Numeración Independiente**
- Cada empresa mantiene su propia serie de numeración
- Evita confusiones entre diferentes empresas
- Facilita la contabilidad separada

---

## Flujo de Trabajo

```
1. CREAR PRESUPUESTO
   └─> Se asigna automáticamente la empresa ficticia principal
   └─> Número generado: P-00001/2025 (serie de empresa ficticia)

2. CLIENTE APRUEBA
   └─> OPCIONAL: Cambiar a empresa real para facturar
   └─> Mantener trazabilidad del presupuesto original

3. FACTURACIÓN
   └─> Usar empresa real con CIF válido
   └─> Factura vinculada al presupuesto original
```

---

## Relación con Triggers Existentes

El trigger `trg_presupuesto_before_insert` **ya utiliza** la empresa ficticia principal para generar el número de presupuesto:

```sql
DELIMITER //

CREATE TRIGGER trg_presupuesto_before_insert
BEFORE INSERT ON presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_serie VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio VARCHAR(4);
    DECLARE v_id_empresa INT UNSIGNED;
    
    SET v_anio = YEAR(CURDATE());
    
    -- Obtener la empresa ficticia principal
    SELECT 
        id_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa + 1
    INTO 
        v_id_empresa,
        v_serie,
        v_numero_actual
    FROM empresa
    WHERE empresa_ficticia_principal = TRUE
    AND activo_empresa = TRUE
    LIMIT 1;
    
    -- Generar número: P-00001/2025
    SET NEW.numero_presupuesto = CONCAT(
        v_serie, '-',
        LPAD(v_numero_actual, 5, '0'),
        '/', v_anio
    );
    
    -- Actualizar contador
    UPDATE empresa 
    SET numero_actual_presupuesto_empresa = v_numero_actual
    WHERE id_empresa = v_id_empresa;
    
    -- AHORA: Almacenar el id_empresa usado
    SET NEW.id_empresa = v_id_empresa;
    
END//

DELIMITER ;
```

**El campo `id_empresa` almacena ese valor para trazabilidad y auditoría.**

---

## Migración de Datos Existentes

Si ya tienes presupuestos creados antes de añadir este campo, ejecuta:

### Paso 1: Ejecutar el ALTER TABLE

```sql
ALTER TABLE presupuesto
    ADD COLUMN id_empresa INT UNSIGNED 
        COMMENT 'Empresa que emite el presupuesto (ficticia o real)'
        AFTER id_presupuesto,
    ADD CONSTRAINT fk_presupuesto_empresa 
        FOREIGN KEY (id_empresa) 
        REFERENCES empresa(id_empresa) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    ADD INDEX idx_id_empresa_presupuesto (id_empresa);
```

### Paso 2: Rellenar datos históricos

```sql
-- Asignar empresa ficticia principal a presupuestos sin empresa
UPDATE presupuesto p
SET p.id_empresa = (
    SELECT id_empresa 
    FROM empresa 
    WHERE empresa_ficticia_principal = TRUE 
    AND activo_empresa = TRUE
    LIMIT 1
)
WHERE p.id_empresa IS NULL;
```

### Paso 3: (Opcional) Hacer campo obligatorio

```sql
-- Solo si quieres que sea NOT NULL
ALTER TABLE presupuesto
MODIFY id_empresa INT UNSIGNED NOT NULL 
    COMMENT 'Empresa que emite el presupuesto (ficticia o real)';
```

---

## Validación Post-Migración

### Verificar que todos los presupuestos tienen empresa

```sql
SELECT 
    COUNT(*) AS total_presupuestos,
    COUNT(id_empresa) AS con_empresa,
    COUNT(*) - COUNT(id_empresa) AS sin_empresa
FROM presupuesto;
```

**Resultado esperado:**
```
total_presupuestos | con_empresa | sin_empresa
------------------ | ----------- | -----------
         100       |     100     |      0
```

### Ver distribución de presupuestos por empresa

```sql
SELECT 
    e.nombre_empresa,
    e.ficticia_empresa,
    COUNT(p.id_presupuesto) AS total_presupuestos
FROM empresa e
LEFT JOIN presupuesto p ON e.id_empresa = p.id_empresa
WHERE e.activo_empresa = TRUE
GROUP BY e.id_empresa, e.nombre_empresa, e.ficticia_empresa
ORDER BY total_presupuestos DESC;
```

---

## Consultas Útiles

### Presupuestos por empresa ficticia vs. real

```sql
SELECT 
    CASE 
        WHEN e.ficticia_empresa = TRUE THEN 'Empresa Ficticia'
        ELSE 'Empresa Real'
    END AS tipo_empresa,
    e.nombre_empresa,
    COUNT(p.id_presupuesto) AS total_presupuestos,
    SUM(CASE WHEN p.estado_general_presupuesto = 'aprobado' THEN 1 ELSE 0 END) AS aprobados
FROM presupuesto p
INNER JOIN empresa e ON p.id_empresa = e.id_empresa
GROUP BY e.ficticia_empresa, e.nombre_empresa
ORDER BY tipo_empresa, total_presupuestos DESC;
```

### Presupuestos pendientes de migrar a empresa real

```sql
-- Presupuestos aprobados que aún están con empresa ficticia
SELECT 
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.estado_general_presupuesto,
    c.nombre_cliente,
    e.nombre_empresa AS empresa_actual,
    e.ficticia_empresa
FROM presupuesto p
INNER JOIN empresa e ON p.id_empresa = e.id_empresa
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
WHERE p.estado_general_presupuesto = 'aprobado'
AND e.ficticia_empresa = TRUE
ORDER BY p.fecha_presupuesto;
```

---

## Consideraciones de Diseño

### ¿Por qué INT UNSIGNED?

- Coherencia con el tipo de dato de `empresa.id_empresa`
- Soporta hasta 4.294.967.295 empresas
- Optimiza el espacio de almacenamiento

### ¿Por qué RESTRICT en ON DELETE?

- No se puede eliminar una empresa si tiene presupuestos asociados
- Protege la integridad de datos históricos
- Requiere mover presupuestos antes de eliminar empresa

### ¿Por qué después de id_presupuesto?

- Agrupa campos de identificación al inicio de la tabla
- Facilita lectura de la estructura
- Estándar del proyecto MDR

---

## Actualización del Trigger (Recomendado)

Aunque el trigger ya funciona, es recomendable actualizarlo para asignar explícitamente el `id_empresa`:

```sql
DROP TRIGGER IF EXISTS trg_presupuesto_before_insert;

DELIMITER //

CREATE TRIGGER trg_presupuesto_before_insert
BEFORE INSERT ON presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_serie VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio VARCHAR(4);
    DECLARE v_id_empresa INT UNSIGNED;
    
    SET v_anio = YEAR(CURDATE());
    
    -- Obtener la empresa ficticia principal
    SELECT 
        id_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa + 1
    INTO 
        v_id_empresa,
        v_serie,
        v_numero_actual
    FROM empresa
    WHERE empresa_ficticia_principal = TRUE
    AND activo_empresa = TRUE
    LIMIT 1;
    
    -- Verificar que encontramos la empresa
    IF v_id_empresa IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se encontró empresa ficticia principal activa';
    END IF;
    
    -- Asignar empresa al presupuesto (NUEVO)
    SET NEW.id_empresa = v_id_empresa;
    
    -- Generar número de presupuesto
    SET NEW.numero_presupuesto = CONCAT(
        v_serie, '-',
        LPAD(v_numero_actual, 5, '0'),
        '/', v_anio
    );
    
    -- Actualizar contador
    UPDATE empresa 
    SET numero_actual_presupuesto_empresa = v_numero_actual
    WHERE id_empresa = v_id_empresa;
    
END//

DELIMITER ;
```

---

## Impacto en el Sistema

### Tablas Afectadas
- ✅ `presupuesto` - Campo añadido
- ✅ `empresa` - Referenciada por FK

### Vistas Afectadas
- ⚠️ `vista_presupuesto_completa` - Considerar añadir datos de empresa
- ⚠️ Cualquier vista que use `presupuesto`

### Triggers Afectados
- ✅ `trg_presupuesto_before_insert` - Actualizar (opcional pero recomendado)

### Stored Procedures Afectados
- ✅ `sp_actualizar_contador_empresa` - Ya contempla empresas
- ✅ `sp_obtener_siguiente_numero` - Ya contempla empresas

### Aplicación PHP
- 📝 Controllers - Actualizar para manejar `id_empresa`
- 📝 Models - Añadir campo al modelo `Presupuesto`
- 📝 Views - Mostrar empresa en formularios si es necesario

---

## Checklist de Implementación

- [ ] Ejecutar `ALTER TABLE` en base de datos de desarrollo
- [ ] Ejecutar script de migración de datos históricos
- [ ] Validar que todos los presupuestos tienen empresa
- [ ] Actualizar trigger `trg_presupuesto_before_insert`
- [ ] Actualizar vista `vista_presupuesto_completa` (si aplica)
- [ ] Actualizar modelo PHP `Presupuesto.php`
- [ ] Actualizar controllers que crean/editan presupuestos
- [ ] Probar creación de nuevo presupuesto
- [ ] Probar migración de empresa ficticia a real
- [ ] Ejecutar en producción con backup previo
- [ ] Documentar en manual de usuario

---

*Documento: 10_alter_presupuesto_empresa.md | Versión: 1.0 | Fecha: 18 Enero 2025*
