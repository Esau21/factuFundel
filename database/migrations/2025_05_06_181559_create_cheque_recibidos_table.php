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
        Schema::create('cheque_recibidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cliente_id')->unsigned();
            $table->integer('cuenta_bancaria_id')->unsigned(); 
            $table->string('numero_cheque');
            $table->string('banco_emisor'); 
            $table->decimal('monto', 12, 2);
            $table->date('fecha_emision');
            $table->date('fecha_pago')->nullable(); 
            $table->enum('estado', ['pendiente', 'cobrado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->string('correlativo')->unique()->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('cuenta_bancaria_id')->references('id')->on('cuentas_bancarias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_recibidos');
    }
};
