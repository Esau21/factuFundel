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
        Schema::create('certificados_digitales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('empresa_id')->unsigned();
            $table->text('certificado'); 
            $table->string('clave');
            $table->timestamp('fecha_expiracion');
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados_digitales');
    }
};
