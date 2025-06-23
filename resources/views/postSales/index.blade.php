@extends('layouts.sneatTheme.base')
@section('title', 'Pos ventas')

@section('content')
    <div class="container-fluid">
        <div class="row align-items-start">
            <!-- Búsqueda de productos -->
            <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                <div class="card mb-0">
                    <div class="card-header">
                        <h6 class="alert bg-label-primary d-flex align-items-center gap-2" role="alert">
                            <i class="bx bx-error-circle" style="font-size: 28px; transition: transform 0.2s;"></i>
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
                        <h6 class="alert bg-label-primary d-flex align-items-center gap-2" role="alert">
                            <i class="bx bx-error-circle" style="font-size: 28px; transition: transform 0.2s;"></i>
                            ¡Aviso Importante! Tienes que ir llenando los campos que son solicitados, en los tipos de pagos.
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Selección de Cliente y Pago -->
                        <div class="row g-4">

                            <!-- tipo de venta -->
                            <div class="col-sm-6 mt-3">
                                <label for="tipo_venta" class="form-label">Tipo de venta</label>
                                <select name="tipo_venta" id="tipo_venta" class="form-select w-100">
                                    <option value="">Seleccione</option>
                                    <option value="1">Contado</option>
                                    <option value="2">Crédito</option>
                                </select>
                            </div>

                            <!-- Plazo -->
                            <div class="col-sm-6">
                                <label for="plazo">Plazo</label>
                                <select name="plazo" id="plazo" class="form-select w-100">
                                    <option value="">Selecciona</option>
                                    <option value="01">Días</option>
                                    <option value="02">Meses</option>
                                    <option value="03">Años</option>
                                </select>
                            </div>

                            <!-- referencia -->
                            <div class="col-sm-6">
                                <label for="plazo">Referencia (Opcional)</label>
                                <input type="text" name="referencia" id="referencia" class="form-control text-center">
                            </div>

                            <!-- Periodo -->
                            <div class="col-sm-6">
                                <label for="plazo">Periodo</label>
                                <input type="text" name="periodo" id="periodo" class="form-control text-center">
                            </div>

                            <!-- Cliente -->
                            <div class="col-sm-6 mt-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="cliente_id" id="cliente_id" class="form-select select2 w-100">
                                    <option value="">Seleccione</option>
                                    @foreach ($clientes as $c)
                                        <option value="{{ $c->id }}">{{ $c->tipo_persona }} | {{ $c->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipo de Pago -->
                            <div class="col-sm-6">
                                <label for="tipo_pago" class="form-label">Tipo de pago</label>
                                <select name="tipo_pago" id="tipo_pago" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="01">BILLETES Y MONEDAS</option>
                                    <option value="04">CHEQUE</option>
                                    <option value="05">TRANSFERENCIA-DEPÓSITO BANCARIO</option>
                                </select>
                            </div>

                            <!-- Tipo de comprobante -->
                            <div class="col-sm-6">
                                <label for="tipo_documento" class="form-label">Tipo de comprobante</label>
                                <select name="tipo_documento" id="tipo_documento" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="factura">Factura</option>
                                    <option value="ccf">Comprobante de Crédito Fiscal</option>
                                    <option value="factura_sujeto_excluido">Factura de Sujeto Excluido</option>
                                    <option value="comprobante_donacion">Comprobante de Donación</option>
                                </select>
                            </div>

                            <div class="col-sm-6" id="campoObservaciones" style="display: none;">
                                <label for="estado" class="form-label">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="1"></textarea>
                            </div>

                            <div class="col-sm-6" id="OtrosDocumentos" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="descDocumento" class="form-label">Descripción del documento</label>
                                        <textarea name="descDocumento" id="descDocumento" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="detalleDocumento" class="form-label">Detalle del documento</label>
                                        <textarea name="detalleDocumento" id="detalleDocumento" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Efectivo -->
                            <div id="efectivo_fields" class="col-12 mt-3" style="display: none;">
                                <div class="col-sm-6 mt-3">
                                    <label class="form-label">Monto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark text-white">MONTO</span>
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
                                        <input type="text" class="form-control" id="numero_cheque"
                                            name="numero_cheque">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cuenta_bancaria_id" class="form-label">Cuenta emisora</label>
                                        <select name="cuenta_bancaria_id" id="cuenta_id" class="form-select select2">
                                            <option value="">Seleccionar cuenta emisora</option>
                                            @foreach ($cuentas_bancarias as $cuenta)
                                                <option value="{{ $cuenta->id }}">
                                                    {{ $cuenta->numero_cuenta }} - {{ $cuenta->moneda }} -
                                                    {{ $cuenta?->banco?->nombre }} | {{ $cuenta->titular }}
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
                                                    {{ $cuenta?->banco?->nombre }} | {{ $cuenta->titular }}
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
            <div class="col-12 col-md-4 d-flex align-self-start">
                <div class="card shadow-lg w-100 sticky-sidebar border-0 rounded-4">
                    <div class="card-body d-flex flex-column justify-content-between position-relative p-4">

                        <!-- Imagen decorativa centrada -->
                        <img src="{{ asset('assets/img/piggy-bank.png') }}" alt="Decoración"
                            class="mx-auto mb-3 opacity-50" style="max-width: 80px;">

                        <h5 class="text-center text-primary fw-bold mb-4 border-bottom pb-2">*** RESUMEN DE VENTAS ***</h5>

                        <div class="card bg-gradient bg-light border-0 shadow-sm mb-4 rounded-3">
                            <div class="card-body text-center">
                                <h3 class="text-success text-uppercase fw-bold mb-2">
                                    Total: $<span id="totalAmount">0.00</span>
                                </h3>
                                <h5 class="text-secondary">Artículos: <span id="totalItems">0</span></h5>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <span class="badge bg-warning text-dark fs-5 px-4 py-2 rounded-pill">
                                Cambio: $<span id="changeAmount">0.00</span>
                            </span>
                        </div>
                        <input type="hidden" name="cambio" id="cambioInput" value="0">

                        <div class="d-grid gap-2">
                            <button
                                class="btn btn-lg btn-primary fw-bold d-flex align-items-center justify-content-center gap-2"
                                type="button" id="guardarVenta">
                                GENERAR VENTA F5
                                <i class="bx bx-cart-download fs-3"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Modal con spinner -->
        <div class="modal fade" id="spinnerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div
                    class="modal-content bg-light border-0 shadow-lg rounded-4 text-center p-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h5 class="text-dark fw-semibold text-center">Generando venta, por favor espere...</h5>
                </div>
            </div>
        </div>
    </div>
    @include('postSales.scripts.scriptsPostventas')
@endsection
