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
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo');
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->integer('stock');
            $table->integer('stock_minimo');
            $table->string('imagen')->nullable();
            $table->enum('estado', ['activo', 'deshabilitado'])->default('activo');
            $table->integer('categoria_id')->unsigned()->nullable();
            $table->integer('unidad_medida_id')->unsigned()->nullable();
            $table->integer('item_id')->unsigned()->nullable();
            $table->timestamps();


            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medidas');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
