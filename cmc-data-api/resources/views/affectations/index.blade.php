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
    </div>

    <form method="GET" action="{{ route('web.affectations.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label" for="filter-pole">Pôle</label>
                <select id="filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-filiere">Filière</label>
                <select id="filter-filiere" name="filiere_code" class="filter-select">
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-annee">Année</label>
                <select id="filter-annee" name="annee_id" class="filter-select">
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-groupe">Groupe</label>
                <select id="filter-groupe" name="groupe_id" class="filter-select">
                    <option value="">Tous les groupes</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-module-code">Code module</label>
                <input id="filter-module-code" type="text" name="module_code" class="filter-input"
                       placeholder="ex. M101" value="{{ request('module_code') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-formateur-mle">Formateur (MLE)</label>
                <input id="filter-formateur-mle" type="text" name="formateur_mle" class="filter-input"
                       placeholder="ex. 19307" value="{{ request('formateur_mle') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-mode">Mode</label>
                <select id="filter-mode" name="mode" class="filter-select">
                    <option value="">Tous</option>
                    <option value="Résidentiel" @selected(request('mode') === 'Résidentiel')>Résidentiel</option>
                    <option value="Alternance"  @selected(request('mode') === 'Alternance')>Alternance</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-has-seances">Avec séances</label>
                <select id="filter-has-seances" name="has_seances" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('has_seances') === '1')>Oui</option>
                    <option value="0" @selected(request('has_seances') === '0')>Non</option>
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
            <x-empty-state icon="📋" title="Aucune affectation trouvée" subtitle="Modifiez vos filtres ou créez une nouvelle affectation." />
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
                    @php $nbSeances = $aff->seances_count ?? $aff->seances?->count() ?? 0; @endphp
                    <tr>
                        <td class="text-muted font-mono" style="font-size:11px">{{ $aff->id }}</td>
                        <td>
                            @if($aff->groupe)
                                <a href="{{ route('web.groupes.show', $aff->groupe) }}" class="badge badge-navy">
                                    {{ $aff->groupe->code }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($aff->module)
                                <a href="{{ route('web.modules.show', $aff->module) }}" class="table-link"
                                   style="font-size:12.5px">{{ $aff->module->libelle }}</a>
                            @else
                                <span class="text-muted font-mono" style="font-size:12px">{{ $aff->module_code }}</span>
                            @endif
                        </td>
                        <td>
                            @if($aff->formateur)
                                <a href="{{ route('web.formateurs.show', $aff->formateur) }}" class="table-link"
                                   style="font-size:12.5px">{{ $aff->formateur->nom_prenom }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($aff->mode)
                                <x-badge :color="$aff->mode === 'Résidentiel' ? 'indigo' : 'amber'">{{ $aff->mode }}</x-badge>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><strong>{{ $aff->mh_totale > 0 ? $aff->mh_totale.'h' : '—' }}</strong></td>
                        <td>
                            @if($nbSeances > 0)
                                <x-badge color="green">{{ $nbSeances }}</x-badge>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.affectations.show', $aff) }}" class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.affectations.edit', $aff) }}" class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
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
document.addEventListener('DOMContentLoaded', function () {
    const C = window.CMCCascade;
    const poleSelect    = document.getElementById('filter-pole');
    const filiereSelect = document.getElementById('filter-filiere');
    const anneeSelect   = document.getElementById('filter-annee');
    const groupeSelect  = document.getElementById('filter-groupe');

    const selPole    = '{{ request('pole_id') }}';
    const selFiliere = '{{ request('filiere_code') }}';
    const selAnnee   = '{{ request('annee_id') }}';
    const selGroupe  = '{{ request('groupe_id') }}';

    C.json('/api/v1/hierarchy/poles').then(function (poles) {
        C.populate(poleSelect, poles, 'id', 'libelle', 'Tous les pôles', selPole);
        if (selPole) bootDownstream();
    });

    function bootDownstream() {
        var poleId = poleSelect.value;
        if (!poleId) return;
        Promise.all([
            C.json('/api/v1/hierarchy/filieres?pole_id=' + poleId),
            C.json('/api/v1/hierarchy/annees?pole_id='   + poleId),
            C.json('/api/v1/hierarchy/groupes?pole_id='  + poleId),
        ]).then(function (_ref) {
            C.populate(filiereSelect, _ref[0], 'code_filiere', 'libelle', 'Toutes les filières', selFiliere);
            C.populate(anneeSelect,   _ref[1], 'id', 'label',             'Toutes les années',   selAnnee);
            C.populate(groupeSelect,  _ref[2], 'id', 'code',              'Tous les groupes',    selGroupe);
        });
    }

    poleSelect.addEventListener('change', function () {
        var poleId = this.value;
        C.reset(filiereSelect, 'Toutes les filières');
        C.reset(anneeSelect,   'Toutes les années');
        C.reset(groupeSelect,  'Tous les groupes');
        if (!poleId) return;
        Promise.all([
            C.json('/api/v1/hierarchy/filieres?pole_id=' + poleId),
            C.json('/api/v1/hierarchy/annees?pole_id='   + poleId),
            C.json('/api/v1/hierarchy/groupes?pole_id='  + poleId),
        ]).then(function (_ref) {
            C.populate(filiereSelect, _ref[0], 'code_filiere', 'libelle', 'Toutes les filières', selFiliere);
            C.populate(anneeSelect,   _ref[1], 'id', 'label',             'Toutes les années',   selAnnee);
            C.populate(groupeSelect,  _ref[2], 'id', 'code',              'Tous les groupes',    selGroupe);
        });
    });

    filiereSelect.addEventListener('change', function () {
        var fc = this.value, pid = poleSelect.value;
        C.reset(anneeSelect,  'Toutes les années');
        C.reset(groupeSelect, 'Tous les groupes');
        var au = fc ? '/api/v1/hierarchy/annees?filiere_code='   + fc : (pid ? '/api/v1/hierarchy/annees?pole_id='    + pid : null);
        var gu = fc ? '/api/v1/hierarchy/groupes?filiere_code=' + fc : (pid ? '/api/v1/hierarchy/groupes?pole_id='   + pid : null);
        if (!au) return;
        Promise.all([ C.json(au), C.json(gu) ]).then(function (_ref) {
            C.populate(anneeSelect,  _ref[0], 'id', 'label', 'Toutes les années', selAnnee);
            C.populate(groupeSelect, _ref[1], 'id', 'code',  'Tous les groupes',  selGroupe);
        });
    });

    anneeSelect.addEventListener('change', function () {
        var aid = this.value, fc = filiereSelect.value, pid = poleSelect.value;
        C.reset(groupeSelect, 'Tous les groupes');
        var gu = aid ? '/api/v1/hierarchy/groupes?annee_id='     + aid
               : fc  ? '/api/v1/hierarchy/groupes?filiere_code=' + fc
               : pid  ? '/api/v1/hierarchy/groupes?pole_id='      + pid : null;
        if (!gu) return;
        C.json(gu).then(function (g) { C.populate(groupeSelect, g, 'id', 'code', 'Tous les groupes', selGroupe); });
    });
});
</script>
@endpush
