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
        Schema::create('cajas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();

            
            $table->decimal('monto_inicial', 10, 2); 
            $table->decimal('total_efectivo', 10, 2)->default(0);
            $table->decimal('total_tarjeta', 10, 2)->default(0);
            $table->decimal('total_otros', 10, 2)->default(0);
            $table->decimal('total_declarado', 10, 2)->nullable(); 
            $table->decimal('diferencia', 10, 2)->nullable();

            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
