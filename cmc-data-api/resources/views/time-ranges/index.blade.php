@extends('layouts.app')

@section('title', 'Créneaux horaires')
@section('breadcrumb')<span class="current">Créneaux horaires</span>@endsection

@section('topbar-actions')
    <a href="{{ route('web.time-ranges.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nouveau créneau
    </a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Créneaux horaires</h1>
            <p class="page-subtitle">{{ $items->total() }} créneau(x) enregistré(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.time-ranges.create') }}" class="btn btn-primary">+ Nouveau créneau</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.time-ranges.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Couvre l'heure</label>
                <input type="time" name="covers" class="filter-input" value="{{ request('covers') }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.time-ranges.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🕐</div>
                <div class="empty-title">Aucun créneau trouvé</div>
                <div class="empty-sub">Créez un nouveau créneau horaire.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Séances</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $timeRange)
                    <tr>
                        <td>
                            <a href="{{ route('web.time-ranges.show', $timeRange) }}" class="table-link font-mono">
                                {{ $timeRange->start_time }}
                            </a>
                        </td>
                        <td class="font-mono">{{ $timeRange->end_time }}</td>
                        <td>
                            <span class="badge badge-gray">{{ $timeRange->seances_count ?? $timeRange->seances?->count() ?? 0 }}</span>
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.time-ranges.show', $timeRange) }}" class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.time-ranges.edit', $timeRange) }}" class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination-wrap">
                <div class="pagination-info">{{ $items->firstItem() }}–{{ $items->lastItem() }} sur {{ $items->total() }}</div>
                <div class="pagination-links">{{ $items->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
@endsection
