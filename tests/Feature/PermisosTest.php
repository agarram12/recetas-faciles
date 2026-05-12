<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Receta;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;


class PermisosTest extends TestCase
{
    use RefreshDatabase;

    private User $propietario;
    private User $intruso;
    private Receta $receta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propietario = User::factory()->create();
        $this->intruso = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $this->receta = Receta::factory()->create([
            'usuario_id'   => $this->propietario->id,
            'categoria_id' => $categoria->id,
            'titulo'       => 'Receta del propietario',
        ]);
    }

    /** @test */
    public function propietario_puede_ver_formulario_editar(): void
    {
        $response = $this->actingAs($this->propietario)
            ->get(route('receta.edit', $this->receta->id));

        $response->assertStatus(200);
        $response->assertSee('Editar receta');
    }

    /** @test */
    public function intruso_no_puede_ver_formulario_editar(): void
    {
        $response = $this->actingAs($this->intruso)
            ->get(route('receta.edit', $this->receta->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function propietario_puede_actualizar_su_receta(): void
    {
        $response = $this->actingAs($this->propietario)
            ->put(route('receta.update', $this->receta->id), [
                'titulo'         => 'Título Actualizado',
                'descripcion'    => 'Nueva descripción',
                'categoria_id'   => $this->receta->categoria_id,
                'tiempo_coccion' => 25,
                'dificultad'     => 'Fácil',
                'pasos'          => ['Nuevo paso 1', 'Nuevo paso 2'],
            ]);

        $response->assertRedirect('/receta/' . $this->receta->id);

        $this->assertDatabaseHas('recetas', [
            'id'     => $this->receta->id,
            'titulo' => 'Título Actualizado',
        ]);
    }

    /** @test */
    public function intruso_no_puede_actualizar_receta_ajena(): void
    {
        $response = $this->actingAs($this->intruso)
            ->put(route('receta.update', $this->receta->id), [
                'titulo'         => 'Hackeado',
                'descripcion'    => 'Modificado sin permiso',
                'categoria_id'   => $this->receta->categoria_id,
                'tiempo_coccion' => 10,
                'dificultad'     => 'Fácil',
                'pasos'          => ['Paso trampa'],
            ]);

        $response->assertStatus(403);

        // Verificar que NO se modificó
        $this->assertDatabaseHas('recetas', [
            'id'     => $this->receta->id,
            'titulo' => 'Receta del propietario',
        ]);
    }

    /** @test */
    public function propietario_puede_eliminar_su_receta(): void
    {
        $response = $this->actingAs($this->propietario)
            ->delete(route('receta.destroy', $this->receta->id));

        $response->assertRedirect('/');

        $this->assertDatabaseMissing('recetas', [
            'id' => $this->receta->id,
        ]);
    }

    /** @test */
    public function intruso_no_puede_eliminar_receta_ajena(): void
    {
        $response = $this->actingAs($this->intruso)
            ->delete(route('receta.destroy', $this->receta->id));

        $response->assertStatus(403);

        // Verificar que la receta sigue existiendo
        $this->assertDatabaseHas('recetas', [
            'id' => $this->receta->id,
        ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_editar(): void
    {
        $response = $this->get(route('receta.edit', $this->receta->id));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_eliminar(): void
    {
        $response = $this->delete(route('receta.destroy', $this->receta->id));

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('recetas', [
            'id' => $this->receta->id,
        ]);
    }

    /** @test */
    public function cualquier_usuario_puede_ver_detalle_receta(): void
    {
        // Sin autenticación
        $response = $this->get(route('receta.show', $this->receta->id));
        $response->assertStatus(200);

        // Otro usuario
        $response = $this->actingAs($this->intruso)
            ->get(route('receta.show', $this->receta->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function cualquier_usuario_puede_ver_feed(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
