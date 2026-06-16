<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Receta;
use App\Models\Valoracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests dinámicos para interacciones sociales: comentarios, valoraciones y favoritos.
 * Todos los datos se crean con factories y las acciones se ejecutan
 * contra las rutas reales de la aplicación.
 *
 * RF-104: Tests para comentarios y valoraciones
 * RF-107: Usa RefreshDatabase (BD de prueba)
 * RF-108: Cubre flujos principales del sistema
 */
class RedSocialInteraccionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function un_usuario_puede_comentar_una_receta(): void
    {
        // Crear datos dinámicos: un usuario comenta la receta de otro
        $autor = User::factory()->create();
        $comentador = User::factory()->create();
        $receta = Receta::factory()->create(['usuario_id' => $autor->id]);

        // Ejecutar la acción real: publicar un comentario
        $response = $this->actingAs($comentador)->post(route('comentario.store', $receta->id), [
            'contenido' => '¡Esta receta me ha quedado espectacular! La repetiré seguro.',
        ]);

        $response->assertRedirect();

        // Verificar que el comentario se guardó en la BD
        $this->assertDatabaseHas('comentarios', [
            'receta_id'  => $receta->id,
            'usuario_id' => $comentador->id,
            'contenido'  => '¡Esta receta me ha quedado espectacular! La repetiré seguro.',
        ]);
    }

    /** @test */
    public function no_se_puede_comentar_con_texto_vacio(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Intentar comentar con contenido vacío
        $response = $this->actingAs($usuario)->post(route('comentario.store', $receta->id), [
            'contenido' => '',
        ]);

        // La validación debe fallar
        $response->assertSessionHasErrors('contenido');

        // No debe haber ningún comentario en BD
        $this->assertDatabaseMissing('comentarios', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
        ]);
    }

    /** @test */
    public function un_usuario_puede_valorar_una_receta(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Ejecutar la valoración real
        $response = $this->actingAs($usuario)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 4,
        ]);

        $response->assertRedirect();

        // Verificar que la valoración se guardó
        $this->assertDatabaseHas('valoraciones', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
            'puntuacion' => 4,
        ]);
    }

    /** @test */
    public function la_valoracion_se_actualiza_si_el_usuario_vuelve_a_valorar(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Primera valoración: 3 estrellas
        $this->actingAs($usuario)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 3,
        ]);

        $this->assertDatabaseHas('valoraciones', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
            'puntuacion' => 3,
        ]);

        // Segunda valoración: cambia a 5 estrellas (updateOrCreate)
        $this->actingAs($usuario)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 5,
        ]);

        // Debe haber solo UNA valoración, no dos
        $this->assertEquals(
            1,
            Valoracion::where('receta_id', $receta->id)
                ->where('usuario_id', $usuario->id)
                ->count()
        );

        // Y debe tener la puntuación actualizada
        $this->assertDatabaseHas('valoraciones', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
            'puntuacion' => 5,
        ]);
    }

    /** @test */
    public function la_valoracion_no_acepta_puntuacion_fuera_de_rango(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Intentar valorar con 10 (máximo es 5)
        $response = $this->actingAs($usuario)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 10,
        ]);

        $response->assertSessionHasErrors('puntuacion');

        // No debe haberse creado ninguna valoración
        $this->assertDatabaseMissing('valoraciones', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
        ]);
    }

    /** @test */
    public function se_puede_hacer_toggle_de_favoritos_con_ajax(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Añadir a favoritos (primera vez)
        $response = $this->actingAs($usuario)->post(
            route('receta.favorito', $receta->id),
            [],
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertJson(['esFavorito' => true]);
        $this->assertDatabaseHas('favoritos', [
            'usuario_id' => $usuario->id,
            'receta_id'  => $receta->id,
        ]);

        // Quitar de favoritos (toggle)
        $response = $this->actingAs($usuario)->post(
            route('receta.favorito', $receta->id),
            [],
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertJson(['esFavorito' => false]);
        $this->assertDatabaseMissing('favoritos', [
            'usuario_id' => $usuario->id,
            'receta_id'  => $receta->id,
        ]);
    }

    /** @test */
    public function un_usuario_puede_comentar_y_valorar_la_misma_receta(): void
    {
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create();

        // Comentar
        $this->actingAs($usuario)->post(route('comentario.store', $receta->id), [
            'contenido' => 'Receta fantástica, 5 estrellas merecidas',
        ]);

        // Valorar
        $this->actingAs($usuario)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 5,
        ]);

        // Verificar ambas interacciones en BD
        $this->assertDatabaseHas('comentarios', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
            'contenido'  => 'Receta fantástica, 5 estrellas merecidas',
        ]);

        $this->assertDatabaseHas('valoraciones', [
            'receta_id'  => $receta->id,
            'usuario_id' => $usuario->id,
            'puntuacion' => 5,
        ]);
    }
}
