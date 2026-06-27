@extends('layouts.app')

@section('title', $espace->libelle)
@section('breadcrumb')
    <a href="{{ route('web.espaces.index') }}">Espaces</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $espace->libelle }}</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('web.espaces.edit', $espace) }}" class="btn btn-outline">Éditer</a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $espace->libelle }}</h1>
            <p class="page-subtitle">
                Espace #{{ $espace->id }}
                @if($espace->pole) · {{ $espace->pole->libelle }} @endif
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.espaces.edit', $espace) }}" class="btn btn-outline">Éditer</a>
            <form method="POST" action="{{ route('web.espaces.destroy', $espace) }}"
                  onsubmit="return confirm('Supprimer cet espace ?')">
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
                    <div class="detail-field-label">Libellé</div>
                    <div class="detail-field-value">{{ $espace->libelle }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Pôle</div>
                    <div class="detail-field-value">
                        @if($espace->pole)
                            <a href="{{ route('web.poles.show', $espace->pole) }}" class="table-link">
                                {{ $espace->pole->libelle }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Capacité</div>
                    <div class="detail-field-value">
                        @if($espace->capacite !== null)
                            <strong>{{ $espace->capacite }}</strong> places
                        @else
                            <span class="text-muted">Illimitée</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Séances --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Séances ({{ $espace->seances?->count() ?? 0 }})</span>
        </div>
        @if($espace->seances && $espace->seances->isNotEmpty())
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Module / Groupe</th>
                    <th>Créneau</th>
                    <th>Type</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($espace->seances->sortByDesc('date')->take(20) as $seance)
                    @php $typeC = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red']; @endphp
                    <tr>
                        <td><a href="{{ route('web.seances.show', $seance) }}" class="table-link">{{ $seance->date?->format('d/m/Y') }}</a></td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $seance->affectation?->module?->libelle ?? '—' }}</div>
                            <div class="text-muted text-sm">{{ $seance->affectation?->groupe?->code ?? '' }}</div>
                        </td>
                        <td class="text-muted">{{ $seance->timeRange?->start_time }} – {{ $seance->timeRange?->end_time }}</td>
                        <td><span class="badge badge-{{ $typeC[$seance->type] ?? 'gray' }}">{{ strtoupper($seance->type) }}</span></td>
                        <td><a href="{{ route('web.seances.show', $seance) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state" style="padding:40px">
                <div class="empty-icon">📅</div>
                <div class="empty-title">Aucune séance planifiée</div>
                <div class="empty-sub">Aucune séance n'a encore lieu dans cet espace.</div>
            </div>
        @endif
    </div>
@endsection
