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
    case Presentiel = 'presentiel';
    case Synchrone = 'synchrone';
    case Async = 'async';

    public function label(): string
    {
        return match ($this) {
            self::Presentiel => 'Résidentiel',
            self::Synchrone => 'Synchrone',
            self::Async => 'Asynchrone',
        };
    }
}
