<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates params destined for QueryFilter::inList(), which accepts either
 * a comma-separated string ("1,2,3") or a real array (PHP-style ?ids[]=1&ids[]=2)
 * and explodes/whereIns it verbatim. Without this, a client can send something
 * like "1,2,3); DROP TABLE--" — Eloquent's whereIn() parameter-binds every
 * element so it can't actually break out to raw SQL, but malformed/huge lists
 * can still: silently match nothing (UX problem), or contain thousands of
 * comma-separated junk values (DoS via huge IN() clauses). This rule caps
 * count and enforces each element looks like a real ID.
 */
readonly class CsvOfIntegers implements ValidationRule
{
    public function __construct(private int $max = 100)
    {
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $items = is_array($value)
            ? $value
            : array_filter(array_map('trim', explode(',', (string) $value)), fn ($v) => $v !== '');

        if (empty($items)) {
            $fail("The {$attribute} field must contain at least one value.");

            return;
        }

        if (count($items) > $this->max) {
            $fail("The {$attribute} field must not contain more than {$this->max} values.");

            return;
        }

        foreach ($items as $item) {
            if (! is_numeric($item) || (int) $item <= 0 || (string) (int) $item !== (string) $item) {
                $fail("The {$attribute} field contains an invalid id: \"{$item}\".");

                return;
            }
        }
    }
}
