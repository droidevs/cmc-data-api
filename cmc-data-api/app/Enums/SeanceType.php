<?php

namespace App\Enums;

enum SeanceType:string
{
    case COURS = "cours";
    case CC = "cc";
    case EFM = "efm";


    public function label(): string
    {
        return match ($this) {
            self::COURS => 'Cours',
            self::CC => 'Controle Continue',
            self::EFM => 'Evaluation Fin de Formation',
        };
    }
}
