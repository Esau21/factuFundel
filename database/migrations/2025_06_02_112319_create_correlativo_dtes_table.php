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
        Schema::create('correlativo_dtes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo_dte');
            $table->string('codigo_establecimiento');
            $table->unsignedBigInteger('correlativo')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correlativo_dtes');
    }
};
