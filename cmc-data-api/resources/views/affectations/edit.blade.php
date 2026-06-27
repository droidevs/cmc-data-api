@extends('layouts.app')

@section('title', 'Éditer affectation #' . $affectation->id)
@section('breadcrumb')
    <a href="{{ route('web.affectations.index') }}">Affectations</a>
    <span class="topbar-sep">/</span>
    <a href="{{ route('web.affectations.show', $affectation) }}">#{{ $affectation->id }}</a>
    <span class="topbar-sep">/</span>
    <span class="current">Éditer</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Éditer l'affectation #{{ $affectation->id }}</h1>
            <p class="page-subtitle">
                {{ $affectation->module?->libelle ?? $affectation->module_code }}
                @if($affectation->groupe) · {{ $affectation->groupe->code }} @endif
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Modifier</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.affectations.update', $affectation) }}">
                @csrf @method('PATCH')
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Groupe <span class="req">*</span></label>
                        <select name="groupe_id" class="form-control" required>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}"
                                    {{ old('groupe_id', $affectation->groupe_id) == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->code }}
                                    @if($groupe->annee?->filiere) ({{ $groupe->annee->filiere->code_filiere }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('groupe_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Module <span class="req">*</span></label>
                        <select name="module_code" class="form-control" required>
                            @foreach($modules as $module)
                                <option value="{{ $module->code_module }}"
                                    {{ old('module_code', $affectation->module_code) === $module->code_module ? 'selected' : '' }}>
                                    {{ $module->code_module }} – {{ $module->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('module_code')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Formateur présentiel</label>
                        <select name="formateur_mle" class="form-control">
                            <option value="">— Non affecté —</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}"
                                    {{ old('formateur_mle', $affectation->formateur_mle) === $formateur->mle ? 'selected' : '' }}>
                                    {{ $formateur->nom_prenom }} ({{ $formateur->mle }})
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_mle')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Formateur synchrone</label>
                        <select name="formateur_mle_syn" class="form-control">
                            <option value="">— Non affecté —</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}"
                                    {{ old('formateur_mle_syn', $affectation->formateur_mle_syn) === $formateur->mle ? 'selected' : '' }}>
                                    {{ $formateur->nom_prenom }} ({{ $formateur->mle }})
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_mle_syn')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mode</label>
                        <select name="mode" class="form-control">
                            <option value="">— Non défini —</option>
                            <option value="Résidentiel" {{ old('mode', $affectation->mode) === 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                            <option value="Alternance"  {{ old('mode', $affectation->mode) === 'Alternance'  ? 'selected' : '' }}>Alternance</option>
                        </select>
                        @error('mode')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">{{-- spacer --}}</div>

                    <div class="form-group">
                        <label class="form-label">MH Affectée Présentiel</label>
                        <input type="number" name="mh_affecte" class="form-control"
                               step="0.01" min="0" max="9999.99"
                               value="{{ old('mh_affecte', $affectation->mh_affecte) }}">
                        @error('mh_affecte')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">MH Affectée Synchrone</label>
                        <input type="number" name="mh_affecte_syn" class="form-control"
                               step="0.01" min="0" max="9999.99"
                               value="{{ old('mh_affecte_syn', $affectation->mh_affecte_syn) }}">
                        @error('mh_affecte_syn')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('web.affectations.show', $affectation) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
