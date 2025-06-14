<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cheque</title>
    <link href="{{ public_path('css/custom_pdf.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('css/custom_page.css') }}" rel="stylesheet" type="text/css" />
</head>

<body style="margin: 0; padding: 0; font-size: 12px;">
    <!-- Contenedor que centra el cheque vertical y horizontal -->
    <div style="width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center;">

        <!-- Cheque -->
        <div
            style="
            width: 90%;
            max-width: 850px;
            height: 250px;
            border: 2px solid black;
            border-radius: 10px;
            padding: 25px 35px;
            position: relative;
        ">

            <!-- Logo -->
            <div style="position: absolute; top: 20px; left: 30px;">
                <img src="{{ empresaLogo() }}" alt="Logo" width="80">
            </div>

            <!-- Número de cheque -->
            <div style="position: absolute; top: 20px; right: 30px; font-weight: bold;">
                Cheque No. {{ $cheques->numero_cheque }}
            </div>

            <!-- Fecha -->
            <div style="position: absolute; top: 50px; right: 30px;">
                Fecha: {{ \Carbon\Carbon::parse($cheques->fecha_emision)->format('d/m/Y') }}
            </div>

            <!-- Beneficiario -->
            <div style="position: absolute; top: 85px; left: 30px; right: 30px;">
                <span>Pagar a la orden de:</span>
                <span style="margin-left: 10px; font-weight: bold;">
                    {{ $cheques->cliente->nombre ?? 'Cliente desconocido' }}
                </span>
                <div style="border-bottom: 1px solid black; margin-top: 5px;"></div>
            </div>

            <!-- Monto numérico -->
            <div style="position: absolute; top: 120px; right: 30px; font-weight: bold;">
                ${{ number_format($cheques->monto, 2) }}
            </div>

            <!-- Monto en letras -->
            <div style="position: absolute; top: 150px; left: 30px; right: 30px;">
                La cantidad de: <strong>{{ \App\Helpers\NumeroALetras::convertir($cheques->monto) }}</strong>
            </div>

            <!-- Información de cuenta -->
            <div style="position: absolute; bottom: 35px; left: 30px; font-size: 10px; line-height: 1.3;">
                Banco: {{ $cheques->cuenta->banco->nombre ?? 'N/A' }}<br>
                Cuenta: {{ $cheques->cuenta->numero_cuenta ?? 'N/A' }}<br>
                Titular: {{ $cheques->cuenta->titular ?? 'N/A' }}
            </div>

            <!-- Firma -->
            <div style="position: absolute; bottom: 20px; right: 30px; text-align: right; font-size: 11px;">
                Firma Autorizada
                <div style="border-top: 1px solid black; width: 160px; margin-top: 2px; float: right;"></div>
            </div>
        </div>

    </div>
</body>

</html>
