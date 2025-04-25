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
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('nit')->nullable();
            $table->string('nrc')->nullable();
            $table->string('giro')->nullable();
            $table->string('direccion');
            $table->string('departamento');
            $table->string('municipio');
            $table->string('telefono')->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('tipo_contribuyente');
            $table->string('codigo_actividad')->nullable();
            $table->enum('tipo_persona', ['natural', 'juridica']);
            $table->boolean('es_extranjero')->default(false);
            $table->string('pais')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
