@extends('layouts.sneatTheme.base')

@section('title', 'Bancos Cheques-Recibidos')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Bancos Cheques Recibidos.</h5>
                    </div>
                    <div class="card-body">
                        @can('cheques_form')
                            <form id="filtro-form">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="cliente_id">Cliente:</label>
                                        <select id="cliente_id" name="cliente_id" class="form-select select2 w-100">
                                            <option value="">-- Todos los clientes --</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="fecha_inicio">Desde:</label>
                                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="fecha_fin">Hasta:</label>
                                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end">
                                        <button type="button" id="btn-filtrar-cheques" class="btn bg-label-primary w-100">
                                            <i class="bx bx-filter-alt"></i> Filtrar
                                        </button>
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-end mt-2">
                                        <button type="button" id="btn-descargar-historialCheques"
                                            class="btn btn-sm bg-label-warning w-100">
                                            <i class="bx bxs-file" style="font-size: 30px; transition: transform 0.2s;"></i>
                                            Descagar historial cheques
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endcan
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Cliente</th>
                                        <th class="text-left">Cuenta Destino</th>
                                        <th class="text-left">N°Cheque</th>
                                        <th class="text-left">Monto</th>
                                        <th class="text-left">Fecha emision</th>
                                        <th class="text-left">Fecha pago</th>
                                        <th class="text-left">Estado</th>
                                        <th class="text-left">Observaciones</th>
                                        <th class="text-left">Correlativos</th>
                                        <th class="text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="no-search"></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="no-search"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        $('#sysconta-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pagingType: 'simple_numbers',
            ajax: {
                url: '{!! route('cheques.getIndexDataCheque') !!}',
                data: function(d) {
                    d.cliente_id = $('#cliente_id').val();
                    d.fecha_inicio = $('#fecha_inicio').val();
                    d.fecha_fin = $('#fecha_fin').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'cliente',
                    name: 'cliente'
                },
                {
                    data: 'cuenta',
                    name: 'cuenta'
                },
                {
                    data: 'numero_cheque',
                    name: 'numero_cheque'
                },
                {
                    data: 'monto',
                    name: 'monto'
                },
                {
                    data: 'fechaEmi',
                    name: 'fechaEmi'
                },
                {
                    data: 'fechaPago',
                    name: 'fechaPago'
                },
                {
                    data: 'estado',
                    name: 'estado'
                },
                {
                    data: 'observaciones',
                    name: 'observaciones'
                },
                {
                    data: 'correlativo',
                    name: 'correlativo'
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: true,
                    searchable: true
                }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json',
                lengthMenu: 'Mostrar _MENU_ registros'
            },
            dom: '<"top d-flex justify-content-between align-items-center mb-3"fl>rt<"bottom d-flex justify-content-between align-items-center mt-3"ip><"clear">',
            initComplete: function() {
                $('#sysconta-datatable tfoot th').each(function() {
                    if (!$(this).hasClass('no-search')) {
                        $(this).html(
                            '<input type="text" class="form-control form-control-lg" placeholder="Buscar..." style="font-size: 0.85rem;" />'
                        );
                    }
                });

                const table = this.api();

                $('#sysconta-datatable tfoot input').on('keyup change', function(e) {
                    if (e.key === 'Enter' || e.type === 'change') {
                        table.column($(this).parent().index()).search(this.value).draw();
                    }
                });
            },
            drawCallback: function() {
                $('#sysconta-datatable_length label').css({
                    'margin-right': '10px',
                    'font-size': '0.9rem'
                });

                $('#sysconta-datatable_length select').addClass('form-select form-select-md').css({
                    'width': '80px',
                    'margin-left': '8px',
                    'display': 'inline-block',
                    'vertical-align': 'middle'
                });

                $('#sysconta-datatable_filter input')
                    .removeClass('form-control-lg')
                    .addClass('form-control')
                    .attr('placeholder', 'Buscar...')
                    .css({
                        'width': '160px',
                        'display': 'inline-block',
                        'font-size': '0.85rem'
                    });

                $('.dataTables_paginate').addClass('pagination-sm');
                $('.dataTables_paginate .paginate_button').css({
                    'padding': '0.25rem 0.5rem',
                    'font-size': '0.85rem'
                });
            }
        });

        $('#btn-descargar-historialCheques').prop('disabled', true);

        $('#btn-filtrar-cheques').click(function() {
            $('#sysconta-datatable').DataTable().ajax.reload();

            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            if (clienteId || fechaInicio || fechaFin) {
                $('#btn-descargar-historialCheques').prop('disabled', false);
            } else {
                $('#btn-descargar-historialCheques').prop('disabled', true);
            }
        });

        $('#btn-descargar-historialCheques').click(function() {
            if ($(this).prop('disabled')) return;

            let clienteId = $('#cliente_id').val();
            let fechaInicio = $('#fecha_inicio').val();
            let fechaFin = $('#fecha_fin').val();

            let url = `/cheques/historial/pdf?`;

            if (clienteId) url += `cliente_id=${clienteId}&`;
            if (fechaInicio) url += `fecha_inicio=${fechaInicio}&`;
            if (fechaFin) url += `fecha_fin=${fechaFin}&`;

            url = url.slice(0, -1);

            window.open(url, '_blank');
        });
    });
</script>
