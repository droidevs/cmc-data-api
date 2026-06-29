@extends('layouts.app')

@section('title', 'Notes')
@section('breadcrumb')<span class="current">Notes</span>@endsection

@section('topbar-actions')
    <a href="{{ route('web.notes.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Saisir une note
    </a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Notes</h1>
            <p class="page-subtitle">{{ $items->total() }} note(s) enregistrée(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.notes.index') }}">
        <div class="filter-bar">
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
                <label class="filter-label" for="filter-annee">Année</label>
                <select id="filter-annee" name="annee_id" class="filter-select" disabled>
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-groupe">Groupe</label>
                <select id="filter-groupe" name="groupe_id" class="filter-select" disabled>
                    <option value="">Tous les groupes</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-stagiaire-cef">Stagiaire (CEF)</label>
                <input id="filter-stagiaire-cef" type="text" name="stagiaire_cef" class="filter-input"
                       placeholder="ex. 123456" value="{{ request('stagiaire_cef') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-seance-id">Séance (ID)</label>
                <input id="filter-seance-id" type="number" name="seance_id" class="filter-input"
                       placeholder="ID séance" value="{{ request('seance_id') }}" style="min-width:90px">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-type">Type</label>
                <select id="filter-type" name="type" class="filter-select">
                    <option value="">Tous types</option>
                    <option value="cc"   @selected(request('type') === 'cc')>CC</option>
                    <option value="efm"  @selected(request('type') === 'efm')>EFM</option>
                    <option value="tp"   @selected(request('type') === 'tp')>TP</option>
                    <option value="th"   @selected(request('type') === 'th')>TH</option>
                    <option value="syn"  @selected(request('type') === 'syn')>SYN</option>
                    <option value="exam" @selected(request('type') === 'exam')>Examen</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-decision">Décision</label>
                <select id="filter-decision" name="decision" class="filter-select">
                    <option value="">Toutes</option>
                    <option value="Admis"      @selected(request('decision') === 'Admis')>Admis</option>
                    <option value="Redoublant" @selected(request('decision') === 'Redoublant')>Redoublant</option>
                    <option value="Abandon"    @selected(request('decision') === 'Abandon')>Abandon</option>
                    <option value="Rattrapage" @selected(request('decision') === 'Rattrapage')>Rattrapage</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-missing">Manquantes</label>
                <select id="filter-missing" name="missing" class="filter-select">
                    <option value="">Toutes</option>
                    <option value="1" @selected(request('missing') === '1')>Oui</option>
                    <option value="0" @selected(request('missing') === '0')>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.notes.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <x-empty-state icon="📝" title="Aucune note trouvée" subtitle="Modifiez vos filtres ou saisissez une nouvelle note." />
        @else
            @php
                $typeColors = ['cc' => 'amber', 'efm' => 'navy'];
                $decColors  = ['Admis' => 'green', 'Redoublant' => 'amber', 'Abandon' => 'red', 'Rattrapage' => 'indigo'];
            @endphp
            <table>
                <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Module / Groupe</th>
                    <th>Type</th>
                    <th>Note /20</th>
                    <th>Décision</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $note)
                    <tr>
                        <td>
                            @if($note->stagiaire)
                                <a href="{{ route('web.stagiaires.show', $note->stagiaire) }}" class="table-link" style="font-size:13px">
                                    {{ $note->stagiaire->nom }} {{ $note->stagiaire->prenom }}
                                </a>
                                <div class="text-muted text-sm font-mono">{{ $note->stagiaire_cef }}</div>
                            @else
                                <span class="font-mono text-muted" style="font-size:12px">{{ $note->stagiaire_cef }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $note->seance?->affectation?->module?->libelle ?? '—' }}</div>
                            <div class="text-muted text-sm">{{ $note->seance?->affectation?->groupe?->code ?? '' }}</div>
                        </td>
                        <td>
                            <x-badge :color="$typeColors[$note->type?->value] ?? 'gray'">{{ strtoupper($note->type?->label() ?? '?') }}</x-badge>
                        </td>
                        <td>
                            @if($note->valeur !== null)
                                <span style="font-weight:700;color:{{ $note->valeur >= 10 ? 'var(--green)' : 'var(--red)' }}">
                                    {{ number_format($note->valeur, 2) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($note->decision)
                                <x-badge :color="$decColors[$note->decision] ?? 'gray'">{{ $note->decision }}</x-badge>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.notes.edit', $note) }}" class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
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
    const groupeSelect = document.getElementById('filter-groupe');

    const selectedPole = '{{ request('pole_id') }}';
    const selectedFiliere = '{{ request('filiere_code') }}';
    const selectedAnnee = '{{ request('annee_id') }}';
    const selectedGroupe = '{{ request('groupe_id') }}';

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
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled = true;

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
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled = true;

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
                if (selectedAnnee) {
                    anneeSelect.dispatchEvent(new Event('change'));
                }
            });
    });

    anneeSelect.addEventListener('change', function() {
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        groupeSelect.disabled = !this.value;

        if (!this.value) return;

        fetch(`/api/v1/hierarchy/groupes?annee_id=${this.value}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(groupe => {
                    const opt = document.createElement('option');
                    opt.value = groupe.id;
                    opt.textContent = groupe.code;
                    if (groupe.id == selectedGroupe) opt.selected = true;
                    groupeSelect.appendChild(opt);
                });
            });
    });
});
</script>
@endpush
