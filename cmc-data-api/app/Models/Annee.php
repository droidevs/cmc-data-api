<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Annee represents one academic year (optionally with a specialization
 * option) within a Filiere.
 *
 * A Filiere only models the GENERAL training program (e.g. "Développement
 * Digital"); any option/specialization is expressed in the Annee label
 * itself, e.g.:
 *   - "1ère année - Tronc Commun"
 *   - "2ème année - Option Développement Web Full Stack"
 *
 * Excel mapping (AvancementProgramme):
 *   Année de formation + Code Filière → libelle (combined into a string)
 *   Code Filière (general)           → filiere_code
 */
class Annee extends Model
{
    use HasFactory;

    protected $fillable = [
        'filiere_code',
        'libelle',       // human-readable year + option label
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Filiere, Annee> */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'filiere_code', 'code_filiere');
    }

    /** @return HasMany<Groupe> */
    public function groupes(): HasMany
    {
        return $this->hasMany(Groupe::class);
    }

    /** @return HasMany<Module> */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
