@extends('layouts.app')

@section('title', $annee->label)
@section('breadcrumb')
    <a href="{{ route('web.annees.index') }}">Années</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $annee->label }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $annee->label }}</h1>
            <p class="page-subtitle">
                Année #{{ $annee->id }}
                @if($annee->filiere) · {{ $annee->filiere->libelle }} @endif
            </p>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">Libellé</div>
                    <div class="detail-field-value">{{ $annee->label }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Filière</div>
                    <div class="detail-field-value">
                        @if($annee->filiere)
                            <a href="{{ route('web.filieres.show', $annee->filiere) }}" class="table-link">
                                {{ $annee->filiere->libelle }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Code filière</div>
                    <div class="detail-field-value mono">{{ $annee->filiere_code }}</div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        {{-- Groupes --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Groupes ({{ $annee->groupes?->count() ?? 0 }})</span>
                <a href="{{ route('web.groupes.index', ['annee_id' => $annee->id]) }}" class="btn btn-outline btn-sm">Tous</a>
            </div>
            @if($annee->groupes && $annee->groupes->isNotEmpty())
                <table>
                    <thead><tr><th>Code</th><th>Mode</th><th>Effectif</th><th></th></tr></thead>
                    <tbody>
                    @foreach($annee->groupes->take(12) as $groupe)
                        <tr>
                            <td><a href="{{ route('web.groupes.show', $groupe) }}" class="table-link font-mono" style="font-size:12.5px">{{ $groupe->code }}</a></td>
                            <td>
                                @if($groupe->mode)
                                    <span class="badge badge-{{ $groupe->mode === 'Résidentiel' ? 'indigo' : 'amber' }}">{{ $groupe->mode }}</span>
                                @else <span class="text-muted">—</span> @endif
                            </td>
                            <td>{{ $groupe->effectif ?? '—' }}</td>
                            <td><a href="{{ route('web.groupes.show', $groupe) }}" class="btn btn-outline btn-sm">Voir</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding:32px">
                    <div class="empty-icon">👥</div>
                    <div class="empty-title" style="font-size:14px">Aucun groupe</div>
                </div>
            @endif
        </div>

        {{-- Modules --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Modules ({{ $annee->modules?->count() ?? 0 }})</span>
                <a href="{{ route('web.modules.index', ['annee_id' => $annee->id]) }}" class="btn btn-outline btn-sm">Tous</a>
            </div>
            @if($annee->modules && $annee->modules->isNotEmpty())
                <table>
                    <thead><tr><th>Code</th><th>Libellé</th><th>Régional</th><th></th></tr></thead>
                    <tbody>
                    @foreach($annee->modules->take(12) as $module)
                        <tr>
                            <td><span class="font-mono text-muted" style="font-size:12px">{{ $module->code_module }}</span></td>
                            <td><a href="{{ route('web.modules.show', $module) }}" class="table-link" style="font-size:13px">{{ $module->libelle }}</a></td>
                            <td>
                                @if($module->regional)
                                    <span class="badge badge-amber">Oui</span>
                                @else <span class="text-muted">—</span> @endif
                            </td>
                            <td><a href="{{ route('web.modules.show', $module) }}" class="btn btn-outline btn-sm">Voir</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding:32px">
                    <div class="empty-icon">📚</div>
                    <div class="empty-title" style="font-size:14px">Aucun module</div>
                </div>
            @endif
        </div>
    </div>
@endsection
