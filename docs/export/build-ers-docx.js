"use strict";
/**
 * Generador del ERS de K'in Solar Guatemala en formato .docx
 * Tipografia institucional UMG Puerto Barrios (Arial 12, justificado, interlineado 1.5,
 * sangria por nivel de titulo con tab-stop) pero conservando la numeracion decimal
 * (1, 1.1, 1.1.1) del documento en vez del esquema I./A./1. de la guia.
 */
const fs = require("fs");
const path = require("path");
const {
  Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType,
  Table, TableRow, TableCell, WidthType, BorderStyle, ShadingType,
  Header, PageNumber, PageBreak, ImageRun, TabStopType, SectionType,
  TableOfContents, VerticalAlign,
} = require("docx");

const PROJECT = "C:\\Users\\ACER NITRO\\Desktop\\DIA DEL PROGRAMADOR IA";
const DOCS = path.join(PROJECT, "docs");
const OUT = path.join(DOCS, "export", "ERS-Kin-Solar-Guatemala.docx");

const FONT = "Arial";
const SZ = 24; // 12pt en half-points
const LINE = { line: 360, lineRule: "auto", before: 0, after: 0 };
const BORDER = { style: BorderStyle.SINGLE, size: 6, color: "000000" };
const ALL_BORDERS = { top: BORDER, bottom: BORDER, left: BORDER, right: BORDER };
const HEADER_SHADE = "D9D9D9";

// Indentacion por nivel (DXA), replicando la logica de la guia UMG extendida a 4 niveles.
const LVL = {
  1: { indent: 426, hanging: 426 },
  2: { indent: 851, hanging: 425 },
  3: { indent: 1276, hanging: 425 },
  4: { indent: 1701, hanging: 425 },
};

function run(text, opts = {}) {
  return new TextRun({ text, font: FONT, size: SZ, ...opts });
}

function blank() {
  return new Paragraph({ spacing: LINE, children: [run("")] });
}

function pageBreak() {
  return new Paragraph({ spacing: LINE, children: [new PageBreak()] });
}

// --- Conversion de LaTeX simplificado ($...$) a texto plano legible en Word ---
function texToPlain(s) {
  return s.replace(/\$\$?(.+?)\$\$?/g, (_, inner) => {
    let t = inner;
    t = t.replace(/\\text\{([^}]*)\}/g, "$1");
    t = t.replace(/\\times/g, "×").replace(/\\cdot/g, "·");
    t = t.replace(/\\ge/g, "≥").replace(/\\le/g, "≤").replace(/\\ne/g, "≠");
    t = t.replace(/\\sum/g, "Σ").replace(/\\circ/g, "°");
    t = t.replace(/\\%/g, "%");
    t = t.replace(/_\{([^}]*)\}/g, "_$1").replace(/\^\{([^}]*)\}/g, "^$1");
    t = t.replace(/\\,/g, " ");
    t = t.replace(/[{}]/g, "");
    t = t.replace(/\s+/g, " ").trim();
    return t;
  });
}

// --- Parser de inline markdown -> TextRun[] (bold, italic, code, links, math) ---
function inlineRuns(text, base = {}) {
  text = texToPlain(text);
  // Enlaces markdown [texto](url) -> solo el texto (los anclajes internos no aplican en Word)
  text = text.replace(/\[([^\]]+)\]\([^)]*\)/g, "$1");
  const runs = [];
  // Tokeniza por **bold**, *italic*, `code`
  const re = /(\*\*[^*]+\*\*|`[^`]+`|\*[^*]+\*)/g;
  let last = 0;
  let m;
  while ((m = re.exec(text)) !== null) {
    if (m.index > last) runs.push(run(text.slice(last, m.index), base));
    const token = m[0];
    if (token.startsWith("**")) {
      runs.push(run(token.slice(2, -2), { ...base, bold: true }));
    } else if (token.startsWith("`")) {
      runs.push(run(token.slice(1, -1), { ...base, font: "Consolas" }));
    } else {
      runs.push(run(token.slice(1, -1), { ...base, italics: true }));
    }
    last = re.lastIndex;
  }
  if (last < text.length) runs.push(run(text.slice(last), base));
  if (runs.length === 0) runs.push(run("", base));
  return runs;
}

function heading(levelNum, rawText) {
  // rawText ya trae el numero "1.1 Titulo" o "1. Titulo" -> separa numero/label del titulo
  const cfg = LVL[levelNum] || LVL[4];
  const match = rawText.match(/^([\dA-Za-z.\-]+(?:\.\d+)*\.?)\s+(.*)$/);
  let label = null, title = rawText;
  if (match && /\d/.test(match[1])) {
    label = match[1];
    title = match[2];
  }
  const headingLevels = [HeadingLevel.HEADING_1, HeadingLevel.HEADING_2, HeadingLevel.HEADING_3, HeadingLevel.HEADING_4];
  const children = [];
  if (label) {
    children.push(run(label, { bold: true }));
    children.push(run("\t", {}));
  }
  children.push(...inlineRuns(title, { bold: true }));
  return new Paragraph({
    heading: headingLevels[levelNum - 1] || HeadingLevel.HEADING_4,
    indent: { left: cfg.indent, hanging: cfg.hanging },
    tabStops: [{ type: TabStopType.LEFT, position: cfg.indent }],
    spacing: LINE,
    children,
  });
}

function bodyParagraph(text, levelNum, opts = {}) {
  const cfg = LVL[levelNum] || LVL[1];
  return new Paragraph({
    alignment: AlignmentType.JUSTIFIED,
    indent: { left: cfg.indent },
    spacing: LINE,
    children: [run("     "), ...inlineRuns(text)],
    ...opts,
  });
}

function listParagraph(text, levelNum, indentExtra = 283) {
  const cfg = LVL[levelNum] || LVL[1];
  return new Paragraph({
    alignment: AlignmentType.JUSTIFIED,
    indent: { left: cfg.indent + indentExtra, hanging: indentExtra },
    spacing: LINE,
    children: inlineRuns(text),
  });
}

function tableCell(text, opts = {}) {
  const isHeader = !!opts.header;
  return new TableCell({
    width: opts.width ? { size: opts.width, type: WidthType.PERCENTAGE } : undefined,
    borders: ALL_BORDERS,
    verticalAlign: VerticalAlign.CENTER,
    shading: isHeader ? { type: ShadingType.SOLID, color: HEADER_SHADE, fill: HEADER_SHADE } : undefined,
    margins: { top: 60, bottom: 60, left: 100, right: 100 },
    children: [
      new Paragraph({
        alignment: AlignmentType.JUSTIFIED,
        spacing: LINE,
        children: inlineRuns(text, isHeader ? { bold: true } : {}),
      }),
    ],
  });
}

function mdTableToDocx(rows) {
  // rows: array de arrays de celdas de texto; rows[0] es encabezado
  const colCount = rows[0].length;
  const width = Math.floor(100 / colCount);
  const trs = rows.map((cells, i) =>
    new TableRow({
      cantSplit: true,
      children: cells.map((c) => tableCell(c, { header: i === 0, width })),
    })
  );
  return new Table({ rows: trs, width: { size: 100, type: WidthType.PERCENTAGE } });
}

function imageParagraph(relSrc, maxWidthPx = 600, maxHeightPx = 820) {
  const abs = path.resolve(DOCS, relSrc.replace(/\.svg$/, ".png"));
  if (!fs.existsSync(abs)) {
    console.warn("Imagen no encontrada, se omite:", abs);
    return bodyParagraph(`[Imagen no encontrada: ${relSrc}]`, 1);
  }
  const buf = fs.readFileSync(abs);
  // calcula alto proporcional leyendo dimensiones PNG del header (IHDR)
  const w = buf.readUInt32BE(16);
  const h = buf.readUInt32BE(20);
  const scale = Math.min(maxWidthPx / w, maxHeightPx / h, 1);
  const targetW = Math.round(w * scale);
  const targetH = Math.round(h * scale);
  return new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: LINE,
    keepLines: true,
    keepNext: true,
    children: [
      new ImageRun({
        data: buf,
        transformation: { width: targetW, height: targetH },
        type: "png",
      }),
    ],
  });
}

// ---------------------------------------------------------------------------
// Parser de bloques markdown (subset usado por el ERS) -> elementos docx
// ---------------------------------------------------------------------------
function parseBody(md) {
  const lines = md.split(/\r?\n/);
  const out = [];
  let currentLevel = 1;
  let i = 0;
  let firstH1Seen = false;

  while (i < lines.length) {
    let line = lines[i];

    if (line.trim() === "" || line.trim() === "---") { i++; continue; }

    const hMatch = line.match(/^(#{2,5})\s+(.*)$/);
    if (hMatch) {
      const mdLevel = hMatch[1].length - 1; // ## -> nivel 1 (H1 word), ### -> nivel2, etc.
      currentLevel = mdLevel;
      if (mdLevel === 1) {
        if (firstH1Seen) out.push(pageBreak());
        firstH1Seen = true;
      }
      out.push(heading(mdLevel, hMatch[2].trim()));
      out.push(blank());
      i++;
      continue;
    }

    if (line.startsWith("![")) {
      const im = line.match(/^!\[([^\]]*)\]\(([^)]+)\)/);
      if (im) {
        out.push(imageParagraph(im[2]));
        out.push(new Paragraph({
          alignment: AlignmentType.CENTER,
          spacing: LINE,
          children: [run(im[1], { italics: true, size: 20 })],
        }));
        out.push(blank());
      }
      i++;
      continue;
    }

    if (line.startsWith("|")) {
      const rows = [];
      while (i < lines.length && lines[i].startsWith("|")) {
        if (!/^\|[\s:|-]+\|$/.test(lines[i])) {
          const cells = lines[i].split("|").slice(1, -1).map((c) => c.trim());
          rows.push(cells);
        }
        i++;
      }
      out.push(mdTableToDocx(rows));
      out.push(blank());
      continue;
    }

    if (line.startsWith("> ")) {
      out.push(new Paragraph({
        alignment: AlignmentType.JUSTIFIED,
        indent: { left: LVL[currentLevel].indent + 200 },
        spacing: LINE,
        children: inlineRuns(line.slice(2), { italics: true }),
      }));
      i++;
      continue;
    }

    const numMatch = line.match(/^(\d+)\.\s+(.*)$/);
    if (numMatch) {
      out.push(listParagraph(`${numMatch[1]}. ${numMatch[2]}`, currentLevel));
      i++;
      continue;
    }

    const bulletMatch = line.match(/^(\s*)-\s+(.*)$/);
    if (bulletMatch) {
      const nested = bulletMatch[1].length > 0;
      out.push(listParagraph(`•  ${bulletMatch[2]}`, currentLevel, nested ? 566 : 283));
      i++;
      continue;
    }

    // parrafo normal (puede continuar en varias lineas fisicas hasta linea en blanco)
    let paraLines = [line];
    i++;
    while (i < lines.length && lines[i].trim() !== "" && !/^(#{2,5}\s|!\[|\||>\s|\d+\.\s|\s*-\s)/.test(lines[i])) {
      paraLines.push(lines[i]);
      i++;
    }
    out.push(bodyParagraph(paraLines.join(" "), currentLevel));
    out.push(blank());
  }
  return out;
}

// ---------------------------------------------------------------------------
// Caratula (construida a mano, no parseada del markdown)
// ---------------------------------------------------------------------------
function buildCover() {
  const logoPath = path.join(PROJECT, "public", "images", "kin-logo-negro.png");
  const logoBuf = fs.readFileSync(logoPath);
  const els = [];
  els.push(blank(), blank());
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER,
    spacing: LINE,
    children: [new ImageRun({ data: logoBuf, transformation: { width: 380, height: 158 }, type: "png" })],
  }));
  els.push(blank(), blank());
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("Especificación de Requerimientos de Software", { bold: true, size: 30 })],
  }));
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("(ERS)", { bold: true, size: 26, color: "92400E" })],
  }));
  els.push(blank());
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("Basada en el estándar IEEE 830-1998 / ISO/IEC/IEEE 29148:2018", { italics: true, size: 22, color: "78350F" })],
  }));
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("Atributos de calidad conforme a ISO/IEC 25010:2011", { italics: true, size: 22, color: "78350F" })],
  }));
  els.push(blank());
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("K'in Solar Guatemala", { bold: true, size: 24 })],
  }));
  els.push(new Paragraph({
    alignment: AlignmentType.CENTER, spacing: LINE,
    children: [run("Sistema de Registro y Monitoreo de Generación Solar por Departamento", { size: 24 })],
  }));
  els.push(blank(), blank());

  const meta = [
    ["Campo", "Detalle", "Campo", "Detalle"],
    ["Proyecto", "K'in Solar Guatemala", "Versión", "2.0"],
    ["Organización", "Universidad Mariano Gálvez de Guatemala — Facultad de Ingeniería en Sistemas, sede Puerto Barrios", "Fecha", "12/09/2026"],
    ["Autores", "Andy Fabricio Aquino Escobar (Carné 0909-22-1669) · Carlos", "Estado", "Aprobado — Post-desarrollo"],
    ["Contexto", "Competencia de Programación con IA — Día del Programador 2026", "Revisado por", "Equipo de desarrollo (autorevisión cruzada)"],
  ];
  els.push(mdTableToDocx(meta));
  els.push(blank());
  els.push(new Paragraph({ spacing: LINE, children: [run("Control de versiones del documento", { bold: true })] }));
  els.push(blank());
  const versions = [
    ["Versión", "Fecha", "Autor", "Descripción del cambio"],
    ["1.0", "11/09/2026", "Andy Aquino & Carlos", "Versión inicial, congelada en la Hora 1, basada en el pliego del reto nacional de generación solar."],
    ["2.0", "12/09/2026", "Andy Aquino & Carlos (con IA)", "Documento post-desarrollo completo: carátula, objetivos, interfaces externas, RF/RNF ampliados, ISO/IEC 25010, doce casos de uso narrados, diagramas UML de casos de uso, entidad-relación, secuencia y actividades."],
  ];
  els.push(mdTableToDocx(versions));
  return els;
}

// ---------------------------------------------------------------------------
// Ensamblado principal
// ---------------------------------------------------------------------------
async function main() {
  const mdPath = path.join(DOCS, "03-PLANTILLA-ERS.md");
  const md = fs.readFileSync(mdPath, "utf-8");
  const bodyStart = md.indexOf("## 1. Introducción");
  if (bodyStart === -1) throw new Error("No se encontro el inicio del cuerpo ('## 1. Introduccion')");
  const bodyMd = md.slice(bodyStart);

  const coverEls = buildCover();
  const bodyEls = parseBody(bodyMd);

  const emptyHeader = new Header({ children: [new Paragraph({ children: [] })] });
  const pageNumberHeader = new Header({
    children: [new Paragraph({
      alignment: AlignmentType.RIGHT,
      spacing: LINE,
      children: [new TextRun({ children: [PageNumber.CURRENT], font: FONT, size: SZ })],
    })],
  });

  const doc = new Document({
    styles: {
      default: {
        document: { run: { font: FONT, size: SZ }, paragraph: { spacing: LINE } },
      },
      paragraphStyles: [
        { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
          run: { size: SZ, bold: true, font: FONT, color: "000000" },
          paragraph: { spacing: LINE, outlineLevel: 0 } },
        { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
          run: { size: SZ, bold: true, font: FONT, color: "000000" },
          paragraph: { spacing: LINE, outlineLevel: 1 } },
        { id: "Heading3", name: "Heading 3", basedOn: "Normal", next: "Normal", quickFormat: true,
          run: { size: SZ, bold: true, font: FONT, color: "000000" },
          paragraph: { spacing: LINE, outlineLevel: 2 } },
        { id: "Heading4", name: "Heading 4", basedOn: "Normal", next: "Normal", quickFormat: true,
          run: { size: SZ, bold: true, font: FONT, color: "000000" },
          paragraph: { spacing: LINE, outlineLevel: 3 } },
      ],
    },
    sections: [
      {
        properties: { type: SectionType.NEXT_PAGE, page: { size: { width: 12240, height: 15840 }, margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
        headers: { default: emptyHeader },
        children: coverEls,
      },
      {
        properties: { type: SectionType.NEXT_PAGE, page: { size: { width: 12240, height: 15840 }, margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
        headers: { default: emptyHeader },
        children: [
          new Paragraph({ spacing: LINE, children: [run("Índice", { bold: true, size: 28 })] }),
          blank(),
          new TableOfContents("Índice", { hyperlink: true, headingStyleRange: "1-4" }),
        ],
      },
      {
        properties: { type: SectionType.NEXT_PAGE, page: { size: { width: 12240, height: 15840 }, margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
        headers: { default: pageNumberHeader },
        children: bodyEls,
      },
    ],
  });

  const buf = await Packer.toBuffer(doc);
  fs.writeFileSync(OUT, buf);
  console.log("Generado:", OUT, "(" + buf.length + " bytes)");
}

main().catch((e) => { console.error(e); process.exit(1); });
