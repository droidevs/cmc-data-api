<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates params destined for QueryFilter::inList() on free-text string
 * columns (code_filiere, code_module, secteur, ...) where there's no fixed
 * enum to check against. Eloquent's whereIn() parameter-binds each element,
 * so this isn't an injection boundary — it's a DoS/sanity boundary: caps
 * the number of comma-separated values and the length of each one so a
 * client can't send a multi-megabyte query string or thousands of IN()
 * values.
 */
readonly class CsvOfStrings implements ValidationRule
{
    public function __construct(
        private int $max = 50,
        private int $maxItemLength = 100,
    ) {
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
            if (! is_string($item) && ! is_numeric($item)) {
                $fail("The {$attribute} field contains an invalid value.");

                return;
            }

            if (mb_strlen((string) $item) > $this->maxItemLength) {
                $fail("The {$attribute} field contains a value longer than {$this->maxItemLength} characters.");

                return;
            }
        }
    }
}
