@extends('layouts.app')

@section('title', 'Ajustes de perfil')

@section('content')
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Cabecera de página --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-gris btn-sm px-3 py-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0" style="color: #729c48;">
                        <i class="bi bi-gear"></i> Ajustes de perfil
                    </h3>
                    <p class="text-muted small mb-0">Gestiona tu información personal, contraseña y cuenta.</p>
                </div>
            </div>

            {{-- Mensajes de éxito --}}
            @if (session('status') === 'profile-updated')
                <div class="alert border-0 shadow-sm mb-4" style="background-color: #eaf3e3; color: #4e6e2e; border-radius: 12px;">
                    <i class="bi bi-check-circle me-2"></i> Tu perfil se ha actualizado correctamente.
                </div>
            @endif

            {{-- SECCIÓN 1: Información del perfil --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-person-circle fs-4" style="color: #729c48;"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Información personal</h5>
                            <p class="text-muted small mb-0">Actualiza tu foto de perfil, nombre y email.</p>
                        </div>
                    </div>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        {{-- Avatar --}}
                        <div class="d-flex align-items-center gap-4 mb-4 p-3 rounded" style="background-color: #f8f9fa; border-radius: 12px !important;">
                            <div class="position-relative">
                                <img id="preview-avatar" 
                                     src="{{ asset($user->avatar ?? 'assets/img/logo.png') }}" 
                                     alt="Avatar" 
                                     class="rounded-circle shadow-sm" 
                                     width="90" height="90" 
                                     style="object-fit: cover; border: 3px solid #729c48;"
                                     onerror="this.src='{{ asset('assets/img/logo.png') }}'">
                                <label for="avatar" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                       style="width: 32px; height: 32px; cursor: pointer; border: 2px solid #729c48;">
                                    <i class="bi bi-camera" style="color: #729c48; font-size: 0.85rem;"></i>
                                </label>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label mb-1">Foto de perfil</label>
                                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control form-control-sm">
                                <small class="text-muted">JPEG, PNG, JPG o WebP. Máximo 2MB.</small>
                                @error('avatar')
                                    <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            @error('name')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 p-2 rounded" style="background-color: #fff3cd;">
                                    <p class="small mb-1 text-dark">
                                        <i class="bi bi-exclamation-triangle text-warning"></i> Tu email no está verificado.
                                    </p>
                                    <button form="send-verification" class="btn btn-sm btn-outline-warning">
                                        Reenviar email de verificación
                                    </button>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="small mt-2 mb-0" style="color: #729c48;">
                                            <i class="bi bi-check-circle"></i> Se ha enviado un nuevo enlace de verificación.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Botón guardar --}}
                        <div class="text-end border-top pt-3">
                            <button type="submit" class="btn btn-verde">
                                <i class="bi bi-check-lg"></i> Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SECCIÓN 2: Cambiar contraseña --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-shield-lock fs-4" style="color: #729c48;"></i>
                        <div>
                            <h5 class="fw-bold mb-0">Cambiar contraseña</h5>
                            <p class="text-muted small mb-0">Usa una contraseña segura y única para proteger tu cuenta.</p>
                        </div>
                    </div>

                    @if (session('status') === 'password-updated')
                        <div class="alert border-0 shadow-sm mb-3" style="background-color: #eaf3e3; color: #4e6e2e; border-radius: 12px;">
                            <i class="bi bi-check-circle me-2"></i> Contraseña actualizada correctamente.
                        </div>
                    @endif

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label">Contraseña actual</label>
                            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" placeholder="Introduce tu contraseña actual">
                            @error('current_password', 'updatePassword')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password" class="form-label">Nueva contraseña</label>
                            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                            @error('password', 'updatePassword')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" placeholder="Repite la nueva contraseña">
                            @error('password_confirmation', 'updatePassword')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end border-top pt-3">
                            <button type="submit" class="btn btn-verde">
                                <i class="bi bi-lock"></i> Actualizar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SECCIÓN 3: Eliminar cuenta --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; border-left: 4px solid #dc3545 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-danger">Eliminar cuenta</h5>
                            <p class="text-muted small mb-0">Esta acción es permanente e irreversible.</p>
                        </div>
                    </div>

                    <p class="text-muted small mb-3">
                        Una vez eliminada tu cuenta, todos tus datos, recetas, comentarios y valoraciones serán eliminados permanentemente. 
                        Descarga cualquier información que quieras conservar antes de continuar.
                    </p>

                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
                        <i class="bi bi-trash3"></i> Eliminar mi cuenta
                    </button>
                </div>
            </div>

        </div>
    </div>
</main>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="modalEliminarCuenta" tabindex="-1" aria-labelledby="modalEliminarCuentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="modalEliminarCuentaLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i> ¿Estás seguro?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <p class="text-muted">
                        Una vez eliminada tu cuenta, <strong>todos tus datos serán borrados permanentemente</strong>. 
                        Introduce tu contraseña para confirmar.
                    </p>
                    <div class="mb-3">
                        <label for="password_delete" class="form-label">Contraseña</label>
                        <input id="password_delete" name="password" type="password" class="form-control" placeholder="Tu contraseña actual" required>
                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-gris" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-trash3"></i> Eliminar cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Auto-abrir modal si hay errores de eliminación --}}
@if ($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalEliminarCuenta'));
        modal.show();
    });
</script>
@endif

{{-- Preview del avatar al seleccionar --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputAvatar = document.getElementById('avatar');
        const previewAvatar = document.getElementById('preview-avatar');
        
        if (inputAvatar && previewAvatar) {
            inputAvatar.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewAvatar.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endsection
