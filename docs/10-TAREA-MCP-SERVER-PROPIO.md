# Tarea: Servidor MCP propio de K'in Solar Guatemala

> **Para el agente que ejecute esta tarea:** este documento es autosuficiente. Contiene el
> código real actual del proyecto (rutas, servicios, modelos) que vas a reutilizar, no
> inventes ni asumas nombres distintos a los aquí listados — verificalos vos mismo antes de
> escribir código, porque el proyecto puede haber cambiado desde que se escribió esto.
> Seguí `CLAUDE.md` / `AGENTS.md` en la raíz del repo para las reglas de commits, ramas y
> checklist de seguridad OWASP — esta tarea no está exenta de esas reglas.

---

## 1. Contexto y por qué existe esta tarea

Este es un proyecto de competencia universitaria ("Día del Programador con IA"). La rúbrica de
evaluación pide, dentro del criterio **"Uso de Inteligencia Artificial" (20% de la nota)**, el uso
de **MCP Server** — pero solo como parte del *proceso de desarrollo* (junto con "IDE con agente
integrado" y "vibecoding"). Eso ya está cubierto: el equipo usa MCP de `filesystem`, `mysql` y
`github` durante el desarrollo (ver `.mcp.json` en la raíz y `docs/04-BITACORA-PROMPTS.md`).

**Esta tarea es un extra, no un requisito de la rúbrica.** El objetivo es sumar al criterio de
**Originalidad (20%)**: construir un **servidor MCP propio del producto** — no para desarrollarlo,
sino como una **funcionalidad más del sistema**. La idea: cualquier asistente de IA (Claude
Desktop, Claude Code, etc.) se conecta a este servidor y puede, en lenguaje natural, **consultar
datos reales de la plataforma y registrar una medición de generación energética real** — un
cambio que después se ve reflejado en el dashboard de la aplicación en vivo.

Esto es lo que se le va a demostrar al jurado: abrir un chat de IA cualquiera, escribirle *"registra
500 kWh reales para la granja X en el período 2026-09"*, ver cómo la IA llama a la herramienta MCP,
y luego refrescar el dashboard de K'in Solar y ver el dato ahí.

---

## 2. Qué hay que construir — resumen ejecutivo

Dos piezas:

1. **Un endpoint nuevo en la API de Laravel** (`POST /api/v1/generations`), protegido con una
   llave de API simple (no la sesión normal del sistema), que registra una medición real
   reutilizando la lógica de negocio que ya existe.
2. **Un servidor MCP propio** (proyecto Node.js separado, dentro de `mcp-server/` en la raíz del
   repo) con 2-3 herramientas que consumen esa API — una de solo lectura (estadísticas) y una de
   escritura (registrar medición).

---

## 3. Parte A — Endpoint de escritura en la API de Laravel

### 3.1 Por qué no se puede usar la ruta que ya existe

Ya existe `POST /generations` (ver `routes/web.php`), pero esa ruta exige **sesión autenticada**
del sistema (`auth` + `can:manage-generations`) — un servidor MCP externo no tiene una sesión de
navegador. Por eso hace falta una ruta nueva en `routes/api.php`, con un mecanismo de
autenticación distinto: una llave estática por cabecera HTTP.

### 3.2 Servicios existentes que hay que reutilizar (no reinventar)

El proyecto ya tiene toda la lógica de negocio encapsulada. **No dupliques esta lógica**, llamala
directamente:

**`app/Services/EnergyGenerationService.php`** — método `store()`:

```php
public function store(SolarFarm $farm, array $data, int $userId): EnergyGeneration
{
    // Ya hace, dentro de una transacción con lockForUpdate:
    // 1. Valida que no exista ya una medición para esa granja+período
    // 2. Calcula el CO2 evitado con CarbonOffsetService (real_kwh * 0.40)
    // 3. Evalúa automáticamente si hay que crear una alerta por déficit ≥20%
    //    (AlertEvaluationService::evaluateGeneration())
    // 4. Devuelve el EnergyGeneration con la relación generationAlert cargada
}
```

Esto significa que tu endpoint nuevo, en el fondo, solo tiene que: validar el request → resolver
la granja → llamar a `$this->generations->store($farm, $data, $userId)` → registrar auditoría →
devolver JSON. Toda la parte difícil (CO2, alertas, condiciones de carrera) ya está resuelta.

**Validación a reutilizar** — mismas reglas que `app/Http/Requests/StoreEnergyGenerationRequest.php`:

```php
'solar_farm_id' => ['required', 'integer', Rule::exists('solar_farms', 'id')->whereNull('deleted_at')],
'period' => ['required', 'regex:/^\d{4}-\d{2}$/', 'date_format:Y-m',
    Rule::unique('energy_generations', 'period')->where('solar_farm_id', $this->integer('solar_farm_id'))],
'record_date' => ['required', 'date'],
'estimated_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
'real_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
'notes' => ['nullable', 'string', 'max:500'],
```

No se puede reutilizar el `FormRequest` tal cual porque su `authorize()` depende de
`$this->user()` (sesión) — hay que crear uno nuevo (ver 3.4) con las mismas `rules()`.

### 3.3 Autenticación: middleware de API Key

Crear `app/Http/Middleware/AuthenticateMcpKey.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-MCP-Key');
        $expected = config('services.mcp.key');

        // OWASP A10: fallar cerrado. Si no hay llave configurada en el servidor,
        // NADIE pasa — nunca se debe interpretar "sin llave configurada" como "todo permitido".
        if (empty($expected) || empty($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
```

Registrar el middleware en `bootstrap/app.php` dentro de `withMiddleware()` con un alias
(revisá cómo está estructurado ese archivo ahora mismo — ya tiene `SecurityHeaders` y
`throttleApi()` configurados, no los borres):

```php
$middleware->alias(['mcp.key' => \App\Http\Middleware\AuthenticateMcpKey::class]);
```

Agregar en `config/services.php` (ya existe el archivo, solo agregar la clave `mcp`):

```php
'mcp' => [
    'key' => env('MCP_API_KEY'),
],
```

Y en `.env` / `.env.example` agregar (generar una llave larga y random, por ejemplo con
`php artisan tinker` → `Str::random(48)`, o `openssl rand -hex 32`):

```
MCP_API_KEY=
```

**Nunca** hardcodees la llave en el código ni la subas a git en texto plano fuera de `.env`.

### 3.4 El FormRequest nuevo

Crear `app/Http/Requests/McpStoreGenerationRequest.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class McpStoreGenerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real ya la hizo el middleware mcp.key.
    }

    public function rules(): array
    {
        return [
            'solar_farm_id' => ['required', 'integer', Rule::exists('solar_farms', 'id')->whereNull('deleted_at')],
            'period' => ['required', 'regex:/^\d{4}-\d{2}$/', 'date_format:Y-m',
                Rule::unique('energy_generations', 'period')->where('solar_farm_id', $this->integer('solar_farm_id'))],
            'record_date' => ['required', 'date'],
            'estimated_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'real_kwh' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
```

### 3.5 Usuario "sistema" para atribuir la acción

`EnergyGenerationService::store()` pide un `$userId` (para la columna `created_by`), y
`BackendAuditService::record()` (ver `app/Services/BackendAuditService.php`) también necesita un
usuario para la bitácora de auditoría — **pero ese servicio lee `$request->user()`, que va a ser
`null`** en una petición autenticada por API key (no hay sesión). Dos cambios necesarios:

**a) Sembrar un usuario "sistema"** — agregar a `database/seeders/SolarDemoSeeder.php` (o crear
un seeder nuevo `McpSystemUserSeeder`) algo así:

```php
User::query()->firstOrCreate(
    ['email' => 'mcp-agent@kinsolar.internal'],
    [
        'name' => 'Agente MCP (K\'in Solar)',
        'password' => Hash::make(Str::random(40)), // nunca se usa para login humano
        'role' => 'operador', // mismo nivel que Gate::define('manage-generations')
    ]
);
```

**b) Extender `BackendAuditService::record()`** para aceptar un `userId` explícito en vez de
depender siempre de `$request->user()`. Cambio mínimo, no rompe los usos existentes:

```php
public function record(Request $request, string $action, Model $model, ?int $userId = null): void
{
    AuditLog::query()->create([
        'user_id' => $userId ?? $request->user()?->getAuthIdentifier(),
        'action' => $action,
        'model_type' => $model::class,
        'model_id' => $model->getKey(),
        'ip_address' => $request->ip(),
        'user_agent' => mb_substr($request->userAgent() ?? '', 0, 500),
    ]);
}
```

### 3.6 El controlador

Crear `app/Http/Controllers/Api/McpGenerationController.php` (nota el subnamespace `Api`, es
nuevo — creá también la carpeta `app/Http/Controllers/Api/`):

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\McpStoreGenerationRequest;
use App\Models\SolarFarm;
use App\Models\User;
use App\Services\BackendAuditService;
use App\Services\EnergyGenerationService;
use Illuminate\Http\JsonResponse;

class McpGenerationController extends Controller
{
    public function __construct(
        private EnergyGenerationService $generations,
        private BackendAuditService $audit,
    ) {}

    public function store(McpStoreGenerationRequest $request): JsonResponse
    {
        $farm = SolarFarm::query()->findOrFail($request->integer('solar_farm_id'));
        $systemUserId = User::query()->where('email', 'mcp-agent@kinsolar.internal')->value('id');

        // OWASP A10: fallar cerrado si el usuario sistema no existe (seeder no corrido).
        if ($systemUserId === null) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario sistema del MCP no configurado. Ejecutar el seeder correspondiente.',
            ], 500);
        }

        $generation = $this->generations->store($farm, $request->validated(), $systemUserId);
        $this->audit->record($request, 'created', $generation, $systemUserId);
        if ($generation->generationAlert !== null) {
            $this->audit->record($request, 'created', $generation->generationAlert, $systemUserId);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $generation->id,
                'solar_farm_id' => $generation->solar_farm_id,
                'period' => $generation->period,
                'estimated_kwh' => (float) $generation->estimated_kwh,
                'real_kwh' => (float) $generation->real_kwh,
                'co2_kg' => (float) $generation->co2_kg,
                'alert_created' => $generation->generationAlert !== null,
                'deviation_percentage' => $generation->generationAlert?->deviation_percentage,
            ],
        ], 201);
    }
}
```

### 3.7 La ruta

Agregar en `routes/api.php`, **fuera** del `Route::prefix('v1')->group(...)` de solo lectura que
ya existe (para que sea evidente en el código que esta ruta tiene un contrato de auth distinto),
o dentro del mismo grupo pero con su propio middleware — cualquiera de las dos formas es válida,
elegí la que quede más legible una vez que veas el archivo real. Ejemplo:

```php
use App\Http\Controllers\Api\McpGenerationController;

Route::prefix('v1')->name('api.v1.')->middleware('mcp.key')->group(function () {
    Route::post('/generations', [McpGenerationController::class, 'store'])
        ->middleware('throttle:10,1') // mas estricto que el limitador global, es escritura
        ->name('mcp.generations.store');
});
```

**Importante:** verificá que este middleware `mcp.key` NO quede aplicado por accidente a las
rutas de solo lectura que ya existen en `routes/api.php` (`/statistics`, `/departments`, `/farms`,
etc.) — esas deben seguir siendo públicas sin llave, tal como están hoy. Si hace falta, separá los
grupos en vez de anidarlos.

### 3.8 Verificación de la Parte A antes de seguir

```bash
# Con el servidor local corriendo y MCP_API_KEY=abc123 en el .env:

# Debe fallar con 401 (sin llave):
curl -X POST http://localhost:8000/api/v1/generations -H "Content-Type: application/json" -d '{}'

# Debe fallar con 422 (llave correcta, datos invalidos):
curl -X POST http://localhost:8000/api/v1/generations \
  -H "X-MCP-Key: abc123" -H "Content-Type: application/json" -d '{}'

# Debe funcionar con 201 (ajustar solar_farm_id y period a datos reales que existan):
curl -X POST http://localhost:8000/api/v1/generations \
  -H "X-MCP-Key: abc123" -H "Content-Type: application/json" \
  -d '{"solar_farm_id":1,"period":"2026-12","record_date":"2026-12-31","estimated_kwh":50000,"real_kwh":48000}'

# Confirmar que aparece en el dashboard/listado normal del sistema (via sesion):
# GET /generations en el navegador, logueado como admin.
```

Correr también la suite completa (`php artisan test`) y agregar un test nuevo en
`tests/Feature/` para este endpoint (mínimo: 401 sin llave, 422 con datos inválidos, 201 con
datos válidos + verificar que el registro exista en la tabla).

---

## 4. Parte B — Servidor MCP propio (Node.js)

### 4.1 Ubicación y estructura

Crear una carpeta **nueva y separada** en la raíz del repo (no toca el proyecto Laravel):

```
mcp-server/
├── package.json
├── index.js
├── .env.example
└── README.md
```

### 4.2 Dependencia

Usar el SDK oficial de Anthropic para MCP:

```bash
cd mcp-server
npm init -y
npm install @modelcontextprotocol/sdk
```

### 4.3 `mcp-server/index.js`

```javascript
#!/usr/bin/env node
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod"; // viene como dependencia del SDK

const BASE_URL = process.env.KIN_SOLAR_API_URL ?? "https://kin-solar-guatemala.duckdns.org/api/v1";
const MCP_KEY = process.env.KIN_SOLAR_MCP_KEY ?? "";

const server = new McpServer({
    name: "kin-solar-guatemala",
    version: "1.0.0",
});

server.registerTool(
    "kin_solar_statistics",
    {
        title: "Estadísticas nacionales de K'in Solar",
        description: "Consulta el consolidado nacional: granjas, paneles, capacidad, kWh generados, CO2 evitado y alertas activas.",
        inputSchema: {},
    },
    async () => {
        const res = await fetch(`${BASE_URL}/statistics`);
        const json = await res.json();
        return { content: [{ type: "text", text: JSON.stringify(json.data, null, 2) }] };
    }
);

server.registerTool(
    "kin_solar_list_farms",
    {
        title: "Listar granjas solares",
        description: "Lista todas las granjas solares registradas, con ubicación, capacidad y familias beneficiadas.",
        inputSchema: {},
    },
    async () => {
        const res = await fetch(`${BASE_URL}/farms`);
        const json = await res.json();
        return { content: [{ type: "text", text: JSON.stringify(json.data, null, 2) }] };
    }
);

server.registerTool(
    "kin_solar_register_generation",
    {
        title: "Registrar medición de generación solar",
        description:
            "Registra una medición REAL de generación de energía solar para una granja y período " +
            "específicos. Esto crea un cambio permanente en la base de datos de producción. " +
            "El sistema calcula automáticamente el CO2 evitado y genera una alerta si la generación " +
            "real es 20% o más inferior a la estimada.",
        inputSchema: {
            solar_farm_id: z.number().int().describe("ID numérico de la granja solar (consultar con kin_solar_list_farms)"),
            period: z.string().regex(/^\d{4}-\d{2}$/).describe("Período en formato AAAA-MM, ej. 2026-09"),
            record_date: z.string().describe("Fecha del registro en formato AAAA-MM-DD"),
            estimated_kwh: z.number().min(0).describe("Generación estimada en kWh para el período"),
            real_kwh: z.number().min(0).describe("Generación real medida en kWh para el período"),
            notes: z.string().max(500).optional().describe("Notas opcionales sobre la medición"),
        },
    },
    async (args) => {
        const res = await fetch(`${BASE_URL}/generations`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-MCP-Key": MCP_KEY,
            },
            body: JSON.stringify(args),
        });
        const json = await res.json();
        if (!res.ok) {
            return {
                content: [{ type: "text", text: `Error ${res.status}: ${JSON.stringify(json)}` }],
                isError: true,
            };
        }
        return { content: [{ type: "text", text: JSON.stringify(json.data, null, 2) }] };
    }
);

const transport = new StdioServerTransport();
await server.connect(transport);
```

### 4.4 `mcp-server/.env.example`

```
KIN_SOLAR_API_URL=https://kin-solar-guatemala.duckdns.org/api/v1
KIN_SOLAR_MCP_KEY=
```

(La llave real va en un `.env` que **no** se sube a git — agregar `mcp-server/.env` a
`.gitignore` si no está ya cubierto por un patrón `.env` genérico.)

### 4.5 Cómo se conecta esto a un cliente de IA para la demo

Para **Claude Desktop**, el archivo de configuración
(`%APPDATA%\Claude\claude_desktop_config.json` en Windows) necesita una entrada así:

```json
{
  "mcpServers": {
    "kin-solar": {
      "command": "node",
      "args": ["C:\\ruta\\completa\\al\\repo\\mcp-server\\index.js"],
      "env": {
        "KIN_SOLAR_API_URL": "https://kin-solar-guatemala.duckdns.org/api/v1",
        "KIN_SOLAR_MCP_KEY": "la-misma-llave-que-esta-en-MCP_API_KEY-del-.env-de-laravel"
      }
    }
  }
}
```

Documentar el equivalente para **Claude Code** (`.mcp.json` a nivel de proyecto o usuario, mismo
formato de `mcpServers`) y probar ambos si el tiempo alcanza.

### 4.6 Guion de la demo en vivo

1. Abrir Claude Desktop (o Claude Code) ya con el servidor conectado.
2. Preguntarle: *"¿Cuántas granjas solares tiene K'in Solar y cuál es la generación acumulada?"*
   → debe usar `kin_solar_statistics` / `kin_solar_list_farms` y responder con datos reales.
3. Pedirle: *"Registra una medición para la granja [nombre real] del período [mes actual]: estimado
   X kWh, real Y kWh"* → debe llamar a `kin_solar_register_generation`.
4. Cambiar a la pestaña del navegador con el dashboard de K'in Solar (sesión ya iniciada), refrescar
   `/generations`, y mostrar que el registro nuevo aparece — con su alerta si el déficit fue ≥20%.

---

## 5. Checklist de seguridad OWASP Top 10:2025 para esta tarea

(Ver `docs/02-SEGURIDAD-OWASP-2025.md` para el detalle completo de cada categoría.)

- [ ] **A01** — El endpoint MCP no usa el sistema de roles humano; su única puerta es la llave.
      Confirmar que `mcp.key` está aplicado y que las rutas de solo lectura existentes no quedaron
      protegidas por error (romper la API pública sería una regresión grave de RF-16).
- [ ] **A02** — `MCP_API_KEY` solo en `.env`, nunca en el código ni en el repo del `mcp-server/`.
- [ ] **A04** — Comparar la llave con `hash_equals()`, nunca con `===` (evita timing attacks).
- [ ] **A05** — Los datos siguen validándose con `FormRequest`, igual que el resto del sistema.
- [ ] **A06** — Throttle específico y más estricto (`throttle:10,1`) en la ruta de escritura.
- [ ] **A09** — Cada registro creado vía MCP debe quedar en `audit_logs`, atribuido al usuario
      sistema `mcp-agent@kinsolar.internal`, no a `null` ni a un admin real.
- [ ] **A10** — Fallar cerrado en ambos puntos nuevos: llave ausente/incorrecta → 401; usuario
      sistema no sembrado → 500 explícito, nunca continuar como si hubiera funcionado.

---

## 6. Entregables esperados de esta tarea

- [ ] Migración/seeder del usuario sistema (o ampliación de `SolarDemoSeeder`)
- [ ] `AuthenticateMcpKey` middleware + registro en `bootstrap/app.php`
- [ ] `McpStoreGenerationRequest`
- [ ] `App\Http\Controllers\Api\McpGenerationController`
- [ ] Ruta nueva en `routes/api.php`, verificada de no afectar las rutas públicas existentes
- [ ] `BackendAuditService::record()` extendido con `?int $userId = null`
- [ ] `MCP_API_KEY` en `.env.example` (vacío) y en `.env` real (con valor, no versionado)
- [ ] Carpeta `mcp-server/` completa y funcional (`package.json`, `index.js`, `.env.example`, `README.md`)
- [ ] Al menos un test automatizado nuevo para el endpoint (`tests/Feature/`)
- [ ] Verificación manual con `curl` (los 3 casos de la sección 3.8)
- [ ] Verificación end-to-end real: usar el servidor MCP desde un cliente de IA, registrar una
      medición, y confirmarla en el dashboard del sistema
- [ ] Entrada nueva en `docs/04-BITACORA-PROMPTS.md` documentando el trabajo (prompt usado,
      qué generó la IA, qué corrigió el humano) — es evidencia para el 20% de "Uso de IA"
- [ ] Todo esto en una rama nueva (`feat/<agente>/mcp-server-propio` o similar) y un Pull Request
      con la plantilla completa de `.github/pull_request_template.md`, siguiendo
      `docs/01-REGLAS-DE-TRABAJO.md`

---

## 7. Fuera de alcance (no hacer en esta tarea)

- No modificar el flujo de autenticación normal del sistema (login de usuarios humanos)
- No exponer más escritura que "registrar generación" — no agregar edición/borrado vía MCP
- No tocar el frontend Blade — el dashboard ya muestra los datos nuevos automáticamente porque
  lee de la misma tabla `energy_generations`
- Documentación completa y diapositivas de la presentación quedan fuera — eso lo maneja el equipo
  por separado, al final
