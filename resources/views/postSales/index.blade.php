@extends('layouts.sneatTheme.base')
@section('title', 'Post ventas')

@section('content')
    <div class="container-fluid">
        <div class="row">
            {{-- Contenido principal --}}
            <div class="col-12 col-lg-8 mb-4 order-2 order-lg-1">
                <div class="card">
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="buscar">Buscar producto:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light d-flex align-items-center">
                                            <i class="bx bx-search" style="font-size: 1.3rem;"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="buscar_producto" id="buscar_producto" class="form-control"
                                        placeholder="Buscar producto...">
                                </div>
                            </div>
                        </div>
                        <div id="productSearchResults"
                            style="border: 1px solid #ccc; display: none; position: absolute; background: white; width: 100%; z-index: 1000;">
                        </div>


                        <!-- Tabla de productos -->
                        <div class="table-responsive mt-5" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th class="text-left">PRODUCTO</th>
                                        <th class="text-center">PRECIO</th>
                                        <th width="13%" class="text-center">CANT</th>
                                        <th class="text-center">IMPORTE</th>
                                        <th class="text-center">PROCESOS</th>
                                    </tr>
                                </thead>
                                <tbody id="productRows">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- resumen de ventas -->
            <div class="col-12 col-lg-4 order-1 order-lg-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-center mb-4">RESUMEN DE VENTAS</h5>

                        <!-- Resumen Total y Artículos -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="text-success text-uppercase">Total: $<span id="totalAmount"></span></h3>
                                <h4 class="mt-3">Artículos: <span id="totalItems"></span></h4>
                            </div>
                        </div>

                        <!-- Selección de Cliente -->
                        <div class="mb-4">
                            <label for="cliente" class="form-label">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-select select2 w-90">
                                <option value="">Seleccione</option>
                                @foreach ($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Efectivo -->
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-dark text-white">EFECTIVO</span>
                            <input type="number" id="cash" class="form-control text-center" value="0.00">
                        </div>

                        <div class="mb-4">
                            <label for="descuento_porcentaje" class="form-label">Descuento porcentaje</label>
                            <select name="descuento_porcentaje[]" id="descuento_porcentaje"
                                class="form-select descuento_porcentaje">
                                <option value="">Seleccione</option>
                                <option value="10%">10%</option>
                                <option value="15%">15%</option>
                                <option value="20%">20%</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="descuento_en_dolar" class="form-label">Descuento en dolar</label>
                            <input type="text" name="descuento_en_dolar" id="descuento_en_dolar" class="form-control"
                                placeholder="En dolar">
                        </div>

                        <!-- Monto de Cambio -->
                        <h4 class="text-muted text-center mb-4">Cambio: $<span id="changeAmount">0.00</span></h4>
                        <input type="hidden" name="cambio" id="cambioInput" value="0">

                        <!-- Botones -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-danger" type="button" id="guardarCotizacion">COTIZACIÓN F4</button>
                            <button class="btn btn-primary" type="button" id="guardarVenta">GUARDAR VENTA F5</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('postSales.scripts.scriptsPostventas')
@endsection
