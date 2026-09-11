# Reglas del Proyecto — Lectura obligatoria para todo agente de IA

> Este archivo lo leen **los agentes del equipo** (Claude Code ×2 [Andy y Carlos], Codex [Carlos], Antigravity ×2 [Andy y Carlos]).
> `AGENTS.md` y `GEMINI.md` apuntan a este mismo contenido.
> Copiar este archivo a la raíz del repositorio de la competencia apenas se cree.

---

## Contexto

Competencia de Programación con IA — Día del Programador, Universidad Mariano Gálvez,
sede Puerto Barrios. Equipo de 2 personas (Andy y Carlos) operando 5 agentes en paralelo/coordinados
durante ~12 horas. La evaluación es por rúbrica; ver `docs/05-CHECKLIST-RUBRICA.md`.

**Stack:** PHP 8.3 · Laravel · MySQL 8 · Blade + Tailwind CSS v4 · Vite · Laragon (local)

---

## Las 10 reglas

1. **Nunca hagas push a `main`.** Siempre rama propia + Pull Request. Sin excepciones,
   ni siquiera para un cambio de una línea, ni siquiera en la última hora.

2. **Respetá tu zona de propiedad de carpetas** (`docs/00-PLAN-MAESTRO.md` §2).
   Si necesitás un cambio fuera de tu zona, pedilo — no lo hagas.

3. **Nunca toques el esquema de base de datos, `routes/` ni `app/Models/`**
   salvo que seas el Agente A / E (Claude Code). Están congelados desde las 18:00.

4. **Commiteá cada 20–30 minutos** con Conventional Commits en español.
   El desarrollo incremental es un criterio evaluado, no una preferencia.

5. **Cumplí el checklist OWASP Top 10:2025** (`docs/02-SEGURIDAD-OWASP-2025.md`) en todo
   código que escribas. Lo innegociable:
   - Toda ruta privada bajo `auth` + `Policy` invocada en **todos** los métodos del controlador
   - Consultas siempre acotadas al usuario dueño (nada de `Model::find($request->id)` pelado)
   - `$fillable` explícito; nunca `$guarded = []`
   - Validación en el servidor con `FormRequest` en **todos** los formularios
   - `{{ }}` en Blade, nunca `{!! !!}`
   - Ningún secreto escrito en el código
   - Nada de `catch` vacíos ni de continuar cuando algo falla

6. **Documentá tus prompts** en `docs/04-BITACORA-PROMPTS.md` antes de abrir el PR.
   Registrá especialmente **qué corrigió el humano** sobre tu salida.

7. **Llená la plantilla de PR completa.** Un PR con secciones vacías no se aprueba.

8. **Verificá en el navegador antes de decir que algo está listo.** "Compila" no es "funciona".
   Y la verificación final es sobre la **URL pública**, no sobre localhost.

9. **No instales dependencias sin avisar al equipo.** Cada paquete nuevo se justifica en el PR.

10. **No amplíes el alcance.** Si se te ocurre una función buenísima que nadie pidió,
    escribila en la sección "Deuda o pendientes" del PR y seguí con lo asignado.
    Después de las 22:00 del viernes no se empiezan funciones nuevas.

---

## Convenciones de código

- **PHP:** PSR-12. Tipado estricto en firmas de métodos. `declare(strict_types=1);` donde aplique.
- **Nombres:** clases en `PascalCase`, métodos en `camelCase`, tablas y columnas en `snake_case`.
- **Idioma:** el código en inglés, los textos visibles al usuario y los mensajes de commit en español.
  Elegido en la Hora 1 — no mezclar a mitad de camino.
- **Controladores:** delgados. La lógica de negocio va a `app/Services/`.
- **Validación:** en `FormRequest`, nunca dentro del controlador.
- **Blade:** componentes reutilizables en `resources/views/components/`. No copiar y pegar markup.
- **Sin comentarios obvios.** Comentar el *por qué*, nunca el *qué*.

## Prohibiciones explícitas

- `dd()`, `dump()`, `var_dump()`, `ray()` en código que se mergea
- `DB::statement` o SQL concatenado con entrada del usuario
- Deshabilitar el middleware CSRF o agregar rutas a su lista de excepciones
- `APP_DEBUG=true` en producción
- Commitear `.env` o cualquier credencial
- `git push --force` a una rama compartida (usar `--force-with-lease` solo en la propia)
- Funciones a medias visibles en el menú de navegación

---

## Antes de abrir un PR — verificación rápida

```bash
git switch main && git pull origin main
git switch mi-rama && git rebase main
php artisan test          # si hay pruebas
npm run build             # los assets compilan
```

- [ ] Probado en el navegador
- [ ] Checklist OWASP del PR marcado con honestidad
- [ ] Prompts registrados en la bitácora
- [ ] Plantilla de PR completa

---

## Documentos de referencia

| Documento | Para qué |
|---|---|
| `docs/00-PLAN-MAESTRO.md` | Cronograma, roles y propiedad de carpetas |
| `docs/01-REGLAS-DE-TRABAJO.md` | Git, ramas, commits, Pull Requests |
| `docs/02-SEGURIDAD-OWASP-2025.md` | **Estándar de seguridad obligatorio** |
| `docs/03-PLANTILLA-ERS.md` | Especificación de requerimientos (IEEE 830 / 29148) |
| `docs/04-BITACORA-PROMPTS.md` | Evidencia de uso de IA — vale el 20 % |
| `docs/05-CHECKLIST-RUBRICA.md` | Auditoría final contra la rúbrica |
| `docs/06-CONTRATOS-HORA-1.md` | Esquema, rutas, vistas y diseño congelados |
| `docs/07-PLAN-DESPLIEGUE.md` | Cómo y dónde se publica el sistema |
| `docs/08-GUION-PRESENTACION.md` | Guion de los 10 minutos finales |
