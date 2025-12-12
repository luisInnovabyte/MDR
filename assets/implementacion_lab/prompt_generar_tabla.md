# Prompt: Generar Tabla MySQL

> Prompt reutilizable para solicitar la creación de tablas siguiendo los estándares del proyecto

---

## Formato de solicitud

Copia y completa esta plantilla cada vez que necesites crear una nueva tabla:

```
NUEVA TABLA
===========
Nombre: <<nombre_en_singular>>
Descripción: <<descripción breve del propósito>>

CAMPOS:
- <<nombre_campo>>: <<descripción>> [obligatorio|opcional]
- <<nombre_campo>>: <<descripción>> [obligatorio|opcional]
- ...

RELACIONES:
- FK a <<tabla>>: [obligatoria|opcional] - <<descripción>>
- ...
(o "Ninguna" si no tiene)

ÚNICOS:
- <<campo>>
- ...
(o "Ninguno" si no aplica)

ENUM (si aplica):
- <<campo>>: valor1, valor2, valor3
```

---

## Ejemplo completo

```
NUEVA TABLA
===========
Nombre: proveedor
Descripción: Proveedores de artículos del catálogo

CAMPOS:
- nombre: Nombre comercial [obligatorio]
- razon_social: Razón social completa [opcional]
- nif: Número de identificación fiscal [opcional]
- email: Correo electrónico principal [opcional]
- telefono: Teléfono de contacto [opcional]
- direccion: Dirección postal [opcional]
- persona_contacto: Nombre del contacto principal [opcional]
- web: Sitio web [opcional]
- observaciones: Notas internas [opcional]

RELACIONES:
- Ninguna

ÚNICOS:
- nif
- email
```

---

## Ejemplo con relaciones

```
NUEVA TABLA
===========
Nombre: factura
Descripción: Facturas emitidas a clientes

CAMPOS:
- numero: Número de factura [obligatorio]
- fecha: Fecha de emisión [obligatorio]
- fecha_vencimiento: Fecha de vencimiento [opcional]
- base_imponible: Importe sin IVA [obligatorio]
- iva: Porcentaje de IVA aplicado [obligatorio]
- total: Importe total con IVA [obligatorio]
- observaciones: Notas de la factura [opcional]
- estado: Estado de la factura [obligatorio]

RELACIONES:
- FK a cliente: obligatoria - Cliente al que se emite
- FK a pedido: opcional - Pedido asociado si existe

ÚNICOS:
- numero

ENUM:
- estado: borrador, emitida, pagada, vencida, anulada
```

---

## Ejemplo tabla pivote (N:M)

```
NUEVA TABLA
===========
Nombre: usuario_rol
Descripción: Relación muchos a muchos entre usuarios y roles

CAMPOS:
- (solo las FK, es tabla pivote)

RELACIONES:
- FK a usuario: obligatoria - CASCADE en DELETE
- FK a rol: obligatoria - CASCADE en DELETE

ÚNICOS:
- Combinación usuario + rol
```

---

## Lo que Claude generará automáticamente

Al recibir tu solicitud, aplicaré:

1. **Nomenclatura**: Sufijo `_<<tabla>>` a todos los campos
2. **Tipos de datos**: Según `03_tipos_campos_estandar.md`
3. **Campos obligatorios**: `id_`, `activo_`, `created_at_`, `updated_at_`
4. **Índices**: PK, índices en FK, UNIQUE donde corresponda
5. **Foreign Keys**: Con ON DELETE/UPDATE apropiados
6. **Configuración**: `ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci`

---

## Respuesta esperada

Recibirás:

1. **SQL completo** listo para ejecutar
2. **Comentarios** con nombre de tabla, descripción y fecha
3. **Checklist** verificando que cumple todos los estándares

---

*Documento: 06-01 | Última actualización: Diciembre 2024*
