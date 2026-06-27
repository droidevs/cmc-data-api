@extends('layouts.app')

@section('title', 'Notes')
@section('breadcrumb')<span class="current">Notes</span>@endsection

@section('topbar-actions')
    <a href="{{ route('web.notes.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Saisir une note
    </a>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Notes</h1>
            <p class="page-subtitle">{{ $items->total() }} note(s) enregistrée(s)</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('web.notes.create') }}" class="btn btn-primary">+ Saisir une note</a>
        </div>
    </div>

    <form method="GET" action="{{ route('web.notes.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Stagiaire (CEF)</label>
                <input type="text" name="stagiaire_cef" class="filter-input" placeholder="ex. 123456"
                       value="{{ request('stagiaire_cef') }}" style="min-width:120px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Séance (ID)</label>
                <input type="number" name="seance_id" class="filter-input" placeholder="ID séance"
                       value="{{ request('seance_id') }}" style="min-width:100px">
            </div>
            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select name="type" class="filter-select">
                    <option value="">Tous types</option>
                    <option value="cc"   {{ request('type') === 'cc'   ? 'selected' : '' }}>CC</option>
                    <option value="efm"  {{ request('type') === 'efm'  ? 'selected' : '' }}>EFM</option>
                    <option value="tp"   {{ request('type') === 'tp'   ? 'selected' : '' }}>TP</option>
                    <option value="th"   {{ request('type') === 'th'   ? 'selected' : '' }}>TH</option>
                    <option value="syn"  {{ request('type') === 'syn'  ? 'selected' : '' }}>SYN</option>
                    <option value="exam" {{ request('type') === 'exam' ? 'selected' : '' }}>Examen</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Décision</label>
                <select name="decision" class="filter-select">
                    <option value="">Toutes</option>
                    <option value="Admis"      {{ request('decision') === 'Admis'      ? 'selected' : '' }}>Admis</option>
                    <option value="Redoublant" {{ request('decision') === 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                    <option value="Abandon"    {{ request('decision') === 'Abandon'    ? 'selected' : '' }}>Abandon</option>
                    <option value="Rattrapage" {{ request('decision') === 'Rattrapage' ? 'selected' : '' }}>Rattrapage</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Manquantes</label>
                <select name="missing" class="filter-select">
                    <option value="">Toutes</option>
                    <option value="1" {{ request('missing') === '1' ? 'selected' : '' }}>Oui</option>
                    <option value="0" {{ request('missing') === '0' ? 'selected' : '' }}>Non</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('web.notes.index') }}" class="btn btn-outline">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-wrap">
        @if($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <div class="empty-title">Aucune note trouvée</div>
                <div class="empty-sub">Modifiez vos filtres ou saisissez une nouvelle note.</div>
            </div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Stagiaire</th>
                    <th>Module / Groupe</th>
                    <th>Type</th>
                    <th>Note /20</th>
                    <th>Décision</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $note)
                    @php
                        $typeC = ['cc'=>'amber','efm'=>'navy','exam'=>'red','tp'=>'indigo','th'=>'gray','syn'=>'green'];
                        $decC  = ['Admis'=>'green','Redoublant'=>'amber','Abandon'=>'red','Rattrapage'=>'indigo'];
                    @endphp
                    <tr>
                        <td>
                            @if($note->stagiaire)
                                <a href="{{ route('web.stagiaires.show', $note->stagiaire) }}" class="table-link" style="font-size:13px">
                                    {{ $note->stagiaire->nom }} {{ $note->stagiaire->prenom }}
                                </a>
                                <div class="text-muted text-sm font-mono">{{ $note->stagiaire_cef }}</div>
                            @else
                                <span class="font-mono text-muted" style="font-size:12px">{{ $note->stagiaire_cef }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $note->seance?->affectation?->module?->libelle ?? '—' }}</div>
                            <div class="text-muted text-sm">{{ $note->seance?->affectation?->groupe?->code ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $typeC[$note->type] ?? 'gray' }}">{{ strtoupper($note->type ?? '?') }}</span>
                        </td>
                        <td>
                            @if($note->valeur !== null)
                                <span style="font-weight:700;color:{{ $note->valeur >= 10 ? 'var(--green)' : 'var(--red)' }}">
                                    {{ number_format($note->valeur, 2) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($note->decision)
                                <span class="badge badge-{{ $decC[$note->decision] ?? 'gray' }}">{{ $note->decision }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline btn-sm">Voir</a>
                            <a href="{{ route('web.notes.edit', $note) }}" class="btn btn-outline btn-sm" style="margin-left:4px">Éditer</a>
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
