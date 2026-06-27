<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Teaching delivery mode for an Affectation / Groupe.
 *
 * Excel source values (French) map as:
 *   "Résidentiel" → Presentiel
 *   "Synchrone"   → Synchrone
 *   "Asynchrone"  → Async
 */
enum AffectationMode: string
{
    case PRESENTIEL = 'presentiel';
    case SYNCHRONE = 'synchrone';
    case ASYNC = 'async';

    public function label(): string
    {
        return match ($this) {
            self::PRESENTIEL => 'Résidentiel',
            self::SYNCHRONE => 'Synchrone',
            self::ASYNC => 'Asynchrone',
        };
    }
}
