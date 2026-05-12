<?php

namespace Database\Factories;

use App\Models\Comentario;
use App\Models\User;
use App\Models\Receta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComentarioFactory extends Factory
{
    protected $model = Comentario::class;

    public function definition(): array
    {
        return [
            'usuario_id' => User::factory(),
            'receta_id'  => Receta::factory(),
            'contenido'  => fake()->sentence(),
        ];
    }
}
