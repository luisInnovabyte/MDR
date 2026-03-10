(function () {
  'use strict';

  // ─── Configuración ────────────────────────────────────────────────────────
  const CONFIG = {
    apiEndpoint: '/MDR/HTML/chat/api/chat.php',   // ← ajusta la ruta según tu servidor
    botName: 'AssetTrack AI',
    welcomeMessage: '¡Hola! Soy el asistente de AssetTrack. ¿En qué puedo ayudarte hoy?',
    placeholder: 'Escribe tu pregunta...',
    maxHistoryMessages: 20,              // máx mensajes que se envían a la API
  };

  // ─── Estado ───────────────────────────────────────────────────────────────
  let conversationHistory = [];
  let isStreaming = false;

  // ─── Inyección de estilos ─────────────────────────────────────────────────
  function injectStyles() {
    if (document.getElementById('at-chat-styles')) return;
    const link = document.createElement('link');
    link.id = 'at-chat-styles';
    link.rel = 'stylesheet';
    link.href = '/MDR/HTML/chat/widget/assettrack-chat.css'; // ← ajusta la ruta
    document.head.appendChild(link);
  }

  // ─── HTML del widget ──────────────────────────────────────────────────────
  function buildWidget() {
    const wrapper = document.createElement('div');
    wrapper.id = 'at-chat-wrapper';
    wrapper.innerHTML = `
      <!-- Botón flotante -->
      <button id="at-chat-toggle" aria-label="Abrir chat AssetTrack" title="¿Tienes dudas sobre AssetTrack?">
        <span class="at-icon-open">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </span>
        <span class="at-icon-close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </span>
      </button>

      <!-- Ventana de chat -->
      <div id="at-chat-window" role="dialog" aria-label="Chat AssetTrack" aria-hidden="true">
        <div id="at-chat-header">
          <div class="at-header-left">
            <div class="at-avatar">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
              </svg>
            </div>
            <div class="at-header-info">
              <span class="at-header-name">${CONFIG.botName}</span>
              <span class="at-header-status"><span class="at-status-dot"></span>En línea</span>
            </div>
          </div>
          <button id="at-chat-minimize" aria-label="Minimizar chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </button>
        </div>

        <div id="at-chat-messages" role="log" aria-live="polite"></div>

        <div id="at-chat-input-area">
          <div id="at-chat-input-wrapper">
            <textarea
              id="at-chat-input"
              placeholder="${CONFIG.placeholder}"
              rows="1"
              maxlength="1000"
              aria-label="Mensaje"
            ></textarea>
            <button id="at-chat-send" aria-label="Enviar mensaje" disabled>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
            </button>
          </div>
          <p class="at-footer-text">Asistente IA de <strong>AssetTrack</strong></p>
        </div>
      </div>
    `;
    document.body.appendChild(wrapper);
  }

  // ─── Helpers de mensajes ──────────────────────────────────────────────────
  function createMessageEl(role) {
    const el = document.createElement('div');
    el.className = `at-message at-message--${role}`;
    const bubble = document.createElement('div');
    bubble.className = 'at-bubble';
    el.appendChild(bubble);
    return { el, bubble };
  }

  function appendMessage(role, text) {
    const messages = document.getElementById('at-chat-messages');
    const { el, bubble } = createMessageEl(role);
    bubble.textContent = text;
    messages.appendChild(el);
    scrollToBottom();
    return bubble;
  }

  function showTypingIndicator() {
    const messages = document.getElementById('at-chat-messages');
    const { el, bubble } = createMessageEl('assistant');
    el.id = 'at-typing-indicator';
    bubble.innerHTML = '<span class="at-dot"></span><span class="at-dot"></span><span class="at-dot"></span>';
    messages.appendChild(el);
    scrollToBottom();
  }

  function removeTypingIndicator() {
    const el = document.getElementById('at-typing-indicator');
    if (el) el.remove();
  }

  function scrollToBottom() {
    const messages = document.getElementById('at-chat-messages');
    messages.scrollTop = messages.scrollHeight;
  }

  function setInputEnabled(enabled) {
    const input = document.getElementById('at-chat-input');
    const btn = document.getElementById('at-chat-send');
    input.disabled = !enabled;
    if (!enabled) {
      btn.disabled = true;
    } else {
      btn.disabled = input.value.trim() === '';
    }
  }

  // ─── Streaming fetch ──────────────────────────────────────────────────────
  async function sendMessage(userText) {
    if (isStreaming || !userText.trim()) return;
    isStreaming = true;
    setInputEnabled(false);

    // Añadir mensaje del usuario al historial y UI
    conversationHistory.push({ role: 'user', content: userText });
    appendMessage('user', userText);

    // Limitar historial enviado a la API
    const historyToSend = conversationHistory.slice(-CONFIG.maxHistoryMessages);

    showTypingIndicator();

    try {
      const response = await fetch(CONFIG.apiEndpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: historyToSend }),
      });

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      removeTypingIndicator();

      // Crear burbuja de respuesta vacía para streaming
      const messages = document.getElementById('at-chat-messages');
      const { el, bubble } = createMessageEl('assistant');
      messages.appendChild(el);
      scrollToBottom();

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let fullText = '';
      let buffer = '';

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split('\n');
        buffer = lines.pop(); // guardar línea incompleta

        for (const line of lines) {
          if (!line.startsWith('data:')) continue;
          const data = line.slice(5).trim();
          if (data === '[DONE]') continue;

          try {
            const parsed = JSON.parse(data);
            // Error devuelto por el proxy PHP
            if (parsed?.error) {
              bubble.textContent = 'Error: ' + parsed.error;
              console.error('[AssetTrack Chat] API error:', parsed.error);
              fullText = parsed.error; // evitar mensaje genérico
              break;
            }
            // Error de la API de Anthropic
            if (parsed?.type === 'error') {
              const msg = parsed?.error?.message || JSON.stringify(parsed.error);
              bubble.textContent = 'Error del servicio: ' + msg;
              console.error('[AssetTrack Chat] Anthropic error:', parsed.error);
              fullText = msg;
              break;
            }
            const delta = parsed?.delta?.text || parsed?.choices?.[0]?.delta?.content || '';
            if (delta) {
              fullText += delta;
              bubble.textContent = fullText;
              scrollToBottom();
            }
          } catch (_) {
            // línea SSE no parseable, ignorar
          }
        }
      }

      // Guardar respuesta completa en el historial
      if (fullText) {
        conversationHistory.push({ role: 'assistant', content: fullText });
      } else {
        bubble.textContent = 'Lo siento, no he podido generar una respuesta. Inténtalo de nuevo.';
      }

    } catch (err) {
      removeTypingIndicator();
      appendMessage('assistant', 'Ha ocurrido un error de conexión. Por favor, inténtalo de nuevo.');
      console.error('[AssetTrack Chat]', err);
    } finally {
      isStreaming = false;
      setInputEnabled(true);
      document.getElementById('at-chat-input').focus();
    }
  }

  // ─── Toggle ventana ───────────────────────────────────────────────────────
  function toggleChat(open) {
    const win = document.getElementById('at-chat-window');
    const toggle = document.getElementById('at-chat-toggle');
    const isOpen = win.classList.contains('at-open');
    const shouldOpen = open !== undefined ? open : !isOpen;

    if (shouldOpen) {
      win.classList.add('at-open');
      win.setAttribute('aria-hidden', 'false');
      toggle.classList.add('at-active');
      document.getElementById('at-chat-input').focus();
    } else {
      win.classList.remove('at-open');
      win.setAttribute('aria-hidden', 'true');
      toggle.classList.remove('at-active');
    }
  }

  // ─── Auto-resize textarea ─────────────────────────────────────────────────
  function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
  }

  // ─── Event listeners ──────────────────────────────────────────────────────
  function bindEvents() {
    const toggle = document.getElementById('at-chat-toggle');
    const minimize = document.getElementById('at-chat-minimize');
    const input = document.getElementById('at-chat-input');
    const sendBtn = document.getElementById('at-chat-send');

    toggle.addEventListener('click', () => toggleChat());
    minimize.addEventListener('click', () => toggleChat(false));

    input.addEventListener('input', () => {
      autoResize(input);
      sendBtn.disabled = input.value.trim() === '' || isStreaming;
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        const text = input.value.trim();
        if (text && !isStreaming) {
          input.value = '';
          input.style.height = 'auto';
          sendBtn.disabled = true;
          sendMessage(text);
        }
      }
    });

    sendBtn.addEventListener('click', () => {
      const text = input.value.trim();
      if (text && !isStreaming) {
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        sendMessage(text);
      }
    });
  }

  // ─── Mensaje de bienvenida ────────────────────────────────────────────────
  function showWelcome() {
    setTimeout(() => {
      appendMessage('assistant', CONFIG.welcomeMessage);
    }, 300);
  }

  // ─── Init ─────────────────────────────────────────────────────────────────
  function init() {
    injectStyles();
    buildWidget();
    bindEvents();
    showWelcome();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
