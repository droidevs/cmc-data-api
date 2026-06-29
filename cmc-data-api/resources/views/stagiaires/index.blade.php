@extends('layouts.app')

@section('title', 'Stagiaires')
@section('breadcrumb')<span class="current">Stagiaires</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Stagiaires</h1>
            <p class="page-subtitle">{{ $items->total() }} stagiaire(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.stagiaires.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label" for="filter-q">Recherche</label>
                <input id="filter-q" type="text" name="q" class="filter-input"
                       placeholder="Nom, prénom, CEF, CNI…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-genre">Genre</label>
                <select id="filter-genre" name="genre" class="filter-select">
                    <option value="">Tous</option>
                    <option value="H" @selected(request('genre') === 'H')>Homme</option>
                    <option value="F" @selected(request('genre') === 'F')>Femme</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-actif">Statut</label>
                <select id="filter-actif" name="actif" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('actif') === '1')>Actifs</option>
                    <option value="0" @selected(request('actif') === '0')>Inactifs</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-pole">Pôle</label>
                <select id="st-filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-filiere">Filière</label>
                <select id="st-filter-filiere" name="filiere_code" class="filter-select">
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-annee">Année</label>
                <select id="st-filter-annee" name="annee_id" class="filter-select">
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-groupe">Groupe</label>
                <select id="st-filter-groupe" name="groupe_id" class="filter-select">
                    <option value="">Tous les groupes</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.stagiaires.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <x-empty-state icon="🎓" title="Aucun stagiaire trouvé" subtitle="Modifiez vos critères de recherche." />
        @else
            <table>
                <thead>
                <tr>
                    <th>CEF</th>
                    <th>Nom complet</th>
                    <th>Genre</th>
                    <th>Date de naissance</th>
                    <th>Groupe</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $stagiaire)
                    <tr>
                        <td class="font-mono" style="font-size:12px;color:var(--slate-500)">{{ $stagiaire->cef }}</td>
                        <td>
                            <a href="{{ route('web.stagiaires.show', $stagiaire) }}" class="table-link">
                                {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                            </a>
                            @if($stagiaire->nom_arabe)
                                <div class="text-muted text-sm" dir="rtl">{{ $stagiaire->nom_arabe }} {{ $stagiaire->prenom_arabe }}</div>
                            @endif
                        </td>
                        <td>
                            <x-badge :color="$stagiaire->genre === 'F' ? 'indigo' : 'navy'">
                                {{ $stagiaire->genre === 'F' ? 'Femme' : 'Homme' }}
                            </x-badge>
                        </td>
                        <td class="text-muted">{{ $stagiaire->date_naissance?->format('d/m/Y') }}</td>
                        <td>
                            @if($stagiaire->groupe)
                                <a href="{{ route('web.groupes.show', $stagiaire->groupe) }}" class="badge badge-navy">
                                    {{ $stagiaire->groupe->code }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <x-badge :color="$stagiaire->actif ? 'green' : 'red'">
                                {{ $stagiaire->actif ? 'Actif' : 'Inactif' }}
                            </x-badge>
                        </td>
                        <td><a href="{{ route('web.stagiaires.show', $stagiaire) }}" class="btn btn-outline btn-sm">Voir</a></td>
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
    const poleSelect    = document.getElementById('st-filter-pole');
    const filiereSelect = document.getElementById('st-filter-filiere');
    const anneeSelect   = document.getElementById('st-filter-annee');
    const groupeSelect  = document.getElementById('st-filter-groupe');

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
