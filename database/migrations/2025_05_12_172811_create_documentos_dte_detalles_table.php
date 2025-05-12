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
        Schema::create('documentos_dte_detalles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('documento_dte_id')->unsigned();
            $table->integer('producto_id')->unsigned();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('descuento', 10, 2)->nullable();
            $table->decimal('iva', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('documento_dte_id')->references('id')->on('documentos_dtes');
            $table->foreign('producto_id')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_dte_detalles');
    }
};
