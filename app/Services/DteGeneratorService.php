<?php

namespace App\Services;

use DOMDocument;

class DteGeneratorService
{
    public function generarFacturaElectronica(array $datos): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        // Nodo raíz
        $documento = $dom->createElement('dte:Documento');
        $documento->setAttribute('xmlns:dte', 'http://www.mh.gob.sv/dte/wsv');
        $documento->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $documento->setAttribute('xsi:schemaLocation', 'http://www.mh.gob.sv/dte/wsv DTE.xsd');
        $dom->appendChild($documento);

        // Identificación
        $identificacion = $dom->createElement('dte:Identificacion');
        $documento->appendChild($identificacion);

        $identificacion->appendChild($dom->createElement('dte:Version', '1'));
        $identificacion->appendChild($dom->createElement('dte:TipoDte', $datos['tipo_dte']));
        $identificacion->appendChild($dom->createElement('dte:NumeroControl', 'DTE-001-' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT)));
        $identificacion->appendChild($dom->createElement('dte:CodigoGeneracion', strtoupper(bin2hex(random_bytes(16)))));
        $identificacion->appendChild($dom->createElement('dte:FechaEmision', now()->format('Y-m-d\TH:i:s')));
        $identificacion->appendChild($dom->createElement('dte:TipoModelo', '1'));
        $identificacion->appendChild($dom->createElement('dte:TipoOperacion', '1'));

        // Emisor
        $emisorData = $datos['emisor'];
        $emisor = $dom->createElement('dte:Emisor');
        $documento->appendChild($emisor);

        $emisor->appendChild($dom->createElement('dte:Nit', $emisorData['nit']));
        $emisor->appendChild($dom->createElement('dte:Nrc', $emisorData['nrc']));
        $emisor->appendChild($dom->createElement('dte:Nombre', $emisorData['nombre']));
        $emisor->appendChild($dom->createElement('dte:NombreComercial', $emisorData['nombre_comercial']));
        $emisor->appendChild($dom->createElement('dte:Direccion', $emisorData['direccion']));
        $emisor->appendChild($dom->createElement('dte:Departamento', $emisorData['departamento']));
        $emisor->appendChild($dom->createElement('dte:Municipio', $emisorData['municipio']));

        // Receptor (cliente)
        $cliente = $datos['cliente'];
        $receptor = $dom->createElement('dte:Receptor');
        $documento->appendChild($receptor);

        $receptor->appendChild($dom->createElement('dte:Nombre', $cliente['nombre']));
        $receptor->appendChild($dom->createElement('dte:TipoDocumento', $cliente['tipo_documento']));
        $receptor->appendChild($dom->createElement('dte:NumeroDocumento', $cliente['numero_documento']));
        $receptor->appendChild($dom->createElement('dte:Direccion', $cliente['direccion']));
        $receptor->appendChild($dom->createElement('dte:Departamento', $cliente['departamento']));
        $receptor->appendChild($dom->createElement('dte:Municipio', $cliente['municipio']));

        // Cuerpo del documento (items)
        $cuerpoDocumento = $dom->createElement('dte:CuerpoDocumento');
        $documento->appendChild($cuerpoDocumento);

        foreach ($datos['items'] as $itemData) {
            $item = $dom->createElement('dte:Item');
            $item->appendChild($dom->createElement('dte:NumeroLinea', $itemData['numero_linea']));
            $item->appendChild($dom->createElement('dte:Codigo', $itemData['codigo']));
            $item->appendChild($dom->createElement('dte:Descripcion', $itemData['descripcion']));
            $item->appendChild($dom->createElement('dte:Cantidad', number_format($itemData['cantidad'], 2, '.', '')));
            $item->appendChild($dom->createElement('dte:PrecioUnitario', number_format($itemData['precio_unitario'], 2, '.', '')));
            $item->appendChild($dom->createElement('dte:PrecioVenta', number_format($itemData['precio_venta'], 2, '.', '')));
            $item->appendChild($dom->createElement('dte:Subtotal', number_format($itemData['subtotal'], 2, '.', '')));
            $item->appendChild($dom->createElement('dte:Total', number_format($itemData['total'], 2, '.', '')));
            $cuerpoDocumento->appendChild($item);
        }

        // Resumen
        $resumenData = $datos['resumen'];
        $resumen = $dom->createElement('dte:Resumen');
        $documento->appendChild($resumen);

        $resumen->appendChild($dom->createElement('dte:TotalGravada', number_format($resumenData['total_gravada'], 2, '.', '')));
        $resumen->appendChild($dom->createElement('dte:TotalIva', number_format($resumenData['total_iva'], 2, '.', '')));
        $resumen->appendChild($dom->createElement('dte:SubTotalVentas', number_format($resumenData['subtotal'], 2, '.', '')));
        $resumen->appendChild($dom->createElement('dte:TotalVentas', number_format($resumenData['total'], 2, '.', '')));

        // Guardar el XML
        $nombreArchivo = 'factura_' . now()->format('Ymd_His') . '.xml';
        $rutaXml = storage_path('app/facturas/' . $nombreArchivo);
        $dom->save($rutaXml);

        return $rutaXml;
    }
}
