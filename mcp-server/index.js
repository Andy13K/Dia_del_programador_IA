#!/usr/bin/env node

/**
 * Servidor MCP propio de K'in Solar Guatemala.
 *
 * Herramientas registradas:
 * - kin_solar_statistics:          Consulta estadísticas nacionales (solo lectura).
 * - kin_solar_list_farms:          Lista las granjas solares (solo lectura).
 * - kin_solar_register_generation: Registra una medición de generación (escritura).
 *
 * Uso:
 *   KIN_SOLAR_API_URL=http://localhost:8000/api/v1 KIN_SOLAR_MCP_KEY=abc123 node index.js
 */

import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";

const BASE_URL =
  process.env.KIN_SOLAR_API_URL ??
  "https://kin-solar-guatemala.duckdns.org/api/v1";
const MCP_KEY = process.env.KIN_SOLAR_MCP_KEY ?? "";

const server = new McpServer({
  name: "kin-solar-guatemala",
  version: "1.0.0",
});

// ─── Herramienta 1: Estadísticas nacionales (solo lectura) ───────────────────

server.registerTool(
  "kin_solar_statistics",
  {
    title: "Estadísticas nacionales de K'in Solar",
    description:
      "Consulta el consolidado nacional: granjas, paneles, capacidad instalada, " +
      "kWh generados, CO₂ evitado y alertas activas del sistema K'in Solar Guatemala.",
    inputSchema: {},
  },
  async () => {
    try {
      const res = await fetch(`${BASE_URL}/statistics`);
      const json = await res.json();
      return {
        content: [
          { type: "text", text: JSON.stringify(json.data, null, 2) },
        ],
      };
    } catch (error) {
      return {
        content: [
          {
            type: "text",
            text: `Error al consultar estadísticas: ${error.message}`,
          },
        ],
        isError: true,
      };
    }
  }
);

// ─── Herramienta 2: Listar granjas solares (solo lectura) ────────────────────

server.registerTool(
  "kin_solar_list_farms",
  {
    title: "Listar granjas solares",
    description:
      "Lista todas las granjas solares registradas en K'in Solar Guatemala, " +
      "con su ID, nombre, departamento, coordenadas GPS, capacidad instalada " +
      "en kW y familias beneficiadas. Usa el ID de la granja para registrar mediciones.",
    inputSchema: {},
  },
  async () => {
    try {
      const res = await fetch(`${BASE_URL}/farms`);
      const json = await res.json();
      return {
        content: [
          { type: "text", text: JSON.stringify(json.data, null, 2) },
        ],
      };
    } catch (error) {
      return {
        content: [
          {
            type: "text",
            text: `Error al listar granjas: ${error.message}`,
          },
        ],
        isError: true,
      };
    }
  }
);

// ─── Herramienta 3: Registrar medición de generación (escritura) ─────────────

server.registerTool(
  "kin_solar_register_generation",
  {
    title: "Registrar medición de generación solar",
    description:
      "Registra una medición REAL de generación de energía solar para una granja " +
      "y período específicos. Esto crea un cambio permanente en la base de datos " +
      "de producción de K'in Solar Guatemala. El sistema calcula automáticamente " +
      "el CO₂ evitado (factor 0.40 kg/kWh) y genera una alerta si la generación " +
      "real es 20% o más inferior a la estimada.",
    inputSchema: {
      solar_farm_id: z
        .number()
        .int()
        .describe(
          "ID numérico de la granja solar (consultar con kin_solar_list_farms)"
        ),
      period: z
        .string()
        .regex(/^\d{4}-\d{2}$/)
        .describe("Período en formato AAAA-MM, ej. 2026-09"),
      record_date: z
        .string()
        .describe("Fecha del registro en formato AAAA-MM-DD"),
      estimated_kwh: z
        .number()
        .min(0)
        .describe("Generación estimada en kWh para el período"),
      real_kwh: z
        .number()
        .min(0)
        .describe("Generación real medida en kWh para el período"),
      notes: z
        .string()
        .max(500)
        .optional()
        .describe("Notas opcionales sobre la medición"),
    },
  },
  async (args) => {
    try {
      const res = await fetch(`${BASE_URL}/generations`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-MCP-Key": MCP_KEY,
        },
        body: JSON.stringify(args),
      });
      const json = await res.json();

      if (!res.ok) {
        return {
          content: [
            {
              type: "text",
              text: `Error ${res.status}: ${JSON.stringify(json, null, 2)}`,
            },
          ],
          isError: true,
        };
      }

      return {
        content: [
          { type: "text", text: JSON.stringify(json.data, null, 2) },
        ],
      };
    } catch (error) {
      return {
        content: [
          {
            type: "text",
            text: `Error de conexión: ${error.message}`,
          },
        ],
        isError: true,
      };
    }
  }
);

// ─── Conectar transporte stdio ───────────────────────────────────────────────

const transport = new StdioServerTransport();
await server.connect(transport);
