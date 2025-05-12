@extends('layouts.sneatTheme.base')

@section('title', 'Sales-day')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Ventas del dia.</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Cliente</th>
                                        <th class="text-left">Usuario</th>
                                        <th class="text-left">fecha venta</th>
                                        <th class="text-left">Tipo pago</th>
                                        <th class="text-left">Total</th>
                                        <th class="text-left">Estado</th>
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
    @include('salesday.modal.viewdetailssales')
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
            ajax: '{!! route('sales.ventasDelDia') !!}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'cliente',
                    name: 'cliente'
                },
                {
                    data: 'usuario',
                    name: 'usuario'
                },
                {
                    data: 'fecha_venta',
                    name: 'fecha_venta'
                },
                {
                    data: 'tipo_pago',
                    name: 'tipo_pago'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'status',
                    name: 'status'
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
    });
</script>
