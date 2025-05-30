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
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('banco_id')->unsigned();
            $table->string('numero_cuenta');
            $table->enum('tipo_cuenta', ['ahorro', 'corriente', 'credito'])->default('corriente');
            $table->string('titular', 255);
            $table->string('moneda')->default('USD');
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('banco_id')->references('id')->on('bancos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_bancarias');
    }
};
