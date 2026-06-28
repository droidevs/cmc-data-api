<?php

declare(strict_types=1);

namespace App\Enums;

enum NoteType: string
{
    case CC  = 'cc';
    case EFM = 'efm';

    public function label(): string
    {
        return match ($this) {
            self::CC  => 'Contrôle continu',
            self::EFM => 'Épreuve de fin de module',
        };
    }

    /**
     * Séance types that may carry a Note.
     * A plain "cours" séance never carries a grade — only cc | efm séances do.
     * This is the single source of truth referenced by Seance::isEvaluable.
     *
     * @return string[]
     */
    public static function evaluable(): array
    {
        return [self::CC->value, self::EFM->value];
    }
}
