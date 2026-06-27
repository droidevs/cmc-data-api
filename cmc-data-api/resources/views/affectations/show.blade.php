@extends('layouts.app')

@section('title', 'Affectation #' . $affectation->id)
@section('breadcrumb')
    <a href="{{ route('web.affectations.index') }}">Affectations</a>
    <span class="topbar-sep">/</span>
    <span class="current">Affectation #{{ $affectation->id }}</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('web.affectations.edit', $affectation) }}" class="btn btn-outline">Éditer</a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $affectation->module?->libelle ?? $affectation->module_code }}
            </h1>
            <p class="page-subtitle">
                Affectation #{{ $affectation->id }}
                @if($affectation->groupe) · Groupe {{ $affectation->groupe->code }} @endif
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.affectations.edit', $affectation) }}" class="btn btn-outline">Éditer</a>
            <form method="POST" action="{{ route('web.affectations.destroy', $affectation) }}"
                  onsubmit="return confirm('Supprimer cette affectation ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Groupe</div>
                    <div class="detail-field-value">
                        @if($affectation->groupe)
                            <a href="{{ route('web.groupes.show', $affectation->groupe) }}" class="table-link">
                                {{ $affectation->groupe->code }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Module</div>
                    <div class="detail-field-value">
                        @if($affectation->module)
                            <a href="{{ route('web.modules.show', $affectation->module) }}" class="table-link">
                                {{ $affectation->module->libelle }}
                            </a>
                            <div class="text-muted text-sm font-mono">{{ $affectation->module_code }}</div>
                        @else <span class="font-mono">{{ $affectation->module_code }}</span> @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Mode</div>
                    <div class="detail-field-value">
                        @if($affectation->mode)
                            <span class="badge badge-{{ $affectation->mode === 'Résidentiel' ? 'indigo' : 'amber' }}">
                                {{ $affectation->mode }}
                            </span>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Formateur présentiel</div>
                    <div class="detail-field-value">
                        @if($affectation->formateur)
                            <a href="{{ route('web.formateurs.show', $affectation->formateur) }}" class="table-link">
                                {{ $affectation->formateur->nom_prenom }}
                            </a>
                        @else <span class="text-muted">Non affecté</span> @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Formateur synchrone</div>
                    <div class="detail-field-value">
                        {{ $affectation->formateur_mle_syn ?? '—' }}
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Heures affectées</div>
                    <div class="detail-field-value">
                        Présentiel : <strong>{{ $affectation->mh_affecte ? $affectation->mh_affecte.'h' : '—' }}</strong>
                        &nbsp;/&nbsp;
                        Sync : <strong>{{ $affectation->mh_affecte_syn ? $affectation->mh_affecte_syn.'h' : '—' }}</strong>
                        &nbsp;/&nbsp;
                        Total : <strong style="color:var(--indigo)">{{ $affectation->mh_totale > 0 ? $affectation->mh_totale.'h' : '—' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Séances --}}
    @if($affectation->seances && $affectation->seances->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Séances planifiées ({{ $affectation->seances->count() }})</span>
                <a href="{{ route('web.seances.create', ['affectation_id' => $affectation->id]) }}"
                   class="btn btn-primary btn-sm">+ Nouvelle séance</a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Créneau</th>
                    <th>Espace</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($affectation->seances->sortBy('date') as $seance)
                    <tr>
                        <td>
                            <a href="{{ route('web.seances.show', $seance) }}" class="table-link">
                                {{ $seance->date?->format('d/m/Y') }}
                            </a>
                        </td>
                        <td>
                            @php $typeC=['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red']; @endphp
                            <span class="badge badge-{{ $typeC[$seance->type] ?? 'gray' }}">
                                {{ strtoupper($seance->type) }}
                            </span>
                        </td>
                        <td class="text-muted">
                            {{ $seance->timeRange?->start_time }} – {{ $seance->timeRange?->end_time }}
                        </td>
                        <td class="text-muted" style="font-size:12.5px">
                            {{ $seance->espace?->libelle ?? '—' }}
                        </td>
                        <td>
                            <a href="{{ route('web.seances.show', $seance) }}"
                               class="btn btn-outline btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <span class="card-title">Séances planifiées</span>
                <a href="{{ route('web.seances.create') }}" class="btn btn-primary btn-sm">+ Nouvelle séance</a>
            </div>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <div class="empty-title">Aucune séance planifiée</div>
                <div class="empty-sub">Créez des séances pour cette affectation.</div>
            </div>
        </div>
    @endif
@endsection
