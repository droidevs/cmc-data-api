@extends('layouts.app')

@section('title', $formateur->nom_prenom)
@section('breadcrumb')
    <a href="{{ route('web.formateurs.index') }}">Formateurs</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $formateur->nom_prenom }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div style="display:flex;align-items:center;gap:16px">
            <div class="avatar" style="width:52px;height:52px;font-size:18px">
                {{ mb_strtoupper(mb_substr($formateur->nom_prenom, 0, 2)) }}
            </div>
            <div>
                <h1 class="page-title">{{ $formateur->nom_prenom }}</h1>
                <p class="page-subtitle">
                    Matricule <span class="font-mono">{{ $formateur->mle }}</span>
                    @if($formateur->pole) · {{ $formateur->pole->libelle }} @endif
                </p>
            </div>
        </div>
    </div>

    @php
        $statutColors = ['OFPPT' => 'indigo', 'Vacataire' => 'amber', 'Contractuel' => 'gray'];
    @endphp

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Statut</div>
                    <x-badge :color="$statutColors[$formateur->statut] ?? 'gray'">{{ $formateur->statut }}</x-badge>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Pôle d'affectation</div>
                    <div class="detail-field-value">
                        @if($formateur->pole)
                            <a href="{{ route('web.poles.show', $formateur->pole) }}" class="table-link">{{ $formateur->pole->libelle }}</a>
                        @else —
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">MHS (quota mensuel)</div>
                    <div class="detail-field-value">{{ $formateur->mhs }}h / mois</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Email éducation</div>
                    <div class="detail-field-value">{{ $formateur->email_edu ?? '—' }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Mutualisé</div>
                    <div class="detail-field-value">
                        @if($formateur->mutualise)
                            <x-badge color="amber">Oui</x-badge>
                            @if($formateur->efp_mutualise) — EFP : {{ $formateur->efp_mutualise }} @endif
                        @else Non @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Enregistré le</div>
                    <div class="detail-field-value">{{ $formateur->created_at?->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Affectations --}}
    @if($formateur->affectations && $formateur->affectations->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Affectations ({{ $formateur->affectations->count() }})</span>
                <a href="{{ route('web.affectations.index', ['formateur_mle' => $formateur->mle]) }}" class="btn btn-outline btn-sm">
                    Toutes les affectations
                </a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Groupe</th>
                    <th>Module</th>
                    <th>Mode</th>
                    <th>MH Présentiel</th>
                    <th>MH Sync</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($formateur->affectations as $aff)
                    <tr>
                        <td>
                            @if($aff->groupe)
                                <a href="{{ route('web.groupes.show', $aff->groupe) }}" class="badge badge-navy">
                                    {{ $aff->groupe->code }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($aff->module)
                                <a href="{{ route('web.modules.show', $aff->module) }}" class="table-link">
                                    {{ $aff->module->libelle }}
                                </a>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($aff->mode)
                                <x-badge :color="$aff->mode === 'Résidentiel' ? 'indigo' : 'amber'">{{ $aff->mode }}</x-badge>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>{{ $aff->mh_affecte ? $aff->mh_affecte.'h' : '—' }}</td>
                        <td>{{ $aff->mh_affecte_syn ? $aff->mh_affecte_syn.'h' : '—' }}</td>
                        <td><strong>{{ $aff->mh_totale > 0 ? $aff->mh_totale.'h' : '—' }}</strong></td>
                        <td><a href="{{ route('web.affectations.show', $aff) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
