@extends('layouts.sneatTheme.base')

@section('title', 'DocumentosDTE - Facturación')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Documentos Tributarios Electrónicos.</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group d-flex flex-wrap gap-1" role="group" aria-label="Filtrar por tipo">
                            <button class="btn bg-label-secondary filter-btn active px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="">
                                <i class="icon-base bx bx-receipt me-2"></i>Todos
                            </button>
                            <button class="btn bg-label-primary filter-btn px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="01">
                                <i class="icon-base bx bx-receipt me-2"></i>Factura
                            </button>
                            <button class="btn bg-label-success filter-btn px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="03">
                                <i class="icon-base bx bx-receipt me-2"></i>Comprobante de crédito fiscal
                            </button>
                            <button class="btn bg-label-dark filter-btn px-4 py-3 fw-semibold text-nowrap" data-tipo="14">
                                <i class="icon-base bx bx-receipt me-2"></i>Sujeto excluido
                            </button>
                            <button class="btn bg-label-warning filter-btn px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="15">
                                <i class="icon-base bx bx-receipt me-2"></i>Comprobante de donación
                            </button>
                            <button class="btn bg-label-info filter-btn px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="05">
                                <i class="icon-base bx bx-receipt me-2"></i>Nota de Crédito
                            </button>
                            <button class="btn bg-label-danger filter-btn px-4 py-3 fw-semibold text-nowrap"
                                data-tipo="06">
                                <i class="icon-base bx bx-receipt me-2"></i>Nota de Débito
                            </button>
                        </div>
                    </div>
                    <div class="card-body" class="pt-0">
                        <form action="" id="">
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
                                    <button type="button" id="btn-filtrar" class="btn bg-label-primary w-100">
                                        <i class="bx bx-filter-alt"></i> Filtrar
                                    </button>
                                </div>
                                <div class="col-sm-2 d-flex align-items-end mt-2">
                                    <button type="button" id="btn-descargar-historial" class="btn bg-label-success w-100">
                                        <i class="bx bxs-file" style="font-size: 20px; transition: transform 0.2s;"></i>
                                        Descagar xlsx
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left" style="display: none;">Id</th>
                                        <th class="text-left">Tipo documento</th>
                                        <th class="text-left">Emitir nota</th>
                                        <th class="text-left">Numero control</th>
                                        <th class="text-left">Codigo generacíon</th>
                                        <th class="text-left">Fecha de Emision</th>
                                        <th class="text-left">Cliente</th>
                                        <th class="text-left">Empresa</th>
                                        <th class="text-left">Sello recibido</th>
                                        <th class="text-left">Estado</th>
                                        <th class="text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="no-search" style="display: none;"></th>
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
    @include('dgii.modal.anularJson')
    @include('dgii.modal.reenviarDte')
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        let tipoSeleccionado = '';
        const table = $('#sysconta-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pagingType: 'simple_numbers',
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '{!! route('facturacion.indexGetDtaDocumentosDte') !!}',
                data: function(d) {
                    d.tipo = tipoSeleccionado;
                    d.cliente_id = $('#cliente_id').val();
                    d.fecha_inicio = $('#fecha_inicio').val();
                    d.fecha_fin = $('#fecha_fin').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'tipo_documento',
                    name: 'tipo_documento'
                },
                {
                    data: 'emitir_invalidacion',
                    name: 'emitir_invalidacion'
                },
                {
                    data: 'numero_control',
                    name: 'numero_control'
                },
                {
                    data: 'codigo_generacion',
                    name: 'codigo_generacion'
                },
                {
                    data: 'fecha_emision',
                    name: 'fecha_emision'
                },
                {
                    data: 'cliente',
                    name: 'cliente'
                },
                {
                    data: 'empresa',
                    name: 'empresa'
                },
                {
                    data: 'sello_recibido',
                    name: 'sello_recibido'
                },
                {
                    data: 'estado',
                    name: 'estado'
                },
                {
                    data: 'acciones',
                    name: 'acciones',
                    orderable: false,
                    searchable: false
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


        $('.filter-btn').on('click', function() {
            /* Cambiar la clase activa en los botones de filtro */
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            /* Obtener el tipo de cliente seleccionado */
            tipoSeleccionado = $(this).data('tipo');

            /* Recargar la tabla con el nuevo filtro */
            table.ajax.reload();
        });

        $('#btn-descargar-historial').prop('disabled', true);

        $('#btn-filtrar').click(function() {
            $('#sysconta-datatable').DataTable().ajax.reload();

            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            if (clienteId || fechaInicio || fechaFin) {
                $('#btn-descargar-historial').prop('disabled', false);
            } else {
                $('#btn-descargar-historial').prop('disabled', true);
            }
        });

        $('#btn-descargar-historial').on('click', function() {
            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            let query = `?cliente_id=${clienteId}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            window.location.href = '{{ route('facturacion.historialDTEFechasXlsx') }}' + query;
        });

    });
</script>
