# Carga adicional de granjas de demostración

`SolarCoverageDemoSeeder` agrega una granja de demostración por departamento que no tenga ningún registro de granja, incluidos registros eliminados lógicamente. Utiliza las coordenadas de referencia del catálogo departamental y marca cada nombre con `Demo`; no representa ubicaciones verificadas de instalaciones reales.

## Resultado previsto en producción

Consulta de la API pública del 12/09/2026: **15 granjas en 12 departamentos**. La carga añadiría estas diez granjas, dejando **25 granjas en los 22 departamentos** si los datos no cambian antes de ejecutarla:

| Departamento | Latitud | Longitud |
|---|---:|---:|
| El Progreso | 14.8653 | -90.0764 |
| Huehuetenango | 15.3197 | -91.4708 |
| Jalapa | 14.6347 | -89.9889 |
| Quiché | 15.0306 | -91.1494 |
| Retalhuleu | 14.5361 | -91.6778 |
| Sacatepéquez | 14.5586 | -90.7339 |
| San Marcos | 14.9639 | -91.7944 |
| Santa Rosa | 14.2783 | -90.2989 |
| Sololá | 14.7722 | -91.1833 |
| Totonicapán | 14.9117 | -91.3611 |

Las diez coordenadas fueron contrastadas con los polígonos de `public/data/guatemala-departments.geojson`: cada punto está dentro del departamento asignado.

## Ejecución

Después de integrar y desplegar el PR, desde la raíz de la aplicación del servidor (donde está `artisan`), ejecutar con el usuario habitual de despliegue:

```bash
php artisan db:seed --class=SolarCoverageDemoSeeder --force
```

Este comando selecciona únicamente el nuevo seeder. No requiere migraciones, reiniciar datos ni ejecutar `DatabaseSeeder` o `SolarDemoSeeder`.

La salida muestra cuántas granjas se agregaron y sus paneles y capacidad. Repetir el comando no duplica ni actualiza las granjas existentes. Si se creó alguna granja manualmente en un departamento antes de la ejecución, ese departamento se omite automáticamente.

## Datos conservados y requisitos

- Requiere el catálogo de 22 departamentos, un administrador existente y al menos un modelo de panel activo con potencia positiva. No crea cuentas ni modifica contraseñas, roles o modelos de panel.
- Asigna las nuevas granjas al primer administrador existente y utiliza el primer panel activo disponible, con entre 600 y 1.800 unidades demo por granja. La capacidad se calcula con la potencia del panel real del catálogo.
- No modifica granjas, paneles asignados, mediciones, alertas ni proyecciones existentes. No reactiva registros eliminados.
- No inventa generación histórica ni familias beneficiadas: las nuevas granjas comienzan con cero familias y sin mediciones.
- La carga es transaccional: si falla algún registro, se revierten todas las inserciones de esta ejecución. Bloquea los departamentos durante la carga para serializar ejecuciones concurrentes del mismo seeder.

## Verificación

1. Consultar `/api/v1/farms` y `/api/v1/departments` antes y después para contrastar nombres, coordenadas, capacidad y cobertura.
2. Iniciar sesión y abrir `/map`. Verificar los nuevos pines, sus fichas y el filtro departamental.
3. Con los datos observados, la salida esperada es `Granjas demo agregadas: 10`; una segunda ejecución debe mostrar `0`.

Pruebas locales: 77 pruebas / 470 aserciones correctas; cuatro pruebas específicas de cobertura, conservación, repetición y rollback. Una copia de los registros públicos pasó de 15 a 25 granjas y una segunda ejecución agregó cero. Navegador local: 25 pines, detalle de Huehuetenango y filtro de Sololá correctos.

**Estado al preparar el PR:** validado localmente; la carga y comprobación final en producción están pendientes del acceso SSH o ejecución por el responsable del servidor.
