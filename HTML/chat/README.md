# AssetTrack Chat Widget — Guía de instalación

## Estructura de archivos

```
/chat/
├── widget/
│   ├── assettrack-chat.js     ← script embebible
│   └── assettrack-chat.css    ← estilos del widget
└── api/
    └── chat.php               ← endpoint proxy (PHP 8.x)
```

## 1. Subir archivos a Plesk

Sube la carpeta `/chat/` al directorio raíz del dominio de AssetTrack.
Ejemplo: `httpdocs/chat/`

## 2. Configurar la API key en Plesk

En **Plesk > Dominios > assettrack.innovabyte.es > PHP Settings**, añade:

```
ANTHROPIC_API_KEY = sk-ant-xxxxxxxxxxxxxxxxxxxxxxxx
```

O bien crea un archivo `.env` (si usas algún cargador), o define la variable
de entorno a nivel de VirtualHost en la configuración de Apache/Nginx.

## 3. Verificar rutas en el JS

Abre `widget/assettrack-chat.js` y revisa las primeras líneas del CONFIG:

```js
apiEndpoint: '/chat/api/chat.php',   // ruta al endpoint PHP
```

Y en la función `injectStyles()`:

```js
link.href = '/chat/widget/assettrack-chat.css';
```

Ajusta según la ruta real en tu servidor.

## 4. Embeber el widget en la página

Añade esta línea justo antes del cierre de `</body>` en la landing de AssetTrack:

```html
<script src="/chat/widget/assettrack-chat.js" defer></script>
```

¡Listo! El botón flotante naranja aparecerá en la esquina inferior derecha.

## 5. Personalizar el system prompt

Edita el string `$systemPrompt` en `api/chat.php` para añadir más información
sobre AssetTrack: funcionalidades, precios, FAQs, casos de uso, etc.

---

## Opciones de mejora (Fase 2)

- **System prompt externo:** mover el prompt a un archivo `system-prompt.txt`
  o a una tabla MySQL, editable sin tocar código.
- **Log de conversaciones:** guardar en MySQL para analizar las dudas más frecuentes.
- **Rate limiting con MySQL:** más robusto que el basado en archivos.
- **CORS restringido:** cambiar `Access-Control-Allow-Origin: *` por el dominio exacto.
- **Modelo:** cambiar `claude-haiku-4-5-20251001` a `claude-sonnet-4-6` para respuestas
  más elaboradas (mayor coste por token).
