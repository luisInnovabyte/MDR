# Reglas de Actuación - Base de Datos MDR

> Protocolo para modificaciones y nuevas incorporaciones al proyecto

---

## Principio Fundamental

- **Tablas existentes**: Se respetan íntegramente. No se modifican convenciones, nombres ni estructura salvo petición expresa.
- **Tablas nuevas**: Se crean siguiendo los estándares de la documentación del proyecto (convenciones de nomenclatura, campos obligatorios, índices, FK, charset).

---

## Cómo Solicitar Cambios

### Para MODIFICAR tabla existente

```
MODIFICAR TABLA EXISTENTE
=========================
Estructura actual:
[pegar el CREATE TABLE completo de la tabla]

Cambios necesarios:
[describir qué campos añadir, modificar o eliminar]

Motivo:
[qué funcionalidad requiere el cliente]
```

Claude generará el `ALTER TABLE` respetando la estructura y convenciones ya existentes en esa tabla.

---

### Para CREAR tabla nueva

```
NUEVA TABLA
===========
Nombre: <<nombre_en_singular>>
Descripción: <<propósito de la tabla>>

CAMPOS:
- <<nombre>>: <<descripción>> [obligatorio|opcional]
- <<nombre>>: <<descripción>> [obligatorio|opcional]

RELACIONES:
- FK a <<tabla>>: [obligatoria|opcional] - <<descripción>>
(o "Ninguna")

ÚNICOS:
- <<campo>>
(o "Ninguno")

ENUM (si aplica):
- <<campo>>: valor1, valor2, valor3
```

Claude generará el `CREATE TABLE` aplicando los estándares de la documentación.

---

## Lo que Claude Aplica Automáticamente en Tablas Nuevas

1. Sufijo `_<<tabla>>` en todos los campos
2. Campos obligatorios: `id_`, `activo_`, `created_at_`, `updated_at_`
3. Tipos de datos según estándar (DECIMAL para dinero, VARCHAR apropiados, etc.)
4. Índices en FK y campos de búsqueda frecuente
5. Foreign Keys con ON DELETE/UPDATE apropiados
6. `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci`

---

## Resumen Rápido

| Situación | Acción |
|-----------|--------|
| Modificar tabla existente | Pasar CREATE TABLE actual → Respetar sus convenciones |
| Crear tabla nueva | Usar plantilla → Aplicar estándares documentados |
| Duda sobre convención existente | Preguntar o pasar ejemplo de tabla similar del proyecto |

---

*Última actualización: Diciembre 2024*
