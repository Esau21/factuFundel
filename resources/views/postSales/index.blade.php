@extends('layouts.sneatTheme.base')
@section('title', 'Pos ventas')

@section('content')
    <div class="container-fluid">
        <div class="row align-items-start">
            <!-- Búsqueda de productos -->
            <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                <div class="card mb-0">
                    <div class="card-header">
                        <h6 class="alert alert-dark d-flex align-items-center gap-2" role="alert">
                            <i class='bx bx-error-circle fs-4'></i>
                            ¡Aviso Importante! Tienes que buscar y seleccionar el producto, que quieres agregar a la venta.
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="buscar_producto" class="form-label"><b class="text-danger">***Buscar
                                    producto***</b></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bx bx-search" style="font-size: 1.3rem;"></i>
                                </span>
                                <input type="text" name="buscar_producto" id="buscar_producto" class="form-control"
                                    placeholder="Buscar producto...">
                            </div>
                        </div>

                        <div id="productSearchResults" class="mt-2"
                            style="border: 1px solid #ddd; display: none; position: absolute; background: #fff; width: 100%; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; border-radius: 8px; padding: 10px;">
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive mt-5" style="max-height: 650px; overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th class="text-start">PRODUCTO</th>
                                        <th width="30%" class="text-center">PRECIO</th>
                                        <th width="20%" class="text-center">CANT</th>
                                        <th width="20%" class="text-center">%</th>
                                        <th width="20%" class="text-center">$</th>
                                        <th class="text-center">IMPORTE</th>
                                        <th class="text-center">PROCESOS</th>
                                    </tr>
                                </thead>
                                <tbody id="productRows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h6 class="alert alert-dark d-flex align-items-center gap-2" role="alert">
                            <i class='bx bx-error-circle fs-4'></i>
                            ¡Aviso Importante! Tienes que ir llenando los campos que son solicitados, en los tipos de pagos.
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Selección de Cliente y Pago -->
                        <div class="row g-4">
                            <!-- Cliente -->
                            <div class="col-sm-6">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select select2 w-100">
                                    <option value="">Seleccione</option>
                                    @foreach ($clientes as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipo de Pago -->
                            <div class="col-sm-6">
                                <label for="tipo_pago" class="form-label">Tipo de pago</label>
                                <select name="tipo_pago" id="tipo_pago" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="efectivo">EFECTIVO</option>
                                    <option value="transferencia">TRANSFERENCIA</option>
                                    <option value="cheque">CHEQUE</option>
                                    <option value="mixto_cheque_efectivo">MIXTO CHEQUE EFECTIVO</option>
                                    <option value="mixto_transferencia_efectivo">MIXTO TRANSFERENCIA EFECTIVO</option>
                                </select>
                            </div>

                            <!-- Tipo de comprobante -->
                            <div class="col-sm-6">
                                <label for="tipo_documento" class="form-label">Tipo de comprobante</label>
                                <select name="tipo_documento" id="tipo_documento" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="factura">Factura Electrónica</option>
                                    <option value="ccf">Crédito Fiscal</option>
                                    <option value="ticket">Consumidor Final</option>
                                </select>
                            </div>


                            <!-- Efectivo -->
                            <div id="efectivo_fields" class="col-12 mt-3" style="display: none;">
                                <div class="col-sm-6 mt-3">
                                    <label class="form-label">Efectivo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white">EFECTIVO</span>
                                        <input type="number" id="cash" class="form-control text-center"
                                            value="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Campos Cheque -->
                            <div id="cheque_fields" class="col-12 mt-3" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="numero_cheque" class="form-label">Número de cheque</label>
                                        <input type="text" class="form-control" id="numero_cheque" name="numero_cheque">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cuenta_bancaria_id" class="form-label">Cuenta emisora</label>
                                        <select name="cuenta_bancaria_id" id="cuenta_id" class="form-select select2">
                                            <option value="">Seleccionar cuenta emisora</option>
                                            @foreach ($cuentas_bancarias as $cuenta)
                                                <option value="{{ $cuenta->id }}">
                                                    {{ $cuenta->numero_cuenta }} - {{ $cuenta->moneda }} -
                                                    {{ $cuenta?->banco?->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="fecha_emision" class="form-label">Fecha de emisión</label>
                                        <input type="date" class="form-control" id="fecha_emision"
                                            name="fecha_emision">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="monto" class="form-label">Monto cheque</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            id="monto" name="monto" placeholder="0.00">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="estado" class="form-label">Estado del cheque</label>
                                        <select name="estado" id="estado" class="form-select select2">
                                            <option value="">Seleccionar estado</option>
                                            <option value="pendiente">PENDIENTE</option>
                                            <option value="cobrado">COBRADO</option>
                                            <option value="rechazado">RECHAZADO</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="estado" class="form-label">Observaciones del cheque</label>
                                        <textarea name="observaciones" id="observaciones" class="form-control" rows="1"></textarea>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="monto" class="form-label">Numero correlatico (Opcional)</label>
                                        <input type="text" class="form-control" id="correlativo" name="correlativo"
                                            placeholder="correlativo">
                                    </div>

                                </div>
                            </div>

                            <!-- Campos Transferencia -->
                            <div id="transferencia_fields" class="col-12 mt-3" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-sm-12">
                                        <label for="cuenta_bancaria_id" class="form-label">Cuenta destino</label>
                                        <select name="cuenta_bancaria_id" id="cuenta_bancaria_id"
                                            class="form-select select2">
                                            <option value="">Seleccione una cuenta</option>
                                            @foreach ($cuentas_bancarias as $cuenta)
                                                <option value="{{ $cuenta->id }}">
                                                    {{ $cuenta->numero_cuenta }} - {{ $cuenta->moneda }} -
                                                    {{ $cuenta?->banco?->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="monto_transferencia" class="form-label">Monto transferido</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            id="monto_transferencia" name="monto_transferencia" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Resumen de ventas -->
            <div class="col-12 col-md-4 d-flex self-start">
                <div class="card shadow-sm w-100 sticky-sidebar">
                    <div class="card-body d-flex flex-column justify-content-between position-relative">

                        <!-- Imagen decorativa centrada -->
                        <img src="{{ asset('assets/img/piggy-bank.png') }}" alt="Decoración"
                            style="max-width: 100px; margin: 0 auto 10px auto; display: block; opacity: 0.4;">

                        <h5 class="text-center mb-4"><b>***RESUMEN DE VENTAS***</b></h5>

                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="text-success text-uppercase">
                                    Total: $<span id="totalAmount">0.00</span>
                                </h3>
                                <h4 class="mt-3">Artículos: <span id="totalItems">0</span></h4>
                            </div>
                        </div>

                        <h4 class="text-muted text-center mb-4">Cambio: $<span id="changeAmount">0.00</span></h4>
                        <input type="hidden" name="cambio" id="cambioInput" value="0">

                        <div class="d-grid gap-2">
                            <button class="btn btn-danger" type="button" id="guardarCotizacion">COTIZACIÓN F4</button>
                            <button class="btn btn-primary" type="button" id="guardarVenta">GENERAR VENTA F5</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('postSales.scripts.scriptsPostventas')
@endsection
