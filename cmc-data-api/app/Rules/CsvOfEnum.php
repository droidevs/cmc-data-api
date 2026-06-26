<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates params destined for QueryFilter::inList() on string/enum columns
 * (statut, mode, type, genre, decision, ...). Each comma-separated element
 * must be a member of $allowed. This is the real safety boundary for these
 * filters: it stops free-text junk from silently producing an empty result
 * set, caps the list length, and documents the exact accepted vocabulary
 * back to the API consumer via the 422 error message.
 */
class CsvOfEnum implements ValidationRule
{
    /** @param array<int, string> $allowed */
    public function __construct(
        private readonly array $allowed,
        private readonly int $max = 20,
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
            if (! in_array($item, $this->allowed, true)) {
                $fail("The {$attribute} field contains an invalid value: \"{$item}\". Allowed: ".implode(', ', $this->allowed).'.');

                return;
            }
        }
    }
}
