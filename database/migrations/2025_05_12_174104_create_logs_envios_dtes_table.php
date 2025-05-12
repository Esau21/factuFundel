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
        Schema::create('logs_envios_dtes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('documento_dte_id')->unsigned();
            $table->timestamp('fecha_envio');
            $table->string('estado_envio');
            $table->text('mensaje_dgii')->nullable();
            $table->timestamps();

            $table->foreign('documento_dte_id')->references('id')->on('documentos_dtes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_envios_dtes');
    }
};
