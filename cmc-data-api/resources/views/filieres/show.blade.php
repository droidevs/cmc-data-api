@extends('layouts.app')

@section('title', $filiere->libelle)
@section('breadcrumb')
    <a href="{{ route('web.filieres.index') }}">Filières</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $filiere->code_filiere }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $filiere->libelle }}</h1>
            <p class="page-subtitle">
                Code : <span class="font-mono">{{ $filiere->code_filiere }}</span>
                @if($filiere->secteur) · {{ $filiere->secteur }} @endif
            </p>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Code filière</div>
                    <div class="detail-field-value mono">{{ $filiere->code_filiere }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Libellé</div>
                    <div class="detail-field-value">{{ $filiere->libelle }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Secteur</div>
                    <div class="detail-field-value">{{ $filiere->secteur ?? '—' }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Pôle</div>
                    <div class="detail-field-value">
                        @if($filiere->pole)
                            <a href="{{ route('web.poles.show', $filiere->pole) }}" class="table-link">
                                {{ $filiere->pole->libelle }}
                            </a>
                        @else —
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Niveau</div>
                    <div class="detail-field-value">
                        <span class="badge badge-gray">{{ $filiere->niveau?->libelle ?? $filiere->niveau_id }}</span>
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Type de formation</div>
                    <div class="detail-field-value">{{ $filiere->typeFormation?->libelle ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Années --}}
    @if($filiere->annees && $filiere->annees->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Années de formation ({{ $filiere->annees->count() }})</span>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Année</th>
                    <th>Groupes</th>
                    <th>Modules</th>
                </tr>
                </thead>
                <tbody>
                @foreach($filiere->annees as $annee)
                    <tr>
                        <td>
                            <span class="badge badge-indigo" style="font-size:13px">{{ $annee->libelle }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.groupes.index', ['filiere_code' => $filiere->code_filiere, 'annee_id' => $annee->id]) }}"
                               class="badge badge-navy">
                                {{ $annee->groupes?->count() ?? 0 }} groupe(s)
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('web.modules.index', ['filiere_code' => $filiere->code_filiere, 'annee_id' => $annee->id]) }}"
                               class="badge badge-gray">
                                {{ $annee->modules?->count() ?? 0 }} module(s)
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
