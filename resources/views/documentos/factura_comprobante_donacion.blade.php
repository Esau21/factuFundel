<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>COMPROBANTE DE DONACIÓN</title>
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
            <div class="bold">COMPROBANTE DE DONACIÓN</div>
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
                Nombre o razón social: {{ $json['donatario']['nombre'] }}<br>
                NIT: {{ $json['donatario']['numDocumento'] }}<br>
                NRC: {{ $json['donatario']['nrc'] }}<br>
                Actividad económica: {{ $json['donatario']['descActividad'] }}<br>
                Dirección: {{ $json['donatario']['direccion']['complemento'] }}<br>
                Número de teléfono: {{ $json['donatario']['telefono'] }}<br>
                Correo electrónico: {{ $json['donatario']['correo'] }}<br>
                Nombre comercial: {{ $json['donatario']['nombreComercial'] }}<br>
                Tipo de establecimiento: {{ $json['donatario']['tipoEstablecimiento'] }}
            </div>
            <div class="box" style="width: 49%;">
                <strong>RECEPTOR</strong><br>
                Nombre o razón social: {{ $json['donante']['nombre'] }}<br>
                NRC: {{ $json['donante']['nrc'] }}<br>
                Actividad económica: {{ $json['donante']['descActividad'] }}<br>
                Dirección: {{ $json['donante']['direccion']['complemento'] }}<br>
                Correo electrónico: {{ $json['donante']['correo'] }}<br>
            </div>
        </div>

        <div class="box">
            <strong>OTROS DOCUMENTOS ASOCIADOS</strong><br>
            Identificación del documento: {{ $json['otrosDocumentos'][0]['codDocAsociado'] ?? '' }} Descripción:
            {{ $json['otrosDocumentos'][0]['descDocumento'] ?? '' }}
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
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Depreciación</th>
                    <th>Valor Unitario</th>
                    <th>Valor Donado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($json['cuerpoDocumento'] as $item)
                    <tr>
                        <td>{{ $item['numItem'] }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>{{ $item['descripcion'] }}</td>
                        <td>{{ $item['uniMedida'] }}</td>
                        <td>${{ number_format($item['depreciacion'], 2) }}</td>
                        <td>${{ number_format($item['valorUni'], 2) }}</td>
                        <td>${{ number_format($item['valor'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex">
            <div style="width: 45%;"></div>
            <div style="width: 53%;">
                <table class="summary-table">
                    <tr>
                        <td><strong>Total de la Donación:</strong></td>
                        <td>${{ number_format($json['resumen']['valorTotal'], 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="box">
            Valor en Letras: {{ $json['resumen']['totalLetras'] }}<br>
            Observaciones: {{ $json['extension']['observaciones'] ?? '-' }}
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
