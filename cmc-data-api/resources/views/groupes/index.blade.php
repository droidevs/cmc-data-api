@extends('layouts.app')

@section('title', 'Groupes')
@section('breadcrumb')<span class="current">Groupes</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Groupes</h1>
            <p class="page-subtitle">{{ $items->total() }} groupe(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.groupes.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Code groupe…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Mode</label>
                <select name="mode" class="filter-select">
                    <option value="">Tous modes</option>
                    <option value="Résidentiel" {{ request('mode') === 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                    <option value="Alternance"  {{ request('mode') === 'Alternance'  ? 'selected' : '' }}>Alternance</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Filière (code)</label>
                <input type="text" name="filiere_code" class="filter-input" placeholder="ex. DIA_DEV_TS"
                       value="{{ request('filiere_code') }}" style="min-width:140px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec stagiaires</label>
                <select name="has_stagiaires" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_stagiaires') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_stagiaires') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.groupes.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                <div class="empty-title">Aucun groupe trouvé</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Filière</th>
                    <th>Année</th>
                    <th>Mode</th>
                    <th>Effectif</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $groupe)
                    <tr>
                        <td>
                            <a href="{{ route('web.groupes.show', $groupe) }}" class="table-link font-mono">
                                {{ $groupe->code }}
                            </a>
                        </td>
                        <td>
                            @if($groupe->annee?->filiere)
                                <a href="{{ route('web.filieres.show', $groupe->annee->filiere) }}"
                                   class="badge badge-navy">{{ $groupe->annee->filiere->code_filiere }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $groupe->annee?->label ?? '—' }}</span>
                        </td>
                        <td>
                            @if($groupe->mode)
                                <span class="badge badge-{{ $groupe->mode === 'Résidentiel' ? 'indigo' : 'amber' }}">
                                    {{ $groupe->mode }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $groupe->effectif ?? '—' }}</strong>
                        </td>
                        <td>
                            <a href="{{ route('web.groupes.show', $groupe) }}" class="btn btn-outline btn-sm">Voir</a>
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
