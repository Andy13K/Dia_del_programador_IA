# Servidor MCP — K'in Solar Guatemala

Servidor [Model Context Protocol](https://modelcontextprotocol.io/) propio del sistema
K'in Solar Guatemala. Permite que cualquier asistente de IA (Claude Desktop, Claude Code,
etc.) consulte datos reales de la plataforma y registre mediciones de generación solar
en lenguaje natural.

## Herramientas disponibles

| Herramienta | Tipo | Descripción |
|---|---|---|
| `kin_solar_statistics` | Lectura | Consolidado nacional: granjas, paneles, kWh, CO₂, alertas |
| `kin_solar_list_farms` | Lectura | Lista de granjas con ID, departamento, capacidad y GPS |
| `kin_solar_register_generation` | Escritura | Registra una medición real para una granja y período |

## Requisitos

- Node.js 18 o superior (usa `fetch` nativo)
- La API de K'in Solar corriendo (local o en producción)
- La llave `MCP_API_KEY` configurada tanto en el `.env` de Laravel como aquí

## Instalación

```bash
cd mcp-server
npm install
```

## Variables de entorno

Copiar `.env.example` a `.env` y llenar:

```bash
cp .env.example .env
```

| Variable | Descripción | Ejemplo |
|---|---|---|
| `KIN_SOLAR_API_URL` | URL base de la API v1 | `http://localhost:8000/api/v1` |
| `KIN_SOLAR_MCP_KEY` | Misma llave que `MCP_API_KEY` en el `.env` de Laravel | (generada) |

## Configuración en Claude Desktop

Editar `%APPDATA%\Claude\claude_desktop_config.json` (Windows) o
`~/Library/Application Support/Claude/claude_desktop_config.json` (macOS):

```json
{
  "mcpServers": {
    "kin-solar": {
      "command": "node",
      "args": ["C:\\ruta\\completa\\al\\repo\\mcp-server\\index.js"],
      "env": {
        "KIN_SOLAR_API_URL": "https://kin-solar-guatemala.duckdns.org/api/v1",
        "KIN_SOLAR_MCP_KEY": "la-misma-llave-del-.env-de-laravel"
      }
    }
  }
}
```

## Configuración en Claude Code

Agregar al `.mcp.json` del proyecto o del usuario:

```json
{
  "mcpServers": {
    "kin-solar": {
      "command": "node",
      "args": ["./mcp-server/index.js"],
      "env": {
        "KIN_SOLAR_API_URL": "https://kin-solar-guatemala.duckdns.org/api/v1",
        "KIN_SOLAR_MCP_KEY": "la-misma-llave-del-.env-de-laravel"
      }
    }
  }
}
```

## Guion de la demo en vivo

1. Abrir Claude Desktop (o Claude Code) con el servidor MCP conectado.
2. Preguntar: *"¿Cuántas granjas solares tiene K'in Solar y cuál es la generación acumulada?"*
   → Usa `kin_solar_statistics` y `kin_solar_list_farms`.
3. Pedir: *"Registra una medición para la granja 1 del período 2026-09: estimado 50000 kWh, real 48000 kWh"*
   → Usa `kin_solar_register_generation`.
4. Abrir el dashboard de K'in Solar en el navegador, ir a `/generations`, y ver el registro nuevo.

## Seguridad

- La llave de API **nunca** se hardcodea en el código ni se sube a git.
- El archivo `mcp-server/.env` está excluido por `.gitignore`.
- El endpoint de escritura tiene `throttle:10,1` (máximo 10 peticiones por minuto).
- Toda acción queda registrada en `audit_logs` atribuida al usuario sistema `mcp-agent@kinsolar.internal`.
