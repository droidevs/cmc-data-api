<?php

use App\Http\Controllers\Api\AffectationController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\AnneeController;
use App\Http\Controllers\Api\AvancementController;
use App\Http\Controllers\Api\EspaceController;
use App\Http\Controllers\Api\FiliereController;
use App\Http\Controllers\Api\FormateurController;
use App\Http\Controllers\Api\GroupeController;
use App\Http\Controllers\Api\HierarchyController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\NiveauController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PoleController;
use App\Http\Controllers\Api\SeanceController;
use App\Http\Controllers\Api\StagiaireController;
use App\Http\Controllers\Api\TimeRangeController;
use App\Http\Controllers\Api\TypeFormationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Read-only reference / imported data ──────────────────────────────
    // These controllers only implement index() and show(). Using apiResource()
    // for them would register store/update/destroy routes that 404→500
    // (method does not exist on the controller). Restrict to index/show.

    Route::apiResource('poles', PoleController::class)
        ->only(['index', 'show']);

    Route::apiResource('espaces', EspaceController::class)
        ->only(['index', 'show']);

    Route::apiResource('formateurs', FormateurController::class)
        ->parameters(['formateurs' => 'formateur'])
        ->only(['index', 'show']);

    Route::apiResource('niveaux', NiveauController::class)
        ->only(['index', 'show']);

    Route::apiResource('type-formations', TypeFormationController::class)
        ->only(['index', 'show']);

    Route::apiResource('filieres', FiliereController::class)
        ->parameters(['filieres' => 'filiere'])
        ->only(['index', 'show']);

    Route::apiResource('annees', AnneeController::class)
        ->only(['index', 'show']);

    Route::apiResource('modules', ModuleController::class)
        ->parameters(['modules' => 'module'])
        ->only(['index', 'show']);

    Route::apiResource('stagiaires', StagiaireController::class)
        ->parameters(['stagiaires' => 'stagiaire'])
        ->only(['index', 'show']);

    Route::apiResource('time-ranges', TimeRangeController::class)
        ->only(['index', 'show']);

    Route::apiResource('avancements', AvancementController::class)
        ->only(['index', 'show']);

    // ─── Groupe ────────────────────────────────────────────────────────────
    // NOTE: StoreGroupeRequest / UpdateGroupeRequest exist in the codebase but
    // GroupeController currently has no store()/update()/destroy() methods.
    // Restricted to index/show until those methods are implemented — see
    // the flag below if write support is actually intended here.
    Route::apiResource('groupes', GroupeController::class)
        ->only(['index', 'show']);

    // ─── Full CRUD — these controllers implement all 5 actions ────────────

    Route::apiResource('affectations', AffectationController::class);

    Route::apiResource('seances', SeanceController::class);

    Route::apiResource('notes', NoteController::class);

    // ─── Hierarchy / Cascade endpoints for UI ─────────────────────────────
    Route::prefix('hierarchy')->group(function () {
        Route::get('poles', [HierarchyController::class, 'getPoles']);
        Route::get('filieres', [HierarchyController::class, 'getFilieres']);
        Route::get('annees', [HierarchyController::class, 'getAnnees']);
        Route::get('groupes', [HierarchyController::class, 'getGroupes']);
        Route::get('modules', [HierarchyController::class, 'getModules']);
        Route::get('stagiaires', [HierarchyController::class, 'getStagiaires']);
        Route::get('seances', [HierarchyController::class, 'getSeances']);
        Route::get('resolve/stagiaire/{cef}', [HierarchyController::class, 'resolveStagiaire']);
        Route::get('resolve/groupe/{id}', [HierarchyController::class, 'resolveGroupe']);
    });

    // ─── Import ────────────────────────────────────────────────────────────
    Route::post('import', [ImportController::class, 'import'])->name('import');

});
