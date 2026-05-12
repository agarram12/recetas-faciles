<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Receta;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;


class FlujoPrincipalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_ver_pagina_registro(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
    }

    /** @test */
    public function puede_ver_pagina_login(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_puede_registrarse(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Test Chef',
            'email'                 => 'testchef@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'testchef@example.com']);
    }

    /** @test */
    public function usuario_puede_hacer_login(): void
    {
        $usuario = User::factory()->create([
            'password' => bcrypt('mipassword'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => $usuario->email,
            'password' => 'mipassword',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($usuario);
    }

    /** @test */
    public function usuario_puede_hacer_logout(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function feed_muestra_recetas(): void
    {
        $categoria = Categoria::factory()->create();
        Receta::factory()->count(3)->create(['categoria_id' => $categoria->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function feed_filtra_por_categoria(): void
    {
        $cat1 = Categoria::factory()->create(['nombre' => 'Veganos']);
        $cat2 = Categoria::factory()->create(['nombre' => 'Carnívoros']);

        Receta::factory()->create(['titulo' => 'Ensalada Vegana', 'categoria_id' => $cat1->id]);
        Receta::factory()->create(['titulo' => 'Costillas BBQ', 'categoria_id' => $cat2->id]);

        $response = $this->get('/?categoria=' . $cat1->id, ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals(1, $json['total']);
    }

    /** @test */
    public function feed_filtra_por_dificultad(): void
    {
        $cat = Categoria::factory()->create();
        Receta::factory()->create(['dificultad' => 'Fácil', 'categoria_id' => $cat->id]);
        Receta::factory()->create(['dificultad' => 'Difícil', 'categoria_id' => $cat->id]);

        $response = $this->get('/?dificultad=Fácil', ['X-Requested-With' => 'XMLHttpRequest']);

        $json = $response->json();
        $this->assertEquals(1, $json['total']);
    }

    /** @test */
    public function feed_filtra_por_tiempo(): void
    {
        $cat = Categoria::factory()->create();
        Receta::factory()->create(['tiempo_coccion' => 10, 'categoria_id' => $cat->id]);
        Receta::factory()->create(['tiempo_coccion' => 60, 'categoria_id' => $cat->id]);

        $response = $this->get('/?tiempo=rapido', ['X-Requested-With' => 'XMLHttpRequest']);

        $json = $response->json();
        $this->assertEquals(1, $json['total']);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_su_dashboard(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee($usuario->name);
    }

    /** @test */
    public function puede_ver_perfil_de_otro_usuario(): void
    {
        $usuario = User::factory()->create();

        $response = $this->get(route('usuario.show', $usuario->id));
        $response->assertStatus(200);
        $response->assertSee($usuario->name);
    }

    /** @test */
    public function puede_ver_lista_seguidores(): void
    {
        $usuario = User::factory()->create();

        $response = $this->get(route('usuario.seguidores', $usuario->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function puede_ver_lista_seguidos(): void
    {
        $usuario = User::factory()->create();

        $response = $this->get(route('usuario.seguidos', $usuario->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_puede_seguir_a_otro(): void
    {
        $seguidor = User::factory()->create();
        $seguido = User::factory()->create();

        $response = $this->actingAs($seguidor)
            ->post(route('usuario.follow', $seguido->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('seguidores', [
            'seguidor_id' => $seguidor->id,
            'seguido_id'  => $seguido->id,
        ]);
    }

    /** @test */
    public function usuario_puede_dejar_de_seguir(): void
    {
        $seguidor = User::factory()->create();
        $seguido = User::factory()->create();

        // Seguir primero
        $seguidor->seguidos()->attach($seguido->id);

        // Dejar de seguir (toggle)
        $response = $this->actingAs($seguidor)
            ->post(route('usuario.follow', $seguido->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('seguidores', [
            'seguidor_id' => $seguidor->id,
            'seguido_id'  => $seguido->id,
        ]);
    }

    /** @test */
    public function flujo_completo_crear_receta_y_interactuar(): void
    {
        // 1. Crear usuario y categoría
        $chef = User::factory()->create();
        $comensal = User::factory()->create();
        $categoria = Categoria::factory()->create(['nombre' => 'Dulceros']);

        // 2. Chef crea una receta
        $this->actingAs($chef)->post(route('receta.store'), [
            'titulo'         => 'Tarta de Chocolate',
            'descripcion'    => 'La mejor tarta del mundo',
            'categoria_id'   => $categoria->id,
            'url_imagen'     => \Illuminate\Http\UploadedFile::fake()->image('tarta.jpg'),
            'tiempo_coccion' => 60,
            'dificultad'     => 'Media',
            'pasos'          => ['Derretir chocolate', 'Mezclar con huevo', 'Hornear 40 min'],
        ]);

        $receta = Receta::where('titulo', 'Tarta de Chocolate')->first();
        $this->assertNotNull($receta);

        // 3. Comensal ve la receta
        $response = $this->actingAs($comensal)->get(route('receta.show', $receta->id));
        $response->assertStatus(200);
        $response->assertSee('Tarta de Chocolate');

        // 4. Comensal comenta
        $this->actingAs($comensal)->post(route('comentario.store', $receta->id), [
            'contenido' => '¡Increíble receta!',
        ]);
        $this->assertDatabaseHas('comentarios', ['contenido' => '¡Increíble receta!']);

        // 5. Comensal valora
        $this->actingAs($comensal)->post(route('receta.valorar', $receta->id), [
            'puntuacion' => 5,
        ]);
        $this->assertDatabaseHas('valoraciones', ['puntuacion' => 5]);

        // 6. Comensal guarda en favoritos
        $this->actingAs($comensal)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('receta.favorito', $receta->id));
        $this->assertDatabaseHas('favoritos', [
            'usuario_id' => $comensal->id,
            'receta_id'  => $receta->id,
        ]);

        // 7. Comensal sigue al chef
        $this->actingAs($comensal)
            ->post(route('usuario.follow', $chef->id));
        $this->assertDatabaseHas('seguidores', [
            'seguidor_id' => $comensal->id,
            'seguido_id'  => $chef->id,
        ]);
    }
}
