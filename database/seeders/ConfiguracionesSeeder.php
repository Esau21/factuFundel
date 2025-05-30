<?php

namespace Database\Seeders;

use App\Models\SociosNegocios\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'departamento_id' => 1,
            'municipio_id' => 1,
            'complemento' => 'CALLE AL MIRADOR COLONIA ESCALON',
            'nit' => '00000998',
            'nrc' => '090999',
            'telefono' => '77665545',
            'correo' => 'fundelong@gmail.com',
            'logo' => asset('img/camara1.png'),
            'actividad_economica_id' => 1,
        ]);

        Empresa::create([
            'nombre' => 'SI INGIENERIA',
            'nombreComercial' => 'SI',
            'departamento_id' => 1,
            'municipio_id' => 1,
            'complemento' => 'CALLE AL MIRADOR COLONIA ESCALON',
            'nit' => '000090998',
            'nrc' => '090233999',
            'telefono' => '76123344',
            'correo' => 'siinge@gmail.com',
            'logo' => asset('img/camara1.png'),
            'actividad_economica_id' => 2,
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
            'empresa_id' => 2
        ]);
    }
}
