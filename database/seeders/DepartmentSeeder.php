<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Los 22 departamentos de Guatemala con coordenadas de su cabecera departamental.
     *
     * @var list<array{name: string, code: string, latitude: float, longitude: float}>
     */
    private const DEPARTMENTS = [
        ['name' => 'Guatemala', 'code' => 'GUA', 'latitude' => 14.6349, 'longitude' => -90.5069],
        ['name' => 'Quetzaltenango', 'code' => 'QUE', 'latitude' => 14.8347, 'longitude' => -91.5181],
        ['name' => 'Escuintla', 'code' => 'ESC', 'latitude' => 14.3009, 'longitude' => -90.7850],
        ['name' => 'Izabal', 'code' => 'IZB', 'latitude' => 15.7278, 'longitude' => -88.5944],
        ['name' => 'Petén', 'code' => 'PET', 'latitude' => 16.9200, 'longitude' => -89.8900],
        ['name' => 'Alta Verapaz', 'code' => 'AVE', 'latitude' => 15.4700, 'longitude' => -90.3700],
        ['name' => 'Baja Verapaz', 'code' => 'BVE', 'latitude' => 15.1000, 'longitude' => -90.3167],
        ['name' => 'Chimaltenango', 'code' => 'CHM', 'latitude' => 14.6611, 'longitude' => -90.8194],
        ['name' => 'Chiquimula', 'code' => 'CHQ', 'latitude' => 14.7978, 'longitude' => -89.5439],
        ['name' => 'El Progreso', 'code' => 'PRO', 'latitude' => 14.8653, 'longitude' => -90.0764],
        ['name' => 'Huehuetenango', 'code' => 'HUE', 'latitude' => 15.3197, 'longitude' => -91.4708],
        ['name' => 'Jalapa', 'code' => 'JAL', 'latitude' => 14.6347, 'longitude' => -89.9889],
        ['name' => 'Jutiapa', 'code' => 'JUT', 'latitude' => 14.2817, 'longitude' => -89.8958],
        ['name' => 'Retalhuleu', 'code' => 'RET', 'latitude' => 14.5361, 'longitude' => -91.6778],
        ['name' => 'Sacatepéquez', 'code' => 'SAC', 'latitude' => 14.5586, 'longitude' => -90.7339],
        ['name' => 'San Marcos', 'code' => 'SMA', 'latitude' => 14.9639, 'longitude' => -91.7944],
        ['name' => 'Santa Rosa', 'code' => 'SRO', 'latitude' => 14.2783, 'longitude' => -90.2989],
        ['name' => 'Sololá', 'code' => 'SOL', 'latitude' => 14.7722, 'longitude' => -91.1833],
        ['name' => 'Suchitepéquez', 'code' => 'SUC', 'latitude' => 14.5342, 'longitude' => -91.5033],
        ['name' => 'Totonicapán', 'code' => 'TOT', 'latitude' => 14.9117, 'longitude' => -91.3611],
        ['name' => 'Zacapa', 'code' => 'ZAC', 'latitude' => 14.9722, 'longitude' => -89.5306],
        ['name' => 'Quiché', 'code' => 'QUI', 'latitude' => 15.0306, 'longitude' => -91.1494],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::DEPARTMENTS as $department) {
            Department::query()->updateOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'latitude' => $department['latitude'],
                    'longitude' => $department['longitude'],
                ],
            );
        }
    }
}
