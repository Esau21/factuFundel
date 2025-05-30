<?php

namespace Database\Seeders;

use App\Models\Ubicaciones\Departamento;
use App\Models\Ubicaciones\Municipio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartamentoMuncipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departamento::create(['codigo' => '00', 'nombre' => 'Otro (Para extranjeros)']);
        Departamento::create(['codigo' => '01', 'nombre' => 'Ahuachapán']);
        Departamento::create(['codigo' => '02', 'nombre' => 'Santa Ana']);
        Departamento::create(['codigo' => '03', 'nombre' => 'Sonsonate']);
        Departamento::create(['codigo' => '04', 'nombre' => 'Chalatenango']);
        Departamento::create(['codigo' => '05', 'nombre' => 'La Libertad']);
        Departamento::create(['codigo' => '06', 'nombre' => 'San Salvador']);
        Departamento::create(['codigo' => '07', 'nombre' => 'Cuscatlán']);
        Departamento::create(['codigo' => '08', 'nombre' => 'La Paz']);
        Departamento::create(['codigo' => '09', 'nombre' => 'Cabañas']);
        Departamento::create(['codigo' => '10', 'nombre' => 'San Vicente']);
        Departamento::create(['codigo' => '11', 'nombre' => 'Usulután']);
        Departamento::create(['codigo' => '12', 'nombre' => 'San Miguel']);
        Departamento::create(['codigo' => '13', 'nombre' => 'Morazán']);
        Departamento::create(['codigo' => '14', 'nombre' => 'La Unión']);


        Municipio::create(['codigo' => '00', 'nombre' => 'Otro (Para extranjeros)']);
        Municipio::create(['codigo' => '13', 'nombre' => 'AHUACHAPAN NORTE']);
        Municipio::create(['codigo' => '14', 'nombre' => 'AHUACHAPAN CENTRO']);
        Municipio::create(['codigo' => '15', 'nombre' => 'AHUACHAPAN SUR']);
        Municipio::create(['codigo' => '14', 'nombre' => 'SANTA ANA NORTE']);
        Municipio::create(['codigo' => '15', 'nombre' => 'SANTA ANA CENTRO']);
        Municipio::create(['codigo' => '16', 'nombre' => 'SANTA ANA ESTE']);
        Municipio::create(['codigo' => '17', 'nombre' => 'SANTA ANA OESTE']);
        Municipio::create(['codigo' => '17', 'nombre' => 'SONSONATE NORTE']);
        Municipio::create(['codigo' => '18', 'nombre' => 'SONSONATE CENTRO']);
        Municipio::create(['codigo' => '19', 'nombre' => 'SONSONATE ESTE']);
        Municipio::create(['codigo' => '20', 'nombre' => 'SONSONATE OESTE']);
        Municipio::create(['codigo' => '34', 'nombre' => 'CHALATENANGO NORTE']);
        Municipio::create(['codigo' => '35', 'nombre' => 'CHALATENANGO CENTRO']);
        Municipio::create(['codigo' => '36', 'nombre' => 'CHALATENANGO SUR']);
        Municipio::create(['codigo' => '23', 'nombre' => 'LA LIBERTAD NORTE']);
        Municipio::create(['codigo' => '24', 'nombre' => 'LA LIBERTAD CENTRO']);
        Municipio::create(['codigo' => '25', 'nombre' => 'LA LIBERTAD OESTE']);
        Municipio::create(['codigo' => '26', 'nombre' => 'LA LIBERTAD ESTE']);
        Municipio::create(['codigo' => '27', 'nombre' => 'LA LIBERTAD COSTA']);
        Municipio::create(['codigo' => '28', 'nombre' => 'LA LIBERTAD SUR']);
        Municipio::create(['codigo' => '20', 'nombre' => 'SAN SALVADOR NORTE']);
        Municipio::create(['codigo' => '21', 'nombre' => 'SAN SALVADOR OESTE']);
        Municipio::create(['codigo' => '22', 'nombre' => 'SAN SALVADOR ESTE']);
        Municipio::create(['codigo' => '23', 'nombre' => 'SAN SALVADOR CENTRO']);
        Municipio::create(['codigo' => '24', 'nombre' => 'SAN SALVADOR SUR']);
        Municipio::create(['codigo' => '17', 'nombre' => 'CUSCATLAN NORTE']);
        Municipio::create(['codigo' => '18', 'nombre' => 'CUSCATLAN SUR']);
        Municipio::create(['codigo' => '23', 'nombre' => 'LA PAZ OESTE']);
        Municipio::create(['codigo' => '24', 'nombre' => 'LA PAZ CENTRO']);
        Municipio::create(['codigo' => '25', 'nombre' => 'LA PAZ ESTE']);
        Municipio::create(['codigo' => '10', 'nombre' => 'CABAÑAS OESTE']);
        Municipio::create(['codigo' => '11', 'nombre' => 'CABAÑAS ESTE']);
        Municipio::create(['codigo' => '14', 'nombre' => 'SAN VICENTE NORTE']);
        Municipio::create(['codigo' => '15', 'nombre' => 'SAN VICENTE SUR']);
        Municipio::create(['codigo' => '24', 'nombre' => 'USULUTAN NORTE']);
        Municipio::create(['codigo' => '25', 'nombre' => 'USULUTAN ESTE']);
        Municipio::create(['codigo' => '26', 'nombre' => 'USULUTAN OESTE']);
        Municipio::create(['codigo' => '21', 'nombre' => 'SAN MIGUEL NORTE']);
        Municipio::create(['codigo' => '22', 'nombre' => 'SAN MIGUEL CENTRO']);
        Municipio::create(['codigo' => '23', 'nombre' => 'SAN MIGUEL OESTE']);
        Municipio::create(['codigo' => '27', 'nombre' => 'MORAZAN NORTE']);
        Municipio::create(['codigo' => '28', 'nombre' => 'MORAZAN SUR']);
        Municipio::create(['codigo' => '19', 'nombre' => 'LA UNION NORTE']);
        Municipio::create(['codigo' => '20', 'nombre' => 'LA UNION SUR']);
    }
}
