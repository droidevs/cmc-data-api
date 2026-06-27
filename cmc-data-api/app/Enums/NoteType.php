<?php

namespace App\Enums;

enum NoteType:string
{
    case CC = "cc";
    case EFM = "efm";

    public static function label(): array
    {
        return [
            self::CC->name => 'Controle Continue',
            self::EFM->name => 'Evaluation Fin de Formation',
        ];
    }

}
