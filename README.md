# Sistema de Registro y Monitoreo de Generación Solar por Departamento (Guatemala)

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)](https://mysql.com)
[![AWS EC2](https://img.shields.io/badge/Desplegado_en-AWS_EC2-FF9900?logo=amazonaws)](http://3.238.198.77)
[![OWASP Top 10:2025](https://img.shields.io/badge/OWASP-Top_10:2025-000000)](docs/02-SEGURIDAD-OWASP-2025.md)

**Equipo:** Andy Aquino · Carlos — Universidad Mariano Gálvez, sede Puerto Barrios
**Competencia:** Día del Programador con IA — Viernes 11/09 → Sábado 12/09/2026
**5 agentes de IA coordinados:** Claude Code ×2 · Codex · Antigravity ×2

---

## 🌐 URL Pública en Vivo

> **http://3.238.198.77**

La aplicación está desplegada en producción sobre **AWS EC2 (Ubuntu 24.04 LTS)** con Nginx + PHP-FPM 8.3 y MySQL 8.0. Sin necesidad de instalación local para evaluar.

---

## 🔑 Credenciales de Acceso por Rol

| Rol | Correo | Contraseña | Acceso |
|---|---|---|---|
| **Administrador** | `admin@solarguatemala.gob.gt` | `Solar2026!Admin` | Control total del sistema |
| **Operador Regional** | `operador@solarguatemala.gob.gt` | `Operador2026!` | Registro y gestión de datos |
| **Evaluador / Visualizador** | `evaluador@umg.edu.gt` | `Evaluador2026!` | Lectura, reportes y API |

> ⚠️ Los datos de demostración ya están precargados. No es necesario crear registros desde cero.

---

## 📋 Matriz de Cumplimiento de Requerimientos

| RF | Descripción | Estado | Módulo / Ruta |
|---|---|---|---|
| RF-01 | Catálogo de los 22 departamentos de Guatemala precargado | ✅ Implementado | Seeder — `/reports` |
| RF-02 | Gestión de paneles solares (marca, modelo, potencia kW) | ✅ Implementado | `/panels` |
| RF-03 | Registro de granjas solares con geolocalización (lat/lng) | ✅ Implementado | `/farms` |
| RF-04 | Asignación de paneles a granjas con cálculo de capacidad (kW) | ✅ Implementado | `/farms/{farm}` |
| RF-05 | Cálculo automático de capacidad instalada (Σ cantidad × potencia) | ✅ Implementado | Accessor `calculated_capacity_kw` |
| RF-06 | Registro de capacidad real vs. nominal por granja | ✅ Implementado | `/farms/{farm}` → detalle técnico |
| RF-07 | Registro del número de familias beneficiadas por granja | ✅ Implementado | `/farms` — campo `benefited_families` |
| RF-08 | Registro de mediciones de generación por período (kWh estimado vs. real) | ✅ Implementado | `/generations/create` |
| RF-09 | Cálculo automático de CO₂ evitado: `real_kwh × 0.40 kg/kWh` | ✅ Implementado | `CarbonOffsetService` |
| RF-10 | Visualización de CO₂ en kg y toneladas métricas | ✅ Implementado | Dashboard + `/generations` |
| RF-11 | Dashboard ejecutivo nacional con 6 KPIs macro | ✅ Implementado | `/dashboard` |
| RF-12 | Ranking departamental por generación y reportes comparativos | ✅ Implementado | `/reports` |
| RF-13 | Mapa interactivo de Guatemala con Leaflet.js + OpenStreetMap | ✅ Implementado | `/map` |
| RF-14 | Detección automática de alertas por desviación ≥ 20% | ✅ Implementado | `AlertEvaluationService` — `/alerts` |
| RF-15 | Proyecciones de generación futura (Promedio Móvil Ponderado + estacionalidad solar) | ✅ Implementado | `SolarForecastService` — `/forecasts` |
| RF-16 | API REST pública documentada (`/api/v1/...`) | ✅ Implementado | `/api-docs` — `routes/api.php` |
| RF-17 | Exportación de reportes en CSV (con BOM UTF-8 para Excel) | ✅ Implementado | `/reports/export` |

**Funciones secundarias implementadas:**
- Trazabilidad y auditoría (OWASP A09): `AuditService` → tabla `audit_logs`
- Cálculo ecológico: equivalencias en árboles plantados y hogares guatemaltecos abastecidos

---

## 🏗️ Arquitectura Técnica

```
┌─────────────────────────────────────────────────────────┐
│                    INTERNET / JURADO                    │
└────────────────────────┬────────────────────────────────┘
                         │ HTTP :80
                ┌────────▼────────┐
                │  AWS EC2        │
                │  Ubuntu 24.04   │
                │  t2.micro       │
                │                 │
                │  ┌───────────┐  │
                │  │  Nginx    │  │  ← Reverse proxy + assets estáticos
                │  └─────┬─────┘  │
                │        │ FastCGI│
                │  ┌─────▼─────┐  │
                │  │ PHP-FPM   │  │  ← PHP 8.3.x
                │  │  8.3      │  │
                │  └─────┬─────┘  │
                │        │        │
                │  ┌─────▼─────┐  │
                │  │ Laravel   │  │  ← Framework v13
                │  │   13.x    │  │     MVC + Policies
                │  └─────┬─────┘  │
                │        │        │
                │  ┌─────▼─────┐  │
                │  │  MySQL    │  │  ← MySQL 8.0 (local en EC2)
                │  │   8.0     │  │     8 tablas, índices optimizados
                │  └───────────┘  │
                └─────────────────┘
```

### Stack completo

| Capa | Tecnología |
|---|---|
| Servidor | AWS EC2 t2.micro — Ubuntu 24.04 LTS |
| Web server | Nginx 1.24 (reverse proxy + gzip + cache de assets) |
| Runtime | PHP 8.3 con PHP-FPM |
| Framework | Laravel 13.x (PSR-12 estricto) |
| Base de datos | MySQL 8.0 (8 tablas, SoftDeletes, índices en `solar_farm_id`, `period`, `status`) |
| Frontend | Blade + Tailwind CSS v4 + Vite |
| Mapa | Leaflet.js 1.9 + OpenStreetMap / CartoDB Positron |
| Gráficas | Chart.js 4.x |
| Autorización | Sistema RBAC propio (`role` en `users`) + Laravel Policies |
| Auditoría | `AuditService` + tabla `audit_logs` (OWASP A09) |
| Seguridad | OWASP Top 10:2025 — checklist firmado en cada PR |

---

## 🚀 Instrucciones de Despliegue en AWS EC2

> Estos son los pasos exactos que se ejecutaron para poner la aplicación en producción.

### Prerrequisitos

- Instancia EC2 `t2.micro` con Ubuntu 24.04 LTS, puerto 80 abierto en el Security Group
- Par de llaves `.pem` para acceso SSH

### 1. Conectarse a la instancia

```bash
ssh -i tu-llave.pem ubuntu@3.238.198.77
```

### 2. Instalar dependencias del sistema

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.3 php8.3-fpm php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath \
    php8.3-tokenizer composer git unzip
```

### 3. Configurar MySQL

```bash
sudo mysql -e "CREATE DATABASE solar_guatemala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'solar_user'@'localhost' IDENTIFIED BY 'TU_PASSWORD_SEGURO';"
sudo mysql -e "GRANT ALL PRIVILEGES ON solar_guatemala.* TO 'solar_user'@'localhost'; FLUSH PRIVILEGES;"
```

### 4. Clonar el repositorio y configurar Laravel

```bash
cd /var/www
sudo git clone https://github.com/Andy13K/Dia_del_programador_IA.git solar
sudo chown -R www-data:www-data /var/www/solar
cd /var/www/solar

sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

### 5. Configurar variables de entorno (`.env`)

```env
APP_NAME="Solar Guatemala"
APP_ENV=production
APP_KEY=              # generada en el paso anterior
APP_DEBUG=false
APP_URL=http://3.238.198.77

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=solar_guatemala
DB_USERNAME=solar_user
DB_PASSWORD=TU_PASSWORD_SEGURO

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false   # true si se configura HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
LOG_LEVEL=error
```

### 6. Ejecutar migraciones y seeders

```bash
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
```

### 7. Compilar assets

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
sudo -u www-data npm ci
sudo -u www-data npm run build
```

### 8. Optimizar para producción

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan optimize
```

### 9. Configurar Nginx

```nginx
# /etc/nginx/sites-available/solar
server {
    listen 80;
    server_name 3.238.198.77;
    root /var/www/solar/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/solar /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

### 10. Verificación post-despliegue

```bash
# Confirmar que el .env no es accesible
curl -s -o /dev/null -w "%{http_code}" http://3.238.198.77/.env   # debe ser 404

# Confirmar que la app responde
curl -s -o /dev/null -w "%{http_code}" http://3.238.198.77/       # debe ser 200 o 302
```

**Tiempo medido de un despliegue completo desde cero:** ~15 minutos

---

## 📂 Documentación del proyecto

| Documento | Descripción |
|---|---|
| [`docs/00-PLAN-MAESTRO.md`](docs/00-PLAN-MAESTRO.md) | Cronograma, roles y propiedad de carpetas |
| [`docs/01-REGLAS-DE-TRABAJO.md`](docs/01-REGLAS-DE-TRABAJO.md) | Git, ramas, commits y Pull Requests |
| [`docs/02-SEGURIDAD-OWASP-2025.md`](docs/02-SEGURIDAD-OWASP-2025.md) | Estándar de seguridad aplicado |
| [`docs/03-PLANTILLA-ERS.md`](docs/03-PLANTILLA-ERS.md) | Especificación de requerimientos (IEEE 830) |
| [`docs/04-BITACORA-PROMPTS.md`](docs/04-BITACORA-PROMPTS.md) | Evidencia de uso de IA — vale el 20% |
| [`docs/05-CHECKLIST-RUBRICA.md`](docs/05-CHECKLIST-RUBRICA.md) | Auditoría final contra la rúbrica |
| [`docs/06-CONTRATOS-HORA-1.md`](docs/06-CONTRATOS-HORA-1.md) | Esquema, rutas y vistas congelados |
| [`docs/07-PLAN-DESPLIEGUE.md`](docs/07-PLAN-DESPLIEGUE.md) | Plan de despliegue en AWS EC2 |
| [`docs/08-GUION-PRESENTACION.md`](docs/08-GUION-PRESENTACION.md) | Guion cronometrado de 10 minutos |
| [`docs/09-MANUAL-USUARIO.md`](docs/09-MANUAL-USUARIO.md) | Manual de usuario para el jurado |

---

## 👥 Roles del equipo

| Integrante | Rol | Agentes de IA |
|---|---|---|
| **Andy Aquino** | Arquitecto e integrador — modelo de datos, autorización, integración de PRs, frontend/UI-UX | Claude Code (Agente E) · Antigravity (Agente C) |
| **Carlos** | Backend, lógica de negocio, DevOps y documentación — controladores, servicios, despliegue, seguridad | Claude Code (Agente A) · Codex (Agente B) · Antigravity (Agente D) |

---

## 🔒 Seguridad

Este sistema implementa los **OWASP Top 10:2025** en su totalidad:

- **A01** Control de acceso: RBAC propio + Policies en cada método del controlador
- **A02** Fallas criptográficas: bcrypt para contraseñas, HTTPS en producción
- **A03** Inyección: Eloquent ORM con parámetros enlazados, sin SQL concatenado
- **A04** Diseño inseguro: `$fillable` explícito, `role` fuera de mass assignment
- **A05** Mala configuración: `APP_DEBUG=false`, headers de seguridad en Nginx
- **A06** Componentes vulnerables: `composer audit` en el pipeline
- **A07** Autenticación: throttling en login (5 intentos / min), tokens CSRF en todos los formularios
- **A08** Integridad: assets compilados con hash en Vite, sin dependencias sin fijar
- **A09** Registro y monitoreo: `AuditService` registra toda acción crítica en `audit_logs`
- **A10** SSRF: sin peticiones HTTP salientes desde el backend

Ver el detalle completo en [`docs/02-SEGURIDAD-OWASP-2025.md`](docs/02-SEGURIDAD-OWASP-2025.md).

---

## 🤖 Metodología de desarrollo con IA

5 agentes trabajando en paralelo sobre un flujo de ramas + Pull Requests con revisión cruzada obligatoria.
Cada PR incluye: el prompt que lo originó · las correcciones humanas · el checklist OWASP firmado.

Ver la evidencia completa en [`docs/04-BITACORA-PROMPTS.md`](docs/04-BITACORA-PROMPTS.md).
