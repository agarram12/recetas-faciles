<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('receta_id');
            $table->timestamp('fecha_guardado')->useCurrent();

            $table->primary(['usuario_id', 'receta_id']);
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receta_id')->references('id')->on('recetas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
