<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Receta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests dinámicos para el feed principal, filtros y búsqueda.
 * Los datos se crean dinámicamente con factories y se prueban
 * los filtros por categoría, dificultad, tiempo y búsqueda por texto.
 *
 * RF-108: Tests cubren flujos principales del sistema
 */
class RedSocialFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function el_feed_principal_carga_correctamente(): void
    {
        // Crear recetas dinámicas con factories
        $categoria = Categoria::factory()->create(['nombre' => 'Veganos']);
        $receta1 = Receta::factory()->create([
            'titulo'       => 'Ensalada César Vegana',
            'categoria_id' => $categoria->id,
        ]);
        $receta2 = Receta::factory()->create([
            'titulo'       => 'Hummus de Garbanzos',
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ensalada César Vegana');
        $response->assertSee('Hummus de Garbanzos');
    }

    /** @test */
    public function se_puede_filtrar_por_categoria(): void
    {
        // Crear dos categorías diferentes
        $catVegana = Categoria::factory()->create(['nombre' => 'Veganos']);
        $catCarne = Categoria::factory()->create(['nombre' => 'Carnívoros']);

        Receta::factory()->create([
            'titulo'       => 'Tofu Salteado',
            'categoria_id' => $catVegana->id,
        ]);
        Receta::factory()->create([
            'titulo'       => 'Chuletón a la Brasa',
            'categoria_id' => $catCarne->id,
        ]);

        // Filtrar solo por Veganos (AJAX)
        $response = $this->get(
            '/?categoria=' . $catVegana->id,
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('Tofu Salteado');
        $response->assertDontSee('Chuletón a la Brasa');
    }

    /** @test */
    public function se_puede_filtrar_por_dificultad(): void
    {
        $categoria = Categoria::factory()->create();

        Receta::factory()->create([
            'titulo'       => 'Ensalada Rápida',
            'dificultad'   => 'Fácil',
            'categoria_id' => $categoria->id,
        ]);
        Receta::factory()->create([
            'titulo'       => 'Wellington de Solomillo',
            'dificultad'   => 'Difícil',
            'categoria_id' => $categoria->id,
        ]);

        // Filtrar por Difícil (AJAX)
        $response = $this->get(
            '/?dificultad=Difícil',
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('Wellington');
        $response->assertDontSee('Ensalada Rápida');
    }

    /** @test */
    public function se_puede_filtrar_por_tiempo_rapido(): void
    {
        $categoria = Categoria::factory()->create();

        Receta::factory()->create([
            'titulo'         => 'Tostada de Aguacate',
            'tiempo_coccion' => 5,
            'categoria_id'   => $categoria->id,
        ]);
        Receta::factory()->create([
            'titulo'         => 'Estofado Largo',
            'tiempo_coccion' => 120,
            'categoria_id'   => $categoria->id,
        ]);

        // Filtrar por rápido (<= 15 min) via AJAX
        $response = $this->get(
            '/?tiempo=rapido',
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('Tostada de Aguacate');
        $response->assertDontSee('Estofado Largo');
    }

    /** @test */
    public function la_busqueda_por_texto_funciona(): void
    {
        $categoria = Categoria::factory()->create();

        Receta::factory()->create([
            'titulo'       => 'Arroz con Pollo',
            'categoria_id' => $categoria->id,
        ]);
        Receta::factory()->create([
            'titulo'       => 'Brownie de Chocolate',
            'categoria_id' => $categoria->id,
        ]);

        // Buscar "Pollo" (AJAX)
        $response = $this->get(
            '/?buscar=Pollo',
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('Arroz con Pollo');
        $response->assertDontSee('Brownie de Chocolate');
    }

    /** @test */
    public function la_paginacion_ajax_devuelve_json_con_estructura_correcta(): void
    {
        // Crear varias recetas para que haya paginación
        $categoria = Categoria::factory()->create();
        Receta::factory()->count(10)->create(['categoria_id' => $categoria->id]);

        // Petición AJAX al feed
        $response = $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'hasMore', 'nextPage', 'total']);
    }

    /** @test */
    public function la_busqueda_tambien_encuentra_por_autor(): void
    {
        $usuario = User::factory()->create(['name' => 'Chef Ramírez']);
        $categoria = Categoria::factory()->create();

        Receta::factory()->create([
            'titulo'       => 'Plato del Chef',
            'usuario_id'   => $usuario->id,
            'categoria_id' => $categoria->id,
        ]);

        // Buscar por nombre de autor (AJAX)
        $response = $this->get(
            '/?buscar=Ramírez',
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('Plato del Chef');
    }
}
