@extends('layouts.sneatTheme.base')

@section('title', 'Asignacion-de-permisos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header">
                    <h3 class="card-title mb-0">Permisos y asignación al sistema</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('asignar.storeAsignarPermisosRoles') }}" method="POST" id="form">
                        @csrf
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role_id" class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                                        <i class='bx bx-error-circle fs-4'></i>
                                        ¡Importante! Tienes que seleccionar un rol
                                      </label>                                                                        
                                    <select class="form-control select2" name="role_id" id="role_id" style="width: 100%;">
                                        <option value="">Elegir</option>
                                        @foreach ($roles as $r)
                                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <button type="button" id="asignar-todo" class="btn btn-outline-success w-100">
                                    Asignar todo <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>

                            <div class="col-md-4">
                                <button type="button" id="revocar-todo" class="btn btn-outline-danger w-100">
                                    Revocar todo <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="sysconta-datatable" class="display cell-border stripe hover order-column">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-left">ID</th>
                                        <th class="text-left">Nombre</th>
                                        <th class="text-left">Asignar</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="no-search"></th>
                                        <th></th>
                                        <th class="no-search"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: 'resolve',
        });

        let table = $('#sysconta-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pagingType: 'simple_numbers',
            ajax: {
                url: '{!! route('asignar.getDataIndexAsiganr') !!}',
                data: function(d) {
                    d.role_id = $('#role_id').val();
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
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

        $('#role_id').on('change', function() {
            table.ajax.reload();
        });

        $(document).on('change', 'input[name="check"]', function() {
            let permissionId = $(this).data('id');
            let roleId = $('#role_id').val();

            if (!roleId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un rol antes de asignar permisos.',
                    icon: 'warning',
                });
                return;
            }

            let isChecked = $(this).is(':checked') ? true : false;

            $.ajax({
                url: "{{ route('asignar.storeAsignarPermisosRoles') }}",
                method: 'POST',
                data: {
                    role_id: roleId,
                    permission_id: permissionId,
                    assign: isChecked
                },
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function() {
                    Swal.fire({
                        title: 'Genial',
                        text: isChecked ? 'PERMISO ASIGNADO.' : 'PERMISO REVOCADO.',
                        icon: isChecked ? 'success' : 'warning',
                    });
                },
                error: function(e) {
                    Swal.fire({
                        title: 'Error',
                        text: e.responseJSON.error,
                        icon: 'error',
                    });
                }
            });
        });

        $('#asignar-todo').on('click', function() {
            let roleId = $('#role_id').val();
            if (!roleId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un rol antes de asignar todos los permisos.',
                    icon: 'warning',
                });
                return;
            }

            $.ajax({
                url: "{{ route('asignar.AsignarTodo') }}",
                method: 'POST',
                data: {
                    role_id: roleId
                },
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function() {
                    Swal.fire({
                        title: 'Genial',
                        text: 'Todos los permisos fueron asignados.',
                        icon: 'success',
                    });
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Algo salió mal al asignar todos los permisos.',
                        icon: 'error',
                    });
                }
            });
        });


        $('#revocar-todo').on('click', function() {
            let roleId = $('#role_id').val();
            if (!roleId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un rol antes de revocar todos los permisos.',
                    icon: 'warning',
                });
                return;
            }

            $.ajax({
                url: "{{ route('asignar.RevocarTodo') }}",
                method: 'POST',
                data: {
                    role_id: roleId
                },
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function() {
                    Swal.fire({
                        title: 'Genial',
                        text: 'Todos los permisos fueron revocados.',
                        icon: 'success',
                    });
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Algo salió mal al asignar todos los permisos.',
                        icon: 'error',
                    });
                }
            });
        });
    });
</script>
