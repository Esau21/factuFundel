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
        Schema::create('abonos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cuenta_por_cobrar_id')->unsigned();
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_abono');
            $table->string('metodo_pago')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

        
            $table->foreign('cuenta_por_cobrar_id')->references('id')->on('cuentas_por_cobrar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
    }
};
