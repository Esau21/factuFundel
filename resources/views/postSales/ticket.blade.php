<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta</title>
    <style>
        @page {
            size: 90mm 210mm;
            /* Ancho x Alto del papel */
            margin: 0;
            /* Elimina márgenes de la página */
        }

        body {
            background: #fff;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 72mm;
        }

        .ticket {
            padding: 10px;
            box-sizing: border-box;
        }

        .logo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .logo span {
            color: #8dc63f;
        }

        .header {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .data {
            margin-bottom: 10px;
        }

        .data p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 2px 0;
            text-align: left;
            word-break: break-word;
        }

        th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .total {
            font-weight: bold;
            font-size: 13px;
            text-align: right;
            margin-top: 10px;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .thank {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="ticket">
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

        @if (Str::startsWith($logoBase64, 'data:image'))
            <img src="{{ $logoBase64 }}" alt="Logo de la empresa" style="max-width: 120px; height: auto;">
        @else
            <img src="{{ $logoBase64 }}" alt="Logo alternativo" style="max-width: 120px; height: auto;">
        @endif


        <div class="header">
            <strong>{{ Auth::user()->empresa->nombre ?? '' }}</strong><br><br>
            {{ Auth::user()->empresa?->direccion ?? '' }}<br>
            (ZipCode) INTERIOR EXITO<br>
            Cel: {{ Auth::user()->empresa?->telefono ?? '' }}<br>
            Correo: {{ Auth::user()->empresa?->correo ?? '' }}
        </div>

        <div class="data">
            <p><strong>Nro Cliente:</strong> {{ str_pad($venta->clientes->id ?? 0, 10, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Cliente:</strong> {{ $venta->clientes->nombre }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y - h:i:s a') }}</p>
            <p><strong>PV:</strong> {{ str_pad($venta->punto_venta ?? 1, 4, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Cajero:</strong> {{ $venta->users->name ?? 'Desconocido' }}</p>
            <p><strong>Nro:</strong> {{ str_pad($venta->id, 7, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>CANT</th>
                    <th>DESCRIPCION</th>
                    <th>PRECIO</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->producto ? $detalle->producto->nombre : 'Producto no disponible' }}</td>
                        <td>{{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                        <td>{{ number_format($detalle->sub_total, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <hr>

        <!-- Total de la venta -->
        <div class="total">TOTAL = {{ number_format($venta->total, 2, ',', '.') }}</div>

        <!-- Monto recibido (si es necesario mostrarlo) -->
        @if ($venta->monto_recibido)
            <div class="total">MONTO RECIBIDO = {{ number_format($venta->monto_recibido, 2, ',', '.') }}</div>
        @endif

        <!-- Cambio (si el campo 'cambio' está presente en los detalles de la venta) -->
        @if ($detalle->cambio)
            <div class="total">CAMBIO = {{ number_format($detalle->cambio, 2, ',', '.') }}</div>
        @endif
        <hr>
        <hr>

        <div class="thank">Gracias por su compra</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
