<?php
/**
 * AssetTrack Chat – Proxy endpoint con streaming SSE
 * Ruta sugerida: /chat/api/chat.php
 *
 * Variables de entorno necesarias (configurar en Plesk > PHP Settings o .env):
 *   ANTHROPIC_API_KEY   → tu clave de API de Anthropic
 */

declare(strict_types=1);

// ─── Cabeceras CORS y SSE ──────────────────────────────────────────────────
header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-store');
header('X-Accel-Buffering: no');           // importante para nginx/Plesk
header('Access-Control-Allow-Origin: *');  // ajusta al dominio en producción
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    sseError('Método no permitido');
    exit;
}

// ─── Configuración ────────────────────────────────────────────────────────
$apiKey = getenv('ANTHROPIC_API_KEY') ?: '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';   // fallback para test local — en producción usar variable de entorno Plesk

if (empty($apiKey)) {
    http_response_code(500);
    sseError('API key no configurada');
    exit;
}

const MODEL           = 'claude-3-5-haiku-20241022';  // rápido y económico
const MAX_TOKENS      = 1024;
const MAX_INPUT_CHARS = 2000;   // máx caracteres por mensaje de usuario
const RATE_LIMIT_MAX  = 20;     // máx peticiones por IP en la ventana
const RATE_LIMIT_WIN  = 60;     // ventana en segundos

// ─── Rate limiting simple basado en archivos ───────────────────────────────
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit($ip)) {
    http_response_code(429);
    sseError('Demasiadas peticiones. Espera un momento.');
    exit;
}

// ─── Leer y validar body ───────────────────────────────────────────────────
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!isset($body['messages']) || !is_array($body['messages'])) {
    http_response_code(400);
    sseError('Formato de petición inválido');
    exit;
}

$messages = sanitizeMessages($body['messages']);

if (empty($messages)) {
    http_response_code(400);
    sseError('No hay mensajes válidos');
    exit;
}

// ─── System prompt ─────────────────────────────────────────────────────────
// Puedes externalizar esto a un archivo .txt o tabla de BD en Fase 2
$systemPrompt = <<<'PROMPT'
Eres el asistente virtual de AssetTrack, una solución ERP especializada en la gestión 
de equipos audiovisuales para empresas de alquiler de material AV (cámaras, focos, 
trípodes, micrófonos, mezcladores, pantallas, proyectores, etc.).

## Tu función
Ayudar a los potenciales clientes a entender qué es AssetTrack, qué problemas resuelve 
y cómo puede mejorar su negocio. Responde siempre en el mismo idioma en que te escriban.

## Sobre AssetTrack
- **¿Qué es?** Un ERP web on-premise diseñado específicamente para empresas de alquiler 
  de equipos audiovisuales. Sin licencias mensuales, sin límite de usuarios, sin restricciones 
  por módulos. El cliente paga una vez y el sistema es suyo.
- **Desarrollado por:** Innovabyte (Valencia, España)
- **Web:** assettrack.innovabyte.es
- **Contacto / Demo:** hola@innovabyte.es

## Módulos y funcionalidades principales

### 1. Gestión de Presupuestos
- Presupuestos bilingües (ES/EN)
- Versionado automático con trazabilidad completa de cambios
- Sistema de descuentos en cascada (por nivel de cliente, por evento, por volumen)
- Coeficientes de reducción por días de alquiler
- Cálculo automático de totales e IVA
- Conversión directa presupuesto → factura

### 2. Inventario Dual (Artículos + Unidades físicas)
- **Nivel comercial:** artículos genéricos que usan los comerciales para presupuestar
- **Nivel técnico:** unidades físicas con número de serie, estado y ubicación
- Tecnología NFC integrada para identificar equipos en campo
- Trazabilidad completa del historial de uso por unidad
- Estados personalizados por unidad (disponible, alquilado, avería, mantenimiento...)

### 3. Sistema de KITs
- KITs de equipos predefinidos y personalizables
- Artículos y KITs anidados (KITs dentro de KITs)
- Descuentos automáticos al aplicar KIT completo
- Versionado de KITs y plantillas reutilizables

### 4. Análisis de Rentabilidad
- Vista dual: precio comercial (lo que cobra al cliente) vs. precio real (coste interno)
- Análisis de márgenes por evento
- Dashboard ejecutivo con KPIs
- Reportes de rentabilidad por evento, cliente, período

### 5. Facturación y Cumplimiento VeriFact
- Facturación automática desde presupuesto aprobado
- Cumplimiento normativa AEAT (VeriFact)
- Multi-empresa: gestión de varias empresas desde un solo sistema
- Facturas rectificativas y abonos automatizados
- Formas de pago flexibles e historial completo

### 6. Control de Flota de Vehículos
- Historial completo de mantenimiento por vehículo
- Control de kilometraje automático
- Alertas de ITV y revisiones periódicas
- Análisis de costes operativos
- Asignación de conductores por evento

### 7. Gestión de Técnicos y Personal
- Calendario de técnicos y disponibilidad
- Control de vacaciones y ausencias
- Asignación de técnicos a eventos
- Roles y permisos por usuario
- Historial de trabajo por técnico

### 8. Multi-Empresa
- Soporte para empresas reales y empresas ficticias (para análisis internos)
- Facturación y datos fiscales independientes por empresa
- Una sola instalación gestiona todo el grupo empresarial

### 9. Tecnología NFC
- Lectura de tags NFC para identificar equipos en campo
- Verificación rápida de inventario sin teclear
- Trazabilidad en tiempo real de qué equipo está en qué evento

## Flujo de trabajo típico
1. Cliente llama con necesidades del evento
2. Comercial crea presupuesto con artículos/KITs en minutos
3. Versiones y negociación hasta aprobación
4. Técnico asigna unidades físicas específicas
5. Preparación y verificación con NFC
6. Asignación de vehículo y conductor
7. Técnicos montan equipos en ubicación
8. Factura generada automáticamente con VeriFact

## Métricas y escala del sistema
- 150+ tablas en base de datos
- 100+ endpoints de API
- 90+ vistas SQL optimizadas
- 30+ módulos funcionales
- 45+ triggers automatizados
- 10.000+ líneas de código PHP
- Reducción de errores: 85%
- Ahorro de tiempo en presupuestos: 60%
- Mejora de rentabilidad media: 35%

## Ventaja competitiva frente a ERPs genéricos
Los ERPs genéricos no tienen inventario dual artículos/unidades, ni coeficientes por días 
de alquiler, ni versionado de presupuestos, ni integración NFC, ni análisis precio real vs. 
comercial, ni empresas ficticias para análisis. AssetTrack fue diseñado desde cero para 
este sector específico.

## Tono y límites
- Sé amigable, profesional y conciso.
- Si te preguntan por precios, explica que es una solución on-premise sin licencias 
  mensuales y que el coste depende de las necesidades; recomienda pedir una demo 
  gratuita de 30 minutos escribiendo a hola@innovabyte.es
- Si te preguntan algo que no sabes con certeza, di que no tienes esa información exacta 
  y recomienda contactar directamente: hola@innovabyte.es
- NO inventes funcionalidades que no están listadas arriba.
- Responde siempre en español salvo que el usuario escriba en otro idioma.
- Sé conciso: respuestas de 2-4 párrafos máximo salvo que pidan detalle.
PROMPT;

// ─── Llamada a la API con streaming ──────────────────────────────────────
$payload = json_encode([
    'model'      => MODEL,
    'max_tokens' => MAX_TOKENS,
    'system'     => $systemPrompt,
    'messages'   => $messages,
    'stream'     => true,
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'anthropic-version: 2023-06-01',
        'x-api-key: ' . $apiKey,
    ],
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_WRITEFUNCTION  => function ($curl, $data) {
        // Reenviar cada chunk SSE al cliente directamente
        echo $data;
        if (ob_get_level() > 0) ob_flush();
        flush();
        return strlen($data);
    },
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$ok = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$ok || $httpCode >= 400) {
    sseError('Error al conectar con el servicio de IA. Inténtalo de nuevo.');
}

// Señal de fin de stream
echo "data: [DONE]\n\n";
if (ob_get_level() > 0) ob_flush();
flush();

// ─── Funciones auxiliares ─────────────────────────────────────────────────

/**
 * Envía un error como evento SSE y termina.
 */
function sseError(string $message): void
{
    $data = json_encode(['error' => $message]);
    echo "data: {$data}\n\n";
    if (ob_get_level() > 0) ob_flush();
    flush();
    exit;
}

/**
 * Sanitiza y valida el array de mensajes recibidos del frontend.
 * Solo permite roles 'user' y 'assistant', limita longitud de contenido.
 */
function sanitizeMessages(array $messages): array
{
    $clean = [];
    foreach ($messages as $msg) {
        if (!isset($msg['role'], $msg['content'])) continue;
        if (!in_array($msg['role'], ['user', 'assistant'], true)) continue;

        $content = trim((string) $msg['content']);
        if ($content === '') continue;
        if (strlen($content) > MAX_INPUT_CHARS) {
            $content = substr($content, 0, MAX_INPUT_CHARS);
        }

        $clean[] = [
            'role'    => $msg['role'],
            'content' => $content,
        ];
    }

    // La API de Anthropic requiere que el primer mensaje sea del usuario
    // y que los roles alternen. Nos aseguramos de ello.
    $validated = [];
    $lastRole  = '';
    foreach ($clean as $msg) {
        if ($msg['role'] === $lastRole) continue; // eliminar duplicados consecutivos
        $validated[] = $msg;
        $lastRole    = $msg['role'];
    }

    // Debe empezar por 'user'
    while (!empty($validated) && $validated[0]['role'] !== 'user') {
        array_shift($validated);
    }

    return array_values($validated);
}

/**
 * Rate limiting basado en archivos temporales.
 * En producción con mucho tráfico, sustituir por Redis o tabla MySQL.
 */
function checkRateLimit(string $ip): bool
{
    $dir   = sys_get_temp_dir() . '/at_chat_rl';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }

    $file = $dir . '/' . md5($ip) . '.json';
    $now  = time();
    $data = ['count' => 0, 'start' => $now];

    if (file_exists($file)) {
        $stored = json_decode(file_get_contents($file), true);
        if ($stored && ($now - $stored['start']) < RATE_LIMIT_WIN) {
            $data = $stored;
        }
    }

    $data['count']++;
    file_put_contents($file, json_encode($data), LOCK_EX);

    return $data['count'] <= RATE_LIMIT_MAX;
}
