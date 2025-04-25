<?php

use App\Http\Controllers\Categorias\CategoriaController;
use App\Http\Controllers\Producto\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proveedor\ProveedorController;
use App\Http\Controllers\Security\AsignarController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\SociosNegocios\ClienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::get('/get-categories', [CategoriaController::class, 'getCategories'])->name('categorias.getCategories');
    Route::post('/categoria/save', [CategoriaController::class, 'storeCategoria'])->name('categorias.storeCategoria');
    Route::post('/actualizar/categoria/{id}', [CategoriaController::class, 'actualizarCategoria'])->name('categorias.actualizarCategoria');
    Route::delete('/delete/categoria/{id}', [CategoriaController::class, 'deleteCategoria'])->name('categorias.deleteCategoria');
    

    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/get/index/dataUsers', [UserController::class, 'getDataUsersIndex'])->name('usuarios.getDataUsersIndex');
    Route::get('/show/user/{id}', [UserController::class, 'showUser'])->name('usuarios.showUser');
    Route::get('/profile/user', [UserController::class, 'MyProfile'])->name('usuarios.MyProfile');
    Route::post('/store/user', [UserController::class, 'storeUser'])->name('usuarios.storeUser');
    Route::delete('/delete/user/{id}', [UserController::class, 'deleteUser'])->name('usuarios.deleteUser');
    Route::post('/update/user/{id}', [UserController::class, 'updateUser'])->name('usuarios.updateUser');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('usuarios.updateProfile');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/get/roles/data', [RoleController::class, 'getDataRoles'])->name('roles.getDataRoles');
    Route::post('/add/role', [RoleController::class, 'StoreRole'])->name('roles.StoreRole');
    Route::delete('/delete/role/{id}', [RoleController::class, 'deleteRole'])->name('roles.deleteRole');
    Route::post('/update/role/{id}', [RoleController::class, 'updateRoles'])->name('roles.updateRoles');

    Route::get('/permisos', [PermissionController::class, 'index'])->name('permisos.index');
    Route::get('/get/index/data/permisos', [PermissionController::class, 'getIndexDataPermisos'])->name('permisos.getIndexDataPermisos');
    Route::post('/store/permiso', [PermissionController::class, 'storePermission'])->name('permisos.storePermission');
    Route::post('/update/permiso/{id}', [PermissionController::class, 'updatePermiso'])->name('permisos.updatePermiso');
    Route::delete('/delete/permiso/{id}', [PermissionController::class, 'deletePermiso'])->name('permisos.deletePermiso');


    Route::get('/asignar', [AsignarController::class, 'index'])->name('asignar.index');
    Route::get('/get/index/data/asignar', [AsignarController::class, 'getDataIndexAsiganr'])->name('asignar.getDataIndexAsiganr');
    Route::post('/store/permisos', [AsignarController::class, 'storeAsignarPermisosRoles'])->name('asignar.storeAsignarPermisosRoles');
    Route::post('/asignar/todo', [AsignarController::class, 'AsignarTodo'])->name('asignar.AsignarTodo');
    Route::post('/revocar/todo', [AsignarController::class, 'RevocarTodo'])->name('asignar.RevocarTodo');


    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/get/index/data/proveedores', [ProveedorController::class, 'getIndexDataProveedores'])->name('proveedores.getIndexDataProveedores');
    Route::post('/store/proveedor', [ProveedorController::class, 'storeProveedor'])->name('proveedores.storeProveedor');
    Route::post('/update/proveedor/{id}', [ProveedorController::class, 'updateProveedor'])->name('proveedores.updateProveedor');
    Route::delete('/delete/proveedor/{id}', [ProveedorController::class, 'deleteProveedor'])->name('proveedor.deleteProveedor');

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/get/index/data', [ProductoController::class, 'getIndexDataProductos'])->name('productos.getIndexDataProductos');
    Route::get('/descargar/plantilla/productos', [ProductoController::class, 'descargarPlantilla'])->name('productos.descargarPlantilla');
    Route::get('/productos/add', [ProductoController::class, 'addProduct'])->name('productos.addProduct');
    Route::post('/new/producto/save', [ProductoController::class, 'storeProduct'])->name('productos.storeProduct');
    Route::post('/carga/masiva/productos-import', [ProductoController::class, 'enviarCargaMasivadeProductos'])->name('productos.enviarCargaMasivadeProductos');
    Route::post('/update/producto/{id}', [ProductoController::class, 'updateProducto'])->name('productos.updateProducto');
    Route::delete('/producto/delete/{id}', [ProductoController::class, 'deleteProducto'])->name('productos.deleteProducto');


    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/index/getData/clientes', [ClienteController::class, 'getIndexDataClientes'])->name('clientes.getIndexDataClientes');
    Route::get('/clientes/add', [ClienteController::class, 'addCliente'])->name('clientes.add');
    Route::post('/store/clientes', [ClienteController::class, 'storeCliente'])->name('store.storeCliente');

});

require __DIR__.'/auth.php';
