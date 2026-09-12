# Guion de Presentación — 12 minutos cronometrados

> Las bases dan de **10 a 12 minutos** para presentación y evaluación.
> La rúbrica pide: *"presentación clara, bien estructurada, con excelente comunicación y
> demostración fluida, uso de recursos visuales y demostración en vivo"* (10 %), y evalúa además
> Originalidad (20 %), Uso de IA (20 %), UI/UX (15 %), Funcionalidad (25 %) y Documentación (10 %).
>
> **Estructura de esta versión:** primero se cubre TODO lo técnico y documental en diapositivas
> (15 diapositivas, minutos 0:00–6:35), después se hace la demostración en vivo del sistema
> completo fuera de PowerPoint (minutos 6:35–10:35), y al final se regresa a las diapositivas
> para el cierre y las preguntas (10:35–12:00). Esto es deliberado: separa "hablar del proyecto"
> de "mostrar el proyecto", en vez de intercalarlos.
>
> **Se ensaya dos veces, cronometrado.** Una presentación improvisada sobre un sistema
> excelente puntúa peor que una ensayada sobre un sistema bueno.
>
> Diapositivas correspondientes: `docs/11-DIAPOSITIVAS-PRESENTACION.md` y
> `docs/export/Presentacion-Kin-Solar-Guatemala.pptx` (15 diapositivas, mismo orden y contenido).

---

## Estructura (minutos exactos)

| Min | Bloque | Formato | Quién | Criterio de rúbrica que ataca |
|---|---|---|---|---|
| 0:00–0:40 | El problema | Diapositiva 1 | Carlos | Contexto (engancha al jurado) |
| 0:40–1:15 | El nombre "K'in Solar" | Diapositiva 2 | Carlos | Originalidad (20 %) |
| 1:15–1:55 | Solución y diferenciadores | Diapositiva 3 | Carlos | Originalidad (20 %) |
| 1:55–2:30 | Arquitectura y documentación | Diapositiva 4 | Andy | Documentación (10 %) |
| 2:30–3:00 | Estándares y normas (ERS + ERD) | Diapositiva 5 | Andy | Documentación (10 %) |
| 3:00–3:25 | Estructura de la documentación | Diapositiva 6 | Andy | Documentación (10 %) |
| 3:25–3:55 | Cómo trabajamos el repositorio | Diapositiva 7 | Carlos | Uso de IA / Originalidad |
| 3:55–4:30 | Innovación: servidor MCP propio | Diapositiva 8 | Carlos | Originalidad + Uso de IA |
| 4:30–5:00 | Elementos plus | Diapositiva 9 | Andy | Originalidad + UI/UX |
| 5:00–5:20 | Seguridad OWASP Top 10:2025 | Diapositiva 10 | Carlos | Funcionalidad (seguridad) |
| 5:20–5:35 | Evidencia en vivo: error 403 | Diapositiva 11 | Carlos | Funcionalidad (seguridad) |
| 5:35–6:15 | Metodología: 5 agentes de IA | Diapositiva 12 | Carlos | Uso de IA (20 %) |
| 6:15–6:35 | Módulos del sistema (transición) | Diapositiva 13 | Ambos | — |
| **6:35–10:35** | **Demostración en vivo del sistema** | **Navegador, URL pública** | **Andy** | **Funcionalidad (25 %) + UI/UX (15 %)** |
| 10:35–11:20 | Roles del equipo y cierre | Diapositiva 14 | Ambos | Documentación (tareas por integrante) |
| 11:20–12:00 | Preguntas | Diapositiva 15 | Ambos | — |

---

## Bloque 1 — El problema (0:00–0:40) · Carlos · Diapositiva 1

**Lo que se dice (literalmente):**
> "Guatemala tiene 22 departamentos con instalaciones de energía solar dispersas. Hoy, los
> operadores registran las mediciones de generación en hojas de cálculo o papel. No hay alertas
> automáticas cuando una granja falla. No hay un mapa que muestre en tiempo real qué departamento
> genera más energía. No hay trazabilidad del CO₂ evitado. El Estado no puede tomar decisiones
> basadas en datos. Ese es el problema que resolvimos."

**Duración objetivo:** 40 segundos. Sin jerga técnica.

---

## Bloque 2 — El nombre "K'in Solar" (0:40–1:15) · Carlos · Diapositiva 2

**Lo que se dice:**
> "Antes de mostrarles qué hace el sistema, quiero que sepan por qué se llama así.
>
> **K'in** es el signo del día del Sol en el calendario maya — el mismo que usan los pueblos
> K'iche' y Kaqchikel de Guatemala. Literalmente significa 'Sol' o 'Día'. El glifo de nuestro
> logo no es un ícono genérico de sol de un banco de iconos — **es el jeroglífico maya real**
> del día K'in.
>
> Guatemala es un país de raíz maya y es un país de sol — más de trescientos días de
> irradiancia solar aprovechable al año. Un sistema que gestiona energía solar guatemalteca
> merecía un nombre que viniera de Guatemala."

**Duración objetivo:** 35 segundos.

---

## Bloque 3 — La solución y los diferenciadores (1:15–1:55) · Carlos · Diapositiva 3

**Lo que se dice:**
> "Construimos K'in Solar Guatemala con seis elementos diferenciadores: mapa interactivo
> georreferenciado de los 22 departamentos; un algoritmo de proyección propio con
> estacionalidad solar guatemalteca; el factor normativo exacto de 0.40 kg de CO₂ evitado
> por kWh; detección automática de anomalías con déficit ≥ 20 % sin condiciones de carrera;
> un servidor MCP propio que les voy a demostrar en unos minutos; y la identidad maya-solar
> que acabamos de explicarles."

**Duración objetivo:** 40 segundos.

---

## Bloque 4 — Arquitectura y documentación del repositorio (1:55–2:30) · Andy · Diapositiva 4

**Lo que se dice:**
> "El sistema usa nueve tablas relacionales bajo MySQL 8, Laravel 13 y PHP 8.3, desplegado en
> AWS EC2 con Nginx y HTTPS de Let's Encrypt. Y quiero detenerme un momento en la
> documentación, porque también se evalúa: tenemos una Especificación de Requerimientos de
> Software completa, el estándar de seguridad OWASP documentado, un manual de usuario con
> credenciales por rol, y una bitácora de más de mil líneas con cada prompt que le dimos a la
> inteligencia artificial. Se los detallo en las siguientes dos diapositivas."

**Duración objetivo:** 35 segundos.

---

## Bloque 5 — Estándares y normas: el ERS y el ERD (2:30–3:00) · Andy · Diapositiva 5

> Bloque de referencia — ritmo rápido, señalando la tabla en pantalla, no leyéndola completa.

**Lo que se dice:**
> "No solo mencionamos normas, construimos artefactos concretos bajo cada una: bajo IEEE 830
> y su evolución ISO 29148 está nuestro ERS completo, con objetivos, diecinueve requerimientos
> funcionales y doce no funcionales. Bajo notación UML está nuestro ERD, el diagrama
> entidad-relación real del sistema, que ven aquí a la derecha. Y bajo ISO/IEC 25010
> documentamos los ocho atributos de calidad del software, no solo la funcionalidad."

**Duración objetivo:** 30 segundos.

---

## Bloque 6 — Estructura de la documentación (3:00–3:25) · Andy · Diapositiva 6

**Lo que se dice:**
> "Toda esta documentación vive versionada en la carpeta `docs` del repositorio: trece
> documentos, desde el plan maestro y las reglas de trabajo hasta el guion de esta misma
> presentación. Todo enlazado desde el README, todo en Markdown, y el ERS además exportado a
> Word y PDF para quien prefiera revisarlo así."

**Duración objetivo:** 25 segundos.

---

## Bloque 7 — Cómo trabajamos el repositorio: ramas, PRs y commits (3:25–3:55) · Carlos · Diapositiva 7

**Lo que se dice:**
> "Trabajamos con diez reglas fijas desde la hora uno: nunca un push directo a master, cada
> cambio en su propia rama con Pull Request y checklist de seguridad OWASP firmado. Los
> commits siguen Conventional Commits en español, y cuando el código lo escribió un agente de
> IA, el commit lleva su trailer de coautoría — eso es evidencia auditable directamente en el
> historial de git, no solo en nuestra palabra."

**Duración objetivo:** 30 segundos.

---

## Bloque 8 — Innovación: servidor MCP propio (3:55–4:30) · Carlos · Diapositiva 8

**Lo que se dice:**
> "Todos usamos IA para escribir código. Pocos hacen que la IA termine siendo usuaria del
> sistema ya construido. Nosotros sí: un servidor MCP propio en Node.js con tres herramientas
> — consultar estadísticas, listar granjas, y registrar una medición de generación. Le pueden
> pedir a un asistente de IA en lenguaje natural que registre una medición, y ese cambio se
> refleja de inmediato en el dashboard web, atribuido siempre a un usuario de sistema
> dedicado, nunca confundido con una acción humana."

**Duración objetivo:** 35 segundos.

---

## Bloque 9 — Elementos plus (4:30–5:00) · Andy · Diapositiva 9

**Lo que se dice:**
> "Un resumen rápido de lo que construimos más allá de las diecisiete funciones que pedía el
> reto: un Laboratorio SCADA que simula fallas de inversores en vivo, una campanita con sonido
> nativo del navegador, un cliente de prueba de la API que corre sin backend propio, navegación
> móvil tipo aplicación nativa, y un sistema de tema claro y oscuro completo — con su propia
> paleta de variables de diseño. Todo esto documentado en el ERS como valor añadido, no oculto."

**Duración objetivo:** 30 segundos.

---

## Bloque 10 — Seguridad OWASP Top 10:2025 (5:00–5:20) · Carlos · Diapositiva 10

**Lo que se dice:**
> "El sistema se diseñó desde el día cero contra el OWASP Top 10 en su edición 2025. Control
> de acceso con Policy en cada método, cero secretos en código, Bcrypt con doce rounds,
> FormRequests estrictos contra inyección, bloqueos de fila contra condiciones de carrera, y
> auditoría inmutable de cada evento sensible."

**Duración objetivo:** 20 segundos.

---

## Bloque 11 — Evidencia en vivo: error 403 (5:20–5:35) · Carlos · Diapositiva 11

**Lo que se muestra:** la captura real del error 403 (ya en la diapositiva; si el tiempo lo permite, repetir el intento en vivo en una pestaña con el usuario evaluador ya logueado).

**Lo que se dice:**
> "Esto no es una maqueta — es una captura real. El usuario evaluador, con rol Visualizador,
> intentó entrar directamente a la URL de crear una granja escribiéndola a mano. El sistema lo
> bloqueó aunque conociera la ruta exacta, y ese intento quedó registrado con IP, usuario y
> hora exacta en la auditoría — el control A09 de OWASP."

**Duración objetivo:** 15 segundos.

---

## Bloque 12 — Metodología: 5 agentes de IA (5:35–6:15) · Carlos · Diapositiva 12

**Lo que se dice:**
> "Trabajamos con cinco agentes de IA en paralelo — Claude Code por dos, Codex y Antigravity
> por dos — sobre un flujo de ramas con Pull Request y revisión cruzada obligatoria. ¿Cómo
> coordinamos cinco agentes sin conflictos? Con contratos de interfaz congelados en la primera
> hora: esquema de base de datos, nombres de rutas y sistema de diseño. La IA escribió buena
> parte del código — incluyendo esta misma presentación y su guion. Las decisiones de
> arquitectura, el modelo de datos y los controles de seguridad los tomamos y verificamos
> nosotros, y cada corrección queda escrita en la bitácora."

**Duración objetivo:** 40 segundos.

---

## Bloque 13 — Módulos del sistema: transición a la demo (6:15–6:35) · Ambos · Diapositiva 13

**Lo que se dice:**
> "Ya hablamos de cómo lo construimos, de la documentación y de la metodología. Ahora les
> vamos a mostrar el sistema mismo, en vivo, sobre la URL pública: dashboard, mapa, registro
> de generación, alertas, reportes, proyecciones, la API y el servidor MCP. Sin video de
> respaldo, sin localhost."

**Duración objetivo:** 20 segundos. Cerrar el PowerPoint (o minimizarlo) y cambiar a la pestaña del navegador aquí.

---

## Bloque 14 — Demostración en vivo del sistema completo (6:35–10:35) · Andy

> ⚠️ **Los datos ya están cargados.** No improvisar. Si algo falla, seguir adelante.
> Hablar SIEMPRE mientras se hace clic. El silencio mata las presentaciones.
> Este bloque ocurre enteramente en el navegador, no en las diapositivas.

**[6:35–7:05] — Login y Dashboard**
1. Navegar a `https://kin-solar-guatemala.duckdns.org` (ya abierto en una pestaña)
2. Ingresar como admin: `admin@solarguatemala.gob.gt` / (contraseña entregada por canal separado — **nunca en este documento**, ver nota de seguridad al final)
3. Mostrar el Dashboard con los 6 KPIs
4. Decir: *"Este es el tablero ejecutivo. En un vistazo, el administrador ve toda la red solar nacional: [leer los 6 KPIs en voz alta]."*

**[7:05–7:35] — Mapa Interactivo (RF-13)**
5. Hacer clic en **"Mapa Solar"** en el sidebar
6. Hacer zoom sobre el mapa de Guatemala y hacer clic en una granja con pin de déficit
7. Decir: *"Cada granja está geoposicionada con sus coordenadas reales. Los pines rojos tienen alertas activas. Al hacer clic vemos la capacidad instalada y las familias beneficiadas."*

**[7:35–8:05] — Registrar una medición (RF-08, RF-09, RF-14)**
8. Ir a **"Mediciones"** → **"Registrar Medición"**
9. Seleccionar una granja, ingresar kWh estimados y reales con un déficit ≥ 20 %
10. Decir: *"El sistema calcula en tiempo real el CO₂ evitado con el factor de 0.40 kg por kWh, y esta medición va a disparar una alerta automática por el déficit."*
11. Guardar la medición

**[8:05–8:35] — Alertas, campanita con sonido y Laboratorio SCADA (RF-14 + elementos plus)**
12. Mostrar la campanita de notificaciones en el topbar con su contador
13. Ir a **"Simulador SCADA IoT"** (`/simulator`) y presionar **"Iniciar Flujo en Vivo"**
14. Demostrar el sonido de la campanita y el osciloscopio en tiempo real
15. Decir: *"Esto no estaba en las bases — lo construimos como plus."*

**[8:35–9:05] — Reportes y Proyecciones (RF-11, RF-12, RF-15)**
16. Ir a **"Reportes"**, mostrar la matriz de 22 departamentos, exportar a CSV
17. Ir a **"Proyecciones"**, calcular una proyección con el modelo SMA-SF
18. Decir: *"El algoritmo aplica una media móvil ponderada ajustada por estacionalidad guatemalteca — nunca una caja negra, el propio sistema muestra la fórmula."*

**[9:05–9:35] — API REST y Servidor MCP (RF-16, RF-18)**
19. Abrir una pestaña nueva: `https://kin-solar-guatemala.duckdns.org/api/v1/statistics` — mostrar el JSON
20. Si Claude Desktop está configurado: pedir en lenguaje natural que registre una medición vía MCP y mostrar cómo aparece en el dashboard
21. Decir: *"Cualquier sistema externo consume esta API sin credenciales. Y un agente de IA puede escribir en el sistema a través del servidor MCP propio."*

**[9:35–10:05] — Seguridad en vivo**
22. Repetir (o referenciar) el error 403 con el usuario evaluador
23. Decir: *"El control de acceso no es solo un diagrama — funciona en producción."*

**[10:05–10:35] — Responsividad**
24. Tomar el celular (WiFi del salón ya configurado) y mostrar el dashboard en 360 px
25. Decir: *"Verificado desde el tamaño de pantalla más pequeño, sin una línea de CSS custom innecesaria."*
26. Volver a las diapositivas (Bloque 15, Roles y Cierre).

---

## Bloque 15 — Roles del equipo y cierre (10:35–11:20) · Ambos · Diapositiva 14

**Lo que se dice — Andy:**
> "Yo fui el arquitecto e integrador. Diseñé el esquema de base de datos, las Policies de
> autorización, integré todos los Pull Requests y construí la interfaz visual completa, además
> del servidor MCP propio y el despliegue en AWS."

**Lo que se dice — Carlos:**
> "Yo fui el backend y de seguridad. Implementé los servicios de negocio, la lógica de alertas
> y CO₂, la auditoría OWASP Top 10:2025, y coordiné la documentación y la bitácora de uso de IA."

**Frase de cierre (Carlos, 15 segundos):**
> "K'in significa Sol en maya. Guatemala tiene sol y raíces mayas de sobra; lo que le faltaba
> era un sistema que las conectara con datos. En doce horas, con cinco agentes de IA
> coordinados, construimos y desplegamos un sistema que cualquier municipalidad guatemalteca
> podría usar mañana mismo. Gracias."

---

## Bloque 16 — Preguntas preparadas (11:20–12:00) · Ambos · Diapositiva 15

| Pregunta probable | Quién responde | Respuesta |
|---|---|---|
| *¿Por qué se llama K'in Solar?* | Carlos | Repetir en 10 segundos la historia del glifo maya del Bloque 2 |
| *¿Qué hizo cada uno?* | Ambos | Ver tabla de roles en el README + "está documentado PR por PR en el repositorio" |
| *¿Cuánto lo hizo la IA?* | Carlos | "La mayor parte del código. Las decisiones de arquitectura, el modelo de datos y los controles de seguridad son nuestros, y cada corrección está documentada en la bitácora." |
| *¿Por qué Laravel?* | Andy | "Eloquent, Policies y CSRF de fábrica — lo que permite cumplir OWASP Top 10:2025 sin implementar seguridad a mano en 12 horas." |
| *¿Cómo coordinaron 5 agentes sin conflictos?* | Carlos | "Propiedad exclusiva de carpetas por agente y contratos de interfaz congelados en la primera hora." |
| *¿Por qué 0.40 kg/kWh?* | Andy | "Es el factor de emisión de la red eléctrica guatemalteca conforme a la CNEE. Lo especificaba el mismo reto." |
| *¿Está seguro el sistema?* | Carlos | Repetir la demo del 403 + "tenemos el checklist OWASP 2025 firmado en cada PR" |
| *¿Qué le falta?* | Andy | "Autenticación de doble factor y enlace directo con sensores de hardware físico en sitio. El Laboratorio SCADA emula la telemetría mientras tanto." |
| *¿Qué es lo más original del proyecto?* | Carlos | "El nombre y la identidad — no es un ícono de sol genérico, es el glifo maya real — y el servidor MCP que convierte a la IA en usuaria del sistema, no solo en su constructora." |
| *¿El mapa funciona sin internet?* | Andy | "No — Leaflet.js carga los tiles de OpenStreetMap desde sus servidores CDN. En la presentación usamos la red del salón." |

---

## Checklist previo a subir al frente

- [ ] PowerPoint (`docs/export/Presentacion-Kin-Solar-Guatemala.pptx`) o `public/presentacion.html` abierto y probado
- [ ] Sesión admin abierta en pestaña 1: `https://kin-solar-guatemala.duckdns.org`
- [ ] Sesión evaluador abierta en pestaña 2 (para la demo de 403)
- [ ] Pestaña 3: `https://kin-solar-guatemala.duckdns.org/api/v1/statistics` (JSON visible)
- [ ] Pestañas 4+: los archivos `.md` de la tabla en `docs/11-DIAPOSITIVAS-PRESENTACION.md`
- [ ] Celular con WiFi del salón, sistema cargado y sesión iniciada
- [ ] Video de respaldo accesible en 10 segundos (guardado en celular y en la laptop)
- [ ] Notificaciones del sistema operativo silenciadas
- [ ] Zoom del navegador al **100 %** (o 110 % si la pantalla del salón es pequeña)
- [ ] **Ensayo cronometrado hecho dos veces** — objetivo 12:00, tolerancia hasta 12:30
- [ ] Practicado el cambio PowerPoint → navegador → PowerPoint sin torpezas

---

## Notas de ensayo

| Ensayo | Fecha/Hora | Tiempo total | Qué ajustar |
|---|---|---|---|
| Ensayo 1 | | | |
| Ensayo 2 | | | |

> Si el ensayo supera 12:30, cortar primero el Bloque 9 (Elementos plus) a una sola frase.
> El bloque que NUNCA se corta: el mapa interactivo, la demo de alertas y la demo del 403.

---

## Nota de seguridad — credenciales de la demo

Las contraseñas de `admin@solarguatemala.gob.gt` y del resto de usuarios semilla **no se
documentan en ningún archivo de este repositorio** (OWASP A02/A07 — ver auditoría en
`docs/04-BITACORA-PROMPTS.md`). Quien presenta debe tenerlas guardadas de antemano en su
gestor de contraseñas o anotadas fuera de git, obtenidas del canal separado del equipo
(no de este documento). Si el repositorio llega a hacerse público, ninguna contraseña
real queda expuesta en el historial de este archivo.
