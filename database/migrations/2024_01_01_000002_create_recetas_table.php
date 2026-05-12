<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->text('pasos')->nullable();
            $table->json('imagenes_pasos')->nullable();
            $table->string('url_imagen', 255)->default('assets/img/logo.png');
            $table->integer('tiempo_coccion');
            $table->string('dificultad', 20)->default('Media');

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
