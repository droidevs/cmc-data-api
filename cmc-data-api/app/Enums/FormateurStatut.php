<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Formateur employment status, sourced from "Statut" column in Base_Formateurs.xlsx.
 */
enum FormateurStatut: string
{
    case Ofppt = 'OFPPT';
    case Vacataire = 'Vacataire';
    case Contractuel = 'Contractuel';

    public function label(): string
    {
        return match ($this) {
            self::Ofppt => 'OFPPT (permanent)',
            self::Vacataire => 'Vacataire',
            self::Contractuel => 'Contractuel',
        };
    }
}
