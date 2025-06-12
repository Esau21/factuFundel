<h2>Historial de ventas</h2>

@if ($clienteId)
    <p>Cliente: {{ ($clienteId)->nombre ?? 'Desconocido' }}</p>
@endif

@if ($fechaInicio)
    <p>Desde: {{ $fechaInicio }}</p>
@endif

@if ($fechaFin)
    <p>Hasta: {{ $fechaFin }}</p>
@endif

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha venta</th>
            <th>Total</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ventas as $venta)
            <tr>
                <td>{{ $venta->id }}</td>
                <td>{{ $venta->clientes->nombre ?? 'sin data' }}</td>
                <td>{{ $venta->fecha_venta }}</td>
                <td>${{ number_format($venta->total + $venta->iva, 2) }}</td>
                <td>{{ $venta->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
