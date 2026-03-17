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

            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/" style="color: #729c48;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" height="40" style="object-fit: contain;">
                Recetas Fáciles
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContenido">

                <form class="d-none d-md-flex mx-auto my-2 my-lg-0" style="width: 100%; max-width: 400px;" action="/" method="GET">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control border-end-0 bg-light" placeholder="Buscar recetas, ingredientes..." style="border-radius: 20px 0 0 20px; border-color: #ced4da;" value="{{ request('buscar') }}">
                        <button class="btn border-start-0 bg-light" type="submit" style="border-radius: 0 20px 20px 0; border-color: #ced4da;">
                            <i class="bi bi-search text-muted"></i>
                        </button>
                    </div>
                </form>

                <div class="ms-auto d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <div class="ms-auto d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        @auth
                        <div class="dropdown">
                            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownPerfil" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" width="40" height="40" class="rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid #729c48;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownPerfil">
                                <li class="px-3 py-2 mb-2 bg-light border-bottom">
                                    <span class="fw-bold d-block" style="color: #729c48;">{{ Auth::user()->name }}</span>
                                    <span class="text-muted small">{{ Auth::user()->email }}</span>
                                </li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Ver perfil</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i> Ajustes</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="border-color: #729c48; color: #729c48;">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="btn btn-sm rounded-pill px-3 text-white" style="background-color: #729c48;">Registrarse</a>
                        @endauth
                    </div>
                </div>

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