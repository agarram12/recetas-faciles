<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Receta;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class RecetaTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;
    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->create();
        $this->categoria = Categoria::factory()->create(['nombre' => 'Veganos']);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_formulario_crear_receta(): void
    {
        $response = $this->actingAs($this->usuario)
            ->get(route('receta.create'));

        $response->assertStatus(200);
        $response->assertSee('Publicar');
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_ver_formulario_crear(): void
    {
        $response = $this->get(route('receta.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function usuario_autenticado_puede_crear_receta_con_datos_validos(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->usuario)->post(route('receta.store'), [
            'titulo'         => 'Tortilla de Patatas',
            'descripcion'    => 'La receta clásica española',
            'categoria_id'   => $this->categoria->id,
            'url_imagen'     => UploadedFile::fake()->image('tortilla.jpg', 600, 400),
            'tiempo_coccion' => 40,
            'dificultad'     => 'Media',
            'pasos'          => ['Pelar las patatas', 'Freír a fuego lento', 'Mezclar con huevo'],
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('recetas', [
            'titulo'      => 'Tortilla de Patatas',
            'usuario_id'  => $this->usuario->id,
            'dificultad'  => 'Media',
        ]);
    }

    /** @test */
    public function no_puede_crear_receta_sin_titulo(): void
    {
        $response = $this->actingAs($this->usuario)->post(route('receta.store'), [
            'titulo'         => '',
            'descripcion'    => 'Descripción de prueba',
            'categoria_id'   => $this->categoria->id,
            'url_imagen'     => UploadedFile::fake()->image('test.jpg'),
            'tiempo_coccion' => 30,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Paso 1'],
        ]);

        $response->assertSessionHasErrors('titulo');
    }

    /** @test */
    public function no_puede_crear_receta_sin_imagen(): void
    {
        $response = $this->actingAs($this->usuario)->post(route('receta.store'), [
            'titulo'         => 'Receta sin imagen',
            'descripcion'    => 'Descripción',
            'categoria_id'   => $this->categoria->id,
            'tiempo_coccion' => 20,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Paso 1'],
        ]);

        $response->assertSessionHasErrors('url_imagen');
    }

    /** @test */
    public function no_puede_crear_receta_sin_pasos(): void
    {
        $response = $this->actingAs($this->usuario)->post(route('receta.store'), [
            'titulo'         => 'Receta sin pasos',
            'descripcion'    => 'Descripción',
            'categoria_id'   => $this->categoria->id,
            'url_imagen'     => UploadedFile::fake()->image('test.jpg'),
            'tiempo_coccion' => 20,
            'dificultad'     => 'Fácil',
            'pasos'          => [],
        ]);

        $response->assertSessionHasErrors('pasos');
    }

    /** @test */
    public function no_puede_crear_receta_con_categoria_inexistente(): void
    {
        $response = $this->actingAs($this->usuario)->post(route('receta.store'), [
            'titulo'         => 'Receta categoría inválida',
            'descripcion'    => 'Descripción',
            'categoria_id'   => 9999,
            'url_imagen'     => UploadedFile::fake()->image('test.jpg'),
            'tiempo_coccion' => 20,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Paso 1'],
        ]);

        $response->assertSessionHasErrors('categoria_id');
    }

    /** @test */
    public function no_puede_crear_receta_sin_autenticacion(): void
    {
        $response = $this->post(route('receta.store'), [
            'titulo'         => 'Receta anónima',
            'descripcion'    => 'Descripción',
            'categoria_id'   => $this->categoria->id,
            'url_imagen'     => UploadedFile::fake()->image('test.jpg'),
            'tiempo_coccion' => 20,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Paso 1'],
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function puede_ver_el_feed_principal(): void
    {
        Receta::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_ver_detalle_de_receta(): void
    {
        $receta = Receta::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->get(route('receta.show', $receta->id));

        $response->assertStatus(200);
        $response->assertSee($receta->titulo);
    }

    /** @test */
    public function feed_ajax_devuelve_json(): void
    {
        Receta::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->get('/', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'hasMore', 'nextPage', 'total']);
    }

    /** @test */
    public function busqueda_filtra_recetas_por_titulo(): void
    {
        Receta::factory()->create([
            'titulo'       => 'Gazpacho Andaluz',
            'categoria_id' => $this->categoria->id,
        ]);
        Receta::factory()->create([
            'titulo'       => 'Paella Valenciana',
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->get('/?buscar=Gazpacho', ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals(1, $json['total']);
    }
}
