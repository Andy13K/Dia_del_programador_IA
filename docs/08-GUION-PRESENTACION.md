# Guion de Presentación — 12 minutos cronometrados

> Las bases dan de **10 a 12 minutos** para presentación y evaluación.
> La rúbrica pide: *"presentación clara, bien estructurada, con excelente comunicación y
> demostración fluida, uso de recursos visuales y demostración en vivo"* (10 %), y evalúa además
> Originalidad (20 %), Uso de IA (20 %), UI/UX (15 %), Funcionalidad (25 %) y Documentación (10 %) —
> **este guion está construido para tocar los seis criterios explícitamente, no solo el de presentación.**
>
> **Se ensaya dos veces, cronometrado.** Una presentación improvisada sobre un sistema
> excelente puntúa peor que una ensayada sobre un sistema bueno.
>
> Diapositivas correspondientes: `docs/11-DIAPOSITIVAS-PRESENTACION.md` (13 diapositivas).

---

## Estructura (minutos exactos)

| Min | Bloque | Quién | Criterio de rúbrica que ataca |
|---|---|---|---|
| 0:00–0:40 | **El problema** | Carlos | Contexto (no puntúa directo, engancha al jurado) |
| 0:40–1:25 | **El nombre "K'in Solar"** | Carlos | Originalidad (20 %) |
| 1:25–2:15 | **La solución y los diferenciadores** | Carlos | Originalidad (20 %) |
| 2:15–3:00 | **Arquitectura y documentación del repositorio** | Andy | Documentación (10 %) |
| 3:00–4:15 | **Demo: Dashboard & Territorio** | Andy | Funcionalidad (25 %) + UI/UX (15 %) |
| 4:15–5:30 | **Demo: Alertas y Operación** | Andy | Funcionalidad (25 %) |
| 5:30–6:30 | **Demo: Proyección SMA-SF** | Andy | Funcionalidad (25 %) |
| 6:30–7:15 | **Innovación: Servidor MCP propio** | Carlos | Originalidad + Uso de IA (20 % + 20 %) |
| 7:15–8:00 | **Elementos plus** | Andy | Originalidad + UI/UX |
| 8:00–8:45 | **Seguridad OWASP Top 10:2025** | Carlos | Funcionalidad (seguridad) |
| 8:45–9:45 | **Metodología: 5 agentes de IA** | Carlos | Uso de IA (20 %) |
| 9:45–10:45 | **Roles del equipo y cierre** | Ambos | Documentación (tareas por integrante) |
| 10:45–12:00 | **Preguntas** | Ambos | — |

---

## Bloque 1 — El problema (0:00–0:40) · Carlos

**Lo que se dice (literalmente):**
> "Guatemala tiene 22 departamentos con instalaciones de energía solar dispersas. Hoy, los
> operadores registran las mediciones de generación en hojas de cálculo o papel. No hay alertas
> automáticas cuando una granja falla. No hay un mapa que muestre en tiempo real qué departamento
> genera más energía. No hay trazabilidad del CO₂ evitado. El Estado no puede tomar decisiones
> basadas en datos. Ese es el problema que resolvimos."

**Duración objetivo:** 40 segundos. Sin jerga técnica. Sin mencionar Laravel todavía.

---

## Bloque 2 — El nombre "K'in Solar" (0:40–1:25) · Carlos

> Este bloque es nuevo respecto a versiones anteriores del guion. Ataca directamente el criterio
> de **Originalidad, profesionalismo e innovación (20 %)** — el de mayor peso junto con Uso de IA
> y Funcionalidad — con una historia de quince segundos que ningún otro equipo va a poder contar.

**Lo que se muestra:** el logo del sistema (portada de la diapositiva 2, o abrir `public/images/kin-logo-negro.png`).

**Lo que se dice:**
> "Antes de mostrarles qué hace el sistema, quiero que sepan por qué se llama así.
>
> **K'in** es el signo del día del Sol en el calendario maya — el mismo que usan los pueblos
> K'iche' y Kaqchikel de Guatemala. Literalmente significa 'Sol' o 'Día'. El glifo de nuestro
> logo no es un ícono genérico de sol de un banco de iconos — **es el jeroglífico maya real**
> del día K'in.
>
> No elegimos ese nombre por casualidad. Guatemala es un país de raíz maya, y es un país de sol
> — más de trescientos días de irradiancia solar aprovechable al año. Un sistema que gestiona
> energía solar guatemalteca merecía un nombre que viniera de Guatemala, no una palabra en inglés
> traducida. Eso es originalidad desde la identidad del producto, no solo desde el código."

**Duración objetivo:** 45 segundos.

---

## Bloque 3 — La solución y los diferenciadores (1:25–2:15) · Carlos

**Lo que se dice:**
> "Construimos K'in Solar Guatemala: una plataforma web desplegada en la nube que tiene seis
> elementos diferenciadores que no vimos en otros equipos:
>
> **Primero** — Mapa interactivo georreferenciado con los 22 departamentos usando Leaflet.js,
> con pines diferenciados por nivel de capacidad y alertas.
>
> **Segundo** — Algoritmo de proyección de generación futura propio, con factor de
> estacionalidad solar guatemalteca — época seca vs. lluviosa.
>
> **Tercero** — Factor normativo exacto de 0.40 kg de CO₂ evitado por kWh real generado,
> conforme a la norma CNEE de Guatemala.
>
> **Cuarto** — Detección automática de anomalías: si la generación real cae un 20 % o más
> respecto a la esperada, el sistema dispara una alerta sin que nadie lo pida.
>
> **Quinto** — Un servidor MCP propio, que les voy a demostrar en vivo en unos minutos, porque
> la mayoría de equipos usa IA para escribir código, pero muy pocos hacen que la IA sea un
> usuario activo del sistema terminado.
>
> **Sexto** — la identidad maya-solar que acabamos de explicarles, sostenida en todo el sistema:
> logo, paleta de color y hasta el nombre de las variables de estilo."

**Duración objetivo:** 50 segundos.

---

## Bloque 4 — Arquitectura y documentación del repositorio (2:15–3:00) · Andy

> Este bloque ataca directamente **Documentación asociada al proyecto (10 %)**, cuyo criterio de
> 5/5 exige explícitamente: *"Objetivos del proyecto, Manual de usuario resumido, Tareas
> realizadas por cada miembro del equipo y rol, Commits claros"*. No basta con decir "tenemos
> documentación" — hay que nombrarla y, si el tiempo lo permite, abrirla en pantalla.

**Lo que se muestra:** una pestaña con `docs/03-PLANTILLA-ERS.md` abierta en GitHub, con scroll rápido
por el índice para que se vean los números de sección.

**Lo que se dice:**
> "El sistema usa nueve tablas relacionales — departamentos, granjas, paneles, mediciones,
> alertas, proyecciones, usuarios y auditoría — bajo MySQL 8, Laravel 13 y PHP 8.3, desplegado
> en AWS EC2 con Nginx y HTTPS de Let's Encrypt.
>
> Pero quiero detenerme en la documentación, porque también se evalúa y la mayoría de equipos
> la deja para el final. Tenemos una Especificación de Requerimientos de Software completa,
> con sus objetivos generales y específicos, diecinueve requerimientos funcionales — los
> diecisiete originales más dos que documentamos con honestidad como valor añadido — doce
> requerimientos no funcionales, un modelo de atributos de calidad bajo la norma ISO/IEC 25010,
> doce casos de uso narrados y seis diagramas UML: casos de uso, entidad-relación, secuencia y
> actividades. Este documento existe en Markdown en el repositorio, y también lo exportamos a
> Word y PDF.
>
> Además tenemos el estándar de seguridad OWASP documentado, un manual de usuario resumido con
> credenciales por rol, y una bitácora de más de mil líneas con cada prompt que le dimos a la
> inteligencia artificial y qué corregimos nosotros sobre su salida. Todo versionado, todo
> enlazado desde el README."

**Duración objetivo:** 45 segundos.

---

## Bloque 5 — Demostración en vivo: Dashboard & Territorio (3:00–4:15) · Andy

> ⚠️ **Los datos ya están cargados.** No improvisar. Si algo falla, seguir adelante.
> Hablar SIEMPRE mientras se hace clic. El silencio mata las presentaciones.

**[3:00–3:30] — Login y Dashboard**
1. Navegar a `https://kin-solar-guatemala.duckdns.org` (ya abierto en una pestaña)
2. Ingresar como admin: `admin@solarguatemala.gob.gt` / (contraseña entregada por canal
   separado — **nunca en este documento**, ver nota de seguridad al final del guion)
3. Mostrar el Dashboard con los 6 KPIs
4. Decir: *"Este es el tablero ejecutivo. En un vistazo, el administrador ve toda la red solar nacional: [leer los 6 KPIs en voz alta]."*

**[3:30–4:15] — Mapa Interactivo (RF-13)**
5. Hacer clic en **"Mapa Interactivo"** en el sidebar
6. Hacer zoom sobre el mapa de Guatemala
7. Hacer clic en una granja con pin rojo (alerta activa)
8. Mostrar el popup con los datos de la granja
9. Decir: *"Cada granja está geoposicionada con sus coordenadas reales. Los pines rojos tienen alertas activas. Al hacer clic vemos la capacidad instalada, las familias beneficiadas y el CO₂ evitado de esa instalación."*

---

## Bloque 6 — Demostración en vivo: Alertas y Operación (4:15–5:30) · Andy

**[4:15–4:45] — Registrar una medición (RF-08, RF-09, RF-14)**
1. Ir a **"Generación de Energía"** → **"Registrar medición"**
2. Seleccionar una granja
3. Ingresar kWh estimados: 500 | kWh reales: 350
4. Mostrar cómo el formulario calcula en tiempo real el CO₂ y la desviación del 30 %
5. Decir: *"El sistema calcula en tiempo real el CO₂ evitado — usando el factor normativo de 0.40 kg por kWh — y advierte al operador antes de guardar que esta medición va a disparar una alerta automática por déficit del 30 %."*
6. Guardar la medición

**[4:45–5:30] — Alertas, campanita con sonido y Laboratorio SCADA (RF-14 + elementos plus)**
7. Mostrar la **campanita de notificaciones** en el topbar con su contador badge.
8. Abrir el pop-up: mostrar cómo distingue alertas no leídas de leídas.
9. Ir a **"Laboratorio SCADA IoT"** (`/simulator`) y presionar **"Simular Falla Inversores"**:
   - Demostrar el sonido armónico (chime) sonando en vivo por Web Audio API.
   - Mostrar el osciloscopio en tiempo real y cómo la campanita suma la alerta sin refrescar la página.
10. Decir: *"La alerta se transmite vía telemetría SCADA simulada, emite aviso sonoro nativo y se persiste con trazabilidad OWASP A09 en `audit_logs`. Esto no estaba en las bases — lo construimos como plus, y se los vamos a resumir en un momento."*

---

## Bloque 7 — Demostración en vivo: Proyección SMA-SF (5:30–6:30) · Andy

1. Ir a **"Reportes"**, mostrar la matriz de los 22 departamentos y exportar a CSV.
2. Ir a **"Proyecciones"**, seleccionar una granja con historial y calcular la proyección de un mes futuro.
3. Decir: *"El algoritmo aplica una media móvil ponderada ajustada por estacionalidad solar guatemalteca: época seca de noviembre a abril, con mayor irradiancia; época lluviosa de mayo a octubre, con nubosidad frecuente. El resultado es una proyección estadísticamente justificable, no una caja negra — el propio sistema muestra la fórmula aplicada."*
4. Opcional si sobra tiempo: abrir el celular y mostrar el dashboard responsivo a 360 px.

**Duración objetivo del Bloque 5+6+7 combinado:** 3 minutos 30 segundos (el corazón de la demo).

---

## Bloque 8 — Innovación: Servidor MCP propio (6:30–7:15) · Carlos

**Lo que se dice:**
> "Todos usamos IA para escribir código. Pocos hacen que la IA termine siendo **usuaria** del
> sistema ya construido. Nosotros sí: construimos un servidor MCP — Model Context Protocol —
> propio, en Node.js, con tres herramientas: consultar estadísticas nacionales, listar granjas,
> y registrar una medición de generación."

**Demo (si hay tiempo y Claude Desktop configurado):**
1. Escribir en Claude Desktop: *"Registra una medición de 45,000 kWh para la Granja Villa Nueva"*.
2. Mostrar cómo la IA invoca la herramienta MCP, se autentica con una clave dedicada, y el dato aparece de inmediato en el dashboard web.
3. Decir: *"Ese cambio queda atribuido en la auditoría a un usuario de sistema dedicado — nunca se mezcla con las acciones de un humano."*

**Si no hay tiempo para la demo en vivo:** mostrar la captura de pantalla guardada en `docs/evidencias/` y narrar el mismo flujo.

**Duración objetivo:** 45 segundos.

---

## Bloque 9 — Elementos plus (7:15–8:00) · Andy

> Este bloque existe para que el jurado entienda que **no nos quedamos en el mínimo de las bases**.
> Es breve y enumerativo a propósito — ya se demostraron varios de estos elementos en vivo en el
> Bloque 6, así que aquí solo se nombran y se cierra el argumento.

**Lo que se dice:**
> "Antes de hablar de seguridad, un resumen rápido de lo que construimos más allá de las
> diecisiete funciones que pedía el reto: el Laboratorio SCADA que ya vieron, la campanita con
> sonido nativo del navegador, un cliente de prueba de la API que corre sin backend propio para
> demostrar consumo externo real, navegación móvil tipo aplicación nativa sin scroll horizontal,
> y un sistema de tema claro y oscuro completo — con su propia paleta de variables de diseño,
> la misma que estamos usando ahora mismo en esta presentación. Todo esto está documentado en
> el ERS como valor añadido, no oculto como si fuera parte del alcance original."

**Duración objetivo:** 45 segundos.

---

## Bloque 10 — Seguridad OWASP Top 10:2025 (8:00–8:45) · Carlos

> Este bloque diferencia a los equipos que saben de seguridad de los que no.

**Lo que se dice:**
> "El sistema se diseñó desde el día cero contra el OWASP Top 10 en su edición **2025**, no
> la de 2021. Documentamos los diez controles y cada Pull Request lleva su checklist de
> seguridad firmado. Les muestro uno en vivo."

**Demo en vivo (30 segundos):**
1. Con el usuario `evaluador@umg.edu.gt` ya logueado en otra pestaña, escribir en la barra de direcciones: `https://kin-solar-guatemala.duckdns.org/farms/create`
   → Aparece **403 Prohibido** ← decir: *"Control de acceso. El visualizador no puede crear granjas, y el sistema lo impide aunque conozca la URL exacta. Ese intento queda registrado en `audit_logs` con usuario, IP y hora exacta — el control A09."*

**Duración objetivo:** 45 segundos.

---

## Bloque 11 — Metodología: 5 agentes de IA (8:45–9:45) · Carlos

**Lo que se muestra (pestañas ya abiertas):** Pull Requests fusionados → uno abierto con su sección "Evidencia de uso de IA" → historial de commits → `docs/04-BITACORA-PROMPTS.md`.

**Lo que se dice:**
> "Trabajamos con **cinco agentes de IA en paralelo** — Claude Code por dos, Codex y
> Antigravity por dos — sobre un flujo de ramas con Pull Request y revisión cruzada obligatoria.
> Nadie hizo push directo a `master`. Cada PR documenta el prompt que lo originó, las
> correcciones que aplicamos sobre la salida del agente, y su checklist de seguridad.
>
> ¿Cómo coordinamos cinco agentes sin conflictos? Con contratos de interfaz congelados en
> la primera hora: esquema de base de datos, nombres de rutas y sistema de diseño. Cada agente
> tuvo su zona de propiedad exclusiva de carpetas.
>
> La IA escribió buena parte del código — incluyendo esta misma presentación y su guion. Las
> decisiones de arquitectura, el modelo de datos y los controles de seguridad los tomamos y
> verificamos nosotros, y cada corrección queda escrita en la bitácora, no solo en nuestra memoria."

**Duración objetivo:** 60 segundos.

---

## Bloque 12 — Roles del equipo y cierre (9:45–10:45) · Ambos

**Lo que se dice — Andy:**
> "Yo fui el arquitecto e integrador. Diseñé el esquema de base de datos, las Policies
> de autorización, integré todos los Pull Requests y construí la interfaz visual completa
> con Tailwind CSS y Leaflet.js, además del servidor MCP propio y el despliegue en AWS."

**Lo que se dice — Carlos:**
> "Yo fui el backend y de seguridad. Implementé los servicios de negocio, la lógica de
> alertas y CO₂, la auditoría OWASP Top 10:2025, y coordiné la documentación y la bitácora
> de uso de IA."

**Frase de cierre (Carlos, 15 segundos):**
> "K'in significa Sol en maya. Guatemala tiene sol y raíces mayas de sobra; lo que le faltaba
> era un sistema que las conectara con datos. En doce horas, con cinco agentes de IA
> coordinados, construimos y desplegamos un sistema que cualquier municipalidad guatemalteca
> podría usar mañana mismo. Gracias."

---

## Bloque 13 — Preguntas preparadas (10:45–12:00) · Ambos

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

- [ ] Sesión admin abierta en pestaña 1: `https://kin-solar-guatemala.duckdns.org`
- [ ] Sesión evaluador abierta en pestaña 2 (para la demo de 403)
- [ ] Pestaña 3: repositorio GitHub → **Pull Requests**
- [ ] Pestañas 4-9: los archivos `.md` de la tabla en `docs/11-DIAPOSITIVAS-PRESENTACION.md` (README, ERS, OWASP, bitácora, checklist, manual de usuario)
- [ ] Diagramas UML listos (ya embebidos en el ERS — `docs/03-PLANTILLA-ERS.md` §6.2–§6.4)
- [ ] Celular con WiFi del salón, sistema cargado y sesión iniciada
- [ ] Video de respaldo accesible en 10 segundos (guardado en celular y en la laptop)
- [ ] Notificaciones del sistema operativo silenciadas
- [ ] Zoom del navegador al **100 %** (o 110 % si la pantalla del salón es pequeña)
- [ ] **Ensayo cronometrado hecho dos veces** — objetivo 12:00, tolerancia hasta 12:30

---

## Notas de ensayo

| Ensayo | Fecha/Hora | Tiempo total | Qué ajustar |
|---|---|---|---|
| Ensayo 1 | | | |
| Ensayo 2 | | | |

> Si el ensayo supera 12:30, cortar primero el Bloque 9 (Elementos plus) a una sola frase —
> ya se demostraron varios de esos elementos en vivo en el Bloque 6. El bloque que NUNCA se
> corta: el mapa interactivo, la demo de alertas y la demo del 403.

---

## Nota de seguridad — credenciales de la demo

Las contraseñas de `admin@solarguatemala.gob.gt` y del resto de usuarios semilla **no se
documentan en ningún archivo de este repositorio** (OWASP A02/A07 — ver auditoría en
`docs/04-BITACORA-PROMPTS.md`). Quien presenta debe tenerlas guardadas de antemano en su
gestor de contraseñas o anotadas fuera de git, obtenidas del canal separado del equipo
(no de este documento). Si el repositorio llega a hacerse público, ninguna contraseña
real queda expuesta en el historial de este archivo.
