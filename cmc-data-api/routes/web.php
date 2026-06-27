<?php

use App\Http\Controllers\Web\AffectationWebController;
use App\Http\Controllers\Web\AnneeWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EspaceWebController;
use App\Http\Controllers\Web\FiliereWebController;
use App\Http\Controllers\Web\FormateurWebController;
use App\Http\Controllers\Web\GroupeWebController;
use App\Http\Controllers\Web\ModuleWebController;
use App\Http\Controllers\Web\NiveauWebController;
use App\Http\Controllers\Web\NoteWebController;
use App\Http\Controllers\Web\PoleWebController;
use App\Http\Controllers\Web\SeanceWebController;
use App\Http\Controllers\Web\StagiaireWebController;
use App\Http\Controllers\Web\TimeRangeWebController;
use App\Http\Controllers\Web\TypeFormationWebController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->name('web.')->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    // ─── Read-only resources (imported from Excel) ────────────────────────
    Route::resource('poles',          PoleWebController::class)         ->only(['index', 'show'])->names('poles');
    Route::resource('formateurs',     FormateurWebController::class)    ->only(['index', 'show'])->names('formateurs');
    Route::resource('stagiaires',     StagiaireWebController::class)    ->only(['index', 'show'])->names('stagiaires');
    Route::resource('groupes',        GroupeWebController::class)       ->only(['index', 'show'])->names('groupes');
    Route::resource('modules',        ModuleWebController::class)       ->only(['index', 'show'])->names('modules');
    Route::resource('filieres',       FiliereWebController::class)      ->only(['index', 'show'])->names('filieres');
    Route::resource('annees',         AnneeWebController::class)        ->only(['index', 'show'])->names('annees');
    Route::resource('niveaux',        NiveauWebController::class)       ->only(['index', 'show'])->names('niveaux');
    Route::resource('type-formations', TypeFormationWebController::class)->only(['index', 'show'])->names('type-formations');

    // ─── Full CRUD resources ──────────────────────────────────────────────
    Route::resource('affectations', AffectationWebController::class)->names('affectations');
    Route::resource('seances',      SeanceWebController::class)     ->names('seances');
    Route::resource('notes',        NoteWebController::class)       ->names('notes');
    Route::resource('espaces',      EspaceWebController::class)     ->names('espaces');
    Route::resource('time-ranges',  TimeRangeWebController::class)  ->names('time-ranges');
});
