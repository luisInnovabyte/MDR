# 🚀 Comandos Rápidos - Proyecto MDR

> **Sistema de trabajo:** Escribes el comando → Claude genera código siguiendo estándares internos → Usas el código generado

---

## 📋 Índice de Comandos

- [Base de Datos](#base-de-datos)
  - [Nueva Tabla](#nueva-tabla)
  - [Nueva Vista](#nueva-vista)
  - [Nuevo Trigger](#nuevo-trigger)
- [Backend PHP](#backend-php)
  - [Nuevo Modelo](#nuevo-modelo)
  - [Nuevo Controller](#nuevo-controller)
- [Ejemplos Completos](#ejemplos-completos)
- [Reglas Rápidas](#reglas-rápidas)

---

## 🗄️ BASE DE DATOS

### Nueva Tabla

**Cuándo usar:** Necesitas crear una nueva tabla en MySQL.

**Formato:**
```
NUEVA TABLA
Nombre: [nombre_singular_sin_espacios]
Descripción: [para qué sirve esta tabla]

CAMPOS:
- campo1: [descripción] [obligatorio/opcional]
- campo2: [descripción] [obligatorio/opcional]
- campo3: [descripción] [obligatorio/opcional]

RELACIONES:
- FK a tabla_x: [obligatoria/opcional] - [descripción] [ON DELETE: RESTRICT/CASCADE/SET NULL]
(o escribir "Ninguna" si no tiene relaciones)

ÚNICOS:
- campo_que_no_debe_repetirse
(o escribir "Ninguno" si no aplica)

ÍNDICES ADICIONALES:
- campo_de_busqueda_frecuente
(o escribir "Ninguno" si no aplica)

ENUM (si aplica):
- campo_estado: valor1, valor2, valor3
(o omitir si no hay campos ENUM)
```

**Ejemplo básico - Tabla simple:**
```
NUEVA TABLA
Nombre: departamento
Descripción: Departamentos de la empresa

CAMPOS:
- codigo: Código único de departamento [obligatorio]
- nombre: Nombre del departamento [obligatorio]
- descripcion: Descripción detallada [opcional]

RELACIONES:
- Ninguna

ÚNICOS:
- codigo
- nombre

ÍNDICES ADICIONALES:
- Ninguno
```

**Ejemplo avanzado - Tabla con relaciones:**
```
NUEVA TABLA
Nombre: empleado
Descripción: Empleados de la empresa

CAMPOS:
- codigo: Código único de empleado [obligatorio]
- nombre: Nombre completo [obligatorio]
- email: Email corporativo [obligatorio]
- telefono: Teléfono de contacto [opcional]
- fecha_ingreso: Fecha de contratación [obligatorio]
- salario: Salario mensual [obligatorio]

RELACIONES:
- FK a departamento: obligatoria - Departamento al que pertenece [ON DELETE: RESTRICT]
- FK a jefe: opcional - Jefe directo (es otro empleado) [ON DELETE: SET NULL]

ÚNICOS:
- codigo
- email

ÍNDICES ADICIONALES:
- nombre
- fecha_ingreso

ENUM:
- estado_empleado: activo, vacaciones, baja_temporal, baja_definitiva
```

---

### Nueva Vista

**Cuándo usar:** Necesitas combinar datos de múltiples tablas con JOINs para consultas frecuentes.

**Formato:**
```
NUEVA VISTA
Nombre: vista_[nombre_tabla]_completa
Descripción: [qué información combina]

TABLA PRINCIPAL: [nombre_tabla]

RELACIONES:
- [tabla_relacionada1]: [INNER/LEFT] - [descripción]
- [tabla_relacionada2]: [INNER/LEFT] - [descripción]

CAMPOS CALCULADOS:
- [nombre_campo]: [descripción del cálculo]
(o escribir "Ninguno" si no necesita campos calculados)

CONTADORES (si aplica):
- cantidad_[subtabla]: Contar registros de [subtabla]
(o omitir si no aplica)

FILTRO WHERE:
- [Solo activos / Todos los registros]
```

**Ejemplo:**
```
NUEVA VISTA
Nombre: vista_empleado_completa
Descripción: Datos completos de empleados con departamento y jefe

TABLA PRINCIPAL: empleado

RELACIONES:
- departamento: INNER - Departamento del empleado
- empleado (como jefe): LEFT - Jefe directo

CAMPOS CALCULADOS:
- nombre_completo_jefe: Concatenar nombre y apellido del jefe
- antiguedad_dias: Días desde fecha_ingreso hasta hoy
- antiguedad_anios: Años de antigüedad

CONTADORES:
- cantidad_subordinados: Contar empleados que tienen a este como jefe

FILTRO WHERE:
- Solo activos
```

---

### Nuevo Trigger

**Cuándo usar:** Necesitas que algo ocurra automáticamente cuando se inserta/actualiza/elimina un registro.

**Formato:**
```
NUEVO TRIGGER
Tipo: [generar_codigo / validar / sincronizar_estado / valor_defecto]
Tabla: [nombre_tabla]
Descripción: [qué debe hacer automáticamente]

DETALLES:
[Información específica según el tipo]
```

**Tipo 1 - Generar código automático:**
```
NUEVO TRIGGER
Tipo: generar_codigo
Tabla: empleado
Descripción: Generar código automático en formato DEPT-001

DETALLES:
- Campo código: codigo_empleado
- Prefijo desde: departamento.codigo_departamento
- Formato: PREFIJO-NNN (3 dígitos)
```

**Tipo 2 - Validar regla de negocio:**
```
NUEVO TRIGGER
Tipo: validar
Tabla: empleado
Descripción: Validar que solo puede haber un gerente general activo

DETALLES:
- Campo a validar: cargo_empleado
- Valor crítico: 'Gerente General'
- Condición: Solo uno puede estar activo
- Error a mostrar: "Ya existe un Gerente General activo"
```

**Tipo 3 - Sincronizar estados:**
```
NUEVO TRIGGER
Tipo: sincronizar_estado
Tabla: proyecto
Descripción: Sincronizar campo activo con estado CANCELADO

DETALLES:
- Campo activo: activo_proyecto
- Campo estado: id_estado_proyecto
- Estado INACTIVO: código 'CANC'
- Estado ACTIVO: código 'PROC'
```

**Tipo 4 - Valor por defecto:**
```
NUEVO TRIGGER
Tipo: valor_defecto
Tabla: empleado
Descripción: Establecer fecha_ingreso como hoy si viene NULL

DETALLES:
- Campo: fecha_ingreso_empleado
- Valor por defecto: NOW()
```

---

## 🔧 BACKEND PHP

### Nuevo Modelo

**Cuándo usar:** Necesitas crear la clase PHP que gestiona los datos de una tabla.

**Formato:**
```
NUEVO MODELO
Entidad: [NombreEntidad]
Vista completa: [SÍ/NO]
Estadísticas: [SÍ/NO]
Campos opcionales: [lista de campos que pueden ser NULL]
```

**Ejemplo simple:**
```
NUEVO MODELO
Entidad: Departamento
Vista completa: NO
Estadísticas: NO
Campos opcionales: descripcion
```

**Ejemplo con vista:**
```
NUEVO MODELO
Entidad: Empleado
Vista completa: SÍ
Estadísticas: SÍ
Campos opcionales: telefono, id_jefe
```

**¿Vista completa SÍ o NO?**
- **SÍ**: Cuando la tabla tiene 3+ relaciones con otras tablas
- **NO**: Cuando es una tabla simple sin muchas relaciones

**¿Estadísticas SÍ o NO?**
- **SÍ**: Para módulos que necesitan dashboards con métricas (empleados, ventas, proyectos)
- **NO**: Para catálogos simples (departamentos, categorías, estados)

---

### Nuevo Controller

**Cuándo usar:** Necesitas el archivo PHP que recibe peticiones AJAX y coordina con el modelo.

**Formato:**
```
NUEVO CONTROLLER
Modelo: [NombreEntidad]
Operaciones: [lista separada por comas]
Campos para verificar: [campo_unico1, campo_unico2]
```

**Operaciones estándar disponibles:**
- `listar` - Obtener todos los registros
- `listar_disponibles` - Solo registros activos
- `guardaryeditar` - Insertar o actualizar
- `mostrar` - Obtener uno por ID
- `eliminar` - Desactivar registro
- `activar` - Reactivar registro
- `desactivar` - Desactivar explícitamente
- `verificar` - Validar campo único

**Ejemplo básico:**
```
NUEVO CONTROLLER
Modelo: Departamento
Operaciones: listar, guardaryeditar, mostrar, eliminar, activar, verificar
Campos para verificar: codigo, nombre
```

**Ejemplo completo:**
```
NUEVO CONTROLLER
Modelo: Empleado
Operaciones: listar, listar_disponibles, guardaryeditar, mostrar, eliminar, activar, desactivar, verificar
Campos para verificar: codigo, email
```

---

## 📚 EJEMPLOS COMPLETOS

### Caso 1: Módulo Completo de Proyectos

**Paso 1 - Tabla:**
```
NUEVA TABLA
Nombre: proyecto
Descripción: Proyectos de la empresa

CAMPOS:
- codigo: Código único de proyecto [obligatorio]
- nombre: Nombre del proyecto [obligatorio]
- descripcion: Descripción detallada [obligatorio]
- fecha_inicio: Fecha de inicio [obligatorio]
- fecha_fin_estimada: Fecha estimada de finalización [opcional]
- presupuesto: Presupuesto asignado [obligatorio]

RELACIONES:
- FK a cliente: obligatoria - Cliente del proyecto [ON DELETE: RESTRICT]
- FK a empleado (como responsable): obligatoria - Responsable del proyecto [ON DELETE: RESTRICT]

ÚNICOS:
- codigo

ÍNDICES ADICIONALES:
- nombre
- fecha_inicio

ENUM:
- estado_proyecto: planificacion, en_curso, pausado, finalizado, cancelado
```

**Paso 2 - Vista:**
```
NUEVA VISTA
Nombre: vista_proyecto_completa
Descripción: Proyectos con datos de cliente y responsable

TABLA PRINCIPAL: proyecto

RELACIONES:
- cliente: INNER - Cliente del proyecto
- empleado (como responsable): INNER - Responsable

CAMPOS CALCULADOS:
- dias_transcurridos: Días desde fecha_inicio hasta hoy
- dias_restantes: Días hasta fecha_fin_estimada
- estado_temporal: 'En plazo', 'Próximo a vencer', 'Retrasado'

FILTRO WHERE:
- Solo activos
```

**Paso 3 - Modelo:**
```
NUEVO MODELO
Entidad: Proyecto
Vista completa: SÍ
Estadísticas: SÍ
Campos opcionales: fecha_fin_estimada
```

**Paso 4 - Controller:**
```
NUEVO CONTROLLER
Modelo: Proyecto
Operaciones: listar, listar_disponibles, guardaryeditar, mostrar, eliminar, activar, verificar
Campos para verificar: codigo
```

---

### Caso 2: Tabla Pivote (Muchos a Muchos)

**Escenario:** Empleados pueden estar en múltiples proyectos, y proyectos tienen múltiples empleados.

```
NUEVA TABLA
Nombre: proyecto_empleado
Descripción: Relación muchos a muchos entre proyectos y empleados

CAMPOS:
- horas_asignadas: Horas semanales asignadas [obligatorio]
- fecha_asignacion: Cuándo se asignó al proyecto [obligatorio]
- rol_en_proyecto: Rol específico en este proyecto [opcional]

RELACIONES:
- FK a proyecto: obligatoria - Proyecto [ON DELETE: CASCADE]
- FK a empleado: obligatoria - Empleado [ON DELETE: CASCADE]

ÚNICOS:
- proyecto + empleado (combinación)

ÍNDICES ADICIONALES:
- Ninguno
```

---

## ⚡ REGLAS RÁPIDAS

### Nomenclatura

| ✅ Correcto | ❌ Incorrecto |
|------------|--------------|
| `empleado` | `empleados`, `Empleado`, `tbl_empleado` |
| `proyecto_empleado` | `proyectoEmpleado`, `Proyecto_Empleado` |
| `codigo_departamento` | `codigo`, `cod_dept` |

### Campos Obligatorio vs Opcional

| Pregunta | Respuesta | Tipo |
|----------|-----------|------|
| ¿El dato SIEMPRE debe existir? | SÍ | `[obligatorio]` |
| ¿Puede estar vacío al crear? | SÍ | `[opcional]` |
| ¿Es información crítica? | SÍ | `[obligatorio]` |

### Relaciones (Foreign Keys)

**¿Qué ON DELETE usar?**

| Situación | ON DELETE | Ejemplo |
|-----------|-----------|---------|
| El hijo NO puede existir sin padre | `CASCADE` | Líneas de pedido → Pedido |
| El padre tiene hijos que lo necesitan | `RESTRICT` | Departamento → Empleados |
| El hijo puede quedar huérfano | `SET NULL` | Empleado → Jefe (cuando se borra el jefe) |

**¿INNER JOIN o LEFT JOIN?**

| En la vista | Cuando FK es | Usar |
|-------------|--------------|------|
| Relación SIEMPRE existe | `NOT NULL` (obligatoria) | `INNER JOIN` |
| Relación puede NO existir | `NULL` (opcional) | `LEFT JOIN` |

### Tipos de Datos Comunes

| Para almacenar | Usa | Ejemplo |
|----------------|-----|---------|
| Nombres, códigos cortos | `VARCHAR(50-150)` | nombre, codigo |
| Email | `VARCHAR(150)` | email |
| Teléfono | `VARCHAR(20)` | telefono |
| Direcciones | `VARCHAR(255)` | direccion |
| Descripciones largas | `TEXT` | descripcion, observaciones |
| Dinero | `DECIMAL(10,2)` | precio, salario |
| Cantidades | `INT` | cantidad, stock |
| Porcentajes | `DECIMAL(5,2)` | descuento, iva |
| Fechas sin hora | `DATE` | fecha_nacimiento |
| Fechas con hora | `DATETIME` | fecha_pedido |
| Verdadero/Falso | `TINYINT(1)` | activo, destacado |

---

## 💡 TIPS

### 1. Siempre en singular
- ✅ `empleado`, `proyecto`, `departamento`
- ❌ `empleados`, `proyectos`, `departamentos`

### 2. Snake_case
- ✅ `fecha_ingreso`, `codigo_empleado`
- ❌ `fechaIngreso`, `FechaIngreso`, `Fecha_Ingreso`

### 3. Campos únicos
Si un campo NO debe repetirse (código, email, NIF), márcalo en `ÚNICOS:`

### 4. Índices en campos de búsqueda
Si vas a buscar/filtrar frecuentemente por un campo, inclúyelo en `ÍNDICES ADICIONALES:`

### 5. Documentación clara
Escribe descripciones claras en cada campo. Ejemplo:
- ✅ `email: Email corporativo del empleado [obligatorio]`
- ❌ `email: email [obligatorio]`

---

## ❓ FAQ Rápido

**P: ¿Tengo que leer documentación técnica?**
R: NO. Solo usa estos comandos y recibirás código listo.

**P: ¿Puedo modificar el código generado?**
R: SÍ, pero mantén la estructura base para consistencia.

**P: ¿Qué hago si el comando no funciona?**
R: Verifica que seguiste el formato exacto. Consulta los ejemplos.

**P: ¿Por qué algunos campos tienen sufijos largos?**
R: Es parte del estándar interno que garantiza código sin ambigüedades.

**P: ¿Puedo crear tablas sin relaciones?**
R: SÍ. Simplemente escribe "RELACIONES: Ninguna"

**P: ¿Todos los modelos necesitan vista completa?**
R: NO. Solo los que tienen 3+ relaciones con otras tablas.

**P: ¿Todos los módulos necesitan estadísticas?**
R: NO. Solo los que tienen dashboards o reportes con métricas.

---

## 🆘 SOPORTE

Si tienes dudas no resueltas aquí:
1. Revisa los **Ejemplos Completos**
2. Verifica que seguiste el **formato exacto**
3. Consulta las **Reglas Rápidas**
4. Contacta al líder técnico

---

**Última actualización:** Diciembre 2024
**Versión:** 1.0
