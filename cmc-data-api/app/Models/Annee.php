<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Annee represents one academic year within a Filiere.
 *
 * Excel mapping (AvancementProgramme):
 *   Année de formation → libelle  (integer: 1, 2)
 *   Code Filière       → filiere_code
 *
 * Stored as an integer (1 or 2); use `formattedLabel()` for the French
 * human-readable form ("1ère année" / "2ème année").
 */
class Annee extends Model
{
    use HasFactory;

    protected $fillable = [
        'filiere_code',
        'libelle',       // integer year (1 or 2)
    ];

    protected function casts(): array
    {
        return [
            'libelle' => 'integer',
        ];
    }

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

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Human-readable French label: "1ère année", "2ème année".
     * Use this accessor in views; avoid adding to $appends to prevent
     * polluting array/JSON serialisation.
     */
    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->libelle) {
                1       => '1ère année',
                2       => '2ème année',
                default => "{$this->libelle}ème année",
            },
        );
    }
}
