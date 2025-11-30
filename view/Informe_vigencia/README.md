# Calendario de Vigencias de Garantías

## Descripción
Calendario interactivo integrado en el sistema MDR ERP que muestra las fechas de vencimiento de garantías de los elementos del inventario.

## Integración con el Sistema
El calendario está completamente integrado con el template de la aplicación MDR, incluyendo:
- Panel lateral de navegación (sidebar)
- Cabecera del sistema
- Panel derecho
- Footer
- Sistema de permisos
- Breadcrumb de navegación

## Características Implementadas

### ✅ Visualización del Calendario
- Navegación por meses (botones anterior/siguiente)
- Botón "Hoy" para volver al mes actual
- Identificación visual del día actual
- Marcado de fines de semana

### ✅ Eventos de Garantías
Los elementos se muestran en el calendario según la fecha de vencimiento de su garantía, con código de colores:

- **🔴 ROJO** - Garantía Vencida
- **🟠 NARANJA** - Garantía Por Vencer (menos de 30 días)
- **🟢 VERDE** - Garantía Vigente (más de 30 días)

### ✅ Información Mostrada
- **Código del elemento** (ej: "0001-001")
- Al hacer clic en un elemento se muestra:
  - Código completo
  - Artículo
  - Familia
  - Marca (si existe)
  - Descripción
  - Fecha de fin de garantía
  - Estado de la garantía

## Estructura de Archivos

```
Informe_vigencia/
├── index.php           # Página principal del calendario
├── css/
│   └── calendario.css  # Estilos del calendario
├── js/
│   └── calendario.js   # Lógica JavaScript del calendario
└── README.md          # Este archivo
```

## Flujo de Datos

1. **Vista SQL**: `vista_elementos_completa`
   - Contiene todos los elementos con sus datos completos
   - Campo: `fecha_fin_garantia_elemento`
   - Campo calculado: `estado_garantia_elemento` (Vencida/Por vencer/Vigente)

2. **Modelo**: `models/Elemento.php`
   - Método: `getWarrantyEvents($month, $year)`
   - Consulta elementos con garantías en el mes especificado

3. **Controlador**: `controller/elemento.php`
   - Operación: `getWarrantyEvents`
   - Recibe mes y año
   - Devuelve JSON con los elementos

4. **Vista**: `view/Informe_vigencia/index.php`
   - JavaScript carga los eventos al renderizar el calendario
   - Muestra los códigos de elementos en los días correspondientes
   - Aplica colores según el estado de garantía

## Uso

1. **Acceso**: Navegar desde el menú lateral o directamente a `view/Informe_vigencia/index.php`
2. **Navegación**: 
   - Usa los botones **<** y **>** para cambiar de mes
   - Click en **"Hoy"** para volver al mes actual
3. **Visualización**: Los elementos con garantías aparecen en sus fechas correspondientes con colores según estado
4. **Detalles**: Click en cualquier código de elemento para ver información completa
5. **Breadcrumb**: Usa el breadcrumb para navegar de vuelta al Dashboard

## Diseño Responsivo
El calendario se adapta a diferentes tamaños de pantalla:
- **Desktop**: Vista completa con todas las características
- **Tablet**: Calendario compacto con información esencial
- **Móvil**: Diseño optimizado para pantallas pequeñas

## Estados de Garantía

Los estados se calculan automáticamente en la vista SQL:

```sql
CASE 
    WHEN fecha_fin_garantia_elemento < CURDATE() THEN 'Vencida'
    WHEN fecha_fin_garantia_elemento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Por vencer'
    WHEN fecha_fin_garantia_elemento > DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Vigente'
    ELSE 'Sin garantía'
END AS estado_garantia_elemento
```

## Próximas Mejoras Sugeridas

- [ ] Modal Bootstrap para mostrar detalles (en lugar de alert)
- [ ] Filtros por estado de garantía
- [ ] Exportación a PDF/Excel
- [ ] Notificaciones/alertas de garantías próximas a vencer
- [ ] Vista de lista complementaria al calendario
- [ ] Búsqueda de elementos específicos
- [ ] Integración con sistema de mantenimiento

## Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP, PDO
- **Base de datos**: MySQL/MariaDB
- **Frameworks CSS**: Bootstrap 5
- **Iconos**: Font Awesome 6

## Notas Técnicas

- El calendario muestra siempre 42 días (6 semanas completas)
- Los días de otros meses aparecen atenuados
- La primera columna es Lunes (no Domingo)
- Las consultas están optimizadas con índices en las fechas
- Los logs se registran en `public/logs/elemento_debug_*.txt`
