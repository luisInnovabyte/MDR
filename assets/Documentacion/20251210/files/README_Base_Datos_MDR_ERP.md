# MDR ERP MANAGER - Documentación de Base de Datos

**Versión:** 2.0  
**Fecha:** 10 de diciembre de 2025  
**Sistema:** ERP completo para gestión de alquiler de equipos audiovisuales

---

## 📋 Descripción General

Sistema de gestión empresarial especializado en alquiler de equipos audiovisuales que implementa:

- ✅ Gestión completa de inventario con trazabilidad por número de serie
- ✅ Sistema dual: Artículos genéricos (administración) + Elementos específicos (técnicos)
- ✅ Presupuestos y facturación multi-empresa
- ✅ Control de ubicación física en almacén (Nave/Pasillo/Altura)
- ✅ Gestión documental y fotográfica por elemento
- ✅ Coeficientes reductores para alquileres multi-día
- ✅ Sistema de pagos flexible con fraccionamiento
- ✅ Observaciones multinivel para presupuestos
- ✅ Cumplimiento normativa VeriFact (AEAT España)
- ✅ Soporte bilingüe español/inglés

---

## 🏗️ Arquitectura de Datos

### Jerarquía Principal de Productos

```
GRUPO (Nivel 1)
  └─ Audio, Video, Iluminación, Estructuras...
     │
     └─ FAMILIA (Nivel 2)
        └─ Microfonía, Altavoces, Proyección...
           │
           └─ ARTÍCULO (Nivel 3)
              └─ TV-40", Micrófono inalámbrico...
                 │
                 └─ ELEMENTO (Nivel 4)
                    └─ TV-40"-001, TV-40"-002 (con número de serie)
```

### Flujo de Trabajo

```
1. CATÁLOGO
   Admin crea: Grupo → Familia → Artículo
   Técnico registra: Elementos físicos con NFC

2. PRESUPUESTO
   Admin usa: Artículos genéricos
   Cliente: Aprueba presupuesto
   
3. OPERATIVA
   Técnico asigna: Elementos específicos
   Técnico usa: NFC para picking/devolución
   
4. FACTURACIÓN
   Sistema genera: Factura con empresa real
   Cumple: Normativa VeriFact
```

---

## 📊 Estructura de Tablas (Resumen)

### 1️⃣ Configuración Básica

| Tabla | Descripción | Registros típicos |
|-------|-------------|------------------|
| `tipo_iva` | IVA y recargo equivalencia | 21%, 10%, 4% |
| `metodo_pago` | Métodos (Transferencia, Tarjeta...) | 7 métodos |
| `forma_pago` | Formas con fraccionamiento | Contado, 40%+60%... |
| `estado_presupuesto` | Estados del presupuesto | Pendiente, Aceptado... |
| `unidad_medida` | Unidades | Unidades, Metros, m²... |
| `coeficiente` | Reductores multi-día | Día 1: 1.00, Día 2: 0.80... |

### 2️⃣ Clientes y Proveedores

| Tabla | Descripción | Relaciones |
|-------|-------------|-----------|
| `cliente` | Datos de clientes | → forma_pago_habitual |
| `contacto_cliente` | Contactos del cliente | → cliente |
| `proveedor` | Datos de proveedores | - |
| `contacto_proveedor` | Contactos del proveedor | → proveedor |

**Vista destacada:** `contacto_cantidad_cliente` (incluye forma de pago y cantidad de contactos)

### 3️⃣ Catálogo de Productos

| Tabla | Descripción | Campos clave |
|-------|-------------|--------------|
| `grupo_articulo` | Nivel 1: Audio, Video, Iluminación... | codigo_grupo, nombre_grupo |
| `familia` | Nivel 2: Microfonía, Proyección... | coeficiente_familia, observaciones_presupuesto |
| `marca` | Marcas de equipos | Shure, Behringer, Sennheiser... |
| `articulo` | Nivel 3: Productos genéricos | precio_alquiler, es_kit, notas_presupuesto (bilingüe) |

**Vista destacada:** `vista_articulo_completa` (toda la jerarquía con cálculos)

### 4️⃣ Inventario Físico

| Tabla | Descripción | Campos clave |
|-------|-------------|--------------|
| `estado_elemento` | Estados (Disponible, Alquilado...) | permite_alquiler, color |
| `elemento` | Unidades físicas con serie | nave, pasillo_columna, altura, numero_serie |
| `documento_elemento` | Manuales, garantías, certificados | archivo_documento, privado_documento |
| `foto_elemento` | Fotos del elemento | archivo_foto, privado_foto |

**Vistas destacadas:** 
- `vista_elemento_completa` (solo activos)
- `vista_elementos_completa` (todos)

### 5️⃣ Observaciones

| Tabla | Descripción | Uso |
|-------|-------------|-----|
| `observacion_general` | Textos estándar bilingües | Condiciones de pago, términos legales... |

**Observaciones multinivel:**
1. **Generales:** Texto estándar reutilizable
2. **Familias:** Específicas por categoría (ej: "Iluminación requiere técnico")
3. **Artículos:** Por producto (ej: "Requiere corriente trifásica")
4. **Presupuesto:** Específicas del proyecto

### 6️⃣ Empresas y Facturación

| Tabla | Descripción | Características |
|-------|-------------|-----------------|
| `empresa` | Empresas del grupo | Ficticias (presupuestos) + Reales (facturas) |

**Características:**
- ✅ Una empresa **ficticia principal** para presupuestos
- ✅ Múltiples empresas **reales** para facturación
- ✅ Series independientes (P2024-0001, F2024/0001)
- ✅ Cumplimiento **VeriFact** para empresas españolas
- ✅ Procedimientos almacenados para numeración

**Procedimientos disponibles:**
- `sp_obtener_siguiente_numero(empresa, tipo)`
- `sp_actualizar_contador_empresa(id, tipo)`
- `sp_obtener_empresa_ficticia_principal()`
- `sp_listar_empresas_facturacion()`

### 7️⃣ Presupuestos (Estructura propuesta)

| Tabla | Descripción | Estado |
|-------|-------------|--------|
| `presupuesto` | Cabecera | Propuesta |
| `linea_presupuesto` | Líneas de detalle | Propuesta |
| `presupuesto_observacion` | Observaciones vinculadas | Propuesta |

---

## 🔑 Características Técnicas Destacadas

### Triggers Automáticos

```sql
-- Generación automática de códigos
trg_elemento_before_insert
  → Genera: CODIGO_ARTICULO-001, CODIGO_ARTICULO-002...

-- Validación de empresa ficticia principal
trg_empresa_validar_ficticia_principal
  → Asegura que solo existe UNA empresa ficticia principal
```

### Vistas Consolidadas

```sql
-- Vista completa de artículos con jerarquía
vista_articulo_completa
  → Incluye: grupo, familia, unidad, coeficiente efectivo, imagen efectiva

-- Vista de elementos con ubicación
vista_elemento_completa (solo activos)
vista_elementos_completa (todos)
  → Incluye: ubicación completa, estado, garantía, mantenimiento

-- Vista de clientes con forma de pago
contacto_cantidad_cliente
  → Incluye: forma de pago habitual, método, cantidad de contactos
```

### Sistema de Ubicación (3 niveles)

```
┌─────────────────────────────────────────┐
│ NAVE: "Nave 1", "Nave Principal"       │
│   ├─ PASILLO/COLUMNA: "A-5", "B-12"    │
│   │    └─ ALTURA: "Planta baja", "2m"  │
│   └─ Ejemplo: Nave 1 | A-5 | Planta 2  │
└─────────────────────────────────────────┘
```

### Pagos Fraccionados

```sql
-- Ejemplo: 40% anticipo + 60% al finalizar
porcentaje_anticipo_pago = 40.00
dias_anticipo_pago = 0  (al firmar)
porcentaje_final_pago = 60.00
dias_final_pago = 0  (al finalizar evento)

-- Ejemplo: 30% anticipo + 70% a 7 días antes
porcentaje_anticipo_pago = 30.00
dias_anticipo_pago = 0
porcentaje_final_pago = 70.00
dias_final_pago = -7  (negativo = días antes del evento)
```

### Coeficientes Reductores

```sql
-- Descuento por alquiler multi-día
Día 1: Precio x 1.00 = 100%
Día 2: Precio x 0.80 = 80%
Día 3: Precio x 0.70 = 70%
Día 4+: Precio x 0.60 = 60%

-- Se aplica a nivel:
- Familia (todos los artículos)
- Artículo individual (override)
- Línea de presupuesto (override específico)
```

---

## 🔍 Consultas Rápidas Útiles

### Ver catálogo completo
```sql
SELECT * FROM vista_articulo_completa 
WHERE activo_articulo = 1
ORDER BY nombre_grupo, nombre_familia, nombre_articulo;
```

### Ver inventario con ubicaciones
```sql
SELECT 
    codigo_elemento,
    descripcion_elemento,
    nave_elemento,
    pasillo_columna_elemento,
    altura_elemento,
    descripcion_estado_elemento,
    precio_compra_elemento
FROM vista_elemento_completa
WHERE permite_alquiler_estado_elemento = TRUE;
```

### Ver clientes con forma de pago
```sql
SELECT 
    nombre_cliente,
    nombre_pago,
    descripcion_forma_pago_cliente,
    cantidad_contactos_cliente
FROM contacto_cantidad_cliente
WHERE activo_cliente = 1;
```

### Obtener siguiente número de presupuesto
```sql
CALL sp_obtener_siguiente_numero('FICTICIA', 'presupuesto', @numero);
SELECT @numero;
-- Resultado: P2025-0001
```

### Obtener siguiente número de factura
```sql
CALL sp_obtener_siguiente_numero('MDR01', 'factura', @numero);
SELECT @numero;
-- Resultado: F2025/0001
```

---

## 📦 Datos de Ejemplo Incluidos

El archivo incluye **inserts de ejemplo** para:

✅ Grupos de artículos (8 categorías principales)
```
AUD - Audio
VID - Video  
ILU - Iluminación
EST - Estructuras
ACC - Accesorios
COM - Comunicaciones
ELE - Eléctrico
MOB - Mobiliario
```

✅ Familias (5 familias de ejemplo con observaciones)
```
- Microfonía y Sonido
- Iluminación Profesional
- Cableado y Conectores
- Video y Proyección
- Estructuras y Rigging
```

✅ Artículos (5 artículos completos con descripción bilingüe)
```
- Micrófono inalámbrico Shure SM58
- Kit iluminación básica (4 PAR LED)
- Consola digital Behringer X32
- Cable XLR 10 metros
- Pantalla LED modular P3
```

✅ Empresas (3 empresas configuradas)
```
- FICTICIA: Para presupuestos (principal)
- MDR AUDIOVISUALES S.L.: Real para facturación
- MDR EVENTOS Y PRODUCCIONES S.L.: Real alternativa
```

---

## 🚀 Próximos Pasos Recomendados

### Fase 1: Presupuestos (Corto plazo)
- [ ] Implementar tabla `presupuesto` completa
- [ ] Implementar tabla `linea_presupuesto`
- [ ] Implementar tabla `presupuesto_observacion`
- [ ] Procedimientos para cálculo de totales
- [ ] Generación de PDF bilingüe

### Fase 2: Operativa (Medio plazo)
- [ ] Tabla `pedido` (conversión desde presupuesto)
- [ ] Tabla `albaran_salida` y `albaran_entrada`
- [ ] Asignación de elementos específicos a pedidos
- [ ] Integración NFC para picking/devolución

### Fase 3: Facturación (Medio plazo)
- [ ] Tabla `factura` con cumplimiento VeriFact
- [ ] Tabla `factura_rectificativa` (abonos)
- [ ] Integración con API VeriFact AEAT
- [ ] Generación XML para envío

### Fase 4: Mantenimiento (Largo plazo)
- [ ] Tabla `plan_mantenimiento`
- [ ] Tabla `registro_mantenimiento`
- [ ] Alertas automáticas de vencimientos
- [ ] Histórico de reparaciones

### Fase 5: Analytics y AI (Futuro)
- [ ] Dashboard de KPIs
- [ ] Asistente IA para presupuestos
- [ ] Predicción de disponibilidad
- [ ] Optimización de rutas de entrega

---

## 🛠️ Herramientas y Tecnologías

- **Base de datos:** MySQL 8.0+
- **Motor:** InnoDB
- **Charset:** utf8mb4 (soporte emojis y caracteres internacionales)
- **Collation:** utf8mb4_0900_ai_ci (case insensitive)
- **Triggers:** Generación automática de códigos
- **Vistas:** Consolidación de datos para consultas rápidas
- **Procedimientos:** Lógica de negocio reutilizable

---

## 📝 Notas Importantes

### Nomenclatura de Campos
- Singular para nombres de tablas: `cliente`, `articulo`, `elemento`
- Campos sufijados con nombre de tabla: `nombre_cliente`, `precio_articulo`
- FK prefijadas con `id_`: `id_cliente`, `id_articulo`

### Convenciones de Código
- Códigos automáticos: Usar triggers BEFORE INSERT
- Códigos manuales: VARCHAR con UNIQUE constraint
- Timestamps: `created_at_*` y `updated_at_*` automáticos
- Soft delete: Campo `activo_*` en vez de DELETE físico

### Seguridad y Privacidad
- Campos `privado_documento` y `privado_foto` para control de acceso
- Datos sensibles de empresa (certificados) requieren encriptación
- Validación de permisos a nivel de aplicación

### Performance
- Índices en todas las FK
- Índices en campos de búsqueda frecuente (código, nombre, fecha)
- Vistas materializadas para consultas complejas (futuro)

---

## 📞 Soporte y Contacto

**Desarrollador:** Luis (MDR ERP Manager)  
**Versión actual:** 2.0  
**Última actualización:** 10 de diciembre de 2025

---

## 📄 Licencia

© 2025 MDR Audiovisuales Group. Todos los derechos reservados.  
Sistema desarrollado específicamente para gestión de alquiler de equipos audiovisuales.
