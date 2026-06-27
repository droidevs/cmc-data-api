@extends('layouts.app')

@section('title', 'Séances')
@section('breadcrumb')<span class="current">Séances</span>@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Séances</h1>
            <p class="page-subtitle">{{ $items->total() }} séance(s) planifiée(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.seances.create') }}" class="btn btn-primary">+ Nouvelle séance</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.seances.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select name="type" class="filter-select">
                    <option value="">Tous types</option>
                    <option value="cours" {{ request('type') === 'cours' ? 'selected' : '' }}>Cours</option>
                    <option value="cc"    {{ request('type') === 'cc'    ? 'selected' : '' }}>CC</option>
                    <option value="efm"   {{ request('type') === 'efm'   ? 'selected' : '' }}>EFM</option>
                    <option value="exam"  {{ request('type') === 'exam'  ? 'selected' : '' }}>Examen</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date début</label>
                <input type="date" name="date_from" class="filter-input"
                       value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Date fin</label>
                <input type="date" name="date_to" class="filter-input"
                       value="{{ request('date_to') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Groupe (ID)</label>
                <input type="number" name="groupe_id" class="filter-input"
                       placeholder="ID groupe" value="{{ request('groupe_id') }}" style="min-width:90px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Formateur (MLE)</label>
                <input type="text" name="formateur_mle" class="filter-input"
                       placeholder="ex. 19307" value="{{ request('formateur_mle') }}" style="min-width:110px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Avec notes</label>
                <select name="has_notes" class="filter-select">
                    <option value="">Tous</option>
                    <option value="1" {{ request('has_notes') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('has_notes') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.seances.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <div class="empty-title">Aucune séance trouvée</div>
                <div class="empty-sub">Modifiez vos filtres ou planifiez une nouvelle séance.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Module / Groupe</th>
                    <th>Type</th>
                    <th>Créneau</th>
                    <th>Espace</th>
                    <th>Notes</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $seance)
                    @php $typeC = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy','exam'=>'red']; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('web.seances.show', $seance) }}" class="table-link">
                                {{ $seance->date?->format('d/m/Y') }}
                            </a>
                            <div class="text-muted text-sm">{{ $seance->date?->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">
                                {{ $seance->affectation?->module?->libelle ?? '—' }}
                            </div>
                            <div class="text-muted text-sm">
                                {{ $seance->affectation?->groupe?->code ?? '' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $typeC[$seance->type] ?? 'gray' }}">
                                {{ strtoupper($seance->type) }}
                            </span>
                        </td>
                        <td class="text-muted">
                            {{ $seance->timeRange?->start_time }}
                            @if($seance->timeRange) – {{ $seance->timeRange->end_time }} @endif
                        </td>
                        <td class="text-muted" style="font-size:12.5px">
                            {{ $seance->espace?->libelle ?? '—' }}
                        </td>
                        <td>
                            @php $nbNotes = $seance->notes?->count() ?? 0; @endphp
                            @if($nbNotes > 0)
                                <a href="{{ route('web.notes.index', ['seance_id' => $seance->id]) }}"
                                   class="badge badge-green">{{ $nbNotes }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.seances.show', $seance) }}"
                               class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.seances.edit', $seance) }}"
                               class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
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
