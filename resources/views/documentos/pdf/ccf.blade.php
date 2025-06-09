<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Crédito Fiscal</title>
    <style>
        /* Contenedor principal */
        .page {
            width: 800px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        h1, h2 {
            margin: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .mb-2 {
            margin-bottom: 12px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            box-sizing: border-box;
            page-break-inside: avoid; /* Evitar corte dentro del bloque */
        }

        .col-5 {
            width: 41.66%;
            box-sizing: border-box;
        }

        .col-2 {
            width: 16.66%;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .col-6 {
            width: 49%;
            box-sizing: border-box;
        }

        .border-box {
            border: 1px solid #000;
            padding: 8px;
            font-size: 9pt;
            margin-bottom: 12px;
            page-break-inside: avoid; /* Evitar corte dentro del bloque */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-end td {
            text-align: right;
        }

        .qr-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        /* Firmas lado a lado */
        .firma-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .firma {
            width: 45%;
            border-top: 1px solid #000;
            padding-top: 10px;
            text-align: center;
            font-size: 9pt;
        }

        /* Evitar que elementos se dividan en salto de página */
        tr, tbody, thead {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="text-center mb-2">
            <h1>DOCUMENTO TRIBUTARIO ELECTRÓNICO</h1>
            <h2>COMPROBANTE DE CRÉDITO FISCAL</h2>
        </div>

        <div class="text-end mb-2" style="font-size: 9pt;">Ver.3</div>

        <div class="row">
            <div class="col-5">
                <div><strong>Código de Generación:</strong> {{ $json['identificacion']['codigoGeneracion'] }}</div>
                <div><strong>Número de Control:</strong> {{ $json['identificacion']['numeroControl'] }}</div>
                <div><strong>Sello de recepción:</strong> {{ $mh['selloRecibido'] ?? '—' }}</div>
            </div>

            <div class="col-2">
                @php
                    $qrUrl =
                        'https://admin.factura.gob.sv/consultaPublica?ambiente=' .
                        $json['identificacion']['ambiente'] .
                        '&codGen=' .
                        $json['identificacion']['codigoGeneracion'] .
                        '&fechaEmi=' .
                        $json['identificacion']['fecEmi'];
                @endphp
                <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}"
                    alt="QR">
            </div>

            <div class="col-5 text-end">
                <div><strong>Modelo de Facturación:</strong> {{ $json['identificacion']['tipoModelo'] }}</div>
                <div><strong>Tipo de Transmisión:</strong> {{ $json['identificacion']['tipoOperacion'] }}</div>
                <div><strong>Fecha y Hora de Generación:</strong> {{ $json['identificacion']['fecEmi'] }}
                    {{ $json['identificacion']['horEmi'] }}</div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-6 border-box">
                <strong>EMISOR</strong><br>
                Nombre: {{ $json['emisor']['nombre'] }}<br>
                NIT: {{ $json['emisor']['nit'] }}<br>
                NRC: {{ $json['emisor']['nrc'] }}<br>
                Actividad: {{ $json['emisor']['descActividad'] }}<br>
                Dirección: {{ $json['emisor']['direccion']['complemento'] }}<br>
                Teléfono: {{ $json['emisor']['telefono'] }}<br>
                Correo: {{ $json['emisor']['correo'] }}<br>
                Comercial: {{ $json['emisor']['nombreComercial'] }}<br>
                Establecimiento: {{ $json['emisor']['tipoEstablecimiento'] }}
            </div>
            <div class="col-6 border-box">
                <strong>RECEPTOR</strong><br>
                Nombre: {{ $json['receptor']['nombre'] }}<br>
                NIT: {{ $json['receptor']['nit'] }}<br>
                NRC: {{ $json['receptor']['nrc'] }}<br>
                Actividad: {{ $json['receptor']['descActividad'] }}<br>
                Dirección: {{ $json['receptor']['direccion']['complemento'] }}<br>
                Correo: {{ $json['receptor']['correo'] }}<br>
                Comercial: {{ $json['receptor']['nombreComercial'] }}
            </div>
        </div>

        <div class="border-box">OTROS DOCUMENTOS ASOCIADOS:<br>Identificación: ___________ Descripción: ___________
        </div>
        <div class="border-box">VENTA A CUENTA DE TERCEROS:<br>NIT: ___________ Nombre: ___________</div>
        <div class="border-box">DOCUMENTOS RELACIONADOS:<br>Tipo: __________ Nº: __________ Fecha: __________</div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cant</th>
                    <th>Unidad</th>
                    <th>Descripción</th>
                    <th>Precio Unit.</th>
                    <th>Desc.</th>
                    <th>No Afecto</th>
                    <th>No Suj.</th>
                    <th>Exento</th>
                    <th>Gravado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($json['cuerpoDocumento'] as $item)
                    <tr>
                        <td>{{ $item['numItem'] }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>{{ $item['uniMedida'] }}</td>
                        <td style="text-align: left;">{{ $item['descripcion'] }}</td>
                        <td>{{ number_format($item['precioUni'], 2) }}</td>
                        <td>{{ number_format($item['montoDescu'], 2) }}</td>
                        <td>{{ number_format($item['noGravado'], 2) }}</td>
                        <td>{{ number_format($item['ventaNoSuj'], 2) }}</td>
                        <td>{{ number_format($item['ventaExenta'], 2) }}</td>
                        <td>{{ number_format($item['ventaGravada'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row">
            <div class="col-6"></div>
            <div class="col-6">
                <table>
                    <tbody>
                        <tr>
                            <td><strong>Suma Total:</strong></td>
                            <td class="text-end">${{ number_format($json['resumen']['subTotalVentas'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Desc. No Suj:</td>
                            <td class="text-end">${{ number_format($json['resumen']['descuNoSuj'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Desc. Exenta:</td>
                            <td class="text-end">${{ number_format($json['resumen']['descuExenta'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Desc. Gravada:</td>
                            <td class="text-end">${{ number_format($json['resumen']['descuGravada'], 2) }}</td>
                        </tr>
                        @foreach ($json['resumen']['tributos'] as $tributo)
                            <tr>
                                <td>{{ $tributo['descripcion'] }}</td>
                                <td class="text-end">${{ number_format($tributo['valor'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>Sub-Total:</strong></td>
                            <td class="text-end">${{ number_format($json['resumen']['subTotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>IVA Percibido:</td>
                            <td class="text-end">${{ number_format($json['resumen']['ivaPerci1'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>IVA Retenido:</td>
                            <td class="text-end">${{ number_format($json['resumen']['ivaRete1'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Renta Retenida:</td>
                            <td class="text-end">${{ number_format($json['resumen']['reteRenta'], 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Operación:</strong></td>
                            <td class="text-end">${{ number_format($json['resumen']['montoTotalOperacion'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total No Afecto:</td>
                            <td class="text-end">${{ number_format($json['resumen']['totalNoGravado'], 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total a Pagar:</strong></td>
                            <td class="text-end">${{ number_format($json['resumen']['totalPagar'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border-box" style="margin-top: 20px;">
            Valor en Letras: {{ $json['resumen']['totalLetras'] }}<br>
            Condición de la Operación: {{ $json['resumen']['condicionOperacion'] }}<br>
            Observaciones: {{ $json['extension']['observaciones'] ?? '-' }}
        </div>

        <div class="firma-container">
            <div class="firma">
                Responsable por el Emisor<br>____________________<br>Nº Documento: ___________
            </div>
            <div class="firma">
                Responsable por el Receptor<br>____________________<br>Nº Documento: ___________
            </div>
        </div>
    </div>
</body>

</html>
