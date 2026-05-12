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
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top" style="z-index: 1050;">
        <div class="container">

            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/" style="color: #729c48;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" height="40" style="object-fit: contain;">
                Recetas Fáciles
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContenido">

                {{-- RF-101: Búsqueda visible tanto en desktop como móvil --}}
                <form class="d-flex mx-auto my-2 my-lg-0" style="width: 100%; max-width: 400px;" action="/" method="GET" id="formBusquedaNav">
                    <div class="input-group">
                        <input type="text" name="buscar" id="searchInputNav" class="form-control border-end-0 bg-light" placeholder="Buscar recetas, ingredientes..." style="border-radius: 20px 0 0 20px; border-color: #ced4da;" value="{{ request('buscar') }}" autocomplete="off">
                        <button class="btn border-start-0 bg-light" type="submit" style="border-radius: 0 20px 20px 0; border-color: #ced4da;">
                            <i class="bi bi-search text-muted" id="searchIcon"></i>
                            <span class="spinner-border spinner-border-sm text-success search-spinner" id="searchSpinner" role="status"></span>
                        </button>
                    </div>
                </form>

                <div class="ms-auto d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <div class="ms-auto d-flex align-items-center gap-3 mt-3 mt-lg-0">
                        @auth
                        <div class="dropdown me-2">
                            <a href="#" class="position-relative btn btn-link text-dark text-decoration-none" id="dropdownNotificaciones" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-4"></i>
                                @if(Auth::user()->unreadNotifications->count())
                                    <span class="badge bg-danger rounded-circle position-absolute" style="top: 0; right: -5px; font-size: 0.65rem;">{{ Auth::user()->unreadNotifications->count() }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownNotificaciones" style="min-width: 320px;">
                                <li class="px-3 py-3 bg-light border-bottom">
                                    <span class="fw-bold text-dark">Notificaciones</span>
                                </li>
                                @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                    <li>
                                        <a href="{{ route('notifications.index') }}" class="dropdown-item small text-wrap">
                                            {{ $notification->data['mensaje'] ?? 'Nueva actividad' }}
                                            <br><span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-3 py-3 text-center text-muted">No hay notificaciones nuevas.</li>
                                @endforelse
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}">Ver todas</a></li>
                                <li>
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="p-3 m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Marcar todas como leídas</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownPerfil" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" width="40" height="40" class="rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid #729c48;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownPerfil">
                                <li class="px-3 py-2 mb-2 bg-light border-bottom">
                                    <span class="fw-bold d-block" style="color: #729c48;">{{ Auth::user()->name }}</span>
                                    <span class="text-muted small">{{ Auth::user()->email }}</span>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-person me-2"></i> Ver perfil</a></li>
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

    {{-- RF-100: Overlay de carga global --}}
    <div id="globalLoadingOverlay" class="loading-overlay d-none">
        <div class="loading-overlay-content">
            <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-white mt-3 fw-bold" id="loadingOverlayText">Procesando...</p>
        </div>
    </div>

    {{-- RF-100: Toast global reutilizable --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="toastNotificacion" class="toast align-items-center text-white border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body" id="toastMensaje">
                    <i class="bi bi-check-circle me-2"></i> <span></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>

    <main class="page-content">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* RF-99: Transición de entrada de página */
        .page-content {
            animation: pageIn 0.3s ease-out;
        }
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* RF-99: Transición de salida — solo usada via JS inline styles, no CSS class */

        /* RF-100: Spinner de búsqueda */
        .search-spinner {
            display: none;
        }
        .search-spinner.active {
            display: inline-block;
        }
        .search-spinner.active + #searchIcon,
        .active ~ #searchIcon {
            display: none;
        }

        /* RF-100: Overlay de carga global */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading-overlay.d-none {
            display: none !important;
        }
        .loading-overlay-content {
            text-align: center;
            animation: pulseIn 0.3s ease;
        }
        @keyframes pulseIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* RF-100: Botón con estado de carga */
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.75;
        }
        .btn-loading .btn-text {
            visibility: hidden;
        }
        .btn-loading .btn-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>

    <script>
    // ============================================
    // RF-100: SISTEMA DE TOASTS GLOBAL
    // ============================================
    window.mostrarToast = function(mensaje, tipo) {
        const toastEl = document.getElementById('toastNotificacion');
        const toastMsg = document.getElementById('toastMensaje');
        
        if (!toastEl || !toastMsg) return;

        const colores = {
            'success': '#729c48',
            'danger': '#dc3545',
            'info': '#0dcaf0',
            'warning': '#ffc107',
        };

        toastEl.style.backgroundColor = colores[tipo] || colores.success;
        toastMsg.querySelector('span').textContent = mensaje;

        const iconos = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-triangle',
            'info': 'bi-info-circle',
            'warning': 'bi-exclamation-circle',
        };
        const iconoEl = toastMsg.querySelector('i');
        iconoEl.className = 'bi me-2 ' + (iconos[tipo] || iconos.success);

        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    };

    // ============================================
    // RF-100: OVERLAY DE CARGA GLOBAL en formularios
    // ============================================
    window.mostrarOverlay = function(texto) {
        const overlay = document.getElementById('globalLoadingOverlay');
        const overlayText = document.getElementById('loadingOverlayText');
        if (overlay) {
            overlayText.textContent = texto || 'Procesando...';
            overlay.classList.remove('d-none');
        }
    };
    window.ocultarOverlay = function() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) overlay.classList.add('d-none');
    };

    document.addEventListener('DOMContentLoaded', function() {
        // RF-100: Interceptar envío de formularios para mostrar overlay
        document.querySelectorAll('form[method="POST"], form[method="post"]').forEach(function(form) {
            // No interceptar formularios AJAX marcados con data-ajax
            if (form.dataset.ajax) return;
            // No interceptar formularios pequeños (logout, marcar notificaciones, etc.)
            if (form.closest('.dropdown-menu') || form.closest('.offcanvas')) return;

            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.classList.contains('btn-loading')) {
                    const originalHtml = btn.innerHTML;
                    btn.classList.add('btn-loading');
                    btn.innerHTML = '<span class="btn-text">' + originalHtml + '</span><span class="btn-spinner"><span class="spinner-border spinner-border-sm" role="status"></span></span>';
                    btn.disabled = true;
                }
                mostrarOverlay('Guardando...');
            });
        });

        // ============================================
        // RF-99: TRANSICIÓN DE SALIDA al navegar
        // ============================================
        document.querySelectorAll('a[href]').forEach(function(link) {
            // Solo para enlaces internos que no abran en otra pestaña
            if (link.target === '_blank') return;
            if (link.getAttribute('href').startsWith('#')) return;
            if (link.getAttribute('href').startsWith('javascript:')) return;
            if (link.closest('.dropdown-menu')) return;
            if (link.dataset.bsToggle) return;

            const href = link.getAttribute('href');
            // Solo para enlaces internos (mismo dominio)
            try {
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;
            } catch(e) { return; }

            link.addEventListener('click', function(e) {
                // No interferir si es ctrl+click o cmd+click
                if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                
                e.preventDefault();
                const content = document.querySelector('.page-content');
                if (content) {
                    content.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                    content.style.opacity = '0';
                    content.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        window.location.href = href;
                    }, 180);
                } else {
                    window.location.href = href;
                }
            });
        });

        // ============================================
        // FIX: Restaurar estado al volver con el botón atrás (bfcache)
        // ============================================
        window.addEventListener('pageshow', function(event) {
            const content = document.querySelector('.page-content');
            if (content) {
                content.style.transition = 'none';
                content.style.opacity = '1';
                content.style.transform = 'translateY(0)';
                // Forzar reflow, luego restaurar transición
                void content.offsetHeight;
                content.style.transition = '';
            }
            // Ocultar overlay de carga si quedó visible
            ocultarOverlay();
            // Restaurar botones de submit que quedaron deshabilitados
            document.querySelectorAll('.btn-loading').forEach(function(btn) {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
            });
        });

        // ============================================
        // RF-98: Prevenir submit del form de búsqueda (solo en feed)
        // ============================================
        const formBusqueda = document.getElementById('formBusquedaNav');
        if (formBusqueda) {
            formBusqueda.addEventListener('submit', function(e) {
                // Si estamos en la página del feed, usar AJAX
                const feedContainer = document.getElementById('feedContainer');
                if (feedContainer) {
                    e.preventDefault();
                    const input = document.getElementById('searchInputNav');
                    if (input && input.value.trim().length >= 2) {
                        // Disparar el evento input para activar la búsqueda AJAX
                        input.dispatchEvent(new Event('input'));
                    } else if (input && input.value.trim().length === 0) {
                        // Restaurar feed sin recarga
                        input.dispatchEvent(new Event('input'));
                    }
                }
                // Si no estamos en el feed, dejar que el form navegue normalmente
            });
        }
    });

    // RF-100: Mostrar toast de flash messages del servidor
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            mostrarToast('{{ session('success') }}', 'success');
        });
    @endif
    </script>
</body>

</html>