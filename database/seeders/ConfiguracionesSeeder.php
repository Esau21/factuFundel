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
            'nit' => '00000998',
            'nrc' => '090999',
            'giro' => 'Empresa de desarrollo',
            'telefono' => '77665545',
            'correo' => 'fundelong@gmail.com',
            'direccion' => 'CALLE AL MIRADOR COLONIA ESCALON',
            'logo' => asset('img/camara1.png'),
        ]);

        Empresa::create([
            'nombre' => 'SI INGIENERIA',
            'nit' => '000090998',
            'nrc' => '090233999',
            'giro' => 'Empresa de ingineria',
            'telefono' => '76123344',
            'correo' => 'siinge@gmail.com',
            'direccion' => 'CALLE AL MIRADOR COLONIA ESCALON',
            'logo' => asset('img/camara1.png'),
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
            'profile' => 'ROOT',
            'status' => 'Active',
            'password' => bcrypt('12345678'),
            'empresa_id' => 2
        ]);
    }
}
