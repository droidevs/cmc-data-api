@extends('layouts.app')

@section('title', $pole->libelle)
@section('breadcrumb')
    <a href="{{ route('web.poles.index') }}">Pôles</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $pole->libelle }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $pole->libelle }}</h1>
            <p class="page-subtitle">Pôle #{{ $pole->id }}</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:24px">
        <div class="card" style="padding:20px 22px;">
            <div class="stat-label" style="margin-bottom:6px">Formateurs rattachés</div>
            <div class="stat-value" style="font-size:28px">{{ $pole->formateurs?->count() ?? 0 }}</div>
        </div>
        <div class="card" style="padding:20px 22px;">
            <div class="stat-label" style="margin-bottom:6px">Filières</div>
            <div class="stat-value" style="font-size:28px">{{ $pole->filieres?->count() ?? 0 }}</div>
        </div>
        <div class="card" style="padding:20px 22px;">
            <div class="stat-label" style="margin-bottom:6px">Espaces</div>
            <div class="stat-value" style="font-size:28px">{{ $pole->espaces?->count() ?? 0 }}</div>
        </div>
    </div>

    {{-- Formateurs --}}
    @if($pole->formateurs && $pole->formateurs->isNotEmpty())
        <div class="card mt-24">
            <div class="card-header">
                <span class="card-title">Formateurs</span>
                <a href="{{ route('web.formateurs.index', ['pole_id' => $pole->id]) }}" class="btn btn-outline btn-sm">
                    Voir tous
                </a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom & Prénom</th>
                    <th>Statut</th>
                    <th>MHS</th>
                    <th>Mutualisé</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($pole->formateurs->take(10) as $formateur)
                    <tr>
                        <td class="font-mono text-muted">{{ $formateur->mle }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar">{{ mb_strtoupper(mb_substr($formateur->nom_prenom, 0, 2)) }}</div>
                                <a href="{{ route('web.formateurs.show', $formateur) }}" class="table-link">
                                    {{ $formateur->nom_prenom }}
                                </a>
                            </div>
                        </td>
                        <td>
                            @php $statutColor = $formateur->statut === 'OFPPT' ? 'indigo' : ($formateur->statut === 'Vacataire' ? 'amber' : 'gray'); @endphp
                            <span class="badge badge-{{ $statutColor }}">{{ $formateur->statut }}</span>
                        </td>
                        <td>{{ $formateur->mhs }}h/mois</td>
                        <td>
                            @if($formateur->mutualise)
                                <span class="badge badge-amber">Mutualisé</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><a href="{{ route('web.formateurs.show', $formateur) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Filières --}}
    @if($pole->filieres && $pole->filieres->isNotEmpty())
        <div class="card mt-24">
            <div class="card-header">
                <span class="card-title">Filières</span>
                <a href="{{ route('web.filieres.index', ['pole_id' => $pole->id]) }}" class="btn btn-outline btn-sm">Voir toutes</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Secteur</th>
                    <th>Niveau</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($pole->filieres as $filiere)
                    <tr>
                        <td class="font-mono"><span class="badge badge-navy">{{ $filiere->code_filiere }}</span></td>
                        <td>
                            <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">{{ $filiere->libelle }}</a>
                        </td>
                        <td class="text-muted">{{ $filiere->secteur ?? '—' }}</td>
                        <td><span class="badge badge-gray">{{ $filiere->niveau_id }}</span></td>
                        <td><a href="{{ route('web.filieres.show', $filiere) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Espaces --}}
    @if($pole->espaces && $pole->espaces->isNotEmpty())
        <div class="card mt-24">
            <div class="card-header">
                <span class="card-title">Espaces</span>
            </div>
            <table>
                <thead><tr><th>ID</th><th>Libellé</th><th>Capacité</th></tr></thead>
                <tbody>
                @foreach($pole->espaces as $espace)
                    <tr>
                        <td class="font-mono text-muted">{{ $espace->id }}</td>
                        <td>{{ $espace->libelle }}</td>
                        <td>{{ $espace->capacite ? $espace->capacite.' places' : '<span class="text-muted">Illimitée</span>' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection
