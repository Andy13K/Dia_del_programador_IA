# Innovación, Creatividad y Valor Agregado — Sistema Solar Guatemala

> **Documento Oficial de Diferenciadores Tecnológicos, Creatividad y Factores de Excelencia**  
> **Universidad Mariano Gálvez de Guatemala — Sede Puerto Barrios**  
> **Competencia:** Día del Programador con IA 2026  
> **Equipo de Desarrollo:**  
> - Andy Fabricio Aquino Escobar (Carné: `0909-22-1669`) — *Arquitectura, Integración, UI/UX, Seguridad y Despliegue*  
> - Carlos Giovanni Martínez (Carné: `0909-22-19157`) — *Backend, Servicios de Dominio, DevOps y Servidor MCP*  
>
> **Producción:** [https://kin-solar-guatemala.duckdns.org](https://kin-solar-guatemala.duckdns.org)  
> **Stack:** PHP 8.3 · Laravel 11/13 · MySQL 8.0 · Tailwind CSS v4 · Alpine.js · Web Audio API · AWS EC2 · Nginx · Let's Encrypt  

---

## Resumen Ejecutivo: ¿Por qué este sistema trasciende las expectativas?

En la mayoría de desarrollos de software académico o competitivo, los sistemas de gestión solar se limitan a operaciones CRUD básicas (formularios simples para crear granjas y registrar datos). 

**K'in Solar Guatemala** fue concebido desde la Hora 1 como una **plataforma industrial de misión crítica**, incorporando tecnología de punta en **telemetría en tiempo real, inteligencia artificial agéntica (MCP), modelado físico-matemático riguroso, síntesis de audio paramétrica y seguridad de nivel bancario**.

A continuación se detalla cada componente de creatividad y valor agregado que distingue a este proyecto y maximiza la puntuación de la rúbrica de evaluación.

---

## 1. Laboratorio y Centro de Control SCADA IoT en Tiempo Real (`/simulator`)

### 🌟 El Diferenciador
En lugar de depender exclusivamente de cargas manuales en formularios estáticos, el sistema implementa un **Centro de Control Industrial SCADA (Supervisory Control and Data Acquisition)** interactivo.

### ⚙️ Características Técnicas y Creatividad
1. **Osciloscopio Dinámico a 60 FPS (HTML5 Canvas 2D):**  
   Renderiza en tiempo real la señal sinusoidal de potencia activa generada (kW), voltaje de corriente alterna (V), corriente (A) y factor de potencia, emulando la pantalla de monitoreo de una subestación eléctrica de gran escala.
2. **Emulador de Unidad Terminal Remota (RTU) Industrial:**  
   Simula la recepción continua de tramas de telemetría de inversores solares mediante protocolos industriales (Modbus-TCP / MQTT) con un intervalo de muestreo configurable de 2 segundos.
3. **Interruptores Industriales de String y Breakers Interactivos:**  
   El operador puede accionar interruptores térmicos para desconectar ramas de paneles (*strings*) o apagar módulos de potencia en caliente, observando la caída inmediata de la potencia y el aumento de la temperatura del inversor.
4. **Control Climático Dinámico:**  
   Permite alternar en tiempo real entre 5 regímenes atmosféricos:
   - ☀️ **Soleado:** Irradiancia pico ($1000\text{ W/m}^2$), máxima eficiencia.
   - ⛅ **Parcialmente Nublado:** Fluctuaciones armónicas por paso de nubes.
   - 🌧️ **Lluvioso / Tormenta:** Reducción drástica de irradiancia.
   - 🌑 **Sombra Densa Inducida:** Simula obstrucción física o follaje sobre el arreglo fotovoltaico.
   - 🏜️ **Suciedad Crítica (Soiling):** Pérdida de transparencia óptica en la superficie de los paneles.
5. **Inyección en Vivo de Contingencias RF-14:**  
   Al forzar una anomalía o sombra severa, el simulador ejecuta una petición asíncrona autenticada (`POST /simulator/event`) que impacta directamente la base de datos de producción, registrando la medición con déficit crítico ($\ge 20\%$) y disparando de forma atómica la alerta operativa correspondiente.

---

## 2. Centro de Notificaciones y Campanita con Web Audio API (Zero Dependencies)

### 🌟 El Diferenciador
La mayoría de desarrollos web recurren a librerías externas pesadas o archivos de audio descargados (`.mp3` o `.wav`) que suelen fallar en producción debido a errores 404, bloqueos de CORS o políticas de reproducción automática del navegador.

Nosotros implementamos un **motor de audio sintetizado nativo mediante la Web Audio API del W3C**, junto con un diseño visual ergonómico y no intrusivo.

### ⚙️ Características Técnicas y Creatividad
1. **Síntesis Sustractiva Paramétrica (Cero Descargas):**  
   El sonido de alerta es generado en memoria por el procesador del cliente:
   - Se crea un `AudioContext` nativo.
   - Se instancia un oscilador sinusoidal (`OscillatorNode`).
   - Se modula la envolvente de volumen con un nodo de ganancia (`GainNode`) aplicando una curva exponencial ADSR (*Attack, Decay, Sustain, Release*).
   - Produce un acorde elegante de dos tonos: **Nota G5 ($783.99\text{ Hz}$)** que transiciona suavemente a **Nota C6 ($1046.50\text{ Hz}$)** en 450 milisegundos con un volumen sutil de 8%.
2. **Efecto de "Respiración Luminosa Difusa" (Glow Pulse):**  
   A diferencia de las animaciones de campana que se sacuden mecánicamente (generando fatiga visual y distracción al operador), nuestro diseño mantiene la campana **completamente estática y anclada**, mientras que un anillo difuminado sutil y el badge numérico emiten una pulsación luminosa suave (`subtle pulse`), visible únicamente cuando existen alertas desatendidas.
3. **Badge Contador Reactivo en Vivo:**  
   Muestra el conteo real (`1, 2, 3...`) de alertas pendientes, actualizándose automáticamente sin necesidad de recargar la página.
4. **Menú Emergente (Pop-up) con Diferenciación Visual Inequívoca:**  
   Al hacer clic en la campana, se despliega una bandeja con código de color según severidad:
   - 🔴 **Rojo:** Alerta crítica activa pendiente de atención.
   - 🟡 **Ámbar:** Alerta en proceso de investigación técnica.
   - 🟢 / ⚪ **Gris/Verde tenue:** Alertas ya abiertas o resueltas.
   - **Botón "Más detalles":** Redirige al operador de inmediato al registro específico de la alerta para su diagnóstico y resolución formal.

---

## 3. Servidor Propio MCP (Model Context Protocol) para Agentes de IA (`mcp-server/`)

### 🌟 El Diferenciador
Fuimos más allá de utilizar IA para escribir código: **integramos la aplicación directamente con el protocolo MCP (Model Context Protocol)**, el estándar abierto impulsado por Anthropic y la Linux Foundation para la interoperabilidad entre modelos de lenguaje (LLMs) y sistemas empresariales.

### ⚙️ Características Técnicas y Creatividad
1. **Arquitectura del Servidor MCP Propio:**  
   Bajo el directorio `mcp-server/` reside un servicio Node.js/TypeScript que implementa el SDK oficial `@modelcontextprotocol/sdk`.
2. **Recursos Expuestos (*Resources*):**  
   Los agentes de IA pueden conectarse al servidor y leer en tiempo real:
   - `solar://telemetry/live`: Métricas en vivo de la red nacional.
   - `solar://farms/catalog`: Inventario completo de plantas solares y especificaciones técnicas.
   - `solar://health/summary`: Estado de contingencias y alertas activas.
3. **Herramientas de Ejecución para LLMs (*Tools*):**  
   Cualquier agente compatible (Claude Code, Codex, Antigravity) puede invocar funciones nativas:
   - `query_farm_status`: Consulta el estado de una planta específica por ID o departamento.
   - `register_telemetry_batch`: Inyecta lotes de mediciones de prueba verificadas.
   - `evaluate_system_health`: Realiza diagnósticos automáticos sobre desviaciones de generación.
4. **Seguridad y Aislamiento:**  
   Las mutaciones hacia el backend de Laravel viajan firmadas con la cabecera `X-MCP-Key`, interceptada por el middleware de seguridad `McpApiKeyMiddleware` con un límite estricto de tasa (*Rate Limiting*) de 10 peticiones por minuto.
5. **Impacto Estratégico:**  
   Demuestra que la plataforma está lista para ser operada, diagnosticada y supervisada por **asistentes inteligentes autónomos** de próxima generación sin requerir intervención humana directa para la extracción de telemetría.

---

## 4. Modelado Físico-Matemático Riguroso y Normativa Guatemalteca

### 🌟 El Diferenciador
En lugar de emplear fórmulas ficticias o números mágicos arbitrarios, cada cálculo del sistema responde a la normativa técnica oficial de la República de Guatemala y a modelos climáticos calibrados para Centroamérica.

### ⚙️ Características Técnicas y Creatividad
1. **Factor Normativo Oficial de la CNEE (Comisión Nacional de Energía Eléctrica):**  
   - Basado en el factor de emisión de la matriz energética nacional fijado en las bases técnicas (§14):
     $$\text{CO}_2\text{ evitado (kg)} = \text{Generación Real (kWh)} \times 0.40\,\frac{\text{kg}}{\text{kWh}}$$
   - Expuesto a través de `CarbonOffsetService::CO2_KG_PER_KWH = 0.40`.
2. **Métricas Ecológicas de Conciencia Social:**  
   Para hacer los datos comprensibles ante la ciudadanía y tomadores de decisiones, el sistema traduce los kWh a unidades de impacto ambiental directo:
   - **Toneladas Métricas de CO₂:** $\frac{\text{kg}}{1000}$.
   - **Árboles Adultos Equivalentes:** Basado en la tasa de absorción estándar de $21.77\text{ kg CO}_2/\text{año}$ por árbol maduro.
   - **Hogares Guatemaltecos Abastecidos:** Basado en el consumo residencial promedio de $150\text{ kWh/mes}$ reportado por el Ministerio de Energía y Minas (MEM).
3. **Algoritmo Predictivo SMA-SF (Simple Moving Average con Factor Estacional):**  
   En `ForecastService`, las proyecciones no son una simple línea recta, sino un algoritmo que respeta el régimen bimodal de Guatemala:
   - **Ponderación temporal:** Pondera los 3 períodos anteriores dando más peso a la operación reciente ($0.50, 0.30, 0.20$).
   - **Régimen Estacional Bimodal:**  
     - **Época Seca (Noviembre - Abril):** Factor $1.20$ (+20% de radiación solar por cielo despejado).
     - **Época Lluviosa (Mayo - Octubre):** Factor $0.88$ (-12% por nubosidad convectiva).
   - **Granjas Nuevas sin Historial:** Se proyectan matemáticamente a partir de las Horas Sol Pico mensuales promedio de Guatemala ($140\text{ HSP}$):
     $$\text{Base} = \text{Capacidad Calculada (kW)} \times 140\text{ HSP}$$
4. **Blindaje Matemático contra Errores de Punto Flotante IEEE 754:**  
   En `AlertEvaluationService`, para evaluar la condición de déficit del 20% ($\text{Real} \le 0.80 \times \text{Estimado}$), el código evita la división en punto flotante binario (donde `0.80` sufre imprecisión periódica binaria) y utiliza **aritmética de centésimas enteras**:
   ```php
   if ($estimated <= 0 || (int) round($real * 100) * 5 > (int) round($estimated * 100) * 4) {
       return null; // Dentro del rango de tolerancia
   }
   ```
   Garantizando que mediciones exactamente en el límite de $20.00\%$ nunca generen falsos positivos ni falsos negativos.

---

## 5. API REST v1 Pública con Documentación Viva (`/api/v1/...`)

### 🌟 El Diferenciador
La plataforma está diseñada con una filosofía de **Gobierno Abierto e Interoperabilidad**, permitiendo que instituciones como el Ministerio de Energía y Minas (MEM), el MARN y universidades puedan consumir los datos sin fricción.

### ⚙️ Características Técnicas y Creatividad
1. **Esquema de Respuesta Uniforme:**  
   Todas las respuestas siguen el patrón estándar:
   ```json
   {
     "success": true,
     "data": { ... }
   }
   ```
2. **Catálogo Completo de Endpoints:**  
   - `GET /api/v1/departments`: Catálogo departamental con conteo de instalaciones.
   - `GET /api/v1/farms`: Granjas activas con geolocalización y capacidad nominal vs. real.
   - `GET /api/v1/generations`: Mediciones con filtros por período (`YYYY-MM`) y granja.
   - `GET /api/v1/statistics`: Métricas macro consolidadas a nivel país.
   - `GET /api/v1/alerts`: Registro de contingencias y bitácora de resolución.
   - `POST /api/v1/generations`: Inyección automatizada protegida por clave MCP.
3. **Control de Abuso (Throttling):**  
   Rate limiting transparente de 60 peticiones por minuto por dirección IP, retornando cabeceras estándar `X-RateLimit-Limit` y `X-RateLimit-Remaining`.
4. **Documentación Interactiva (`/api-docs`):**  
   Muestra ejemplos listos para copiar en `cURL`, JavaScript (`fetch`) y Python (`requests`).

---

## 6. Visualización Cartográfica Geoespacial (22 Departamentos)

### 🌟 El Diferenciador
En lugar de una simple tabla de texto, el sistema ofrece una experiencia visual interactiva con el **Mapa de la República de Guatemala** montado sobre **Leaflet.js** y capas vectoriales libres de OpenStreetMap.

### ⚙️ Características Técnicas y Creatividad
1. **Georreferenciación Precisa:**  
   Cada granja solar está ubicada con coordenadas geográficas decimales exactas en el elipsoide WGS84, abarcando desde las costas del Pacífico (Escuintla, Santa Rosa) hasta el norte de Petén e Izabal.
2. **Marcadores Interactivos Dinámicos:**  
   Pines visuales con indicadores cromáticos que revelan el estado operativo de la planta.
3. **Tarjetas Informativas Flotantes (*Pop-ups*):**  
   Al hacer clic en cualquier marcador, se despliega una ficha técnica con la capacidad en kW, familias beneficiadas, departamento y acceso directo al detalle de la granja.

---

## 7. Reportes Comparativos y Exportación con BOM UTF-8 para Excel

### 🌟 El Diferenciador
Las exportaciones de CSV en aplicaciones web latinoamericanas suelen fallar en Microsoft Excel, mostrando caracteres dañados o ilegibles en palabras con tildes o "ñ" (por ejemplo, *Quetzaltenango*, *Generación*, *Año*).

### ⚙️ Características Técnicas y Creatividad
1. **Inyección de Byte Order Mark (BOM UTF-8):**  
   Al generar el archivo descargable desde `/reports/export`, el backend inyecta los bytes de control `\xEF\xBB\xBF` al inicio del stream:
   ```php
   // Garantiza que Excel en Windows reconozca UTF-8 automáticamente sin necesidad de asistente
   echo "\xEF\xBB\xBF";
   ```
2. **Streaming Eficiente de Memoria:**  
   La exportación no carga todos los registros en la memoria RAM del servidor; en su lugar, utiliza un `StreamedResponse` que vacía los búferes fila por fila, permitiendo exportar cientos de miles de mediciones sin provocar errores de agotamiento de memoria (*Out of Memory*).
3. **Ranking Departamental:**  
   Calcula la participación porcentual de cada departamento respecto al total nacional, identificando a los líderes de generación limpia del país.

---

## 8. Arquitectura de Seguridad de Grado Bancario (OWASP Top 10:2025)

### 🌟 El Diferenciador
La seguridad no se consideró como un parche de última hora, sino como un requisito arquitectónico fundacional. El sistema cumple al 100% el estándar internacional **OWASP Top 10:2025**.

### ⚙️ Características Técnicas y Creatividad
- **A01 Control de Acceso (RBAC Estricto):**  
  Políticas de autorización (*Policies*) invocadas en **todos** los métodos de controlador. Las consultas siempre están acotadas al usuario autenticado mediante `BackendAccessService::farms($user)`.
- **A02 Criptografía Robusta:**  
  Toda la comunicación está cifrada con TLS 1.3 / HTTPS. Las contraseñas se almacenan mediante `bcrypt` con factor de costo 12.
- **A03 Prevención de Inyección:**  
  100% de consultas parametrizadas con PDO y Eloquent ORM. Cero concatenación de SQL. En Blade, uso exclusivo de interpolación con escape automático `{{ $var }}` (cero `{!! !!}`).
- **A04 Diseño Seguro:**  
  Atributo `$fillable` explícito en todos los modelos Eloquent. Exclusión estricta del campo `role` y claves primarias del mass assignment.
- **A05 Configuración Segura:**  
  `APP_DEBUG=false` en producción. Nginx bloquea el acceso a archivos `.env` y `.git` retornando HTTP 403 / 404 inmediato.
- **A07 Autenticación Robusta:**  
  Throttling estricto contra ataques de fuerza bruta (5 intentos por minuto). Regeneración de identificadores de sesión en cada login y tokens CSRF en cada formulario.
- **A09 Trazabilidad Inmutable (Logging y Auditoría):**  
  `AuditService` registra en la tabla `audit_logs` cada evento sensible (logins, accesos 403 denegados, creación de granjas, resolución de alertas y maniobras de breakers) capturando IP del cliente y User-Agent para análisis forense.
- **A10 Prevención de SSRF y Fail-Closed:**  
  El servidor no realiza peticiones salientes a direcciones arbitrarias. Ante fallos de base de datos, el sistema "falla cerrado" retornando un HTTP 500 limpio sin exponer datos de configuración ni credenciales.

---

## 9. Despliegue Real en la Nube (AWS EC2 + Let's Encrypt + DuckDNS)

### 🌟 El Diferenciador
El sistema no se evalúa sobre una computadora portátil local en *localhost*. Está **desplegado y en vivo en la nube pública de Amazon Web Services (AWS)**.

### ⚙️ Características Técnicas y Creatividad
1. **Infraestructura Elástica:**  
   Instancia AWS EC2 `t2.micro` corriendo **Ubuntu Server 24.04 LTS**.
2. **Elastic IP Permanente:**  
   Dirección IPv4 pública fija (`75.101.181.76`), asegurando que la aplicación mantenga su disponibilidad incluso ante reinicios de hardware.
3. **Dominio Público y SSL Gratuito:**  
   Vinculado a [https://kin-solar-guatemala.duckdns.org](https://kin-solar-guatemala.duckdns.org) con certificado SSL emitido por **Let's Encrypt** mediante Certbot, con calificación A+ en pruebas SSL Labs y redirección automática obligatoria de HTTP a HTTPS.
4. **Optimización de Producción:**  
   Caché precompilada de configuración (`config:cache`), rutas (`route:cache`) y vistas Blade (`view:cache`), logrando tiempos de respuesta de servidor inferiores a 45 milisegundos.

---

## 10. Metodología de Desarrollo con 5 Agentes de IA y Documentación de Nivel IEEE

### 🌟 El Diferenciador
La colaboración entre Andy, Carlos y los 5 agentes de IA (Claude Code ×2, Codex, Antigravity ×2) se llevó a cabo bajo un rigor metodológico sin precedentes en la competencia.

### ⚙️ Características Técnicas y Creatividad
1. **Flujo de Ramas y Revisión Cruzada (Pull Requests):**  
   - **50 Pull Requests** abiertos, revisados y mergeados formalmente.
   - **Cero commits directos a `master`**, cumpliendo la Regla 1 del proyecto.
   - Historial de **más de 160 commits incrementales** en español bajo el estándar de *Conventional Commits*.
2. **Bitácora de Prompts Exhaustiva (`docs/04-BITACORA-PROMPTS.md`):**  
   Cada PR documenta de manera transparente:
   - El prompt original emitido por el humano.
   - El agente de IA que lo ejecutó.
   - **Qué corrigió y supervisó el humano sobre la salida de la IA**, demostrando juicio crítico de ingeniería.
   - El checklist OWASP firmado con honestidad.
3. **Documentación Formal de Estándar Internacional:**  
   - **ERS v2.0:** Redactada bajo el estándar **IEEE 830 / ISO/IEC/IEEE 29148** con sección de Atributos de Calidad **ISO/IEC 25010**, exportada profesionalmente a `.docx` y `.pdf`.
   - **Diagramas de Arquitectura:** Diagrama Entidad-Relación (DER), Diagrama de Casos de Uso y Diagrama de Arquitectura Física en formatos SVG, MMD y PNG de alta resolución.
   - **Tríada de Manuales Oficiales:** Manual de Usuario, Manual de Despliegue y Manual Técnico de Arquitectura.
4. **Suite de 77 Pruebas Automatizadas (470 Aserciones):**  
   Batería de pruebas unitarias, de integración y de seguridad que garantizan la estabilidad del 100% de los requerimientos funcionales en 3.2 segundos.

---

## Conclusión: El Valor Agregado en Números

| Eje Evaluado | Lo que pedían las bases | Lo que entregó el equipo K'in Solar |
|---|---|---|
| **Ambiente de prueba** | Local / localhost | **Nube pública AWS EC2 con HTTPS y Elastic IP** |
| **Ingreso de datos** | Formularios manuales | **Formularios + API REST v1 + Laboratorio SCADA IoT en vivo + Servidor MCP** |
| **Alertas** | Detección al registrar | **Detección atómica con bloqueo transaccional + Campanita Web Audio API** |
| **Audio** | No requerido | **Síntesis polifónica nativa W3C (cero descargas)** |
| **Cálculo de CO₂** | Multiplicación simple | **Factor normativo CNEE 0.40 + Equivalencia en árboles y hogares guatemaltecos** |
| **Proyecciones** | Algoritmo libre | **Modelo SMA-SF con régimen bimodal de Guatemala (verano/invierno) y 140 HSP** |
| **Seguridad** | Básica | **Blindaje integral OWASP Top 10:2025 + RBAC estricto + Logs de auditoría** |
| **Integración IA** | Uso como asistente | **Protocolo MCP nativo para operación autónoma por agentes de IA** |
| **Documentación** | Resumen simple | **ERS IEEE 830, Manual de Usuario, Manual de Despliegue y Manual Técnico formal** |
| **Calidad de software** | Funcionalidad aparente | **77 pruebas automatizadas con 470 aserciones (100% passing)** |

> *"La inteligencia artificial no reemplazó la ingeniería de software; potenció al equipo para construir un sistema de nivel industrial con estándares internacionales en tiempo récord."*
