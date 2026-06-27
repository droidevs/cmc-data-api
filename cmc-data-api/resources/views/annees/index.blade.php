@extends('layouts.app')

@section('title', 'Années')
@section('breadcrumb')<span class="current">Années</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Années de formation</h1>
            <p class="page-subtitle">{{ $items->total() }} année(s) enregistrée(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.annees.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Filière (code)</label>
                <input type="text" name="filiere_code" class="filter-input" placeholder="ex. DIA_DEV_TS"
                       value="{{ request('filiere_code') }}" style="min-width:140px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Année (libellé)</label>
                <input type="number" name="libelle" class="filter-input" placeholder="1, 2…"
                       value="{{ request('libelle') }}" style="min-width:90px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Pôle (ID)</label>
                <input type="number" name="pole_id" class="filter-input" placeholder="ID pôle"
                       value="{{ request('pole_id') }}" style="min-width:90px">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.annees.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📆</div>
                <div class="empty-title">Aucune année trouvée</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Année</th>
                    <th>Filière</th>
                    <th>Groupes</th>
                    <th>Modules</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $annee)
                    <tr>
                        <td>
                            <a href="{{ route('web.annees.show', $annee) }}" class="table-link">
                                <span class="badge badge-indigo">{{ $annee->label }}</span>
                            </a>
                        </td>
                        <td>
                            @if($annee->filiere)
                                <a href="{{ route('web.filieres.show', $annee->filiere) }}" class="badge badge-navy">
                                    {{ $annee->filiere->code_filiere }}
                                </a>
                            @else
                                <span class="text-muted font-mono" style="font-size:12px">{{ $annee->filiere_code }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $annee->groupes?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $annee->modules?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.annees.show', $annee) }}" class="btn btn-outline btn-sm">Voir</a>
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
