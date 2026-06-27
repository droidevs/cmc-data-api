@extends('layouts.app')

@section('title', $typeFormation->libelle)
@section('breadcrumb')
    <a href="{{ route('web.type-formations.index') }}">Types de formation</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $typeFormation->libelle }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $typeFormation->libelle }}</h1>
            <p class="page-subtitle">Type de formation #{{ $typeFormation->id }}</p>
        </div>
    </div>

    {{-- Filières --}}
    @if($typeFormation->filieres && $typeFormation->filieres->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Filières ({{ $typeFormation->filieres->count() }})</span>
                <a href="{{ route('web.filieres.index', ['type_formation_id' => $typeFormation->id]) }}" class="btn btn-outline btn-sm">Toutes</a>
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
                @foreach($typeFormation->filieres as $filiere)
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
                <div class="empty-sub">Aucune filière n'est associée à ce type de formation.</div>
            </div>
        </div>
    @endif
@endsection
