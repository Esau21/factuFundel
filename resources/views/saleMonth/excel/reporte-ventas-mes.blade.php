<table>
    <thead>
        <tr>
            <th colspan="8" align="center" style="font-size: 20px;"><strong>Reporte de Ventas Mensual</strong></th>
        </tr>
        <tr>
            <th colspan="8">
                Fecha del reporte: {{ \Carbon\Carbon::now()->format('d-m-Y') }}
            </th>
        </tr>
        @if ($cliente)
            <tr>
                <th colspan="8">
                    Cliente: {{ $cliente->nombre }}
                </th>
            </tr>
        @endif
        @if ($fechaInicio && $fechaFin)
            <tr>
                <th colspan="8">
                    Desde: {{ $fechaInicio }} - Hasta: {{ $fechaFin }}
                </th>
            </tr>
        @endif
        <tr></tr>
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
    <tbody>
        @foreach ($ventas as $venta)
            <tr>
                <td>{{ $venta->id }}</td>
                <td>{{ $venta->documentoDte->numero_control ?? 'Sin dato' }}</td>
                <td>{{ $venta->clientes->nombre ?? '' }}</td>
                <td>{{ $venta->users->name ?? '' }}</td>
                <td>{{ $venta->fecha_venta }}</td>
                <td>{{ $venta->tipo_pago }}</td>
                <td>{{ number_format($venta->total + $venta->iva - $venta->retencion, 2) }}</td>
                <td>{{ $venta->status }}</td>
            </tr>
        @endforeach
        <tr></tr>
        <tr>
            <td colspan="6" align="right"><strong>Total General:</strong></td>
            <td colspan="2"><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
        </tr>
    </tbody>
</table>
