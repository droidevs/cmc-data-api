@extends('layouts.app')

@section('title', 'Éditer ' . $espace->libelle)
@section('breadcrumb')
    <a href="{{ route('web.espaces.index') }}">Espaces</a>
    <span class="topbar-sep">/</span>
    <a href="{{ route('web.espaces.show', $espace) }}">{{ $espace->libelle }}</a>
    <span class="topbar-sep">/</span>
    <span class="current">Éditer</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Éditer l'espace</h1>
            <p class="page-subtitle">{{ $espace->libelle }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Modifier</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.espaces.update', $espace) }}">
                @csrf @method('PATCH')
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Libellé <span class="req">*</span></label>
                        <input type="text" name="libelle" class="form-control" required
                               value="{{ old('libelle', $espace->libelle) }}">
                        @error('libelle')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pôle <span class="req">*</span></label>
                        <select name="pole_id" class="form-control" required>
                            @foreach($poles as $pole)
                                <option value="{{ $pole->id }}"
                                    {{ old('pole_id', $espace->pole_id) == $pole->id ? 'selected' : '' }}>
                                    {{ $pole->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('pole_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Capacité</label>
                        <input type="number" name="capacite" class="form-control" min="0" max="1000"
                               placeholder="Laisser vide = illimitée" value="{{ old('capacite', $espace->capacite) }}">
                        @error('capacite')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('web.espaces.show', $espace) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
