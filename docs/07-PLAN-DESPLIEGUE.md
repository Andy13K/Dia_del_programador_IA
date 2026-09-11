# Plan de Despliegue — El riesgo número uno

> Las bases lo dicen sin ambigüedad: **"La aplicación deberá estar online"** al momento de presentar.
> Localhost no cuenta. Un equipo con un sistema excelente sin desplegar pierde de golpe
> funcionalidad (25 %) y presentación (10 %).
>
> **Esto se resuelve ANTES de las 17:00 del viernes**, con un "hola mundo" ya publicado.
> Desplegar Laravel por primera vez a las 11 de la noche, con sueño, es cómo se pierde una competencia.

---

## Regla de oro

**Se despliega VACÍO primero.** A las 18:20 del viernes, apenas exista el `laravel new`,
la URL pública ya debe mostrar la pantalla de bienvenida. A partir de ahí, cada integración
(21:30, 23:30, sábado 13:00 y 15:00) vuelve a desplegar. Nunca un único despliegue al final.

---

## Opciones, ordenadas por riesgo para este caso

### Opción 1 — Hosting compartido con cPanel (menor riesgo si ya tienen uno)

Si alguno ya tiene hosting con cPanel y MySQL, es lo más rápido y predecible.

- Subir por Git o FTP
- **Punto crítico:** el *document root* debe apuntar a `/public`
- SSL con Let's Encrypt desde cPanel (un clic)
- Crear la base de datos y un usuario dedicado, nunca `root`

### Opción 2 — Railway / Render (más limpio, requiere tarjeta o plan gratuito)

- Conectar el repositorio de GitHub → despliegue automático en cada push a `main`
- MySQL como servicio adjunto
- HTTPS automático
- **Ventaja fuerte:** cada merge a `main` redespliega solo. Cero fricción durante la noche.
- **Riesgo:** el plan gratuito puede dormir el servicio o agotarse

### Opción 3 — VPS (DigitalOcean / Hetzner / Contabo)

Máximo control, máximo tiempo de configuración. Solo si ya tienen uno andando con Nginx + PHP-FPM.

### Opción 4 — Cloudflare Tunnel sobre Laragon — **solo como plan de respaldo**

Expone el Laragon local con una URL pública HTTPS.

```bash
cloudflared tunnel --url http://localhost:8000
```

- **A favor:** funciona en 2 minutos, sin configurar nada
- **En contra:** depende de que la laptop esté encendida y con internet durante la evaluación.
  Si se cae el wifi del salón, se cae la demo.
- **Veredicto:** tener el binario `cloudflared` descargado y probado como red de seguridad,
  pero **no** como plan principal.

---

## Decisión del equipo

| Campo | Valor |
|---|---|
| **Plataforma elegida** | `<________>` |
| **URL de producción** | `<________>` |
| **Plan de respaldo** | Cloudflare Tunnel sobre Laragon |
| **Responsable** | Agente D (Carlos) con apoyo del Agente A |
| **Fecha límite del hola-mundo desplegado** | **viernes 11/09, antes de las 17:00** |

---

## Checklist de despliegue inicial (hacer ANTES del reto)

- [ ] Cuenta creada en la plataforma elegida
- [ ] Proyecto Laravel vacío desplegado y accesible por HTTPS
- [ ] Base de datos MySQL creada y conectada (`php artisan migrate` corre sin error)
- [ ] `APP_DEBUG=false` y `APP_ENV=production` configurados
- [ ] `APP_KEY` generada en producción
- [ ] `https://<dominio>/.env` devuelve 404 (probarlo de verdad)
- [ ] Vite compila en producción y los estilos se ven
- [ ] Anotado **cuánto tarda** un despliegue completo (dato clave para planificar la noche)
- [ ] Anotado el procedimiento exacto en la sección siguiente

## Procedimiento de despliegue (llenar con los comandos reales)

```bash
# 1.
# 2.
# 3.
```

**Tiempo medido de un despliegue completo:** `____ minutos`

---

## Variables de entorno de producción

> Nunca en el repositorio. Se configuran en el panel de la plataforma.

```
APP_NAME=
APP_ENV=production
APP_KEY=          # generar en producción
APP_DEBUG=false
APP_URL=https://

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=      # usuario dedicado, NO root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

LOG_LEVEL=error
```

---

## Comandos posteriores a cada despliegue

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

> Si algo se comporta raro tras un despliegue, lo primero es `php artisan optimize:clear`.
> Una caché de configuración vieja es la causa del 80 % de los "pero en mi máquina sí funciona".

---

## Verificación post-despliegue (los 2 minutos que salvan la presentación)

Correr esto después de **cada** integración:

- [ ] La URL carga
- [ ] Se puede iniciar sesión con el usuario admin
- [ ] La función principal se ejecuta de principio a fin
- [ ] Los estilos se ven (no HTML pelado → indica que Vite no compiló)
- [ ] No aparece ningún stack trace
- [ ] Abrirlo **desde el celular**, con datos móviles, no con el wifi de la casa

---

## Plan de contingencia para la presentación

- [ ] **Video de respaldo** (3–4 min) de la demo completa, grabado el sábado a las 15:00
      y guardado en el celular y en la laptop. Si se cae el internet del salón, se proyecta el video.
- [ ] Sesión ya iniciada y pestañas abiertas **antes** de pasar al frente
- [ ] Datos de demostración ya cargados: nada de crear registros en vivo frente al jurado
      salvo en el momento planeado del guion
- [ ] Segunda laptop con la misma sesión abierta, como respaldo
