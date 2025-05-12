h<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cotización #{{ $venta->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 40px;
            background-color: #fff;
            color: #000;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }

        .company-info {
            text-align: right;
            font-size: 13px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .client-info,
        .details {
            margin-bottom: 20px;
        }

        .client-info p,
        .company-info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
        }

        .totals {
            text-align: right;
            font-weight: bold;
        }

        .thank {
            text-align: center;
            margin-top: 40px;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            @php
                $logoPath = public_path('storage/' . Auth::user()->empresa->logo);
                if (file_exists($logoPath)) {
                    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($logoPath);
                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    $logoBase64 = asset('img/camara1.png');
                }
            @endphp
            <img src="{{ $logoBase64 }}" alt="Logo Empresa">
        </div>
        <div class="company-info">
            <strong>{{ Auth::user()->empresa->nombre ?? '' }}</strong><br>
            {{ Auth::user()->empresa?->direccion ?? '' }}<br>
            Tel: {{ Auth::user()->empresa?->telefono ?? '' }}<br>
            Email: {{ Auth::user()->empresa?->correo ?? '' }}
        </div>
    </header>

    <div class="title">Cotización</div>

    <div class="client-info">
        <p><strong>Cliente:</strong> {{ $venta->clientes->nombre }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y - h:i:s a') }}</p>
        <p><strong>Punto de Venta:</strong> {{ str_pad($venta->punto_venta ?? 1, 4, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Cajero:</strong> {{ $venta->users->name ?? 'Desconocido' }}</p>
        <p><strong>N° Cotización:</strong> {{ str_pad($venta->id, 7, '0', STR_PAD_LEFT) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Descripción</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                    <td>{{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                    <td>{{ number_format($detalle->sub_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total: {{ number_format($venta->total, 2, ',', '.') }}</p>

        @if ($venta->monto_recibido)
            <p>Monto Recibido: {{ number_format($venta->monto_recibido, 2, ',', '.') }}</p>
        @endif

    </div>

    <div class="thank">Gracias por confiar en nosotros</div>

    <div class="footer">Cotización generada por el sistema - No es comprobante fiscal</div>
</body>

</html>