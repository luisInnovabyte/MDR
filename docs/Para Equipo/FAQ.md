# ❓ Preguntas Frecuentes (FAQ)

> Respuestas rápidas a las dudas más comunes del equipo

---

## 📚 ÍNDICE

- [General](#general)
- [Base de Datos](#base-de-datos)
- [Backend PHP](#backend-php)
- [Convenciones y Estándares](#convenciones-y-estándares)
- [Problemas Comunes](#problemas-comunes)
- [Soporte](#soporte)

---

## 🌐 GENERAL

### ¿Tengo que leer documentación técnica extensa?

**NO.** El sistema está diseñado para que uses comandos simples. Claude aplica automáticamente todos los estándares técnicos sin que necesites conocer los detalles internos.

---

### ¿Puedo ver los estándares completos de la empresa?

Los estándares técnicos son **propiedad intelectual de la empresa** y se aplican automáticamente cuando usas los comandos. Solo necesitas saber usar los comandos del archivo `comandos_rapidos.md`.

---

### ¿Puedo modificar el código generado?

**SÍ**, pero te recomendamos:
- ✅ Mantener la estructura base (nombres de campos, métodos estándar)
- ✅ Respetar la nomenclatura de tablas y campos
- ✅ No eliminar campos obligatorios del sistema
- ❌ No cambiar convenciones de nombres (rompe consistencia)

---

### ¿Qué hago si el comando no genera lo esperado?

1. **Verifica el formato:** Asegúrate de seguir exactamente el formato del comando
2. **Revisa los ejemplos:** Compara con los ejemplos en `comandos_rapidos.md`
3. **Información completa:** Verifica que incluiste todos los datos necesarios
4. **Consulta:** Si persiste el problema, contacta al líder técnico

---

### ¿Por qué el código tiene cierto formato específico?

El código sigue **estándares internos** de la empresa diseñados para garantizar:
- **Consistencia** en todo el proyecto
- **Mantenibilidad** a largo plazo
- **Escalabilidad** cuando el proyecto crezca
- **Calidad** de código profesional

No necesitas conocer los detalles técnicos, solo confiar en que el código generado cumple todos los requisitos.

---

## 🗄️ BASE DE DATOS

### ¿Por qué las tablas tienen nombres en singular?

Es parte del estándar. Ejemplo: `empleado` en lugar de `empleados`. Esto facilita la comprensión del código y las relaciones entre tablas.

---

### ¿Por qué todos los campos terminan en `_tabla`?

**Ventajas de este estándar:**
- ✅ **Sin ambigüedad en JOINs:** Cada campo es único, no necesitas alias
- ✅ **Autocompletado eficiente:** Escribes el sufijo y filtras por tabla
- ✅ **Trazabilidad:** Sabes de qué tabla viene cada campo en cualquier consulta

**Ejemplo:**
```sql
SELECT 
    nombre_empleado,
    nombre_departamento,
    codigo_empleado,
    codigo_departamento
FROM empleado
JOIN departamento ON empleado.id_departamento = departamento.id_departamento;
```

Sin sufijos sería ambiguo cuál `nombre` o `codigo` pertenece a qué tabla.

---

### ¿Qué son los campos `created_at` y `updated_at`?

Son **campos obligatorios de auditoría** que se añaden automáticamente a todas las tablas:

- `created_at_tabla`: Guarda cuándo se creó el registro (automático)
- `updated_at_tabla`: Guarda cuándo se modificó por última vez (automático)

**Beneficios:**
- Trazabilidad completa de cambios
- Requisito para auditorías
- Útil para reportes y análisis

---

### ¿Qué significa el campo `activo_tabla`?

Es el sistema de **soft delete** (borrado lógico):

- `activo_tabla = 1`: Registro **activo** y visible
- `activo_tabla = 0`: Registro **desactivado** (oculto pero no borrado)

**Ventajas:**
- ✅ NO se pierden datos nunca
- ✅ Se pueden recuperar registros "eliminados"
- ✅ Se mantiene integridad referencial
- ✅ Auditoría completa de cambios

**Importante:** NUNCA se hace `DELETE` físico en la base de datos, solo se cambia `activo_tabla` a 0.

---

### ¿Cuándo usar RESTRICT, CASCADE o SET NULL en Foreign Keys?

| Situación | Usar | Ejemplo |
|-----------|------|---------|
| El hijo NO puede existir sin padre | `CASCADE` | Líneas de pedido cuando se borra el pedido |
| El padre tiene hijos que lo necesitan | `RESTRICT` | Departamento con empleados activos |
| El hijo puede quedar huérfano | `SET NULL` | Empleado sin jefe cuando se borra el jefe |

**RESTRICT:** Impide borrar el padre si tiene hijos
**CASCADE:** Borrar el padre borra automáticamente los hijos
**SET NULL:** El hijo queda sin padre (campo FK debe permitir NULL)

---

### ¿Qué diferencia hay entre campo obligatorio y opcional?

| Tipo | En BD | En INSERT | Ejemplo |
|------|-------|-----------|---------|
| **Obligatorio** | `NOT NULL` | Debe tener valor siempre | Nombre, Email, Código |
| **Opcional** | `NULL` | Puede estar vacío | Teléfono, Descripción |

**Regla práctica:**
- Si el dato SIEMPRE debe existir → **obligatorio**
- Si puede estar vacío al crear el registro → **opcional**

---

### ¿Qué es una vista SQL y cuándo se usa?

Una **vista** es una "tabla virtual" que combina datos de varias tablas relacionadas.

**Usar vista cuando:**
- ✅ La tabla tiene 3+ relaciones con otras tablas
- ✅ Necesitas campos calculados frecuentemente
- ✅ Las consultas SELECT son complejas y repetitivas

**NO usar vista cuando:**
- ❌ Hacer INSERT, UPDATE o DELETE (usar tabla directamente)
- ❌ La tabla es simple sin relaciones
- ❌ Solo necesitas campos de la tabla principal

---

### ¿Qué es un trigger y para qué sirve?

Un **trigger** es código que se ejecuta **automáticamente** cuando ocurre un evento en la tabla (INSERT, UPDATE, DELETE).

**Casos de uso comunes:**
- Generar códigos automáticos (EMP-001, EMP-002, etc.)
- Validar reglas de negocio complejas
- Sincronizar campos relacionados
- Establecer valores por defecto dinámicos

**Ejemplo:** Cuando insertas un empleado, el trigger genera automáticamente su código único sin que tengas que calcularlo manualmente.

---

## 🔧 BACKEND PHP

### ¿Por qué el modelo tiene tantos métodos?

Son los **métodos estándar CRUD** que necesita cualquier módulo:

| Método | Qué hace |
|--------|----------|
| `get_entidades()` | Lista todos los registros |
| `get_entidades_disponibles()` | Lista solo los activos |
| `get_entidadxid($id)` | Obtiene uno por ID |
| `insert_entidad(...)` | Crea nuevo registro |
| `update_entidad(...)` | Modifica registro existente |
| `delete_entidadxid($id)` | Desactiva registro (soft delete) |
| `activar_entidadxid($id)` | Reactiva registro |
| `verificarEntidad(...)` | Valida campos únicos |

Estos métodos son **reutilizables** y siguen un patrón consistente en todo el proyecto.

---

### ¿Qué son los "prepared statements" y por qué se usan?

Son **consultas SQL preparadas** que protegen contra **SQL Injection** (ataque común de seguridad).

**Ejemplo seguro (prepared statement):**
```php
$sql = "SELECT * FROM empleado WHERE id_empleado = ?";
$stmt = $this->conexion->prepare($sql);
$stmt->bindValue(1, $id, PDO::PARAM_INT);
$stmt->execute();
```

**Ejemplo INSEGURO (concatenación directa):**
```php
// ❌ NUNCA hacer esto:
$sql = "SELECT * FROM empleado WHERE id_empleado = $id";
```

El código generado **siempre** usa prepared statements para máxima seguridad.

---

### ¿Cuándo crear un modelo con vista completa?

**Vista completa SÍ:**
- Tabla con 3 o más relaciones a otras tablas
- Necesitas campos calculados frecuentemente
- Consultas SELECT complejas con múltiples JOINs

**Ejemplos:** Empleado, Proyecto, Presupuesto, Pedido

**Vista completa NO:**
- Tablas simples sin relaciones
- Catálogos básicos

**Ejemplos:** Departamento, Categoría, Estado, País

---

### ¿Cuándo incluir estadísticas en un modelo?

**Estadísticas SÍ:**
- Módulos con dashboards
- Necesitas métricas y KPIs
- Reportes con totales, promedios, contadores

**Ejemplos:** Empleados, Ventas, Proyectos, Presupuestos

**Estadísticas NO:**
- Catálogos simples
- Tablas auxiliares

**Ejemplos:** Departamentos, Categorías, Estados

---

### ¿Qué es el RegistroActividad?

Es el **sistema de logging** que registra todas las operaciones importantes:
- Quién realizó la acción
- En qué pantalla/módulo
- Qué operación (insertar, actualizar, eliminar)
- Cuándo exactamente
- Resultado de la operación

**Beneficios:**
- Auditoría completa del sistema
- Debugging facilitado
- Trazabilidad de cambios
- Cumplimiento de normativas

---

## 📏 CONVENCIONES Y ESTÁNDARES

### ¿Por qué es importante seguir las convenciones?

**Beneficios:**
- ✅ **Consistencia:** Todo el código se ve igual, fácil de entender
- ✅ **Mantenibilidad:** Cualquiera puede trabajar en cualquier módulo
- ✅ **Escalabilidad:** Fácil añadir nuevas funcionalidades
- ✅ **Calidad:** Menos errores y bugs
- ✅ **Trabajo en equipo:** Todos hablan el mismo "idioma"

---

### ¿Qué pasa si no sigo las convenciones?

**Problemas potenciales:**
- ❌ Código inconsistente difícil de mantener
- ❌ Errores en consultas SQL por nombres incorrectos
- ❌ Confusión para otros desarrolladores
- ❌ Dificultad para integrar con módulos existentes
- ❌ Revisiones de código más lentas

---

### ¿Puedo sugerir cambios a los estándares?

SÍ. Si identificas una mejora potencial:
1. Documenta el problema actual
2. Propón la solución con ejemplos
3. Consulta con el líder técnico
4. Si se aprueba, se actualiza el estándar para todos

---

## 🔧 PROBLEMAS COMUNES

### Error: "Table already exists"

**Causa:** Ya existe una tabla con ese nombre.

**Solución:**
```sql
-- Ver si existe:
SHOW TABLES LIKE 'nombre_tabla';

-- Si existe y quieres recrearla:
DROP TABLE IF EXISTS nombre_tabla;
-- Luego ejecuta el CREATE TABLE
```

---

### Error: "Cannot add foreign key constraint"

**Causa:** La tabla referenciada no existe o el tipo de dato no coincide.

**Solución:**
1. Verifica que la tabla padre existe: `SHOW TABLES;`
2. Verifica que el campo FK tiene el mismo tipo que la PK
3. Crea primero las tablas padres, luego las hijas

---

### Error: "Duplicate entry for key 'uk_campo'"

**Causa:** Intentas insertar un valor que ya existe en un campo UNIQUE.

**Solución:**
1. Usa el método `verificarEntidad()` antes de insertar
2. Verifica que el campo no esté duplicado en la BD
3. Usa UPDATE en lugar de INSERT si el registro existe

---

### El código generado no compila

**Verifica:**
1. Copiaste el código completo (incluyendo `<?php` y `?>`)
2. Los `require_once` apuntan a las rutas correctas
3. No hay caracteres especiales copiados incorrectamente
4. El archivo tiene extensión `.php`

---

### La vista no devuelve datos

**Verifica:**
1. La vista se creó correctamente: `SHOW CREATE VIEW vista_nombre;`
2. Las tablas relacionadas tienen datos
3. El filtro WHERE no es demasiado restrictivo
4. Los JOINs coinciden con los datos reales

---

## 🆘 SOPORTE

### ¿A quién contacto si tengo dudas?

**Orden de escalación:**
1. **Consulta este FAQ** primero
2. **Revisa los ejemplos** en `comandos_rapidos.md`
3. **Contacta al líder técnico** si persiste la duda
4. **Documenta** la solución para futuros casos

---

### ¿Cómo reporto un bug en el código generado?

**Información necesaria:**
1. **Comando usado:** Copia exacta del comando que escribiste
2. **Código generado:** El SQL/PHP completo que recibiste
3. **Error obtenido:** Mensaje de error completo
4. **Contexto:** Qué estabas intentando hacer
5. **Capturas:** Si es posible, screenshots del error

---

### ¿Dónde encuentro más ejemplos?

**Fuentes de ejemplos:**
1. `comandos_rapidos.md` → Sección "Ejemplos Completos"
2. Código existente del proyecto → Revisa módulos similares
3. Consulta al líder técnico → Puede mostrarte casos reales

---

### ¿Puedo contribuir al FAQ?

**SÍ.** Si encuentras una pregunta frecuente no documentada:
1. Documenta la pregunta y respuesta
2. Comparte con el líder técnico
3. Se evaluará para inclusión en el FAQ

---

## 📚 GLOSARIO RÁPIDO

| Término | Significado |
|---------|-------------|
| **CRUD** | Create, Read, Update, Delete (operaciones básicas) |
| **FK** | Foreign Key (clave foránea) |
| **PK** | Primary Key (clave primaria) |
| **Soft Delete** | Borrado lógico (desactivar sin eliminar) |
| **Prepared Statement** | Consulta SQL preparada (segura) |
| **Trigger** | Disparador automático en BD |
| **Vista SQL** | Tabla virtual que combina datos |
| **ORM** | Object-Relational Mapping |
| **PDO** | PHP Data Objects (biblioteca de BD) |
| **JSON** | JavaScript Object Notation |
| **AJAX** | Asynchronous JavaScript and XML |
| **MVC** | Model-View-Controller (arquitectura) |

---

## 🔄 ACTUALIZACIONES

Este FAQ se actualiza periódicamente. Si tienes sugerencias de preguntas a incluir, contacta al líder técnico.

**Última actualización:** Diciembre 2024
**Versión:** 1.0

---

## ✅ CHECKLIST: "¿Leí el FAQ?"

Antes de preguntar, verifica que revisaste:

- [ ] Sección General
- [ ] Sección de tu área (BD o Backend)
- [ ] Problemas Comunes
- [ ] Ejemplos en `comandos_rapidos.md`

Si después de esto persiste la duda, contacta al líder técnico con:
- Qué buscaste en el FAQ
- Qué intentaste
- Qué resultado obtuviste
- Qué esperabas obtener

---

**¡Gracias por usar el sistema de comandos del proyecto MDR!**
