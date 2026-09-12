# Exportaciones del ERS

Este directorio contiene las versiones exportadas del ERS (`docs/03-PLANTILLA-ERS.md`) y de la
presentación (`public/presentacion.html`), listas para entregar sin depender de un visor de Markdown.

| Archivo | Cómo se genera |
|---|---|
| `ERS-Kin-Solar-Guatemala.docx` | `node build-ers-docx.js` (requiere `npm install docx@9` en una carpeta aparte, **no** en el proyecto Laravel). Parsea `docs/03-PLANTILLA-ERS.md` y produce un `.docx` con tipografía Arial 12pt, justificado, interlineado 1.5 y sangría por nivel de título, según el formato institucional UMG Puerto Barrios, conservando la numeración decimal (1, 1.1, 1.1.1) del documento. |
| `ERS-Kin-Solar-Guatemala.pdf` | `ers-print.html` renderiza el mismo Markdown en el navegador (con `marked.js` y `KaTeX` vía CDN) e imprime a PDF con `chrome --headless --print-to-pdf`. |
| `presentacion-kin-solar.pdf` | `public/presentacion.html` imprimido con `chrome --headless --print-to-pdf` (hoja `@media print` incluida en el propio archivo). |
| `Presentacion-Kin-Solar-Guatemala.pptx` | `node build-presentacion-pptx.js` (requiere `npm install pptxgenjs` en una carpeta aparte). Genera un PowerPoint editable de 16 diapositivas, alternando tema oscuro/claro con los tokens de color reales del sistema, incluyendo el origen del nombre "K'in Solar", los estándares y normas aplicadas, la estructura completa de `docs/`, y el flujo de ramas/PRs/commits — pensado como respaldo editable de `public/presentacion.html`. |

**Nota:** `build-ers-docx.js` y `build-presentacion-pptx.js` usan las librerías `docx` y `pptxgenjs`
(npm) como herramientas de generación de documentos, instaladas en una carpeta de trabajo separada —
no se agregaron a `package.json` del proyecto Laravel.
