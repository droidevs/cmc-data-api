<?php

namespace App\Enums;

enum NoteType:string
{
    case CC = "cc";
    case EFM = "efm";

    public static function label(): array
    {
        return [
            self::CC->value => 'Contrôle continu',
            self::EFM->value => 'Exam fin de module',
        ];
    }

}
