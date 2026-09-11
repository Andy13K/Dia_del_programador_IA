# Bitácora de Uso de Inteligencia Artificial

> **Este documento vale el 20 % de la nota.** Es el criterio más barato de toda la rúbrica:
> no requiere programar, requiere disciplina.
>
> El descriptor de 5 puntos exige: *IDE con agente integrado · desarrollo incremental con IDE ·
> **uso de MCP Server** · vibecoding/generación por descripción en lenguaje natural ·
> uso de LLMs como asistente · **prompts documentados** · evidencia de uso de IA*.
>
> Sin MCP Server y sin prompts documentados, el máximo alcanzable baja a 4 puntos.

---

## 1. Herramientas de IA utilizadas

| # | Operador | Agente / IDE | Modelo | Rol en el proyecto |
|---|---|---|---|---|
| A | Andy Aquino | Claude Code (desktop) | Claude Opus 5 | Arquitectura, modelo de datos, autorización, integración de PRs |
| B | Carlos | Codex | `<modelo>` | Controladores, servicios, validación, pruebas |
| C | Andy Aquino | Antigravity | Gemini | Interfaz, Blade, Tailwind, responsividad |
| D | Carlos | Antigravity | Gemini | Documentación, seguridad, despliegue, presentación |

## 2. MCP Servers activos

> **Obligatorio para el puntaje máximo.** Configurar ANTES de las 17:00 y dejar captura de pantalla.

| MCP Server | Agente que lo usa | Para qué se usó en este proyecto | Captura |
|---|---|---|---|
| `<ej. filesystem>` | | | `evidencias/mcp-01.png` |
| `<ej. github>` | | | `evidencias/mcp-02.png` |
| `<ej. mysql / postgres>` | | | |
| `<ej. playwright / browser>` | | | |

**Evidencia mínima a capturar:**
- Captura de la lista de MCP servers conectados en cada IDE
- Al menos una captura de una llamada real a una herramienta MCP durante el desarrollo
- La configuración (`.mcp.json` o equivalente) versionada en el repositorio

---

## 3. Registro de prompts

> Regla: **cada PR agrega su bloque aquí antes de mergearse.**
> No hace falta pegar todo; los 2 o 3 prompts que realmente movieron la aguja.
> Mantener el orden cronológico — eso es lo que demuestra *desarrollo incremental*.

### Formato

```
### [HH:MM] <Agente> — <Tarea> — PR #<NN>

**Objetivo:** qué se quería lograr.

**Prompt:**
> (texto del prompt tal cual se escribió)

**Resultado:** qué generó la IA.

**Intervención humana:** qué se corrigió, rechazó o ajustó a mano. ← *lo más valioso de registrar*

**Iteraciones:** N
```

---

### [17:00] Todos — Análisis del reto y elaboración del ERS — PR #1

**Objetivo:** convertir el planteamiento del problema en requerimientos verificables antes de programar.

**Prompt:**
> `<pegar aquí>`

**Resultado:**

**Intervención humana:**

**Iteraciones:**

---

### [HH:MM] `<Agente>` — `<Tarea>` — PR #

**Objetivo:**

**Prompt:**
>

**Resultado:**

**Intervención humana:**

**Iteraciones:**

---

## 4. Evidencia visual

Guardar en `docs/evidencias/` con nombres descriptivos. Mínimo a recolectar:

| Evidencia | Momento | Responsable |
|---|---|---|
| Captura de MCP servers conectados (cada IDE) | Antes de las 17:00 | Cada operador |
| Foto del equipo trabajando (exige las bases) | 18:00 y 22:00 | Cualquiera |
| Captura del agente generando código en el IDE | Durante el bloque 1 | Cada operador |
| Captura del historial de commits mostrando incrementalidad | 23:30 | Agente D |
| Captura de la lista de PRs con sus descripciones | Sábado 13:00 | Agente D |
| Captura de la app funcionando en la URL pública | Sábado 15:00 | Agente D |

## 5. Resumen para la presentación

> Llenar el sábado a las 15:00. Son las cifras que se dicen en voz alta ante el jurado.

| Métrica | Valor |
|---|---|
| Agentes de IA utilizados en paralelo | 4 |
| MCP Servers integrados | |
| Total de commits | |
| Total de Pull Requests | |
| Prompts documentados | |
| Requerimientos funcionales implementados | de |
| Controles OWASP Top 10:2025 aplicados | de 10 |

**Frase para la exposición:**
> "Trabajamos con cuatro agentes de IA en paralelo sobre un flujo de ramas y pull requests
> con revisión cruzada. Cada PR documenta el prompt que lo originó, qué corrigió el humano
> sobre la salida del agente, y su checklist de seguridad OWASP 2025. La IA escribió gran
> parte del código; las decisiones de arquitectura, el modelo de datos y los controles de
> seguridad los tomamos y verificamos nosotros."
