<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarDTECliente extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;
    public $pdfContent;
    public $codigoGeneracion;
    public $jsonContent;

    public function __construct($venta, string $pdfContent, string $codigoGeneracion, string $jsonContent)
    {
        $this->venta = $venta;
        $this->pdfContent = $pdfContent;
        $this->codigoGeneracion = $codigoGeneracion;
        $this->jsonContent = $jsonContent;
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'NOTIFICACIÓN DE FACTURA ELECTRÓNICA FUNDEL',
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            markdown: 'emails.dte',
            with: [
                'venta' => $this->venta,
            ]
        );
    }

    public function attachments()
    {
        return [
            // PDF attachment
            \Illuminate\Mail\Mailables\Attachment::fromData(function () {
                return $this->pdfContent;
            }, "DTE_{$this->codigoGeneracion}.pdf", [
                'mime' => 'application/pdf',
            ]),

            // JSON attachment
            \Illuminate\Mail\Mailables\Attachment::fromData(function () {
                return $this->jsonContent;
            }, "{$this->codigoGeneracion}.json", [
                'mime' => 'application/json',
            ]),
        ];
    }
}
