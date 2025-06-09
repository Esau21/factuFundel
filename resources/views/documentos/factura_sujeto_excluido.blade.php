<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>FACTURA SUJETO EXCLUIDO</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: auto;
            border: 1px solid #000;
            position: relative;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        .header,
        .section {
            margin-bottom: 6px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 2px;
            border-bottom: 1px solid #000;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .box {
            border: 1px solid #000;
            padding: 4px;
            margin-bottom: 4px;
        }

        .qr {
            text-align: center;
        }

        .table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .table th,
        .table td,
        .summary-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        .summary-table td {
            text-align: left;
        }

        .footer {
            text-align: right;
            margin-top: 10px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
        }
    </style>
</head>

<body>

    <div class="page">
        <div class="text-center header">
            <div class="bold">DOCUMENTO TRIBUTARIO ELECTRÓNICO</div>
            <div class="bold">FACTURA SUJETO EXCLUIDO</div>
        </div>

        <div class="text-right">Ver.1</div>

        <div class="flex header">
            <div>
                <div><strong>Código de Generación:</strong> {{ $json['identificacion']['codigoGeneracion'] }}</div>
                <div><strong>Número de Control:</strong> {{ $json['identificacion']['numeroControl'] }}</div>
                <div><strong>Sello de recepción:</strong> {{ $mh['selloRecibido'] ?? '—' }}</div>
            </div>
            @php
                $qrUrl =
                    'https://admin.factura.gob.sv/consultaPublica?ambiente=' .
                    $json['identificacion']['ambiente'] .
                    '&codGen=' .
                    $json['identificacion']['codigoGeneracion'] .
                    '&fechaEmi=' .
                    $json['identificacion']['fecEmi'];
            @endphp
            <div class="qr">
                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}" width="90"
                    alt="QR">
            </div>
            <div class="text-right">
                <div><strong>Modelo de Facturación:</strong> {{ $json['identificacion']['tipoModelo'] }}</div>
                <div><strong>Tipo de Transmisión:</strong> {{ $json['identificacion']['tipoOperacion'] }}</div>
                <div><strong>Fecha y Hora de Generación:</strong> {{ $json['identificacion']['fecEmi'] }}
                    {{ $json['identificacion']['horEmi'] }}</div>
            </div>
        </div>

        <div class="flex">
            <div class="box" style="width: 49%;">
                <strong>EMISOR</strong><br>
                Nombre o razón social: {{ $json['emisor']['nombre'] }}<br>
                NIT: {{ $json['emisor']['nit'] }}<br>
                NRC: {{ $json['emisor']['nrc'] }}<br>
                Actividad económica: {{ $json['emisor']['descActividad'] }}<br>
                Dirección: {{ $json['emisor']['direccion']['complemento'] }}<br>
                Número de teléfono: {{ $json['emisor']['telefono'] }}<br>
                Correo electrónico: {{ $json['emisor']['correo'] }}<br>
            </div>
            <div class="box" style="width: 49%;">
                <strong>RECEPTOR</strong><br>
                Nombre o razón social: {{ $json['sujetoExcluido']['nombre'] }}<br>
                Tipo de doc. de Identificación: {{ $json['sujetoExcluido']['tipoDocumento'] }}<br>
                N° de doc. de Identificación: {{ $json['sujetoExcluido']['numDocumento'] }}<br>
                N° de teléfono: {{ $json['sujetoExcluido']['telefono'] }}<br>
                Dirección: {{ $json['sujetoExcluido']['direccion']['complemento'] }}<br>
            </div>
        </div>

        <div class="box">
            <strong>OTROS DOCUMENTOS ASOCIADOS</strong><br>
            Identificación del documento: ____________________ Descripción: ____________________
        </div>

        <div class="box">
            <strong>VENTA A CUENTA DE TERCEROS</strong><br>
            NIT: ____________________ Nombre, denominación o razón social: ____________________
        </div>

        <div class="box">
            <strong>DOCUMENTOS RELACIONADOS</strong><br>
            Tipo de Documento: _______________ Nº de documento: _______________ Fecha del documento: _______________
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Descripción</th>
                    <th>Precio Unitario</th>
                    <th>Descuento por Ítem</th>
                    <th>Ventas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($json['cuerpoDocumento'] as $item)
                    <tr>
                        <td>{{ $item['numItem'] }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>{{ $item['uniMedida'] }}</td>
                        <td>{{ $item['descripcion'] }}</td>
                        <td>{{ number_format($item['precioUni'], 2) }}</td>
                        <td>{{ number_format($item['montoDescu'], 2) }}</td>
                        <td>{{ number_format($item['compra'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex">
            <div style="width: 45%;"></div>
            <div style="width: 53%;">
                <table class="summary-table">
                    <tr>
                        <td colspan="2"><strong>SUMA DE VENTAS</strong></td>
                    </tr>
                    <tr>
                        <td>Sumatoria de ventas:</td>
                        <td>${{ number_format($json['resumen']['totalCompra'], 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sub-Total:</strong></td>
                        <td>${{ number_format($json['resumen']['subTotal'], 2) }}</td>
                    </tr>
                    @if ($json['resumen']['reteRenta'] > 0)
                        <tr>
                            <td><strong>Retención Renta:</strong></td>
                            <td>${{ number_format($json['resumen']['reteRenta'], 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>Total a Pagar:</strong></td>
                        <td>${{ number_format($json['resumen']['totalPagar'], 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box">
            Valor en Letras: {{ $json['resumen']['totalLetras'] }}<br>
            Condición de la Operación: {{ $json['resumen']['condicionOperacion'] }}<br>
            Observaciones: {{ $json['resumen']['observaciones'] ?? '-' }}
        </div>

        <div class="signature-section box">
            <div>
                Responsable por parte del emisor: ____________________<br>
                Nº de Documento: ___________
            </div>
            <div>
                Responsable por parte del receptor: __________________<br>
                Nº de Documento: ___________
            </div>
        </div>

        <div class="footer">Página 1 de 1</div>
    </div>

</body>

</html>
