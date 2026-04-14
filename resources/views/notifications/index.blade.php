@extends('layouts.app')

@section('content')
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Notificaciones</h4>
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">Marcar todas leídas</button>
                    </form>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <a href="{{ $notification->data['ruta'] ?? '#' }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            <div>
                                <div class="fw-bold">{{ $notification->data['mensaje'] ?? 'Nueva notificación' }}</div>
                                @if(! empty($notification->data['tipo']))
                                    <small class="text-muted">{{ ucfirst($notification->data['tipo']) }}</small>
                                @endif
                            </div>
                            <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-bell-slash display-4 mb-3"></i>
                            <p class="mb-0">No tienes notificaciones todavía.</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white border-0">
                    {{ $notifications->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
