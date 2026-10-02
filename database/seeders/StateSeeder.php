<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        State::query()->upsert([
            ['clave' => 'AGS', 'clave_curp' => 'AS', 'nombre' => 'Aguascalientes'],
            ['clave' => 'BCN', 'clave_curp' => 'BC', 'nombre' => 'Baja California'],
            ['clave' => 'BCS', 'clave_curp' => 'BS', 'nombre' => 'Baja California Sur'],
            ['clave' => 'CAM', 'clave_curp' => 'CC', 'nombre' => 'Campeche'],
            ['clave' => 'CHH', 'clave_curp' => 'CH', 'nombre' => 'Chihuahua'],
            ['clave' => 'CHP', 'clave_curp' => 'CS', 'nombre' => 'Chiapas'],
            ['clave' => 'CMX', 'clave_curp' => 'DF', 'nombre' => 'Ciudad de México'],
            ['clave' => 'COA', 'clave_curp' => 'CL', 'nombre' => 'Coahuila'],
            ['clave' => 'COL', 'clave_curp' => 'CM', 'nombre' => 'Colima'],
            ['clave' => 'DGO', 'clave_curp' => 'DG', 'nombre' => 'Durango'],
            ['clave' => 'GRO', 'clave_curp' => 'GR', 'nombre' => 'Guerrero'],
            ['clave' => 'GTO', 'clave_curp' => 'GT', 'nombre' => 'Guanajuato'],
            ['clave' => 'HGO', 'clave_curp' => 'HG', 'nombre' => 'Hidalgo'],
            ['clave' => 'JAL', 'clave_curp' => 'JC', 'nombre' => 'Jalisco'],
            ['clave' => 'MEX', 'clave_curp' => 'MC', 'nombre' => 'Estado de México'],
            ['clave' => 'MIC', 'clave_curp' => 'MN', 'nombre' => 'Michoacán'],
            ['clave' => 'MOR', 'clave_curp' => 'MS', 'nombre' => 'Morelos'],
            ['clave' => 'NAY', 'clave_curp' => 'NT', 'nombre' => 'Nayarit'],
            ['clave' => 'NLE', 'clave_curp' => 'NL', 'nombre' => 'Nuevo León'],
            ['clave' => 'OAX', 'clave_curp' => 'OC', 'nombre' => 'Oaxaca'],
            ['clave' => 'PUE', 'clave_curp' => 'PL', 'nombre' => 'Puebla'],
            ['clave' => 'QUE', 'clave_curp' => 'QT', 'nombre' => 'Querétaro'],
            ['clave' => 'ROO', 'clave_curp' => 'QR', 'nombre' => 'Quintana Roo'],
            ['clave' => 'SIN', 'clave_curp' => 'SL', 'nombre' => 'Sinaloa'],
            ['clave' => 'SLP', 'clave_curp' => 'SP', 'nombre' => 'San Luis Potosí'],
            ['clave' => 'SON', 'clave_curp' => 'SR', 'nombre' => 'Sonora'],
            ['clave' => 'TAB', 'clave_curp' => 'TC', 'nombre' => 'Tabasco'],
            ['clave' => 'TAM', 'clave_curp' => 'TS', 'nombre' => 'Tamaulipas'],
            ['clave' => 'TLX', 'clave_curp' => 'TL', 'nombre' => 'Tlaxcala'],
            ['clave' => 'VER', 'clave_curp' => 'VZ', 'nombre' => 'Veracruz'],
            ['clave' => 'YUC', 'clave_curp' => 'YN', 'nombre' => 'Yucatán'],
            ['clave' => 'ZAC', 'clave_curp' => 'ZS', 'nombre' => 'Zacatecas'],
        ], ['clave'], ['clave_curp', 'nombre']);
    }
}
