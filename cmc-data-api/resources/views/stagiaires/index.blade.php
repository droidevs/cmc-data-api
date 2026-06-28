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
                <label class="filter-label" for="filter-groupe-code">Code groupe</label>
                <input id="filter-groupe-code" type="text" name="groupe_code" class="filter-input"
                       placeholder="ex. DEV101" value="{{ request('groupe_code') }}" style="min-width:120px">
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
