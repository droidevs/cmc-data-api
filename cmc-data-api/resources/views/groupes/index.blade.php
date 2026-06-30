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
                    <option value="Résidentiel" @selected(request('mode') === 'Résidentiel')>Résidentiel</option>
                    <option value="Alternance"  @selected(request('mode') === 'Alternance')>Alternance</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-pole">Pôle</label>
                <select id="filter-pole" name="pole_id" class="filter-select">
                    <option value="">Tous les pôles</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-filiere">Filière</label>
                <select id="filter-filiere" name="filiere_code" class="filter-select" disabled>
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-annee">Année de formation</label>
                <select id="filter-annee" name="annee_id" class="filter-select" disabled>
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec stagiaires</label>
                <select name="has_stagiaires" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('has_stagiaires') === '1')>Oui</option>
                    <option value="0" @selected(request('has_stagiaires') === '0')>Non</option>
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
                            <span class="badge badge-gray">{{ $groupe->annee?->libelle ?? '—' }}</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const poleSelect = document.getElementById('filter-pole');
    const filiereSelect = document.getElementById('filter-filiere');
    const anneeSelect = document.getElementById('filter-annee');

    const selectedPole = '{{ request('pole_id') }}';
    const selectedFiliere = '{{ request('filiere_code') }}';
    const selectedAnnee = '{{ request('annee_id') }}';

    // Fetch and populate poles
    fetch('/api/v1/hierarchy/poles')
        .then(res => res.json())
        .then(data => {
            data.forEach(pole => {
                const opt = document.createElement('option');
                opt.value = pole.id;
                opt.textContent = pole.libelle;
                if (pole.id == selectedPole) opt.selected = true;
                poleSelect.appendChild(opt);
            });
            if (selectedPole) {
                poleSelect.dispatchEvent(new Event('change'));
            }
        });

    poleSelect.addEventListener('change', function() {
        filiereSelect.innerHTML = '<option value="">Toutes les filières</option>';
        filiereSelect.disabled = !this.value;
        anneeSelect.innerHTML = '<option value="">Toutes les années</option>';
        anneeSelect.disabled = true;

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/filieres?pole_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(filiere => {
                    const opt = document.createElement('option');
                    opt.value = filiere.code_filiere;
                    opt.textContent = filiere.libelle;
                    if (filiere.code_filiere === selectedFiliere) opt.selected = true;
                    filiereSelect.appendChild(opt);
                });
                if (selectedFiliere) {
                    filiereSelect.dispatchEvent(new Event('change'));
                }
            });
    });

    filiereSelect.addEventListener('change', function() {
        anneeSelect.innerHTML = '<option value="">Toutes les années</option>';
        anneeSelect.disabled = !this.value;

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/annees?filiere_code=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(annee => {
                    const opt = document.createElement('option');
                    opt.value = annee.id;
                    opt.textContent = annee.label;
                    if (annee.id == selectedAnnee) opt.selected = true;
                    anneeSelect.appendChild(opt);
                });
            });
    });
});
</script>
@endpush

