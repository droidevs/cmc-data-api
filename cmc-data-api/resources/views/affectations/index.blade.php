@extends('layouts.app')

@section('title', 'Affectations')
@section('breadcrumb')<span class="current">Affectations</span>@endsection

@section('topbar-actions')
    <a href="{{ route('web.affectations.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nouvelle affectation
    </a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Affectations</h1>
            <p class="page-subtitle">{{ $items->total() }} affectation(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.affectations.create') }}" class="btn btn-primary">+ Nouvelle</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.affectations.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Groupe (ID)</label>
                <input type="number" name="groupe_id" class="filter-input" placeholder="ID groupe"
                       value="{{ request('groupe_id') }}" style="min-width:100px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Code module</label>
                <input type="text" name="module_code" class="filter-input" placeholder="ex. M101"
                       value="{{ request('module_code') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Formateur (MLE)</label>
                <input type="text" name="formateur_mle" class="filter-input" placeholder="ex. 19307"
                       value="{{ request('formateur_mle') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Mode</label>
                <select name="mode" class="filter-select">
                    <option value="">Tous</option>
                    <option value="Résidentiel" {{ request('mode') === 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                    <option value="Alternance"  {{ request('mode') === 'Alternance'  ? 'selected' : '' }}>Alternance</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec séances</label>
                <select name="has_seances" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_seances') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_seances') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.affectations.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Aucune affectation trouvée</div>
                <div class="empty-sub">Modifiez vos filtres ou créez une nouvelle affectation.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Groupe</th>
                    <th>Module</th>
                    <th>Formateur présentiel</th>
                    <th>Mode</th>
                    <th>MH Total</th>
                    <th>Séances</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $aff)
                    <tr>
                        <td class="text-muted font-mono" style="font-size:11px">{{ $aff->id }}</td>
                        <td>
                            @if($aff->groupe)
                                <a href="{{ route('web.groupes.show', $aff->groupe) }}" class="badge badge-navy">
                                    {{ $aff->groupe->code }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($aff->module)
                                <a href="{{ route('web.modules.show', $aff->module) }}" class="table-link"
                                   style="font-size:12.5px">{{ $aff->module->libelle }}</a>
                            @else <span class="text-muted font-mono" style="font-size:12px">{{ $aff->module_code }}</span> @endif
                        </td>
                        <td>
                            @if($aff->formateur)
                                <a href="{{ route('web.formateurs.show', $aff->formateur) }}" class="table-link"
                                   style="font-size:12.5px">{{ $aff->formateur->nom_prenom }}</a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($aff->mode)
                                <span class="badge badge-{{ $aff->mode === 'Résidentiel' ? 'indigo' : 'amber' }}">
                                    {{ $aff->mode }}
                                </span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td><strong>{{ $aff->mh_totale > 0 ? $aff->mh_totale.'h' : '—' }}</strong></td>
                        <td>
                            @php $nbSeances = $aff->seances?->count() ?? 0; @endphp
                            @if($nbSeances > 0)
                                <span class="badge badge-green">{{ $nbSeances }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.affectations.show', $aff) }}"
                               class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.affectations.edit', $aff) }}"
                               class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
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
