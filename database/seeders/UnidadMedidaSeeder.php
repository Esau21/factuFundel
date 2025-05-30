<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/unidades.json');

        if (!file_exists($path)) {
            $this->command->error('El archivo unidades.json no existe en storage/app');
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->command->error('No se pudo decodificar el JSON.');
            return;
        }

        foreach ($data as $unidad) {
            UnidadMedida::updateOrCreate(
                ['codigo' => $unidad['codigo']],
                ['nombre' => $unidad['nombre']]
            );
        }

        $this->command->info('Unidades de medida insertadas correctamente.');
    }
}
