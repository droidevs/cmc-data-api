<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Casts the French Excel boolean conventions ("Oui"/"Non", "O"/"N") used across
 * AvancementProgramme.xlsx, Base_Formateurs.xlsx and BasePlateEvaluation.xlsx
 * to/from a native PHP boolean.
 *
 * @implements CastsAttributes<bool, bool|string>
 */
class FrenchBoolean implements CastsAttributes
{
    /** @param array<string, mixed> $attributes */
    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        return (bool) $value;
    }

    /** @param array<string, mixed> $attributes */
    public function set(Model $model, string $key, mixed $value, array $attributes): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(mb_strtolower(trim($value)), ['oui', 'o', 'yes', 'y', '1', 'true'], true);
        }

        return (bool) $value;
    }
}
