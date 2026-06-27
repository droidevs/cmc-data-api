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
                        <label class="form-label">Séance <span class="req">*</span></label>
                        <select name="seance_id" class="form-control" required>
                            <option value="">— Choisir une séance —</option>
                            @foreach($seances as $seance)
                                <option value="{{ $seance->id }}"
                                    {{ old('seance_id', request('seance_id')) == $seance->id ? 'selected' : '' }}>
                                    {{ $seance->date?->format('d/m/Y') }}
                                    — {{ $seance->affectation?->module?->libelle ?? 'Module ?' }}
                                    @if($seance->affectation?->groupe) ({{ $seance->affectation->groupe->code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('seance_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stagiaire <span class="req">*</span></label>
                        <select name="stagiaire_cef" class="form-control" required>
                            <option value="">— Choisir un stagiaire —</option>
                            @foreach($stagiaires as $stagiaire)
                                <option value="{{ $stagiaire->cef }}"
                                    {{ old('stagiaire_cef') === $stagiaire->cef ? 'selected' : '' }}>
                                    {{ $stagiaire->nom }} {{ $stagiaire->prenom }} ({{ $stagiaire->cef }})
                                </option>
                            @endforeach
                        </select>
                        @error('stagiaire_cef')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <option value="">— Non défini —</option>
                            <option value="cc"   {{ old('type') === 'cc'   ? 'selected' : '' }}>CC — Contrôle continu</option>
                            <option value="efm"  {{ old('type') === 'efm'  ? 'selected' : '' }}>EFM — Épreuve de fin de module</option>
                            <option value="tp"   {{ old('type') === 'tp'   ? 'selected' : '' }}>TP — Travaux pratiques</option>
                            <option value="th"   {{ old('type') === 'th'   ? 'selected' : '' }}>TH — Travaux d'heures</option>
                            <option value="syn"  {{ old('type') === 'syn'  ? 'selected' : '' }}>SYN — Synchrone</option>
                            <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>Examen</option>
                        </select>
                        @error('type')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Note /20</label>
                        <input type="number" name="valeur" class="form-control"
                               step="0.01" min="0" max="20"
                               placeholder="ex. 14.50" value="{{ old('valeur') }}">
                        @error('valeur')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Décision</label>
                        <select name="decision" class="form-control">
                            <option value="">— Non définie —</option>
                            <option value="Admis"      {{ old('decision') === 'Admis'      ? 'selected' : '' }}>Admis</option>
                            <option value="Redoublant" {{ old('decision') === 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                            <option value="Abandon"    {{ old('decision') === 'Abandon'    ? 'selected' : '' }}>Abandon</option>
                            <option value="Rattrapage" {{ old('decision') === 'Rattrapage' ? 'selected' : '' }}>Rattrapage</option>
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
