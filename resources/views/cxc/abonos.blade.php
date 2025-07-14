@extends('layouts.sneatTheme.base')

@section('title', 'Cuentas por Cobrar')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Abonos cuentas por cobrar</h3>
                </div>
                <div class="card-body">
                    <form id="abonoForm" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="cuenta-select">Cuenta por Cobrar</label>
                                <select class="form-control select2" name="cuenta_por_cobrar_id" id="cuenta-select"
                                    required>
                                    <option value="" selected disabled>Seleccione una cuenta por cobrar...</option>
                                    @foreach ($cuentasPorCobrarPendientes as $cuenta)
                                        <option value="{{ $cuenta->id }}">
                                            {{ 'Venta #' . $cuenta->sale->id . ' - Cliente: ' . $cuenta->sale->clientes->nombre . ' - Saldo pendiente: $' . number_format($cuenta->saldo_pendiente, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="monto">Monto a abonar</label>
                                <input type="number" step="any" name="monto" id="monto" class="form-control"
                                    required min="0.01" max="9999999">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="fecha_abono">Fecha de abono</label>
                                <input type="date" name="fecha_abono" id="fecha_abono" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="metodo_pago">Método de pago</label>
                                <select name="metodo_pago" id="metodo_pago" class="form-control" required>
                                    <option value="" disabled selected>Seleccione método</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <button type="submit" class="btn bg-label-primary">Registrar Abono</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        $('#cliente-select').select2({
            placeholder: 'Seleccione un cliente',
            allowClear: true
        });
        $('#cuentas-select').select2({
            placeholder: 'Seleccione una cuenta',
            allowClear: true
        });

        $("#abonoForm").on('submit', function(e) {
            e.preventDefault();
            let url = "{{ route('store.abono.cuenta') }}";
            var btnSubmit = $(this);
            btnSubmit.prop('disabled', true);
            $.ajax({
                url: url,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function() {
                    Toastify({
                        text: "Abono guardado correctamente.",
                        className: "success",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                },
                error: function(e) {
                    if (e.status === 422) {
                        let errors = e.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value.join('<br>');
                        });
                        Swal.fire({
                            title: 'Errores de validación.',
                            html: errorMessage,
                            icon: 'error',
                        });
                    } else if (e.status === 405) {
                        Swal.fire({
                            title: 'Error',
                            text: e.responseJSON.error,
                            icon: 'error',
                        });
                    } else {
                        Swal.fire({
                            title: 'Algo salió mal al insertar los datos.',
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });
</script>
