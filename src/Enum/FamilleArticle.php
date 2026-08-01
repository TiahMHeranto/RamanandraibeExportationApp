<?php

namespace App\Enum;

enum FamilleArticle: string
{
    case ToutVenant = 'tout_venant';
    case SemiFini = 'semi_fini';
    case Chute = 'chute';
    case Dechet = 'dechet';
    case Fil = 'fil';
    case Retour = 'retour';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::ToutVenant => 'Tout venant',
            self::SemiFini => 'Semi-fini',
            self::Chute => 'Chute',
            self::Dechet => 'Déchet',
            self::Fil => 'Fil',
            self::Retour => 'Retour',
            self::Autre => 'Autre',
        };
    }
}
