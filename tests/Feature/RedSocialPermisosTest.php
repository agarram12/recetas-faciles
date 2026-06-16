<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Receta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests dinámicos para verificar permisos de acceso.
 * Comprueba que usuarios no autorizados no pueden modificar contenido ajeno.
 *
 * RF-105: Tests comprueban permisos (no editar contenido ajeno)
 * RF-107: Usa RefreshDatabase (BD de prueba)
 */
class RedSocialPermisosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function el_administrador_id_1_puede_borrar_cualquier_receta(): void
    {
        // Crear admin (forzando ID 1) y un usuario normal con sus recetas
        $admin = User::factory()->create(['id' => 1]);
        $usuarioNormal = User::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id' => $usuarioNormal->id,
            'titulo'     => 'Receta de otro usuario',
        ]);

        // El admin borra la receta de otro usuario
        $response = $this->actingAs($admin)->delete(route('receta.destroy', $receta->id));

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('recetas', ['id' => $receta->id]);
    }

    /** @test */
    public function un_usuario_normal_no_puede_borrar_receta_ajena(): void
    {
        // Crear dos usuarios normales (ninguno es admin ID 1)
        $propietario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id' => $propietario->id,
            'titulo'     => 'Receta Protegida del Propietario',
        ]);

        // El otro usuario intenta borrar
        $response = $this->actingAs($otroUsuario)->delete(route('receta.destroy', $receta->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('recetas', [
            'id'     => $receta->id,
            'titulo' => 'Receta Protegida del Propietario',
        ]);
    }

    /** @test */
    public function un_usuario_no_autenticado_no_puede_crear_recetas(): void
    {
        // Sin autenticación, intentar acceder al formulario de creación
        $response = $this->get(route('receta.create'));

        // Debe redirigir al login
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function un_usuario_no_autenticado_no_puede_comentar(): void
    {
        $receta = Receta::factory()->create();

        // Sin autenticación, intentar comentar
        $response = $this->post(route('comentario.store', $receta->id), [
            'contenido' => 'Intento de comentario sin login',
        ]);

        $response->assertRedirect(route('login'));

        // No se debe haber creado el comentario
        $this->assertDatabaseMissing('comentarios', [
            'receta_id' => $receta->id,
            'contenido' => 'Intento de comentario sin login',
        ]);
    }

    /** @test */
    public function un_usuario_no_autenticado_no_puede_valorar(): void
    {
        $receta = Receta::factory()->create();

        // Sin autenticación, intentar valorar
        $response = $this->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 5,
        ]);

        $response->assertRedirect(route('login'));

        // No se debe haber creado la valoración
        $this->assertDatabaseMissing('valoraciones', [
            'receta_id' => $receta->id,
        ]);
    }

    /** @test */
    public function un_usuario_no_autenticado_no_puede_seguir_a_otro(): void
    {
        $usuario = User::factory()->create();

        // Sin login, intentar seguir
        $response = $this->post(route('usuario.follow', $usuario->id));

        $response->assertRedirect(route('login'));
    }
}
