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
        Schema::create('parametros_fiscales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('empresa_id')->unsigned();
            $table->string('nit');
            $table->string('nrc')->nullable();
            $table->string('codigo_establecimiento');
            $table->string('codigo_punto_venta');
            $table->string('codigo_sucursal');
            $table->string('tipo_entorno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_fiscales');
    }
};
