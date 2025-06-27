@extends('layouts.sneatTheme.base')

@section('title', 'DocumentosDTE - Notas de Crédito')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h6>Emitir Nota de Crédito</h6>
                </div>
                <div class="card-body">
                    {{-- INFORMACIÓN DEL DOCUMENTO RELACIONADO --}}
                    <h5>Documento Relacionado</h5>
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <label>Tipo Documento</label>
                            <input type="text" class="form-control" readonly value="{{ $documento->tipo_documento }}">
                        </div>
                        <div class="col-sm-4">
                            <label>Número de Control</label>
                            <input type="text" class="form-control" readonly value="{{ $documento->numero_control }}">
                        </div>
                        <div class="col-sm-4">
                            <label>Código de Generación</label>
                            <input type="text" class="form-control" readonly value="{{ $documento->codigo_generacion }}">
                        </div>
                        <div class="col-sm-4">
                            <label>Fecha de Emisión</label>
                            <input type="text" class="form-control" readonly value="{{ $documento->fecha_emision }}">
                        </div>
                        <div class="col-sm-4">
                            <label>Cliente</label>
                            <input type="text" class="form-control" readonly value="{{ $documento->cliente->nombre }}">
                        </div>
                    </div>

                    {{-- FORMULARIO PARA LA NOTA DE DÉBITO --}}
                    <form method="POST" action="{{ route('facturacion.storeNotaCredito') }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="documento_relacionado_id" value="{{ $documento->id }}">
                        <h5>Datos de la Nota de Crédito</h5>
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Fecha Emisión Nota Crédito</label>
                                <input type="date" name="fecha_emision" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label>Hora Emisión</label>
                                <input type="time" name="hora_emision" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label>Tipo Moneda</label>
                                <select name="tipo_moneda" class="form-control" required>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>Detalle de Cargos (Motivo de la Nota de Crédito)</h5>
                        <div id="items-container">
                            <div class="item row mb-2">
                                <div class="col-sm-6">
                                    <label>Descripción</label>
                                    <input type="text" name="detalle[0][descripcion]" class="form-control"
                                        placeholder="Ej. Penalización, diferencia, ajuste..." required>
                                </div>
                                <div class="col-sm-2">
                                    <label>Cantidad</label>
                                    <input type="number" name="detalle[0][cantidad]" class="form-control" value="1"
                                        min="1" required>
                                </div>
                                <div class="col-sm-2">
                                    <label>Precio Unitario</label>
                                    <input type="number" step="0.01" name="detalle[0][precio]" class="form-control"
                                        required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success mb-3" onclick="agregarItem()">Agregar
                            Ítem</button>

                        <div class="form-group">
                            <label>Total en Letras</label>
                            <input type="text" name="total_letras" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Emitir Nota de Crédito</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function agregarItem() {
        let container = document.getElementById('items-container');
        let index = container.children.length;

        let item = `
                <div class="item row mb-2">
                    <div class="col-sm-6">
                        <label>Descripción</label>
                        <input type="text" name="detalle[${index}][descripcion]" class="form-control" placeholder="Ej. Penalización, diferencia, ajuste..." required>
                    </div>
                    <div class="col-sm-2">
                        <label>Cantidad</label>
                        <input type="number" name="detalle[${index}][cantidad]" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-sm-2">
                        <label>Precio Unitario</label>
                        <input type="number" step="0.01" name="detalle[${index}][precio]" class="form-control" required>
                    </div>
                </div>
            `;

        container.insertAdjacentHTML('beforeend', item);
    }
</script>
