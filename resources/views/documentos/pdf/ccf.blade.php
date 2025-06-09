<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Crédito Fiscal</title>
    <style>
        /* Reset y caja */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .page {
            width: 18cm;
            /* A4 ancho real útil */
            min-height: 27cm;
            /* A4 altura útil */
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
            margin-bottom: 4px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .flex {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 6px;
            font-size: 9pt;
            word-wrap: break-word;
        }

        /* Contenedores del flex, para evitar desbordes */
        .box.w49 {
            width: 49%;
            min-width: 49%;
        }

        .qr {
            text-align: center;
        }

        /* Imagen QR tamaño fijo */
        img.qr-img {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        table.table,
        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 9pt;
        }

        table.table th,
        table.table td,
        table.summary-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            word-break: break-word;
        }

        table.summary-table td {
            text-align: left;
        }

        /* Para que descripción no se desborde y tenga algo más de espacio */
        table.table td:nth-child(4) {
            text-align: left;
            max-width: 8cm;
            white-space: normal;
        }

        .footer {
            text-align: right;
            margin-top: 12px;
            font-size: 9pt;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            font-size: 9pt;
            gap: 20px;
        }

        .signature-section>div {
            width: 48%;
            border-top: 1px solid #000;
            padding-top: 4px;
            text-align: center;
        }

        /* Pequeño ajuste responsive para flex */
        @media print {
            .flex {
                flex-wrap: nowrap;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="text-center header">
            <div class="bold" style="font-size: 12pt;">DOCUMENTO TRIBUTARIO ELECTRÓNICO</div>
            <div class="bold" style="font-size: 11pt;">COMPROBANTE DE CRÉDITO FISCAL</div>
        </div>

        <div class="text-right" style="font-size: 9pt;">Ver.3</div>

        <div class="flex header" style="font-size: 9pt;">
            <div style="width: 38%;">
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
            <div class="qr" style="width: 20%;">
                <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}"
                    alt="QR">
            </div>
            <div style="width: 38%; text-align: right;">
                <div><strong>Modelo de Facturación:</strong> {{ $json['identificacion']['tipoModelo'] }}</div>
                <div><strong>Tipo de Transmisión:</strong> {{ $json['identificacion']['tipoOperacion'] }}</div>
                <div><strong>Fecha y Hora de Generación:</strong> {{ $json['identificacion']['fecEmi'] }}
                    {{ $json['identificacion']['horEmi'] }}</div>
            </div>
        </div>

        <div class="flex" style="font-size: 9pt;">
            <div class="box w49">
                <strong>EMISOR</strong><br>
                Nombre o razón social: {{ $json['emisor']['nombre'] }}<br>
                NIT: {{ $json['emisor']['nit'] }}<br>
                NRC: {{ $json['emisor']['nrc'] }}<br>
                Actividad económica: {{ $json['emisor']['descActividad'] }}<br>
                Dirección: {{ $json['emisor']['direccion']['complemento'] }}<br>
                Número de teléfono: {{ $json['emisor']['telefono'] }}<br>
                Correo electrónico: {{ $json['emisor']['correo'] }}<br>
                Nombre comercial: {{ $json['emisor']['nombreComercial'] }}<br>
                Tipo de establecimiento: {{ $json['emisor']['tipoEstablecimiento'] }}
            </div>
            <div class="box w49">
                <strong>RECEPTOR</strong><br>
                Nombre o razón social: {{ $json['receptor']['nombre'] }}<br>
                NIT: {{ $json['receptor']['nit'] }}<br>
                NRC: {{ $json['receptor']['nrc'] }}<br>
                Actividad económica: {{ $json['receptor']['descActividad'] }}<br>
                Dirección: {{ $json['receptor']['direccion']['complemento'] }}<br>
                Correo electrónico: {{ $json['receptor']['correo'] }}<br>
                Nombre comercial: {{ $json['receptor']['nombreComercial'] }}
            </div>
        </div>

        <div class="box" style="font-size: 9pt;">
            <strong>OTROS DOCUMENTOS ASOCIADOS</strong><br>
            Identificación del documento: ____________________ Descripción: ____________________
        </div>

        <div class="box" style="font-size: 9pt;">
            <strong>VENTA A CUENTA DE TERCEROS</strong><br>
            NIT: ____________________ Nombre, denominación o razón social: ____________________
        </div>

        <div class="box" style="font-size: 9pt;">
            <strong>DOCUMENTOS RELACIONADOS</strong><br>
            Tipo de Documento: _______________ Nº de documento: _______________ Fecha del documento: _______________
        </div>

        <table class="table" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Descripción</th>
                    <th>Precio Unitario</th>
                    <th>Descuento por Ítem</th>
                    <th>Otros montos no afectos</th>
                    <th>Ventas No Sujetas</th>
                    <th>Ventas Exentas</th>
                    <th>Ventas Gravadas</th>
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
                        <td>{{ number_format($item['noGravado'], 2) }}</td>
                        <td>{{ number_format($item['ventaNoSuj'], 2) }}</td>
                        <td>{{ number_format($item['ventaExenta'], 2) }}</td>
                        <td>{{ number_format($item['ventaGravada'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex" style="margin-top: 12px;">
            <div style="width: 45%;"></div>
            <div style="width: 53%;">
                <table class="summary-table" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td colspan="2"><strong>SUMA DE VENTAS</strong></td>
                        </tr>
                        <tr>
                            <td>Suma Total de Operaciones:</td>
                            <td>${{ number_format($json['resumen']['subTotalVentas'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Monto global Desc., Rebajas y otras ventas no sujetas:</td>
                            <td>${{ number_format($json['resumen']['descuNoSuj'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Monto global Desc., Rebajas y otras ventas exentas:</td>
                            <td>${{ number_format($json['resumen']['descuExenta'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Monto global Desc., Rebajas y otras ventas gravadas:</td>
                            <td>${{ number_format($json['resumen']['descuGravada'], 2) }}</td>
                        </tr>
                        @foreach ($json['resumen']['tributos'] as $tributo)
                            <tr>
                                <td>{{ $tributo['descripcion'] }}</td>
                                <td>${{ number_format($tributo['valor'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>Sub-Total:</strong></td>
                            <td>${{ number_format($json['resumen']['subTotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>IVA Percibido:</td>
                            <td>${{ number_format($json['resumen']['ivaPerci1'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>IVA Retenido:</td>
                            <td>${{ number_format($json['resumen']['ivaRete1'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Retención Renta:</td>
                            <td>${{ number_format($json['resumen']['reteRenta'], 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Monto Total de la Operación:</strong></td>
                            <td>${{ number_format($json['resumen']['montoTotalOperacion'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Otros montos no afectos:</td>
                            <td>${{ number_format($json['resumen']['totalNoGravado'], 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total a Pagar:</strong></td>
                            <td>${{ number_format($json['resumen']['totalPagar'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box" style="font-size: 9pt;">
            Valor en Letras: {{ $json['resumen']['totalLetras'] }}<br>
            Condición de la Operación: {{ $json['resumen']['condicionOperacion'] }}<br>
            Observaciones: {{ $json['extension']['observaciones'] ?? '-' }}
        </div>

        <div class="signature-section">
            <div>
                Responsable por parte del emisor:<br> ____________________<br>
                Nº de Documento: ___________
            </div>
            <div>
                Responsable por parte del receptor:<br> __________________<br>
                Nº de Documento: ___________
            </div>
        </div>
    </div>
</body>

</html>
