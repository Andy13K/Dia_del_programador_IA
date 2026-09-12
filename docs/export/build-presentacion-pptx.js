"use strict";
/**
 * Generador de la presentacion oficial de K'in Solar Guatemala en PowerPoint (.pptx),
 * alternando diapositivas en tema oscuro y tema claro con los tokens de color reales
 * del sistema (resources/css/app.css). v2: capturas reales del sistema, tabla
 * Estandar -> Artefacto (ERS/ERD), y una unica diapositiva de "Modulos del Sistema"
 * como transicion hacia la demostracion en vivo (en vez de 3 diapositivas de demo).
 */
const fs = require("fs");
const path = require("path");
const pptxgen = require("pptxgenjs");

const PROJECT = "C:\\Users\\ACER NITRO\\Desktop\\DIA DEL PROGRAMADOR IA";
const IMG = path.join(PROJECT, "public", "images");
const SHOT = path.join(PROJECT, "docs", "evidencias", "pptx");
const DIAG = path.join(PROJECT, "docs", "diagramas");
const OUT = path.join(PROJECT, "docs", "export", "Presentacion-Kin-Solar-Guatemala.pptx");

const DARK = {
  bg: "0B0F17", surface: "111722", raised: "182131", line: "263143",
  ink: "EDF2FA", muted: "A1AEC2", accent: "FBBF24", accentSoft: "3A2F12",
  logo: path.join(IMG, "kin-logo-blanco.png"),
};
const LIGHT = {
  bg: "F5F7FA", surface: "FFFFFF", raised: "F8FAFC", line: "E2E8F0",
  ink: "172033", muted: "526175", accent: "A65A00", accentSoft: "FCE9CE",
  logo: path.join(IMG, "kin-logo-negro.png"),
};

const FONT = "Arial";
const W = 13.333, H = 7.5; // 16:9

function newPres() {
  const p = new pptxgen();
  p.defineLayout({ name: "KIN_WIDE", width: W, height: H });
  p.layout = "KIN_WIDE";
  p.author = "Andy Aquino & Carlos";
  p.company = "K'in Solar Guatemala — UMG Puerto Barrios";
  p.title = "K'in Solar Guatemala — Presentacion Oficial";
  return p;
}

function baseSlide(p, theme, { kicker, title, time, speaker, footerRight }) {
  const s = p.addSlide();
  s.background = { color: theme.bg };
  s.addShape("rect", { x: 0, y: 0, w: W, h: 0.09, fill: { color: theme.accent } });
  s.addImage({ path: theme.logo, x: W - 1.7, y: 0.32, w: 1.35, h: 0.56, sizing: { type: "contain", w: 1.35, h: 0.56 } });
  if (kicker) {
    s.addText(kicker.toUpperCase(), { x: 0.55, y: 0.32, w: 8.5, h: 0.35, fontFace: FONT, fontSize: 12, bold: true, color: theme.accent, charSpacing: 1, align: "left" });
  }
  if (title) {
    s.addText(title, { x: 0.55, y: 0.62, w: 10.8, h: 0.6, fontFace: FONT, fontSize: 22, bold: true, color: theme.ink, align: "left" });
  }
  if (time) {
    s.addText(time, { x: 0.55, y: 1.18, w: 5, h: 0.3, fontFace: FONT, fontSize: 11, color: theme.muted, align: "left" });
  }
  s.addShape("line", { x: 0.55, y: 1.5, w: W - 1.1, h: 0, line: { color: theme.line, width: 1 } });
  s.addShape("line", { x: 0.55, y: H - 0.55, w: W - 1.1, h: 0, line: { color: theme.line, width: 1 } });
  s.addText(speaker ? `Orador: ${speaker}` : "", { x: 0.55, y: H - 0.48, w: 5, h: 0.3, fontFace: FONT, fontSize: 10, color: theme.muted });
  s.addText(footerRight || "K'in Solar Guatemala", { x: W - 5.55, y: H - 0.48, w: 5, h: 0.3, fontFace: FONT, fontSize: 10, color: theme.muted, align: "right" });
  return s;
}

function bulletList(s, items, opts) {
  const runs = items.map((it) => ({ text: it, options: { bullet: { code: "2022" }, breakLine: true } }));
  s.addText(runs, Object.assign({ x: 0.65, y: 1.75, w: W - 1.3, h: H - 2.5, fontFace: FONT, fontSize: 14, valign: "top", lineSpacingMultiple: 1.25 }, opts || {}));
}

function card(s, theme, x, y, w, h, title, body, opts) {
  s.addShape("roundRect", { x, y, w, h, rectRadius: 0.08, fill: { color: theme.raised }, line: { color: theme.line, width: 1 } });
  if (title) {
    s.addText(title, { x: x + 0.18, y: y + 0.12, w: w - 0.36, h: 0.35, fontFace: FONT, fontSize: 12.5, bold: true, color: (opts && opts.titleColor) || theme.accent });
  }
  if (body) {
    s.addText(body, { x: x + 0.18, y: y + (title ? 0.48 : 0.15), w: w - 0.36, h: h - (title ? 0.6 : 0.3), fontFace: FONT, fontSize: 10.5, color: theme.muted, valign: "top", lineSpacingMultiple: 1.15 });
  }
}

function statCard(s, theme, x, y, w, h, label, value, sub) {
  s.addShape("roundRect", { x, y, w, h, rectRadius: 0.08, fill: { color: theme.raised }, line: { color: theme.line, width: 1 } });
  s.addText(label.toUpperCase(), { x: x + 0.16, y: y + 0.12, w: w - 0.32, h: 0.3, fontFace: FONT, fontSize: 9.5, bold: true, color: theme.muted, charSpacing: 0.5 });
  s.addText(value, { x: x + 0.16, y: y + 0.4, w: w - 0.32, h: 0.55, fontFace: FONT, fontSize: 22, bold: true, color: theme.ink });
  if (sub) s.addText(sub, { x: x + 0.16, y: y + h - 0.42, w: w - 0.32, h: 0.35, fontFace: FONT, fontSize: 9, color: theme.accent });
}

function pill(s, theme, x, y, text, opts) {
  const w = Math.max(1.4, text.length * 0.085 + 0.4);
  s.addShape("roundRect", { x, y, w, h: 0.34, rectRadius: 0.17, fill: { color: (opts && opts.fill) || theme.accentSoft }, line: { type: "none" } });
  s.addText(text, { x, y, w, h: 0.34, fontFace: FONT, fontSize: 10, bold: true, color: (opts && opts.color) || theme.accent, align: "center", valign: "middle" });
  return w;
}

function screenshotFrame(s, theme, x, y, w, h, imgPath, caption) {
  s.addShape("roundRect", { x, y, w, h, rectRadius: 0.05, fill: { type: "none" }, line: { color: theme.accent, width: 1.5 } });
  s.addImage({ path: imgPath, x: x + 0.06, y: y + 0.06, w: w - 0.12, h: h - (caption ? 0.42 : 0.12), sizing: { type: "cover", w: w - 0.12, h: h - (caption ? 0.42 : 0.12) } });
  if (caption) {
    s.addText(caption, { x: x + 0.1, y: y + h - 0.34, w: w - 0.2, h: 0.3, fontFace: FONT, fontSize: 9.5, italic: true, color: theme.muted });
  }
}

// =====================================================================
async function main() {
  const p = newPres();

  // ---------- 1. Portada & Problema (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "01 · Portada & Problema", title: null, time: "0:00 – 0:40 · Carlos" });
    screenshotFrame(s, t, 6.85, 1.75, 5.95, 4.55, path.join(SHOT, "01-login.png"), "Pantalla de inicio de sesión de K'in Solar Guatemala");
    s.addImage({ path: t.logo, x: 0.55, y: 1.75, w: 3.0, h: 1.25, sizing: { type: "contain", w: 3.0, h: 1.25 } });
    s.addText([
      { text: "K'in Solar ", options: { color: t.ink } },
      { text: "Guatemala", options: { color: t.accent } },
    ], { x: 0.55, y: 3.05, w: 6, h: 0.75, fontFace: FONT, fontSize: 32, bold: true });
    s.addText("Plataforma centralizada de trazabilidad, monitoreo en tiempo real y proyección energética fotovoltaica en los 22 departamentos de Guatemala.", {
      x: 0.55, y: 3.8, w: 6.1, h: 1.0, fontFace: FONT, fontSize: 13, color: t.muted,
    });
    let px = 0.55;
    ["PHP 8.3 / Laravel 13", "AWS EC2 + HTTPS", "MCP Propio", "OWASP 2025", "ISO 25010"].forEach((txt) => {
      const w = pill(s, t, px, 4.9, txt);
      px += w + 0.15;
    });
    card(s, t, 0.55, 5.45, 6.1, 1.85, "El Problema Nacional", "• 22 departamentos con generación fragmentada en hojas de cálculo.\n• Cero detección automática de fallas en tiempo real.\n• Sin trazabilidad de CO₂ evitado ni proyección de producción futura.");
  }

  // ---------- 2. El nombre K'in Solar (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "02 · Originalidad (20%)", title: "¿Por Qué \"K'in\"?", time: "0:40 – 1:25 · Carlos", speaker: "Carlos", footerRight: "Identidad de Producto" });
    s.addShape("roundRect", { x: 0.65, y: 1.85, w: 4.3, h: 2.6, rectRadius: 0.1, fill: { color: "FFFFFF" }, line: { color: t.line, width: 1 } });
    s.addImage({ path: LIGHT.logo, x: 1.05, y: 2.55, w: 3.5, h: 1.2, sizing: { type: "contain", w: 3.5, h: 1.2 } });
    s.addText("El glifo real del día K'in del calendario maya", { x: 0.65, y: 4.55, w: 4.3, h: 0.4, fontFace: FONT, fontSize: 10, italic: true, color: t.muted, align: "center" });
    s.addText([
      { text: "K'in", options: { bold: true, color: t.accent } },
      { text: " es el signo del día del Sol en el calendario Tzolk'in/Haab' maya, usado por los pueblos K'iche', Kaqchikel y otras naciones mayas de Guatemala. Significa literalmente \"Sol\" / \"Día\".", options: { color: t.ink } },
    ], { x: 5.25, y: 1.85, w: 7.5, h: 1.3, fontFace: FONT, fontSize: 13, lineSpacingMultiple: 1.25 });
    s.addText("No es un nombre decorativo: Guatemala es un país de raíz maya y de sol — más de 300 días de irradiancia solar aprovechable al año.", {
      x: 5.25, y: 3.2, w: 7.5, h: 0.85, fontFace: FONT, fontSize: 13, color: t.ink, lineSpacingMultiple: 1.25,
    });
    s.addText("El glifo del logo es el jeroglífico maya real del día K'in, no un ícono genérico — identidad construida desde cero, no traducida de una plantilla extranjera.", {
      x: 5.25, y: 4.15, w: 7.5, h: 0.9, fontFace: FONT, fontSize: 13, color: t.ink, lineSpacingMultiple: 1.25,
    });
    pill(s, t, 5.25, 5.2, "CRITERIO DE ORIGINALIDAD · 20% DE LA RÚBRICA");
  }

  // ---------- 3. Solucion y Diferenciadores (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "03 · Originalidad", title: "Seis Elementos Innovadores de K'in Solar", time: "1:25 – 2:15 · Carlos", speaker: "Carlos", footerRight: "Diferenciadores del Producto" });
    const items = [
      ["Territorio Georreferenciado", "Mapa Leaflet.js de los 22 departamentos, pines proporcionales a kW."],
      ["Inteligencia Predictiva SMA-SF", "Media móvil ponderada + factor estacional bimodal guatemalteco."],
      ["Impacto Ambiental Riguroso", "Factor normativo 0.40 kg CO₂/kWh (CNEE), en kg y toneladas."],
      ["Motor de Alertas Autónomo", "lockForUpdate + umbral de déficit ≥ 20% en tiempo real."],
      ["Ecosistema MCP Propio", "Servidor MCP nativo: la IA consulta y registra en lenguaje natural."],
      ["Identidad Maya-Solar Propia", "Nombre, glifo y paleta ámbar como síntesis conceptual del producto."],
    ];
    let cx = 0.55, cy = 1.75; const cw = 4.0, ch = 1.75, gap = 0.2;
    items.forEach((it, i) => {
      card(s, t, cx, cy, cw, ch, `${i + 1}. ${it[0]}`, it[1]);
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
  }

  // ---------- 4. Arquitectura & Documentacion (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "04 · Documentación (10%)", title: "Arquitectura Escalable y Documentación Trazable", time: "2:15 – 3:00 · Andy", speaker: "Andy", footerRight: "Stack + Documentación" });
    card(s, t, 0.55, 1.75, 5.5, 2.75, "Stack Tecnológico", "Servidor Nube:  AWS EC2 Ubuntu 24.04 LTS\n\nServidor Web / SSL:  Nginx + Let's Encrypt\n\nBackend:  PHP 8.3 + Laravel 13\n\nBase de Datos:  MySQL 8 · 9 entidades\n\nFrontend:  Tailwind CSS v4 + Leaflet + Chart.js");
    card(s, t, 0.55, 4.6, 5.5, 1.7, "Documentación Versionada (docs/)", "ERS v2.0, OWASP Top 10:2025, Bitácora de Prompts (+1000 líneas), Manual de Usuario — 13 documentos en total, todos enlazados desde README.md.");
    screenshotFrame(s, t, 6.25, 1.75, 6.55, 4.55, path.join(SHOT, "02-dashboard.png"), "Panorama nacional en producción: 6 KPIs consolidados en tiempo real");
  }

  // ---------- 5. Estandares y Normas: ERS + ERD (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "05 · Cumplimiento Normativo", title: "Estándares Aplicados y los Artefactos que Construimos con Ellos", time: "Diapositiva de referencia · Andy", speaker: "Andy", footerRight: "ERS + ERD + OWASP + ISO" });
    const rows = [
      ["IEEE 830-1998 / ISO 29148:2018", "ERS — Especificación de Requerimientos de Software completa: objetivos, 19 RF, 12 RNF, 12 casos de uso (docs/03-PLANTILLA-ERS.md, exportado a Word y PDF)."],
      ["UML 2.5", "ERD — Diagrama Entidad-Relación + diagramas de Casos de Uso, Secuencia y Actividades (docs/diagramas/)."],
      ["ISO/IEC 25010:2011 (SQuaRE)", "Sección de Atributos de Calidad del ERS (§5): funcionalidad, eficiencia, compatibilidad, usabilidad, fiabilidad, seguridad, mantenibilidad, portabilidad."],
      ["OWASP Top 10:2025", "Documento de seguridad con los 10 controles (docs/02-SEGURIDAD-OWASP-2025.md) + checklist firmado en cada Pull Request."],
      ["PSR-12 · Conventional Commits", "Estilo de código consistente en todo el backend + historial de commits auditable con trailer de coautoría de IA."],
    ];
    let y = 1.75;
    rows.forEach((r) => {
      s.addShape("roundRect", { x: 0.55, y, w: 7.1, h: 0.95, rectRadius: 0.06, fill: { color: t.raised }, line: { color: t.line, width: 1 } });
      s.addText(r[0], { x: 0.72, y: y + 0.08, w: 6.76, h: 0.32, fontFace: FONT, fontSize: 11.5, bold: true, color: t.accent });
      s.addText(r[1], { x: 0.72, y: y + 0.4, w: 6.76, h: 0.5, fontFace: FONT, fontSize: 9.5, color: t.muted, lineSpacingMultiple: 1.05 });
      y += 1.05;
    });
    screenshotFrame(s, t, 7.95, 1.75, 4.85, 4.55, path.join(DIAG, "diagrama-entidad-relacion.png"), "ERD real del sistema — 9 entidades, database/migrations/");
  }

  // ---------- 6. Estructura de la Documentacion del Proyecto (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "06 · Documentación (10%)", title: "Estructura Completa de la Documentación (docs/)", time: "Diapositiva de referencia · Andy", speaker: "Andy", footerRight: "13 Documentos Versionados" });
    const docs = [
      ["00", "PLAN-MAESTRO", "Cronograma, roles y propiedad de carpetas por agente"],
      ["01", "REGLAS-DE-TRABAJO", "Git, ramas, commits, Pull Requests"],
      ["02", "SEGURIDAD-OWASP-2025", "Estándar de seguridad obligatorio, los 10 controles"],
      ["03", "PLANTILLA-ERS", "ERS completo: objetivos, RF/RNF, ISO 25010, casos de uso"],
      ["04", "BITACORA-PROMPTS", "Evidencia de uso de IA — prompt + corrección humana"],
      ["05", "CHECKLIST-RUBRICA", "Auditoría final honesta contra la rúbrica"],
      ["06", "CONTRATOS-HORA-1", "Esquema, rutas y diseño congelados en la Hora 1"],
      ["07", "PLAN-DESPLIEGUE", "Cómo y dónde se publica el sistema (AWS EC2)"],
      ["08", "GUION-PRESENTACION", "Guion palabra por palabra de la presentación"],
      ["09", "MANUAL-USUARIO", "Manual resumido con credenciales por rol"],
      ["10", "TAREA-MCP-SERVER-PROPIO", "Especificación del servidor MCP propio"],
      ["11", "DIAPOSITIVAS-PRESENTACION", "Estructura de diapositivas y mapeo a la rúbrica"],
    ];
    let cx = 0.55, cy = 1.75; const cw = 3.95, ch = 1.28, gap = 0.15;
    docs.forEach((d, i) => {
      s.addShape("roundRect", { x: cx, y: cy, w: cw, h: ch, rectRadius: 0.06, fill: { color: t.raised }, line: { color: t.line, width: 1 } });
      s.addText(d[0], { x: cx + 0.12, y: cy + 0.08, w: 0.6, h: 0.4, fontFace: FONT, fontSize: 16, bold: true, color: t.accent });
      s.addText(d[1], { x: cx + 0.65, y: cy + 0.08, w: cw - 0.75, h: 0.4, fontFace: FONT, fontSize: 10.5, bold: true, color: t.ink });
      s.addText(d[2], { x: cx + 0.12, y: cy + 0.5, w: cw - 0.24, h: ch - 0.55, fontFace: FONT, fontSize: 9, color: t.muted, valign: "top" });
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
    s.addText("+ OPERACION-COBERTURA-DEMO.md · todo enlazado desde README.md · exportado también a Word y PDF en docs/export/", {
      x: 0.55, y: 6.85, w: W - 1.1, h: 0.3, fontFace: FONT, fontSize: 10, italic: true, color: t.muted,
    });
  }

  // ---------- 7. Flujo de trabajo: ramas, PRs y commits (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "07 · Control de Versiones", title: "Cómo Trabajamos el Repositorio: Ramas, PRs y Commits", time: "Diapositiva de referencia · Carlos", speaker: "Carlos", footerRight: "Disciplina de Equipo" });
    card(s, t, 0.55, 1.75, 4.05, 4.4, "10 Reglas del Proyecto", "1. Nunca push directo a master\n2. Zona de propiedad de carpetas por agente\n3. Esquema/rutas/modelos congelados desde Hora 1\n4. Commit cada 20-30 min (desarrollo incremental)\n5. Checklist OWASP en todo código\n6. Prompts documentados antes del PR\n7. Plantilla de PR completa, sin secciones vacías\n8. Verificar en el navegador antes de decir \"listo\"\n9. Avisar antes de instalar dependencias\n10. No ampliar el alcance sin registrarlo");
    card(s, t, 4.75, 1.75, 4.0, 2.05, "Conventional Commits", "feat(alertas): agregar umbral de desviación del 20%\n\nfix(mapa): corregir popup en modo oscuro\n\ndocs(ers): completar sección de objetivos\n\nCo-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>");
    card(s, t, 4.75, 3.95, 4.0, 2.2, "Pull Request Obligatorio", "Rama propia → PR con plantilla completa (resumen, RF cubierto, checklist OWASP, evidencia de IA) → revisión cruzada → merge a master.\n\nCero commits directos a master en todo el proyecto.");
    card(s, t, 8.9, 1.75, 3.9, 4.4, "Evidencia en Números", "Decenas de Pull Requests fusionados\n\n5 agentes de IA operando en ramas propias\n\n1 bitácora cronológica de +1000 líneas\n\n0 pushes directos a master\n\nCada PR = 1 checklist OWASP firmado");
  }

  // ---------- 8. Innovacion: MCP Propio (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "08 · Originalidad + Uso de IA (20% + 20%)", title: "La IA como Usuario Activo del Sistema", time: "Diapositiva de referencia · Carlos", speaker: "Carlos", footerRight: "Servidor MCP Propio" });
    statCard(s, t, 0.55, 1.75, 3.9, 1.5, "Servidor", "mcp-server/", "Node.js + @modelcontextprotocol/sdk");
    statCard(s, t, 4.65, 1.75, 3.9, 1.5, "Herramientas", "3", "statistics · list_farms · register_generation");
    statCard(s, t, 8.75, 1.75, 4.05, 1.5, "Autenticación", "X-MCP-Key", "hash_equals() — falla cerrada");
    card(s, t, 0.55, 3.45, 12.25, 1.35, "El flujo completo", "\"Registra una medición de 45,000 kWh para la Granja Villa Nueva\" → el agente de IA invoca la herramienta MCP → se autentica → EnergyGenerationService::store() procesa la medición (el mismo servicio que usa el formulario web) → se refleja al instante en el Dashboard.");
    card(s, t, 0.55, 4.95, 12.25, 1.2, "Trazabilidad total", "Todo cambio hecho vía MCP queda registrado en audit_logs, atribuido al usuario de sistema mcp-agent@kinsolar.internal — nunca se confunde con una acción humana.");
  }

  // ---------- 9. Elementos Plus (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "09 · Originalidad + UI/UX", title: "Lo que Nadie nos Pidió, pero Construimos", time: "Diapositiva de referencia · Andy", speaker: "Andy", footerRight: "Creatividad y Valor Añadido" });
    const items = [
      ["Laboratorio SCADA IoT", "Simula fallas de inversores en vivo, con osciloscopio visual."],
      ["Campanita Web Audio API", "Aviso sonoro nativo del navegador ante nueva alerta."],
      ["Tema Claro / Oscuro Real", "Tokens de diseño completos — igual que esta presentación."],
      ["Navegación Móvil Nativa", "Barra inferior tipo app, cero scroll horizontal desde 360px."],
      ["Cliente de API Externo", "public/api-demo.html consume la API sin backend propio."],
      ["Identidad Maya-Solar", "Nombre, glifo y paleta como síntesis conceptual del producto."],
    ];
    let cx = 0.55, cy = 1.75; const cw = 4.0, ch = 1.45, gap = 0.18;
    items.forEach((it, i) => {
      card(s, t, cx, cy, cw, ch, it[0], it[1]);
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
    screenshotFrame(s, t, 0.55, 4.75, 12.25, 1.55, path.join(SHOT, "07-simulador-scada.png"), "Laboratorio SCADA IoT en vivo — osciloscopio de potencia en tiempo real");
  }

  // ---------- 10. Seguridad OWASP Top 10:2025 (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "10 · Funcionalidad (Seguridad)", title: "Cumplimiento Estricto OWASP Top 10:2025", time: "Diapositiva de referencia · Carlos", speaker: "Carlos", footerRight: "10 Controles Verificados por PR" });
    card(s, t, 0.55, 1.75, 6.05, 1.35, "A01 Access Control", "auth + Policy en cada método. RBAC Admin/Operador/Visualizador.");
    card(s, t, 6.75, 1.75, 6.05, 1.35, "A02 Misconfiguration", "Cero secretos en código. Seeders leen de .env vía config/seed.php.");
    card(s, t, 0.55, 3.25, 6.05, 1.35, "A04 Cryptographic Failures", "Bcrypt rounds 12. hash_equals() en comparación de API keys.");
    card(s, t, 6.75, 3.25, 6.05, 1.35, "A05 Injection", "FormRequests estrictos. 100% Eloquent ORM. Cero SQL concatenado.");
    card(s, t, 0.55, 4.75, 6.05, 1.35, "A06 Insecure Design", "lockForUpdate contra condiciones de carrera. Rate limiting login/API.");
    card(s, t, 6.75, 4.75, 6.05, 1.35, "A09 Logging & Alerting", "audit_logs con IP y User-Agent en cada evento sensible.");
  }

  // ---------- 10b. Demo del 403 (DARK) — evidencia visual de A01 ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "11 · Evidencia en Vivo", title: "Control de Acceso Real: Error 403 por Rol", time: "Demo de 30 segundos · Carlos", speaker: "Carlos", footerRight: "OWASP A01 + A09" });
    screenshotFrame(s, t, 0.55, 1.75, 7.3, 4.55, path.join(SHOT, "09-error-403.png"), "Usuario Visualizador intentando crear una granja: acceso denegado");
    card(s, t, 8.05, 1.75, 4.75, 2.15, "Lo que pasó", "El usuario evaluador@umg.edu.gt (rol Visualizador) navegó directamente a /farms/create escribiendo la URL. El sistema lo bloqueó aunque conociera la ruta exacta.");
    card(s, t, 8.05, 4.1, 4.75, 2.2, "Lo que queda registrado", "El intento se guarda en audit_logs con usuario, IP, User-Agent y timestamp exacto — control A09 de OWASP Top 10:2025, verificable en producción.");
  }

  // ---------- 11. Metodologia: 5 Agentes IA (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "12 · Uso de Inteligencia Artificial (20%)", title: "Desarrollo Incremental y Disciplinado con 5 Agentes de IA", time: "Diapositiva de referencia · Carlos", speaker: "Carlos", footerRight: "Metodología del Equipo" });
    card(s, t, 0.55, 1.75, 3.9, 1.9, "5 Agentes en Paralelo", "Claude Code ×2 (Andy y Carlos)\nCodex (Carlos)\nAntigravity ×2 (Andy y Carlos)");
    card(s, t, 4.65, 1.75, 3.9, 1.9, "Ramas y Pull Requests", "Decenas de PRs fusionados sobre master.\nCero commits directos a master.\nRevisión cruzada obligatoria.");
    card(s, t, 8.75, 1.75, 4.05, 1.9, "Bitácora de Prompts", "+1000 líneas: cada prompt, la salida de la IA y la corrección humana obligatoria.");
    card(s, t, 0.55, 3.85, 12.25, 1.9, "¿Cómo coordinamos 5 agentes sin conflictos?", "Contratos de interfaz congelados en la Hora 1: esquema de base de datos, nombres de rutas y sistema de diseño. Cada agente con zona de propiedad exclusiva de carpetas. La IA escribió buena parte del código — incluyendo esta misma presentación; las decisiones de arquitectura, el modelo de datos y los controles de seguridad los tomamos y verificamos nosotros, y cada corrección queda en la bitácora, no solo en la memoria del equipo.");
  }

  // ---------- 12. Modulos del Sistema -> Transicion a Demo en Vivo (DARK) — NUEVA, unica ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "13 · Transición a la Demostración", title: "Los Módulos que Vamos a Presentar en Vivo", time: "A continuación: demo sobre la URL pública · Ambos", speaker: "Ambos", footerRight: "kin-solar-guatemala.duckdns.org" });
    const modules = [
      ["Dashboard Nacional", "6 KPIs + ranking departamental"],
      ["Mapa Interactivo", "22 departamentos, pines por kW"],
      ["Granjas y Paneles", "CRUD con capacidad automática"],
      ["Registro de Generación", "CO₂ automático + validación"],
      ["Alertas de Desviación", "Detección ≥20% + campanita"],
      ["Reportes Departamentales", "PDF, Excel y CSV"],
      ["Proyección SMA-SF", "Estacionalidad guatemalteca"],
      ["API REST + Servidor MCP", "Consumo externo e IA activa"],
    ];
    let cx = 0.55, cy = 1.7; const cw = 2.95, ch = 1.35, gap = 0.15;
    modules.forEach((m, i) => {
      s.addShape("roundRect", { x: cx, y: cy, w: cw, h: ch, rectRadius: 0.07, fill: { color: t.raised }, line: { color: t.line, width: 1 } });
      s.addShape("roundRect", { x: cx + 0.14, y: cy + 0.12, w: 0.34, h: 0.34, rectRadius: 0.17, fill: { color: t.accent }, line: { type: "none" } });
      s.addText(String(i + 1), { x: cx + 0.14, y: cy + 0.12, w: 0.34, h: 0.34, fontFace: FONT, fontSize: 12, bold: true, color: "0B0F17", align: "center", valign: "middle" });
      s.addText(m[0], { x: cx + 0.14, y: cy + 0.52, w: cw - 0.28, h: 0.4, fontFace: FONT, fontSize: 11, bold: true, color: t.ink });
      s.addText(m[1], { x: cx + 0.14, y: cy + 0.92, w: cw - 0.28, h: 0.4, fontFace: FONT, fontSize: 9, color: t.muted });
      cx += cw + gap;
      if ((i + 1) % 4 === 0) { cx = 0.55; cy += ch + gap; }
    });
    s.addShape("roundRect", { x: 0.55, y: 6.55, w: 12.25, h: 0.6, rectRadius: 0.08, fill: { color: t.accentSoft }, line: { type: "none" } });
    s.addText("A continuación: demostración en vivo sobre la URL pública — no localhost, no un video de respaldo.", {
      x: 0.7, y: 6.55, w: 12, h: 0.6, fontFace: FONT, fontSize: 12, bold: true, color: t.accent, valign: "middle",
    });
  }

  // ---------- 13. Roles del equipo y cierre (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "14 · Cierre", title: "Equipo de Desarrollo y Conclusión", time: "Después de la demo en vivo · Ambos", speaker: "Ambos", footerRight: "K'in Solar Guatemala · UMG 2026" });
    card(s, t, 0.55, 1.75, 5.95, 2.6, "Andy Aquino", "Arquitectura de Datos, Integración Frontend UI/UX, Servidor MCP Propio, Despliegue en AWS EC2 y Demo.");
    card(s, t, 6.75, 1.75, 6.05, 2.6, "Carlos", "Seguridad OWASP Top 10, Lógica de Negocio y Servicios, Auditoría de Calidad, Bitácora y Control de Versiones.");
    s.addShape("roundRect", { x: 0.55, y: 4.6, w: 12.25, h: 1.55, rectRadius: 0.08, fill: { color: t.accentSoft }, line: { color: t.accent, width: 1 } });
    s.addText("\"K'in significa Sol en maya. Guatemala tiene sol y raíces mayas de sobra; lo que le faltaba era un sistema que las conectara con datos. La inteligencia artificial escribió gran parte del código; pero la arquitectura, el rigor matemático, la seguridad y el control absoluto del sistema siempre estuvieron en nuestras manos.\"", {
      x: 0.85, y: 4.72, w: 11.65, h: 1.3, fontFace: FONT, fontSize: 13, italic: true, color: t.ink, valign: "middle",
    });
  }

  // ---------- 14. Preguntas (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "15 · Ronda de Preguntas", title: "¿Preguntas? Aquí Van Algunas Respuestas Listas", time: "Cierre · Ambos", speaker: "Ambos", footerRight: "Banco completo en docs/08-GUION-PRESENTACION.md" });
    const qa = [
      ["¿Por qué se llama K'in Solar?", "K'in es el signo maya del Sol. Guatemala tiene sol y raíces mayas — el nombre viene de ahí."],
      ["¿Cuánto lo hizo la IA?", "La mayor parte del código. Arquitectura, modelo de datos y seguridad son nuestros — todo en la bitácora."],
      ["¿Cómo coordinaron 5 agentes?", "Propiedad exclusiva de carpetas y contratos de interfaz congelados en la Hora 1."],
      ["¿Está seguro el sistema?", "Checklist OWASP Top 10:2025 firmado en cada PR. Podemos repetir la demo del 403."],
      ["¿Qué es lo más original?", "El nombre y la identidad maya-solar, y el servidor MCP que hace a la IA usuaria del sistema."],
      ["¿Qué le falta al sistema?", "2FA y sensores físicos reales. El Laboratorio SCADA emula la telemetría mientras tanto."],
    ];
    let cx = 0.55, cy = 1.75; const cw = 4.0, ch = 1.75, gap = 0.2;
    qa.forEach((q, i) => {
      card(s, t, cx, cy, cw, ch, q[0], q[1]);
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
  }

  await p.writeFile({ fileName: OUT });
  console.log("Generado:", OUT, "-", p.slides ? p.slides.length : "?", "diapositivas");
}

main().catch((e) => { console.error(e); process.exit(1); });
