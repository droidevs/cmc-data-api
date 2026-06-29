@extends('layouts.app')

@section('title', 'Créneau ' . $timeRange->start_time . '–' . $timeRange->end_time)
@section('breadcrumb')
    <a href="{{ route('web.time-ranges.index') }}">Créneaux horaires</a>
    <span class="topbar-sep">/</span>
    <span class="current">#{{ $timeRange->id }}</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('web.time-ranges.edit', $timeRange) }}" class="btn btn-outline">Éditer</a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $timeRange->start_time }} – {{ $timeRange->end_time }}</h1>
            <p class="page-subtitle">Créneau horaire #{{ $timeRange->id }}</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.time-ranges.edit', $timeRange) }}" class="btn btn-outline">Éditer</a>
            <form method="POST" action="{{ route('web.time-ranges.destroy', $timeRange) }}"
                  onsubmit="return confirm('Supprimer ce créneau horaire ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations</span></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-field">
                    <div class="detail-field-label">Heure de début</div>
                    <div class="detail-field-value mono">{{ $timeRange->start_time }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Heure de fin</div>
                    <div class="detail-field-value mono">{{ $timeRange->end_time }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Séances --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Séances ({{ $timeRange->seances?->count() ?? 0 }})</span>
        </div>
        @if($timeRange->seances && $timeRange->seances->isNotEmpty())
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Module / Groupe</th>
                    <th>Type</th>
                    <th>Espace</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($timeRange->seances->sortByDesc('date')->take(20) as $seance)
                    @php $typeC = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red']; @endphp
                    <tr>
                        <td><a href="{{ route('web.seances.show', $seance) }}" class="table-link">{{ $seance->date?->format('d/m/Y') }}</a></td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $seance->affectation?->module?->libelle ?? '—' }}</div>
                            <div class="text-muted text-sm">{{ $seance->affectation?->groupe?->code ?? '' }}</div>
                        </td>
                        <td><span class="badge badge-{{ $typeC[$seance->type?->value] ?? 'gray' }}">{{ strtoupper($seance->type?->label()) }}</span></td>
                        <td class="text-muted" style="font-size:12.5px">{{ $seance->espace?->libelle ?? '—' }}</td>
                        <td><a href="{{ route('web.seances.show', $seance) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state" style="padding:40px">
                <div class="empty-icon">📅</div>
                <div class="empty-title">Aucune séance planifiée</div>
                <div class="empty-sub">Aucune séance n'utilise ce créneau pour le moment.</div>
            </div>
        @endif
    </div>
@endsection
