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
                <label class="filter-label">Recherche</label>
                <input type="text" name="q" class="filter-input" placeholder="Nom, prénom, CEF, CNI…" value="{{ request('q') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Genre</label>
                <select name="genre" class="filter-select">
                    <option value="">Tous</option>
                    <option value="H" {{ request('genre') === 'H' ? 'selected' : '' }}>Homme</option>
                    <option value="F" {{ request('genre') === 'F' ? 'selected' : '' }}>Femme</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Statut</label>
                <select name="actif" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                    <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Code groupe</label>
                <input type="text" name="groupe_code" class="filter-input" placeholder="ex. DEV101" value="{{ request('groupe_code') }}" style="min-width:120px">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.stagiaires.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🎓</div>
                <div class="empty-title">Aucun stagiaire trouvé</div>
                <div class="empty-sub">Modifiez vos critères de recherche.</div>
            </div>
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
                        <span class="badge {{ $stagiaire->genre === 'F' ? 'badge-indigo' : 'badge-navy' }}">
                            {{ $stagiaire->genre === 'F' ? 'Femme' : 'Homme' }}
                        </span>
                        </td>
                        <td class="text-muted">{{ $stagiaire->date_naissance?->format('d/m/Y') }}</td>
                        <td>
                            @if($stagiaire->groupe)
                                <a href="{{ route('web.groupes.show', $stagiaire->groupe) }}" class="badge badge-navy">
                                    {{ $stagiaire->groupe->code }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($stagiaire->actif)
                                <span class="badge badge-green">Actif</span>
                            @else
                                <span class="badge badge-red">Inactif</span>
                            @endif
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
