# Guion de Presentación — 10 minutos

> Las bases dan **10 minutos finales** para presentación y evaluación.
> La rúbrica pide: *"presentación clara, bien estructurada, con excelente comunicación y
> demostración fluida, uso de recursos visuales y demostración en vivo"*.
>
> **Se ensaya dos veces, cronometrado.** Una presentación improvisada sobre un sistema
> excelente puntúa peor que una ensayada sobre un sistema bueno.

---

## Estructura (minutos exactos)

| Min | Bloque | Quién | Contenido |
|---|---|---|---|
| 0:00–0:45 | **El problema** | Carlos | Qué problema resuelve, en lenguaje humano. Sin jerga técnica. |
| 0:45–1:30 | **La solución y el diferenciador** | Carlos | Qué construimos y qué lo hace distinto |
| 1:30–2:30 | **Arquitectura y modelo de datos** | Andy | Diagrama ER + stack + por qué ese stack |
| 2:30–6:30 | **Demostración en vivo** | Andy | El recorrido de abajo. **Es el corazón de la presentación** |
| 6:30–7:30 | **Seguridad — OWASP Top 10:2025** | Carlos | Demo del 403 + bitácora de auditoría |
| 7:30–8:45 | **Proceso: 4 agentes de IA + flujo de PRs** | Andy | Repositorio, PRs, bitácora de prompts, MCP |
| 8:45–9:15 | **Roles del equipo y cierre** | Ambos | Quién hizo qué + una frase de cierre |
| 9:15–10:00 | **Preguntas** | Ambos | |

---

## El recorrido de la demo (4 minutos, ensayado paso a paso)

> Escribir aquí los clics **exactos**. Nada de improvisar frente al jurado.

1. **Login** como administrador → mostrar que aterriza en su dashboard
2. `<Función principal 1>` — crear un registro de principio a fin
3. `<Función principal 2>` —
4. `<Función principal 3>` —
5. **Cerrar sesión y entrar como `<rol secundario>`** → mostrar que la interfaz cambia según el rol
6. **Responsividad:** abrir la misma pantalla en el celular, en vivo
7. `<El diferenciador>` — dejarlo de último, es lo que se queda en la memoria del jurado

**Reglas de la demo:**
- Los datos ya están cargados. No se crea nada desde cero salvo el paso 2.
- Si algo falla, **no se depura en vivo**: se sigue adelante y se menciona al final.
- Se habla mientras se hace clic. El silencio de 5 segundos buscando un botón se nota muchísimo.

---

## Bloque de seguridad (1 minuto — el diferenciador más barato)

> Casi ningún equipo va a hacer esto. Toca tres criterios a la vez.

**Qué se dice:**
> "El sistema se diseñó contra el OWASP Top 10 en su edición **2025**, no la de 2021.
> Documentamos los diez controles y cada Pull Request lleva su checklist de seguridad firmado."

**Qué se muestra, en vivo:**

1. Con el usuario de rol bajo ya logueado, pegar en la barra de direcciones la URL de administrador
   → **403 Prohibido**
2. Cambiar de pestaña a la sesión de administrador, abrir la **bitácora de auditoría**
   → ahí está el intento que acaba de ocurrir, con usuario, IP y hora
3. Mostrar en pantalla `docs/02-SEGURIDAD-OWASP-2025.md` por dos segundos

---

## Bloque de proceso con IA (1:15 — vale el 20 %)

**Qué se muestra:**

1. La pestaña de **Pull Requests** del repositorio: N PRs, cada uno con descripción larga,
   revisión cruzada y checklist OWASP
2. Un PR abierto, específicamente su sección **"Evidencia de uso de IA"** con el prompt
   y el "qué corrigió el humano"
3. El **historial de commits**: frecuentes, descriptivos, con autoría de ambos
4. La captura del **MCP Server** conectado
5. La **bitácora de prompts**

**Qué se dice:**
> "Trabajamos con cuatro agentes de IA en paralelo — Claude Code, Codex y dos instancias de
> Antigravity — sobre un flujo de ramas con Pull Request y revisión cruzada obligatoria.
> Nadie empujó nunca directo a `main`. Cada PR documenta el prompt que lo originó, las
> correcciones que hicimos sobre la salida del agente, y su checklist de seguridad.
> La IA escribió buena parte del código; el modelo de datos, la arquitectura y los controles
> de seguridad los decidimos y verificamos nosotros."

---

## Roles del equipo (30 segundos — lo pide la rúbrica literalmente)

| Integrante | Rol | Agentes |
|---|---|---|
| **Andy Aquino** | Arquitecto e integrador — modelo de datos, autorización, integración de PRs, frontend | Claude Code · Antigravity |
| **Carlos** | Backend y lógica de negocio — controladores, validación, documentación y despliegue | Codex · Antigravity |

---

## Preguntas probables y respuestas preparadas

| Pregunta | Respuesta |
|---|---|
| *¿Qué hizo cada uno?* | Ver tabla de roles + "está documentado por PR en el repositorio" |
| *¿Cuánto lo hizo la IA?* | "La mayor parte del código. Las decisiones de arquitectura, el modelo de datos y los controles de seguridad son nuestros, y cada corrección que le hicimos al agente está documentada." |
| *¿Por qué Laravel?* | Ecosistema maduro, Eloquent, Policies y protección CSRF/XSS de fábrica — lo que permite cumplir OWASP sin implementar seguridad a mano en 12 horas |
| *¿Cómo evitaron conflictos entre 4 agentes?* | Propiedad exclusiva de carpetas por agente y contratos de interfaz congelados en la primera hora |
| *¿Está seguro el sistema?* | Mostrar el documento OWASP 2025 y repetir la demo del 403 |
| *¿Qué le falta?* | Responder con honestidad y con una lista concreta. Decir "nada" resta credibilidad. |

---

## Checklist previo a subir al frente

- [ ] Sesiones ya iniciadas en dos pestañas (admin y rol secundario)
- [ ] Pestaña del repositorio abierta en Pull Requests
- [ ] Pestaña con el diagrama ER abierta
- [ ] Celular listo y desbloqueado para la demo de responsividad
- [ ] Video de respaldo accesible en 10 segundos
- [ ] Diapositivas abiertas en modo presentación
- [ ] Notificaciones del sistema operativo silenciadas
- [ ] Modo oscuro del navegador coherente con el diseño de la app
- [ ] Zoom del navegador al 100 % (o 110 % si la pantalla del salón es pequeña)
- [ ] Ensayo cronometrado hecho **dos veces**
