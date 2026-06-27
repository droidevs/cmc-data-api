@extends('layouts.app')

@section('title', 'Niveaux')
@section('breadcrumb')<span class="current">Niveaux</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Niveaux</h1>
            <p class="page-subtitle">{{ $items->total() }} niveau(x) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.niveaux.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Libellé…" value="{{ request('q') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.niveaux.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                <div class="empty-title">Aucun niveau trouvé</div>
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
                @foreach($items as $niveau)
                    <tr>
                        <td class="text-muted font-mono">{{ $niveau->id }}</td>
                        <td>
                            <a href="{{ route('web.niveaux.show', $niveau) }}" class="table-link">
                                {{ $niveau->libelle }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $niveau->filieres_count ?? $niveau->filieres?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.niveaux.show', $niveau) }}" class="btn btn-outline btn-sm">Voir</a>
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
