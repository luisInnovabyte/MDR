# Triggers MySQL

> Estándar para triggers de generación de códigos y cálculos automáticos

---

## Nomenclatura de Triggers

### Formato estándar

```
<<tabla>>_before_insert
<<tabla>>_after_insert
<<tabla>>_before_update
<<tabla>>_after_update
<<tabla>>_before_delete
<<tabla>>_after_delete
```

### Ejemplos válidos

```
cliente_before_insert        -- Genera código de cliente
pedido_before_insert         -- Genera número de pedido
factura_before_insert        -- Genera número de factura
linea_pedido_after_insert    -- Actualiza total en cabecera
linea_pedido_after_update    -- Recalcula total en cabecera
linea_pedido_after_delete    -- Recalcula total en cabecera
```

---

## Caso 1: Generación de Códigos Secuenciales

### Propósito

Generar automáticamente códigos únicos que el usuario no debe manipular pero sí ver.

### Plantilla base

```sql
-- ============================================
-- Trigger: <<tabla>>_before_insert
-- Descripción: Genera código secuencial para <<tabla>>
-- Formato código: <<describir formato>>
-- Fecha: <<fecha_creación>>
-- ============================================

DELIMITER //

CREATE TRIGGER <<tabla>>_before_insert
BEFORE INSERT ON <<tabla>>
FOR EACH ROW
BEGIN
    DECLARE nuevo_secuencial INT;
    
    -- Obtener el siguiente secuencial
    SELECT IFNULL(MAX(<<campo_secuencial>>), 0) + 1 
    INTO nuevo_secuencial
    FROM <<tabla>>;
    
    -- Asignar el código con formato
    SET NEW.codigo_<<tabla>> = CONCAT('<<PREFIJO>>', LPAD(nuevo_secuencial, <<digitos>>, '0'));
    
    -- Guardar el secuencial si existe campo separado
    -- SET NEW.secuencial_<<tabla>> = nuevo_secuencial;
END //

DELIMITER ;
```

---

### Ejemplo 1: Código simple (CLI-00001)

```sql
-- ============================================
-- Trigger: cliente_before_insert
-- Descripción: Genera código de cliente
-- Formato código: CLI-00001, CLI-00002, ...
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER cliente_before_insert
BEFORE INSERT ON cliente
FOR EACH ROW
BEGIN
    DECLARE nuevo_secuencial INT;
    
    -- Obtener el siguiente secuencial
    SELECT IFNULL(MAX(secuencial_cliente), 0) + 1 
    INTO nuevo_secuencial
    FROM cliente;
    
    -- Asignar valores
    SET NEW.secuencial_cliente = nuevo_secuencial;
    SET NEW.codigo_cliente = CONCAT('CLI-', LPAD(nuevo_secuencial, 5, '0'));
END //

DELIMITER ;
```

**Campos necesarios en la tabla:**
```sql
codigo_cliente VARCHAR(20) NOT NULL,
secuencial_cliente INT NOT NULL,
```

---

### Ejemplo 2: Código con año (PED-2024-00001)

```sql
-- ============================================
-- Trigger: pedido_before_insert
-- Descripción: Genera número de pedido con año
-- Formato código: PED-2024-00001 (reinicia cada año)
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER pedido_before_insert
BEFORE INSERT ON pedido
FOR EACH ROW
BEGIN
    DECLARE nuevo_secuencial INT;
    DECLARE anio_actual INT;
    
    -- Obtener año actual
    SET anio_actual = YEAR(CURRENT_DATE);
    
    -- Obtener el siguiente secuencial del año actual
    SELECT IFNULL(MAX(secuencial_pedido), 0) + 1 
    INTO nuevo_secuencial
    FROM pedido
    WHERE YEAR(created_at_pedido) = anio_actual;
    
    -- Asignar valores
    SET NEW.secuencial_pedido = nuevo_secuencial;
    SET NEW.numero_pedido = CONCAT('PED-', anio_actual, '-', LPAD(nuevo_secuencial, 5, '0'));
END //

DELIMITER ;
```

**Campos necesarios en la tabla:**
```sql
numero_pedido VARCHAR(20) NOT NULL,
secuencial_pedido INT NOT NULL,
```

---

### Ejemplo 3: Código con prefijo y separador alternativo (FAC/2024/0001)

```sql
-- ============================================
-- Trigger: factura_before_insert
-- Descripción: Genera número de factura
-- Formato código: FAC/2024/0001 (reinicia cada año)
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER factura_before_insert
BEFORE INSERT ON factura
FOR EACH ROW
BEGIN
    DECLARE nuevo_secuencial INT;
    DECLARE anio_actual INT;
    
    SET anio_actual = YEAR(CURRENT_DATE);
    
    SELECT IFNULL(MAX(secuencial_factura), 0) + 1 
    INTO nuevo_secuencial
    FROM factura
    WHERE YEAR(created_at_factura) = anio_actual;
    
    SET NEW.secuencial_factura = nuevo_secuencial;
    SET NEW.numero_factura = CONCAT('FAC/', anio_actual, '/', LPAD(nuevo_secuencial, 4, '0'));
END //

DELIMITER ;
```

---

### Ejemplo 4: Código con tipo incluido (ALB-E-00001, ALB-S-00001)

```sql
-- ============================================
-- Trigger: albaran_before_insert
-- Descripción: Genera número de albarán según tipo
-- Formato código: ALB-E-00001 (entrada), ALB-S-00001 (salida)
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER albaran_before_insert
BEFORE INSERT ON albaran
FOR EACH ROW
BEGIN
    DECLARE nuevo_secuencial INT;
    DECLARE prefijo_tipo VARCHAR(1);
    
    -- Determinar prefijo según tipo
    IF NEW.tipo_albaran = 'entrada' THEN
        SET prefijo_tipo = 'E';
    ELSE
        SET prefijo_tipo = 'S';
    END IF;
    
    -- Secuencial independiente por tipo
    SELECT IFNULL(MAX(secuencial_albaran), 0) + 1 
    INTO nuevo_secuencial
    FROM albaran
    WHERE tipo_albaran = NEW.tipo_albaran;
    
    SET NEW.secuencial_albaran = nuevo_secuencial;
    SET NEW.numero_albaran = CONCAT('ALB-', prefijo_tipo, '-', LPAD(nuevo_secuencial, 5, '0'));
END //

DELIMITER ;
```

---

## Caso 2: Cálculo de Totales en Cabeceras

### Propósito

Mantener actualizados automáticamente los totales en tablas cabecera cuando se modifican las líneas de detalle.

### Plantilla base

```sql
-- ============================================
-- Trigger: <<tabla_linea>>_after_insert
-- Descripción: Actualiza total en <<tabla_cabecera>>
-- Fecha: <<fecha_creación>>
-- ============================================

DELIMITER //

CREATE TRIGGER <<tabla_linea>>_after_insert
AFTER INSERT ON <<tabla_linea>>
FOR EACH ROW
BEGIN
    UPDATE <<tabla_cabecera>>
    SET total_<<tabla_cabecera>> = (
        SELECT IFNULL(SUM(total_<<tabla_linea>>), 0)
        FROM <<tabla_linea>>
        WHERE id_<<tabla_cabecera>> = NEW.id_<<tabla_cabecera>>
        AND activo_<<tabla_linea>> = 1
    )
    WHERE id_<<tabla_cabecera>> = NEW.id_<<tabla_cabecera>>;
END //

DELIMITER ;
```

---

### Ejemplo completo: Totales de pedido

```sql
-- ============================================
-- Trigger: linea_pedido_after_insert
-- Descripción: Actualiza total del pedido al insertar línea
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER linea_pedido_after_insert
AFTER INSERT ON linea_pedido
FOR EACH ROW
BEGIN
    UPDATE pedido
    SET total_pedido = (
        SELECT IFNULL(SUM(total_linea_pedido), 0)
        FROM linea_pedido
        WHERE id_pedido = NEW.id_pedido
        AND activo_linea_pedido = 1
    )
    WHERE id_pedido = NEW.id_pedido;
END //

DELIMITER ;

-- ============================================
-- Trigger: linea_pedido_after_update
-- Descripción: Actualiza total del pedido al modificar línea
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER linea_pedido_after_update
AFTER UPDATE ON linea_pedido
FOR EACH ROW
BEGIN
    -- Recalcular si cambió algún valor que afecte al total
    IF OLD.total_linea_pedido != NEW.total_linea_pedido 
       OR OLD.activo_linea_pedido != NEW.activo_linea_pedido THEN
        
        UPDATE pedido
        SET total_pedido = (
            SELECT IFNULL(SUM(total_linea_pedido), 0)
            FROM linea_pedido
            WHERE id_pedido = NEW.id_pedido
            AND activo_linea_pedido = 1
        )
        WHERE id_pedido = NEW.id_pedido;
    END IF;
END //

DELIMITER ;

-- ============================================
-- Trigger: linea_pedido_after_delete
-- Descripción: Actualiza total del pedido al eliminar línea
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER linea_pedido_after_delete
AFTER DELETE ON linea_pedido
FOR EACH ROW
BEGIN
    UPDATE pedido
    SET total_pedido = (
        SELECT IFNULL(SUM(total_linea_pedido), 0)
        FROM linea_pedido
        WHERE id_pedido = OLD.id_pedido
        AND activo_linea_pedido = 1
    )
    WHERE id_pedido = OLD.id_pedido;
END //

DELIMITER ;
```

---

## Caso 3: Cálculo de Total en Línea

### Propósito

Calcular automáticamente el total de cada línea antes de insertar/actualizar.

```sql
-- ============================================
-- Trigger: linea_pedido_before_insert
-- Descripción: Calcula total de línea antes de insertar
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER linea_pedido_before_insert
BEFORE INSERT ON linea_pedido
FOR EACH ROW
BEGIN
    -- Calcular total: (precio * cantidad) - descuento
    SET NEW.total_linea_pedido = 
        (NEW.precio_unitario_linea_pedido * NEW.cantidad_linea_pedido) 
        - IFNULL(NEW.descuento_linea_pedido, 0);
END //

DELIMITER ;

-- ============================================
-- Trigger: linea_pedido_before_update
-- Descripción: Recalcula total de línea antes de actualizar
-- Fecha: 2024-12-01
-- ============================================

DELIMITER //

CREATE TRIGGER linea_pedido_before_update
BEFORE UPDATE ON linea_pedido
FOR EACH ROW
BEGIN
    SET NEW.total_linea_pedido = 
        (NEW.precio_unitario_linea_pedido * NEW.cantidad_linea_pedido) 
        - IFNULL(NEW.descuento_linea_pedido, 0);
END //

DELIMITER ;
```

---

## Resumen de Triggers por Tabla

### Tabla con código autogenerado

| Trigger | Función |
|---------|---------|
| `<<tabla>>_before_insert` | Genera código secuencial |

### Tabla cabecera con líneas

| Trigger | Función |
|---------|---------|
| `<<tabla_cabecera>>_before_insert` | Genera código/número |
| `<<tabla_linea>>_before_insert` | Calcula total de línea |
| `<<tabla_linea>>_before_update` | Recalcula total de línea |
| `<<tabla_linea>>_after_insert` | Actualiza total cabecera |
| `<<tabla_linea>>_after_update` | Actualiza total cabecera |
| `<<tabla_linea>>_after_delete` | Actualiza total cabecera |

---

## Verificar Triggers Existentes

```sql
-- Ver todos los triggers de la base de datos
SHOW TRIGGERS;

-- Ver triggers de una tabla específica
SHOW TRIGGERS WHERE `Table` = 'nombre_tabla';

-- Ver definición de un trigger
SHOW CREATE TRIGGER nombre_trigger;
```

---

## Eliminar y Recrear Triggers

```sql
-- Eliminar trigger existente
DROP TRIGGER IF EXISTS cliente_before_insert;

-- Luego ejecutar el CREATE TRIGGER nuevamente
```

---

## Prompt para Solicitar Triggers

### Para generación de código

```
NUEVO TRIGGER - CÓDIGO
======================
Tabla: <<nombre>>
Formato código: <<ejemplo: CLI-00001, PED/2024/0001>>
¿Reinicia por año?: [sí|no]
¿Depende de algún campo?: [no | campo y valores]
```

### Para cálculo de totales

```
NUEVO TRIGGER - TOTALES
=======================
Tabla cabecera: <<nombre>>
Tabla líneas: <<nombre>>
Campo total cabecera: <<nombre>>
Campo total línea: <<nombre>>
Fórmula línea (si aplica): <<precio * cantidad - descuento>>
```

---

## Buenas Prácticas

1. **Siempre usar DELIMITER** para triggers multilínea
2. **IFNULL()** para evitar problemas con valores NULL
3. **Filtrar por activo = 1** en sumas para respetar soft delete
4. **Guardar secuencial** en campo separado para facilitar consultas
5. **Documentar formato** del código en el comentario de cabecera
6. **Verificar duplicados** antes de crear (DROP IF EXISTS)

---

*Documento: 02-08 | Última actualización: Diciembre 2024*
