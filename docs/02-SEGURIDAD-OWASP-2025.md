# Estándar de Seguridad del Proyecto — OWASP Top 10:2025

**Norma base:** OWASP Top 10:2025 (publicado en noviembre de 2025)
**Stack objetivo:** PHP 8.3 · Laravel · MySQL 8 · Blade + Tailwind
**Aplicabilidad:** obligatoria para los cuatro agentes. Ningún PR se aprueba sin el checklist de su categoría.

> Cambios relevantes frente a la edición 2021, para que nadie cite la lista vieja:
> `Security Misconfiguration` sube al #2; aparece `Software Supply Chain Failures` (#3) como
> expansión de "componentes vulnerables"; aparece `Mishandling of Exceptional Conditions` (#10)
> y **SSRF deja de ser categoría propia** (queda absorbida en A06 Insecure Design).

---

## A01:2025 — Broken Access Control

El riesgo #1 por cuarta edición consecutiva. En una competencia de 12 horas es **el más probable de fallar**,
porque el patrón típico de la IA es generar un CRUD que funciona sin verificar quién lo está llamando.

### Controles obligatorios

- [ ] **Denegar por defecto.** Toda ruta va dentro de `Route::middleware(['auth'])`. Las públicas se declaran una por una, explícitamente.
- [ ] **Autorización por objeto, no solo por rol.** Una `Policy` por cada modelo con dueño:

```php
// app/Policies/SolicitudPolicy.php
public function update(User $user, Solicitud $solicitud): bool
{
    return $user->id === $solicitud->user_id || $user->hasRole('admin');
}
```

- [ ] **Invocar la autorización en TODOS los métodos**, no solo en los "peligrosos": `$this->authorize('update', $solicitud);`
      Atajo recomendado: `$this->authorizeResource(Solicitud::class, 'solicitud');` en el constructor del controlador.
- [ ] **IDOR — el error clásico.** Nunca confiar en el ID que viene de la URL.

```php
// MAL: cualquiera cambia el 5 por un 6 y ve datos ajenos
$s = Solicitud::findOrFail($request->id);

// BIEN: el alcance lo impone la consulta, no el usuario
$s = auth()->user()->solicitudes()->findOrFail($id);
```

- [ ] **Mass assignment.** Definir `$fillable` explícito en todos los modelos. Nunca `$guarded = []`.
      Jamás incluir `role`, `user_id`, `estado`, `precio` o `is_admin` en `$fillable`.
- [ ] La UI que oculta un botón **no es un control de acceso**. La verificación va en el servidor, siempre.
- [ ] Permisos con `spatie/laravel-permission`, sembrados en un seeder y versionados.

### Cómo se verifica (hacerlo antes de presentar)

Iniciar sesión con un usuario de rol bajo y pedir a mano la URL de una acción de administrador.
Debe devolver **403**, no la página. Repetir cambiando un ID por el de otro usuario.

---

## A02:2025 — Security Misconfiguration

Subió del #5 al #2. Es el riesgo que más se dispara en despliegues hechos a la carrera.

### Controles obligatorios

- [ ] **`APP_DEBUG=false` y `APP_ENV=production` en el servidor.** Con debug activo, cualquier error
      muestra el stack trace completo, rutas del servidor y **variables de entorno con contraseñas**.
      Este es el error más común y más fatal de un despliegue apurado.
- [ ] `.env` **nunca** en el repositorio. Verificar que esté en `.gitignore` desde el primer commit.
      Subir un `.env.example` con las claves pero sin valores.
- [ ] `APP_KEY` generada en producción (`php artisan key:generate`), distinta a la de desarrollo.
- [ ] El *document root* del servidor apunta a `/public`, **no** a la raíz del proyecto.
      Si `https://tu-app/.env` descarga un archivo, estás expuesto.
- [ ] HTTPS obligatorio. `URL::forceScheme('https')` en producción y redirección de HTTP a HTTPS.
- [ ] Cabeceras de seguridad en un middleware global:

```php
$response->headers->set('X-Content-Type-Options', 'nosniff');
$response->headers->set('X-Frame-Options', 'DENY');
$response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
$response->headers->set('Content-Security-Policy', "default-src 'self'; img-src 'self' data:;");
```

- [ ] Cookies: `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`.
- [ ] Usuario de MySQL con permisos **solo** sobre la base del proyecto. Nunca `root`.
- [ ] Eliminar rutas de prueba, `dd()`, `dump()` y `Log::info($request->all())` antes del merge final.
- [ ] Cuentas de demo con contraseñas reales, no `123456`. El jurado va a ver el seeder.

---

## A03:2025 — Software Supply Chain Failures

Categoría nueva y directamente relevante: con 4 agentes de IA instalando paquetes en paralelo,
es fácil terminar con dependencias basura o abandonadas.

### Controles obligatorios

- [ ] **Regla de equipo: ningún agente instala un paquete sin avisar en el chat.**
      Cada dependencia nueva se justifica en el PR que la introduce.
- [ ] `composer.lock` y `package-lock.json` **siempre versionados**. En producción se instala con
      `composer install --no-dev --optimize-autoloader` (nunca `composer update`).
- [ ] Auditoría antes del deploy final: `composer audit` y `npm audit --omit=dev`
- [ ] Solo paquetes conocidos y mantenidos (Spatie, Laravel oficiales, barryvdh/dompdf).
      Si un agente propone un paquete con 40 estrellas y último commit en 2021, se rechaza.
- [ ] Nada de CDNs de terceros para JS crítico. Se compila con Vite y se sirve desde el propio dominio.
- [ ] Activar Dependabot en el repositorio (1 clic, y suma como evidencia de "uso de herramientas de desarrollo").

---

## A04:2025 — Cryptographic Failures

### Controles obligatorios

- [ ] Contraseñas con `bcrypt` (el `Hash::make()` por defecto de Laravel). **Nunca** `md5`, `sha1` ni `base64`.
- [ ] TLS 1.2+ en todo el tráfico. Certificado válido (Let's Encrypt sirve).
- [ ] Datos sensibles en reposo cifrados con el cast `encrypted` de Eloquent:

```php
protected $casts = ['dpi' => 'encrypted', 'telefono' => 'encrypted'];
```

- [ ] Tokens de API, secretos y llaves **solo** en `.env`, jamás escritos en el código.
      Si un agente pega una API key en un archivo `.php`, se revierte el commit.
- [ ] Tokens de recuperación/verificación generados con `Str::random(64)`, con expiración y de un solo uso.
      Nunca `rand()` ni `uniqid()`.
- [ ] Si el reto toca pagos: **no se almacena ningún dato de tarjeta**. Se delega a la pasarela (PCI-DSS por delegación).

---

## A05:2025 — Injection

Laravel protege por defecto, pero solo si no se sale del camino.

### Controles obligatorios

- [ ] **SQL:** usar Eloquent o Query Builder siempre. Si es indispensable un `DB::raw` o `whereRaw`,
      va con *bindings* parametrizados, nunca con concatenación:

```php
// MAL — inyección directa
DB::select("SELECT * FROM users WHERE email = '$email'");

// BIEN
DB::select('SELECT * FROM users WHERE email = ?', [$email]);
```

- [ ] **Nunca** interpolar entrada del usuario en `orderBy()`, `pluck()` o nombres de columna.
      Validar contra una lista blanca:

```php
$columna = in_array($request->sort, ['nombre','fecha','estado']) ? $request->sort : 'fecha';
```

- [ ] **XSS:** usar `{{ $var }}` en Blade (escapa por defecto). El uso de `{!! !!}` está **prohibido**
      salvo autorización explícita del Agente A, y siempre sobre HTML ya saneado.
- [ ] **Validación de entrada en el servidor** para *todos* los formularios, con `FormRequest`:

```php
public function rules(): array {
    return [
        'nombre' => ['required','string','max:100'],
        'email'  => ['required','email','max:150'],
        'monto'  => ['required','numeric','min:0','max:999999'],
        'estado' => ['required', Rule::in(['pendiente','aprobado','rechazado'])],
    ];
}
```

  La validación del lado del cliente es solo comodidad de UX; no cuenta como control.
- [ ] **Subida de archivos:** validar `mimes` y `max`, renombrar con `Str::uuid()`,
      guardar fuera de `/public` y servir por una ruta con autorización.
      Jamás confiar en la extensión del nombre original.
- [ ] **Comandos del sistema:** prohibido `exec`, `shell_exec`, `system` con datos del usuario.

---

## A06:2025 — Insecure Design

Aquí se absorbió **SSRF**, que ya no es categoría independiente.

### Controles obligatorios

- [ ] **Rate limiting en autenticación y formularios públicos.** Bloqueo de fuerza bruta:
      `Route::post('/login', ...)->middleware('throttle:5,1');` (5 intentos por minuto)
- [ ] Modelar los **límites del negocio** en el servidor: no se aprueba dos veces, no se paga un monto negativo,
      no se reserva un cupo agotado. Verificar con transacciones (`DB::transaction`) y bloqueos donde aplique.
- [ ] **SSRF (ahora dentro de esta categoría):** si el sistema consume una URL provista por el usuario
      (webhook, importar desde enlace, avatar remoto), validar el esquema (`https` únicamente) y
      bloquear destinos internos: `127.0.0.1`, `localhost`, `169.254.169.254`, rangos `10.x`, `172.16–31.x`, `192.168.x`.
- [ ] Enumeración de usuarios: el mensaje de login fallido es genérico — *"Credenciales inválidas"* —
      nunca *"ese correo no existe"*.
- [ ] Reglas de negocio críticas **nunca** solo en JavaScript.

---

## A07:2025 — Authentication Failures

### Controles obligatorios

- [ ] Usar el andamiaje de autenticación de Laravel (Breeze / Fortify). **No inventar** un login propio.
- [ ] Política de contraseñas: `Password::min(8)->letters()->numbers()->uncompromised()`
      (`uncompromised()` consulta HaveIBeenPwned: es un punto que se puede lucir en la presentación.)
- [ ] `session()->regenerate()` tras login exitoso y `session()->invalidate()` en logout — previene *session fixation*.
- [ ] Expiración de sesión configurada (`SESSION_LIFETIME`).
- [ ] Throttling de login (ver A06).
- [ ] Rutas de cambio de contraseña y de datos sensibles protegidas con confirmación de contraseña
      (middleware `password.confirm`).
- [ ] **CSRF activo.** Laravel lo trae; el riesgo es que un agente deshabilite el middleware
      o meta rutas en la lista de excepciones para "que funcione el AJAX". Prohibido — se envía el token en la cabecera.

---

## A08:2025 — Software and Data Integrity Failures

### Controles obligatorios

- [ ] Nunca deserializar datos del usuario con `unserialize()`. Usar `json_decode` con validación.
- [ ] Assets compilados con Vite desde el repositorio; sin scripts remotos sin verificar.
      Si se usa un CDN permitido, agregar atributo `integrity` (SRI).
- [ ] Webhooks entrantes: verificar firma HMAC antes de procesar. Sin firma válida, `401`.
- [ ] Migraciones versionadas en git; ningún cambio de esquema aplicado a mano en producción.
- [ ] URLs firmadas de Laravel (`URL::temporarySignedRoute`) para cualquier enlace de acción
      enviado por correo o compartido.

---

## A09:2025 — Security Logging and Alerting Failures

Renombrada: ya no es "Monitoring", es **"Alerting"** — no basta con registrar, hay que poder notar.
Además, esta categoría es la que hace visible el punto de **auditabilidad** de la documentación.

### Controles obligatorios

- [ ] Tabla `auditorias` (o `activity_log` de `spatie/laravel-activitylog`) con:
      `usuario_id`, `accion`, `modelo`, `modelo_id`, `ip`, `user_agent`, `created_at`.
- [ ] Se registran como mínimo: login exitoso, login fallido, logout, cambio de rol/permiso,
      creación/edición/eliminación de registros críticos, e intentos de acceso denegado (403).
- [ ] **Nunca** escribir contraseñas, tokens ni datos de tarjeta en los logs.
      Revisar que `config/logging.php` no vuelque `$request->all()`.
- [ ] Pantalla de "Bitácora de auditoría" visible para el rol administrador.
      **Esto se enseña en la demo:** es un diferenciador fuerte frente a equipos que solo hicieron CRUD.
- [ ] Alerta visible (badge en el dashboard) ante N intentos fallidos de login del mismo usuario.

---

## A10:2025 — Mishandling of Exceptional Conditions

Categoría nueva. Trata sobre qué pasa cuando algo sale mal: fallos abiertos, errores tragados
en silencio, y mensajes de error que filtran información interna.

### Controles obligatorios

- [ ] **Páginas de error propias** para 403, 404, 419, 500 (`resources/views/errors/`).
      El error 500 por defecto de Laravel en producción se ve descuidado y el de debug filtra todo.
- [ ] **Fallar cerrado, no abierto.** Si el servicio externo o la verificación no responden,
      se deniega la operación — no se continúa "por si acaso":

```php
try {
    $ok = $servicio->verificar($dato);
} catch (\Throwable $e) {
    Log::error('Fallo de verificacion', ['id' => $dato->id, 'error' => $e->getMessage()]);
    return back()->withErrors('No fue posible completar la operación. Intente de nuevo.');
    // NUNCA: $ok = true;
}
```

- [ ] Prohibidos los `catch` vacíos y el operador `@` para silenciar errores.
- [ ] Los mensajes de error al usuario son **genéricos**; el detalle técnico va al log.
      Nunca mostrar consultas SQL, rutas del servidor ni nombres de clases.
- [ ] Operaciones de varios pasos dentro de `DB::transaction()` para que un fallo a la mitad
      no deje datos inconsistentes.
- [ ] Validar siempre el resultado de operaciones que pueden devolver `null`
      (`findOrFail` en lugar de `find` sin verificar).

---

## Checklist final de seguridad — ejecutar antes de presentar

Lo corre el Agente D el sábado a las 15:00 y firma el resultado en el PR final.

- [ ] `APP_DEBUG=false` en producción → provocar un error y confirmar que no sale stack trace
- [ ] `https://<dominio>/.env` → debe dar 404, no descargar nada
- [ ] Login con usuario básico + URL de admin → 403
- [ ] Cambiar un ID en la URL por el de otro usuario → 403 o 404, nunca datos ajenos
- [ ] 6 intentos de login fallidos → bloqueo por throttling
- [ ] Enviar una etiqueta `script` con `alert(1)` en cada campo de texto → se muestra como texto, no ejecuta
- [ ] Enviar `' OR '1'='1` en el buscador y en el login → sin error de SQL, sin acceso
- [ ] `composer audit` + `npm audit` → sin vulnerabilidades críticas
- [ ] Formulario sin token CSRF → 419
- [ ] Subir un `.php` renombrado a `.jpg` → rechazado
- [ ] Revisar los logs → sin contraseñas ni tokens en texto plano
- [ ] Las 4 páginas de error personalizadas se ven bien

---

## Cómo se presenta esto ante el jurado

No basta con ser seguro: hay que **demostrarlo**. En la presentación, 60 segundos:

1. Mostrar este documento y decir que el sistema se diseñó contra el
   OWASP Top 10 **edición 2025**, no la de 2021.
2. Demo en vivo: intentar entrar a una URL de administrador con un usuario básico → 403.
3. Demo en vivo: mostrar la bitácora de auditoría con ese intento ya registrado.

Ese par de gestos toca tres criterios a la vez: funcionalidad, originalidad/profesionalismo y presentación.

---

## Fuentes

- [OWASP Top 10:2025 — Introducción (owasp.org)](https://owasp.org/Top10/2025/0x00_2025-Introduction/)
- [The OWASP Top Ten 2025](https://www.owasptopten.org/)
- [What Changed in OWASP Top 10 2025 — Qualys](https://blog.qualys.com/qualys-insights/2026/06/15/what-changed-in-owasp-top-10-2025-and-recommendations-for-each-category)
- [OWASP Top 10 2025: Key Changes for Developers — Aikido](https://www.aikido.dev/blog/owasp-top-10-2025-changes-for-developers)
