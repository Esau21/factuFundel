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
            'nombre' => 'TU EMPRESA',
            'nombreComercial' => 'EMPRESA EJEMPLO',
            'tipo_documento' => '36',
            'departamento_id' => 1,
            'municipio_id' => 1,
            'complemento' => 'SAN SALVADOR EJEMPLO DE TU DIREECION',
            'nit' => 'XXXXXXX',
            'nrc' => 'XXXXXXXX',
            'telefono' => 'XXXXXXX',
            'correo' => 'tucorreo@gmail.com',
            'logo' => asset('img/camara1.png'),
            'actividad_economica_id' => 1,
            'tipoEstablecimiento' => '01',
            'nombre_establecimiento' => 'casa matriz',
            'token' => 'XXXXX',
            'token_expire' => 'XXXXXXX',
            'ambiente' => '00',
            'codPuntoVentaMH' => 'P001',
            'codEstablecimientoMH' => 'M001',
            'claveAPI' => Crypt::encryptString('XXXXXXXXXX'),
            'claveCert' => Crypt::encryptString('XXXXXXXXXX')
        ]);
    }
}
