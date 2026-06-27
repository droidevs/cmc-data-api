@extends('layouts.app')

@section('title', 'Nouvel espace')
@section('breadcrumb')
    <a href="{{ route('web.espaces.index') }}">Espaces</a>
    <span class="topbar-sep">/</span>
    <span class="current">Nouveau</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Nouvel espace</h1>
            <p class="page-subtitle">Créer une salle, un laboratoire ou un atelier</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Formulaire</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.espaces.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Libellé <span class="req">*</span></label>
                        <input type="text" name="libelle" class="form-control" required
                               placeholder="ex. Salle B12" value="{{ old('libelle') }}">
                        @error('libelle')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pôle <span class="req">*</span></label>
                        <select name="pole_id" class="form-control" required>
                            <option value="">— Choisir un pôle —</option>
                            @foreach($poles as $pole)
                                <option value="{{ $pole->id }}" {{ old('pole_id') == $pole->id ? 'selected' : '' }}>
                                    {{ $pole->libelle }}
                                </option>
                            @endforeach
                        </select>
                        @error('pole_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Capacité</label>
                        <input type="number" name="capacite" class="form-control" min="0" max="1000"
                               placeholder="Laisser vide = illimitée" value="{{ old('capacite') }}">
                        @error('capacite')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Créer l'espace</button>
                    <a href="{{ route('web.espaces.index') }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
