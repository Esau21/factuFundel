<?php

use App\Http\Controllers\Categorias\CategoriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
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

});

require __DIR__.'/auth.php';
