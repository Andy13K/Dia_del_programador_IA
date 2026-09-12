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
| A | Carlos | Claude Code | Claude 3.7 Sonnet / Opus | Arquitectura, modelo de datos, rutas, policies, integrador principal |
| B | Carlos | Codex | Codex / GPT-4o | Controladores, servicios, validación, pruebas |
| C | Andy Aquino | Antigravity | Gemini | Interfaz, vistas Blade, Tailwind CSS, responsividad |
| D | Carlos | Antigravity | Gemini | DevOps, despliegue, seguridad, tareas operativas de soporte |
| E | Andy Aquino | Claude Code | Claude 3.7 Sonnet / Opus | Co-arquitecto (rol Agente A), documentación técnica, QA y corrección de detalles |

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

### [18:20] Claude Code (Agente A) — Scaffolding inicial de Laravel — PR #2

**Objetivo:** instalar la última versión de Laravel sobre PHP 8.3, crear las 8 migraciones
congeladas, los modelos Eloquent con `$fillable` y relaciones, el seeder de los 22
departamentos y registrar las rutas nombradas de `docs/06-CONTRATOS-HORA-1.md`, sin tocar
`docs/`, `AGENTS.md`, `CLAUDE.md`, `GEMINI.md`, `README.md` ni `.git/`.

**Prompt:**
> Actúa como el Agente A (Arquitecto e Integrador) en Claude Code [...] Debes inicializar el
> proyecto base instalando LA ÚLTIMA VERSIÓN DISPONIBLE DE LARAVEL sobre PHP 8.3 [...] crear
> las 8 migraciones congeladas, los modelos Eloquent con relaciones y $fillable explícito, el
> seeder con los 22 departamentos de Guatemala y el registro de rutas. (prompt completo con los
> 7 pasos detallados — instalación, migraciones exactas, modelos, seeder, rutas, verificación y PR)

**Resultado:** Laravel 13.31.0 instalado sobre PHP 8.3.30 (Laragon); 8 migraciones en orden de
dependencia; 7 modelos nuevos (`Department`, `SolarPanel`, `SolarFarm`, `EnergyGeneration`,
`GenerationAlert`, `GenerationForecast`, `AuditLog`) con `$fillable` explícito y relaciones
según el contrato, más el accessor `getCalculatedCapacityKwAttribute()`; `DepartmentSeeder` con
los 22 departamentos; 39 rutas nombradas registradas en `web.php`/`api.php`. Verificado con
`migrate:fresh --seed` (22 filas en `departments`) y en el navegador contra
`php artisan serve`.

**Intervención humana / autocorrección durante la ejecución:**
- El primer intento de mover los archivos de `temp_laravel/` a la raíz con `Move-Item -Force`
  hubiera sobreescrito `AGENTS.md`, `CLAUDE.md` y `README.md` del proyecto (Laravel trae sus
  propios stubs con esos nombres). Se excluyeron explícitamente antes de mover.
- Al probar `/dashboard` en el navegador, el middleware `auth` devolvía **500**
  (`RouteNotFoundException: Route [login] not defined`) en vez de simplemente redirigir,
  porque aún no existe andamiaje de autenticación (Breeze/Fortify no está instalado — fuera de
  alcance de este PR). Se agregó una ruta `login` placeholder para que la redirección no rompa
  a los Agentes B/C mientras prueban sus propias rutas; se documentó como pendiente en el PR.
- Se decidió usar `protected $fillable = [...]` clásico en vez del nuevo atributo
  `#[Fillable(...)]` de Laravel 13/PHP 8.3, para que el checklist OWASP ("`$fillable`
  explícito") sea inequívoco a simple vista para el jurado y el resto del equipo.
- MySQL de Laragon no estaba inicializado/corriendo en el entorno; se detectó el binario
  (`C:\laragon\bin\mysql\mysql-8.4.3-winx64`) y se arrancó manualmente `mysqld` para poder
  correr las migraciones y verificar el seeder.

**Iteraciones:** 1 (sin retrabajo del contrato; los ajustes anteriores se resolvieron dentro de
la misma pasada, antes de commitear).

---

### [18:50] Antigravity (Agente C) — Maquetación de Layout, Componentes Blade, Dashboard y Mapa Interactivo — PR #2

**Objetivo:** Desarrollar el sistema visual integral de la aplicación: layout maestro responsivo con sidebar, suite de 8 componentes Blade reutilizables, vista de Dashboard nacional con 6 KPIs obligatorios (RF-11), vista del Mapa Interactivo de Guatemala con Leaflet.js (RF-13), matriz de reportes comparativos departamentales (RF-12), bandeja de alertas de generación (RF-14), visualización de proyecciones algorítmicas (RF-15) y documentación de la API REST (RF-16).

**Prompt:**
> LISTO YA SE LOS ENVIE AHORA VAMOS NOSOTROSO CON LO QUE NOS TOCA POR FAVOR

**Resultado:**
- Creación de `resources/views/layouts/app.blade.php` con navegación lateral completa, soporte de modo oscuro persistente en localStorage, notificaciones flash y compatibilidad con Leaflet.js y Chart.js.
- Creación de 8 componentes Blade reutilizables en `resources/views/components/` (`card`, `kpi-card`, `button`, `badge`, `empty-state`, `table`, `input`, `select`, `modal`).
- Implementación de `dashboard.blade.php` con las 6 métricas macro obligatorias, gráfica comparativa mensual real vs. esperada y ranking departamental.
- Implementación de `map/index.blade.php` centrado en Guatemala con Leaflet.js, pines diferenciados por capacidad y estado de alerta, popups informativos y filtros departamentales.
- Vistas de `reports/index.blade.php`, `alerts/index.blade.php`, `forecasts/index.blade.php` y `api-docs/index.blade.php`.
- Compilación de assets con Vite y Tailwind CSS v4 (`npm run build`).

**Intervención humana:**
- Se acordó enfocar el Agente C en la construcción de los componentes Blade y vistas principales para no pisar el trabajo de controladores y modelos que realizan los agentes A y B.
- Verificación en local de la compatibilidad con Node v22 y compilación exitosa de los estilos de Tailwind.

**Iteraciones:** 2

---

### [19:00] Codex (Agente B) — Backend de paneles, granjas, mediciones y alertas — PR #2

**Objetivo:** implementar las validaciones, controladores y servicios del módulo solar
en la rama `feat/carlos-codex/backend-paneles-granjas-mediciones`, respetando los
modelos, rutas y migraciones del Agente A y los contratos de vistas congelados.

**Prompts clave (extractos literales):**
> Actúa como el Agente B (Desarrollador Backend y Lógica de Negocio) en Codex para el proyecto Solar Guatemala.

> Tu responsabilidad exclusiva son las carpetas `app/Http/Controllers/`, `app/Http/Requests/` y `app/Services/`. NO toques migraciones, modelos ni rutas para evitar conflictos con el Agente A.

> Haz push de la rama `feat/carlos-codex/backend-paneles-granjas-mediciones` y abre el PR en GitHub.

La solicitud detalló los FormRequests, la conversión de CO₂ (`real_kwh * 0.40`),
el déficit inclusivo del 20 %, la sincronización de paneles, SoftDeletes y la
resolución de alertas con notas y fecha.

**Resultado:** cuatro controladores, FormRequests y servicios de negocio con
autorización por acción, consultas acotadas, auditoría y transacciones. Se controlan
mediciones mensuales duplicadas, reevaluación de alertas, doble resolución,
referencias eliminadas, estimación cero y precisión del umbral decimal.
Commits de implementación: `6ac2155` y `e19ea8f`.
PR: https://github.com/Andy13K/Dia_del_programador_IA/pull/2 (en borrador).

**Verificación:** 57 comprobaciones aisladas sobre SQLite en memoria, las dos
pruebas existentes, sintaxis de los 21 archivos PHP y Laravel Pint pasaron.
El script temporal utilizó Gates y vistas de prueba; no verifica las Policies
definitivas ni la interfaz. El build no pudo ejecutarse por ausencia de Vite local.
Quedan pendientes integración de rutas, Policies y vistas, pruebas con MySQL y
verificación en la URL pública. No se instalaron dependencias.

**Intervención humana:** el humano definió alcance, rama y reglas de negocio.
Posteriormente respondió «Si por favor» a la solicitud de autorización para agregar
únicamente esta entrada a la bitácora, como excepción al límite de tres carpetas.
No hubo correcciones humanas al código durante esta tarea; los controles adicionales
y ajustes de precisión fueron realizados por Codex, sin atribuirlos al humano.

**Trazabilidad:** esta entrada se incorpora después de abrir el PR en borrador,
tras recibir la autorización explícita; no se presenta como un registro previo al PR.
Herramientas utilizadas: terminal y edición de Codex, Git y GitHub CLI. No se utilizó
un servidor MCP externo para la implementación.

**Iteraciones:** una solicitud de implementación con ciclos internos de comprobación
y corrección; un seguimiento humano para autorizar el registro documental.

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
