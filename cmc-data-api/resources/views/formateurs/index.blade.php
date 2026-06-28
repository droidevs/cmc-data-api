@extends('layouts.app')

@section('title', 'Formateurs')
@section('breadcrumb')<span class="current">Formateurs</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Formateurs</h1>
            <p class="page-subtitle">{{ $items->total() }} formateur(s) enregistré(s)</p>
        </div>
    </div>

    <form method="GET" action="{{ route('web.formateurs.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label" for="filter-q">Recherche</label>
                <input id="filter-q" type="text" name="q" class="filter-input"
                       placeholder="Nom, matricule…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-statut">Statut</label>
                <select id="filter-statut" name="statut" class="filter-select">
                    <option value="">Tous statuts</option>
                    <option value="OFPPT"       @selected(request('statut') === 'OFPPT')>OFPPT</option>
                    <option value="Vacataire"   @selected(request('statut') === 'Vacataire')>Vacataire</option>
                    <option value="Contractuel" @selected(request('statut') === 'Contractuel')>Contractuel</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-mutualise">Mutualisé</label>
                <select id="filter-mutualise" name="mutualise" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('mutualise') === '1')>Oui</option>
                    <option value="0" @selected(request('mutualise') === '0')>Non</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filter-has-affectations">Avec affectations</label>
                <select id="filter-has-affectations" name="has_affectations" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('has_affectations') === '1')>Oui</option>
                    <option value="0" @selected(request('has_affectations') === '0')>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.formateurs.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <x-empty-state icon="👤" title="Aucun formateur trouvé" subtitle="Modifiez vos critères de recherche." />
        @else
            @php
                $statutColors = ['OFPPT' => 'indigo', 'Vacataire' => 'amber', 'Contractuel' => 'gray'];
            @endphp
            <table>
                <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom &amp; Prénom</th>
                    <th>Pôle</th>
                    <th>Statut</th>
                    <th>MHS</th>
                    <th>Email</th>
                    <th>Mutualisé</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $formateur)
                    <tr>
                        <td class="font-mono" style="font-size:12px; color:var(--slate-500)">{{ $formateur->mle }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar">{{ mb_strtoupper(mb_substr($formateur->nom_prenom, 0, 2)) }}</div>
                                <a href="{{ route('web.formateurs.show', $formateur) }}" class="table-link">
                                    {{ $formateur->nom_prenom }}
                                </a>
                            </div>
                        </td>
                        <td>
                            @if($formateur->pole)
                                <a href="{{ route('web.poles.show', $formateur->pole) }}" class="badge badge-navy">
                                    {{ $formateur->pole->libelle }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <x-badge :color="$statutColors[$formateur->statut] ?? 'gray'">{{ $formateur->statut }}</x-badge>
                        </td>
                        <td>{{ $formateur->mhs }}h</td>
                        <td class="text-muted" style="font-size:12px">{{ $formateur->email_edu ?? '—' }}</td>
                        <td>
                            @if($formateur->mutualise)
                                <x-badge color="amber">Oui</x-badge>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><a href="{{ route('web.formateurs.show', $formateur) }}" class="btn btn-outline btn-sm">Voir</a></td>
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
