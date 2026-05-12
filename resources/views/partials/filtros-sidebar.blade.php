{{-- Partial: Sidebar de filtros (reutilizado en desktop y offcanvas móvil) --}}

{{-- Filtros activos --}}
@if($categoria || $dificultad || $tiempo || $orden !== 'recientes')
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2 px-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="small fw-bold text-muted"><i class="bi bi-funnel"></i> Filtros activos</span>
            <a href="/" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                <i class="bi bi-x-lg"></i> Limpiar
            </a>
        </div>
    </div>
</div>
@endif

{{-- Categorías dinámicas --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-bookmark" style="color: #729c48;"></i> Categorías
    </div>
    <div class="list-group list-group-flush">
        @foreach($categorias as $cat)
        @php
            $iconos = ['Veganos' => '🥗', 'Carnívoros' => '🥩', 'Dulceros' => '🍰'];
            $icono = $iconos[$cat->nombre] ?? '🍽️';
            $activa = $categoria == $cat->id;
        @endphp
        <a href="/?categoria={{ $cat->id }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activa ? 'active' : '' }}"
           style="{{ $activa ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
            <span>{{ $icono }} {{ $cat->nombre }}</span>
            @if($activa)
                <i class="bi bi-check-circle-fill"></i>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- Dificultad --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-speedometer2" style="color: #729c48;"></i> Dificultad
    </div>
    <div class="list-group list-group-flush">
        @foreach(['Fácil' => '🟢', 'Media' => '🟡', 'Difícil' => '🔴'] as $nivel => $color)
        @php $activaD = $dificultad === $nivel; @endphp
        <a href="/?dificultad={{ $nivel }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaD ? 'active' : '' }}"
           style="{{ $activaD ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
            <span>{{ $color }} {{ $nivel }}</span>
            @if($activaD)
                <i class="bi bi-check-circle-fill"></i>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- Tiempo de cocción --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-clock" style="color: #729c48;"></i> Tiempo
    </div>
    <div class="list-group list-group-flush">
        @foreach(['rapido' => '⚡ Rápido (≤15 min)', 'medio' => '🕐 Medio (16-45 min)', 'largo' => '🕑 Largo (46-90 min)', 'elaborado' => '👨‍🍳 Elaborado (+90 min)'] as $clave => $etiqueta)
        @php $activaT = $tiempo === $clave; @endphp
        <a href="/?tiempo={{ $clave }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $orden !== 'recientes' ? '&orden='.$orden : '' }}" 
           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaT ? 'active' : '' }}"
           style="{{ $activaT ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
            <span>{{ $etiqueta }}</span>
            @if($activaT)
                <i class="bi bi-check-circle-fill"></i>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- Ordenar por --}}
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-header bg-white fw-bold border-0 pt-3">
        <i class="bi bi-sort-down" style="color: #729c48;"></i> Ordenar por
    </div>
    <div class="list-group list-group-flush">
        @foreach(['recientes' => '🆕 Más recientes', 'antiguos' => '📅 Más antiguos', 'rapidos' => '⏱️ Menos tiempo', 'lentos' => '🍲 Más tiempo'] as $clave => $etiqueta)
        @php $activaO = $orden === $clave; @endphp
        <a href="/?orden={{ $clave }}{{ $categoria ? '&categoria='.$categoria : '' }}{{ $dificultad ? '&dificultad='.$dificultad : '' }}{{ $tiempo ? '&tiempo='.$tiempo : '' }}" 
           class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center {{ $activaO ? 'active' : '' }}"
           style="{{ $activaO ? 'background-color: #729c48; color: white; border-radius: 8px;' : '' }}">
            <span>{{ $etiqueta }}</span>
            @if($activaO)
                <i class="bi bi-check-circle-fill"></i>
            @endif
        </a>
        @endforeach
    </div>
</div>
