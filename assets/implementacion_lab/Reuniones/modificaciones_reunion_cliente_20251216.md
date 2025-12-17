# Modificaciones Solicitadas - Reunión Cliente MDR ERP Manager

**Fecha de reunión**: Diciembre 2024  
**Documento**: Análisis de requisitos y aclaraciones

---

## 1. Gestor Documental para Técnicos

### Descripción
Sistema de almacenamiento y gestión de documentos PDF orientado a proporcionar información técnica y operativa al personal de campo.

### Casos de Uso Identificados
- Manuales de seguridad e higiene en el trabajo
- Fichas técnicas de equipos
- Procedimientos operativos
- Documentación de normativas

### Especificaciones Técnicas CONFIRMADAS
- ✅ **Almacenamiento físico**: Carpeta en sistema de archivos (NUNCA en base de datos)
- ✅ **Base de datos**: Solo metadatos y ruta relativa
- ✅ **Versiones**: NO se controlan versiones (un documento = un archivo actual)
- ✅ **Categorización**: Tabla de tipos de documentos

### Estructura de Tablas Propuesta

**Tabla: `tipo_documento`**
```sql
- id_tipo_documento (PK)
- nombre_tipo_documento (VARCHAR)
- descripcion_tipo_documento (TEXT, opcional)
- activo_tipo_documento (BOOLEAN)
```

**Ejemplos de tipos:**
- Seguridad e Higiene
- Manuales de Equipo
- Procedimientos Operativos
- Normativas
- Fichas Técnicas

**Tabla: `documento`**
```sql
- id_documento (PK)
- titulo_documento (VARCHAR)
- descripcion_documento (TEXT, opcional)
- ruta_documento (VARCHAR) - Ej: /documentos/seguridad/manual_001.pdf
- id_tipo_documento (FK → tipo_documento)
- fecha_publicacion_documento (DATE)
- activo_documento (BOOLEAN)
- fecha_creacion_documento (TIMESTAMP)
- fecha_modificacion_documento (TIMESTAMP)
```

### Consideraciones de Implementación
- **Rutas relativas**: Usar rutas relativas para portabilidad
- **Nomenclatura**: Establecer convención de nombres de archivos
- **Validación**: Verificar existencia física del archivo al consultar
- **Seguridad**: Control de acceso a carpeta de documentos
- **Backup**: Incluir carpeta en estrategia de respaldo

### Prioridad
**ÚTIL** - Complementario, valor añadido para técnicos

---

## 2. Artículos KIT - Composición de Artículos

### Cambio Conceptual
**Situación actual**: Artículos → compuestos por Elementos (equipos físicos)  
**Nueva realidad**: 
- Artículos NO-KIT → compuestos por Elementos
- Artículos KIT → compuestos por otros Artículos

### Reglas de Negocio CONFIRMADAS

✅ **Composición simple**: KIT → Artículos (NO se permite KIT → KIT)  
✅ **Pricing independiente**: El precio se asigna al artículo padre (el KIT)  
✅ **Coeficientes**: Se aplican al precio del artículo KIT, no a componentes  
✅ **Detalle variable**: Marca en artículo padre determina si se desglosa en presupuesto  
⏳ **Disponibilidad**: PENDIENTE de confirmación con cliente

### Estructura de Tablas

**Modificación en tabla `articulo`:**
```sql
- es_kit_articulo (BOOLEAN) - Indica si es un KIT
- detallar_kit_articulo (BOOLEAN) - NUEVO CAMPO
  * TRUE: Mostrar componentes desglosados en presupuesto
  * FALSE: Mostrar solo el KIT como línea única
```

**Nueva tabla: `articulo_composicion_kit`**
```sql
- id_composicion_kit (PK)
- id_articulo_padre (FK → articulo) - El KIT
- id_articulo_componente (FK → articulo) - Artículo que forma parte del KIT
- cantidad_componente (DECIMAL) - Unidades del componente en el KIT
- orden_componente (INT, opcional) - Orden de visualización
```

**Constraint de integridad:**
```sql
-- El componente NO puede ser otro KIT
CHECK (id_articulo_componente NOT IN 
  (SELECT id_articulo FROM articulo WHERE es_kit_articulo = TRUE))
```

### Visualización en Presupuestos

**Si `detallar_kit_articulo = TRUE`:**
```
Presupuesto:
  [KIT] Sistema de Audio Completo - 1 ud - 500€
    → Mesa de mezclas - 1 ud
    → Altavoces principales - 2 ud
    → Microfonos inalámbricos - 4 ud
```

**Si `detallar_kit_articulo = FALSE`:**
```
Presupuesto:
  [KIT] Sistema de Audio Completo - 1 ud - 500€
```

### Disponibilidad - PENDIENTE CONFIRMACIÓN

**Opciones planteadas al cliente:**
1. **Restrictiva**: KIT disponible solo si TODOS los componentes están disponibles
2. **Flexible**: KIT disponible independientemente (se gestionan componentes aparte)
3. **Mixta**: Alertas si faltan componentes pero permite asignar el KIT

### Prioridad
**CRÍTICO** - Impacta estructura de datos y operativa

---

## 3. Elementos: Proveedor y Tipo de Propiedad

### Nuevos Atributos Requeridos
1. **Proveedor del elemento** (id_proveedor)
2. **Tipo de propiedad**: Propio / Externo (alquilado)
3. **Método de pago específico** (override del proveedor)

### Lógica de Negocio

**Si elemento es PROPIO:**
- Proveedor = proveedor de compra
- Método de pago: heredado del proveedor

**Si elemento es EXTERNO (alquilado):**
- Proveedor = proveedor de alquiler prioritario
- Método de pago: heredado del proveedor PERO modificable en la ficha del elemento

### Modificaciones en tabla `elemento`

```sql
- es_propio_elemento (BOOLEAN)
  * TRUE: Equipo de la empresa
  * FALSE: Equipo alquilado a proveedor
  
- id_proveedor_elemento (FK → proveedor)
  * Si es_propio = TRUE → proveedor de compra
  * Si es_propio = FALSE → proveedor de alquiler
  
- id_metodo_pago_elemento (FK → metodo_pago, NULLABLE)
  * NULL: usar método de pago del proveedor
  * Valor: override del método de pago
  
- notas_alquiler_elemento (TEXT, opcional)
  * Condiciones especiales, mínimo de días, etc.
```

### Consideraciones de Impacto
- **Costos**: Elementos externos generan costos de alquiler → impacto en rentabilidad
- **Disponibilidad**: Elementos externos pueden tener menor disponibilidad
- **Gestión financiera**: Necesario seguimiento de alquileres a proveedores
- **Reporting**: Distinguir en informes equipos propios vs alquilados
- **Contratos**: Elementos externos pueden tener condiciones contractuales

### Prioridad
**IMPORTANTE** - Afecta costos y rentabilidad

---

## 4. Tabla de Empleados

### Estado
⏳ **PENDIENTE**: Verificar si existe estructura mínima en sistema actual

### Datos Mínimos Requeridos
Si no existe, implementar:
- Identificación (DNI/NIE, nombre, apellidos)
- Datos de contacto (teléfono, email)
- Rol/departamento (técnico, administrativo, gerencia)
- Estado (activo/inactivo)
- Fecha de alta/baja

### Relaciones Potenciales
- Asignación de eventos/presupuestos
- Tracking de documentación leída (gestor documental)
- Asignación de furgonetas
- Registro de kilometraje

---

## 5. Gestión de Mantenimiento de Furgonetas

### Objetivos
- Control periódico de kilometraje
- Alertas de mantenimiento preventivo basado en km
- Control de fechas de ITV
- Anticipación a revisiones

### Estructura de Datos Necesaria

**Tabla: `furgoneta`**
```sql
- id_furgoneta (PK)
- matricula_furgoneta (VARCHAR, UNIQUE)
- marca_furgoneta (VARCHAR)
- modelo_furgoneta (VARCHAR)
- anio_furgoneta (INT)
- kilometraje_actual_furgoneta (INT)
- kilometros_entre_revisiones_furgoneta (INT) - Ej: 10000 km
- fecha_proxima_itv_furgoneta (DATE)
- fecha_ultima_revision_furgoneta (DATE, opcional)
- estado_furgoneta (ENUM: 'operativa', 'taller', 'baja')
- numero_bastidor_furgoneta (VARCHAR, opcional)
- capacidad_carga_furgoneta (DECIMAL, opcional) - En kg o m³
- activo_furgoneta (BOOLEAN)
```

**Tabla: `furgoneta_registro_kilometraje`**
```sql
- id_registro_km (PK)
- id_furgoneta (FK → furgoneta)
- fecha_registro (DATE)
- kilometraje_registrado (INT)
- id_empleado (FK → empleado, opcional) - Quién registra
- observaciones_registro (TEXT, opcional)
```

**Tabla: `furgoneta_mantenimiento` (opcional, para histórico)**
```sql
- id_mantenimiento (PK)
- id_furgoneta (FK → furgoneta)
- fecha_mantenimiento (DATE)
- tipo_mantenimiento (VARCHAR) - ITV, Revisión, Reparación
- descripcion_mantenimiento (TEXT)
- costo_mantenimiento (DECIMAL)
- taller_mantenimiento (VARCHAR, opcional)
- kilometraje_mantenimiento (INT)
```

### Lógica de Alertas

**Alerta de Mantenimiento:**
- Cuando: `kilometraje_actual >= (kilometraje_ultima_revision + kilometros_entre_revisiones - margen)`
- Margen sugerido: 500-1000 km antes

**Alerta de ITV:**
- Cuando: `fecha_actual >= (fecha_proxima_itv - dias_antelacion)`
- Días sugeridos: 30 días antes

### Consideraciones de Implementación
- **Frecuencia de registro**: Determinar periodicidad (diario, semanal, post-evento)
- **Responsables**: Definir quién registra los kilómetros
- **Automatización**: Posible integración con GPS/OBD (futuro)
- **Dashboard**: Interfaz visual para estado de flota
- **Notificaciones**: Sistema de alertas automáticas

### Campos Adicionales Útiles (opcionales)
- Seguro (compañía, póliza, fecha vencimiento)
- Combustible habitual
- Consumo medio
- Taller habitual
- Costos de operación (combustible, peajes, etc.)

### Prioridad
**ÚTIL** - Mejora operativa y control de gastos

---

## 6. Coeficientes - Control de Aplicación en Presupuesto

### Cambio Solicitado
Añadir flag en cabecera de presupuesto para activar/desactivar la aplicación de coeficientes reductores por días de alquiler.

### Modificación en tabla `presupuesto`

```sql
- aplicar_coeficientes_presupuesto (BOOLEAN)
  * TRUE: Aplicar coeficientes reductores según días
  * FALSE: Usar precio base sin reducción por días
```

### Casos de Uso
- Alquileres muy cortos donde no aplica reducción
- Clientes especiales con tarifas fijas
- Eventos corporativos con pricing diferenciado
- Servicios que no se alquilan por días

### Implicaciones Técnicas
- Vistas y funciones de cálculo deben contemplar este flag
- Los coeficientes siguen existiendo pero pueden no aplicarse
- Documentar claramente en presupuesto si se aplicaron o no

### Prioridad
**IMPORTANTE** - Impacta cálculo de precios

---

## 7. Sistema de Descuentos

### Arquitectura del Descuento - CONFIRMADO ✅

**Nivel 1 - Familia de Artículos:**
```sql
familia:
- permite_descuento_familia (BOOLEAN)
```
Control maestro: si una familia no permite descuento, ningún artículo de esa familia lo tendrá.

**Nivel 2 - Cliente:**
```sql
cliente:
- porcentaje_descuento_cliente (DECIMAL 5,2)
```
Descuento habitual/acordado con el cliente.

**Nivel 3 - Presupuesto:**
```sql
presupuesto:
- aplicar_descuento_cliente_presupuesto (BOOLEAN)
```
Se arrastra el porcentaje del cliente pero puede desactivarse en cada presupuesto.

### Lógica de Aplicación - CONFIRMADA ✅

```
SI familia.permite_descuento = TRUE
  Y presupuesto.aplicar_descuento_cliente = TRUE
  ENTONCES aplicar cliente.porcentaje_descuento
SINO
  descuento = 0
```

### Orden de Cálculo - VALIDADO ✅

```
1. Precio base del artículo
2. Aplicar coeficiente reductor por días (si aplicar_coeficientes = TRUE)
3. Aplicar descuento del cliente (si familia permite Y presupuesto lo activa)
4. = Precio final
```

### Restricciones
- Familias de artículos que nunca tienen descuento (ej: consumibles, servicios especiales)
- El descuento del cliente es orientativo pero modificable en cada presupuesto
- Los descuentos se reflejan en el pie del presupuesto

### Prioridad
**CRÍTICO** - Impacta pricing y facturación

---

## 8. Doble Visualización de Presupuestos

### Requisito CRÍTICO
Generar DOS versiones del mismo presupuesto:

**Versión 1 - SIN DESCUENTO (Comercial):**
- Para enviar al cliente final
- Muestra precios completos
- Uso: presentación comercial, ofertas

**Versión 2 - CON DESCUENTO (Real):**
- Para facturación real
- Muestra precios con descuento aplicado
- Uso: factura al cliente, contabilidad interna

### Casos de Uso
- **Cliente final**: Ve precios completos (sin descuento)
- **Cliente intermediario/habitual**: Factura con descuento aplicado
- **Transparencia**: Ambas versiones claras sobre qué representan

### Implementación Recomendada

**Almacenamiento:**
- Guardar precios BASE en base de datos
- Calcular dinámicamente ambas versiones

**Interfaz de usuario:**
- Botón/selector para alternar entre "Vista Comercial" y "Vista Real"
- Ambas versiones imprimibles/exportables a PDF
- Marca de agua o encabezado diferenciador en cada versión

**Campos necesarios en líneas de presupuesto:**
```sql
presupuesto_linea:
- precio_unitario_base (DECIMAL) - Sin coeficientes ni descuentos
- coeficiente_aplicado (DECIMAL, nullable) - Si se aplicó
- porcentaje_descuento_aplicado (DECIMAL, nullable) - Si se aplicó
- precio_unitario_comercial (DECIMAL) - Con coeficiente, SIN descuento
- precio_unitario_real (DECIMAL) - Con coeficiente Y descuento
```

### Impacto en Reporting
- Informes de rentabilidad usan precios REALES (con descuento)
- Históricos de ofertas muestran ambas cifras
- Facturación usa SIEMPRE precios reales

### IMPORTANTE - Facturación
La factura DEBE generarse con los precios REALES (con descuento aplicado) para evitar problemas fiscales.

### Prioridad
**CRÍTICO** - Impacta presentación comercial y facturación

---

## 9. Ubicaciones de Montaje del Cliente

### Descripción
Relación **1:N** entre Cliente y sus ubicaciones habituales de eventos/instalaciones.

### Justificación
- **Clientes recurrentes**: Eventos en las mismas ubicaciones (teatros, auditorios, sedes)
- **Agilidad operativa**: Prellenar datos de ubicación en presupuestos
- **Logística**: Conocer características del lugar anticipadamente
- **Histórico**: Ver qué se instaló previamente en cada ubicación

### Estructura de Tabla

**Tabla: `cliente_ubicacion`**
```sql
- id_ubicacion (PK)
- id_cliente (FK → cliente)
- nombre_ubicacion (VARCHAR) - Ej: "Teatro Municipal", "Auditorio Central"
- direccion_ubicacion (VARCHAR)
- codigo_postal_ubicacion (VARCHAR)
- poblacion_ubicacion (VARCHAR)
- provincia_ubicacion (VARCHAR)
- pais_ubicacion (VARCHAR, default: 'España')
- coordenadas_gps_ubicacion (VARCHAR, opcional) - Para rutas optimizadas
- persona_contacto_ubicacion (VARCHAR, opcional)
- telefono_contacto_ubicacion (VARCHAR, opcional)
- observaciones_ubicacion (TEXT) - "Acceso calle trasera", "Ascensor limitado"
- activo_ubicacion (BOOLEAN)
- es_principal_ubicacion (BOOLEAN) - Ubicación por defecto del cliente
- fecha_creacion_ubicacion (TIMESTAMP)
- fecha_modificacion_ubicacion (TIMESTAMP)
```

### Campos Opcionales Avanzados (considerar futuro)
```sql
- tipo_espacio_ubicacion (ENUM: 'teatro', 'auditorio', 'exterior', 'corporativo')
- aforo_ubicacion (INT) - Capacidad del espacio
- superficie_m2_ubicacion (DECIMAL)
- acceso_vehiculos_ubicacion (VARCHAR) - "Calle peatonal", "Carga directa"
- restricciones_horarias_ubicacion (TEXT) - "No ruido después 22h"
```

### Relación con Presupuestos

**Modificación en tabla `presupuesto`:**
```sql
- id_ubicacion (FK → cliente_ubicacion, NULLABLE)
```

**Flujo de trabajo:**
1. Se crea presupuesto para cliente X
2. Sistema ofrece ubicaciones habituales del cliente
3. Usuario selecciona ubicación existente o introduce nueva
4. Si es ubicación nueva recurrente → se añade a `cliente_ubicacion`

### Beneficios
- **Autocompletado**: Direcciones y contactos prellenados
- **Validación**: Alertas si ubicación tiene restricciones especiales
- **Histórico**: "En este teatro hicimos X evento con Y equipos"
- **Optimización**: Calcular rutas y tiempos entre eventos
- **Reportes**: Análisis por ubicaciones más rentables/frecuentes

### Consideraciones
- **Ubicaciones genéricas**: Algunos presupuestos sin ubicación definida aún
- **Múltiples ubicaciones**: Un evento puede tener varias (complejo, poco frecuente)
- **Actualización**: Ubicaciones físicas cambian (reformas, cambios contacto)
- **Privacidad**: Datos pueden ser sensibles para algunos clientes

### Prioridad
**IMPORTANTE** - Mejora agilidad y operativa

---

## 10. Campos Operativos en Líneas de Presupuesto (FUTURO)

### Descripción
Campos adicionales en líneas de presupuesto para información operativa de campo.

### Campos a Incorporar (NO IMPLEMENTAR AHORA)
```sql
presupuesto_linea:
- fecha_montaje_linea (DATE) - Cuándo se instala el equipo
- fecha_desmontaje_linea (DATE) - Cuándo se retira el equipo
- ubicacion_instalacion_linea (VARCHAR) - "Sala Principal", "Escenario 2", "Stand A3"
```

### Justificación
- **Granularidad temporal**: Equipos con diferentes duraciones en mismo evento
- **Logística**: Planificación de técnicos y transporte
- **Facturación**: Fechas reales determinan días de alquiler
- **Operativa**: Técnicos saben QUÉ va a DÓNDE y CUÁNDO

### Impacto Futuro
- Checklist de montaje/desmontaje
- Rutas de trabajo optimizadas
- Integración con planning de técnicos
- Relación con ubicaciones del cliente (campo `ubicacion_instalacion` como FK o texto libre)

### Nota Importante
⚠️ **NO IMPLEMENTAR AHORA** - Documentado para no crear conflictos en diseño futuro

---

## Visión Integrada del Sistema

### Flujo Completo: Cliente → Ubicación → Presupuesto → Líneas

```
CLIENTE
├─ porcentaje_descuento_cliente
├─ metodo_pago_habitual
│
├─ UBICACIÓN 1: "Teatro Municipal"
├─ UBICACIÓN 2: "Auditorio Universidad"  
└─ UBICACIÓN 3: "Sede Central"
    │
    └─ PRESUPUESTO (evento en Sede Central)
        ├─ aplicar_coeficientes = TRUE
        ├─ aplicar_descuento_cliente = TRUE
        │
        ├─ LÍNEA 1: Pantalla LED
        │   ├─ familia.permite_descuento = TRUE
        │   ├─ precio_base = 100€
        │   ├─ coeficiente (3 días) = 0.9 → 90€
        │   ├─ descuento cliente (10%) → 81€
        │   └─ ubicacion_instalacion: "Sala Conferencias" (futuro)
        │
        ├─ LÍNEA 2: [KIT] Sistema Audio
        │   ├─ detallar_kit = TRUE
        │   ├─ Componente: Mesa mezclas x1
        │   ├─ Componente: Altavoces x2
        │   └─ Componente: Micrófonos x4
        │
        └─ PIE DE PRESUPUESTO
            ├─ Subtotal SIN descuento (versión comercial)
            └─ Subtotal CON descuento (versión real/factura)
```

---

## Resumen de Prioridades

| Requisito | Estado | Complejidad | Prioridad | Acción |
|-----------|--------|-------------|-----------|--------|
| Sistema descuentos | Confirmado ✅ | Media-Alta | **CRÍTICO** | Implementar |
| Doble vista presupuesto | Confirmado ✅ | Media | **CRÍTICO** | Implementar |
| Artículos KIT | Confirmado parcial ⏳ | Media | **CRÍTICO** | Esperar confirmación disponibilidad |
| Ubicaciones cliente | Confirmado ✅ | Baja | **IMPORTANTE** | Implementar |
| Coeficientes ON/OFF | Confirmado ✅ | Baja | **IMPORTANTE** | Implementar |
| Elementos propio/externo | Pendiente diseño | Media | **IMPORTANTE** | Diseñar estructura |
| Gestor documental | Confirmado ✅ | Baja | **ÚTIL** | Implementar |
| Mantenimiento furgonetas | Pendiente diseño | Media | **ÚTIL** | Diseñar estructura |
| Tabla empleados | Verificar existencia ⏳ | Baja | **ÚTIL** | Verificar primero |
| Campos operativos líneas | Documentado | Baja | **FUTURO** | NO implementar aún |

---

## Pendientes de Confirmación con Cliente

1. ⏳ **Disponibilidad de KITs**: ¿Cómo se gestiona la disponibilidad cuando un KIT tiene artículos componentes?
   - Opción A: Restrictiva (todos disponibles)
   - Opción B: Flexible (independiente)
   - Opción C: Mixta (alertas pero permite)

2. ⏳ **Alcance elementos externos**: Confirmar si se requieren campos adicionales para contratos, seguros, etc.

3. ⏳ **Tabla empleados**: Verificar si existe estructura básica o debe crearse desde cero

---

## Notas de Implementación

### Orden Sugerido de Implementación

**FASE 1 - Fundacional (Alta prioridad)**
1. Sistema de descuentos (familia, cliente, presupuesto)
2. Ubicaciones de clientes
3. Flag de coeficientes en presupuesto
4. Gestor documental básico

**FASE 2 - Estructural (Media prioridad)**
5. Artículos KIT (esperar confirmación disponibilidad)
6. Elementos propios/externos con proveedores
7. Doble visualización de presupuestos

**FASE 3 - Operativa (Baja prioridad)**
8. Verificar/crear tabla empleados
9. Mantenimiento de furgonetas
10. Documentación y testing completo

**FASE FUTURA**
- Campos operativos en líneas de presupuesto
- Integración con NFC para ubicaciones
- Dashboard de alertas de mantenimiento

---

## Convenciones y Estándares a Mantener

### Nomenclatura
- Tablas en singular
- Campos sufijados con nombre de tabla
- Claves foráneas: `id_[tabla_referenciada]_[tabla_actual]`
- Booleanos: `[verbo]_[concepto]_[tabla]` (ej: `permite_descuento_familia`)

### Campos Estándar
Todas las tablas maestras incluyen:
- `activo_[tabla]` (BOOLEAN)
- `fecha_creacion_[tabla]` (TIMESTAMP)
- `fecha_modificacion_[tabla]` (TIMESTAMP)

### Integridad Referencial
- Todas las FK con ON DELETE y ON UPDATE explícitos
- Constraints de validación de lógica de negocio
- Índices en todas las FK y campos de búsqueda frecuente

---

**FIN DEL DOCUMENTO**

*Última actualización: Diciembre 2024*
