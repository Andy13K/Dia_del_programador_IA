# Exportaciones del ERS

Este directorio contiene las versiones exportadas del ERS (`docs/03-PLANTILLA-ERS.md`) y de la
presentación (`public/presentacion.html`), listas para entregar sin depender de un visor de Markdown.

| Archivo | Cómo se genera |
|---|---|
| `ERS-Kin-Solar-Guatemala.docx` | `node build-ers-docx.js` (requiere `npm install docx@9` en una carpeta aparte, **no** en el proyecto Laravel). Parsea `docs/03-PLANTILLA-ERS.md` y produce un `.docx` con tipografía Arial 12pt, justificado, interlineado 1.5 y sangría por nivel de título, según el formato institucional UMG Puerto Barrios, conservando la numeración decimal (1, 1.1, 1.1.1) del documento. |
| `ERS-Kin-Solar-Guatemala.pdf` | `ers-print.html` renderiza el mismo Markdown en el navegador (con `marked.js` y `KaTeX` vía CDN) e imprime a PDF con `chrome --headless --print-to-pdf`. |
| `presentacion-kin-solar.pdf` | `public/presentacion.html` imprimido con `chrome --headless --print-to-pdf` (hoja `@media print` incluida en el propio archivo). |

**Nota:** `build-ers-docx.js` usa la librería `docx` (npm) como herramienta de generación de documentos,
instalada en una carpeta de trabajo separada — no se agregó a `package.json` del proyecto Laravel.
