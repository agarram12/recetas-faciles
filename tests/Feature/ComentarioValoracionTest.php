<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Receta;
use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ComentarioValoracionTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;
    private Receta $receta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $this->receta = Receta::factory()->create([
            'usuario_id'   => User::factory()->create()->id,
            'categoria_id' => $categoria->id,
        ]);
    }

    /** @test */
    public function usuario_autenticado_puede_comentar_receta(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('comentario.store', $this->receta->id), [
                'contenido' => 'Esta receta está buenísima',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comentarios', [
            'receta_id'  => $this->receta->id,
            'usuario_id' => $this->usuario->id,
            'contenido'  => 'Esta receta está buenísima',
        ]);
    }

    /** @test */
    public function comentario_ajax_devuelve_json(): void
    {
        $response = $this->actingAs($this->usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('comentario.store', $this->receta->id), [
                'contenido' => 'Comentario AJAX',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'   => true,
            'contenido' => 'Comentario AJAX',
            'autor'     => $this->usuario->name,
        ]);
    }

    /** @test */
    public function no_puede_comentar_sin_autenticacion(): void
    {
        $response = $this->post(route('comentario.store', $this->receta->id), [
            'contenido' => 'Comentario anónimo',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comentarios', [
            'contenido' => 'Comentario anónimo',
        ]);
    }

    /** @test */
    public function no_puede_comentar_sin_contenido(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('comentario.store', $this->receta->id), [
                'contenido' => '',
            ]);

        $response->assertSessionHasErrors('contenido');
    }

    /** @test */
    public function no_puede_comentar_con_mas_de_500_caracteres(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('comentario.store', $this->receta->id), [
                'contenido' => str_repeat('a', 501),
            ]);

        $response->assertSessionHasErrors('contenido');
    }

    /** @test */
    public function usuario_autenticado_puede_valorar_receta(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 5,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('valoraciones', [
            'receta_id'  => $this->receta->id,
            'usuario_id' => $this->usuario->id,
            'puntuacion' => 5,
        ]);
    }

    /** @test */
    public function valoracion_ajax_devuelve_json_con_media(): void
    {
        $response = $this->actingAs($this->usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 4,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'    => true,
            'media'      => 4.0,
            'puntuacion' => 4,
        ]);
    }

    /** @test */
    public function valorar_actualiza_en_vez_de_duplicar(): void
    {
        // Primera valoración
        $this->actingAs($this->usuario)
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 3,
            ]);

        // Segunda valoración (actualiza)
        $this->actingAs($this->usuario)
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 5,
            ]);

        // Solo debería haber una valoración de este usuario
        $this->assertEquals(1, Valoracion::where([
            'receta_id'  => $this->receta->id,
            'usuario_id' => $this->usuario->id,
        ])->count());

        // Y debería ser la última (5)
        $this->assertEquals(5, Valoracion::where([
            'receta_id'  => $this->receta->id,
            'usuario_id' => $this->usuario->id,
        ])->first()->puntuacion);
    }

    /** @test */
    public function no_puede_valorar_sin_autenticacion(): void
    {
        $response = $this->post(route('receta.valorar', $this->receta->id), [
            'puntuacion' => 5,
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function no_puede_valorar_con_puntuacion_invalida(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 6,
            ]);

        $response->assertSessionHasErrors('puntuacion');
    }

    /** @test */
    public function no_puede_valorar_con_puntuacion_cero(): void
    {
        $response = $this->actingAs($this->usuario)
            ->post(route('receta.valorar', $this->receta->id), [
                'puntuacion' => 0,
            ]);

        $response->assertSessionHasErrors('puntuacion');
    }

    /** @test */
    public function usuario_puede_agregar_receta_a_favoritos(): void
    {
        $response = $this->actingAs($this->usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('receta.favorito', $this->receta->id));

        $response->assertStatus(200);
        $response->assertJson(['esFavorito' => true]);

        $this->assertDatabaseHas('favoritos', [
            'usuario_id' => $this->usuario->id,
            'receta_id'  => $this->receta->id,
        ]);
    }

    /** @test */
    public function usuario_puede_quitar_receta_de_favoritos(): void
    {
        // Añadir primero
        $this->usuario->recetasFavoritas()->attach($this->receta->id);

        // Quitar (toggle)
        $response = $this->actingAs($this->usuario)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('receta.favorito', $this->receta->id));

        $response->assertStatus(200);
        $response->assertJson(['esFavorito' => false]);

        $this->assertDatabaseMissing('favoritos', [
            'usuario_id' => $this->usuario->id,
            'receta_id'  => $this->receta->id,
        ]);
    }

    /** @test */
    public function no_puede_agregar_favorito_sin_autenticacion(): void
    {
        $response = $this->post(route('receta.favorito', $this->receta->id));

        $response->assertRedirect(route('login'));
    }
}
