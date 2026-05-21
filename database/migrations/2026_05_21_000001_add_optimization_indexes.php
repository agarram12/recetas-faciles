<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RF-110: Índices en campos usados en búsquedas y relaciones.
 *
 * Añade índices explícitos a las columnas que se usan frecuentemente
 * en filtros, ordenación, JOINs y búsquedas del feed social.
 */
return new class extends Migration
{
    public function up(): void
    {
        // recetas: filtros de búsqueda y ordenación
        Schema::table('recetas', function (Blueprint $table) {
            $table->index('dificultad', 'idx_recetas_dificultad');
            $table->index('tiempo_coccion', 'idx_recetas_tiempo_coccion');
        });

        // comentarios: listado por receta
        Schema::table('comentarios', function (Blueprint $table) {
            $table->index('receta_id', 'idx_comentarios_receta_id');
        });

        // valoraciones: media por receta + unique para updateOrCreate
        Schema::table('valoraciones', function (Blueprint $table) {
            $table->index('receta_id', 'idx_valoraciones_receta_id');
            $table->unique(['usuario_id', 'receta_id'], 'uq_valoraciones_usuario_receta');
        });

        // favoritos: consulta inversa (recetas que son favoritas)
        Schema::table('favoritos', function (Blueprint $table) {
            $table->index('receta_id', 'idx_favoritos_receta_id');
        });

        // seguidores: buscar seguidores de un usuario
        Schema::table('seguidores', function (Blueprint $table) {
            $table->index('seguido_id', 'idx_seguidores_seguido_id');
        });
    }

    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropIndex('idx_recetas_dificultad');
            $table->dropIndex('idx_recetas_tiempo_coccion');
        });

        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropIndex('idx_comentarios_receta_id');
        });

        Schema::table('valoraciones', function (Blueprint $table) {
            $table->dropIndex('idx_valoraciones_receta_id');
            $table->dropUnique('uq_valoraciones_usuario_receta');
        });

        Schema::table('favoritos', function (Blueprint $table) {
            $table->dropIndex('idx_favoritos_receta_id');
        });

        Schema::table('seguidores', function (Blueprint $table) {
            $table->dropIndex('idx_seguidores_seguido_id');
        });
    }
};
