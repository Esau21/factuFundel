<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class EnviarDTECliente extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;
    public $pdfContent;

    public function __construct($venta, string $pdfContent)
    {
        $this->venta = $venta;
        $this->pdfContent = $pdfContent;
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Documento Electrónico - Venta #' . $this->venta->id,
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
            \Illuminate\Mail\Mailables\Attachment::fromData(function () {
                return $this->pdfContent;
            }, "DTE_Venta_{$this->venta->id}.pdf", [
                'mime' => 'application/pdf',
            ]),
        ];
    }
}
