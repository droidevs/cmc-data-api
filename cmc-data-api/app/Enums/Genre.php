<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Stagiaire gender, as encoded in source spreadsheets (Sexe / Genre / Sexe columns).
 * Source data uses French initials: "F" (Femme) and "H" (Homme) — NOT "F"/"M".
 */
enum Genre: string
{
    case Femme = 'F';
    case Homme = 'H';

    public function label(): string
    {
        return match ($this) {
            self::Femme => 'Féminin',
            self::Homme => 'Masculin',
        };
    }
}
