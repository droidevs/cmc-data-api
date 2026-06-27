@extends('layouts.app')

@section('title', 'Éditer le créneau #' . $timeRange->id)
@section('breadcrumb')
    <a href="{{ route('web.time-ranges.index') }}">Créneaux horaires</a>
    <span class="topbar-sep">/</span>
    <a href="{{ route('web.time-ranges.show', $timeRange) }}">#{{ $timeRange->id }}</a>
    <span class="topbar-sep">/</span>
    <span class="current">Éditer</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Éditer le créneau horaire</h1>
            <p class="page-subtitle">{{ $timeRange->start_time }} – {{ $timeRange->end_time }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Modifier</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('web.time-ranges.update', $timeRange) }}">
                @csrf @method('PATCH')
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Heure de début <span class="req">*</span></label>
                        <input type="time" name="start_time" class="form-control" required
                               value="{{ old('start_time', optional($timeRange->start_time)->format('H:i')) }}">
                        @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Heure de fin <span class="req">*</span></label>
                        <input type="time" name="end_time" class="form-control" required
                               value="{{ old('end_time', optional($timeRange->end_time)->format('H:i')) }}">
                        @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('web.time-ranges.show', $timeRange) }}" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
