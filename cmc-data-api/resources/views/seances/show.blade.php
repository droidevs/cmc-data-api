@extends('layouts.app')

@section('title', 'Séance du ' . ($seance->date?->format('d/m/Y') ?? '#'.$seance->id))
@section('breadcrumb')
    <a href="{{ route('web.seances.index') }}">Séances</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $seance->date?->format('d/m/Y') ?? '#'.$seance->id }}</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('web.seances.edit', $seance) }}" class="btn btn-outline">Éditer</a>
@endsection

@section('content')
    @php $typeC = ['cours'=>'indigo','cc'=>'amber','efm'=>'navy']; @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">
                Séance —
                {{ $seance->affectation?->module?->libelle ?? 'Module inconnu' }}
            </h1>
            <p class="page-subtitle">
                {{ $seance->date?->format('d/m/Y') }}
                @if($seance->timeRange) · {{ $seance->timeRange->start_time }} – {{ $seance->timeRange->end_time }} @endif
                @if($seance->affectation?->groupe) · Groupe {{ $seance->affectation->groupe->code }} @endif
            </p>
        </div>
        <div class="page-header-actions">
            <span class="badge badge-{{ $typeC[$seance->type?->value] ?? 'gray' }}"
                  style="font-size:13px;padding:6px 14px">{{ strtoupper($seance->type?->label() ?? '?') }}
            </span>
            <a href="{{ route('web.seances.edit', $seance) }}" class="btn btn-outline">Éditer</a>
            <form method="POST" action="{{ route('web.seances.destroy', $seance) }}"
                  onsubmit="return confirm('Supprimer cette séance et ses notes ?')">
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
                    <div class="detail-field-label">Date</div>
                    <div class="detail-field-value">{{ $seance->date?->format('d/m/Y') }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Type</div>
                    <div class="detail-field-value">
                        <span class="badge badge-{{ $typeC[$seance->type?->value] ?? 'gray' }}"
                              style="font-size:13px;padding:6px 14px">{{ strtoupper($seance->type?->label() ?? '?') }}
                        </span>
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Créneau horaire</div>
                    <div class="detail-field-value">
                        {{ $seance->timeRange?->start_time }} – {{ $seance->timeRange?->end_time }}
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Module</div>
                    <div class="detail-field-value">
                        @if($seance->affectation?->module)
                            <a href="{{ route('web.modules.show', $seance->affectation->module) }}" class="table-link">
                                {{ $seance->affectation->module->libelle }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Groupe</div>
                    <div class="detail-field-value">
                        @if($seance->affectation?->groupe)
                            <a href="{{ route('web.groupes.show', $seance->affectation->groupe) }}" class="table-link">
                                {{ $seance->affectation->groupe->code }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Formateur</div>
                    <div class="detail-field-value">
                        @if($seance->affectation?->formateur)
                            <a href="{{ route('web.formateurs.show', $seance->affectation->formateur) }}" class="table-link">
                                {{ $seance->affectation->formateur->nom_prenom }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Espace</div>
                    <div class="detail-field-value">
                        {{ $seance->espace?->libelle ?? 'En ligne / non défini' }}
                        @if($seance->espace?->pole)
                            <div class="text-muted text-sm">{{ $seance->espace->pole->libelle }}</div>
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Affectation</div>
                    <div class="detail-field-value">
                        <a href="{{ route('web.affectations.show', $seance->affectation_id) }}"
                           class="table-link">#{{ $seance->affectation_id }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Notes ({{ $seance->notes?->count() ?? 0 }})</span>
            <a href="{{ route('web.notes.create', ['seance_id' => $seance->id]) }}"
               class="btn btn-primary btn-sm">+ Saisir note</a>
        </div>
        @if($seance->notes && $seance->notes->isNotEmpty())
            <table>
                <thead>
                <tr>
                    <th>CEF stagiaire</th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Note /20</th>
                    <th>Décision</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($seance->notes as $note)
                    <tr>
                        <td class="font-mono text-muted" style="font-size:11px">{{ $note->stagiaire_cef }}</td>
                        <td>
                            @if($note->stagiaire)
                                <a href="{{ route('web.stagiaires.show', $note->stagiaire) }}" class="table-link"
                                   style="font-size:13px">
                                    {{ $note->stagiaire->nom }} {{ $note->stagiaire->prenom }}
                                </a>
                            @else {{ $note->stagiaire_cef }} @endif
                        </td>
                        <td>
                            @php $tC=['cours' => 'indigo','cc'=>'amber','efm'=>'navy','th'=>'gray','syn'=>'green']; @endphp
                            <span class="badge badge-{{ $typeC[$seance->type?->value] ?? 'gray' }}"
                                  style="font-size:13px;padding:6px 14px">{{ strtoupper($seance->type?->label() ?? '?') }}
                            </span>
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
                                @php $dC=['Admis'=>'green','Redoublant'=>'amber','Abandon'=>'red','Rattrapage'=>'indigo']; @endphp
                                <span class="badge badge-{{ $dC[$note->decision] ?? 'gray' }}">{{ $note->decision }}</span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            <a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state" style="padding:40px">
                <div class="empty-icon">📝</div>
                <div class="empty-title">Aucune note saisie</div>
                <div class="empty-sub">Saisissez les notes pour cette séance.</div>
            </div>
        @endif
    </div>
@endsection
