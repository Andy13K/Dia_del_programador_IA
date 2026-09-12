# Checklist de la Rúbrica — Auditoría antes de presentar

> Lo recorre el **Agente D** el sábado a las 15:00 y lo reporta en el chat del equipo.
> Cada punto no marcado es puntaje que se está regalando.

**Escala de la rúbrica:** 5 · 4 · 3 · 2 · 1 puntos por criterio, ponderado.

---

## 1. Originalidad, profesionalismo e innovación — 20 %

*Para 5/5: "La idea es completamente innovadora y presenta un enfoque único.
Uso adecuado de herramientas de desarrollo y control de versiones."*

- [ ] La solución tiene **al menos un elemento diferenciador** que no es un CRUD estándar
      (anotarlo aquí: `<________>`)
- [ ] Repositorio en GitHub, público o accesible al jurado
- [ ] Historial con commits frecuentes y descriptivos (Conventional Commits)
- [ ] Flujo de ramas + Pull Requests con revisión cruzada, visible en la pestaña de PRs
- [ ] Rama `main` protegida (se puede mostrar la configuración)
- [ ] Dependabot / auditoría de dependencias activada
- [ ] Código con estilo consistente (PSR-12), nomenclatura uniforme en español o inglés, no mezclada
- [ ] `README.md` profesional: qué es, cómo instalarlo, cómo entrar, credenciales de demo

## 2. Uso de Inteligencia Artificial — 20 %

*Para 5/5 hacen falta TODOS estos elementos. Faltando MCP o prompts documentados, el techo es 4.*

- [ ] IDE con agente integrado — usado y con captura
- [ ] Desarrollo incremental evidenciado en el historial de commits
- [x] **MCP Server en uso** — configurado, usado y con captura de pantalla ([`.mcp.json`](../.mcp.json) y [`docs/evidencias/mcp-*.png`](evidencias/mcp-01.png))
- [ ] Vibecoding / generación por descripción en lenguaje natural — con ejemplo documentado
- [ ] LLMs como asistentes de código — listados en `04-BITACORA-PROMPTS.md`
- [ ] **Prompts documentados** — bitácora completa y cronológica
- [ ] Evidencia de uso de IA: capturas, trailers `Co-Authored-By` en los commits, sección de IA en cada PR
- [ ] La bitácora registra **qué corrigió el humano** sobre la salida de la IA

## 3. Diseño de UI/UX — 15 %

*Para 5/5: "intuitiva, familiaridad entre pantallas, alto nivel de usabilidad, minimalista y/o
innovadora, uso adecuado de colores, tipografía consistente, diseño responsivo".*

- [ ] Una sola paleta de colores definida en `tailwind.config` y usada en todas las pantallas
- [ ] Una sola familia tipográfica (máximo dos) con escala consistente
- [ ] **Familiaridad entre pantallas:** mismo layout, misma barra lateral, mismos botones,
      misma posición de títulos y acciones en TODAS las vistas
- [ ] Estados vacíos diseñados (no una tabla en blanco)
- [ ] Mensajes de éxito y de error visibles y consistentes
- [ ] Estados de carga en las acciones que tardan
- [ ] **Responsivo verificado a 360 px, 768 px y 1440 px** — abrir el inspector y comprobarlo
- [ ] Contraste suficiente para leer el texto (no gris claro sobre blanco)
- [ ] Máximo 3 clics desde el dashboard hasta cualquier tarea principal
- [ ] Las 4 páginas de error tienen el mismo diseño que el resto del sistema

## 4. Funcionalidad y cumplimiento de requerimientos — 25 %

*Para 5/5: "Todas las funciones principales **y secundarias** funcionan sin errores".*

- [ ] Cada `RF-XX` del ERS está implementado **o** explícitamente marcado como fuera de alcance
- [ ] **Cada criterio de aceptación verificado en la URL pública**, uno por uno, con el ERS al lado
- [ ] Ninguna función a medias visible en el menú (si no está lista, se quita del menú)
- [ ] Ningún enlace roto, ningún botón que no haga nada
- [ ] Los formularios validan y muestran errores legibles
- [ ] Datos de demostración realistas cargados por seeder (nombres reales, fechas coherentes,
      no "test test test")
- [ ] Probado con los navegadores del jurado, no solo con el propio
- [ ] **Probado desde un celular real**

## 5. Documentación — 10 %

*Para 5/5: "Documentación técnica clara: objetivos del proyecto, manual de usuario resumido,
tareas realizadas por cada miembro del equipo y rol. Commits claros."*

- [ ] **Objetivos del proyecto** escritos (ERS §1.2)
- [ ] **Manual de usuario resumido** con credenciales de acceso por rol
- [ ] **Tareas realizadas por cada integrante y su rol** (ERS §5.5) ← la rúbrica lo pide literal
- [ ] **Commits claros** — revisar el historial completo antes de presentar
- [ ] **Diagrama de base de datos** ← exigido por las bases como criterio de evaluación
- [ ] ERS conforme a IEEE 830 / ISO/IEC/IEEE 29148
- [ ] Documento de seguridad OWASP Top 10:2025
- [ ] Bitácora de prompts
- [ ] Toda la documentación dentro del repositorio, enlazada desde el `README.md`

## 6. Presentación del proyecto — 10 %

*Para 5/5: "clara, bien estructurada, con excelente comunicación y demostración fluida,
uso de recursos visuales y demostración en vivo".*

- [ ] Guion escrito y repartido entre los dos (ver `08-GUION-PRESENTACION.md`)
- [ ] **Ensayada dos veces, cronometrada a 10 minutos**
- [ ] Diapositivas con recursos visuales: diagrama ER, arquitectura, capturas
- [ ] **Demostración en vivo** sobre la URL pública, no un video
- [ ] Sesión iniciada y pestañas preparadas **antes** de subir a presentar
- [ ] Plan B: video de respaldo de la demo, por si falla el internet del salón
- [ ] Ambos integrantes hablan
- [ ] Respuestas preparadas para: *¿qué hizo cada quién?*, *¿cómo usaron la IA?*,
      *¿cómo aseguraron el sistema?*, *¿por qué ese stack?*

---

## Requisitos de las bases (no de la rúbrica, pero descalifican)

- [ ] Check-in inicial en Google Meet realizado
- [ ] Evidencia de avance durante la etapa remota: **commits y fotos**
- [ ] La aplicación **está online** al momento de presentar
- [ ] Trabajo realizado únicamente durante el tiempo de la competencia
- [ ] Repositorio colaborativo con ambos integrantes contribuyendo

---

## Semáforo final — 15:30 del sábado

| Criterio | Peso | Autoevaluación (1–5) | Qué falta |
|---|---|---|---|
| Funcionalidad | 25 % | | |
| Originalidad | 20 % | | |
| Uso de IA | 20 % | | |
| UI/UX | 15 % | | |
| Documentación | 10 % | | |
| Presentación | 10 % | | |
| **Proyección de nota** | | | |

Si algún criterio está en 3 o menos y queda tiempo, **atacar primero el de mayor peso**.
Media hora invertida en la bitácora de prompts rinde más que media hora en otra función secundaria.
