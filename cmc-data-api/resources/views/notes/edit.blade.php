@extends('layouts.app')

@section('title', 'Éditer la note #' . $note->id)
@section('breadcrumb')
    <a href="{{ route('web.notes.index') }}">Notes</a>
    <span class="topbar-sep">/</span>
    <a href="{{ route('web.notes.show', $note) }}">#{{ $note->id }}</a>
    <span class="topbar-sep">/</span>
    <span class="current">Éditer</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Éditer la note</h1>
            <p class="page-subtitle">
                {{ $note->stagiaire?->nom }} {{ $note->stagiaire?->prenom }}
                @if($note->seance?->affectation?->module) · {{ $note->seance->affectation->module->libelle }} @endif
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Modifier</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.notes.update', $note) }}">
                @csrf @method('PATCH')
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Séance <span class="req">*</span></label>
                        <select name="seance_id" class="form-control" required>
                            @foreach($seances as $seance)
                                <option value="{{ $seance->id }}"
                                    {{ old('seance_id', $note->seance_id) == $seance->id ? 'selected' : '' }}>
                                    {{ $seance->date?->format('d/m/Y') }}
                                    — {{ $seance->affectation?->module?->libelle ?? 'Module ?' }}
                                </option>
                            @endforeach
                        </select>
                        @error('seance_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stagiaire <span class="req">*</span></label>
                        <select name="stagiaire_cef" class="form-control" required>
                            @foreach($stagiaires as $stagiaire)
                                <option value="{{ $stagiaire->cef }}"
                                    {{ old('stagiaire_cef', $note->stagiaire_cef) === $stagiaire->cef ? 'selected' : '' }}>
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
                            <option value="cc"   {{ old('type', $note->type) === 'cc'   ? 'selected' : '' }}>CC — Contrôle continu</option>
                            <option value="efm"  {{ old('type', $note->type) === 'efm'  ? 'selected' : '' }}>EFM — Épreuve de fin de module</option>
                            <option value="tp"   {{ old('type', $note->type) === 'tp'   ? 'selected' : '' }}>TP — Travaux pratiques</option>
                            <option value="th"   {{ old('type', $note->type) === 'th'   ? 'selected' : '' }}>TH — Travaux d'heures</option>
                            <option value="syn"  {{ old('type', $note->type) === 'syn'  ? 'selected' : '' }}>SYN — Synchrone</option>
                            <option value="exam" {{ old('type', $note->type) === 'exam' ? 'selected' : '' }}>Examen</option>
                        </select>
                        @error('type')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Note /20</label>
                        <input type="number" name="valeur" class="form-control"
                               step="0.01" min="0" max="20"
                               value="{{ old('valeur', $note->valeur) }}">
                        @error('valeur')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Décision</label>
                        <select name="decision" class="form-control">
                            <option value="">— Non définie —</option>
                            <option value="Admis"      {{ old('decision', $note->decision) === 'Admis'      ? 'selected' : '' }}>Admis</option>
                            <option value="Redoublant" {{ old('decision', $note->decision) === 'Redoublant' ? 'selected' : '' }}>Redoublant</option>
                            <option value="Abandon"    {{ old('decision', $note->decision) === 'Abandon'    ? 'selected' : '' }}>Abandon</option>
                            <option value="Rattrapage" {{ old('decision', $note->decision) === 'Rattrapage' ? 'selected' : '' }}>Rattrapage</option>
                        </select>
                        @error('decision')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('web.notes.show', $note) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
