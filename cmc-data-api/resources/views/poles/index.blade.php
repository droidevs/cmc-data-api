@extends('layouts.app')

@section('title', 'Pôles')
@section('breadcrumb')<span class="current">Pôles</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Pôles</h1>
            <p class="page-subtitle">{{ $items->total() }} pôle(s) au total</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('web.poles.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Nom du pôle…"
                       value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Formateurs</label>
                <select name="has_formateurs" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_formateurs') === '1' ? 'selected' : '' }}>Avec formateurs</option>
                    <option value="0" {{ request('has_formateurs') === '0' ? 'selected' : '' }}>Sans formateurs</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Filières</label>
                <select name="has_filieres" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_filieres') === '1' ? 'selected' : '' }}>Avec filières</option>
                    <option value="0" {{ request('has_filieres') === '0' ? 'selected' : '' }}>Sans filières</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.poles.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏢</div>
                <div class="empty-title">Aucun pôle trouvé</div>
                <div class="empty-sub">Modifiez vos filtres ou importez des données.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Formateurs</th>
                    <th>Filières</th>
                    <th>Espaces</th>
                    <th>Créé le</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $pole)
                    <tr>
                        <td class="text-muted font-mono">{{ $pole->id }}</td>
                        <td>
                            <a href="{{ route('web.poles.show', $pole) }}" class="table-link">
                                {{ $pole->libelle }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-green">{{ $pole->formateurs_count ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-indigo">{{ $pole->filieres_count ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $pole->espaces_count ?? '—' }}</span>
                        </td>
                        <td class="text-muted">{{ $pole->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('web.poles.show', $pole) }}" class="btn btn-outline btn-sm">Détails</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pagination-wrap">
                <div class="pagination-info">
                    Affichage {{ $items->firstItem() }}–{{ $items->lastItem() }} sur {{ $items->total() }}
                </div>
                <div class="pagination-links">
                    {{ $items->withQueryString()->links('pagination::simple-bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
@endsection
