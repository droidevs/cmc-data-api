@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Vue d'ensemble du centre — {{ now()->format('d F Y') }}</p>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="stats-grid">
        <div class="stat-card" style="--stat-color: #4F46E5">
            <div class="stat-label">Pôles actifs</div>
            <div class="stat-value">{{ $stats['poles'] }}</div>
            <div class="stat-sub">Unités organisationnelles</div>
        </div>
        <div class="stat-card" style="--stat-color: #10B981">
            <div class="stat-label">Formateurs</div>
            <div class="stat-value">{{ $stats['formateurs'] }}</div>
            <div class="stat-sub">Corps enseignant</div>
        </div>
        <div class="stat-card" style="--stat-color: #F59E0B">
            <div class="stat-label">Stagiaires actifs</div>
            <div class="stat-value">{{ $stats['stagiaires'] }}</div>
            <div class="stat-sub">En formation</div>
        </div>
        <div class="stat-card" style="--stat-color: #6366F1">
            <div class="stat-label">Groupes</div>
            <div class="stat-value">{{ $stats['groupes'] }}</div>
            <div class="stat-sub">Classes / promotions</div>
        </div>
        <div class="stat-card" style="--stat-color: #EC4899">
            <div class="stat-label">Affectations</div>
            <div class="stat-value">{{ $stats['affectations'] }}</div>
            <div class="stat-sub">Module × Formateur</div>
        </div>
        <div class="stat-card" style="--stat-color: #14B8A6">
            <div class="stat-label">Séances</div>
            <div class="stat-value">{{ $stats['seances'] }}</div>
            <div class="stat-sub">Sessions planifiées</div>
        </div>
        <div class="stat-card" style="--stat-color: #8B5CF6">
            <div class="stat-label">Notes saisies</div>
            <div class="stat-value">{{ $stats['notes'] }}</div>
            <div class="stat-sub">
                @if($stats['notes_manquantes'] > 0)
                    <span style="color: var(--amber)">{{ $stats['notes_manquantes'] }} manquantes</span>
                @else
                    Toutes renseignées
                @endif
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

        {{-- Recent séances --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Séances récentes</span>
                <a href="{{ route('web.seances.index') }}" class="btn btn-outline btn-sm">Tout voir</a>
            </div>
            @if($recent_seances->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <div class="empty-title">Aucune séance récente</div>
                    <div class="empty-sub">Aucune séance sur les 7 derniers jours</div>
                </div>
            @else
                <table>
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Module / Groupe</th>
                        <th>Créneau</th>
                        <th>Type</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recent_seances as $seance)
                        <tr>
                            <td>
                                <a href="{{ route('web.seances.show', $seance) }}" class="table-link">
                                    {{ $seance->date?->format('d/m/Y') }}
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:600; font-size:13px">{{ $seance->affectation?->module?->libelle ?? '—' }}</div>
                                <div class="text-muted text-sm">{{ $seance->affectation?->groupe?->code ?? '' }}</div>
                            </td>
                            <td class="text-muted">
                                {{ $seance->timeRange?->start_time }} – {{ $seance->timeRange?->end_time }}
                            </td>
                            <td>
                                @php
                                    $typeColors = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red'];
                                    $color = $typeColors[$seance->type] ?? 'gray';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ strtoupper($seance->type) }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Pôles overview --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Pôles — Répartition</span>
                <a href="{{ route('web.poles.index') }}" class="btn btn-outline btn-sm">Voir tout</a>
            </div>
            <div class="card-body" style="padding:0">
                @foreach($poles_with_counts as $pole)
                    <div style="display:flex; align-items:center; padding:14px 22px; border-bottom:1px solid var(--slate-100); gap:14px;">
                        <div style="flex:1">
                            <a href="{{ route('web.poles.show', $pole) }}" class="table-link" style="font-size:14px">
                                {{ $pole->libelle }}
                            </a>
                        </div>
                        <div style="display:flex; gap:8px; flex-shrink:0">
                            <span class="badge badge-navy">{{ $pole->formateurs_count }} form.</span>
                            <span class="badge badge-indigo">{{ $pole->filieres_count }} fil.</span>
                            <span class="badge badge-gray">{{ $pole->espaces_count }} esp.</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

@endsection
