<?php

use App\Http\Controllers\Caja\CajaController;
use App\Http\Controllers\Categorias\CategoriaController;
use App\Http\Controllers\Cobros\BancosController;
use App\Http\Controllers\CuentasPorCobrarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DGII\DocumentosDTEController;
use App\Http\Controllers\DGII\LectorJsonController;
use App\Http\Controllers\Post\SalesController;
use App\Http\Controllers\Producto\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proveedor\ProveedorController;
use App\Http\Controllers\Security\AsignarController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\SociosNegocios\ClienteController;
use App\Http\Controllers\SociosNegocios\EmpresaController;
use App\Models\SociosNegocios\Empresa;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $empresas = Empresa::all();
    return view('auth.login', compact('empresas'));
});

Route::middleware(['auth', 'estado'])->group(function () {

    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified', 'estado'])
        ->name('dashboard');

    //ventas graficos home
    Route::get('/ventas-por-mes', [DashboardController::class, 'ventasPorMes'])->middleware(['auth', 'verified', 'estado']);

    //perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //categorias del sistema
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::get('/get-categories', [CategoriaController::class, 'getCategories'])->name('categorias.getCategories');
    Route::post('/categoria/save', [CategoriaController::class, 'storeCategoria'])->name('categorias.storeCategoria');
    Route::post('/actualizar/categoria/{id}', [CategoriaController::class, 'actualizarCategoria'])->name('categorias.actualizarCategoria');
    Route::delete('/delete/categoria/{id}', [CategoriaController::class, 'deleteCategoria'])->name('categorias.deleteCategoria');


    //usuarios del sistema
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/get/index/dataUsers', [UserController::class, 'getDataUsersIndex'])->name('usuarios.getDataUsersIndex');
    Route::get('/show/user/{id}', [UserController::class, 'showUser'])->name('usuarios.showUser');
    Route::get('/profile/user', [UserController::class, 'MyProfile'])->name('usuarios.MyProfile');
    Route::post('/store/user', [UserController::class, 'storeUser'])->name('usuarios.storeUser');
    Route::delete('/delete/user/{id}', [UserController::class, 'deleteUser'])->name('usuarios.deleteUser');
    Route::post('/update/user/{id}', [UserController::class, 'updateUser'])->name('usuarios.updateUser');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('usuarios.updateProfile');


    //roles del sistema
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/get/roles/data', [RoleController::class, 'getDataRoles'])->name('roles.getDataRoles');
    Route::post('/add/role', [RoleController::class, 'StoreRole'])->name('roles.StoreRole');
    Route::delete('/delete/role/{id}', [RoleController::class, 'deleteRole'])->name('roles.deleteRole');
    Route::post('/update/role/{id}', [RoleController::class, 'updateRoles'])->name('roles.updateRoles');


    //permisos
    Route::get('/permisos', [PermissionController::class, 'index'])->name('permisos.index');
    Route::get('/get/index/data/permisos', [PermissionController::class, 'getIndexDataPermisos'])->name('permisos.getIndexDataPermisos');
    Route::post('/store/permiso', [PermissionController::class, 'storePermission'])->name('permisos.storePermission');
    Route::post('/update/permiso/{id}', [PermissionController::class, 'updatePermiso'])->name('permisos.updatePermiso');
    Route::delete('/delete/permiso/{id}', [PermissionController::class, 'deletePermiso'])->name('permisos.deletePermiso');


    //asigancion de permisos
    Route::get('/asignar', [AsignarController::class, 'index'])->name('asignar.index');
    Route::get('/get/index/data/asignar', [AsignarController::class, 'getDataIndexAsiganr'])->name('asignar.getDataIndexAsiganr');
    Route::post('/store/permisos', [AsignarController::class, 'storeAsignarPermisosRoles'])->name('asignar.storeAsignarPermisosRoles');
    Route::post('/asignar/todo', [AsignarController::class, 'AsignarTodo'])->name('asignar.AsignarTodo');
    Route::post('/revocar/todo', [AsignarController::class, 'RevocarTodo'])->name('asignar.RevocarTodo');


    //proveedores
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('/get/index/data/proveedores', [ProveedorController::class, 'getIndexDataProveedores'])->name('proveedores.getIndexDataProveedores');
    Route::post('/store/proveedor', [ProveedorController::class, 'storeProveedor'])->name('proveedores.storeProveedor');
    Route::post('/update/proveedor/{id}', [ProveedorController::class, 'updateProveedor'])->name('proveedores.updateProveedor');
    Route::delete('/delete/proveedor/{id}', [ProveedorController::class, 'deleteProveedor'])->name('proveedor.deleteProveedor');


    //productos
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/get/index/data', [ProductoController::class, 'getIndexDataProductos'])->name('productos.getIndexDataProductos');
    Route::get('/descargar/plantilla/productos', [ProductoController::class, 'descargarPlantilla'])->name('productos.descargarPlantilla');
    Route::get('/productos/add', [ProductoController::class, 'addProduct'])->name('productos.addProduct');
    Route::post('/new/producto/save', [ProductoController::class, 'storeProduct'])->name('productos.storeProduct');
    Route::post('/carga/masiva/productos-import', [ProductoController::class, 'enviarCargaMasivadeProductos'])->name('productos.enviarCargaMasivadeProductos');
    Route::post('/update/producto/{id}', [ProductoController::class, 'updateProducto'])->name('productos.updateProducto');
    Route::delete('/producto/delete/{id}', [ProductoController::class, 'deleteProducto'])->name('productos.deleteProducto');


    //clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/index/getData/clientes', [ClienteController::class, 'getIndexDataClientes'])->name('clientes.getIndexDataClientes');
    Route::get('/clientes/add', [ClienteController::class, 'addCliente'])->name('clientes.add');
    Route::post('/store/clientes', [ClienteController::class, 'storeCliente'])->name('store.storeCliente');
    Route::get('/detalles/edit/cliente/{id}', [ClienteController::class, 'editarCliente'])->name('clientes.edit');
    Route::post('/update/cliente/{id}', [ClienteController::class, 'updateCliente'])->name('clientes.update');


    //empresas
    Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
    Route::get('/empresa/get/data', [EmpresaController::class, 'indexGetDataEmpresa'])->name('empresas.getData');
    Route::get('/empresa/add', [EmpresaController::class, 'addviewEmpresa'])->name('empresas.add');
    Route::post('/empresa/store', [EmpresaController::class, 'storeEmpresa'])->name('empresas.store');
    Route::get('/empresas/edit/{id}', [EmpresaController::class, 'editarEmpresa'])->name('empresas.edit');
    Route::post('/update/empresa/{id}', [EmpresaController::class, 'updateEmpresa'])->name('empresas.update');
    Route::post('/generar/nuevo/token', [EmpresaController::class, 'generarNuevoToken'])->name('empresas.generarNuevoToken');

    //bancos
    Route::get('/bancos', [BancosController::class, 'index'])->name('bancos.index');
    Route::get('/get/index/data/bancos', [BancosController::class, 'bancosgetIndexData'])->name('bancos.bancosgetIndexData');
    Route::post('/store/banco', [BancosController::class, 'storeBanco'])->name('bancos.store');
    Route::post('/update/banco/{id}', [BancosController::class, 'updateBanco'])->name('bancos.update');
    Route::get('/cheques/index', [BancosController::class, 'indexCheques'])->name('cheques.indexCheques');
    Route::get('/cheques/index/dataGet', [BancosController::class, 'getIndexDataCheque'])->name('cheques.getIndexDataCheque');
    Route::get('/cheques/historial/pdf', [BancosController::class, 'descargarHistorialPDF']);
    Route::get('/cheque/generate/{id}', [BancosController::class, 'generarCheque'])->name('cheques.generarCheque');
    //Cuentas Bancarias
    Route::get('/cuentas/bancarias/{id}', [BancosController::class, 'indexCuentasBancarias'])->name('cuentas.indexCuentasBancarias');
    Route::get('/cuentas/bancarias/index/data/{id}', [BancosController::class, 'indexGetCuentasBancarias'])->name('cuentas.indexGetCuentasBancarias');
    Route::post('/cuenta/bancaria/store/{bancoId}', [BancosController::class, 'storeCuentasBancarias'])->name('cuentas.storeCuentasBancarias');
    Route::post('/cuenta/update/bancaria/{cuentaId}', [BancosController::class, 'updateCuentaBancaria'])->name('cuentas.updateCuentaBancaria');


    //sales
    Route::get('/sales/post/index', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/buscar/productos', [SalesController::class, 'buscarProductos'])->name('sales.buscarProductos');
    Route::post('/generar/sale', [SalesController::class, 'generarSale'])->name('sales.generarSale');
    Route::post('/generar/cotizacion', [SalesController::class, 'generarCotizacion'])->name('sales.generarCotizacion');
    Route::get('/ventas/totales', [SalesController::class, 'SalesIndex'])->name('sales.SalesIndex');
    Route::get('/ventas/totalGetData', [SalesController::class, 'SalesIndexGetData'])->name('sales.SalesIndexGetData');
    Route::get('/ventas/historial/pdf', [SalesController::class, 'descargarHistorialPDF'])->name('sales.descargarHistorialPDF');
    Route::get('/sales/days/get', [SalesController::class, 'ventasDays'])->name('sales.getdays');
    Route::get('/sales/days/get/data', [SalesController::class, 'ventasDelDia'])->name('sales.ventasDelDia');
    Route::get('/detalles/ventas/{id}', [SalesController::class, 'verDetallesdeVenta'])->name('sales.verDetallesdeVenta');
    Route::get('/sales/ventas/mes', [SalesController::class, 'ventasDelMes'])->name('sales.ventasDelMes');
    Route::get('/report/mes/pdf', [SalesController::class, 'downloadReportePdfMes'])->name('ventas.descargar.pdf');
    Route::get('/download/report/mes', [SalesController::class, 'downloadExcelReporteMes'])->name('ventas.descargar.excel');
    Route::get('/get/detallesventas', [SalesController::class, 'getDetalleVentasMensual'])->name('sales.getDetalleVentasMensual');
    Route::get('/ventas/resumen-mensual', [SalesController::class, 'getResumenMensual'])->name('ventas.resumen.mensual');
    Route::get('/generar/pdf/ventas/dia/{id}', [SalesController::class, 'generarPDfDetalles'])->name('sales.generarPDfDetalles');
    Route::post('/facturacion/emitir/contingencia/documento/{id}', [SalesController::class, 'emitirEvenetodeContigencia'])->name('sales.emitirEvenetodeContigencia');

    //facturacion
    Route::get('/facturacion/index', [DocumentosDTEController::class, 'index'])->name('facturacion.index');
    Route::get('/facturacion/getData', [DocumentosDTEController::class, 'indexGetDtaDocumentosDte'])->name('facturacion.indexGetDtaDocumentosDte');
    Route::get('/download/json/dte/{documento_id}', [DocumentosDTEController::class, 'getDocumentoTributarioJson'])->name('facturacion.getDocumentoTributarioJson');
    Route::get('/mh/response/{documentoId}', [DocumentosDTEController::class, 'viewMHResponse'])->name('facturacion.viewMHResponse');
    Route::get('/mostrar/factura/tipo/{documentoId}', [DocumentosDTEController::class, 'generarDocumentoElectronico'])->name('facturacion.generarDocumentoElectronico');
    Route::get('/download/pdf/tipo/documento/{documentoId}', [DocumentosDTEController::class, 'descargarPDFTipoDocumento'])->name('facturacion.descargarPDFTipoDocumento');
    Route::get('/correlativos/dte', [DocumentosDTEController::class, 'correlativosDteIndex'])->name('correlativos.correlativosDteIndex');
    Route::get('/get/data/correlativos/index', [DocumentosDTEController::class, 'correlativosDteIndexGetData'])->name('correlativos.correlativosDteIndexGetData');
    Route::post('/documentos-dte/enviar/{documento}', [DocumentosDTEController::class, 'enviarADGII'])
        ->name('documentos-dte.enviar');
    Route::get('/download/history/dte', [DocumentosDTEController::class, 'historialDTEFechasXlsx'])->name('facturacion.historialDTEFechasXlsx');
    Route::post('/facturacion/anular-json/{id}', [DocumentosDTEController::class, 'anularDocumentoTributarioElectronico']);
    Route::get('/facturacion/obtener-json/{id}', [DocumentosDTEController::class, 'obtenerJsonDte']);
    Route::post('/reenviar/json/dte/{id}', [DocumentosDTEController::class, 'reenviarDteDocumentoId'])->name('facturacion.reenviarDteDocumentoId');
    Route::get('/documentos/notas/credito/{documentoId}', [DocumentosDTEController::class, 'emitirnotaCredito'])->name('facturacion.notas.credito');
    Route::post('/store/nota/credito/', [DocumentosDTEController::class, 'storeNotaCredito'])->name('facturacion.storeNotaCredito');
    Route::get('/emitir/nota/debito/{documentoId}', [DocumentosDTEController::class, 'emitirnotaDebito'])->name('facturacion.notas.debito');
    Route::post('/store/nota/debido', [DocumentosDTEController::class, 'storeNotaDebito'])->name('facturacion.storeNotaDebito');

    //Cajas
    Route::get('/cajas/index', [CajaController::class, 'index'])->name('cajas.index');
    Route::get('/cajas/get/index/data', [CajaController::class, 'getIndexCaja'])->name('cajas.getIndexCaja');
    Route::post('/store/apertura/caja', [CajaController::class, 'storeCaja'])->name('cajas.storeCaja');
    Route::post('/cajas/{id}/cerrar', [CajaController::class, 'cerrarCaja'])->name('cajas.cerrar');
    Route::delete('/cajas/delete/{id}', [CajaController::class, 'eliminarCaja'])->name('cajas.eliminarCaja');

    //CxC
    Route::get('/cuentas/por/cobrar', [CuentasPorCobrarController::class, 'index'])->name('cxc.index');
    Route::get('/cxc/getData', [CuentasPorCobrarController::class, 'indexGetDataCxC'])->name('cxc.indexGetDataCxC');
    Route::get('/abonos/cxc', [CuentasPorCobrarController::class, 'abonosIndex'])->name('cxc.abonosIndex');
    Route::post('/abono/cuenta/store', [CuentasPorCobrarController::class, 'registrarAbono'])->name('store.abono.cuenta');

    //herramienta del json
    Route::get('/lector/json', [LectorJsonController::class, 'index'])->name('lector.index');
    Route::post('/conver/to/json/excel', [LectorJsonController::class, 'uploadFile'])->name('lector.uploadFile');
});

Route::get('/notaccess', function () {
    return view('auth.access');
});

require __DIR__ . '/auth.php';
