@extends('layouts.app')

@section('title', 'Modules')
@section('breadcrumb')<span class="current">Modules</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Modules</h1>
            <p class="page-subtitle">{{ $items->total() }} module(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.modules.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Libellé ou code module…"
                       value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Filière (code)</label>
                <input type="text" name="filiere_code" class="filter-input" placeholder="ex. DIA_DEV_TS"
                       value="{{ request('filiere_code') }}" style="min-width:140px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Régional</label>
                <select name="regional" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('regional') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('regional') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec affectations</label>
                <select name="has_affectations" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_affectations') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_affectations') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.modules.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📚</div>
                <div class="empty-title">Aucun module trouvé</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Code module</th>
                    <th>Libellé</th>
                    <th>Filière</th>
                    <th>Année</th>
                    <th>Régional</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $module)
                    <tr>
                        <td>
                            <a href="{{ route('web.modules.show', $module) }}" class="table-link font-mono"
                               style="font-size:12.5px">
                                {{ $module->code_module }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('web.modules.show', $module) }}" class="table-link">
                                {{ $module->libelle }}
                            </a>
                        </td>
                        <td>
                            @if($module->annee?->filiere)
                                <a href="{{ route('web.filieres.show', $module->annee->filiere) }}"
                                   class="badge badge-navy">
                                    {{ $module->annee->filiere->code_filiere }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $module->annee?->label ?? '—' }}</span>
                        </td>
                        <td>
                            @if($module->regional)
                                <span class="badge badge-amber">Régional</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('web.modules.show', $module) }}"
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
