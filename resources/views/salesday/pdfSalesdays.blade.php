<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura #{{ str_pad($sale->id, 7, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            margin: 40px;
            background-color: #fff;
            color: #000;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 120px;
            height: auto;
        }

        .company-info {
            text-align: right;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .client-info,
        .sale-info {
            font-size: 12px;
            line-height: 1.6;
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
        }

        th {
            background-color: #f5f5f5;
        }

        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px;
            border: 1px solid #ccc;
            text-align: right;
        }

        .totals-table td:first-child {
            text-align: left;
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .thank {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
            font-size: 13px;
        }

        .footer {
            position: fixed;
            bottom: 20px;
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

    <div class="title">Factura / Cotización N° {{ str_pad($sale->id, 7, '0', STR_PAD_LEFT) }}</div>

    <div class="info-section">
        <div class="client-info">
            <p><strong>Cliente:</strong> {{ $sale->clientes->nombre }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($sale->fecha_venta)->format('d/m/Y - h:i:s a') }}</p>
            <p><strong>Punto de Venta:</strong> {{ str_pad($sale->punto_venta ?? 1, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="sale-info">
            <p><strong>Cajero:</strong> {{ $sale->users->name ?? 'Desconocido' }}</p>
            <p><strong>Método de Pago:</strong> {{ $sale->tipo_pago ?? '---' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:10%">Cant.</th>
                <th style="width:50%">Descripción</th>
                <th style="width:20%">Precio Unitario</th>
                <th style="width:20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                    <td>${{ number_format($detalle->sub_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Total:</td>
            <td>${{ number_format($sale->total, 2, ',', '.') }}</td>
        </tr>
        @if ($sale->monto_recibido)
            <tr>
                <td>Monto Recibido:</td>
                <td>${{ number_format($sale->monto_recibido, 2, ',', '.') }}</td>
            </tr>
        @endif

        <!-- Cambio (si el campo 'cambio' está presente en los detalles de la venta) -->
        @if ($detalle->cambio)
            <tr>
                <td>Cambio:</td>
                <td>${{ number_format($detalle->cambio, 2, ',', '.') }}</td>
            </tr>
        @endif



    </table>

    <div class="thank">Gracias por confiar en nosotros</div>

    <div class="footer">Documento generado por el sistema - No es comprobante fiscal</div>
</body>

</html>
