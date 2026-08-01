<?php

namespace App\Enum;

enum CategorieLigneTraitement: string
{
    case Produit = 'produit';
    case Chute = 'chute';
    case Dechet = 'dechet';
    case Retour = 'retour';
    case Fil = 'fil';

    public function label(): string
    {
        return match ($this) {
            self::Produit => 'Produit',
            self::Chute => 'Chute',
            self::Dechet => 'Déchet',
            self::Retour => 'Retour',
            self::Fil => 'Fil',
        };
    }
}
