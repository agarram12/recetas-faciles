<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // buscar el archivo SQL con la estructura y datos de prueba
        $ruta_script = database_path('recetas_db.sql');
        // obtener contenido del archivo SQL
        $sql = file_get_contents($ruta_script);
        // ejecución
        DB::unprepared($sql);
        // mensaje de éxito
        $this->command->info('BBDD cargada con éxito');
    }
}