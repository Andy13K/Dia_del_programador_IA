# Manual de Usuario — Sistema Solar Guatemala

> **Documento dirigido al jurado evaluador y a usuarios finales del sistema.**
> No se requiere conocimiento técnico previo para navegar la plataforma.
>
> **URL de la aplicación:** https://kin-solar-guatemala.duckdns.org
>
> **Tiempo estimado de exploración completa:** 15–20 minutos

---

## Índice

1. [Acceso al sistema](#1-acceso-al-sistema)
2. [Dashboard Nacional (Pantalla principal)](#2-dashboard-nacional)
3. [Mapa Interactivo de los 22 Departamentos](#3-mapa-interactivo)
4. [Gestión de Granjas Solares](#4-gestión-de-granjas-solares)
5. [Catálogo de Paneles Solares](#5-catálogo-de-paneles-solares)
6. [Registrar una Medición de Generación](#6-registrar-una-medición-de-generación)
7. [Bandeja de Alertas y Resolución](#7-bandeja-de-alertas-y-resolución)
8. [Reportes Comparativos Departamentales](#8-reportes-comparativos-departamentales)
9. [Proyecciones de Generación Futura](#9-proyecciones-de-generación-futura)
10. [API REST Pública](#10-api-rest-pública)
11. [Diferencias por Rol](#11-diferencias-por-rol)
12. [Preguntas frecuentes del jurado](#12-preguntas-frecuentes-del-jurado)

---

## 1. Acceso al sistema

### Paso 1 — Abrir el navegador

Ingresar a **https://kin-solar-guatemala.duckdns.org** en cualquier navegador moderno (Chrome, Firefox, Edge, Safari).

La página de inicio redirige automáticamente al formulario de inicio de sesión.

### Paso 2 — Iniciar sesión

| Campo | Valor para el jurado |
|---|---|
| Correo electrónico | `evaluador@umg.edu.gt` |
| Contraseña | *entregada por el equipo en un canal separado* (no se publica en este manual ni en el repositorio, OWASP A02/A07) |

> 💡 **Para la demo completa** (incluyendo creación y edición de registros), usar el administrador:
> `admin@solarguatemala.gob.gt` — misma indicación: la contraseña se entrega aparte.

### Paso 3 — Dashboard

Tras autenticarse correctamente, el sistema redirige al **Dashboard Nacional** con los 6 indicadores macro del sistema solar de Guatemala.

### Cerrar sesión

Hacer clic en el nombre de usuario (esquina superior derecha) → **"Cerrar sesión"**.
La sesión expira automáticamente tras 120 minutos de inactividad.

---

## 2. Dashboard Nacional

La primera pantalla tras el login presenta el estado en tiempo real de toda la red solar nacional.

### Indicadores KPI (tarjetas superiores)

| Tarjeta | Qué muestra |
|---|---|
| 🏭 **Granjas activas** | Total de instalaciones solares registradas en el sistema |
| ⚡ **Paneles instalados** | Total de paneles físicos en todas las granjas |
| 🔋 **Capacidad total (kW)** | Suma de `cantidad × potencia_nominal_kw` por granja |
| ☀️ **kWh generados** | Energía eléctrica real acumulada de todas las mediciones |
| 👨‍👩‍👧‍👦 **Familias beneficiadas** | Conteo consolidado de hogares con acceso a energía solar |
| 🌿 **CO₂ evitado** | Calculado con factor normativo: `kWh_real × 0.40 kg/kWh` |

### Gráfica mensual comparativa

Debajo de los KPIs aparece un gráfico de líneas que compara:
- **Línea azul:** kWh generados esperados (estimados)
- **Línea verde:** kWh generados reales

Cuando la línea real cae un **20% o más** por debajo de la estimada, el sistema dispara una alerta automática.

### Ranking departamental

Tabla con los 22 departamentos ordenados de mayor a menor generación acumulada.
Incluye capacidad instalada (kW), kWh totales, CO₂ evitado y familias beneficiadas.

### Mini-mapa

Vista compacta del mapa interactivo con los pines de granjas. Hacer clic en **"Ver mapa completo"** para la vista detallada.

---

## 3. Mapa Interactivo

Acceso: menú lateral → **"Mapa Interactivo"** o directamente a `/map`.

### Qué se puede ver

El mapa centrado en Guatemala muestra **marcadores georreferenciados** de cada granja solar registrada. Los pines están diferenciados por color según la capacidad instalada y el estado de alerta:

| Color del pin | Significado |
|---|---|
| 🟡 Amarillo (amber) | Granja activa — capacidad media |
| 🟢 Verde (emerald) | Granja activa — alta capacidad |
| 🔴 Rojo (rose) | Granja con alerta activa de déficit |
| ⚫ Gris | Granja inactiva o en mantenimiento |

### Cómo interactuar

1. **Zoom:** rueda del ratón o botones `+` / `−`
2. **Desplazamiento:** clic y arrastre sobre el mapa
3. **Ver detalles de una granja:** hacer clic en cualquier marcador → aparece un popup con:
   - Nombre de la granja
   - Departamento
   - Capacidad instalada (kW)
   - kWh generados acumulados
   - Familias beneficiadas
   - CO₂ evitado (kg)
   - Estado actual y alertas activas

### Filtros laterales

El panel izquierdo permite filtrar las granjas por:
- **Departamento** (los 22 de Guatemala)
- **Estado** (activo / inactivo / mantenimiento)
- **Nivel de alerta** (solo las que tienen alertas activas)

Al cambiar un filtro, el mapa actualiza los marcadores instantáneamente sin recargar la página.

---

## 4. Gestión de Granjas Solares

Acceso: menú lateral → **"Granjas Solares"** o `/farms`.

> 🔒 Ver granjas: todos los roles. Crear/editar/eliminar: solo `admin` y `operador`.

### Ver el listado de granjas

La tabla muestra todas las granjas con:
- Nombre y departamento
- Capacidad instalada total (kW) — calculada automáticamente
- Número de paneles asignados
- Familias beneficiadas
- Estado (badge de color)
- Acciones: Ver · Editar · Eliminar (según el rol)

**Filtros disponibles:** por departamento (selector), por estado (selector) y búsqueda por nombre.

### Ver el detalle de una granja

Hacer clic en **"Ver"** o en el nombre de la granja → ficha técnica completa:
- Datos generales (nombre, departamento, coordenadas GPS)
- Capacidad instalada calculada automáticamente
- Lista de paneles asignados con marca, modelo y cantidad
- Historial de mediciones de generación mensuales
- Alertas activas o históricas
- Proyecciones de generación futura

### Crear una nueva granja (rol admin/operador)

1. Clic en **"Nueva granja"** (botón azul, esquina superior derecha)
2. Completar el formulario:
   - **Nombre** de la instalación
   - **Departamento** (selector con los 22 departamentos de Guatemala)
   - **Latitud y Longitud** (coordenadas GPS — se puede obtener de Google Maps)
   - **Familias beneficiadas** (número entero)
   - **Estado** (activo / inactivo / mantenimiento)
3. En la sección **"Paneles asignados"**, seleccionar cada modelo de panel y la cantidad instalada.
   - La capacidad total (kW) se calcula automáticamente al agregar paneles.
4. Clic en **"Guardar granja"**.

El sistema valida todos los campos antes de guardar. Si hay errores, aparecen mensajes en rojo debajo de cada campo.

### Editar o eliminar

- **Editar:** clic en el ícono de lápiz → mismo formulario con datos precargados
- **Eliminar:** clic en el ícono de basura → confirmación de doble clic requerida
  - El borrado es lógico (SoftDelete): la granja desaparece del listado pero los datos históricos se conservan.

---

## 5. Catálogo de Paneles Solares

Acceso: menú lateral → **"Paneles Solares"** o `/panels`.

> 🔒 Ver paneles: todos los roles. Crear/editar/eliminar: solo `admin` y `operador`.

### Ver el catálogo

Tabla con todos los modelos de paneles disponibles:
- Marca y modelo
- Potencia nominal (kW por unidad)
- Estado del panel (activo / inactivo / mantenimiento)
- Número de granjas que lo usan

### Agregar un nuevo modelo de panel

1. Clic en **"Nuevo panel"**
2. Completar:
   - **Marca** (ej.: "SunPower", "Jinko Solar")
   - **Modelo** (ej.: "SPR-MAX3-400")
   - **Potencia nominal (kW)** (ej.: 0.400 para un panel de 400 W)
   - **Estado**
3. Clic en **"Guardar panel"**

---

## 6. Registrar una Medición de Generación

Acceso: menú lateral → **"Generación de Energía"** → **"Registrar medición"** o `/generations/create`.

> 🔒 Solo `admin` y `operador` pueden registrar mediciones.

Este es el módulo central del sistema: registra cuánta energía solar generó cada granja en un período mensual.

### Pasos

1. **Seleccionar la granja** (selector desplegable con todas las granjas activas)
2. **Período** — formato `YYYY-MM`, por ejemplo `2026-08` para agosto 2026
3. **Fecha de registro** (la fecha real del día de la lectura)
4. **kWh estimados** — la generación esperada según la capacidad instalada y las horas sol del período
5. **kWh reales** — la lectura real del medidor de energía

### Cálculo automático en tiempo real

Al ingresar los valores de kWh estimados y reales, el formulario calcula **en tiempo real**:
- **CO₂ evitado:** `kWh_reales × 0.40 kg/kWh` (factor CNEE Guatemala)
- **Desviación porcentual:** `((kWh_estimados − kWh_reales) / kWh_estimados) × 100`
- **Indicador de alerta:** si la desviación ≥ 20%, aparece un aviso naranja antes de guardar

### Alerta automática

Si al guardar la medición los kWh reales son ≤ 80% de los estimados, el sistema:
1. Crea automáticamente un registro en la tabla `generation_alerts`
2. La granja aparece con indicador de alerta en el mapa
3. El contador de alertas activas en el menú lateral se incrementa

> 💡 El operador puede agregar **notas** opcionales para documentar la causa del déficit (nubosidad, mantenimiento, avería técnica).

---

## 7. Bandeja de Alertas y Resolución

Acceso: menú lateral → **"Alertas"** o `/alerts`.

Las alertas se disparan automáticamente cuando la generación real es inferior al **80%** de la esperada (déficit ≥ 20%).

### Columnas del listado

| Columna | Descripción |
|---|---|
| Granja | Nombre e ícono de departamento |
| Período | Mes y año de la medición afectada |
| kWh estimados | Lo que se esperaba generar |
| kWh reales | Lo que realmente se generó |
| Desviación | Porcentaje exacto de déficit, con badge rojo |
| Estado | Activa (rojo) o Resuelta (verde) |
| Acciones | Ver detalle / Resolver |

### Ver el detalle de una alerta

Clic en **"Ver"** → página de detalle con:
- Datos completos de la medición que la disparó
- Línea de tiempo: cuándo se detectó la alerta
- Campo para escribir la nota de resolución

### Resolver una alerta (rol admin/operador)

1. Clic en **"Resolver"** en el listado, o en el botón del detalle
2. En el modal, escribir la **nota de resolución** (causa, acción correctiva tomada)
3. Clic en **"Confirmar resolución"**

La alerta pasa a estado "Resuelta" y queda registrada con el usuario que la resolvió, la fecha y la nota. El intento de resolución también se registra en el log de auditoría.

---

## 8. Reportes Comparativos Departamentales

Acceso: menú lateral → **"Reportes"** o `/reports`.

### Matriz departamental

Tabla completa con los 22 departamentos de Guatemala, mostrando para cada uno:

| Columna | Descripción |
|---|---|
| Departamento | Nombre |
| Granjas | Número de instalaciones |
| Paneles | Total de unidades físicas |
| Capacidad (kW) | Suma de capacidad instalada |
| kWh generados | Energía acumulada |
| CO₂ evitado (kg) | Emisiones evitadas en kilogramos |
| CO₂ evitado (ton) | Misma cifra en toneladas métricas |
| Familias | Total de hogares beneficiados |

La tabla está ordenada por generación descendente (el departamento con más producción aparece primero).

### Exportar a CSV

Clic en el botón **"Exportar CSV"** → descarga automática de un archivo compatible con Excel (BOM UTF-8 incluido para tildes correctas).

### Detalle por departamento

Clic en el nombre de cualquier departamento → vista detallada con la lista de granjas de ese departamento y su historial mensual de generación.

---

## 9. Proyecciones de Generación Futura

Acceso: menú lateral → **"Proyecciones"** o `/forecasts`.

> 🔒 Ver proyecciones: todos los roles. Generar proyección: solo `admin` y `operador`.

### ¿Cómo funciona el algoritmo?

El sistema usa un **Promedio Móvil Ponderado con Factor de Estacionalidad Solar Guatemalteca (SMA-SF)**:

- Analiza el historial de generación real de los últimos meses de cada granja
- Aplica mayor peso a los meses más recientes
- Ajusta el resultado con un **factor de estacionalidad** según el clima de Guatemala:
  - **Época seca (noviembre–abril):** factor 1.15–1.25 (más irradiancia solar)
  - **Época lluviosa (mayo–octubre):** factor 0.85–0.92 (nubosidad frecuente)
- Corrige por cambios en la capacidad instalada de la granja

### Generar una nueva proyección

1. Seleccionar la **granja** en el selector
2. Seleccionar el **mes objetivo** (período futuro a proyectar)
3. Clic en **"Calcular proyección"**
4. El sistema muestra el kWh proyectado con su justificación y la comparación vs. el historial real

### Comparación ex-post

Cuando el período proyectado ya pasó y se registró la medición real, la tabla muestra automáticamente el margen de error del algoritmo.

---

## 10. API REST Pública

Acceso: menú lateral → **"API REST"** o `/api-docs`, también directamente en `/api/v1/...`.

La API es **pública** (no requiere autenticación). Devuelve datos en formato JSON uniforme:

```json
{
  "success": true,
  "data": [...]
}
```

### Endpoints disponibles

| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/api/v1/departments` | Lista los 22 departamentos con totales agregados |
| `GET` | `/api/v1/departments/{id}` | Detalle de un departamento con sus granjas |
| `GET` | `/api/v1/farms` | Lista de granjas con capacidad y ubicación GPS |
| `GET` | `/api/v1/farms/{id}` | Detalle de granja con paneles y mediciones |
| `GET` | `/api/v1/generations` | Histórico de generación y CO₂ evitado |
| `GET` | `/api/v1/statistics` | Totales nacionales (kW, kWh, CO₂, familias, alertas) |
| `GET` | `/api/v1/alerts` | Listado de alertas activas |

### Ejemplo de uso

```bash
# Ver estadísticas nacionales
curl https://kin-solar-guatemala.duckdns.org/api/v1/statistics

# Ver todas las granjas
curl https://kin-solar-guatemala.duckdns.org/api/v1/farms

# Ver detalle del departamento 1 (Guatemala)
curl https://kin-solar-guatemala.duckdns.org/api/v1/departments/1
```

La página `/api-docs` muestra la documentación interactiva con ejemplos de respuesta JSON para cada endpoint.

---

## 11. Diferencias por Rol

| Función | admin | operador | visualizador |
|---|---|---|---|
| Ver Dashboard y KPIs | ✅ | ✅ | ✅ |
| Ver Mapa Interactivo | ✅ | ✅ | ✅ |
| Ver listado de granjas | ✅ | ✅ | ✅ |
| **Crear / editar granjas** | ✅ | ✅ | ❌ |
| **Eliminar granjas** | ✅ | ✅ | ❌ |
| Ver catálogo de paneles | ✅ | ✅ | ✅ |
| **Crear / editar paneles** | ✅ | ✅ | ❌ |
| Ver mediciones de generación | ✅ | ✅ | ✅ |
| **Registrar nueva medición** | ✅ | ✅ | ❌ |
| Ver alertas activas | ✅ | ✅ | ✅ |
| **Resolver alertas** | ✅ | ✅ | ❌ |
| Ver reportes y exportar CSV | ✅ | ✅ | ✅ |
| Ver y generar proyecciones | ✅ | ✅ | ✅ |
| **Generar proyección nueva** | ✅ | ✅ | ❌ |
| Consultar API REST | ✅ | ✅ | ✅ |
| **Gestión de usuarios** | ✅ | ❌ | ❌ |

> 🔒 Intentar acceder a una función sin el permiso correspondiente devuelve un error **403 Prohibido** y registra el intento en el log de auditoría.

---

## 12. Preguntas frecuentes del jurado

**¿Se puede registrar un panel desde el celular?**
Sí. La interfaz es totalmente responsiva y funciona en resoluciones desde 360 px (móvil) hasta 1440 px (escritorio).

**¿Qué pasa si ingreso kWh reales mayores a los estimados?**
El sistema guarda la medición normalmente (superávit de generación). No se dispara ninguna alerta; al contrario, el badge de desviación aparece en verde.

**¿Cómo se calcula el CO₂ evitado en toneladas?**
`CO₂_toneladas = CO₂_kg / 1000`. El factor base es `0.40 kg de CO₂ por kWh real generado`, conforme a la norma de la Comisión Nacional de Energía Eléctrica (CNEE) de Guatemala.

**¿Puedo ver el historial de auditoría?**
El `admin` puede consultar el log de auditoría directamente en la base de datos. La tabla `audit_logs` registra usuario, acción, modelo afectado, IP y timestamp de cada operación crítica.

**¿Qué hacer si la URL no carga?**
Contactar al equipo. Se tiene preparado un plan de contingencia con Cloudflare Tunnel sobre Laragon como respaldo, y un video de demostración grabado el día anterior.
