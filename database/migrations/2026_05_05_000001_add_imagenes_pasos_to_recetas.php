<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade columna imagenes_pasos (JSON) a la tabla recetas
     * para almacenar las rutas de imágenes de cada paso.
     */
    public function up(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->json('imagenes_pasos')->nullable()->after('pasos');
        });
    }

    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropColumn('imagenes_pasos');
        });
    }
};
