<?php

namespace Database\Factories;

use App\Models\Receta;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecetaFactory extends Factory
{
    protected $model = Receta::class;

    public function definition(): array
    {
        return [
            'usuario_id'     => User::factory(),
            'categoria_id'   => Categoria::factory(),
            'titulo'         => fake()->sentence(3),
            'descripcion'    => fake()->paragraph(1),
            'pasos'          => 'Paso uno. Paso dos. Paso tres.',
            'url_imagen'     => 'assets/img/logo.png',
            'tiempo_coccion' => fake()->numberBetween(5, 120),
            'dificultad'     => fake()->randomElement(['Fácil', 'Media', 'Difícil']),
            'imagenes_pasos' => null,
        ];
    }
}
