@extends('layouts.app')

@section('title', 'Nouveau créneau horaire')
@section('breadcrumb')
    <a href="{{ route('web.time-ranges.index') }}">Créneaux horaires</a>
    <span class="topbar-sep">/</span>
    <span class="current">Nouveau</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Nouveau créneau horaire</h1>
            <p class="page-subtitle">Définir une plage horaire utilisable par les séances</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Formulaire</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.time-ranges.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Heure de début <span class="req">*</span></label>
                        <input type="time" name="start_time" class="form-control" required
                               value="{{ old('start_time') }}">
                        @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Heure de fin <span class="req">*</span></label>
                        <input type="time" name="end_time" class="form-control" required
                               value="{{ old('end_time') }}">
                        @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Créer le créneau</button>
                    <a href="{{ route('web.time-ranges.index') }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
