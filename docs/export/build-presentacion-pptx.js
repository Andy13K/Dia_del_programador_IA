"use strict";
/**
 * Generador de la presentacion oficial de K'in Solar Guatemala en PowerPoint (.pptx),
 * alternando diapositivas en tema oscuro y tema claro con los tokens de color reales
 * del sistema (resources/css/app.css), para que el jurado vea en la propia presentacion
 * el sistema de diseno dual del producto.
 */
const fs = require("fs");
const path = require("path");
const pptxgen = require("pptxgenjs");

const PROJECT = "C:\\Users\\ACER NITRO\\Desktop\\DIA DEL PROGRAMADOR IA";
const IMG = path.join(PROJECT, "public", "images");
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

/** Crea una diapositiva base con fondo, franja superior de acento, logo y pie con orador/tiempo. */
function baseSlide(p, theme, { kicker, title, time, speaker, footerRight }) {
  const s = p.addSlide();
  s.background = { color: theme.bg };

  // Franja superior de acento (identidad de marca)
  s.addShape("rect", { x: 0, y: 0, w: W, h: 0.09, fill: { color: theme.accent } });

  // Logo pequeno arriba a la derecha
  s.addImage({ path: theme.logo, x: W - 1.7, y: 0.32, w: 1.35, h: 0.56, sizing: { type: "contain", w: 1.35, h: 0.56 } });

  if (kicker) {
    s.addText(kicker.toUpperCase(), {
      x: 0.55, y: 0.32, w: 8.5, h: 0.35, fontFace: FONT, fontSize: 12, bold: true,
      color: theme.accent, charSpacing: 1, align: "left",
    });
  }
  if (title) {
    s.addText(title, {
      x: 0.55, y: 0.62, w: 10.8, h: 0.6, fontFace: FONT, fontSize: 22, bold: true,
      color: theme.ink, align: "left",
    });
  }
  if (time) {
    s.addText(time, {
      x: 0.55, y: 1.18, w: 4, h: 0.3, fontFace: FONT, fontSize: 11, color: theme.muted, align: "left",
    });
  }

  // Linea separadora
  s.addShape("line", { x: 0.55, y: 1.5, w: W - 1.1, h: 0, line: { color: theme.line, width: 1 } });

  // Pie de pagina
  s.addShape("line", { x: 0.55, y: H - 0.55, w: W - 1.1, h: 0, line: { color: theme.line, width: 1 } });
  s.addText(speaker ? `Orador: ${speaker}` : "", {
    x: 0.55, y: H - 0.48, w: 5, h: 0.3, fontFace: FONT, fontSize: 10, color: theme.muted,
  });
  s.addText(footerRight || "K'in Solar Guatemala", {
    x: W - 5.55, y: H - 0.48, w: 5, h: 0.3, fontFace: FONT, fontSize: 10, color: theme.muted, align: "right",
  });

  return s;
}

function bulletList(s, items, opts) {
  const runs = items.map((it) => {
    if (typeof it === "string") return { text: it, options: { bullet: { code: "2022" }, breakLine: true } };
    return it;
  });
  s.addText(runs, Object.assign({
    x: 0.65, y: 1.75, w: W - 1.3, h: H - 2.5, fontFace: FONT, fontSize: 14,
    color: opts && opts.color, valign: "top", lineSpacingMultiple: 1.25,
  }, opts || {}));
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

function pill(s, theme, x, y, text, opts) {
  const w = Math.max(1.4, text.length * 0.085 + 0.4);
  s.addShape("roundRect", { x, y, w, h: 0.34, rectRadius: 0.17, fill: { color: (opts && opts.fill) || theme.accentSoft }, line: { type: "none" } });
  s.addText(text, { x, y, w, h: 0.34, fontFace: FONT, fontSize: 10, bold: true, color: (opts && opts.color) || theme.accent, align: "center", valign: "middle" });
  return w;
}

// =====================================================================
async function main() {
  const p = newPres();

  // ---------- 1. Portada & Problema (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "01 · Portada & Problema", title: null, time: "0:00 – 0:40 · Carlos" });
    s.addImage({ path: t.logo, x: 0.55, y: 1.9, w: 3.4, h: 1.42, sizing: { type: "contain", w: 3.4, h: 1.42 } });
    s.addText([
      { text: "K'in Solar ", options: { color: t.ink } },
      { text: "Guatemala", options: { color: t.accent } },
    ], { x: 0.55, y: 3.35, w: 9, h: 0.9, fontFace: FONT, fontSize: 36, bold: true });
    s.addText("Plataforma centralizada de trazabilidad, monitoreo en tiempo real y proyección energética fotovoltaica en los 22 departamentos de Guatemala.", {
      x: 0.55, y: 4.2, w: 9, h: 0.9, fontFace: FONT, fontSize: 14, color: t.muted,
    });
    let px = 0.55;
    ["PHP 8.3 / Laravel 13", "AWS EC2 + HTTPS", "Servidor MCP Propio", "OWASP Top 10:2025", "ISO/IEC 25010"].forEach((txt) => {
      const w = pill(s, t, px, 5.25, txt);
      px += w + 0.18;
    });
    card(s, t, 9.9, 1.9, 2.9, 3.6, "El Problema Nacional", "• 22 departamentos con generación fragmentada en hojas de cálculo.\n\n• Cero detección automática de fallas.\n\n• Sin trazabilidad de CO₂ evitado.\n\n• Imposible proyectar producción futura.");
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
    card(s, t, 0.55, 1.75, 5.9, 4.4, "Stack Tecnológico", "Servidor Nube:  AWS EC2 Ubuntu 24.04 LTS\n\nServidor Web / SSL:  Nginx + Let's Encrypt\n\nBackend:  PHP 8.3 + Laravel 13\n\nBase de Datos:  MySQL 8 · 9 entidades relacionales\n\nFrontend:  Tailwind CSS v4 + Vite + Leaflet + Chart.js");
    card(s, t, 6.65, 1.75, 6.15, 4.4, "Documentación Versionada (docs/)", "ERS v2.0: Objetivos, 19 RF, 12 RNF, ISO/IEC 25010, 12 casos de uso, 6 diagramas UML (Word + PDF)\n\nOWASP Top 10:2025: los 10 controles documentados y verificados\n\nBitácora de Prompts: cada prompt de IA + corrección humana, +1000 líneas\n\nManual de Usuario resumido con credenciales por rol\n\n\"No es documentación de relleno — cada RF se verificó contra el código real.\"");
  }

  // ---------- 5. Estandares y Normas Aplicadas (DARK) — NUEVA ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "05 · Cumplimiento Normativo", title: "Estándares y Normas que Rigen el Proyecto", time: "Diapositiva de referencia · Andy", speaker: "Andy", footerRight: "Normas Aplicadas" });
    const rows = [
      ["IEEE 830-1998", "Estructura y verificabilidad del ERS (Especificación de Requerimientos de Software)."],
      ["ISO/IEC/IEEE 29148:2018", "Evolución de IEEE 830 — ingeniería de requerimientos del ciclo de vida completo."],
      ["ISO/IEC 25010:2011 (SQuaRE)", "8 características de calidad de producto de software: funcionalidad, eficiencia, compatibilidad, usabilidad, fiabilidad, seguridad, mantenibilidad, portabilidad."],
      ["OWASP Top 10:2025", "Estándar de seguridad de aplicaciones web — no negociable, verificado en cada Pull Request."],
      ["PSR-12", "Estilo de código PHP de la PHP-FIG, aplicado en todo el backend."],
      ["Conventional Commits", "Formato estándar de mensajes de commit, en español, con trailer de coautoría de IA."],
      ["UML 2.5", "Notación de los diagramas de casos de uso, entidad-relación, secuencia y actividades."],
    ];
    let y = 1.75;
    rows.forEach((r) => {
      s.addShape("roundRect", { x: 0.55, y, w: W - 1.1, h: 0.62, rectRadius: 0.06, fill: { color: t.raised }, line: { color: t.line, width: 1 } });
      s.addText(r[0], { x: 0.75, y, w: 3.3, h: 0.62, fontFace: FONT, fontSize: 12, bold: true, color: t.accent, valign: "middle" });
      s.addText(r[1], { x: 4.2, y, w: W - 1.1 - 3.85, h: 0.62, fontFace: FONT, fontSize: 11, color: t.muted, valign: "middle" });
      y += 0.72;
    });
  }

  // ---------- 6. Estructura de la Documentacion del Proyecto (LIGHT) — NUEVA ----------
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
      ["08", "GUION-PRESENTACION", "Guion palabra por palabra de los 12 minutos"],
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
    s.addText("+ 09-MANUAL-USUARIO.md y OPERACION-COBERTURA-DEMO.md · todo enlazado desde README.md", {
      x: 0.55, y: 6.85, w: W - 1.1, h: 0.3, fontFace: FONT, fontSize: 10, italic: true, color: t.muted,
    });
  }

  // ---------- 7. Flujo de trabajo: ramas, PRs y commits (DARK) — NUEVA ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "07 · Control de Versiones", title: "Cómo Trabajamos el Repositorio: Ramas, PRs y Commits", time: "Diapositiva de referencia · Carlos", speaker: "Carlos", footerRight: "Disciplina de Equipo" });
    card(s, t, 0.55, 1.75, 4.05, 4.4, "10 Reglas del Proyecto", "1. Nunca push directo a master\n2. Zona de propiedad de carpetas por agente\n3. Esquema/rutas/modelos congelados desde Hora 1\n4. Commit cada 20-30 min (desarrollo incremental)\n5. Checklist OWASP en todo código\n6. Prompts documentados antes del PR\n7. Plantilla de PR completa, sin secciones vacías\n8. Verificar en el navegador antes de decir \"listo\"\n9. Avisar antes de instalar dependencias\n10. No ampliar el alcance sin registrarlo");
    card(s, t, 4.75, 1.75, 4.0, 2.05, "Conventional Commits", "feat(alertas): agregar umbral de desviación del 20%\n\nfix(mapa): corregir popup en modo oscuro\n\ndocs(ers): completar sección de objetivos\n\nCo-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>");
    card(s, t, 4.75, 3.95, 4.0, 2.2, "Pull Request Obligatorio", "Rama propia → PR con plantilla completa (resumen, RF cubierto, checklist OWASP, evidencia de IA) → revisión cruzada → merge a master.\n\nCero commits directos a master en todo el proyecto.");
    card(s, t, 8.9, 1.75, 3.9, 4.4, "Evidencia en Números", "Decenas de Pull Requests fusionados\n\n5 agentes de IA operando en ramas propias\n\n1 bitácora cronológica de +1000 líneas\n\n0 pushes directos a master\n\nCada PR = 1 checklist OWASP firmado");
  }

  // ---------- 8. Demo: Dashboard & Territorio (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "08 · Demostración en Vivo", title: "Dashboard Nacional & Territorio", time: "3:00 – 4:15 · Andy", speaker: "Andy", footerRight: "Funcionalidad + UI/UX" });
    bulletList(s, [
      "Dashboard Nacional: 6 KPIs consolidados (generación acumulada, CO₂ evitado, granjas, paneles, kW, familias).",
      "Ranking Departamental: participación porcentual y curva real vs. esperada.",
      "Mapa Interactivo: filtrado instantáneo por departamento, pines según kW, drawer sin scroll horizontal.",
    ], { color: t.ink, fontSize: 15 });
  }

  // ---------- 9. Demo: Alertas y Operacion (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "09 · Demostración en Vivo", title: "Monitoreo Autónomo de Anomalías Energéticas", time: "4:15 – 5:30 · Andy", speaker: "Andy", footerRight: "Funcionalidad (25%)" });
    bulletList(s, [
      "Registro de medición en vivo con cálculo automático de CO₂ (factor 0.40 kg/kWh).",
      "Déficit del 25-30% → disparo inmediato de alerta + campanita con Web Audio API.",
      "Resolución de alertas con justificación técnica trazada obligatoria.",
      "Reporte consolidado de los 22 departamentos, exportable a CSV con BOM UTF-8.",
    ], { color: t.ink, fontSize: 15 });
  }

  // ---------- 10. Demo: Proyeccion SMA-SF (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "10 · Demostración en Vivo", title: "Modelo SMA-SF (Seasonal Moving Average – Solar Forecast)", time: "5:30 – 6:30 · Andy", speaker: "Andy", footerRight: "Funcionalidad (25%)" });
    card(s, t, 0.55, 1.75, 12.25, 1.1, "Fórmula Predictiva Ponderada", "Base = 0.50·M(t-1) + 0.30·M(t-2) + 0.20·M(t-3)     →     Proyección = Base × F(estación)");
    card(s, t, 0.55, 3.0, 6.0, 1.5, "Época Seca (Nov–Abr)", "Factor = 1.20  (+20% radiación directa)");
    card(s, t, 6.8, 3.0, 6.0, 1.5, "Época Lluviosa (May–Oct)", "Factor = 0.88  (–12% cobertura nubosa)");
    card(s, t, 0.55, 4.65, 12.25, 1.5, "Fallback Nominal y Verificación", "Sin 3 mediciones históricas → Capacidad (kW) × 140 HSP. En /forecasts se contrasta la proyección contra el dato real histórico registrado — nunca una caja negra.");
  }

  // ---------- 11. Innovacion: MCP Propio (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "11 · Originalidad + Uso de IA (20% + 20%)", title: "La IA como Usuario Activo del Sistema", time: "6:30 – 7:15 · Carlos", speaker: "Carlos", footerRight: "Servidor MCP Propio" });
    bulletList(s, [
      "Servidor propio en Node.js (mcp-server/) con @modelcontextprotocol/sdk.",
      "3 herramientas: kin_solar_statistics, kin_solar_list_farms, kin_solar_register_generation.",
      "Demo: \"Registra una medición de 45,000 kWh para la Granja Villa Nueva\" → la IA invoca la herramienta → se autentica con X-MCP-Key → se refleja al instante en el Dashboard.",
      "Trazabilidad total: todo cambio queda en audit_logs, atribuido a mcp-agent@kinsolar.internal.",
    ], { color: t.ink, fontSize: 15 });
  }

  // ---------- 12. Elementos Plus (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "12 · Originalidad + UI/UX", title: "Lo que Nadie nos Pidió, pero Construimos", time: "7:15 – 8:00 · Andy", speaker: "Andy", footerRight: "Creatividad y Valor Añadido" });
    const items = [
      ["Laboratorio SCADA IoT", "Simula fallas de inversores en vivo, con osciloscopio visual."],
      ["Campanita Web Audio API", "Aviso sonoro nativo del navegador ante nueva alerta."],
      ["Tema Claro / Oscuro Real", "Tokens de diseño completos — igual que esta presentación."],
      ["Navegación Móvil Nativa", "Barra inferior tipo app, cero scroll horizontal desde 360px."],
      ["Cliente de API Externo", "public/api-demo.html consume la API sin backend propio."],
      ["Identidad Maya-Solar", "Nombre, glifo y paleta como síntesis conceptual del producto."],
    ];
    let cx = 0.55, cy = 1.75; const cw = 4.0, ch = 1.75, gap = 0.2;
    items.forEach((it, i) => {
      card(s, t, cx, cy, cw, ch, it[0], it[1]);
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
    s.addText("Documentado con honestidad en el ERS como RF-18 / RF-19 — no oculto como si fuera alcance original.", {
      x: 0.55, y: 5.5, w: 12.25, h: 0.4, fontFace: FONT, fontSize: 11, italic: true, color: t.muted,
    });
  }

  // ---------- 13. Seguridad OWASP Top 10:2025 (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "13 · Funcionalidad (Seguridad)", title: "Cumplimiento Estricto OWASP Top 10:2025", time: "8:00 – 8:45 · Carlos", speaker: "Carlos", footerRight: "10 Controles Verificados por PR" });
    const controls = [
      ["A01 Access Control", "auth + Policy en cada método. RBAC Admin/Operador/Visualizador."],
      ["A02 Misconfiguration", "Cero secretos en código. Seeders leen de .env vía config/seed.php."],
      ["A04 Cryptographic Failures", "Bcrypt rounds 12. hash_equals() en comparación de API keys."],
      ["A05 Injection", "FormRequests estrictos. 100% Eloquent ORM. Cero SQL concatenado."],
      ["A06 Insecure Design", "lockForUpdate contra condiciones de carrera. Rate limiting login/API."],
      ["A09 Logging & Alerting", "audit_logs con IP y User-Agent en cada evento sensible."],
    ];
    let cx = 0.55, cy = 1.75; const cw = 4.0, ch = 1.75, gap = 0.2;
    controls.forEach((c, i) => {
      card(s, t, cx, cy, cw, ch, c[0], c[1]);
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
    s.addText("Demo en vivo: usuario visualizador → /farms/create → 403 Prohibido, registrado en audit_logs (control A09).", {
      x: 0.55, y: 5.5, w: 12.25, h: 0.4, fontFace: FONT, fontSize: 11, italic: true, color: t.muted,
    });
  }

  // ---------- 14. Metodologia: 5 Agentes IA (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "14 · Uso de Inteligencia Artificial (20%)", title: "Desarrollo Incremental y Disciplinado con 5 Agentes de IA", time: "8:45 – 9:45 · Carlos", speaker: "Carlos", footerRight: "Metodología del Equipo" });
    card(s, t, 0.55, 1.75, 3.9, 1.9, "5 Agentes en Paralelo", "Claude Code ×2 (Andy y Carlos)\nCodex (Carlos)\nAntigravity ×2 (Andy y Carlos)");
    card(s, t, 4.65, 1.75, 3.9, 1.9, "Ramas y Pull Requests", "Decenas de PRs fusionados sobre master.\nCero commits directos a master.\nRevisión cruzada obligatoria.");
    card(s, t, 8.75, 1.75, 4.05, 1.9, "Bitácora de Prompts", "+1000 líneas: cada prompt, la salida de la IA y la corrección humana obligatoria.");
    card(s, t, 0.55, 3.85, 12.25, 1.9, "¿Cómo coordinamos 5 agentes sin conflictos?", "Contratos de interfaz congelados en la Hora 1: esquema de base de datos, nombres de rutas y sistema de diseño. Cada agente con zona de propiedad exclusiva de carpetas. La IA escribió buena parte del código — incluyendo esta misma presentación; las decisiones de arquitectura, el modelo de datos y los controles de seguridad los tomamos y verificamos nosotros, y cada corrección queda en la bitácora, no solo en la memoria del equipo.");
  }

  // ---------- 15. Roles del equipo y cierre (DARK) ----------
  {
    const t = DARK;
    const s = baseSlide(p, t, { kicker: "15 · Cierre", title: "Equipo de Desarrollo y Conclusión", time: "9:45 – 10:45 · Ambos", speaker: "Ambos", footerRight: "K'in Solar Guatemala · UMG 2026" });
    card(s, t, 0.55, 1.75, 5.95, 2.6, "Andy Aquino", "Arquitectura de Datos, Integración Frontend UI/UX, Servidor MCP Propio, Despliegue en AWS EC2 y Demo.");
    card(s, t, 6.75, 1.75, 6.05, 2.6, "Carlos", "Seguridad OWASP Top 10, Lógica de Negocio y Servicios, Auditoría de Calidad, Bitácora y Control de Versiones.");
    s.addShape("roundRect", { x: 0.55, y: 4.6, w: 12.25, h: 1.55, rectRadius: 0.08, fill: { color: t.accentSoft }, line: { color: t.accent, width: 1 } });
    s.addText("\"K'in significa Sol en maya. Guatemala tiene sol y raíces mayas de sobra; lo que le faltaba era un sistema que las conectara con datos. La inteligencia artificial escribió gran parte del código; pero la arquitectura, el rigor matemático, la seguridad y el control absoluto del sistema siempre estuvieron en nuestras manos.\"", {
      x: 0.85, y: 4.72, w: 11.65, h: 1.3, fontFace: FONT, fontSize: 13, italic: true, color: t.ink, valign: "middle",
    });
  }

  // ---------- 16. Preguntas (LIGHT) ----------
  {
    const t = LIGHT;
    const s = baseSlide(p, t, { kicker: "16 · Ronda de Preguntas", title: "¿Preguntas? Aquí Van Algunas Respuestas Listas", time: "10:45 – 12:00 · Ambos", speaker: "Ambos", footerRight: "Banco completo en docs/08-GUION-PRESENTACION.md" });
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
      card(s, t, cx, cy, cw, ch, q[0], q[1], { titleColor: t.accent });
      cx += cw + gap;
      if ((i + 1) % 3 === 0) { cx = 0.55; cy += ch + gap; }
    });
  }

  await p.writeFile({ fileName: OUT });
  console.log("Generado:", OUT);
}

main().catch((e) => { console.error(e); process.exit(1); });
