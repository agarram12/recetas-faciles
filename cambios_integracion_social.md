# Cambios de Integración Social - Recetas Fáciles

Este documento detalla todos los cambios realizados para integrar el sistema de búsqueda, feed social, autenticación con perfiles, seguimiento de usuarios y notificaciones en el proyecto Laravel "Recetas Fáciles".

## 1. Modelos

### app/Models/User.php

`protected $fillable = ['name', 'email', 'password', 'avatar'];`
El campo `avatar` se añade al array fillable para permitir la asignación masiva de la ruta de la imagen de perfil.

`public function seguidores() { return $this->belongsToMany(self::class, 'seguidores', 'seguido_id', 'seguidor_id')->withTimestamps(); }`
Define la relación de seguidores: un usuario puede tener muchos seguidores a través de la tabla pivot `seguidores`, donde `seguido_id` es el usuario seguido y `seguidor_id` es quien sigue.

`public function seguidos() { return $this->belongsToMany(self::class, 'seguidores', 'seguidor_id', 'seguido_id')->withTimestamps(); }`
Define la relación de seguidos: un usuario puede seguir a muchos otros usuarios, usando la misma tabla pivot pero invirtiendo las claves.

`public function sigueA(User $usuario) { return $this->seguidos()->where('seguido_id', $usuario->id)->exists(); }`
Método helper que verifica si el usuario actual sigue a otro usuario específico, consultando la relación de seguidos.

`public function feedRecetas() { $seguidos = $this->seguidos()->pluck('seguido_id')->toArray(); return \App\Models\Receta::where(function ($query) use ($seguidos) { if (! empty($seguidos)) { $query->whereIn('usuario_id', $seguidos); } $query->orWhere('usuario_id', $this->id); }); }`
Método que construye una consulta para obtener las recetas del feed social: incluye recetas de los usuarios seguidos y las propias del usuario.

## 2. Controladores

### app/Http/Controllers/RecetaController.php

`use Illuminate\Support\Facades\DB;`
Importa la fachada DB para usar el query builder en lugar de Eloquent para consultas más complejas.

`public function index(Request $request) { $buscar = $request->input('buscar'); $query = DB::table('recetas')->join('users', 'recetas.usuario_id', '=', 'users.id')->join('categorias', 'recetas.categoria_id', '=', 'categorias.id')->select('recetas.*', 'users.id as autor_id', 'users.name as autor_nombre', 'users.avatar as autor_avatar', 'categorias.nombre as categoria_nombre'); if ($buscar) { $query->where(function ($sub) use ($buscar) { $sub->where('recetas.titulo', 'LIKE', '%' . $buscar . '%')->orWhere('recetas.descripcion', 'LIKE', '%' . $buscar . '%')->orWhere('users.name', 'LIKE', '%' . $buscar . '%')->orWhere('categorias.nombre', 'LIKE', '%' . $buscar . '%'); }); } elseif (Auth::check()) { /** @var \App\Models\User $user */ $user = Auth::user(); $seguidos = $user->seguidos()->pluck('seguido_id')->toArray(); if (! empty($seguidos)) { $query->where(function ($sub) use ($seguidos) { $sub->whereIn('recetas.usuario_id', $seguidos)->orWhere('recetas.usuario_id', Auth::id()); }); } } $recetas = $query->orderByDesc('recetas.id')->paginate(8); $populares = Receta::withAvg('valoraciones', 'puntuacion')->orderBy('valoraciones_avg_puntuacion', 'desc')->limit(3)->get(); return view('index', ['recetas' => $recetas, 'populares' => $populares, 'buscar' => $buscar]); }`
Método !!!index!!!! actualizado para manejar búsqueda y feed social: usa joins para combinar datos de recetas, usuarios y categorías; filtra por búsqueda en múltiples campos; si no hay búsqueda, muestra feed de seguidos; incluye paginación de 8 elementos.

### app/Http/Controllers/ProfileController.php

`use App\Models\User;`
Importa el modelo User para usar en el método show.

`public function update(ProfileUpdateRequest $request): RedirectResponse { $user = $request->user(); $user->fill($request->except('avatar')); if ($request->hasFile('avatar')) { $file = $request->file('avatar'); $nombreAvatar = time() . '_' . $file->getClientOriginalName(); $file->move(public_path('assets/img'), $nombreAvatar); $user->avatar = 'assets/img/' . $nombreAvatar; } if ($user->isDirty('email')) { $user->email_verified_at = null; } $user->save(); return Redirect::route('profile.edit')->with('status', 'profile-updated'); }`
Actualiza el método update para manejar la subida de avatar: excluye 'avatar' del fill masivo, procesa el archivo si existe, lo guarda en public/assets/img y actualiza la ruta en el usuario.

`public function show(User $user): View { $esSeguido = false; if (Auth::check() && Auth::id() !== $user->id) { /** @var \App\Models\User $auth */ $auth = Auth::user(); $esSeguido = $auth->sigueA($user); } $recetas = $user->recetas()->with('categoria')->orderByDesc('id')->get(); return view('usuario.show', ['usuario' => $user, 'recetas' => $recetas, 'esSeguido' => $esSeguido]); }`
Nuevo método show para mostrar el perfil público de un usuario: verifica si el usuario autenticado lo sigue, obtiene las recetas del usuario y pasa datos a la vista.

`public function toggleFollow(\Illuminate\Http\Request $request, \App\Models\User $user): RedirectResponse { $auth = $request->user(); if ($auth->id === $user->id) { return back()->with('error', 'No puedes seguirte a ti mismo.'); } $estaSeguido = $auth->sigueA($user); $auth->seguidos()->toggle($user->id); if (! $estaSeguido) { $user->notify(new \App\Notifications\NuevoSeguidor($auth)); } return back()->with('success', $estaSeguido ? 'Dejaste de seguir a este usuario.' : 'Ahora sigues a este usuario.'); }`
    public function seguidores(User $user): View { $seguidores = $user->seguidores()->with('seguidores')->paginate(20); return view('usuario.seguidores', ['usuario' => $user, 'seguidores' => $seguidores]); }`
Nuevo método seguidores para mostrar la lista paginada de seguidores de un usuario.

    public function seguidos(User $user): View { $seguidos = $user->seguidos()->with('seguidos')->paginate(20); return view('usuario.seguidos', ['usuario' => $user, 'seguidos' => $seguidos]); }`
Nuevo método seguidos para mostrar la lista paginada de usuarios seguidos por un usuario.

### app/Http/Controllers/InteraccionController.php

`use App\Models\Receta; use App\Notifications\NuevoComentario; use App\Notifications\NuevaValoracion;`
Importa el modelo Receta y las clases de notificación para enviar avisos.

`public function comentar(Request $request, $id) { $request->validate(['contenido' => 'required|string|max:500'], ['contenido.required' => 'No puedes enviar un comentario vacío.', 'contenido.max' => 'Tu comentario no puede superar los 500 caracteres.']); $comentario = Comentario::create(['receta_id' => $id, 'usuario_id' => Auth::id(), 'contenido' => $request->contenido]); $receta = Receta::findOrFail($id); if ($receta->usuario_id !== Auth::id()) { $receta->autor->notify(new NuevoComentario(Auth::user(), $receta, $comentario->contenido)); } return back()->with('success', '¡Gracias por compartir tu opinión!'); }`
Actualiza comentar para enviar notificación: crea el comentario, busca la receta, verifica que no sea el autor quien comenta y envía notificación al autor.

`public function valorar(Request $request, $id) { $request->validate(['puntuacion' => 'required|integer|min:1|max:5']); $valoracion = Valoracion::updateOrCreate(['usuario_id' => Auth::id(), 'receta_id' => $id], ['puntuacion' => $request->puntuacion]); $receta = Receta::findOrFail($id); if ($receta->usuario_id !== Auth::id()) { $receta->autor->notify(new NuevaValoracion(Auth::user(), $receta, $valoracion->puntuacion)); } return back()->with('success', '¡Gracias por tu valoración!'); }`
Actualiza valorar para enviar notificación: crea o actualiza la valoración, busca la receta, verifica que no sea el autor quien valora y envía notificación al autor.

### app/Http/Controllers/NotificationController.php

`<?php namespace App\Http\Controllers; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth; use Illuminate\View\View; class NotificationController extends Controller { public function index(Request $request): View { /** @var \App\Models\User $user */ $user = Auth::user(); $notifications = $user->notifications()->latest()->paginate(10); return view('notifications.index', ['notifications' => $notifications]); } public function markAllRead(Request $request) { Auth::user()->unreadNotifications->markAsRead(); return back()->with('success', 'Todas las notificaciones se han marcado como leídas.'); } }`
Nuevo controlador para manejar notificaciones: index lista las notificaciones paginadas, markAllRead marca todas como leídas.

## 3. Requests

### app/Http/Requests/ProfileUpdateRequest.php

`public function rules(): array { return ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)], 'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']]; }`
Añade validación para el campo avatar: debe ser una imagen opcional de tipos específicos y máximo 2MB.

## 4. Vistas

### resources/views/index.blade.php

`@if(request('buscar')) <div class="alert alert-success border-0 shadow-sm mb-4" style="background-color: #eaf3e3; color: #4e6e2e;"> <i class="bi bi-search me-2"></i> Mostrando resultados para: <strong>"{{ request('buscar') }}"</strong> <a href="/" class="float-end text-decoration-none" style="color: #729c48;">Limpiar filtro <i class="bi bi-x-circle"></i></a> </div> @else <div class="mb-3"> <h5 class="fw-bold">Feed social</h5> <p class="text-muted small">Recetas de las personas que sigues y tus publicaciones recientes.</p> </div> @endif`
Añade mensajes condicionales: si hay búsqueda, muestra alerta con resultados; si no, explica el feed social.

`@if($recetas->count() == 0) <div class="text-center py-5"> <i class="bi bi-emoji-frown display-4 text-muted mb-3"></i> <h4 class="text-muted">No hay recetas para mostrar</h4> <p class="text-muted">Prueba con otra búsqueda o sigue a nuevos usuarios.</p> </div> @endif`
Muestra mensaje cuando no hay recetas, con consejos para el usuario.

`<img src="{{ asset($receta->autor_avatar ?? 'assets/img/logo.png') }}" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;"> <div> <h6 class="mb-0 fw-bold" style="font-size: 0.9rem;"> <a href="{{ route('usuario.show', $receta->autor_id) }}" class="text-decoration-none text-dark"> {{ $receta->autor_nombre }} </a> </h6> </div>`
Actualiza el avatar y nombre del autor: usa datos del query builder, hace el nombre un enlace al perfil del usuario.

`<span class="badge bg-light text-dark border">{{ $receta->categoria_nombre }}</span>`
Actualiza la categoría para usar el nombre del query builder.

`<div class="mt-4 d-flex justify-content-center"> {{ $recetas->withQueryString()->links('pagination::bootstrap-5') }} </div>`
Añade paginación con links que mantienen los parámetros de búsqueda.

### resources/views/dashboard.blade.php

`<div class="mt-2 d-flex gap-2 align-items-center"> <a href="{{ route('usuario.seguidores', Auth::user()->id) }}" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 text-decoration-none">Seguidores: {{ Auth::user()->seguidores()->count() }}</a> <a href="{{ route('usuario.seguidos', Auth::user()->id) }}" class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 text-decoration-none">Siguiendo: {{ Auth::user()->seguidos()->count() }}</a> </div>`
Añade estadísticas de seguidores y seguidos en el dashboard como enlaces a las listas respectivas.

### resources/views/profile/partials/update-profile-information-form.blade.php

`<form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">`
Añade enctype para permitir subida de archivos.

`<div class="flex items-center gap-4"> <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100"> <img src="{{ asset($user->avatar ?? 'assets/img/logo.png') }}" alt="Avatar" class="w-full h-full object-cover"> </div> <div class="w-full"> <x-input-label for="avatar" :value="__('Foto de perfil')" /> <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500"> <x-input-error class="mt-2" :messages="$errors->get('avatar')" /> </div> </div>`
Añade sección para subir avatar: muestra imagen actual y campo de archivo.

### resources/views/layouts/app.blade.php

`<div class="dropdown me-2"> <a href="#" class="position-relative btn btn-link text-dark text-decoration-none" id="dropdownNotificaciones" data-bs-toggle="dropdown" aria-expanded="false"> <i class="bi bi-bell fs-4"></i> @if(Auth::user()->unreadNotifications->count()) <span class="badge bg-danger rounded-circle position-absolute" style="top: 0; right: -5px; font-size: 0.65rem;">{{ Auth::user()->unreadNotifications->count() }}</span> @endif </a> <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownNotificaciones" style="min-width: 320px;"> <li class="px-3 py-2 mb-2 bg-light border-bottom"> <span class="fw-bold text-dark">Notificaciones</span> </li> @forelse(Auth::user()->unreadNotifications->take(5) as $notification) <li> <a href="{{ route('notifications.index') }}" class="dropdown-item small text-wrap"> {{ $notification->data['mensaje'] ?? 'Nueva actividad' }} <br><span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span> </a> </li> @empty <li class="px-3 py-3 text-center text-muted">No hay notificaciones nuevas.</li> @endforelse <li><hr class="dropdown-divider"></li> <li> <form action="{{ route('notifications.markAllRead') }}" method="POST" class="p-3 m-0"> @csrf <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Marcar todas como leídas</button> </form> </li> </ul> </div>`
Añade dropdown de notificaciones en la navbar: muestra contador de no leídas, lista las últimas 5, enlace a ver todas y botón para marcar como leídas.

### resources/views/usuario/show.blade.php

Nueva vista para mostrar perfil público de usuario: incluye avatar, nombre, estadísticas de seguidores/seguidos (ahora como enlaces), botón seguir/dejar seguir, y lista de recetas.

### resources/views/usuario/seguidores.blade.php

Nueva vista para mostrar la lista paginada de seguidores de un usuario: incluye avatar, nombre, email, y botón para seguir/dejar seguir a cada seguidor.

### resources/views/usuario/seguidos.blade.php

Nueva vista para mostrar la lista paginada de usuarios seguidos por un usuario: incluye avatar, nombre, email, y botón para seguir/dejar seguir a cada seguido.

### resources/views/notifications/index.blade.php

Nueva vista para listar notificaciones: muestra lista con mensajes, tipos y fechas, con paginación y botón para marcar todas como leídas.

## 5. Rutas

### routes/web.php

`Route::middleware('auth')->group(function () { ... Route::post('/usuario/{user}/seguir', [ProfileController::class, 'toggleFollow'])->name('usuario.follow'); Route::get('/notificaciones', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index'); Route::post('/notificaciones/marcar-leidas', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead'); }); Route::get('/usuario/{user}', [ProfileController::class, 'show'])->name('usuario.show'); Route::get('/usuario/{user}/seguidores', [ProfileController::class, 'seguidores'])->name('usuario.seguidores'); Route::get('/usuario/{user}/seguidos', [ProfileController::class, 'seguidos'])->name('usuario.seguidos');`
Añade rutas para ver perfil de usuario, lista de seguidores y lista de seguidos.
Añade rutas protegidas para seguir usuarios, ver notificaciones y marcar como leídas, y ruta pública para ver perfil de usuario.

## 6. Migraciones

### database/migrations/2026_04_14_000001_create_seguidores_table.php

`Schema::create('seguidores', function (Blueprint $table) { $table->unsignedBigInteger('seguidor_id'); $table->unsignedBigInteger('seguido_id'); $table->timestamps(); $table->primary(['seguidor_id', 'seguido_id']); $table->foreign('seguidor_id')->references('id')->on('users')->onDelete('cascade'); $table->foreign('seguido_id')->references('id')->on('users')->onDelete('cascade'); });`
Crea tabla pivot para relaciones de seguimiento: claves primarias compuestas, foreign keys a users con cascade delete.

### database/migrations/2026_04_14_000002_create_notifications_table.php

`Schema::create('notifications', function (Blueprint $table) { $table->uuid('id')->primary(); $table->string('type'); $table->morphs('notifiable'); $table->text('data'); $table->timestamp('read_at')->nullable(); $table->timestamps(); });`
Crea tabla estándar de notificaciones Laravel: id uuid, tipo, notifiable morph, data json, read_at nullable.

## 7. Notificaciones

### app/Notifications/NuevoComentario.php

`public function __construct(public User $autor, public Receta $receta, public string $comentario) { } public function via($notifiable) { return ['database']; } public function toDatabase($notifiable) { return ['mensaje' => sprintf('%s comentó tu receta "%s".', $this->autor->name, $this->receta->titulo), 'autor_id' => $this->autor->id, 'autor_nombre' => $this->autor->name, 'receta_id' => $this->receta->id, 'ruta' => route('receta.show', $this->receta->id), 'tipo' => 'comentario', 'contenido' => $this->comentario]; }`
Clase de notificación para nuevos comentarios: constructor con datos, envía a database, formatea mensaje con autor, receta y contenido.

### app/Notifications/NuevaValoracion.php

`public function __construct(public User $autor, public Receta $receta, public int $puntuacion) { } public function via($notifiable) { return ['database']; } public function toDatabase($notifiable) { return ['mensaje' => sprintf('%s valoró tu receta "%s" con %s estrellas.', $this->autor->name, $this->receta->titulo, $this->puntuacion), 'autor_id' => $this->autor->id, 'autor_nombre' => $this->autor->name, 'receta_id' => $this->receta->id, 'ruta' => route('receta.show', $this->receta->id), 'tipo' => 'valoracion', 'puntuacion' => $this->puntuacion]; }`
Clase de notificación para nuevas valoraciones: similar a comentario, incluye puntuación.

### app/Notifications/NuevoSeguidor.php

`public function __construct(public User $seguidor) { } public function via($notifiable) { return ['database']; } public function toDatabase($notifiable) { return ['mensaje' => sprintf('%s comenzó a seguirte.', $this->seguidor->name), 'seguidor_id' => $this->seguidor->id, 'seguidor_nombre' => $this->seguidor->name, 'ruta' => route('usuario.show', $this->seguidor->id), 'tipo' => 'seguimiento']; }`
Clase de notificación para nuevos seguidores: mensaje simple con nombre del seguidor y enlace a su perfil.