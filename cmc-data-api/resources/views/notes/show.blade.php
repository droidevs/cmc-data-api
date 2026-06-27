@extends('layouts.app')

@section('title', 'Note #' . $note->id)
@section('breadcrumb')
    <a href="{{ route('web.notes.index') }}">Notes</a>
    <span class="topbar-sep">/</span>
    <span class="current">#{{ $note->id }}</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('web.notes.edit', $note) }}" class="btn btn-outline">Éditer</a>
@endsection

@section('content')
    @php
        $typeC = ['cc'=>'amber','efm'=>'navy','exam'=>'red','tp'=>'indigo','th'=>'gray','syn'=>'green'];
        $decC  = ['Admis'=>'green','Redoublant'=>'amber','Abandon'=>'red','Rattrapage'=>'indigo'];
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">
                Note —
                {{ $note->stagiaire ? $note->stagiaire->nom.' '.$note->stagiaire->prenom : $note->stagiaire_cef }}
            </h1>
            <p class="page-subtitle">
                Note #{{ $note->id }}
                @if($note->seance?->affectation?->module) · {{ $note->seance->affectation->module->libelle }} @endif
            </p>
        </div>
        <div class="page-header-actions">
            @if($note->type)
                <span class="badge badge-{{ $typeC[$note->type] ?? 'gray' }}" style="font-size:13px;padding:6px 14px">
                    {{ strtoupper($note->type) }}
                </span>
            @endif
            <a href="{{ route('web.notes.edit', $note) }}" class="btn btn-outline">Éditer</a>
            <form method="POST" action="{{ route('web.notes.destroy', $note) }}"
                  onsubmit="return confirm('Supprimer cette note ?')">
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
                    <div class="detail-field-label">Stagiaire</div>
                    <div class="detail-field-value">
                        @if($note->stagiaire)
                            <a href="{{ route('web.stagiaires.show', $note->stagiaire) }}" class="table-link">
                                {{ $note->stagiaire->nom }} {{ $note->stagiaire->prenom }}
                            </a>
                            <div class="text-muted text-sm font-mono">{{ $note->stagiaire_cef }}</div>
                        @else
                            <span class="font-mono">{{ $note->stagiaire_cef }}</span>
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Note /20</div>
                    <div class="detail-field-value">
                        @if($note->valeur !== null)
                            <span style="font-weight:700;font-size:18px;color:{{ $note->valeur >= 10 ? 'var(--green)' : 'var(--red)' }}">
                                {{ number_format($note->valeur, 2) }}
                            </span>
                        @else
                            <span class="text-muted">Non saisie</span>
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Décision</div>
                    <div class="detail-field-value">
                        @if($note->decision)
                            <span class="badge badge-{{ $decC[$note->decision] ?? 'gray' }}">{{ $note->decision }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Séance</div>
                    <div class="detail-field-value">
                        @if($note->seance)
                            <a href="{{ route('web.seances.show', $note->seance) }}" class="table-link">
                                {{ $note->seance->date?->format('d/m/Y') }}
                            </a>
                        @else — @endif
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Module</div>
                    <div class="detail-field-value">
                        {{ $note->seance?->affectation?->module?->libelle ?? '—' }}
                    </div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Groupe</div>
                    <div class="detail-field-value">
                        @if($note->seance?->affectation?->groupe)
                            <a href="{{ route('web.groupes.show', $note->seance->affectation->groupe) }}" class="table-link">
                                {{ $note->seance->affectation->groupe->code }}
                            </a>
                        @else — @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
