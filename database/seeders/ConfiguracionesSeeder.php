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
        


        User::create([
            'name' => 'Edgar',
            'email_verified_at' => '2023-09-11 22:44:25',
            'email' => 'root@gmail.com',
            'profile' => 'ROOT',
            'status' => 'Active',
            'password' => bcrypt('12345678'),
            'empresa_id' => 1
        ]);

        User::create([
            'name' => 'Michelle',
            'email_verified_at' => '2023-09-11 22:44:25',
            'email' => 'michelle@gmail.com',
            'profile' => 'ADMINISTRADOR',
            'status' => 'Active',
            'password' => bcrypt('12345678'),
            'empresa_id' => 1
        ]);
    }
}
