@extends('layouts.app')

@section('title', 'Espaces')
@section('breadcrumb')<span class="current">Espaces</span>@endsection

@section('topbar-actions')
    <a href="{{ route('web.espaces.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvel espace
    </a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Espaces</h1>
            <p class="page-subtitle">{{ $items->total() }} espace(s) enregistré(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.espaces.create') }}" class="btn btn-primary">+ Nouvel espace</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.espaces.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Libellé…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-pole">Pôle</label>
                <select id="filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Capacité min</label>
                <input type="number" name="capacite_min" class="filter-input" placeholder="0"
                       value="{{ request('capacite_min') }}" style="min-width:90px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Capacité max</label>
                <input type="number" name="capacite_max" class="filter-input" placeholder="999"
                       value="{{ request('capacite_max') }}" style="min-width:90px">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.espaces.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏫</div>
                <div class="empty-title">Aucun espace trouvé</div>
                <div class="empty-sub">Modifiez vos filtres ou créez un nouvel espace.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Pôle</th>
                    <th>Capacité</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $espace)
                    <tr>
                        <td class="text-muted font-mono">{{ $espace->id }}</td>
                        <td>
                            <a href="{{ route('web.espaces.show', $espace) }}" class="table-link">
                                {{ $espace->libelle }}
                            </a>
                        </td>
                        <td>
                            @if($espace->pole)
                                <a href="{{ route('web.poles.show', $espace->pole) }}" class="badge badge-navy">
                                    {{ $espace->pole->libelle }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($espace->capacite !== null)
                                <strong>{{ $espace->capacite }}</strong> places
                            @else
                                <span class="text-muted">Illimitée</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.espaces.show', $espace) }}" class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.espaces.edit', $espace) }}" class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-wrap">
                <div class="pagination-info">{{ $items->firstItem() }}–{{ $items->lastItem() }} sur {{ $items->total() }}</div>
                <div class="pagination-links">{{ $items->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const poleSelect = document.getElementById('filter-pole');
    const selectedPole = '{{ request('pole_id') }}';

    // Fetch and populate poles
    fetch('/api/v1/hierarchy/poles')
        .then(res => res.json())
        .then(data => {
            data.forEach(pole => {
                const opt = document.createElement('option');
                opt.value = pole.id;
                opt.textContent = pole.libelle;
                if (pole.id == selectedPole) opt.selected = true;
                poleSelect.appendChild(opt);
            });
        });
});
</script>
@endpush

