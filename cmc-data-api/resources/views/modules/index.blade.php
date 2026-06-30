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
                <label class="filter-label">Régional</label>
                <select name="regional" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('regional') === '1')>Oui</option>
                    <option value="0" @selected(request('regional') === '0')>Non</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec affectations</label>
                <select name="has_affectations" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('has_affectations') === '1')>Oui</option>
                    <option value="0" @selected(request('has_affectations') === '0')>Non</option>
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
                            <span class="badge badge-gray">{{ $module->annee?->libelle ?? '—' }}</span>
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

