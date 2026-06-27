@extends('layouts.app')

@section('title', $niveau->libelle)
@section('breadcrumb')
    <a href="{{ route('web.niveaux.index') }}">Niveaux</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $niveau->libelle }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $niveau->libelle }}</h1>
            <p class="page-subtitle">Niveau #{{ $niveau->id }}</p>
        </div>
    </div>

    {{-- Filières --}}
    @if($niveau->filieres && $niveau->filieres->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Filières ({{ $niveau->filieres->count() }})</span>
                <a href="{{ route('web.filieres.index', ['niveau_id' => $niveau->id]) }}" class="btn btn-outline btn-sm">Toutes</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Secteur</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($niveau->filieres as $filiere)
                    <tr>
                        <td class="font-mono"><span class="badge badge-navy">{{ $filiere->code_filiere }}</span></td>
                        <td>
                            <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">{{ $filiere->libelle }}</a>
                        </td>
                        <td class="text-muted">{{ $filiere->secteur ?? '—' }}</td>
                        <td><a href="{{ route('web.filieres.show', $filiere) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <div class="empty-title">Aucune filière</div>
                <div class="empty-sub">Aucune filière n'est associée à ce niveau.</div>
            </div>
        </div>
    @endif
@endsection
