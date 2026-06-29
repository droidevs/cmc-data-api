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
                <select id="st-filter-filiere" name="filiere_code" class="filter-select" disabled>
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-annee">Année</label>
                <select id="st-filter-annee" name="annee_id" class="filter-select" disabled>
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="st-filter-groupe">Groupe</label>
                <select id="st-filter-groupe" name="groupe_id" class="filter-select" disabled>
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
document.addEventListener('DOMContentLoaded', function() {
    const poleSelect    = document.getElementById('st-filter-pole');
    const filiereSelect = document.getElementById('st-filter-filiere');
    const anneeSelect   = document.getElementById('st-filter-annee');
    const groupeSelect  = document.getElementById('st-filter-groupe');

    const selectedPole    = '{{ request('pole_id') }}';
    const selectedFiliere = '{{ request('filiere_code') }}';
    const selectedAnnee   = '{{ request('annee_id') }}';
    const selectedGroupe  = '{{ request('groupe_id') }}';

    fetch('/api/v1/hierarchy/poles')
        .then(r => r.json())
        .then(data => {
            data.forEach(p => poleSelect.add(new Option(p.libelle, p.id, false, p.id == selectedPole)));
            if (selectedPole) poleSelect.dispatchEvent(new Event('change'));
        });

    poleSelect.addEventListener('change', function() {
        filiereSelect.innerHTML = '<option value="">Toutes les filières</option>';
        filiereSelect.disabled = !this.value;
        anneeSelect.innerHTML  = '<option value="">Toutes les années</option>';
        anneeSelect.disabled   = true;
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled  = true;
        if (!this.value) return;
        fetch(`/api/v1/hierarchy/filieres?pole_id=${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(f => filiereSelect.add(new Option(f.libelle, f.code_filiere, false, f.code_filiere === selectedFiliere)));
                if (selectedFiliere) filiereSelect.dispatchEvent(new Event('change'));
            });
    });

    filiereSelect.addEventListener('change', function() {
        anneeSelect.innerHTML  = '<option value="">Toutes les années</option>';
        anneeSelect.disabled   = !this.value;
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled  = true;
        if (!this.value) return;
        fetch(`/api/v1/hierarchy/annees?filiere_code=${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(a => anneeSelect.add(new Option(a.label, a.id, false, a.id == selectedAnnee)));
                if (selectedAnnee) anneeSelect.dispatchEvent(new Event('change'));
            });
    });

    anneeSelect.addEventListener('change', function() {
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled  = !this.value;
        if (!this.value) return;
        fetch(`/api/v1/hierarchy/groupes?annee_id=${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(g => groupeSelect.add(new Option(g.code, g.id, false, g.id == selectedGroupe)));
            });
    });
});
</script>
@endpush
