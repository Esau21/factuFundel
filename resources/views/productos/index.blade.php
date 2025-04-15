@extends('layouts.sneatTheme.base')

@section('title', 'Productos-listado')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Productos del sistema.</h5>
                        <div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProduct">Producto Nuevo</button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#cargaMasivaProductos"><i class="bx bx-cloud-download"></i> Importar</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column"
                                style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Imagen</th>
                                        <th class="text-left">Codigo</th>
                                        <th class="text-left">Nombre</th>
                                        <th class="text-left">Categoria</th>
                                        <th class="text-left">Descripcion</th>
                                        <th class="text-left">Precio_compra</th>
                                        <th class="text-left">Precio_venta</th>
                                        <th class="text-left">Stock</th>
                                        <th class="text-left">Stock_minimo</th>
                                        <th class="text-left">U.Medida</th>
                                        <th class="text-left">Marca</th>
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

    @include('productos.modal.form')
    @include('productos.modal.cargaMasiva')
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
            ajax: '{!! route('productos.getIndexDataProductos') !!}',
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'imagen',
                    name: 'imagen'
                },
                {
                    data: 'codigo',
                    name: 'codigo'
                },
                {
                    data: 'nombre',
                    name: 'nombre'
                },
                {
                    data: 'categoria',
                    name: 'categoria'
                },
                {
                    data: 'descripcion',
                    name: 'descripcion'
                },
                {
                    data: 'precio_compra',
                    name: 'precio_compra'
                },
                {
                    data: 'precio_venta',
                    name: 'precio_venta'
                },
                {
                    data: 'stock',
                    name: 'stock'
                },
                {
                    data: 'stock_minimo',
                    name: 'stock_minimo'
                },
                {
                    data: 'unidad_medida',
                    name: 'unidad_medida'
                },
                {
                    data: 'marca',
                    name: 'marca'
                },
                {
                    data: 'estado',
                    name: 'estado'
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
