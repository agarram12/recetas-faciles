<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
        });

        Schema::create('receta_ingredientes', function (Blueprint $table) {
            $table->unsignedBigInteger('receta_id');
            $table->unsignedBigInteger('ingrediente_id');
            $table->string('cantidad', 50)->nullable();

            $table->primary(['receta_id', 'ingrediente_id']);
            $table->foreign('receta_id')->references('id')->on('recetas')->onDelete('cascade');
            $table->foreign('ingrediente_id')->references('id')->on('ingredientes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_ingredientes');
        Schema::dropIfExists('ingredientes');
    }
};
