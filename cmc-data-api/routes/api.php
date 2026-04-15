<?php

use App\Http\Controllers\Api\AffectationController;
use App\Http\Controllers\Api\AnneeController;
use App\Http\Controllers\Api\DateSeanceController;
use App\Http\Controllers\Api\EspaceController;
use App\Http\Controllers\Api\FiliereController;
use App\Http\Controllers\Api\FormateurController;
use App\Http\Controllers\Api\GroupeController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\NiveauController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PoleController;
use App\Http\Controllers\Api\SeanceController;
use App\Http\Controllers\Api\StagiaireController;
use App\Http\Controllers\Api\TypeFormationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('poles', PoleController::class);
    Route::apiResource('espaces', EspaceController::class);
    Route::apiResource('formateurs', FormateurController::class)->parameters(['formateurs' => 'formateur']);
    Route::apiResource('niveaux', NiveauController::class);
    Route::apiResource('type-formations', TypeFormationController::class);
    Route::apiResource('filieres', FiliereController::class)->parameters(['filieres' => 'filiere']);
    Route::apiResource('annees', AnneeController::class);
    Route::apiResource('groupes', GroupeController::class);
    Route::apiResource('modules', ModuleController::class)->parameters(['modules' => 'module']);
    Route::apiResource('affectations', AffectationController::class);
    Route::apiResource('seances', SeanceController::class);
    Route::apiResource('date-seances', DateSeanceController::class);
    Route::apiResource('stagiaires', StagiaireController::class)->parameters(['stagiaires' => 'stagiaire']);
    Route::apiResource('notes', NoteController::class);
});

