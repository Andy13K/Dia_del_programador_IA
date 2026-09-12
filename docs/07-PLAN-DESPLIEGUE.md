# Manual de Despliegue y Operaciones en Producción

> **Sistema:** K'in Solar Guatemala — Registro y Monitoreo de Generación Solar por Departamento  
> **Entorno:** AWS EC2 (Ubuntu 24.04 LTS) · Nginx 1.24 · PHP-FPM 8.3 · MySQL 8.0  
> **Dominio y Certificado:** `https://kin-solar-guatemala.duckdns.org` (Let's Encrypt TLS 1.3)  
> **IP Elástica Fija (Elastic IP):** `75.101.181.76`  
> **Autores:** Andy Fabricio Aquino Escobar (Carné 0909-22-1669) · Carlos Giovanni Martínez (Carné 0909-22-19157)  
> **Universidad:** Universidad Mariano Gálvez de Guatemala — Sede Puerto Barrios

---

## Índice

1. [Arquitectura de Infraestructura en la Nube](#1-arquitectura-de-infraestructura-en-la-nube)
2. [Prerrequisitos y Configuración de AWS](#2-prerrequisitos-y-configuración-de-aws)
3. [Instalación del Stack Base (Ubuntu 24.04 LTS)](#3-instalación-del-stack-base-ubuntu-2404-lts)
4. [Configuración de la Base de Datos (MySQL 8.0)](#4-configuración-de-la-base-de-datos-mysql-80)
5. [Despliegue de la Aplicación Laravel 13](#5-despliegue-de-la-aplicación-laravel-13)
6. [Configuración del Servidor Web (Nginx + PHP-FPM 8.3)](#6-configuración-del-servidor-web-nginx--php-fpm-83)
7. [Dominio Gratuito y Certificado SSL / TLS (Let's Encrypt)](#7-dominio-gratuito-y-certificado-ssl--tls-lets-encrypt)
8. [Configuración del Servidor MCP Propio (Node.js)](#8-configuración-del-servidor-mcp-propio-nodejs)
9. [Procedimiento de Actualización Continua (Runbook de Producción)](#9-procedimiento-de-actualización-continua-runbook-de-producción)
10. [Plan de Recuperación ante Desastres y Contingencias](#10-plan-de-recuperación-ante-desastres-y-contingencias)

---

## 1. Arquitectura de Infraestructura en la Nube

La solución está desplegada bajo una arquitectura cloud monolítica de alto rendimiento optimizada para la capa gratuita (Free Tier) de AWS:

```
                  ┌─────────────────────────────────────────┐
                  │            USUARIO / JURADO             │
                  └────────────────────┬────────────────────┘
                                       │ HTTPS :443 / DNS A
                 ┌─────────────────────▼─────────────────────┐
                 │    DuckDNS: kin-solar-guatemala.duckdns.org│
                 │         Elastic IP: 75.101.181.76         │
                 └─────────────────────┬─────────────────────┘
                                       │
                 ┌─────────────────────▼─────────────────────┐
                 │      AWS EC2 Instance (t2.micro)          │
                 │          Ubuntu 24.04 LTS                 │
                 │                                           │
                 │  ┌─────────────────────────────────────┐  │
                 │  │       Nginx 1.24 Reverse Proxy      │  │
                 │  │  - SSL Termination (Let's Encrypt)  │  │
                 │  │  - OWASP Security Headers (A02)     │  │
                 │  │  - Gzip Compression + Static Cache  │  │
                 │  │  - Bloqueo de archivos ocultos/.env │  │
                 │  └──────────────────┬──────────────────┘  │
                 │                     │ Unix Socket FastCGI │
                 │  ┌──────────────────▼──────────────────┐  │
                 │  │       PHP-FPM 8.3 + OPCache         │  │
                 │  │      Laravel 13 Core Engine         │  │
                 │  │   Document Root: /var/www/.../public│  │
                 │  └──────────┬──────────────────────────┘  │
                 │             │ PDO TCP 3306 (localhost)    │
                 │  ┌──────────▼──────────────────────────┐  │
                 │  │            MySQL 8.0                │  │
                 │  │   Base de datos: solar_guatemala    │  │
                 │  │   Usuario acotado: solar_user       │  │
                 │  └─────────────────────────────────────┘  │
                 │                                           │
                 │  ┌─────────────────────────────────────┐  │
                 │  │    Servidor MCP Propio (Node.js)    │  │
                 │  │    mcp-server/ (Stdio / JSON-RPC)   │  │
                 │  │    Inyección Segura API X-MCP-Key   │  │
                 │  └─────────────────────────────────────┘  │
                 └───────────────────────────────────────────┘
```

---

## 2. Prerrequisitos y Configuración de AWS

### 2.1 Especificaciones de la Instancia EC2
- **Tipo de instancia:** `t2.micro` (1 vCPU, 1 GiB RAM).
- **Sistema Operativo:** Ubuntu Server 24.04 LTS (HVM), EBS General Purpose SSD (gp3) 20 GB.
- **Asignación de IP Elástica (Elastic IP):** Se reservó y asoció la IP `75.101.181.76` a la instancia `i-0ddc9cd7ed1e085c6`. Esto garantiza que si la máquina se reinicia, la IP pública no cambia jamás.

### 2.2 Reglas del Security Group (Firewall)
Se abrieron estrictamente los puertos necesarios:

| Protocolo | Puerto | Origen | Propósito |
|---|---|---|---|
| **SSH** | `22` | `0.0.0.0/0` | Gestión remota y despliegue por terminal (MobaXterm) |
| **HTTP** | `80` | `0.0.0.0/0` | Acceso web inicial y validación de reto ACME de Let's Encrypt |
| **HTTPS** | `443` | `0.0.0.0/0` | Tráfico cifrado de producción con TLS 1.3 |

> 🔒 **OWASP A02:** El puerto MySQL `3306` **NO está expuesto** en el Security Group; MySQL solo escucha en `127.0.0.1`.

---

## 3. Instalación del Stack Base (Ubuntu 24.04 LTS)

Conectarse por SSH usando la llave privada `.pem`:

```bash
ssh -i "kin-solar-key.pem" ubuntu@75.101.181.76
```

Actualizar el sistema e instalar Nginx, PHP 8.3, extensiones requeridas por Laravel, MySQL y herramientas:

```bash
sudo apt update && sudo apt upgrade -y

# Instalar Nginx, MySQL y herramientas
sudo apt install -y nginx mysql-server git unzip curl

# Instalar PHP 8.3 y extensiones necesarias para Laravel
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml     php8.3-curl php8.3-zip php8.3-bcmath php8.3-tokenizer php8.3-intl

# Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 4. Configuración de la Base de Datos (MySQL 8.0)

Crear la base de datos oficial en UTF-8 y un usuario con privilegios restringidos únicamente sobre esta base (OWASP A01 / A02):

```sql
sudo mysql
```

```sql
CREATE DATABASE solar_guatemala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'solar_user'@'localhost' IDENTIFIED BY 'PASSWORD_SEGURO_AQUI';
GRANT ALL PRIVILEGES ON solar_guatemala.* TO 'solar_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Despliegue de la Aplicación Laravel 13

### 5.1 Clonación del Repositorio
Clonar el repositorio oficial dentro de `/var/www/solar-guatemala`:

```bash
cd /var/www
sudo git clone https://github.com/Andy13K/Dia_del_programador_IA.git solar-guatemala
sudo chown -R ubuntu:ubuntu /var/www/solar-guatemala
cd /var/www/solar-guatemala
```

### 5.2 Instalación de Dependencias PHP
```bash
composer install --no-dev --optimize-autoloader
```

### 5.3 Archivo de Entorno de Producción (`.env`)
Crear el archivo `.env` configurando las variables reales (nunca versionadas en Git):

```ini
APP_NAME="K'in Solar Guatemala"
APP_ENV=production
APP_KEY=base64:GENERAR_CON_ARTISAN_KEY_GENERATE
APP_DEBUG=false
APP_URL=https://kin-solar-guatemala.duckdns.org

FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=solar_guatemala
DB_USERNAME=solar_user
DB_PASSWORD=PASSWORD_SEGURO_AQUI

# Credenciales maestras del seeder (leídas exclusivamente vía config/seed.php)
SEED_ADMIN_PASSWORD=PASSWORD_ADMIN_DEMO
SEED_OPERADOR_PASSWORD=PASSWORD_OPERADOR_DEMO
SEED_EVALUADOR_PASSWORD=PASSWORD_EVALUADOR_DEMO

# Llave de autenticación para el Servidor MCP Propio
MCP_SERVER_KEY=LLAVE_SECRETA_MCP_AQUI
```

Generar la clave criptográfica y enlazar almacenamiento:

```bash
php artisan key:generate
php artisan storage:link
```

### 5.4 Permisos de Carpetas
Asignar propiedad a `www-data` para carpetas de escritura:

```bash
sudo chown -R www-data:www-data /var/www/solar-guatemala/storage /var/www/solar-guatemala/bootstrap/cache
sudo chmod -R 775 /var/www/solar-guatemala/storage /var/www/solar-guatemala/bootstrap/cache
```

### 5.5 Ejecutar Migraciones y Datos Semilla
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## 6. Configuración del Servidor Web (Nginx + PHP-FPM 8.3)

Crear el archivo de configuración en `/etc/nginx/sites-available/solar-guatemala`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name kin-solar-guatemala.duckdns.org;
    root /var/www/solar-guatemala/public;

    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.php index.html;
    charset utf-8;

    # Compresión gzip para máxima velocidad
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Bloqueo total de archivos .env y carpetas ocultas (OWASP A02)
    location ~ /\.(?!well-known).* {
        deny all;
        return 404;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2|woff)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, no-transform";
    }
}
```

Habilitar el sitio y reiniciar Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/solar-guatemala /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

---

## 7. Dominio Gratuito y Certificado SSL / TLS (Let's Encrypt)

### 7.1 Configuración de DuckDNS
1. Se registró el subdominio `kin-solar-guatemala` en [DuckDNS.org](https://www.duckdns.org).
2. Se apuntó el registro tipo A directamente a la IP elástica: `75.101.181.76`.

### 7.2 Emisión Automatizada con Certbot
Instalar Certbot y su plugin de Nginx:

```bash
sudo apt install -y certbot python3-certbot-nginx
```

Emitir el certificado y aplicar redirección automática de HTTP a HTTPS:

```bash
sudo certbot --nginx -d kin-solar-guatemala.duckdns.org --non-interactive --agree-tos -m aaquinoe1@miumg.edu.gt --redirect
```

Certbot configura automáticamente la directiva SSL y crea el temporizador systemd de renovación automática (`certbot.timer`).

---

## 8. Configuración del Servidor MCP Propio (Node.js)

El repositorio incluye un servidor MCP propio en la carpeta `mcp-server/`. Para mantenerlo en ejecución:

```bash
cd /var/www/solar-guatemala/mcp-server
npm install --omit=dev
```

Se configura con las variables de entorno de producción que apuntan a la URL pública y su clave dedicada `X-MCP-Key`.

---

## 9. Procedimiento de Actualización Continua (Runbook de Producción)

Este es el procedimiento estándar ejecutado desde **MobaXterm** para descargar actualizaciones mergeadas en `master` y aplicarlas limpiamente en menos de 10 segundos:

```bash
cd /var/www/solar-guatemala
sudo chown -R ubuntu:ubuntu /var/www/solar-guatemala
git fetch origin master
git reset --hard origin/master
sudo chown -R www-data:www-data storage bootstrap/cache
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Si hubo migraciones nuevas en el commit:
```bash
php artisan migrate --force
```

### Si se compilaron nuevos assets:
```bash
php artisan optimize
```

---

## 10. Plan de Recuperación ante Desastres y Contingencias

| Escenario | Causa Posible | Acción Inmediata de Recuperación |
|---|---|---|
| **Página en blanco / Error 500** | Caché de configuración o vistas desfasadas | Ejecutar `php artisan optimize:clear` y verificar logs en `storage/logs/laravel.log`. |
| **Estilos rotos (sin CSS)** | Nginx no encuentra `public/build` o permiso denegado | Verificar `ls -la public/build` y asegurar permisos `www-data:www-data`. |
| **Falla en el servicio web** | Caída de Nginx o PHP-FPM | Ejecutar `sudo systemctl restart php8.3-fpm nginx`. |
| **Falla en base de datos** | Proceso mysqld detenido | Ejecutar `sudo systemctl restart mysql`. |
| **Caída del internet en el salón de la sede** | Falla del proveedor local | Activar hotspot móvil 4G/5G en el teléfono de Andy o Carlos; el sistema reside en AWS, no en la red local. |
| **Plan de Respaldo Definitivo** | Fallo total de conectividad externa | Se dispone de la presentación interactiva descargada en formato PDF y PowerPoint (`docs/export/Presentacion-Kin-Solar-Guatemala.pptx`). |

---

## Firma de Auditoría de Despliegue

- [x] **URL pública operativa y verificada:** `https://kin-solar-guatemala.duckdns.org`
- [x] **Certificado TLS 1.3 activo y seguro**
- [x] **Protección contra descarga de `.env` activa (404/403)**
- [x] **Modo depuración desactivado (`APP_DEBUG=false`)**
- [x] **Base de datos sembrada con 22 departamentos y telemetría de prueba**
