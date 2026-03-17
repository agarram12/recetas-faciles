<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Recetas Fáciles') }}</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <link rel="stylesheet" href="{{ asset('assets/style/styles.css') }}">
    </head>
    <body class="font-sans antialiased" style="background-color: #f8f9fa;">
        
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/" style="color: #729c48;">
                    <i class="bi bi-egg-fried"></i> Recetas Fáciles
                </a>
                
                <div class="ms-auto d-flex align-items-center gap-3">
                    @auth
                        <span class="fw-bold" style="color: #729c48;">
                            <i class="bi bi-person-circle"></i> Hola, {{ Auth::user()->name }}
                        </span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                Cerrar sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="border-color: #729c48; color: #729c48;">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="btn btn-sm rounded-pill px-3 text-white" style="background-color: #729c48;">Registrarse</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
            {{ $slot ?? '' }}
        </main>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>