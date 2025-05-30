<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActividadesEconomicasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/csvjson.json');

        if (!file_exists($path)) {
            $this->command->error('El archivo csvjson.json no existe en storage/app');
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->command->error('No se pudo decodificar el JSON.');
            return;
        }

        $actividades = reset($data); // Accede al primer nivel de claves

        foreach ($actividades as $clave => $valor) {
            /* Esto garantiza que solo se ejecuta explode() si la cadena contiene realmente el separador | */
            if (strpos($clave, '|') !== false) {
                [$codigo, $descripcion] = explode('|', $clave, 2);

                DB::table('actividades_economicas')->insert([
                    'codActividad' => trim($codigo),
                    'descActividad' => trim($descripcion),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Actividades económicas insertadas correctamente.');
    }
}
