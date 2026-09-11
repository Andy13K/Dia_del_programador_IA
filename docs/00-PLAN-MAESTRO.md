# Plan Maestro — Competencia Día del Programador con IA

**Equipo:** Andy Aquino · Carlos
**Reto publicado:** viernes 11/09, 17:00
**Presentación:** sábado 12/09, ~16:00
**Agentes disponibles:** 4 (Claude Code, Codex, Antigravity×2)

---

## 1. Matriz de la rúbrica (a dónde va cada hora)

| Criterio | Peso | Dueño principal | Evidencia que lo prueba |
|---|---|---|---|
| Funcionalidad y cumplimiento | 25% | Agente B (backend) | Demo en vivo + criterios de aceptación del ERS |
| Originalidad y profesionalismo | 20% | Todos | Repo con PRs, ramas, ERS, diagramas |
| **Uso de IA** | **20%** | Agente D | Bitácora de prompts + captura de MCP activo + commits incrementales |
| UI/UX | 15% | Agente C | Sistema de diseño consistente, responsivo |
| Documentación | 10% | Agente D | ERS, diagrama ER, manual, roles por integrante |
| Presentación | 10% | Andy + Carlos | Guion ensayado + demo en vivo |

> **Regla de oro:** el 20% de "Uso de IA" se gana con disciplina, no con código.
> Requiere MCP Server activo + prompts documentados. Es el punto más barato de toda la rúbrica.

---

## 2. Roles y propiedad de carpetas

Cada agente es **dueño exclusivo** de sus carpetas. Nadie edita fuera de su zona.
Si necesitás un cambio en zona ajena, se pide por comentario en el PR o por chat — no se edita.

| Agente | Operador | Rol | Carpetas de su propiedad |
|---|---|---|---|
| **A — Claude Code** | Andy | Arquitecto e integrador. Dueño del repo. | `database/migrations/`, `database/seeders/`, `app/Models/`, `app/Policies/`, `routes/`, `config/`, revisión de todos los PR |
| **B — Codex** | Carlos | Backend / lógica de negocio | `app/Http/Controllers/`, `app/Http/Requests/`, `app/Services/`, `tests/` |
| **C — Antigravity** | Andy | Frontend / UI-UX | `resources/views/`, `resources/css/`, `resources/js/`, `tailwind.config.js` |
| **D — Antigravity** | Carlos | Documentación, seguridad, DevOps, presentación | `docs/`, `README.md`, despliegue, bitácora de prompts, diapositivas |

**Regla anti-conflicto:** `routes/web.php`, migraciones y `app/Models/` los toca **solo el Agente A**.
Si B necesita una ruta nueva, la pide; A la agrega en un commit de 30 segundos.

---

## 3. Cronograma

### Viernes 11/09

| Hora | Actividad | Quién | Entregable |
|---|---|---|---|
| 16:45 | Check-in en Google Meet (obligatorio por bases) | Ambos | — |
| **17:00–18:00** | **Hora 1 — Cero código.** Leer el reto, ERS express, modelo de datos, contratos de interfaz | Ambos + Agente A | `docs/ERS.md`, `docs/06-CONTRATOS.md` lleno, diagrama ER |
| 18:00–18:20 | Scaffolding: `laravel new`, migraciones, seeders, primer deploy vacío a producción | Agente A + D | Repo vivo + URL pública funcionando |
| 18:20–21:30 | **Bloque paralelo 1** — 4 agentes trabajando a la vez | Todos | Módulo core funcional |
| 21:30–22:00 | **Integración 1**: merge de todos los PR, deploy, smoke test | A + D | Rama `main` verde y desplegada |
| 22:00–23:30 | **Bloque paralelo 2** — funciones secundarias + pulido UI | Todos | Funciones secundarias |
| 23:30–00:30 | **Integración 2 + hardening OWASP** (ver `02-SEGURIDAD-OWASP-2025.md`) | A + D | Checklist de seguridad firmado, deploy estable |
| 00:30 | **Dormir.** No negociable. | Ambos | — |

### Sábado 12/09

| Hora | Actividad | Quién |
|---|---|---|
| 06:30–07:00 | Revisión rápida: ¿sigue arriba la URL? | Andy |
| 07:00–12:00 | Clases. Trabajo de bajo riesgo únicamente: manual de usuario, diapositivas, bitácora de prompts. **Cero refactors.** | Agente D en ratos |
| 12:00–13:00 | Almuerzo + revisión del estado real | Ambos |
| 13:00–14:00 | Seeders con datos realistas para la demo + arreglo de bugs conocidos | A + B |
| **14:00–16:00** | **Bloque presencial (2h).** Congelamiento de funciones. Solo bugs. | Todos |
| 15:30–16:00 | **Ensayo de la demo, dos veces, cronometrado a 10 min** | Ambos |
| 16:00 | Presentación | Ambos |

---

## 4. Reglas de alcance (para no perder el 25%)

1. El reto se divide en **funciones principales** y **secundarias**. Se listan en la Hora 1 y **no se agregan más**.
2. Prohibido empezar una función nueva después de las **22:00 del viernes**.
3. Prohibido tocar código que no sea corrección de bug después de las **15:00 del sábado**.
4. Si una función no está lista a la hora del congelamiento, **se quita del menú de navegación**. Una app con 6 cosas que funcionan gana a una con 10 donde 3 truenan.
5. Toda función declarada "lista" debe tener su criterio de aceptación verificado en el navegador, no en la terminal.

---

## 5. Puntos de sincronización obligatorios

Cada 90 minutos, mensaje corto en el chat del equipo con este formato:

```
[HH:MM] <Agente> — Hecho: ... | En curso: ... | Bloqueado por: ... | PR: #NN
```

Esto además sirve como evidencia de avance para las bases (que exigen mostrar progreso durante la etapa remota).
