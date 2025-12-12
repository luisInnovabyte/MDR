# Implementación Lab - Estructura de Documentación

> Base de conocimiento para desarrollo MVC con PHP, MySQL, JavaScript, HTML y CSS

---

## Checklist de Documentos a Crear

### 📁 01 - Arquitectura MVC

- [ ] `01_estructura_directorios.md` - Árbol de carpetas estándar del proyecto MVC
- [ ] `02_flujo_request.md` - Ciclo de vida de una petición (entrada → controlador → modelo → vista)
- [ ] `03_convenciones_nombrado.md` - Nomenclatura de archivos, clases, métodos y variables
- [ ] `04_configuracion_proyecto.md` - Archivos de configuración, constantes y entorno

---

### 📁 02 - Base de Datos MySQL

- [ ] `01_plantilla_esquema.sql` - Estructura base para nuevas tablas
- [ ] `02_convenciones_tablas.md` - Nombrado de tablas, campos y relaciones
- [ ] `03_tipos_campos_estandar.md` - Tipos de datos preferidos según uso (fechas, textos, estados, etc.)
- [ ] `04_campos_comunes.md` - Campos que siempre se incluyen (id, timestamps, soft delete, etc.)
- [ ] `05_claves_foraneas.md` - Convención para FK, índices y restricciones
- [ ] `06_triggers_procedimientos.md` - Triggers y stored procedures reutilizables
- [ ] `07_collation_charset.md` - Configuración de caracteres y collation estándar

---

### 📁 03 - Backend PHP

- [ ] `01_modelo_base.md` - Clase Model base con métodos CRUD estándar
- [ ] `02_controlador_base.md` - Clase Controller base con métodos comunes
- [ ] `03_router.md` - Sistema de rutas y mapeo a controladores
- [ ] `04_helpers.md` - Funciones helper reutilizables
- [ ] `05_gestion_sesiones.md` - Manejo de sesiones, autenticación y permisos
- [ ] `06_validaciones.md` - Validación de datos de entrada
- [ ] `07_manejo_errores.md` - Gestión de errores y excepciones
- [ ] `08_conexion_bd.md` - Clase de conexión a base de datos (PDO/mysqli)

---

### 📁 04 - Frontend

- [ ] `01_estructura_vistas.md` - Organización de archivos de vista (layouts, partials, páginas)
- [ ] `02_plantilla_html_base.md` - Estructura HTML estándar con includes
- [ ] `03_componentes_js.md` - Módulos JavaScript reutilizables
- [ ] `04_ajax_comunicacion.md` - Patrón estándar para llamadas AJAX
- [ ] `05_estilos_css.md` - Organización CSS y convenciones
- [ ] `06_formularios.md` - Estructura estándar de formularios y validación cliente

---

### 📁 05 - Patrones CRUD

- [ ] `01_crear_modulo_completo.md` - Guía paso a paso para crear un módulo nuevo
- [ ] `02_plantilla_modelo.php` - Código plantilla para nuevo modelo
- [ ] `03_plantilla_controlador.php` - Código plantilla para nuevo controlador
- [ ] `04_plantilla_vistas.md` - Vistas estándar (listado, formulario, detalle)
- [ ] `05_plantilla_javascript.js` - JS estándar para un módulo CRUD

---

### 📁 06 - Prompts para Claude

- [ ] `01_prompt_generar_tabla.md` - Prompt para crear estructura de tabla MySQL
- [ ] `02_prompt_generar_modelo.md` - Prompt para crear clase Model
- [ ] `03_prompt_generar_controlador.md` - Prompt para crear Controller
- [ ] `04_prompt_generar_vistas.md` - Prompt para crear conjunto de vistas
- [ ] `05_prompt_crud_completo.md` - Prompt para generar módulo CRUD completo
- [ ] `06_prompt_revision_codigo.md` - Prompt para revisar y optimizar código existente

---

## Prioridad Sugerida

| Orden | Documento | Motivo |
|-------|-----------|--------|
| 1 | 02 - Base de Datos (completo) | Es el cimiento de todo el sistema |
| 2 | 03-01 Modelo base | Define cómo interactúas con BD |
| 3 | 03-02 Controlador base | Define el flujo de lógica |
| 4 | 01 - Arquitectura MVC | Documenta la estructura general |
| 5 | 05 - Patrones CRUD | Plantillas reutilizables |
| 6 | 06 - Prompts | Optimiza trabajo con IA |
| 7 | 04 - Frontend | Estandariza la capa visual |

---

## Notas

- **Formato**: Todos los documentos en Markdown (`.md`) excepto código fuente
- **Actualización**: Revisar y actualizar conforme evolucione el stack
- **Uso**: Subir a Project Knowledge en Claude y mantener copia en repositorio local

---

*Última actualización: Diciembre 2024*
