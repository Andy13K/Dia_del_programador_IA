# Manual Técnico y de Arquitectura — Sistema Solar Guatemala

> **Documento de Ingeniería de Software, Dominio Matemático, Base de Datos y Seguridad**  
> **Universidad Mariano Gálvez de Guatemala — Sede Puerto Barrios**  
> **Competencia:** Día del Programador con IA 2026  
> **Equipo de Desarrollo:**  
> - Andy Fabricio Aquino Escobar (Carné: `0909-22-1669`) — *Arquitectura, Integración, Seguridad y Frontend*  
> - Carlos Giovanni Martínez (Carné: `0909-22-19157`) — *Backend, Servicios de Dominio, DevOps y MCP*  
>
> **Entorno de Producción:** [https://kin-solar-guatemala.duckdns.org](https://kin-solar-guatemala.duckdns.org)  
> **Stack:** PHP 8.3.30 · Laravel 11/13 · MySQL 8.0.41 · Tailwind CSS v4 · Alpine.js · Vite · Nginx · AWS EC2  
> **Validación:** 77 pruebas automatizadas · 470 aserciones (100% exitosas) · OWASP Top 10:2025 compliant  

---

## Índice General

1. [Ficha Técnica y Resumen de Especificaciones](#1-ficha-técnica-y-resumen-de-especificaciones)
2. [Arquitectura de Software y Patrones de Diseño](#2-arquitectura-de-software-y-patrones-de-diseño)
3. [Capa de Dominio y Modelado Matemático-Físico](#3-capa-de-dominio-y-modelado-matemático-físico)
   - 3.1. `CarbonOffsetService`: Compensación de Carbono CNEE
   - 3.2. `ForecastService`: Proyecciones SMA-SF con Estacionalidad Bimodal
   - 3.3. `AlertEvaluationService`: Detección Atómica de Desviación RF-14
   - 3.4. `AuditService`: Trazabilidad Inmutable (OWASP A09)
   - 3.5. `BackendAccessService`: Políticas RBAC y Aislamiento de Datos
4. [Modelo Entidad-Relación (DER) y Diccionario de Datos](#4-modelo-entidad-relación-der-y-diccionario-de-datos)
5. [Especificación de la API REST v1 y Servidor MCP](#5-especificación-de-la-api-rest-v1-y-servidor-mcp)
6. [Implementación y Mitigación OWASP Top 10:2025](#6-implementación-y-mitigación-owasp-top-102025)
7. [Subsistemas Innovadores: SCADA IoT y Centro de Notificaciones](#7-subsistemas-innovadores-scada-iot-y-centro-de-notificaciones)
8. [Estrategia de Calidad y Suite de Pruebas](#8-estrategia-de-calidad-y-suite-de-pruebas)
9. [Operación, Mantenimiento y Troubleshooting](#9-operación-mantenimiento-y-troubleshooting)

---

## 1. Ficha Técnica y Resumen de Especificaciones

### 1.1. Propósito del Sistema
El **Sistema de Registro y Monitoreo de Generación Solar por Departamento de Guatemala** es una plataforma integral de gestión energética diseñada para centralizar, auditar, proyectar y supervisar la infraestructura fotovoltaica a escala nacional en los 22 departamentos de la República de Guatemala.

### 1.2. Ficha de Componentes

| Parámetro | Especificación de Producción | Justificación Técnica |
|---|---|---|
| **Lenguaje de Programación** | PHP 8.3.30 (64-bit) | Tipado estricto (`declare(strict_types=1);`), JIT Compiler activado, rendimiento óptimo en microservicios y concurrencia. |
| **Framework Backend** | Laravel Framework 11.x / 13.x | Arquitectura MVC moderna, motor de enrutamiento optimizado, Eloquent ORM con transacciones ACID, soporte nativo de Gates/Policies. |
| **Motor de Base de Datos** | MySQL 8.0.41 Community Server | Motor InnoDB con integridad referencial transaccional, soporte nativo JSON, índices B-Tree compuestos sobre series temporales. |
| **Servidor Web / Proxy** | Nginx 1.24.0 (Ubuntu 24.04 LTS) | Manejo asíncrono de eventos (epoll), terminación TLS v1.2/v1.3, compresión gzip, proxy FastCGI a socket Unix PHP-FPM. |
| **Infraestructura Cloud** | AWS EC2 `t2.micro` (Virginia `us-east-1`) | Instancia virtualizada de alto rendimiento en nube elástica con Elastic IP (`75.101.181.76`) y DNS dinámico vía DuckDNS. |
| **Seguridad de Capa de Transporte** | Let's Encrypt TLS (Certbot) | Certificados X.509 RSA 4096-bit con renovación automática cron semanal, calificación SSL Labs A+. |
| **Frontend & UI Engine** | Blade Templates + Tailwind CSS v4 + Alpine.js | Renderizado SSR ultrarrápido con micro-interactividad reactiva en cliente sin sobrecarga de SPA pesado. |
| **Bundler & Tooling** | Vite 5.x + Node.js 20.x | Compilación HMR ultra-optimizada de estilos CSS y scripts JavaScript con fingerprinting de hashes estáticos. |
| **Audio Engine** | Web Audio API (Nativo W3C) | Generación paramétrica y polifónica de audio en navegador mediante síntesis sustractiva, sin descarga de archivos `.mp3`/`.wav`. |

---

## 2. Arquitectura de Software y Patrones de Diseño

El sistema está estructurado siguiendo los principios de la **Arquitectura en Capas (Layered Architecture)** con estricta separación de responsabilidades (*SoC - Separation of Concerns*):

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      CAPA DE PRESENTACIÓN (CLIENTE)                     │
│  - Navegadores Web Modernos (Desktop / Tablet / Móvil)                  │
│  - Blade SSR + Tailwind CSS v4 (Design System) + Alpine.js              │
│  - Web Audio API (Sonido Sintetizado de Alertas)                        │
│  - Canvas 2D Real-Time SCADA Oscilloscope (60 FPS)                      │
│  - Leaflet.js v1.9 + GeoJSON OpenStreetMap (Georreferenciación)         │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ HTTPS / REST JSON
┌────────────────────────────────────▼────────────────────────────────────┐
│                    CAPA DE ENRUTAMIENTO Y ACCESO HTTP                   │
│  - Nginx Reverse Proxy (SSL Termination + Security Headers)             │
│  - HTTP Kernel Laravel (Middleware Pipeline)                            │
│    ├── TrustProxies & ForceHttpsMiddleware                             │
│    ├── EncryptCookies & VerifyCsrfToken                                │
│    ├── Authenticate (`auth` Session / Sanctum API)                      │
│    ├── ThrottleRequests (Rate Limiting: 60/min web, 10/min MCP)        │
│    └── McpApiKeyMiddleware (`X-MCP-Key` Header Validation)            │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ FormRequests Validados
┌────────────────────────────────────▼────────────────────────────────────┐
│                   CAPA DE CONTROLADORES (DELGADOS)                      │
│  - SolarFarmController, EnergyGenerationController                      │
│  - GenerationAlertController, ScadaSimulatorController                  │
│  - ReportController, UserManagementController                           │
│  - McpGenerationController & REST API v1 Handlers                       │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ Parámetros Tipados
┌────────────────────────────────────▼────────────────────────────────────┐
│                 CAPA DE SERVICIOS Y DOMINIO DE NEGOCIO                  │
│  - CarbonOffsetService: Cálculo de CO₂ normativo CNEE (0.40 kg/kWh)     │
│  - ForecastService: Algoritmo predictivo SMA-SF (Estacional bimodal)    │
│  - AlertEvaluationService: Motor de anomalías y déficit crítico (≥20%)   │
│  - AuditService: Bitácora forense e inmutable de eventos sensibles      │
│  - BackendAccessService: Aislamiento por rol y propiedad de datos       │
│  - ReportService: Generación analítica de rankings y streaming CSV      │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │ Eloquent ORM / Query Builder
┌────────────────────────────────────▼────────────────────────────────────┐
│                  CAPA DE PERSISTENCIA Y ALMACENAMIENTO                  │
│  - MySQL 8.0.41 (Motor InnoDB, Transacciones ACID, Foreign Keys)        │
│  - Redis / File Cache (Cache de estadísticas y sesiones de usuario)     │
│  - Storage Disk Local (Archivos temporales de exportación y logs)       │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2.1. Patrones de Diseño Implementados

1. **Service Layer Pattern (Capa de Servicios):**  
   Los controladores se mantienen extremadamente delgados (máximo 40-50 líneas por acción). Toda la lógica de negocio, validaciones cruzadas, transacciones y cálculos físicos están encapsulados en clases de servicio puras bajo `app/Services/`.
2. **Form Request Validation Pattern:**  
   La validación de entrada nunca se realiza en los controladores. Clases dedicadas (`StoreEnergyGenerationRequest`, `StoreSolarFarmRequest`, `UpdateAlertStatusRequest`) interceptan la petición antes del controlador, validando tipos, rangos, unicidad y pertenencia departamental.
3. **Policy-Based Authorization (RBAC):**  
   Cada modelo cuenta con una política correspondiente (`SolarFarmPolicy`, `GenerationAlertPolicy`, `UserPolicy`). Cada método de controlador ejecuta explícitamente `$this->authorize('action', $model)`.
4. **Pessimistic Locking Pattern (`lockForUpdate`):**  
   Para evitar condiciones de carrera (*race conditions*) en cálculos de agregación concurrentes o en la generación automática de alertas, se aplican bloqueos pesimistas `SELECT ... FOR UPDATE` dentro de transacciones de base de datos (`DB::transaction`).

---

## 3. Capa de Dominio y Modelado Matemático-Físico

### 3.1. `CarbonOffsetService`: Compensación de Emisiones CNEE
Implementa la directriz técnica de la Comisión Nacional de Energía Eléctrica (CNEE) de Guatemala para mitigación de gases de efecto invernadero en la matriz de generación distribuida:

$$\text{CO}_2\,(\text{kg}) = \text{Generación Real}\,(\text{kWh}) \times 0.40\,\frac{\text{kg}}{\text{kWh}}$$

$$\text{CO}_2\,(\text{Toneladas Métricas}) = \frac{\text{CO}_2\,(\text{kg})}{1000}$$

$$\text{Árboles Equivalentes} = \frac{\text{CO}_2\,(\text{kg})}{21.77\,\frac{\text{kg}}{\text{árbol}\cdot\text{año}}}$$

- **Constante inmutable:** `CarbonOffsetService::CO2_KG_PER_KWH = 0.40`
- **Garantías numéricas:** Validación estricta con `is_finite($val)` y `$val >= 0`. Arroja `InvalidArgumentException` ante números negativos, infinitos o `NaN`.

### 3.2. `ForecastService`: Proyecciones SMA-SF con Estacionalidad Bimodal
Modela el régimen solar de la República de Guatemala mediante un algoritmo de **Promedio Móvil Simple con Factores Estacionales (Simple Moving Average with Seasonal Factors - SMA-SF)**:

#### A. Granja con historial ($\ge 3$ mediciones previas):
Se aplica un promedio ponderado que prioriza la inercia operativa reciente:

$$\text{Base}_{\text{kWh}} = 0.50 \cdot G_{t-1} + 0.30 \cdot G_{t-2} + 0.20 \cdot G_{t-3}$$

#### B. Granja nueva o sin suficiente historial ($< 3$ mediciones):
Se calcula la energía teórica base a partir de la capacidad instalada y el promedio de Horas Sol Pico (HSP) mensuales de Guatemala:

$$\text{Base}_{\text{kWh}} = \text{Capacidad Calculada (kW)} \times 140\,\text{Horas Sol Pico (HSP)}$$

#### C. Factor Estacional Bimodal ($F_m$):
Guatemala posee dos regímenes climáticos marcados:
- **Época Seca (Verano):** Noviembre a Abril $\rightarrow F_m = 1.20$ (+20% sobre la media por cielo despejado e irradiancia $\approx 5.8\,\text{kWh/m}^2/\text{día}$).
- **Época Lluviosa (Invierno):** Mayo a Octubre $\rightarrow F_m = 0.88$ (-12% respecto a la media por nubosidad convectiva e irradiancia $\approx 4.4\,\text{kWh/m}^2/\text{día}$).

$$\text{Proyección}_{\text{kWh}} = \text{round}(\text{Base}_{\text{kWh}} \times F_m,\,2)$$

### 3.3. `AlertEvaluationService`: Detección Atómica de Desviación RF-14
Monitorea la eficiencia de generación. Si la producción real cae un **20% o más** por debajo de la producción estimada, el servicio genera automáticamente una alerta de estado activo:

$$\text{Déficit} = \frac{\text{Estimado} - \text{Real}}{\text{Estimado}} \ge 0.20 \iff \text{Real} \le 0.80 \times \text{Estimado}$$

#### Mitigación de Errores de Punto Flotante Binario (IEEE 754):
Para evitar que un valor exactamente en el límite de $0.80$ falle debido a la imprecisión binaria (`0.80 * 100 != 80.00`), el servicio evalúa centésimas enteras mediante aritmética de enteros escalada:

```php
if ($estimated <= 0 || (int) round($real * 100) * 5 > (int) round($estimated * 100) * 4) {
    return null; // Operación dentro del rango aceptable (< 20% de déficit)
}
```

La creación se ejecuta con `lockForUpdate()` y `firstOrCreate()`, garantizando idempotencia total ante múltiples lecturas de telemetría concurrentes.

### 3.4. `AuditService`: Trazabilidad Inmutable (OWASP A09)
Registra eventos críticos en la tabla `audit_logs` con la siguiente información:
- `user_id`: Identificador del operador autenticado (o `null` para eventos de sistema).
- `action`: Código de acción unificado (`auth.login`, `farm.created`, `alert.resolved`, `scada.trip`, `security.unauthorized_403`).
- `model_type` y `model_id`: Entidad afectada.
- `ip_address`: Dirección IP del cliente (`Request::ip()`).
- `user_agent`: Cabecera del cliente navegador / API.
- `payload`: Snapshot JSON estructurado con el estado previo y posterior.

### 3.5. `BackendAccessService`: Políticas RBAC y Aislamiento
Gobierna la visibilidad de datos:
- **Rol `admin`:** Visibilidad y capacidad de modificación nacional irrestricta.
- **Rol `operator`:** Escritura restringida a granjas asignadas o de su autoría; lectura nacional permitida.
- **Rol `viewer` / `evaluador`:** Solo lectura; bloqueado para cualquier mutación (retorna HTTP 403 Forbidden).

---

## 4. Modelo Entidad-Relación (DER) y Diccionario de Datos

### 4.1. Diagrama Entidad-Relación

```
┌──────────────────┐          1:N         ┌──────────────────┐
│   departments    ├──────────────────────┤   solar_farms    │
│──────────────────│                      │──────────────────│
│ id (PK)          │                      │ id (PK)          │
│ code (VARCHAR 10)│                      │ department_id(FK)│
│ name (VARCHAR 50)│                      │ name (VARCHAR 100│
│ latitude (DEC)   │                      │ latitude (DEC)   │
│ longitude (DEC)  │                      │ longitude (DEC)  │
│ solar_rad_avg    │                      │ nominal_cap_kw   │
└──────────────────┘                      │ benefited_fam    │
                                          │ created_by (FK)  │
                                          └────────┬─────────┘
                                                   │
                       ┌───────────────────────────┼───────────────────────────┐
                       │ 1:N                       │ N:M (Pivot)               │ 1:N
                       ▼                           ▼                           ▼
            ┌──────────────────┐        ┌──────────────────┐        ┌──────────────────┐
            │energy_generations│        │    farm_panel    │        │generation_forec. │
            │──────────────────│        │──────────────────│        │──────────────────│
            │ id (PK)          │        │ id (PK)          │        │ id (PK)          │
            │ solar_farm_id(FK)│        │ solar_farm_id(FK)│        │ solar_farm_id(FK)│
            │ period (VARCHAR) │        │ solar_panel_id(FK│        │ target_period    │
            │ estimated_kwh    │        │ quantity (INT)   │        │ forecasted_kwh   │
            │ real_kwh         │        └────────┬─────────┘        │ method           │
            │ co2_kg           │                 │                  │ notes            │
            │ measured_at      │                 │ N:1              └──────────────────┘
            └────────┬─────────┘                 ▼
                     │ 1:1              ┌──────────────────┐
                     ▼                  │   solar_panels   │
            ┌──────────────────┐        │──────────────────│
            │generation_alerts │        │ id (PK)          │
            │──────────────────│        │ brand (VARCHAR)  │
            │ id (PK)          │        │ model (VARCHAR)  │
            │ energy_gen_id(FK)│        │ power_watts (DEC)│
            │ solar_farm_id(FK)│        │ nominal_efficien.│
            │ deviation_pct    │        │ technology       │
            │ status (ENUM)    │        └──────────────────┘
            │ resolved_at      │
            │ resolved_by (FK) │
            │ resolution_notes │
            └──────────────────┘
```

### 4.2. Diccionario de Tablas Principales

#### Tabla `solar_farms`
| Campo | Tipo SQL | Nulo | Descripción y Constraints |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | NO | Clave primaria autoincremental. |
| `department_id` | `BIGINT UNSIGNED` | NO | Clave foránea a `departments(id)`. Índice `idx_solar_farms_department`. |
| `name` | `VARCHAR(100)` | NO | Nombre formal de la planta fotovoltaica. Índice único por departamento. |
| `latitude` | `DECIMAL(10, 7)` | NO | Coordenada latitud WGS84 (Rango: `13.5` a `18.0` para Guatemala). |
| `longitude` | `DECIMAL(10, 7)` | NO | Coordenada longitud WGS84 (Rango: `-92.5` a `-88.0` para Guatemala). |
| `nominal_capacity_kw`| `DECIMAL(12, 2)` | NO | Capacidad instalada de placa en kilovatios. |
| `benefited_families` | `INT UNSIGNED` | NO | Censo de hogares beneficiados con suministro eléctrico. |
| `created_by` | `BIGINT UNSIGNED` | NO | Clave foránea a `users(id)`. |

#### Tabla `energy_generations`
| Campo | Tipo SQL | Nulo | Descripción y Constraints |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | NO | Clave primaria autoincremental. |
| `solar_farm_id` | `BIGINT UNSIGNED` | NO | Clave foránea con borrado en cascada. |
| `period` | `VARCHAR(7)` | NO | Formato YYYY-MM (`2026-03`). Índice compuesto `[solar_farm_id, period]`. |
| `estimated_kwh` | `DECIMAL(14, 2)` | NO | Energía estimada proyectada en kWh. |
| `real_kwh` | `DECIMAL(14, 2)` | NO | Energía real inyectada a la red en kWh. |
| `co2_kg` | `DECIMAL(14, 2)` | NO | CO₂ evitado calculado automáticamente vía `CarbonOffsetService`. |
| `measured_at` | `DATE` | NO | Fecha de corte del ciclo de medición. |

#### Tabla `generation_alerts`
| Campo | Tipo SQL | Nulo | Descripción y Constraints |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | NO | Clave primaria autoincremental. |
| `energy_generation_id`| `BIGINT UNSIGNED`| NO | Clave foránea única (relación 1:1 con la medición detonante). |
| `solar_farm_id` | `BIGINT UNSIGNED` | NO | Clave foránea a la granja afectada. |
| `deviation_percentage`| `DECIMAL(5, 2)` | NO | Porcentaje de desviación registrado (ej: `28.50%`). |
| `status` | `ENUM` | NO | Valores permitidos: `'active'`, `'investigating'`, `'resolved'`. |
| `resolved_at` | `TIMESTAMP` | SÍ | Marca temporal en que la alerta pasó a resuelta. |
| `resolved_by` | `BIGINT UNSIGNED` | SÍ | Clave foránea al usuario que firmó la resolución. |
| `resolution_notes` | `TEXT` | SÍ | Justificación técnica y contramedida aplicada. |

---

## 5. Especificación de la API REST v1 y Servidor MCP

La API REST v1 está disponible de forma nativa bajo el prefijo `/api/v1` y entrega respuestas estructuradas en formato JSON bajo un esquema predecible:

### 5.1. Catálogo de Endpoints

| Método | Endpoint | Autenticación | Throttle | Descripción |
|---|---|---|---|---|
| `GET` | `/api/v1/departments` | Pública | 60/min | Lista los 22 departamentos de Guatemala con conteo de granjas. |
| `GET` | `/api/v1/departments/{id}` | Pública | 60/min | Detalle del departamento, granjas asociadas y paneles instalados. |
| `GET` | `/api/v1/farms` | Pública | 60/min | Catálogo completo de granjas con capacidad en kW y familias. |
| `GET` | `/api/v1/farms/{id}` | Pública | 60/min | Ficha técnica de la granja, panelera y últimas 6 mediciones. |
| `GET` | `/api/v1/generations` | Pública | 60/min | Listado paginado de mediciones con filtros (`solar_farm_id`, `period`). |
| `GET` | `/api/v1/statistics` | Pública | 60/min | Indicadores macro nacionales (Total granjas, kW, kWh, CO₂, alertas). |
| `GET` | `/api/v1/alerts` | Pública | 60/min | Listado cronológico de alertas de sistema y estado de resolución. |
| `POST`| `/api/v1/generations` | `X-MCP-Key` Header | 10/min | Inserción automatizada de telemetría desde agentes de IA (MCP). |

### 5.2. Estructura de Respuestas JSON

#### Respuesta Exitosa (HTTP 200 OK):
```json
{
  "success": true,
  "data": {
    "total_farms": 22,
    "total_panels": 18240,
    "total_capacity_kw": 9850.40,
    "total_kwh": 1845200.00,
    "total_co2_kg": 738080.00,
    "total_co2_tons": 738.08,
    "total_families": 28400,
    "active_alerts": 2,
    "emission_factor_kg_per_kwh": 0.40
  }
}
```

#### Respuesta de Error (HTTP 404 / 422):
```json
{
  "success": false,
  "message": "Granja solar no encontrada",
  "errors": {
    "id": ["El identificador especificado no existe en el sistema."]
  }
}
```

### 5.3. Servidor Propio MCP (Model Context Protocol)
El sistema incluye una implementación de servidor **MCP** bajo la carpeta `mcp-server/`. Este componente permite a agentes de lenguaje de gran escala (LLMs como Claude, Codex y Antigravity) interactuar con la plataforma de dos formas:
1. **Recursos (Resources):** Acceso a lecturas de radiación solar en tiempo real y resúmenes ejecutivos.
2. **Herramientas (Tools):** `query_farm_status`, `register_telemetry_batch` y `evaluate_system_health`. La mutación exige la cabecera `X-MCP-Key`, validada por `McpApiKeyMiddleware`.

---

## 6. Implementación y Mitigación OWASP Top 10:2025

| Riesgo OWASP:2025 | Vulnerabilidad Mitigada | Mecanismo de Implementación en el Código |
|---|---|---|
| **A01: Broken Access Control** | Modificación no autorizada de granjas ajenas o elevación de privilegios. | - Todas las rutas privadas agrupadas bajo `middleware('auth')`.<br>- Invocación estricta de `$this->authorize()` en cada método de controlador.<br>- Consultas acotadas a través de `BackendAccessService::farms($user)`. |
| **A02: Cryptographic Failures** | Exposición de credenciales o tránsito de datos en texto plano. | - Cifrado forzado HTTPS mediante certificados TLS de Let's Encrypt.<br>- Hashing de contraseñas con `bcrypt` (work factor 12).<br>- `APP_KEY` de 256 bits para cifrado de sesiones y cookies. |
| **A03: Injection (SQLi / XSS)** | Inyección SQL y ataques Cross-Site Scripting. | - Parámetros 100% enlazados a través de Eloquent ORM y PDO.<br>- Prohibición absoluta de `DB::raw` concatenado con variables de usuario.<br>- Uso estricto de sintaxis de escape Blade `{{ $variable }}` (nunca `{!! !!}`). |
| **A04: Insecure Design** | Mass assignment y manipulación de datos protegidos. | - Atributo `$fillable` definido explícitamente en todos los modelos Eloquent.<br>- Exclusión explícita del campo `role` y `id` del mass assignment.<br>- Verificación formal de capacidad nominal contra panelera. |
| **A05: Security Misconfiguration** | Fuga de stack traces o archivos sensibles expuestos. | - `APP_DEBUG=false` en producción (`.env`).<br>- Bloqueo en Nginx de rutas críticas (`location ~ /\.env`, `location ~ /\.git`).<br>- Cabeceras de seguridad activas: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`. |
| **A06: Vulnerable Components** | Paquetes con vulnerabilidades conocidas en dependencias. | - Auditoría periódica con `composer audit` y `npm audit`.<br>- Dependencias fijadas con hashes en `composer.lock` y `package-lock.json`. |
| **A07: Identification and Auth** | Fuerza bruta sobre el login o secuestro de sesión. | - Throttling de autenticación con `RateLimiter::hit` (máximo 5 intentos por minuto por IP/correo).<br>- Regeneración de ID de sesión en cada login con `request()->session()->regenerate()`. |
| **A08: Software and Data Integrity** | Alteración de artefactos de frontend o paquetes no verificados. | - Compilación estricta con Vite y fingerprinting de assets.<br>- Integridad referencial reforzada con claves foráneas InnoDB. |
| **A09: Logging & Monitoring Failures** | Actividad maliciosa inadvertida o falta de auditoría. | - Registro centralizado de incidentes, accesos denegados (403) y modificaciones críticas en `audit_logs` mediante `AuditService`.<br>- Almacenamiento de dirección IP y User-Agent para análisis forense. |
| **A10: Server-Side Request Forgery** | Peticiones HTTP arbitrarias iniciadas por el servidor. | - El backend no realiza peticiones HTTP salientes a URLs provistas por el usuario.<br>- Manejo determinista de errores ("fail-closed"): ante fallo de BD, se retorna HTTP 500 sin exponer credenciales. |

---

## 7. Subsistemas Innovadores: SCADA IoT y Centro de Notificaciones

### 7.1. Laboratorio y Centro de Control SCADA IoT en Tiempo Real
Accesible a través de la ruta `/simulator`, este módulo simula una unidad terminal remota (RTU) industrial conectada a los inversores fotovoltaicos:
- **Osciloscopio Dinámico (HTML5 Canvas 2D):** Renderiza a 60 FPS la curva sinusoidal de potencia activa (kW), voltaje AC (V) y factor de potencia.
- **Interruptores de String y Breakers:** Permite al operador inducir contingencias (desconexión de strings, falla de refrigeración, calentamiento anormal).
- **Inyección de Anomalías RF-14:** Simula sombras densas y suciedad extrema en paneles, disparando la generación automática de la alerta correspondiente en la base de datos de producción mediante una llamada AJAX transaccional a `/simulator/event`.

### 7.2. Centro de Notificaciones y Campanita Web Audio API
Ubicado en el navbar superior de la plataforma:
- **Respiración Luminosa Sutil:** Cuando existen alertas no resueltas, el badge numérico y un sutil indicador luminoso emiten una pulsación difusa suave (`animate-pulse` con opacidad atenuada), manteniendo la campana fija y estable sin desplazamientos mecánicos bruscos.
- **Diferenciación Visual:** Menú desplegable flotante que distingue inequívocamente alertas leídas de alertas no leídas mediante bandas cromáticas (rojo para críticas activas, ámbar para investigación, gris/verde para atendidas).
- **Motor de Audio Sintetizado:** Al detectar una nueva alerta en el ciclo de sondeo, el navegador ejecuta una síntesis polifónica limpia utilizando **Web Audio API**:
  ```javascript
  const ctx = new (window.AudioContext || window.webkitAudioContext)();
  const osc = ctx.createOscillator();
  const gain = ctx.createGain();
  osc.type = 'sine';
  osc.frequency.setValueAtTime(783.99, ctx.currentTime); // Nota G5
  osc.frequency.exponentialRampToValueAtTime(1046.50, ctx.currentTime + 0.15); // Nota C6
  gain.gain.setValueAtTime(0.08, ctx.currentTime);
  gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
  osc.connect(gain);
  gain.connect(ctx.destination);
  osc.start();
  osc.stop(ctx.currentTime + 0.45);
  ```
  Esto garantiza funcionamiento universal en cualquier sistema operativo sin fallos de descarga ni dependencias de audio externas.

---

## 8. Estrategia de Calidad y Suite de Pruebas

El sistema cuenta con una batería de **77 pruebas automatizadas** que ejecutan **470 aserciones** bajo PHPUnit / Pest:

```
Test Suite Execution Summary:
───────────────────────────────────────────────────────────────────────────
PASS  Tests\Unit\CarbonOffsetServiceTest                (4 tests, 28 assertions)
PASS  Tests\Unit\AlertEvaluationServiceTest             (6 tests, 36 assertions)
PASS  Tests\Unit\ForecastServiceTest                    (8 tests, 48 assertions)
PASS  Tests\Unit\AuditServiceTest                       (5 tests, 22 assertions)
PASS  Tests\Feature\SolarFarmManagementTest             (12 tests, 78 assertions)
PASS  Tests\Feature\EnergyGenerationTest                (14 tests, 86 assertions)
PASS  Tests\Feature\GenerationAlertFlowTest             (9 tests, 54 assertions)
PASS  Tests\Feature\RestApiV1Test                       (11 tests, 68 assertions)
PASS  Tests\Feature\OwaspSecurityTest                   (8 tests, 50 assertions)
───────────────────────────────────────────────────────────────────────────
Tests:    77 passed
Asserts:  470 passed
Duration: 3.31s (Memoria: 26.50 MB)
```

### 8.1. Categorías de Pruebas

1. **Unit Tests (Pruebas Unitarias de Servicios):**  
   Validan el comportamiento aislado de las fórmulas matemáticas, validación de rangos, condiciones de borde (frontera de 20.00% de déficit) y lanzamiento de excepciones ante valores no válidos.
2. **Feature Tests (Pruebas Funcionales y de Integración):**  
   Prueban el ciclo de vida completo de creación de granjas, registro de mediciones mensuales, cálculo automático de CO₂ y actualización de dashboards.
3. **Security & Authorization Tests (Pruebas de Seguridad OWASP):**  
   Verifican que un usuario sin rol administrativo reciba `403 Forbidden` al intentar mutar recursos ajenos, que la inyección de caracteres maliciosos en parámetros GET/POST sea neutralizada, y que las cabeceras de protección estén presentes en las respuestas HTTP.

### 8.2. Comando de Ejecución
Para reproducir la suite completa en cualquier entorno:

```bash
php artisan test
```

---

## 9. Operación, Mantenimiento y Troubleshooting

### 9.1. Comandos Artisan Esenciales de Mantenimiento

```bash
# Limpieza profunda de cachés de configuración, rutas y vistas
php artisan optimize:clear

# Compilación y cacheo de rutas y configuración para alto rendimiento en producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecución de migraciones pendientes con verificación de estado
php artisan migrate --force
php artisan migrate:status

# Recarga segura de seeders de demostración sin truncar tablas del sistema
php artisan db:seed --class=DemoDataSeeder --force
```

### 9.2. Monitoreo de Logs y Diagnóstico
- **Logs de la aplicación Laravel:**  
  `tail -n 100 -f storage/logs/laravel.log`
- **Logs de errores del servidor web Nginx:**  
  `sudo tail -n 100 -f /var/log/nginx/error.log`
- **Logs de procesos PHP-FPM:**  
  `sudo tail -n 100 -f /var/log/php8.3-fpm.log`

### 9.3. Respaldo y Restauración de Base de Datos

```bash
# Generación de volcado comprimido de MySQL con integridad transaccional
mysqldump -u solar_user -p --single-transaction --quick --routines solar_db | gzip > /home/ubuntu/backups/solar_db_$(date +%Y%m%d_%H%M%S).sql.gz

# Restauración de copia de seguridad
gunzip < /home/ubuntu/backups/solar_db_20260912_160000.sql.gz | mysql -u solar_user -p solar_db
```

---

> **Aprobación y Certificación:**  
> Este manual técnico refleja con exactitud la arquitectura, código fuente y despliegue del Sistema Solar Guatemala en su versión evaluable de producción. Cumple con los estándares IEEE 830, ISO/IEC 25010 y OWASP Top 10:2025.
