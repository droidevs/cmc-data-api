@extends('layouts.app')

@section('title', 'Séances')
@section('breadcrumb')<span class="current">Séances</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Séances</h1>
            <p class="page-subtitle">{{ $items->total() }} séance(s) planifiée(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.seances.create') }}" class="btn btn-primary">+ Nouvelle séance</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.seances.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select name="type" class="filter-select">
                    <option value="">Tous types</option>
                    <option value="cours" {{ request('type') === 'cours' ? 'selected' : '' }}>Cours</option>
                    <option value="cc"    {{ request('type') === 'cc'    ? 'selected' : '' }}>CC</option>
                    <option value="efm"   {{ request('type') === 'efm'   ? 'selected' : '' }}>EFM</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date début</label>
                <input type="date" name="date_from" class="filter-input"
                       value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Date fin</label>
                <input type="date" name="date_to" class="filter-input"
                       value="{{ request('date_to') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-pole">Pôle</label>
                <select id="sc-filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-filiere">Filière</label>
                <select id="sc-filter-filiere" name="filiere_code" class="filter-select">
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-annee">Année</label>
                <select id="sc-filter-annee" name="annee_id" class="filter-select">
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-groupe">Groupe</label>
                <select id="sc-filter-groupe" name="groupe_id" class="filter-select">
                    <option value="">Tous les groupes</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Formateur (MLE)</label>
                <input type="text" name="formateur_mle" class="filter-input"
                       placeholder="ex. 19307" value="{{ request('formateur_mle') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec notes</label>
                <select name="has_notes" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_notes') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_notes') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.seances.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <div class="empty-title">Aucune séance trouvée</div>
                <div class="empty-sub">Modifiez vos filtres ou planifiez une nouvelle séance.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Module / Groupe</th>
                    <th>Type</th>
                    <th>Créneau</th>
                    <th>Espace</th>
                    <th>Notes</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $seance)
                    @php $typeC = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red']; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('web.seances.show', $seance) }}" class="table-link">
                                {{ $seance->date?->format('d/m/Y') }}
                            </a>
                            <div class="text-muted text-sm">{{ $seance->date?->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">
                                {{ $seance->affectation?->module?->libelle ?? '—' }}
                            </div>
                            <div class="text-muted text-sm">
                                {{ $seance->affectation?->groupe?->code ?? '' }}
                            </div>
                        </td>
                        <td>
                            <x-badge :color="$typeC[$seance->type?->value] ?? 'gray'">
                                {{ strtoupper($seance->type?->label()) }}
                            </x-badge>
                        </td>
                        <td class="text-muted">
                            {{ $seance->timeRange?->start_time }}
                            @if($seance->timeRange) – {{ $seance->timeRange->end_time }} @endif
                        </td>
                        <td class="text-muted" style="font-size:12.5px">
                            {{ $seance->espace?->libelle ?? '—' }}
                        </td>
                        <td>
                            @php $nbNotes = $seance->notes?->count() ?? 0; @endphp
                            @if($nbNotes > 0)
                                <a href="{{ route('web.notes.index', ['seance_id' => $seance->id]) }}"
                                   class="badge badge-green">{{ $nbNotes }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.seances.show', $seance) }}"
                               class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.seances.edit', $seance) }}"
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const C = window.CMCCascade;
    const poleSelect    = document.getElementById('sc-filter-pole');
    const filiereSelect = document.getElementById('sc-filter-filiere');
    const anneeSelect   = document.getElementById('sc-filter-annee');
    const groupeSelect  = document.getElementById('sc-filter-groupe');

    const selPole    = '{{ request('pole_id') }}';
    const selFiliere = '{{ request('filiere_code') }}';
    const selAnnee   = '{{ request('annee_id') }}';
    const selGroupe  = '{{ request('groupe_id') }}';

    C.json('/api/v1/hierarchy/poles').then(function (poles) {
        C.populate(poleSelect, poles, 'id', 'libelle', 'Tous les pôles', selPole);
        if (selPole) bootDownstream();
    });

    function bootDownstream() {
        var pid = poleSelect.value;
        if (!pid) return;
        Promise.all([
            C.json('/api/v1/hierarchy/filieres?pole_id=' + pid),
            C.json('/api/v1/hierarchy/annees?pole_id='   + pid),
            C.json('/api/v1/hierarchy/groupes?pole_id='  + pid),
        ]).then(function (_ref) {
            C.populate(filiereSelect, _ref[0], 'code_filiere', 'libelle', 'Toutes les filières', selFiliere);
            C.populate(anneeSelect,   _ref[1], 'id', 'label',             'Toutes les années',   selAnnee);
            C.populate(groupeSelect,  _ref[2], 'id', 'code',              'Tous les groupes',    selGroupe);
        });
    }

    poleSelect.addEventListener('change', function () {
        var pid = this.value;
        C.reset(filiereSelect, 'Toutes les filières');
        C.reset(anneeSelect,   'Toutes les années');
        C.reset(groupeSelect,  'Tous les groupes');
        if (!pid) return;
        Promise.all([
            C.json('/api/v1/hierarchy/filieres?pole_id=' + pid),
            C.json('/api/v1/hierarchy/annees?pole_id='   + pid),
            C.json('/api/v1/hierarchy/groupes?pole_id='  + pid),
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
