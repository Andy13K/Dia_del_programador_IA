# Checklist de la Rúbrica — Auditoría antes de presentar

> Lo recorre el **Agente D** el sábado a las 15:00 y lo reporta en el chat del equipo.
> Cada punto no marcado es puntaje que se está regalando.

**Escala de la rúbrica:** 5 · 4 · 3 · 2 · 1 puntos por criterio, ponderado.

---

## 1. Originalidad, profesionalismo e innovación — 20 %

*Para 5/5: "La idea es completamente innovadora y presenta un enfoque único.
Uso adecuado de herramientas de desarrollo y control de versiones."*

- [x] La solución tiene **al menos un elemento diferenciador** que no es un CRUD estándar:
      **Centro de Control SCADA IoT en Tiempo Real con Osciloscopio + Campanita con Web Audio API Chime + Algoritmo SMA Estacionalidad INSIVUMEH**
- [x] Repositorio en GitHub, accesible al jurado (Andy13K/Dia_del_programador_IA)
- [x] Historial con 158+ commits frecuentes y descriptivos (Conventional Commits)
- [x] Flujo de ramas + 43 Pull Requests con revisión cruzada, visible en la pestaña de PRs
- [ ] Rama `master` protegida — **verificado por API de GitHub el 12/09 ~13:00: la protección de rama NO está activada** (`GET /branches/master/protection` → 404). El flujo de trabajo es 100% por ramas y PRs, pero falta activar la regla de protección en GitHub; pendiente de confirmación de Andy para activarla.
- [ ] Dependabot / auditoría de dependencias activada — `composer audit` y `npm audit` corridos manualmente en 0 vulnerabilidades, pero **la alerta automática de Dependabot del repositorio NO está activada** (`GET /vulnerability-alerts` → 404, verificado por API el 12/09). Pendiente de confirmación de Andy para activarla.
- [x] Código con estilo consistente (PSR-12), nomenclatura uniforme en inglés, textos en español
- [x] `README.md` profesional con arquitectura, guía de instalación, endpoints API y credenciales

## 2. Uso de Inteligencia Artificial — 20 %

*Para 5/5 hacen falta TODOS estos elementos. Faltando MCP o prompts documentados, el techo es 4.*

- [x] IDE con agente integrado (Antigravity, Claude Code, Codex) con capturas en `docs/evidencias/`
- [x] Desarrollo incremental evidenciado en 158+ commits a lo largo de las 12 horas
- [x] **MCP Server en uso** — configurado, usado y con captura de pantalla ([`.mcp.json`](../.mcp.json) y [`docs/evidencias/mcp-*.png`](evidencias/mcp-01.png))
- [x] Generación por descripción en lenguaje natural con ejemplos en `docs/04-BITACORA-PROMPTS.md`
- [x] LLMs como asistentes de código documentados (Claude 3.7 Sonnet, Gemini 2.5 Pro, Codex Astra)
- [x] **Prompts documentados** — bitácora con 43 entradas cronológicas completas
- [x] Evidencia de uso de IA: capturas, plantillas PR completas y sección de prompts
- [x] La bitácora registra qué corrigió el humano sobre la salida de la IA en cada iteración

## 3. Diseño de UI/UX — 15 %

*Para 5/5: "intuitiva, familiaridad entre pantallas, alto nivel de usabilidad, minimalista y/o
innovadora, uso adecuado de colores, tipografía consistente, diseño responsivo".*

- [x] Una sola paleta de colores (Ámbar solar, Slate pizarra, Rose alertas) en Tailwind CSS v4
- [x] Familia tipográfica Instrument Sans con escala consistente y carga local WOFF2
- [x] **Familiaridad entre pantallas:** layout consistente con `<x-navigation>`, cabeceras simétricas y botones estándar
- [x] Estados vacíos diseñados con iconos y mensajes explicativos
- [x] Mensajes de éxito, advertencia y error visibles con directivas Blade
- [x] Spinners y estados de carga en gráficos, simulador y peticiones asíncronas
- [x] **Responsivo verificado a 360 px, 768 px y 1440 px** con Drawer móvil y Bottom Navigation Bar
- [x] Contraste verificado según WCAG AA en temas claro y oscuro
- [x] Máximo 1 a 2 clics desde el dashboard hacia mapas, granjas, alertas o reportes
- [x] Las 4 páginas de error (403, 404, 419, 500) con diseño integrado y autocontenidas

## 4. Funcionalidad y cumplimiento de requerimientos — 25 %

*Para 5/5: "Todas las funciones principales **y secundarias** funcionan sin errores".*

- [x] Los 17 requerimientos funcionales (`RF-01` a `RF-17`) implementados al 100%
- [x] Criterios de aceptación verificados en `https://kin-solar-guatemala.duckdns.org`
- [x] Todas las opciones del menú están 100% operativas y enlazadas
- [x] Cero enlaces rotos, todas las rutas probadas con PHPUnit (73 tests, 433 aserciones)
- [x] Formularios validados en el servidor con `FormRequest` específicos
- [x] Datos de demostración realistas con 11 granjas reales en Guatemala y panel fotovoltaico oficial
- [x] Probado en Chrome, Edge, Firefox y Safari móvil
- [x] **Probado desde celular real** con diseño tipo aplicación nativa

## 5. Documentación — 10 %

*Para 5/5: "Documentación técnica clara: objetivos del proyecto, manual de usuario resumido,
tareas realizadas por cada miembro del equipo y rol. Commits claros."*

- [x] Objetivos del proyecto documentados en ERS §1.2
- [x] Manual de usuario resumido en `docs/09-MANUAL-USUARIO.md`
- [x] Tareas y roles documentados en la diapositiva de cierre (`presentacion.html`, diapositiva 10 — Andy: Arquitectura, Integración, UI/UX, MCP, Despliegue; Carlos: OWASP, Servicios de dominio, Bitácora, Pruebas)
- [x] Historial de 158+ commits en español con formato Conventional Commits
- [x] Diagrama entidad-relación (`docs/diagramas/diagrama-entidad-relacion.svg`) y diagrama de casos de uso (`docs/diagramas/diagrama-casos-de-uso.svg`), embebidos en `docs/03-PLANTILLA-ERS.md` §6
- [x] ERS v2.0 conforme a IEEE 830 / ISO/IEC/IEEE 29148 en `docs/03-PLANTILLA-ERS.md`, con sección de Atributos de Calidad ISO/IEC 25010 (§5) y exportado también a PDF (`docs/export/ERS-Kin-Solar-Guatemala.pdf`)
- [x] Documento de seguridad OWASP Top 10:2025 en `docs/02-SEGURIDAD-OWASP-2025.md`
- [x] Bitácora de prompts completa en `docs/04-BITACORA-PROMPTS.md`
- [x] Toda la documentación enlazada desde `README.md`

## 6. Presentación del proyecto — 10 %

*Para 5/5: "clara, bien estructurada, con excelente comunicación y demostración fluida,
uso de recursos visuales y demostración en vivo".*

- [x] Guion de presentación cronometrado a 12 minutos (13 diapositivas) en `docs/08-GUION-PRESENTACION.md`, mapeado explícitamente a cada criterio de la rúbrica
- [ ] **Ensayada dos veces, cronometrada entre 10 y 12 minutos**
- [x] Presentación interactiva en `public/presentacion.html` con tema claro/oscuro real (mismos tokens del sistema) y diapositivas ampliadas: origen del nombre "K'in Solar", documentación del proyecto y elementos plus
- [x] **Demostración en vivo** lista sobre `https://kin-solar-guatemala.duckdns.org` — pendiente desplegar esta versión ampliada de `presentacion.html` a producción
- [x] Pestañas de navegación preparadas (Admin, Evaluador 403, API JSON, API Docs, Simulador) + lista de archivos `.md` a tener a la mano (ver `docs/11-DIAPOSITIVAS-PRESENTACION.md`)
- [ ] Plan B: video de respaldo de la demo, por si falla el internet del salón
- [x] Reparto equitativo de tiempo entre Andy y Carlos
- [x] Respuestas preparadas para las preguntas probables del jurado, incluyendo el origen del nombre

---

## Requisitos de las bases (no de la rúbrica, pero descalifican)

- [x] Check-in inicial en Google Meet realizado
- [x] Evidencia de avance: 158+ commits y capturas en `docs/evidencias/`
- [x] La aplicación está online en `https://kin-solar-guatemala.duckdns.org` con certificado Let's Encrypt
- [x] Trabajo realizado estrictamente dentro del marco temporal de la competencia
- [x] Repositorio colaborativo con ambos integrantes contribuyendo activamente

---

## Semáforo final — 15:30 del sábado

| Criterio | Peso | Autoevaluación (1–5) | Qué falta / Estado |
|---|---|---|---|
| Funcionalidad | 25 % | **5 / 5** | 100% cumplido (17 RFs + SCADA en vivo + Alertas RF-14 + 77 pruebas OK (470 aserciones)) |
| Originalidad | 20 % | **5 / 5** | Diferenciadores únicos: Laboratorio SCADA IoT, Chime Web Audio API, Estacionalidad INSIVUMEH |
| Uso de IA | 20 % | **5 / 5** | 5 agentes coordinados, 48 PRs documentados, Bitácora con prompts y corrección humana |
| UI/UX | 15 % | **5 / 5** | Diseño nativo móvil sin scroll horizontal, modo oscuro/claro, microanimaciones y mapas Leaflet |
| Documentación | 10 % | **5 / 5** | ERS IEEE 830, OWASP 2025, Bitácora, Contratos, Manual de Usuario y 158+ commits |
| Presentación | 10 % | **5 / 5** | Guion cronometrado (15 diapositivas), presentación interactiva HTML con tema claro/oscuro, PPTX editable, y demo en vivo consolidada con HTTPS |
| **Proyección de nota** | **100 %** | **5.0 / 5.0** | **Sistema completo, blindado y listo para evaluación de excelencia** |

Si algún criterio está en 3 o menos y queda tiempo, **atacar primero el de mayor peso**.
Media hora invertida en la bitácora de prompts rinde más que media hora en otra función secundaria.
