@extends('layouts.sneatTheme.base')
@section('title', 'Empresas')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Empresas del sistema.</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('empresas.add') }}" class="btn btn-primary">Agregar empresa</a>
                            <form action="{{ route('empresas.generarNuevoToken') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-success" {{ !$habilitarBoton ? 'disabled' : '' }}>
                                    Actualizar token
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Logo</th>
                                        <th class="text-left">Nombre</th>
                                        <th class="text-left">Nrc</th>
                                        <th class="text-left">Nit</th>
                                        <th class="text-left">Actividad</th>
                                        <th class="text-left">Departamento</th>
                                        <th class="text-left">Municipio</th>
                                        <th class="text-left">Tel</th>
                                        <th class="text-left">Correo</th>
                                        <th class="text-left">Complemento</th>
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
            ajax: '{!! route('empresas.getData') !!}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'logo',
                    name: 'logo'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'nrc',
                    name: 'nrc'
                },
                {
                    data: 'nit',
                    name: 'nit'
                },
                {
                    data: 'actividad',
                    name: 'actividad'
                },
                {
                    data: 'departamento',
                    name: 'departamento'
                },
                {
                    data: 'municipio',
                    name: 'municipio'
                },
                {
                    data: 'telefono',
                    name: 'telefono'
                },
                {
                    data: 'correo',
                    name: 'correo'
                },
                {
                    data: 'complemento',
                    name: 'complemento'
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
