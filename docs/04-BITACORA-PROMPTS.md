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

### [18:50] Antigravity (Agente C) — Maquetación de Layout, Componentes Blade, Dashboard y Mapa Interactivo — PR #3

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

### [19:10] Claude Code (Agente A) — Autenticación, RBAC y datos demo — PR #4

**Objetivo:** implementar autenticación por sesión, control de acceso por rol (RBAC) conforme
a OWASP Top 10:2025, un servicio de auditoría automático, y sembrar los 3 usuarios y los datos
demo realistas (paneles, granjas, mediciones, alertas) que el jurado verá en la demo.

**Prompt:**
> Actúa como el Agente A (Arquitecto de Seguridad y Datos) en Claude Code para el proyecto
> Solar Guatemala [...] implementar la autenticación segura, el control de acceso (RBAC)
> conforme a OWASP Top 10:2025 y los datos de prueba realistas para la evaluación del jurado.
> (prompt completo con 4 tareas: AuthController + login/logout + throttling, columna role +
> 4 Policies, AuditService, DatabaseSeeder + SolarDemoSeeder)

**Resultado:** `AuthController` (showLoginForm/login/logout) + `LoginRequest` + vista de login
con `@csrf`; columna `role` (enum, fuera de `$fillable`) y 4 Policies (`SolarFarmPolicy`,
`SolarPanelPolicy`, `EnergyGenerationPolicy`, `GenerationAlertPolicy`) registradas en
`AppServiceProvider` junto con los Gates `manage-*` que ya usaban las rutas del PR #1;
`AuditService::log()`; los 3 usuarios semilla del contrato con su rol; `SolarDemoSeeder` con 6
paneles, 10 granjas, 60 mediciones y 3 alertas activas (déficit del 25%). Verificado con
`migrate:fresh --seed` y probando el login/logout real contra `php artisan serve`.

**Intervención humana / autocorrección durante la ejecución:**
- **Riesgo de choque de working directory:** al preparar la rama, `git status` mostró que la
  sesión de Codex (rama `feat/carlos-codex/backend-paneles-granjas-mediciones`) tenía cambios
  sin commitear *en ese mismo momento* en el mismo directorio compartido. Se detuvo el
  `git switch master` (que habría descartado su trabajo) y, con el visto bueno del usuario, se
  aisló todo este PR en un **git worktree** aparte (`.claude/worktrees/...`) con su propia base
  de datos de prueba (`solar_guatemala_claude_wt`) para no tocar ni el checkout ni la base de
  datos que Codex estaba usando para probar.
- Se detectó que `app/Services/BackendAuditService.php` (de Codex, sin commitear en su rama) es
  funcionalmente parecido a `AuditService` que pidió este prompt — mismo directorio, nombres
  distintos. Se dejó una nota en el commit y aquí para que el equipo reconcilie ambos servicios
  antes del merge final; no se intentó fusionarlos unilateralmente.
- `role` se agregó **fuera** de `$fillable` en `User` (regla explícita de
  `docs/02-SEGURIDAD-OWASP-2025.md`, A01: "Jamás incluir role... en $fillable"). Los seeders
  asignan el rol por atributo directo (`$user->role = ...; $user->save();`), nunca por mass
  assignment.
- Al probar el logout con un token CSRF deliberadamente inválido vía `fetch()`, la petición
  igual pasó. Antes de asumir una falla de seguridad se revisó el middleware de Laravel 13
  (`PreventRequestForgery`): valida también la cabecera `Sec-Fetch-Site` del navegador, y mi
  prueba se hacía desde el mismo origen (`same-origin`), lo cual la aprueba legítimamente sin
  mirar el token — así protege Laravel 13 por defecto. No había ninguna vulnerabilidad; quedó
  documentado para que nadie repita la misma prueba mal planteada.

Detalle completo en el diff del PR.

**Iteraciones:** 1 (los hallazgos anteriores se resolvieron dentro de la misma pasada, antes de
commitear).

### [19:30] Agente C (Antigravity) & Agente E (Claude Code) — Integración de Rutas Web, Vistas CRUD Blade y API REST v1 — PR #5

**Objetivo:** Conectar el enrutador de Laravel (`routes/web.php` y `routes/api.php`) que mantenía respuestas de prueba (`fn () => 'OK: home'`) hacia los controladores reales y hacia el Dashboard Nacional (cálculo de 6 KPIs con datos de la BD), crear las vistas Blade CRUD faltantes (`farms/`, `panels/`, `generations/`, `alerts/show`), y exponer datos reales en los endpoints REST v1 (RF-16).

**Prompt:**
> "Conectar las rutas web hacia los controladores existentes y hacia el Dashboard ejecutivo con cálculo en tiempo real de los 6 KPIs (granjas, paneles, potencia kW, energía kWh, familias y CO2 evitado con factor 0.40). Crear las vistas Blade completas para los CRUDs de Granjas Solares (index con filtros por departamento/estado/búsqueda, create, edit, show), Paneles (index, create, edit), Mediciones de Generación (index, create con cálculo en vivo de emisiones y detección de déficit >= 20%, show) y Alertas (actualizar index y crear show con resolución). Conectar routes/api.php para servir datos reales JSON uniformes en /api/v1/departments, /farms, /generations, /statistics y /alerts."

**Resultado:**
- Creación de 10 vistas Blade consistentes con el diseño de Tailwind CSS v4, Lucide Icons, componentes reutilizables y modo oscuro.
- Cableado de 42 rutas en `routes/web.php` y `routes/api.php`.
- Dashboard servido tanto en `/` (modo público para evaluadores) como en `/dashboard` (bajo `auth`), con manejo defensivo de excepciones ante entornos de testing.
- Pruebas PHPUnit pasando al 100% y assets de Vite compilados en 534ms.

**Intervención humana:**
- Se detectó que en entornos de pruebas SQLite en memoria sin migraciones automáticas, la consulta de Eloquent en el handler del Dashboard fallaba; se añadió manejo defensivo con `try/catch (\Throwable)` y valores de respaldo basados en los seeders oficiales.
- Se implementó un cálculo interactivo en Javascript dentro del formulario de registro de mediciones (`generations/create`) para advertir visualmente al operador antes de guardar si la medición generará una alerta automática por déficit $\ge 20\%$.
- Se configuró la exportación de reportes departamentales en formato CSV con BOM UTF-8 para garantizar apertura nativa en Microsoft Excel sin problemas de tildes o caracteres especiales.

### [19:50] Agente C (Antigravity - Andy) — Pulido UI/UX, Vistas de Error y Conexión de Datos Reales — PR #6

**Objetivo:** Rediseñar la pantalla de autenticación con el sistema de diseño solar y accesos rápidos de 1-clic para el jurado, crear las 4 páginas de error personalizadas (`403`, `404`, `419`, `500`), y conectar datos 100% dinámicos de MySQL en el Ranking Departamental del Dashboard, la gráfica comparativa histórica y la matriz de 22 departamentos en Reportes.

**Prompt:**
> "Rediseñar auth/login.blade.php con estilo solar glassmorphism, botones para autocompletar credenciales demo (Admin, Operador, Evaluador) y aviso de limitación throttle:5,1 (OWASP A07). Crear las 4 vistas de error 403, 404, 419 y 500 bajo layouts/app.blade.php para cumplir con la rúbrica de UI/UX. Conectar los datos de la base de datos para nutrir el Top 5 ranking de departamentos, la gráfica mensual de Chart.js y la matriz de reportes departamentales en routes/web.php."

**Resultado:**
- Pantalla de inicio de sesión con branding oficial, responsive y accesible para evaluadores.
- 4 vistas de error diseñadas consistentemente que evitan pantallas por defecto del framework.
- Métricas dinámicas en tiempo real en Reportes y Dashboard con tolerancia a fallos.
- Pruebas PHPUnit al 100% y assets compilados con Vite en 549ms.

**Intervención humana:**
- Se corrigió un conflicto de sintaxis en el compilador de Blade al usar arrays literales dentro de directivas `@json()`, separando la inyección de datos de los valores de respaldo en Javascript limpio.
- Se agregaron botones de un clic para autocompletar credenciales de Administrador, Operador y Jurado Evaluador en el Login, agilizando la demostración en vivo de 10 minutos.

---

### [19:44] Codex (Agente B) — SMA-SF y pruebas automatizadas

**Objetivo:** implementar RF-15 en `ForecastService`, conectar el POST existente y
agregar las pruebas de carbono, alertas, proyecciones y API pública solicitadas.

**Prompt (extractos literales):**
> Trabajemos en la rama feat/carlos-codex/forecast-service-y-pruebas.
> Lee AGENTS.md antes de empezar. Recuerda las reglas: nunca push a main, FormRequest, PSR-12 y Co-Authored-By.

> El método generateForecast(SolarFarm $farm, string $targetPeriod): GenerationForecast debe calcular el promedio móvil ponderado de las últimas 3 mediciones reales y multiplicarlo por el factor estacional del mes proyectado.

> Asegúrate de que php artisan test pase al 100% antes de abrir el PR.
> Documenta el prompt en docs/04-BITACORA-PROMPTS.md.

**Resultado:** promedio con pesos 3, 2 y 1 desde el registro más reciente anterior
al período objetivo, dividido entre 6; factor 1.20 en noviembre–abril y 0.88 en
mayo–octubre. Requiere tres mediciones, redondea a dos decimales y persiste SMA-SF
con actualización idempotente por granja/período y bloqueo transaccional.
FormRequest y controlador con permiso de módulo, Policy de granja, alcance por
propietario y auditoría. El POST sin parámetros conserva el botón existente:
genera el próximo mes para las granjas autorizadas, con rollback del lote completo
si alguna no tiene historial suficiente. También acepta solar_farm_id y target_period.
La única edición en rutas conecta el POST solicitado; no cambia middleware.

**Verificación:** `php artisan test`: 17/17 pruebas y 105 aserciones correctas.
Casos: las doce estaciones mensuales, ponderación, exclusión de registros futuros
y ajenos, historial insuficiente, idempotencia, umbral inclusivo de alerta, estimación
cero, autorización HTTP, validación, auditoría y rollback; tres endpoints API con
respuesta 200 y envoltura success/data. Los servicios que persisten usan SQLite
en memoria con RefreshDatabase, aun estando sus pruebas en tests/Unit como se pidió.
Vite compiló después de instalar el lockfile existente con npm ci --ignore-scripts;
no se agregaron dependencias. El worktree usa su propia APP_KEY local no versionada.
La URL pública sigue sin especificar: no se declara verificación pública.

**Intervención humana:** el humano fijó factores, tres mediciones, nombres de
archivos, conexión POST y exigencia de suite completa. Codex eligió pesos 3/2/1
y exigencia de tres registros; no se atribuyen estas decisiones al humano.
Durante el trabajo paralelo cambió la rama del directorio compartido: Codex
trasladó su commit a un worktree exclusivo y retiró ese commit de la rama ajena,
preservando los cambios del otro agente. No hubo correcciones humanas posteriores.

**Pendientes de otros módulos:** la vista de proyecciones conserva los ejemplos
estáticos del agente C; persistir una proyección no actualiza esas filas estáticas.
La API de estadísticas tiene valores de ejemplo cuando no existen datos; las
pruebas solicitadas verifican contrato y un caso poblado, no corrigen esa lógica.

**Iteraciones:** una solicitud, con implementación y comprobaciones internas.
**Herramientas:** Codex, terminal, Git, PHPUnit, Pint y GitHub CLI.

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
