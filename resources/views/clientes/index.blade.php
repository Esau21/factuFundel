@extends('layouts.sneatTheme.base')
@section('title', 'Clientes')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="card-title mb-0 w-100 w-md-auto">Clientes del sistema.</h5>
                        <div class="d-flex flex-wrap gap-2 align-items-center w-100 w-md-auto justify-content-md-end">
                            <div class="btn-group" role="group" aria-label="Filtrar por tipo">
                                <button class="btn btn-outline-secondary filter-btn active" data-tipo="">Todos</button>
                                <button class="btn btn-success filter-btn" data-tipo="natural">Natural</button>
                                <button class="btn btn-dark filter-btn" data-tipo="juridica">Jurídico</button>
                            </div>
                            <a href="{{ route('clientes.add') }}" class="btn btn-primary mt-2 mt-md-0">
                                <i class="bx bx-plus"></i> Cliente Nuevo
                            </a>
                        </div>
                    </div>                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Nombre</th>
                                        <th class="text-left">Nombre Comercial</th>
                                        <th class="text-left">Tipo documento</th>
                                        <th class="text-left">Numero documento</th>
                                        <th class="text-left">Nit</th>
                                        <th class="text-left">Nrc</th>
                                        <th class="text-left">Giro</th>
                                        <th class="text-left">Direccion</th>
                                        <th class="text-left">Departamento</th>
                                        <th class="text-left">Municipio</th>
                                        <th class="text-left">Telefono</th>
                                        <th class="text-left">Correo</th>
                                        <th class="text-left">Tipo Contribuyente</th>
                                        <th class="text-left">Tipo persona</th>
                                        <th class="text-left">Es extranjero</th>
                                        <th class="text-left">Pais</th>
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
        let tipoSeleccionado = '';

        const table = $('#sysconta-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pagingType: 'simple_numbers',
            ajax: {
                url: '{!! route('clientes.getIndexDataClientes') !!}',
                data: function(d) {
                    d.tipo = tipoSeleccionado;
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'nombreComercial',
                    name: 'nombreComercial'
                },
                {
                    data: 'tipo_documento',
                    name: 'tipo_documento'
                },
                {
                    data: 'numero_documento',
                    name: 'numero_documento'
                },
                {
                    data: 'nit',
                    name: 'nit'
                },
                {
                    data: 'nrc',
                    name: 'nrc'
                },
                {
                    data: 'actividad',
                    name: 'actividad'
                },
                {
                    data: 'direccion',
                    name: 'direccion'
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
                    data: 'correo_electronico',
                    name: 'correo_electronico'
                },
                {
                    data: 'tipo_contribuyente',
                    name: 'tipo_contribuyente'
                },
                {
                    data: 'tipo_persona',
                    name: 'tipo_persona'
                },
                {
                    data: 'es_extranjero',
                    name: 'es_extranjero'
                },
                {
                    data: 'pais',
                    name: 'pais'
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
                /* Inicialización de los filtros de búsqueda en el pie de tabla */
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

        /* Manejo de filtros */
        $('.filter-btn').on('click', function() {
            /* Cambiar la clase activa en los botones de filtro */
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            /* Obtener el tipo de cliente seleccionado */
            tipoSeleccionado = $(this).data('tipo');

            /* Recargar la tabla con el nuevo filtro */
            table.ajax.reload();
        });
    });
</script>
