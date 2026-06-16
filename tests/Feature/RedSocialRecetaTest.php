<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Receta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests dinámicos para el CRUD de recetas.
 * Cada test crea datos con factories, ejecuta una acción real
 * contra la ruta y verifica el resultado en la base de datos.
 *
 * RF-103: Tests automáticos para creación de recetas
 * RF-105: Tests comprueban permisos (no editar contenido ajeno)
 * RF-107: Usa RefreshDatabase (BD de prueba SQLite en memoria)
 * RF-108: Cubre flujos principales del sistema
 */
class RedSocialRecetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function un_usuario_autenticado_puede_crear_una_receta_con_imagen(): void
    {
        // Crear usuario y categoría dinámicamente con factories
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();

        // Ejecutar la acción real: POST a la ruta de creación
        $response = $this->actingAs($usuario)->post(route('receta.store'), [
            'titulo'         => 'Tortilla Española Casera',
            'descripcion'    => 'La receta tradicional de la abuela con patatas y cebolla',
            'categoria_id'   => $categoria->id,
            'tiempo_coccion' => 35,
            'dificultad'     => 'Media',
            'pasos'          => ['Pelar y cortar las patatas', 'Freír a fuego lento', 'Batir los huevos y mezclar'],
            'url_imagen'     => UploadedFile::fake()->image('tortilla.jpg'),
        ]);

        // Verificar redirección exitosa
        $response->assertRedirect('/');

        // Verificar que la receta se creó en la base de datos
        $this->assertDatabaseHas('recetas', [
            'titulo'      => 'Tortilla Española Casera',
            'usuario_id'  => $usuario->id,
            'categoria_id' => $categoria->id,
            'dificultad'  => 'Media',
        ]);
    }

    /** @test */
    public function un_usuario_puede_crear_una_receta_con_imagenes_de_pasos(): void
    {
        // Datos dinámicos con factories
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();

        // Ejecutar creación con imágenes de pasos (T19)
        $response = $this->actingAs($usuario)->post(route('receta.store'), [
            'titulo'         => 'Paella Valenciana',
            'descripcion'    => 'Receta auténtica con paso a paso fotográfico',
            'categoria_id'   => $categoria->id,
            'tiempo_coccion' => 60,
            'dificultad'     => 'Difícil',
            'pasos'          => ['Preparar el sofrito', 'Añadir el arroz', 'Dejar reposar 5 minutos'],
            'url_imagen'     => UploadedFile::fake()->image('paella.jpg'),
            'imagenes_pasos' => [
                0 => UploadedFile::fake()->image('paso1_sofrito.jpg'),
                1 => UploadedFile::fake()->image('paso2_arroz.jpg'),
            ],
        ]);

        $response->assertRedirect('/');

        // Verificar que la receta existe con imágenes de pasos
        $this->assertDatabaseHas('recetas', [
            'titulo'     => 'Paella Valenciana',
            'usuario_id' => $usuario->id,
        ]);

        // Verificar que las imágenes de pasos se guardaron (campo no nulo)
        $receta = Receta::where('titulo', 'Paella Valenciana')->first();
        $this->assertNotNull($receta);
        $this->assertNotNull($receta->imagenes_pasos);
        $this->assertIsArray($receta->imagenes_pasos);
        $this->assertCount(2, $receta->imagenes_pasos);
    }

    /** @test */
    public function se_puede_ver_el_detalle_de_una_receta_creada(): void
    {
        // Crear una receta completa con factory
        $receta = Receta::factory()->create([
            'titulo' => 'Gazpacho Andaluz',
        ]);

        // Acceder al detalle
        $response = $this->get(route('receta.show', $receta->id));

        $response->assertStatus(200);
        $response->assertSee('Gazpacho Andaluz');
    }

    /** @test */
    public function el_propietario_puede_editar_su_receta(): void
    {
        // Crear usuario y receta con factories
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id'   => $usuario->id,
            'categoria_id' => $categoria->id,
            'titulo'       => 'Receta Original',
        ]);

        // Ejecutar la edición real
        $response = $this->actingAs($usuario)->put(route('receta.update', $receta->id), [
            'titulo'         => 'Receta Actualizada por el Chef',
            'descripcion'    => 'Descripción mejorada con nuevos trucos',
            'categoria_id'   => $categoria->id,
            'tiempo_coccion' => 25,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Nuevo paso mejorado', 'Segundo paso refinado'],
        ]);

        $response->assertRedirect('/receta/' . $receta->id);

        // Verificar que los datos se actualizaron en BD
        $this->assertDatabaseHas('recetas', [
            'id'     => $receta->id,
            'titulo' => 'Receta Actualizada por el Chef',
        ]);

        // Verificar que el título anterior ya no está
        $this->assertDatabaseMissing('recetas', [
            'id'     => $receta->id,
            'titulo' => 'Receta Original',
        ]);
    }

    /** @test */
    public function un_intruso_no_puede_editar_una_receta_ajena(): void
    {
        // Crear propietario, intruso y receta del propietario
        $propietario = User::factory()->create();
        $intruso = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id'   => $propietario->id,
            'categoria_id' => $categoria->id,
            'titulo'       => 'Mi Receta Secreta',
        ]);

        // El intruso intenta editar la receta ajena
        $response = $this->actingAs($intruso)->put(route('receta.update', $receta->id), [
            'titulo'         => 'Receta Hackeada',
            'descripcion'    => 'Intruso',
            'categoria_id'   => $categoria->id,
            'tiempo_coccion' => 10,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Hack'],
        ]);

        // Debe recibir 403 Forbidden
        $response->assertStatus(403);

        // La receta no debe haberse modificado
        $this->assertDatabaseHas('recetas', [
            'id'     => $receta->id,
            'titulo' => 'Mi Receta Secreta',
        ]);
    }

    /** @test */
    public function el_propietario_puede_eliminar_su_receta(): void
    {
        // Crear usuario y receta
        $usuario = User::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id' => $usuario->id,
            'titulo'     => 'Receta Para Borrar',
        ]);

        // Verificar que existe antes de borrar
        $this->assertDatabaseHas('recetas', ['id' => $receta->id]);

        // Ejecutar la eliminación real
        $response = $this->actingAs($usuario)->delete(route('receta.destroy', $receta->id));

        $response->assertRedirect('/');

        // Verificar que se eliminó de la BD
        $this->assertDatabaseMissing('recetas', ['id' => $receta->id]);
    }

    /** @test */
    public function un_intruso_no_puede_eliminar_una_receta_ajena(): void
    {
        // Crear propietario, intruso y receta
        $propietario = User::factory()->create();
        $intruso = User::factory()->create();
        $receta = Receta::factory()->create([
            'usuario_id' => $propietario->id,
            'titulo'     => 'Receta Protegida',
        ]);

        // El intruso intenta borrar
        $response = $this->actingAs($intruso)->delete(route('receta.destroy', $receta->id));

        $response->assertStatus(403);

        // La receta sigue existiendo
        $this->assertDatabaseHas('recetas', ['id' => $receta->id]);
    }

    /** @test */
    public function la_validacion_rechaza_receta_sin_titulo(): void
    {
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->create();

        // Intentar crear sin título
        $response = $this->actingAs($usuario)->post(route('receta.store'), [
            'titulo'         => '',
            'descripcion'    => 'Sin título',
            'categoria_id'   => $categoria->id,
            'tiempo_coccion' => 20,
            'dificultad'     => 'Fácil',
            'pasos'          => ['Un paso'],
            'url_imagen'     => UploadedFile::fake()->image('test.jpg'),
        ]);

        // La validación debe fallar (redirect con errores)
        $response->assertSessionHasErrors('titulo');

        // No se debe haber creado ninguna receta
        $this->assertDatabaseMissing('recetas', ['descripcion' => 'Sin título']);
    }
}
