<?php

use App\Http\Controllers\Web\AffectationWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FiliereWebController;
use App\Http\Controllers\Web\FormateurWebController;
use App\Http\Controllers\Web\GroupeWebController;
use App\Http\Controllers\Web\ModuleWebController;
use App\Http\Controllers\Web\NoteWebController;
use App\Http\Controllers\Web\PoleWebController;
use App\Http\Controllers\Web\SeanceWebController;
use App\Http\Controllers\Web\StagiaireWebController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->name('web.')->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    // ─── Read-only resources (imported from Excel) ────────────────────────
    Route::resource('poles',      PoleWebController::class)     ->only(['index', 'show'])->names('poles');
    Route::resource('formateurs', FormateurWebController::class)->only(['index', 'show'])->names('formateurs');
    Route::resource('stagiaires', StagiaireWebController::class)->only(['index', 'show'])->names('stagiaires');
    Route::resource('groupes',    GroupeWebController::class)   ->only(['index', 'show'])->names('groupes');
    Route::resource('modules',    ModuleWebController::class)   ->only(['index', 'show'])->names('modules');
    Route::resource('filieres',   FiliereWebController::class)  ->only(['index', 'show'])->names('filieres');

    // ─── Full CRUD resources ──────────────────────────────────────────────
    Route::resource('affectations', AffectationWebController::class)->names('affectations');
    Route::resource('seances',      SeanceWebController::class)     ->names('seances');
    Route::resource('notes',        NoteWebController::class)       ->names('notes');
});
