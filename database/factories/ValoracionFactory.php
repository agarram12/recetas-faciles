<?php

namespace Database\Factories;

use App\Models\Valoracion;
use App\Models\User;
use App\Models\Receta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValoracionFactory extends Factory
{
    protected $model = Valoracion::class;

    public function definition(): array
    {
        return [
            'usuario_id' => User::factory(),
            'receta_id'  => Receta::factory(),
            'puntuacion' => fake()->numberBetween(1, 5),
        ];
    }
}
