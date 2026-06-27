@extends('layouts.app')

@section('title', 'Types de formation')
@section('breadcrumb')<span class="current">Types de formation</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Types de formation</h1>
            <p class="page-subtitle">{{ $items->total() }} type(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.type-formations.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Libellé…" value="{{ request('q') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.type-formations.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                <div class="empty-title">Aucun type de formation trouvé</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Filières</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $typeFormation)
                    <tr>
                        <td class="text-muted font-mono">{{ $typeFormation->id }}</td>
                        <td>
                            <a href="{{ route('web.type-formations.show', $typeFormation) }}" class="table-link">
                                {{ $typeFormation->libelle }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $typeFormation->filieres_count ?? $typeFormation->filieres?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.type-formations.show', $typeFormation) }}" class="btn btn-outline btn-sm">Voir</a>
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
