<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos_dtes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo_documento');
            $table->string('numero_control')->unique();
            $table->string('codigo_generacion')->unique();
            $table->date('fecha_emision');
            $table->integer('cliente_id')->unsigned();
            $table->integer('empresa_id')->unsigned();
            $table->enum('estado', ['pendiente', 'firmado', 'enviado', 'rechazado'])->default('pendiente');
            $table->text('xml_firmado')->nullable();
            $table->text('xml_respuesta_dgii')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('empresa_id')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_dtes');
    }
};
