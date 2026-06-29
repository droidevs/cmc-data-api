@extends('layouts.app')

@section('title', 'Années')
@section('breadcrumb')<span class="current">Années</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Années de formation</h1>
            <p class="page-subtitle">{{ $items->total() }} année(s) enregistrée(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.annees.index') }}">
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
                <label class="filter-label" for="filter-libelle">Année</label>
                <select id="filter-libelle" name="libelle" class="filter-select">
                    <option value="">Toutes les années</option>
                    <option value="1" @selected(request('libelle') === '1')>1ère année</option>
                    <option value="2" @selected(request('libelle') === '2')>2ème année</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.annees.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📆</div>
                <div class="empty-title">Aucune année trouvée</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Année</th>
                    <th>Filière</th>
                    <th>Groupes</th>
                    <th>Modules</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $annee)
                    <tr>
                        <td>
                            <a href="{{ route('web.annees.show', $annee) }}" class="table-link">
                                <span class="badge badge-indigo">{{ $annee->label }}</span>
                            </a>
                        </td>
                        <td>
                            @if($annee->filiere)
                                <a href="{{ route('web.filieres.show', $annee->filiere) }}" class="badge badge-navy">
                                    {{ $annee->filiere->code_filiere }}
                                </a>
                            @else
                                <span class="text-muted font-mono" style="font-size:12px">{{ $annee->filiere_code }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $annee->groupes?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $annee->modules?->count() ?? 0 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.annees.show', $annee) }}" class="btn btn-outline btn-sm">Voir</a>
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

    const selectedPole = '{{ request('pole_id') }}';
    const selectedFiliere = '{{ request('filiere_code') }}';

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
            });
    });
});
</script>
@endpush

