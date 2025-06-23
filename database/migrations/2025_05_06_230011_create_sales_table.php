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
            $table->decimal('total', 10, 2);
            $table->decimal('cambio', 10, 2);
            $table->enum('status', ['PAID', 'PENDING', 'CANCEL'])->default('PENDING');
            $table->string('tipo_pago')->nullable();
            $table->string('tipo_venta', 20);
            $table->string('plazo')->nullable();
            $table->string('referencia')->nullable();
            $table->string('descDocumento')->nullable();
            $table->string('detalleDocumento');
            $table->string('periodo')->nullable();
            $table->decimal('iva', 10, 2)->default(0.00);
            $table->decimal('retencion', 10,2)->default(0.00);
            $table->text('observaciones');
            $table->decimal('monto_efectivo', 12, 2)->nullable();
            $table->decimal('monto_transferencia', 12, 2)->nullable();
            $table->integer('cuenta_bancaria_id')->unsigned()->nullable();
            $table->integer('cheque_bancario_id')->unsigned()->nullable();
            $table->integer('documento_dte_id')->unsigned();
            $table->timestamps();


            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cuenta_bancaria_id')->references('id')->on('cuentas_bancarias');
            $table->foreign('cheque_bancario_id')->references('id')->on('cheque_recibidos');
            $table->foreign('documento_dte_id')->references('id')->on('documentos_dtes');
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
