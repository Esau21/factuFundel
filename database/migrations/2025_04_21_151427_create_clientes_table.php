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
            $table->string('nombreComercial')->nullable();
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('nit')->nullable();
            $table->string('nrc')->nullable();
            $table->string('direccion');
            $table->integer('departamento_id')->unsigned();
            $table->integer('municipio_id')->unsigned();
            $table->integer('actividad_economica_id')->unsigned()->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo_electronico');
            $table->string('tipo_contribuyente');
            $table->enum('tipo_persona', ['natural', 'juridica']);
            $table->boolean('es_extranjero')->default(false);
            $table->string('pais')->nullable();
            $table->timestamps();

            $table->foreign('departamento_id')->references('id')->on('departamentos');
            $table->foreign('municipio_id')->references('id')->on('municipios');
            $table->foreign('actividad_economica_id')->references('id')->on('actividades_economicas');
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
