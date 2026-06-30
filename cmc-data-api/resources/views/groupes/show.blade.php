@extends('layouts.app')

@section('title', $groupe->code)
@section('breadcrumb')
    <a href="{{ route('web.groupes.index') }}">Groupes</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $groupe->code }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $groupe->code }}</h1>
            <p class="page-subtitle">
                Groupe #{{ $groupe->id }}
                @if($groupe->annee?->filiere) · {{ $groupe->annee->filiere->libelle }} @endif
            </p>
        </div>
        @if($groupe->mode)
            <div class="page-header-actions">
                <span class="badge badge-{{ $groupe->mode === 'Résidentiel' ? 'indigo' : 'amber' }}"
                      style="font-size:13px;padding:6px 14px">{{ $groupe->mode }}</span>
            </div>
        @endif
    </div>

    {{-- Info card --}}
    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Code groupe</div>
                    <div class="detail-field-value mono">{{ $groupe->code }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Effectif</div>
                    <div class="detail-field-value" style="font-size:22px;font-weight:700;color:var(--navy)">
                        {{ $groupe->effectif ?? '—' }}
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Année de formation</div>
                    <div class="detail-field-value">{{ $groupe->annee?->libelle ?? '—' }}</div>
                </div>
                @if($groupe->annee?->filiere)
                    @php $filiere = $groupe->annee->filiere; @endphp
                    <div class="detail-field">
                        <div class="detail-field-label">Filière</div>
                        <div class="detail-field-value">
                            <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">
                                {{ $filiere->libelle }}
                            </a>
                        </div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Code filière</div>
                        <div class="detail-field-value mono">{{ $filiere->code_filiere }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Secteur</div>
                        <div class="detail-field-value">{{ $filiere->secteur ?? '—' }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        {{-- Affectations --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Affectations ({{ $groupe->affectations?->count() ?? 0 }})</span>
                <a href="{{ route('web.affectations.index', ['groupe_id' => $groupe->id]) }}"
                   class="btn btn-outline btn-sm">Toutes</a>
            </div>
            @if($groupe->affectations && $groupe->affectations->isNotEmpty())
                <table>
                    <thead>
                    <tr>
                        <th>Module</th>
                        <th>Formateur</th>
                        <th>MH</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupe->affectations->take(12) as $aff)
                        <tr>
                            <td>
                                @if($aff->module)
                                    <a href="{{ route('web.modules.show', $aff->module) }}" class="table-link"
                                       style="font-size:12.5px">{{ $aff->module->libelle }}</a>
                                @else <span class="text-muted">—</span> @endif
                            </td>
                            <td style="font-size:12px;color:var(--slate-500)">
                                {{ $aff->formateur?->nom_prenom ?? '—' }}
                            </td>
                            <td>{{ $aff->mh_totale > 0 ? $aff->mh_totale.'h' : '—' }}</td>
                            <td>
                                <a href="{{ route('web.affectations.show', $aff) }}"
                                   class="btn btn-outline btn-sm">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding:32px">
                    <div class="empty-icon">📋</div>
                    <div class="empty-title" style="font-size:14px">Aucune affectation</div>
                </div>
            @endif
        </div>

        {{-- Stagiaires --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Stagiaires ({{ $groupe->stagiaires?->count() ?? 0 }})</span>
                <a href="{{ route('web.stagiaires.index', ['groupe_id' => $groupe->id]) }}"
                   class="btn btn-outline btn-sm">Tous</a>
            </div>
            @if($groupe->stagiaires && $groupe->stagiaires->isNotEmpty())
                <table>
                    <thead>
                    <tr>
                        <th>CEF</th>
                        <th>Nom complet</th>
                        <th>Genre</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupe->stagiaires->take(15) as $stagiaire)
                        <tr>
                            <td class="font-mono text-muted" style="font-size:11px">{{ $stagiaire->cef }}</td>
                            <td>
                                <a href="{{ route('web.stagiaires.show', $stagiaire) }}" class="table-link"
                                   style="font-size:13px">
                                    {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-{{ $stagiaire->genre === 'F' ? 'indigo' : 'navy' }}"
                                      style="font-size:10px">{{ $stagiaire->genre }}</span>
                            </td>
                            <td>
                                <a href="{{ route('web.stagiaires.show', $stagiaire) }}"
                                   class="btn btn-outline btn-sm">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding:32px">
                    <div class="empty-icon">🎓</div>
                    <div class="empty-title" style="font-size:14px">Aucun stagiaire</div>
                </div>
            @endif
        </div>
    </div>
@endsection
