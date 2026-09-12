<?php

declare(strict_types=1);

use App\Services\CarbonOffsetService;

return [

    /*
    |--------------------------------------------------------------------------
    | Factor de emisión para el CO₂ evitado
    |--------------------------------------------------------------------------
    |
    | Fijado por las bases de la competencia (§14 "Datos y reglas de cálculo"):
    | 0.40 kg de CO₂ evitado por cada kWh generado con energía solar. El número
    | vive en CarbonOffsetService::CO2_KG_PER_KWH (el servicio es PHP puro, sin
    | dependencia del framework); aquí solo se expone para las vistas y el
    | componente <x-co2-info>, de modo que cálculo y explicación nunca diverjan.
    |
    */

    'co2_kg_per_kwh' => CarbonOffsetService::CO2_KG_PER_KWH,

];
