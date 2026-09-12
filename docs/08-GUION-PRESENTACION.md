# Guion de Presentación — 10 minutos cronometrados

> Las bases dan **10 minutos finales** para presentación y evaluación.
> La rúbrica pide: *"presentación clara, bien estructurada, con excelente comunicación y
> demostración fluida, uso de recursos visuales y demostración en vivo"*.
>
> **Se ensaya dos veces, cronometrado.** Una presentación improvisada sobre un sistema
> excelente puntúa peor que una ensayada sobre un sistema bueno.

---

## Estructura (minutos exactos)

| Min | Bloque | Quién | Contenido clave |
|---|---|---|---|
| 0:00–0:45 | **El problema** | Carlos | El problema de la trazabilidad solar en Guatemala, en lenguaje humano |
| 0:45–1:45 | **La solución y el diferenciador** | Carlos | Qué construimos, los 5 elementos innovadores, el stack |
| 1:45–2:30 | **Arquitectura y modelo de datos** | Andy | Diagrama ER + AWS EC2 + por qué Laravel en 12 horas con IA |
| 2:30–6:30 | **Demostración en vivo** | Andy | Los 5 módulos principales — ver recorrido detallado abajo |
| 6:30–7:15 | **Seguridad — OWASP Top 10:2025** | Carlos | Demo del 403 + log de auditoría |
| 7:15–8:30 | **Proceso: 5 agentes de IA + flujo de PRs** | Carlos | Repositorio, PRs, bitácora, MCP, 5 agentes coordinados |
| 8:30–9:00 | **Roles del equipo y cierre** | Ambos | Quién hizo qué + frase de cierre impactante |
| 9:00–10:00 | **Preguntas** | Ambos | Respuestas preparadas abajo |

---

## Bloque 1 — El problema (0:00–0:45) · Carlos

**Lo que se dice (literalmente):**
> "Guatemala tiene 22 departamentos con instalaciones de energía solar dispersas. Hoy, los
> operadores registran las mediciones de generación en hojas de cálculo o papel. No hay alertas
> automáticas cuando una granja falla. No hay un mapa que muestre en tiempo real qué departamento
> genera más energía. No hay trazabilidad del CO₂ evitado. El Estado no puede tomar decisiones
> basadas en datos. Ese es el problema que resolvimos."

**Duración objetivo:** 40 segundos. Sin jerga técnica. Sin mencionar Laravel todavía.

---

## Bloque 2 — La solución y los diferenciadores (0:45–1:45) · Carlos

**Lo que se dice:**
> "Construimos el Sistema de Registro y Monitoreo de Generación Solar por Departamento
> de Guatemala. Una plataforma web desplegada en la nube que tiene cinco elementos
> diferenciadores que no vimos en otros equipos:
>
> **Primero** — Mapa interactivo georreferenciado con los 22 departamentos usando Leaflet.js,
> con pines diferenciados por nivel de capacidad y alertas.
>
> **Segundo** — Algoritmo de proyección de generación futura propio, con factor de
> estacionalidad solar guatemalteca — época seca vs. lluviosa — justificado con datos del INSIVUMEH.
>
> **Tercero** — Factor normativo exacto de 0.40 kg de CO₂ evitado por kWh real generado,
> conforme a la norma CNEE de Guatemala, con equivalencias tangibles: árboles plantados
> y hogares guatemaltecos abastecidos.
>
> **Cuarto** — Detección automática de anomalías: si la generación real cae un 20% o más
> respecto a la esperada, el sistema dispara una alerta sin que nadie lo pida.
>
> **Quinto** — API REST pública documentada para integración con otros sistemas."

**Duración objetivo:** 60 segundos.

---

## Bloque 3 — Arquitectura (1:45–2:30) · Andy

**Lo que se muestra:**
- Diagrama ER (abrir el archivo `docs/06-CONTRATOS-HORA-1.md` en el navegador o tener la imagen preparada)
- Diagrama de arquitectura AWS EC2

**Lo que se dice:**
> "El sistema usa ocho tablas relacionales — departamentos, granjas, paneles, mediciones,
> alertas, proyecciones, usuarios y auditoría. Todo bajo MySQL 8, Laravel 13 y PHP 8.3,
> desplegado en AWS EC2 con Nginx. El modelo de datos lo diseñamos en la primera hora
> de la competencia y lo congelamos como contrato entre los 5 agentes de IA — así evitamos
> conflictos durante las 12 horas de trabajo paralelo."

**Duración objetivo:** 45 segundos.

---

## Bloque 4 — Demostración en vivo (2:30–6:30) · Andy

> ⚠️ **Los datos ya están cargados.** No improvisar. Si algo falla, seguir adelante.
> Hablar SIEMPRE mientras se hace clic. El silencio mata las presentaciones.

### Secuencia exacta de clics (4 minutos)

**[2:30–2:55] — Login y Dashboard**
1. Navegar a `https://kin-solar-guatemala.duckdns.org` (ya abierto en una pestaña)
2. Ingresar como admin: `admin@solarguatemala.gob.gt` / `Solar2026!Admin`
3. Mostrar el Dashboard con los 6 KPIs
4. Decir: *"Este es el tablero ejecutivo. En un vistazo, el administrador ve toda la red solar nacional: [leer los 6 KPIs en voz alta]."*

**[2:55–3:25] — Mapa Interactivo (RF-13)**
5. Hacer clic en **"Mapa Interactivo"** en el sidebar
6. Hacer zoom sobre el mapa de Guatemala
7. Hacer clic en una granja con pin rojo (alerta activa)
8. Mostrar el popup con los datos de la granja
9. Decir: *"Cada granja está geoposicionada con sus coordenadas reales. Los pines rojos tienen alertas activas. Al hacer clic vemos la capacidad instalada, las familias beneficiadas y el CO₂ evitado de esa instalación."*

**[3:25–3:55] — Registrar una medición (RF-08, RF-09, RF-14)**
10. Ir a **"Generación de Energía"** → **"Registrar medición"**
11. Seleccionar una granja
12. Ingresar kWh estimados: 500 | kWh reales: 350
13. Mostrar cómo el formulario calcula en tiempo real el CO₂ y la desviación del 30%
14. Mostrar la advertencia naranja de alerta antes de guardar
15. Decir: *"El sistema calcula en tiempo real el CO₂ evitado — usando el factor normativo de 0.40 kg por kWh — y advierte al operador antes de guardar que esta medición va a disparar una alerta automática por déficit del 30%."*
16. Guardar la medición

**[3:55–4:25] — Alerta generada automáticamente (RF-14)**
17. Ir a **"Alertas"** en el sidebar
18. Mostrar la nueva alerta con el badge rojo del 30%
19. Clic en **"Ver"** para abrir el detalle
20. Decir: *"La alerta se creó sola, sin intervención humana. El operador ahora puede investigar la causa y registrar la resolución."*
21. Hacer clic en **"Resolver"**, escribir una nota, confirmar

**[4:25–4:55] — Dashboard de Reportes y CO₂ (RF-11, RF-12)**
22. Ir a **"Reportes"**
23. Mostrar la matriz de los 22 departamentos
24. Hacer clic en **"Exportar CSV"** (demostrar la descarga)
25. Decir: *"La matriz departamental muestra en una sola pantalla toda la red solar del país. El administrador puede descargar el reporte en CSV listo para abrir en Excel, con tildes y caracteres especiales correctos."*

**[4:55–5:30] — Proyecciones (RF-15)**
26. Ir a **"Proyecciones"**
27. Seleccionar una granja con historial de mediciones
28. Seleccionar un mes futuro y hacer clic en **"Calcular proyección"**
29. Decir: *"El algoritmo aplica Promedio Móvil Ponderado ajustado por estacionalidad solar guatemalteca. Diferencia entre época seca — noviembre a abril — con mayor irradiancia, y época lluviosa — mayo a octubre — con nubosidad frecuente. El resultado es una proyección estadísticamente justificable ante cualquier revisor."*

**[5:30–6:00] — API REST (RF-16)**
30. Abrir una pestaña nueva y navegar a `https://kin-solar-guatemala.duckdns.org/api/v1/statistics`
31. Mostrar el JSON de respuesta
32. Navegar a `https://kin-solar-guatemala.duckdns.org/api-docs` y mostrar la documentación
33. Decir: *"La API REST pública expone todos los datos en formato JSON estándar. Cualquier sistema externo — del MINEM, de municipalidades o de ONGs — puede consumir estos datos sin credenciales."*

**[6:00–6:30] — Responsividad**
34. Tomar el celular (ya preparado con el wifi del salón)
35. Abrir `https://kin-solar-guatemala.duckdns.org` desde el celular
36. Mostrar el dashboard y el mapa en la pantalla del celular
37. Decir: *"Verificado desde 360 px de ancho — el tamaño de pantalla más pequeño. Todo el sistema es responsivo sin una línea de CSS custom innecesaria."*

---

## Bloque 5 — Seguridad OWASP Top 10:2025 (6:30–7:15) · Carlos

> Este bloque diferencia a los equipos que saben de seguridad de los que no.

**Lo que se dice:**
> "El sistema se diseñó desde el día cero contra el OWASP Top 10 en su edición **2025**, no
> la de 2021. Documentamos los diez controles y cada Pull Request lleva su checklist de
> seguridad firmado.
>
> Les muestro tres en vivo."

**Demo en vivo (45 segundos):**

1. Con el usuario `evaluador@umg.edu.gt` ya logueado en otra pestaña, escribir en la barra de direcciones: `https://kin-solar-guatemala.duckdns.org/farms/create`
   → Aparece **403 Prohibido** ← decir: *"Control de acceso. El visualizador no puede crear granjas, y el sistema lo impide aunque conozca la URL exacta."*

2. Cambiar a la pestaña del admin → ir al log de auditoría (si está expuesto en la UI) o decir:
   → *"Este intento quedó registrado en `audit_logs` con el usuario, la IP y la hora exacta. Es el control A09 de OWASP."*

3. Mostrar `docs/02-SEGURIDAD-OWASP-2025.md` por 3 segundos
   → *"El documento completo está versionado en el repositorio."*

---

## Bloque 6 — Proceso con 5 agentes de IA (7:15–8:30) · Carlos

**Lo que se muestra (abrir pestañas en orden):**

1. **Repositorio GitHub** → pestaña **Pull Requests** → N PRs fusionados
2. Abrir uno de los PRs → mostrar la sección **"Evidencia de uso de IA"** con el prompt y la corrección humana
3. **Historial de commits**: frecuentes, con Conventional Commits en español, dos autores
4. **Bitácora de prompts** (`docs/04-BITACORA-PROMPTS.md`) — mostrar que tiene entradas de cada agente

**Lo que se dice:**
> "Trabajamos con **cinco agentes de IA en paralelo** — Claude Code por dos, Codex y
> Antigravity por dos — sobre un flujo de ramas con Pull Request y revisión cruzada obligatoria.
> Nadie hizo push directo a main. Cada PR documenta el prompt que lo originó, las
> correcciones que aplicamos sobre la salida del agente, y su checklist de seguridad.
>
> ¿Cómo coordinamos cinco agentes sin conflictos? Con contratos de interfaz congelados en
> la primera hora: esquema de base de datos, nombres de rutas, nombres de vistas y sistema
> de diseño. Cada agente tuvo su zona de propiedad exclusiva de carpetas.
>
> La IA escribió buena parte del código. Las decisiones de arquitectura, el modelo de datos
> y los controles de seguridad los tomamos y verificamos nosotros."

**Duración objetivo:** 75 segundos.

---

## Bloque 7 — Roles del equipo y cierre (8:30–9:00) · Ambos

**Lo que se dice — Andy:**
> "Yo fui el arquitecto e integrador. Diseñé el esquema de base de datos, las Policies
> de autorización, integré todos los Pull Requests y construí la interfaz visual completa
> con Tailwind CSS y Leaflet.js."

**Lo que se dice — Carlos:**
> "Yo fui el backend y DevOps. Implementé los controladores, los servicios de negocio,
> la lógica de alertas y CO₂, y gestioné el despliegue en AWS EC2. También coordiné
> la documentación y la bitácora de uso de IA."

**Frase de cierre (Carlos, 10 segundos):**
> "En 12 horas, con cinco agentes de IA coordinados, construimos y desplegamos un sistema
> que cualquier municipalidad guatemalteca podría usar mañana mismo. Gracias."

---

## Bloque 8 — Preguntas preparadas (9:00–10:00) · Ambos

| Pregunta probable | Quién responde | Respuesta |
|---|---|---|
| *¿Qué hizo cada uno?* | Ambos | Ver tabla de roles en el README + "está documentado PR por PR en el repositorio" |
| *¿Cuánto lo hizo la IA?* | Carlos | "La mayor parte del código. Las decisiones de arquitectura, el modelo de datos y los controles de seguridad son nuestros, y cada corrección está documentada en la bitácora." |
| *¿Por qué Laravel?* | Andy | "Eloquent, Policies y CSRF de fábrica — lo que permite cumplir OWASP Top 10:2025 sin implementar seguridad a mano en 12 horas." |
| *¿Cómo coordinaron 5 agentes sin conflictos?* | Carlos | "Propiedad exclusiva de carpetas por agente y contratos de interfaz congelados en la primera hora. Ningún agente tocó la zona de otro." |
| *¿Por qué 0.40 kg/kWh?* | Andy | "Es el factor de emisión de la red eléctrica guatemalteca conforme a la Comisión Nacional de Energía Eléctrica (CNEE). Lo especificaba el mismo reto." |
| *¿Está seguro el sistema?* | Carlos | Repetir la demo del 403 + "tenemos el checklist OWASP 2025 firmado en cada PR" |
| *¿Qué le falta?* | Andy | "Autenticación con doble factor y HTTPS con certificado Let's Encrypt para producción total. En 12 horas priorizamos funcionalidad sobre certificación TLS." |
| *¿El mapa funciona sin internet?* | Andy | "No — Leaflet.js carga los tiles de OpenStreetMap desde sus servidores CDN. En la presentación usamos la red del salón." |

---

## Checklist previo a subir al frente

- [ ] Sesión admin abierta en pestaña 1: `https://kin-solar-guatemala.duckdns.org`
- [ ] Sesión evaluador abierta en pestaña 2 (para la demo de 403)
- [ ] Pestaña 3: repositorio GitHub → **Pull Requests**
- [ ] Pestaña 4: `https://kin-solar-guatemala.duckdns.org/api/v1/statistics` (JSON visible)
- [ ] Pestaña 5: `https://kin-solar-guatemala.duckdns.org/api-docs`
- [ ] Diagrama ER listo (en `docs/06-CONTRATOS-HORA-1.md` o imagen)
- [ ] Celular con WiFi del salón, `https://kin-solar-guatemala.duckdns.org` cargado y sesión iniciada
- [ ] Video de respaldo accesible en 10 segundos (guardado en celular y en la laptop)
- [ ] Notificaciones del sistema operativo silenciadas
- [ ] Zoom del navegador al **100 %** (o 110 % si la pantalla del salón es pequeña)
- [ ] **Ensayo cronometrado hecho dos veces**

---

## Notas de ensayo

| Ensayo | Fecha/Hora | Tiempo total | Qué ajustar |
|---|---|---|---|
| Ensayo 1 | | | |
| Ensayo 2 | | | |

> Si el ensayo supera 10:30, cortar alguno de los pasos del bloque de demo.
> El bloque que NUNCA se corta: el diferenciador del mapa + la demo del 403.
