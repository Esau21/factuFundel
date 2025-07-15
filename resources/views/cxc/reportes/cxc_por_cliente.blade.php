<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Reporte Cuentas por Cobrar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0 30px;
        }

        /* Para que DomPDF muestre bordes */
        table,
        th,
        td {
            border: 1px solid #444 !important;
        }

        /* Estilo encabezado */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 10px;
            border-bottom: 2px solid #007BFF;
            margin-bottom: 1rem;
        }

        .header-logo img {
            max-height: 70px;
        }

        .header-info {
            text-align: right;
            font-size: 0.9rem;
            color: #555;
        }

        .title {
            text-align: center;
            color: #007BFF;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Tabla */
        .table thead {
            background-color: #007BFF;
            color: white;
            text-align: center;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Alinear montos a la derecha */
        .text-right {
            text-align: right !important;
        }

        /* Pie de página */
        .footer {
            font-size: 0.8rem;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 3rem;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-logo">
            <img src="{{ $detalles_empresa->imagen }}" alt="Logo Empresa" />
        </div>
        <div class="header-info">
            <div><strong>{{ $detalles_empresa->nombre ?? 'Nombre Empresa' }}</strong></div>
            <div>{{ $detalles_empresa->correo ?? '' }}</div>
            <div>{{ $detalles_empresa->telefono ?? '' }}</div>
            <div>{{ $detalles_empresa->nit ?? '' }}</div>
        </div>
    </header>

    <h1 class="title">Reporte Cuentas por Cobrar</h1>

    <p><strong>Cliente:</strong> {{ $cliente->nombre ?? 'Desconocido' }}</p>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Venta#</th>
                <th scope="col">Monto</th>
                <th scope="col">Fecha de Pago</th>
                <th scope="col">Saldo Pendiente</th>
                <th scope="col">Método de Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cuentas as $cuenta)
                <tr>
                    <td class="text-center">{{ $cuenta->id }}</td>
                    <td class="text-center">{{ $cuenta->sale_id }}</td>
                    <td class="text-right">${{ number_format($cuenta->monto, 2) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($cuenta->fecha_pago)->format('d/m/Y') }}</td>
                    <td class="text-right">${{ number_format($cuenta->saldo_pendiente, 2) }}</td>
                    <td class="text-center">
                        @switch($cuenta->metodo_pago)
                            @case('01') Efectivo @break
                            @case('04') Cheque @break
                            @case('05') Transferencia @break
                            @default Otro
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer class="footer">
        Reporte generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </footer>
</body>

</html>
