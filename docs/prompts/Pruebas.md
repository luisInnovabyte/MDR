# FASE 1
FASE 1 — Testing técnico (Claude Code)
  → Limpia cualquier dato TEST_ residual
  → Crea sus propios datos de prueba
  → Ejecuta los 10 TCs
  → Reporta PASS/FAIL
  → Limpia sus datos
  → Tú corriges los bugs encontrados
PROMPT: 
## Limpia los datos previos y ejecuta el plan de testing completo
Limpia datos TEST_ residuales, ejecuta el plan completo 
de testing de .claude/specs/factura_agrupada.md 
y limpia al finalizar. Reporta PASS/FAIL por cada TC.

FASE 2 — Testing de negocio (tú manualmente)
  → Claude Code crea datos de prueba y te da el inventario
  → Tú pruebas desde la interfaz
  → Tú corriges o documentas lo encontrado

PROMPT:
## Para que Claude genere los datos de prueba y yo pueda hacer las pruebas manualmente
Lee .claude/specs/factura_agrupada.md y genera únicamente 
los datos de prueba en BD necesarios para los 10 casos de test. 
No ejecutes ningún test todavía. Usa el prefijo TEST_ en 
observaciones para poder limpiarlos después. 
Al finalizar muéstrame un resumen de qué datos ha creado y 
con qué IDs, para que pueda hacer las pruebas manualmente.

FASE 3 — Testing de negocio (tú manualmente)
  → Claude Code crea datos de prueba y te da el inventario
  → Tú pruebas desde la interfaz
  → Tú corriges o documentas lo encontrado

PROMPT:
## Cuando los datos ya están creados
Limpia datos TEST_ residuales y ejecuta la validación 
final completa de .claude/specs/factura_agrupada.md. 
Todos los TCs deben pasar. Reporta resultado final.