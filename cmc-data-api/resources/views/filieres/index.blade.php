@extends('layouts.app')

@section('title', 'Filières')
@section('breadcrumb')<span class="current">Filières</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Filières</h1>
            <p class="page-subtitle">{{ $items->total() }} filière(s) enregistrée(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.filieres.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Libellé ou code filière…"
                       value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Secteur</label>
                <input type="text" name="secteur" class="filter-input"
                       placeholder="ex. Digital et IA…" value="{{ request('secteur') }}" style="min-width:160px">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-pole">Pôle</label>
                <select id="filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-niveau">Niveau</label>
                <select id="filter-niveau" name="niveau_id" class="filter-select">
                    <option value="">Tous les niveaux</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.filieres.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <div class="empty-title">Aucune filière trouvée</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Code filière</th>
                    <th>Libellé</th>
                    <th>Secteur</th>
                    <th>Pôle</th>
                    <th>Niveau</th>
                    <th>Type formation</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $filiere)
                    <tr>
                        <td>
                            <a href="{{ route('web.filieres.show', $filiere) }}"
                               class="table-link font-mono" style="font-size:12px">
                                {{ $filiere->code_filiere }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">
                                {{ $filiere->libelle }}
                            </a>
                        </td>
                        <td class="text-muted" style="font-size:12.5px">{{ $filiere->secteur ?? '—' }}</td>
                        <td>
                            @if($filiere->pole)
                                <a href="{{ route('web.poles.show', $filiere->pole) }}" class="badge badge-navy">
                                    {{ $filiere->pole->libelle }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $filiere->niveau?->libelle ?? $filiere->niveau_id }}</span>
                        </td>
                        <td class="text-muted" style="font-size:12px">
                            {{ $filiere->typeFormation?->libelle ?? '—' }}
                        </td>
                        <td>
                            <a href="{{ route('web.filieres.show', $filiere) }}"
                               class="btn btn-outline btn-sm">Voir</a>
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
    const niveauSelect = document.getElementById('filter-niveau');

    const selectedPole = '{{ request('pole_id') }}';
    const selectedNiveau = '{{ request('niveau_id') }}';

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

    // Fetch and populate niveaux
    fetch('/api/v1/hierarchy/niveaux')
        .then(res => res.json())
        .then(data => {
            data.forEach(niveau => {
                const opt = document.createElement('option');
                opt.value = niveau.id;
                opt.textContent = niveau.libelle;
                if (niveau.id == selectedNiveau) opt.selected = true;
                niveauSelect.appendChild(opt);
            });
        });
});
</script>
@endpush

