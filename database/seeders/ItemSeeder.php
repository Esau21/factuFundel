<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create(['codigo' => '1', 'nombre' => 'Bienes']);
        Item::create(['codigo' => '2', 'nombre' => 'Servicios']);
        Item::create(['codigo' => '3', 'nombre' => 'Ambos (Bienes y Servicios, incluye los dos inherentea los Productos o servicios)']);
        Item::create(['codigo' => '4', 'nombre' => 'Otros tributos por ítem']);
    }
}
