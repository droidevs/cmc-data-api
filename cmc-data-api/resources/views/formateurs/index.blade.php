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
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Nom, matricule…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Statut</label>
                <select name="statut" class="filter-select">
                    <option value="">Tous statuts</option>
                    <option value="OFPPT"       {{ request('statut') === 'OFPPT'       ? 'selected' : '' }}>OFPPT</option>
                    <option value="Vacataire"   {{ request('statut') === 'Vacataire'   ? 'selected' : '' }}>Vacataire</option>
                    <option value="Contractuel" {{ request('statut') === 'Contractuel' ? 'selected' : '' }}>Contractuel</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Mutualisé</label>
                <select name="mutualise" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('mutualise') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('mutualise') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec affectations</label>
                <select name="has_affectations" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_affectations') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_affectations') === '0' ? 'selected' : '' }}>Non</option>
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
            <div class="empty-state">
                <div class="empty-icon">👤</div>
                <div class="empty-title">Aucun formateur trouvé</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom & Prénom</th>
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
                            @php $sc = $formateur->statut === 'OFPPT' ? 'indigo' : ($formateur->statut === 'Vacataire' ? 'amber' : 'gray'); @endphp
                            <span class="badge badge-{{ $sc }}">{{ $formateur->statut }}</span>
                        </td>
                        <td>{{ $formateur->mhs }}h</td>
                        <td class="text-muted" style="font-size:12px">{{ $formateur->email_edu ?? '—' }}</td>
                        <td>
                            @if($formateur->mutualise)
                                <span class="badge badge-amber">Oui</span>
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
