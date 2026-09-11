# Kit de Competencia — Día del Programador con IA

Equipo: **Andy Aquino · Carlos** · Universidad Mariano Gálvez, sede Puerto Barrios
Reto publicado: **viernes 11/09, 17:00** · Presentación: **sábado 12/09, ~16:00**

> Este directorio contiene **únicamente plantillas, reglas de trabajo y estándares**.
> No contiene código del proyecto. El repositorio de la competencia se crea limpio
> a las 17:00 del viernes; entonces se copian aquí dentro `CLAUDE.md`, `AGENTS.md`,
> `GEMINI.md`, `.github/` y `docs/`, y se empieza a llenar.

---

## Orden de lectura

| # | Documento | Cuándo se usa |
|---|---|---|
| 1 | [Plan maestro](docs/00-PLAN-MAESTRO.md) | Léelo primero. Cronograma, roles, propiedad de carpetas |
| 2 | [Reglas de trabajo](docs/01-REGLAS-DE-TRABAJO.md) | Git, ramas, commits, Pull Requests |
| 3 | [Seguridad OWASP Top 10:2025](docs/02-SEGURIDAD-OWASP-2025.md) | Durante todo el desarrollo |
| 4 | [Plantilla de ERS](docs/03-PLANTILLA-ERS.md) | Hora 1 (17:00–18:00) |
| 5 | [Contratos de interfaz](docs/06-CONTRATOS-HORA-1.md) | Hora 1 — congelar esquema, rutas y vistas |
| 6 | [Bitácora de prompts](docs/04-BITACORA-PROMPTS.md) | Continuamente. Vale el 20 % |
| 7 | [Plan de despliegue](docs/07-PLAN-DESPLIEGUE.md) | **Antes de las 17:00 del viernes** |
| 8 | [Checklist de la rúbrica](docs/05-CHECKLIST-RUBRICA.md) | Sábado 15:00 |
| 9 | [Guion de presentación](docs/08-GUION-PRESENTACION.md) | Sábado 15:30 |

Reglas para los agentes de IA: [`CLAUDE.md`](CLAUDE.md) (= `AGENTS.md` = `GEMINI.md`).
Plantilla de Pull Request: [`.github/pull_request_template.md`](.github/pull_request_template.md).

---

## Dónde está el puntaje

| Criterio | Peso | Dueño |
|---|---|---|
| Funcionalidad y cumplimiento | 25 % | Agente B |
| Originalidad y profesionalismo | 20 % | Todos |
| **Uso de IA** (requiere **MCP Server** + prompts documentados) | 20 % | Agentes D / E |
| UI/UX | 15 % | Agente C |
| Documentación (incluye **diagrama de base de datos**) | 10 % | Agentes D / E |
| Presentación | 10 % | Ambos (Andy y Carlos) |

---

## Los tres riesgos que deciden la competencia

1. **Despliegue.** La app debe estar **online** al presentar. Resolver con un hola-mundo
   desplegado **antes** de las 17:00 del viernes.
2. **MCP Server.** Sin él, el criterio de Uso de IA (20 %) tiene techo de 4/5. Configurarlo antes y capturarlo.
3. **Alcance.** Funcionalidad vale 25 % y el 5/5 exige que funcionen también las funciones
   secundarias. Pocas cosas, perfectas.

---

## Lista de preparación previa (viernes, antes de las 17:00)

- [ ] Laragon, PHP 8.3, Composer, Node y MySQL funcionando en ambas máquinas
- [ ] **Hola-mundo de Laravel desplegado en la URL pública, con HTTPS y base de datos conectada**
- [ ] Cuentas de GitHub de ambos con `git config user.name` y `user.email` correctos
- [ ] **MCP Servers configurados y capturados** en los cuatro agentes
- [ ] `cloudflared` descargado y probado como plan de respaldo
- [ ] Carpeta `docs/evidencias/` creada para capturas y fotos
- [ ] Ambos agregados al Google Meet del check-in inicial
- [ ] Canal de comunicación del equipo acordado
- [ ] Este kit leído por los dos, no solo por uno
