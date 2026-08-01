<?php

namespace App\Enum;

enum RolePersonnel: string
{
    case Trieuse = 'trieuse';
    case Controleuse = 'controleuse';
    case LesDeux = 'les_deux';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Trieuse => 'Trieuse',
            self::Controleuse => 'Contrôleuse',
            self::LesDeux => 'Trieuse / Contrôleuse',
            self::Autre => 'Autre',
        };
    }
}
