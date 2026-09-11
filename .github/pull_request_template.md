<!--
  PLANTILLA OBLIGATORIA DE PULL REQUEST
  Llenar TODAS las secciones. Esta descripción es la evidencia que el jurado
  puede leer para los criterios de Documentación (10%) y Uso de IA (20%).
  Un PR con la plantilla incompleta NO se aprueba.
-->

## 1. Resumen

<!-- Una o dos frases: qué resuelve este PR y por qué existe. -->


## 2. Requerimiento que cubre

| Campo | Valor |
|---|---|
| Requerimiento(s) del ERS | `RF-XX`, `RF-YY` |
| Módulo | |
| Tipo | `feat` / `fix` / `docs` / `refactor` / `style` / `test` / `chore` / `security` |
| Prioridad | Alta / Media / Baja |

## 3. Cambios realizados

<!-- Lista concreta. Un punto por cambio real, no "varios ajustes". -->

- [ ]
- [ ]
- [ ]

### Archivos y carpetas tocados

<!-- Confirmar que están dentro de la zona de propiedad del agente (ver docs/00-PLAN-MAESTRO.md §2).
     Si se tocó algo fuera de la zona, explicar por qué y avisar al Agente A. -->


## 4. Cómo probarlo

<!-- Pasos EXACTOS para que el revisor lo verifique en menos de 5 minutos. -->

1. Entrar a `<URL>` con el usuario `<correo>` / rol `<rol>`
2.
3. **Resultado esperado:**

### Criterio de aceptación verificado

<!-- Copiar el criterio de aceptación del RF y marcar si se cumplió. -->

- [ ] Verificado **en la URL pública**, no solo en localhost


## 5. Evidencia de uso de IA

> Esta sección alimenta directamente el 20% de la rúbrica. No se deja en blanco.

| Campo | Valor |
|---|---|
| Operador | Andy / Carlos |
| Agente / IDE | Claude Code / Codex / Antigravity (Gemini) |
| MCP Server(s) utilizados | |
| Nº de iteraciones con el agente | |

### Prompts clave utilizados

```
<!-- Pegar los 2 o 3 prompts más relevantes, tal cual se escribieron. -->
```

### Qué corrigió el humano sobre la salida de la IA

<!-- Importante: demuestra criterio propio y no vibecoding ciego.
     Ej: "el agente no aplicó la Policy, se agregó a mano en el método update()". -->


## 6. Checklist de seguridad — OWASP Top 10:2025

> Marcar solo lo que aplica a este cambio. Ver `docs/02-SEGURIDAD-OWASP-2025.md`.

- [ ] **A01 Broken Access Control** — rutas bajo `auth`, `Policy` invocada, sin IDOR, `$fillable` explícito
- [ ] **A02 Security Misconfiguration** — sin secretos en el código, sin `dd()`/`dump()` olvidados
- [ ] **A03 Supply Chain** — no se agregaron dependencias / se agregaron y están justificadas abajo
- [ ] **A04 Cryptographic Failures** — sin datos sensibles en texto plano, sin llaves en el código
- [ ] **A05 Injection** — validación con `FormRequest`, sin SQL concatenado, sin `{!! !!}` en Blade
- [ ] **A06 Insecure Design** — reglas de negocio validadas en el servidor, throttling donde aplica
- [ ] **A07 Authentication Failures** — CSRF intacto, sin rutas excluidas del middleware
- [ ] **A08 Integrity Failures** — migraciones versionadas, sin deserialización insegura
- [ ] **A09 Logging & Alerting** — eventos críticos registrados en auditoría, sin secretos en logs
- [ ] **A10 Exceptional Conditions** — errores manejados, se falla cerrado, mensajes genéricos al usuario

### Dependencias nuevas

<!-- Nombre, versión, para qué, y por qué esa y no otra. Si no hay, escribir "Ninguna". -->


## 7. Capturas

<!-- Obligatorio si el cambio es visual. Antes / después si aplica. -->


## 8. Deuda o pendientes que deja este PR

<!-- Ser honesto. Lo que quede aquí se revisa en el bloque de integración. -->

- [ ]


---

### Checklist final del autor

- [ ] La rama sale de `main` actualizado
- [ ] Los commits siguen Conventional Commits y están en español
- [ ] Los commits llevan mi autoría real de GitHub
- [ ] Ejecuté la app y funciona (no solo "compila")
- [ ] Actualicé la documentación si el cambio lo amerita
- [ ] Registré los prompts en `docs/BITACORA-PROMPTS.md`

### Para el revisor

- [ ] Probé los pasos de la sección 4 y funcionan
- [ ] Revisé el checklist OWASP y es coherente con el diff
- [ ] El PR no toca carpetas fuera de la zona del autor sin justificación
