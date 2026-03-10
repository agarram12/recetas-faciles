<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizca & Fácil - @yield('title', 'Inicio')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ asset('assets/style/styles.css') }}">
</head>

<body class="bg-light" style="padding-top: 100px;">
    
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm" style="min-height: 80px;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" height="55" class="d-inline-block align-text-top">
                <span class="ms-2 d-none d-sm-inline fw-bold text-primary" style="font-family: 'Poppins', sans-serif; font-size: 1.2rem;">Pizca & Fácil</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <form class="d-flex mx-auto my-2 my-lg-0 w-50">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input id="searchInput" class="form-control search-input bg-light border-0" type="search" placeholder="Buscar receta, ingrediente o chef...">
                    </div>
                </form>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-3">
                    <li class="nav-item text-center">
                        <a class="nav-link active d-flex flex-column align-items-center p-0" href="/">
                            <i class="bi bi-house-door-fill fs-5"></i>
                            <span class="small" style="font-size: 0.7rem;">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('assets/img/usuario1.png') }}" class="rounded-circle border border-2 border-white shadow-sm" width="40" height="40" style="object-fit: cover;">
                            <span class="fw-medium ms-2 d-none d-lg-block">Mi Cuenta</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/scripts/script.js') }}"></script>
</body>
</html>