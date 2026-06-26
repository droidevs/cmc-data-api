<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Module (teaching unit) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Code Module        → code_module  (PK, string e.g. "M101", "EGTS102")
 *   Module             → libelle
 *   Régional           → regional     ← NEW: boolean (whether it's a regional/shared module)
 *   Année de formation → annee_id     (via annee relationship)
 *   Code Filière       → annee.filiere_code
 *
 * Note: the same code_module can appear in multiple filieres/annees with different
 * libelles (e.g. "EGTS202" is "Français" across all TS filieres).
 * The unique key is (code_module, annee_id).
 */
class Module extends Model
{
    protected $primaryKey = 'code_module';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'code_module',
        'annee_id',
        'libelle',
        'regional',   // ← NEW: flag from "Régional" column
    ];

    protected $casts = [
        'regional' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Annee, Module> */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(Annee::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'module_code', 'code_module');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeRegional(Builder $query): Builder
    {
        return $query->where('regional', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('libelle', 'like', "%{$term}%")
            ->orWhere('code_module', 'like', "%{$term}%");
    }
}
