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

        //roles
        Permission::create(['name' => 'rol_index']);
        Permission::create(['name' => 'rol_create']);
        Permission::create(['name' => 'rol_edit']);
        Permission::create(['name' => 'rol_destroy']);

        //permisos
        Permission::create(['name' => 'permission_index']);
        Permission::create(['name' => 'permission_create']);
        Permission::create(['name' => 'permission_edit']);
        Permission::create(['name' => 'permission_destroy']);

        //asigancion de permisos
        Permission::create(['name' => 'assign_index']);
        Permission::create(['name' => 'assign_form']);
        Permission::create(['name' => 'assign_table']);

        //bancos
        Permission::create(['name' => 'bancos_view']);
        Permission::create(['name' => 'bancos_index']);
        Permission::create(['name' => 'cheques_index']);
        Permission::create(['name' => 'bancos_create']);
        Permission::create(['name' => 'bancos_edit']);
        Permission::create(['name' => 'bancos_create_accounts']);
        Permission::create(['name' => 'cheques_form']);
        Permission::create(['name' => 'add_account_bank']);
        Permission::create(['name' => 'edit_cuenta_bank']);

        //productos y mas
        Permission::create(['name' => 'productos_y_mas_view']);
        Permission::create(['name' => 'categorias_index']);
        Permission::create(['name' => 'categoria_create']);
        Permission::create(['name' => 'categoria_edit']);
        Permission::create(['name' => 'categoria_delete']);
        Permission::create(['name' => 'productos_index']);
        Permission::create(['name' => 'create_producto']);
        Permission::create(['name' => 'edit_producto']);
        Permission::create(['name' => 'delete_producto']);
        Permission::create(['name' => 'importar_productos']);

        //socios de negocios
        Permission::create(['name' => 'socios_negocios_view']);
        Permission::create(['name' => 'clientes_index']);
        Permission::create(['name' => 'clientes_add']);
        Permission::create(['name' => 'edit_cliente']);
        Permission::create(['name' => 'proveedores_index']);

        //ventas view
        Permission::create(['name' => 'ventas_view']);
        Permission::create(['name' => 'ventas_index']);
        Permission::create(['name' => 'ventas_create']);
        Permission::create(['name' => 'ventas_form']);
        Permission::create(['name' => 'ventas_view_details']);
        Permission::create(['name' => 'ventas_print']);
        Permission::create(['name' => 'ventas_send_contingencia']);
        Permission::create(['name' => 'ventas_del_dia_index']);
        Permission::create(['name' => 'ventas_del_mes_index']);

        //facturacion
        Permission::create(['name' => 'facturacion_view']);
        Permission::create(['name' => 'facuracion_index']);
        Permission::create(['name' => 'facturacion_mh_response']);
        Permission::create(['name' => 'facturacion_view_factura']);
        Permission::create(['name' => 'download_json']);
        Permission::create(['name' => 'download_pdf_factura']);
        Permission::create(['name' => 'anulacion_json']);
        Permission::create(['name' => 'send_factura_mh_response']);
        Permission::create(['name' => 'reenvio_json']);
        Permission::create(['name' => 'correlativos_index']);
        Permission::create(['name' => 'json_lector_index']);

        Permission::create(['name'=> 'cxc_account']);

        //Cajas
        Permission::create(['name' => 'caja_cerrar']);
        Permission::create(['name' => 'caja_delete']);


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


        $uadmin = User::find(1);
        $uadmin->syncRoles('ROOT');

        $uadmin = User::find(2);
        $uadmin->syncRoles('GERENCIA');
    }
}
