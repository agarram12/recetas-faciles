<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests dinámicos para el sistema de seguidores.
 * Los usuarios se crean con factories y las acciones de
 * seguir/dejar de seguir se ejecutan contra las rutas reales.
 *
 * RF-108: Tests cubren flujos principales del sistema
 */
class RedSocialSeguidoresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function un_usuario_puede_seguir_a_otro(): void
    {
        // Crear dos usuarios con factories
        $seguidor = User::factory()->create(['name' => 'Fan Entusiasta']);
        $seguido = User::factory()->create(['name' => 'Chef Estrella']);

        // Ejecutar la acción real de seguir
        $response = $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));

        $response->assertRedirect();

        // Verificar que la relación se creó en BD
        $this->assertDatabaseHas('seguidores', [
            'seguidor_id' => $seguidor->id,
            'seguido_id'  => $seguido->id,
        ]);
    }

    /** @test */
    public function un_usuario_puede_dejar_de_seguir_usando_toggle(): void
    {
        // Crear usuarios y establecer la relación de seguimiento
        $seguidor = User::factory()->create();
        $seguido = User::factory()->create();

        // Primero: seguir (acción real)
        $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));

        // Verificar que se creó el seguimiento
        $this->assertDatabaseHas('seguidores', [
            'seguidor_id' => $seguidor->id,
            'seguido_id'  => $seguido->id,
        ]);

        // Segundo: dejar de seguir (toggle, misma ruta)
        $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));

        // Verificar que se eliminó el seguimiento
        $this->assertDatabaseMissing('seguidores', [
            'seguidor_id' => $seguidor->id,
            'seguido_id'  => $seguido->id,
        ]);
    }

    /** @test */
    public function no_puedes_seguirte_a_ti_mismo(): void
    {
        $usuario = User::factory()->create();

        // Intentar seguirse a sí mismo
        $response = $this->actingAs($usuario)->post(route('usuario.follow', $usuario));

        // Debe recibir mensaje de error
        $response->assertSessionHas('error', 'No puedes seguirte a ti mismo.');

        // No debe existir registro de auto-seguimiento
        $this->assertDatabaseMissing('seguidores', [
            'seguidor_id' => $usuario->id,
            'seguido_id'  => $usuario->id,
        ]);
    }

    /** @test */
    public function se_puede_ver_el_perfil_de_un_usuario(): void
    {
        $usuario = User::factory()->create(['name' => 'Chef Famoso']);

        $response = $this->get(route('usuario.show', $usuario));

        $response->assertStatus(200);
        $response->assertSee('Chef Famoso');
    }

    /** @test */
    public function seguir_y_dejar_de_seguir_no_deja_registros_duplicados(): void
    {
        $seguidor = User::factory()->create();
        $seguido = User::factory()->create();

        // Seguir → dejar de seguir → volver a seguir
        $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));
        $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));
        $this->actingAs($seguidor)->post(route('usuario.follow', $seguido));

        // Debe haber exactamente UN registro de seguimiento
        $this->assertEquals(
            1,
            \Illuminate\Support\Facades\DB::table('seguidores')
                ->where('seguidor_id', $seguidor->id)
                ->where('seguido_id', $seguido->id)
                ->count()
        );
    }
}
