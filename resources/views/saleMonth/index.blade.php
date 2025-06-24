@extends('layouts.sneatTheme.base')

@section('title', 'Ventas')

@section('content')
    <div class="container-fluid">
        <div class="row g-4">
            <!-- Gráfico de ventas -->
            <div class="col-12 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-center text-muted text-dark"><b>Resumen de ventas del mes</b></h6>
                    </div>
                    <div class="card-body">
                        <div id="graficoVentasMes"></div>
                    </div>
                    <div class="card-footer">
                        <h6 class="text-center text-dark">Descargar resumen de ventas del mes</h6>
                        <div class="mt-3">
                            <a href="#" id="btn-descargar-excel" class="btn bg-label-success w-100 mb-3"><i class="bx bx-file"
                                    style="font-size: 28px;"></i>Descargar excel</a>
                            <a href="#" id="btn-descargar-pdf" class="btn bg-label-danger w-100 mb-3"><i
                                    class="bx bx-file" style="font-size: 28px;"></i>Descargar pdf</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros + Tabla -->
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Búsqueda de Ventas del Mes</h6>
                    </div>
                    <div class="card-body">
                        <form id="filtro-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <label for="cliente_id" class="form-label">Cliente</label>
                                    <select id="cliente_id" name="cliente_id" class="form-select select2">
                                        <option value="">-- Todos los clientes --</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="fecha_inicio" class="form-label">Desde</label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="fecha_fin" class="form-label">Hasta</label>
                                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                                </div>
                                <div class="col-12 col-md-12 col-lg-4 d-flex align-items-end">
                                    <button type="button" id="btn-filtrar" class="btn bg-label-primary w-100">
                                        <i class="bx bx-filter-alt"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-footer">
                        <div class="table-responsive">
                            <table id="tabla-ventas" class="table table-bordered table-hover w-100">
                                <thead class="bg-label-dark text-white">
                                    <tr>
                                        <th>ID</th>
                                        <th>N° Documento</th>
                                        <th>Cliente</th>
                                        <th>Usuario</th>
                                        <th>Fecha Pago</th>
                                        <th>Tipo Pago</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let chart = null;

        let table = $('#tabla-ventas').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            paging: true,
            ordering: false,
            deferLoading: 0,
            lengthChange: false,
            ajax: {
                url: '{{ route('sales.getDetalleVentasMensual') }}',
                data: function(d) {
                    d.cliente_id = $('#cliente_id').val();
                    d.fecha_inicio = $('#fecha_inicio').val();
                    d.fecha_fin = $('#fecha_fin').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'numero_documento'
                },
                {
                    data: 'cliente'
                },
                {
                    data: 'usuario'
                },
                {
                    data: 'fecha_venta'
                },
                {
                    data: 'tipo_pago'
                },
                {
                    data: 'total'
                },
                {
                    data: 'status'
                }
            ]
        });

        $('#btn-filtrar').on('click', function() {
            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            if (!clienteId && !fechaInicio && !fechaFin) {
                alert('Por favor selecciona al menos un filtro.');
                return;
            }

            // Grafico resumen
            fetch('{{ route('ventas.resumen.mensual') }}?cliente_id=' + clienteId + '&fecha_inicio=' +
                    fechaInicio + '&fecha_fin=' + fechaFin)
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.querySelector("#graficoVentasMes");
                    if (!contenedor) return;

                    if (chart) {
                        chart.destroy();
                        chart = null;
                        contenedor.innerHTML = '';
                    }

                    if (data.length === 0) {
                        contenedor.innerHTML =
                            '<p class="text-center text-muted">No hay datos para mostrar.</p>';
                        return;
                    }

                    const meses = data.map(item => item.mes);
                    const valores = data.map(item => parseFloat(item.total_ventas));

                    const options = {
                        chart: {
                            type: 'donut',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        labels: meses,
                        series: valores,
                        dataLabels: {
                            enabled: true
                        },
                        title: {
                            text: 'Resumen de Ventas por Mes',
                            align: 'center'
                        }
                    };

                    chart = new ApexCharts(contenedor, options);
                    chart.render();
                });

            table.ajax.reload();
        });


        document.getElementById('btn-descargar-excel').addEventListener('click', function(e) {
            e.preventDefault();
            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            const url =
                `{{ route('ventas.descargar.excel') }}?cliente_id=${clienteId}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            window.open(url, '_blank');
        });

        document.getElementById('btn-descargar-pdf').addEventListener('click', function(e) {
            e.preventDefault();
            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            const url =
                `{{ route('ventas.descargar.pdf') }}?cliente_id=${clienteId}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            window.open(url, '_blank');
        });
    });
</script>
