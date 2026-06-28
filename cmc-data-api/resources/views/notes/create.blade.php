@extends('layouts.app')

@section('title', 'Saisir une note')
@section('breadcrumb')
    <a href="{{ route('web.notes.index') }}">Notes</a>
    <span class="topbar-sep">/</span>
    <span class="current">Nouvelle</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Saisir une note</h1>
            <p class="page-subtitle">Associer une note à un stagiaire et une séance</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Formulaire</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.notes.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label" for="note-seance">Séance <span class="req">*</span></label>
                        <select id="note-seance" name="seance_id" class="form-control" required>
                            <option value="">— Choisir une séance —</option>
                            @foreach($seances as $seance)
                                <option value="{{ $seance->id }}"
                                    @selected(old('seance_id', request('seance_id')) == $seance->id)>
                                    {{ $seance->date?->format('d/m/Y') }}
                                    — {{ $seance->affectation?->module?->libelle ?? 'Module ?' }}
                                    @if($seance->affectation?->groupe) ({{ $seance->affectation->groupe->code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('seance_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="note-stagiaire">Stagiaire <span class="req">*</span></label>
                        <select id="note-stagiaire" name="stagiaire_cef" class="form-control" required>
                            <option value="">— Choisir un stagiaire —</option>
                            @foreach($stagiaires as $stagiaire)
                                <option value="{{ $stagiaire->cef }}"
                                    @selected(old('stagiaire_cef') === $stagiaire->cef)>
                                    {{ $stagiaire->nom }} {{ $stagiaire->prenom }} ({{ $stagiaire->cef }})
                                </option>
                            @endforeach
                        </select>
                        @error('stagiaire_cef')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="note-type">Type</label>
                        <select id="note-type" name="type" class="form-control">
                            <option value="">— Non défini —</option>
                            <option value="cc"   @selected(old('type') === 'cc')>CC — Contrôle continu</option>
                            <option value="efm"  @selected(old('type') === 'efm')>EFM — Épreuve de fin de module</option>
                            <option value="tp"   @selected(old('type') === 'tp')>TP — Travaux pratiques</option>
                            <option value="th"   @selected(old('type') === 'th')>TH — Travaux d'heures</option>
                            <option value="syn"  @selected(old('type') === 'syn')>SYN — Synchrone</option>
                            <option value="exam" @selected(old('type') === 'exam')>Examen</option>
                        </select>
                        @error('type')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="note-valeur">Note /20</label>
                        <input id="note-valeur" type="number" name="valeur" class="form-control"
                               step="0.01" min="0" max="20"
                               placeholder="ex. 14.50" value="{{ old('valeur') }}">
                        @error('valeur')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="note-decision">Décision</label>
                        <select id="note-decision" name="decision" class="form-control">
                            <option value="">— Non définie —</option>
                            <option value="Admis"      @selected(old('decision') === 'Admis')>Admis</option>
                            <option value="Redoublant" @selected(old('decision') === 'Redoublant')>Redoublant</option>
                            <option value="Abandon"    @selected(old('decision') === 'Abandon')>Abandon</option>
                            <option value="Rattrapage" @selected(old('decision') === 'Rattrapage')>Rattrapage</option>
                        </select>
                        @error('decision')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer la note</button>
                    <a href="{{ route('web.notes.index') }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
