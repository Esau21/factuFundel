<?php

namespace Database\Seeders;

use App\Models\SociosNegocios\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class ConfiguracionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Empresa::create([
            'nombre' => 'FUNDACION DE DESARROLLO LATINO AMERICANO',
            'nombreComercial' => 'FUNDEL',
            'tipo_documento' => '36',
            'departamento_id' => 1,
            'municipio_id' => 1,
            'complemento' => 'CALLE AL MIRADOR COLONIA ESCALON',
            'nit' => '06140607181088',
            'nrc' => '2760965',
            'telefono' => '77665545',
            'correo' => 'fundelong@gmail.com',
            'logo' => asset('img/camara1.png'),
            'actividad_economica_id' => 1,
            'tipoEstablecimiento' => '01',
            'nombre_establecimiento' => 'FundelcasaMatriz',
            'token' => 'tokentokentokentoken',
            'token_expire' => 'token_expiredffrrffvrfvfv',
            'ambiente' => '00',
            'codPuntoVentaMH' => '01',
            'codEstablecimientoMH' => 'MP000001',
            'claveAPI' => Crypt::encryptString('Fundel@2025**'),
            'claveCert' => Crypt::encryptString('FundelOng@2025##')
        ]);
    }
}
