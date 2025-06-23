@extends('layouts.sneatTheme.base')

@section('title', 'Ventas')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-center text-muted text-dark"><b>Resumen de ventas del mes</b></h6>
                    </div>
                    <div class="card-body">
                        <div id="graficoVentasMes"></div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h6>Realizar busqueda de ventas del mes</h6>
                    </div>
                    <div class="card-body">
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
                                    <button type="button" id="btn-filtrar" class="btn bg-label-primary w-100">
                                        <i class="bx bx-filter-alt"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="table-responsive">
                            <table id="tabla-ventas" class="table table-bordered table-hover table-stripe">
                                <thead class="bg-label-dark">
                                    <tr>
                                        <th>Id</th>
                                        <th>N° Documento</th>
                                        <th>Cliente</th>
                                        <th>Usuario</th>
                                        <th>Fecha pago</th>
                                        <th>Tipo pago</th>
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

        // Inicializa la tabla sin cargar datos inicialmente
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

        document.getElementById('btn-filtrar').addEventListener('click', function() {
            const clienteId = $('#cliente_id').val();
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();

            if (!clienteId && !fechaInicio && !fechaFin) {
                alert('Por favor selecciona al menos un filtro.');
                return;
            }

            // Fetch para el gráfico
            fetch('{{ route('ventas.resumen.mensual') }}?cliente_id=' + clienteId + '&fecha_inicio=' +
                    fechaInicio + '&fecha_fin=' + fechaFin)
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.querySelector("#graficoVentasMes");

                    if (!contenedor) {
                        console.error('No existe el contenedor del gráfico');
                        return;
                    }

                    if (data.length === 0) {
                        if (chart) {
                            chart.destroy();
                            chart = null;
                        }
                        contenedor.innerHTML =
                            '<p class="text-center text-muted">No hay datos para mostrar.</p>';
                        return;
                    }

                    const meses = data.map(item => item.mes);
                    const valores = data.map(item => parseFloat(item.total_ventas));

                    if (chart) {
                        chart.destroy();
                        chart = null;
                        contenedor.innerHTML = '';
                    }

                    var options = {
                        chart: {
                            type: 'donut',
                            height: 350,
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    selection: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
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
    });
</script>
