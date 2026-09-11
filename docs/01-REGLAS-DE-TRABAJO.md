# Reglas de Trabajo — Git, Ramas y Pull Requests

Estas reglas son **obligatorias para los cuatro agentes y los dos operadores**.
Existen por dos razones: evitar conflictos de merge entre 4 agentes en paralelo, y
generar la evidencia que la rúbrica exige (commits claros, tareas por integrante y rol).

---

## 1. Modelo de ramas

Una sola rama permanente: **`main`**. No hay `develop`.

```
main  ──●────●────●────●────●──→   (siempre desplegable, siempre verde)
         \   /      \   /
          ●─●        ●─●            ramas de trabajo, cortas, efímeras
```

**`main` está protegida.** Nadie empuja directo a `main`. Nunca. Ni "solo este arreglito".

### Nomenclatura de ramas

```
<tipo>/<agente>/<descripcion-corta-en-kebab-case>
```

Tipos permitidos: `feat`, `fix`, `docs`, `refactor`, `style`, `test`, `chore`, `security`

Ejemplos reales:
```
feat/andy-claude/modelo-datos-inicial
feat/carlos-codex/crud-solicitudes
style/andy-antigravity/layout-dashboard
docs/carlos-antigravity/ers-seccion-3
security/andy-claude/policies-rbac
```

El segmento `<agente>` identifica **persona + herramienta**. Esto es lo que
permite demostrar en la rúbrica quién hizo qué y con qué IA.

### Ciclo de vida de una rama

1. `git switch main && git pull origin main` — **siempre partir de main actualizado**
2. `git switch -c feat/andy-claude/lo-que-sea`
3. Trabajar. Commits pequeños y frecuentes (ver §2).
4. `git push -u origin feat/andy-claude/lo-que-sea`
5. Abrir PR con la plantilla completa (ver §3).
6. El otro integrante revisa y aprueba.
7. **Squash merge** a `main`. Borrar la rama.
8. Todos los demás hacen `git pull origin main` antes de su siguiente commit.

**Vida máxima de una rama: 90 minutos.** Si pasás de ahí, tu PR va a ser
un infierno de conflictos. Partí el trabajo en pedazos más chicos.

---

## 2. Convención de commits

Formato [Conventional Commits](https://www.conventionalcommits.org/):

```
<tipo>(<alcance>): <qué hace, en imperativo, en español, sin punto final>

<cuerpo opcional: por qué se hizo, no qué se hizo>
```

Ejemplos:
```
feat(solicitudes): agregar validación de fechas en el formulario de creación
fix(auth): corregir redirección tras login de usuario con rol técnico
security(rbac): aplicar Policy de autorización en SolicitudController
docs(ers): completar sección 3.3 de requerimientos no funcionales
```

Reglas:
- **Un commit = un cambio lógico.** Nada de "avances varios".
- Mínimo **un commit cada 20–30 minutos** de trabajo. La rúbrica premia
  explícitamente el *desarrollo incremental*; un repo con 6 commits gigantes
  se ve exactamente igual que uno hecho con IA sin control.
- Prohibido: `update`, `cambios`, `fix`, `asdf`, `wip final final`.

### Autoría — esto importa para la rúbrica

Cada quien commitea con **su propia cuenta de GitHub**. Verificar antes de empezar:

```bash
git config user.name "Andy Aquino"
git config user.email "tu-correo-de-github@ejemplo.com"
```

Si el commit lo generó un agente, se agrega el trailer al final del mensaje:

```
Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>
```

Eso deja **evidencia auditable de uso de IA directamente en el historial de git** —
exactamente lo que pide el criterio del 20%.

---

## 3. Pull Requests

### Reglas duras

| Regla | Valor |
|---|---|
| Tamaño máximo recomendado | ~400 líneas cambiadas |
| Aprobaciones requeridas | 1 (del otro integrante) |
| Tiempo máximo de revisión | 15 minutos — si pasa de ahí, se aprueba y se anota la deuda |
| Estrategia de merge | **Squash and merge** (historial lineal y legible) |
| Borrar rama al mergear | Sí, automático |
| Auto-merge | Prohibido |

### Qué debe llevar la descripción

La plantilla está en `.github/pull_request_template.md` y se llena **completa**.
No es burocracia: esa descripción es literalmente el insumo de la sección
"Tareas realizadas por cada miembro del equipo y rol" de la documentación final,
y es lo que el jurado va a ver si abre el repositorio.

Mínimo indispensable en cada PR:
1. **Qué** se implementó (lista de cambios concretos)
2. **Por qué** (qué requerimiento del ERS cubre: `RF-XX`)
3. **Cómo probarlo** (pasos exactos para que el revisor lo verifique)
4. **Agente de IA utilizado** y resumen de los prompts clave
5. **Checklist de seguridad OWASP** aplicable al cambio
6. **Capturas** si el cambio es visual

### Cuando hay conflictos

El que abrió el PR lo resuelve, **nunca el revisor**:

```bash
git switch main && git pull origin main
git switch mi-rama
git rebase main       # o merge si el rebase se complica
# resolver, luego:
git push --force-with-lease
```

Usar `--force-with-lease`, **nunca** `--force` a secas.

---

## 4. Reglas de emergencia (para las últimas 2 horas)

Durante el bloque presencial (14:00–16:00 del sábado), cuando el reloj apremia:

- Los PR de tipo `fix` se aprueban en **máximo 5 minutos**.
- Se sigue usando rama + PR. **Nunca** push directo a `main`, ni siquiera al final.
  Un push directo puede tumbar producción a 20 minutos de presentar, y además
  rompe la evidencia de trabajo colaborativo que evalúa la rúbrica.
- Antes de cada merge: verificar que la URL pública sigue respondiendo.

---

## 5. Protección de `main` en GitHub

El Agente A configura esto apenas se crea el repo (Settings → Branches → Add rule):

- [ ] Require a pull request before merging
- [ ] Require approvals: **1**
- [ ] Dismiss stale approvals when new commits are pushed
- [ ] Do not allow bypassing the above settings
- [ ] Automatically delete head branches

---

## 6. Definición de "Terminado"

Una tarea no está terminada hasta que:

- [ ] El código funciona **en la URL pública**, no en localhost
- [ ] El PR está mergeado a `main`
- [ ] El criterio de aceptación del `RF-XX` correspondiente se verificó en el navegador
- [ ] El checklist OWASP del PR está completo
- [ ] Los prompts clave quedaron registrados en `docs/BITACORA-PROMPTS.md`
