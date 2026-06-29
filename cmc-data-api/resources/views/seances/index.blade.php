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
                <select id="sc-filter-filiere" name="filiere_code" class="filter-select" disabled>
                    <option value="">Toutes les filières</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-annee">Année</label>
                <select id="sc-filter-annee" name="annee_id" class="filter-select" disabled>
                    <option value="">Toutes les années</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="sc-filter-groupe">Groupe</label>
                <select id="sc-filter-groupe" name="groupe_id" class="filter-select" disabled>
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
                            <span class="badge badge-{{ $typeC[$seance->type] ?? 'gray' }}">
                                {{ strtoupper($seance->type) }}
                            </span>
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
document.addEventListener('DOMContentLoaded', function() {
    const poleSelect    = document.getElementById('sc-filter-pole');
    const filiereSelect = document.getElementById('sc-filter-filiere');
    const anneeSelect   = document.getElementById('sc-filter-annee');
    const groupeSelect  = document.getElementById('sc-filter-groupe');

    const selectedPole    = '{{ request('pole_id') }}';
    const selectedFiliere = '{{ request('filiere_code') }}';
    const selectedAnnee   = '{{ request('annee_id') }}';
    const selectedGroupe  = '{{ request('groupe_id') }}';

    fetch('/api/v1/hierarchy/poles')
        .then(r => r.json())
        .then(data => {
            data.forEach(pole => {
                const opt = new Option(pole.libelle, pole.id, false, pole.id == selectedPole);
                poleSelect.add(opt);
            });
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
