@extends('layouts.app')

@section('title', $module->libelle)
@section('breadcrumb')
    <a href="{{ route('web.modules.index') }}">Modules</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $module->code_module }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $module->libelle }}</h1>
            <p class="page-subtitle">
                Code : <span class="font-mono">{{ $module->code_module }}</span>
                @if($module->annee?->filiere) · {{ $module->annee->filiere->libelle }} @endif
            </p>
        </div>
        @if($module->regional)
            <div class="page-header-actions">
                <span class="badge badge-amber" style="font-size:13px;padding:6px 14px">Régional</span>
            </div>
        @endif
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Code module</div>
                    <div class="detail-field-value mono">{{ $module->code_module }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Libellé</div>
                    <div class="detail-field-value">{{ $module->libelle }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Module régional</div>
                    <div class="detail-field-value">
                        @if($module->regional)
                            <span class="badge badge-amber">Oui</span>
                        @else
                            Non
                        @endif
                    </div>
                </div>
                @if($module->annee)
                    <div class="detail-field">
                        <div class="detail-field-label">Année de formation</div>
                        <div class="detail-field-value">{{ $module->annee->label }}</div>
                    </div>
                @endif
                @if($module->annee?->filiere)
                    @php $filiere = $module->annee->filiere; @endphp
                    <div class="detail-field">
                        <div class="detail-field-label">Filière</div>
                        <div class="detail-field-value">
                            <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">
                                {{ $filiere->libelle }}
                            </a>
                        </div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Secteur</div>
                        <div class="detail-field-value">{{ $filiere->secteur ?? '—' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Affectations --}}
    @if($module->affectations && $module->affectations->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Affectations ({{ $module->affectations->count() }})</span>
                <a href="{{ route('web.affectations.index', ['module_code' => $module->code_module]) }}"
                   class="btn btn-outline btn-sm">Toutes</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Groupe</th>
                    <th>Formateur présentiel</th>
                    <th>Formateur sync</th>
                    <th>Mode</th>
                    <th>MH Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($module->affectations as $aff)
                    <tr>
                        <td>
                            @if($aff->groupe)
                                <a href="{{ route('web.groupes.show', $aff->groupe) }}"
                                   class="badge badge-navy">{{ $aff->groupe->code }}</a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($aff->formateur)
                                <a href="{{ route('web.formateurs.show', $aff->formateur) }}" class="table-link"
                                   style="font-size:13px">{{ $aff->formateur->nom_prenom }}</a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td class="text-muted" style="font-size:12px">
                            {{ $aff->formateur_mle_syn ?? '—' }}
                        </td>
                        <td>
                            @if($aff->mode)
                                <span class="badge badge-{{ $aff->mode === 'Résidentiel' ? 'indigo' : 'amber' }}">
                                    {{ $aff->mode }}
                                </span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td><strong>{{ $aff->mh_totale > 0 ? $aff->mh_totale.'h' : '—' }}</strong></td>
                        <td>
                            <a href="{{ route('web.affectations.show', $aff) }}"
                               class="btn btn-outline btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
