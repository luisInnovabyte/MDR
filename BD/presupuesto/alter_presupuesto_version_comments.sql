-- ============================================
-- DOCUMENTACIÓN DE CAMPOS - presupuesto_version
-- ============================================
-- Proyecto: MDR ERP Manager
-- Base de datos: toldos_db
-- Fecha: 20 de enero de 2026
-- ============================================
-- 
-- Este script actualiza los COMMENT de cada campo de la tabla
-- presupuesto_version para mejorar la comprensión del sistema
-- de versionado de presupuestos.
--
-- CONCEPTO CLAVE:
-- - id_version_presupuesto: ID único de tabla (puede ser 1, 2, 3... AUTO_INCREMENT)
-- - numero_version_presupuesto: Número de versión lógico del presupuesto (1, 2, 3...)
--
-- EJEMPLO:
-- Presupuesto A:
--   - Versión 1 → id_version_presupuesto = 1, numero_version_presupuesto = 1
--   - Versión 2 → id_version_presupuesto = 2, numero_version_presupuesto = 2
--
-- Presupuesto B (creado después):
--   - Versión 1 → id_version_presupuesto = 3, numero_version_presupuesto = 1
--                 ↑ ID de tabla diferente      ↑ Primera versión de ESTE presupuesto
--
-- ============================================

USE toldos_db;

-- ============================================
-- ALTER TABLE CON COMENTARIOS DETALLADOS
-- ============================================

ALTER TABLE presupuesto_version

-- ============================================
-- CAMPO 1: ID de Tabla (PRIMARY KEY)
-- ============================================
MODIFY COLUMN id_version_presupuesto INT UNSIGNED NOT NULL AUTO_INCREMENT
    COMMENT '🔑 ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. 
            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,
            pero cada presupuesto tendrá sus propias versiones 1 y 2',

-- ============================================
-- CAMPO 2: Relación con Cabecera
-- ============================================
MODIFY COLUMN id_presupuesto INT UNSIGNED NOT NULL
    COMMENT '🔗 FK a tabla presupuesto (cabecera). 
            Indica a qué presupuesto pertenece esta versión.
            Múltiples versiones pueden apuntar al mismo id_presupuesto',

-- ============================================
-- CAMPO 3: Número de Versión Lógico
-- ============================================
MODIFY COLUMN numero_version_presupuesto INT UNSIGNED NOT NULL
    COMMENT '📊 Número LÓGICO de versión dentro de este presupuesto (1, 2, 3...).
            Es secuencial DENTRO de cada presupuesto.
            Presupuesto A: versiones 1, 2, 3
            Presupuesto B: versiones 1, 2 (independiente de A)
            ⚠️ NO confundir con id_version_presupuesto',

-- ============================================
-- CAMPO 4: Versión Padre (Genealogía)
-- ============================================
MODIFY COLUMN version_padre_presupuesto INT UNSIGNED DEFAULT NULL
    COMMENT '👨‍👦 ID de la versión anterior (genealogía).
            NULL = Versión original (primera)
            Si tiene valor = ID de la versión desde la cual se creó esta
            Ejemplo: Versión 3 creada desde versión 2 → version_padre = id de versión 2
            Permite rastrear el árbol de cambios',

-- ============================================
-- CAMPO 5: Estado de la Versión
-- ============================================
MODIFY COLUMN estado_version_presupuesto 
    ENUM('borrador','enviado','aprobado','rechazado','cancelado') 
    COLLATE utf8mb4_spanish_ci 
    NOT NULL 
    DEFAULT 'borrador'
    COMMENT '📋 Estado específico de ESTA versión (workflow).
            - borrador: En edición, se pueden modificar líneas
            - enviado: Enviado al cliente, bloqueado para edición
            - aprobado: Cliente aceptó, bloqueado permanentemente
            - rechazado: Cliente rechazó, se puede crear nueva versión
            - cancelado: Versión cancelada, no se usa
            ⚠️ Solo "borrador" permite editar líneas',

-- ============================================
-- CAMPO 6: Motivo de Modificación
-- ============================================
MODIFY COLUMN motivo_modificacion_version TEXT COLLATE utf8mb4_spanish_ci
    COMMENT '📝 Razón por la que se creó esta versión.
            - Versión 1: "Versión inicial"
            - Versión 2: "Cliente solicitó cambio de precios"
            - Versión 3: "Reducción de equipos por presupuesto"
            Ayuda a entender el historial de cambios',

-- ============================================
-- CAMPO 7: Fecha de Creación
-- ============================================
MODIFY COLUMN fecha_creacion_version TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT '📅 Fecha y hora de creación de esta versión.
            Se establece automáticamente al crear el registro.
            Útil para auditoría y timeline del presupuesto',

-- ============================================
-- CAMPO 8: Usuario Creador
-- ============================================
MODIFY COLUMN creado_por_version INT UNSIGNED NOT NULL
    COMMENT '👤 ID del usuario que creó esta versión.
            Permite rastrear responsabilidades.
            TODO: Vincular con tabla usuarios cuando exista
            Actualmente = 1 por defecto',

-- ============================================
-- CAMPO 9: Fecha de Envío
-- ============================================
MODIFY COLUMN fecha_envio_version DATETIME DEFAULT NULL
    COMMENT '📧 Fecha y hora de envío al cliente.
            NULL = Aún no enviado
            Se establece automáticamente al cambiar estado a "enviado"
            Marca el momento en que el cliente recibió esta versión',

-- ============================================
-- CAMPO 10: Usuario que Envió
-- ============================================
MODIFY COLUMN enviado_por_version INT UNSIGNED DEFAULT NULL
    COMMENT '👤 ID del usuario que envió esta versión al cliente.
            NULL = No enviado aún
            Permite rastrear quién realizó el envío',

-- ============================================
-- CAMPO 11: Fecha de Aprobación
-- ============================================
MODIFY COLUMN fecha_aprobacion_version DATETIME DEFAULT NULL
    COMMENT '✅ Fecha y hora de aprobación del cliente.
            NULL = No aprobado
            Se establece al cambiar estado a "aprobado"
            Importante para facturación y producción',

-- ============================================
-- CAMPO 12: Fecha de Rechazo
-- ============================================
MODIFY COLUMN fecha_rechazo_version DATETIME DEFAULT NULL
    COMMENT '❌ Fecha y hora de rechazo del cliente.
            NULL = No rechazado
            Se establece al cambiar estado a "rechazado"
            Indica que se necesita nueva versión',

-- ============================================
-- CAMPO 13: Motivo del Rechazo
-- ============================================
MODIFY COLUMN motivo_rechazo_version TEXT COLLATE utf8mb4_spanish_ci
    COMMENT '💬 Razón por la que el cliente rechazó esta versión.
            NULL = No rechazado o no especificado
            Ejemplo: "Precio muy alto", "Faltan equipos"
            Ayuda a crear la siguiente versión correctamente',

-- ============================================
-- CAMPO 14: Ruta del PDF Generado
-- ============================================
MODIFY COLUMN ruta_pdf_version VARCHAR(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL
    COMMENT '📄 Ruta del archivo PDF generado para esta versión.
            NULL = PDF no generado
            Formato: /documentos/presupuestos/P-00001-2026_v1.pdf
            Se genera automáticamente al enviar.
            Mantiene histórico de PDFs enviados',

-- ============================================
-- CAMPO 15: Soft Delete
-- ============================================
MODIFY COLUMN activo_version TINYINT(1) DEFAULT 1
    COMMENT '🗑️ Soft delete: 1=activo, 0=eliminado lógicamente.
            NO usar DELETE físico, cambiar a 0 para "eliminar"
            Mantiene histórico completo en BD',

-- ============================================
-- CAMPO 16: Timestamp de Creación
-- ============================================
MODIFY COLUMN created_at_version TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT '⏱️ Timestamp de creación del registro en BD.
            Auditoría técnica del sistema',

-- ============================================
-- CAMPO 17: Timestamp de Actualización
-- ============================================
MODIFY COLUMN updated_at_version TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    COMMENT '⏱️ Timestamp de última actualización del registro.
            Se actualiza automáticamente en cada UPDATE.
            Auditoría técnica del sistema';

-- ============================================
-- VERIFICAR CAMBIOS
-- ============================================

DESCRIBE presupuesto_version;

-- ============================================
-- CONSULTA EXPLICATIVA
-- ============================================

SELECT 
    '========================================' AS 'EXPLICACIÓN',
    'id_version_presupuesto = ID de TABLA' AS 'Concepto 1',
    'numero_version_presupuesto = Versión LÓGICA' AS 'Concepto 2',
    '========================================' AS 'Separador',
    'Tu caso:' AS 'Tu Situación',
    'id_version_presupuesto = 2' AS 'ID Tabla',
    'numero_version_presupuesto = 1' AS 'Número Versión',
    '¿Por qué ID=2 si es versión 1?' AS 'Pregunta',
    'Probablemente existió un ID=1 antes (otra prueba/presupuesto)' AS 'Respuesta';

-- ============================================
-- EJEMPLO PRÁCTICO
-- ============================================

/*
EJEMPLO REAL DE CÓMO FUNCIONA:
================================

PRESUPUESTO A (id_presupuesto = 5):
-----------------------------------
| id_version | id_presupuesto | numero_version | estado    | notas                    |
|------------|----------------|----------------|-----------|--------------------------|
| 10         | 5              | 1              | borrador  | Primera versión          |
| 15         | 5              | 2              | enviado   | Cliente pidió cambios    |
| 20         | 5              | 3              | aprobado  | Versión final aprobada   |

PRESUPUESTO B (id_presupuesto = 8):
-----------------------------------
| id_version | id_presupuesto | numero_version | estado    | notas                    |
|------------|----------------|----------------|-----------|--------------------------|
| 25         | 8              | 1              | borrador  | Primera versión          |
| 30         | 8              | 2              | aprobado  | Aprobado directamente    |

NOTAS:
- Los id_version son únicos en toda la tabla (10, 15, 20, 25, 30...)
- Los numero_version son relativos a cada presupuesto (1, 2, 3...)
- Presupuesto A tiene 3 versiones (1, 2, 3)
- Presupuesto B tiene 2 versiones (1, 2)
- Los IDs de tabla no son consecutivos (pueden haber gaps por eliminaciones)

TU CASO:
--------
| id_version | id_presupuesto | numero_version | estado    |
|------------|----------------|----------------|-----------|
| 2          | 10             | 1              | borrador  |

Explicación:
- id_version = 2: Es el segundo registro que se ha creado en la tabla presupuesto_version
  (probablemente hubo un id=1 antes, de una prueba o presupuesto anterior)
- numero_version = 1: Es la PRIMERA versión de TU presupuesto (id_presupuesto=10)
- Todo funciona correctamente, el ID 2 no es un problema
*/

-- ============================================
-- QUERY PARA VER TU PRESUPUESTO
-- ============================================

SELECT 
    p.id_presupuesto AS 'ID Presupuesto',
    p.numero_presupuesto AS 'Número',
    p.version_actual_presupuesto AS 'Versión Actual',
    pv.id_version_presupuesto AS 'ID Versión (Tabla)',
    pv.numero_version_presupuesto AS 'Número Versión (Lógico)',
    pv.estado_version_presupuesto AS 'Estado',
    pv.motivo_modificacion_version AS 'Motivo',
    pv.fecha_creacion_version AS 'Fecha Creación'
FROM presupuesto p
INNER JOIN presupuesto_version pv 
    ON pv.id_presupuesto = p.id_presupuesto
WHERE p.id_presupuesto = 10  -- Tu presupuesto
ORDER BY pv.numero_version_presupuesto;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

SELECT 
    '✅ Comentarios actualizados correctamente' AS Resultado,
    'Ejecuta: DESCRIBE presupuesto_version; para ver los comentarios' AS Instrucción,
    'El ID 2 es normal, no indica un error' AS Aclaración;
