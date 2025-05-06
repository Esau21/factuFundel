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
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cliente_id')->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('fecha_venta');
            $table->decimal('total', 10,2);
            $table->enum('status', ['PAID', 'PENDING', 'CANCEL'])->default('PENDING');
            $table->string('tipo_pago');
            $table->text('observaciones');
            $table->timestamps();


            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
