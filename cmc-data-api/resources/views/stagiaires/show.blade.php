@extends('layouts.app')

@section('title', $stagiaire->nom_complet)
@section('breadcrumb')
    <a href="{{ route('web.stagiaires.index') }}">Stagiaires</a>
    <span class="topbar-sep">/</span>
    <span class="current">{{ $stagiaire->nom_complet }}</span>
@endsection

@section('content')
    @php
        $typeColors = ['cc' => 'amber', 'efm' => 'navy', 'exam' => 'red', 'tp' => 'indigo', 'th' => 'gray', 'syn' => 'green'];
        $decColors  = ['Admis' => 'green', 'Redoublant' => 'amber', 'Abandon' => 'red', 'Rattrapage' => 'indigo'];
    @endphp

    <div class="page-header">
        <div style="display:flex;align-items:center;gap:16px">
            <div class="avatar" style="width:52px;height:52px;font-size:18px;background:{{ $stagiaire->genre === 'F' ? 'var(--indigo-soft)' : '#E3E8F5' }}">
                {{ mb_strtoupper(mb_substr($stagiaire->nom, 0, 1).mb_substr($stagiaire->prenom, 0, 1)) }}
            </div>
            <div>
                <h1 class="page-title">{{ $stagiaire->nom }} {{ $stagiaire->prenom }}</h1>
                <p class="page-subtitle">
                    CEF : <span class="font-mono">{{ $stagiaire->cef }}</span>
                    @if($stagiaire->groupe) · Groupe : {{ $stagiaire->groupe->code }} @endif
                </p>
            </div>
        </div>
        <div class="page-header-actions">
            <x-badge :color="$stagiaire->actif ? 'green' : 'red'" style="font-size:13px;padding:6px 14px">
                {{ $stagiaire->actif ? 'Actif' : 'Inactif' }}
            </x-badge>
        </div>
    </div>

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Informations personnelles</span></div>
        <div class="card-body">
            <div class="detail-grid thirds">
                <div class="detail-field">
                    <div class="detail-field-label">CNI</div>
                    <div class="detail-field-value mono">{{ $stagiaire->cni ?? '—' }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Date de naissance</div>
                    <div class="detail-field-value">{{ $stagiaire->date_naissance?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Genre</div>
                    <div class="detail-field-value">
                        <x-badge :color="$stagiaire->genre === 'F' ? 'indigo' : 'navy'">
                            {{ $stagiaire->genre === 'F' ? 'Femme' : 'Homme' }}
                        </x-badge>
                    </div>
                </div>
                @if($stagiaire->nom_arabe)
                    <div class="detail-field">
                        <div class="detail-field-label">Nom en arabe</div>
                        <div class="detail-field-value" dir="rtl">{{ $stagiaire->nom_arabe }} {{ $stagiaire->prenom_arabe }}</div>
                    </div>
                @endif
                <div class="detail-field">
                    <div class="detail-field-label">Téléphone</div>
                    <div class="detail-field-value">{{ $stagiaire->telephone ?? '—' }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-field-label">Niveau scolaire</div>
                    <div class="detail-field-value">{{ $stagiaire->niveau_scolaire ?? '—' }}</div>
                </div>
                @if($stagiaire->adresse)
                    <div class="detail-field" style="grid-column:1/-1">
                        <div class="detail-field-label">Adresse</div>
                        <div class="detail-field-value">{{ $stagiaire->adresse }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Groupe / Filière --}}
    @if($stagiaire->groupe)
        <div class="card" style="margin-bottom:24px">
            <div class="card-header"><span class="card-title">Parcours de formation</span></div>
            <div class="card-body">
                <div class="detail-grid thirds">
                    <div class="detail-field">
                        <div class="detail-field-label">Groupe</div>
                        <div class="detail-field-value">
                            <a href="{{ route('web.groupes.show', $stagiaire->groupe) }}" class="table-link">
                                {{ $stagiaire->groupe->code }}
                            </a>
                        </div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Effectif groupe</div>
                        <div class="detail-field-value">{{ $stagiaire->groupe->effectif ?? '—' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field-label">Mode</div>
                        <div class="detail-field-value">
                            @if($stagiaire->groupe->mode)
                                <x-badge :color="$stagiaire->groupe->mode === 'Résidentiel' ? 'indigo' : 'amber'">
                                    {{ $stagiaire->groupe->mode }}
                                </x-badge>
                            @else — @endif
                        </div>
                    </div>
                    @if($stagiaire->groupe?->annee?->filiere)
                        @php $filiere = $stagiaire->groupe->annee->filiere; @endphp
                        <div class="detail-field">
                            <div class="detail-field-label">Filière</div>
                            <div class="detail-field-value">
                                <a href="{{ route('web.filieres.show', $filiere) }}" class="table-link">{{ $filiere->libelle }}</a>
                            </div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field-label">Code filière</div>
                            <div class="detail-field-value mono">{{ $filiere->code_filiere }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field-label">Secteur</div>
                            <div class="detail-field-value">{{ $filiere->secteur ?? '—' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Notes --}}
    @if($stagiaire->notes && $stagiaire->notes->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">Notes ({{ $stagiaire->notes->count() }})</span>
                <a href="{{ route('web.notes.index', ['stagiaire_cef' => $stagiaire->cef]) }}" class="btn btn-outline btn-sm">
                    Toutes les notes
                </a>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Module</th>
                    <th>Type</th>
                    <th>Note /20</th>
                    <th>Décision</th>
                    <th>Date séance</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($stagiaire->notes->take(20) as $note)
                    <tr>
                        <td>{{ $note->seance?->affectation?->module?->libelle ?? '—' }}</td>
                        <td>
                            <x-badge :color="$typeColors[$note->type] ?? 'gray'">{{ strtoupper($note->type ?? '?') }}</x-badge>
                        </td>
                        <td>
                            @if($note->valeur !== null)
                                <div class="score-bar">
                                    <span style="font-weight:700;min-width:36px;color:{{ $note->valeur >= 10 ? 'var(--green)' : 'var(--red)' }}">
                                        {{ number_format($note->valeur, 2) }}
                                    </span>
                                    <div class="score-track">
                                        <div class="score-fill {{ $note->valeur >= 10 ? 'pass' : 'fail' }}"
                                             style="width:{{ min(100, $note->valeur * 5) }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Non saisi</span>
                            @endif
                        </td>
                        <td>
                            @if($note->decision)
                                <x-badge :color="$decColors[$note->decision] ?? 'gray'">{{ $note->decision }}</x-badge>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td class="text-muted">{{ $note->seance?->date?->format('d/m/Y') }}</td>
                        <td><a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
