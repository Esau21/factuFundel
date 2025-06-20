<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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


        $root = Role::create(['name' => 'ROOT']);
        $uadmin = Role::create(['name' => 'GERENCIA']);

        /**
         * permisos del sistema
         */

        Permission::create(['name' => 'rol_index']);
        Permission::create(['name' => 'rol_create']);
        Permission::create(['name' => 'rol_edit']);
        Permission::create(['name' => 'rol_destroy']);

        Permission::create(['name' => 'permission_index']);
        Permission::create(['name' => 'permission_create']);
        Permission::create(['name' => 'permission_edit']);
        Permission::create(['name' => 'permission_destroy']);

        Permission::create(['name' => 'assign_index']);
        Permission::create(['name' => 'assign_form']);
        Permission::create(['name' => 'assign_table']);


        $uadmin->givePermissionTo([
            'rol_index',
            'rol_create',
            'rol_edit',
            'rol_destroy',
            'permission_index',
            'permission_create',
            'permission_edit',
            'permission_destroy',
            'assign_index',
            'assign_form',
            'assign_table',
        ]);

        $root->givePermissionTo([
            'seguridad_modulo',
            'rol_index',
            'rol_create',
            'rol_edit',
            'rol_destroy',
            'permission_index',
            'permission_create',
            'permission_edit',
            'permission_destroy',
            'user_index',
            'user_create',
            'user_edit',
            'user_destroy',
            'assign_index',
            'assign_form',
            'assign_table',
        ]);


        $uadmin = User::find(1);
        $uadmin->syncRoles('ROOT');

        $uadmin = User::find(2);
        $uadmin->syncRoles('GERENCIA');
    }
}
