@extends('layouts.app')

@section('title', 'Nouvelle affectation')
@section('breadcrumb')
    <a href="{{ route('web.affectations.index') }}">Affectations</a>
    <span class="topbar-sep">/</span>
    <span class="current">Nouvelle</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Nouvelle affectation</h1>
            <p class="page-subtitle">Lier un groupe à un module et un formateur</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Formulaire</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.affectations.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Groupe <span class="req">*</span></label>
                        <select name="groupe_id" class="form-control" required>
                            <option value="">— Choisir un groupe —</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}"
                                    {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>
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
                            <option value="">— Choisir un module —</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->code_module }}"
                                    {{ old('module_code') === $module->code_module ? 'selected' : '' }}>
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
                                    {{ old('formateur_mle') === $formateur->mle ? 'selected' : '' }}>
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
                                    {{ old('formateur_mle_syn') === $formateur->mle ? 'selected' : '' }}>
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
                            <option value="Résidentiel" {{ old('mode') === 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                            <option value="Alternance"  {{ old('mode') === 'Alternance'  ? 'selected' : '' }}>Alternance</option>
                        </select>
                        @error('mode')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        {{-- spacer --}}
                    </div>

                    <div class="form-group">
                        <label class="form-label">MH Affectée Présentiel</label>
                        <input type="number" name="mh_affecte" class="form-control"
                               step="0.01" min="0" max="9999.99"
                               placeholder="ex. 52.00" value="{{ old('mh_affecte') }}">
                        @error('mh_affecte')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">MH Affectée Synchrone</label>
                        <input type="number" name="mh_affecte_syn" class="form-control"
                               step="0.01" min="0" max="9999.99"
                               placeholder="ex. 26.00" value="{{ old('mh_affecte_syn') }}">
                        @error('mh_affecte_syn')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Créer l'affectation</button>
                    <a href="{{ route('web.affectations.index') }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
