<?php

namespace App\Enum;

enum TypeOperationStock: string
{
    case Arrivage = 'arrivage';
    case TriageSortie = 'triage_sortie';
    case TriageEntree = 'triage_entree';
    case ColorationSortie = 'coloration_sortie';
    case ColorationEntree = 'coloration_entree';
    case ArtisanatSortie = 'artisanat_sortie';
    case ArtisanatEntree = 'artisanat_entree';
    case Transfert = 'transfert';
    case Export = 'export';
    case SortieDechet = 'sortie_dechet';
    case Inventaire = 'inventaire';
    case Retour = 'retour';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Arrivage => 'Arrivage',
            self::TriageSortie => 'Triage — sortie',
            self::TriageEntree => 'Triage — entrée',
            self::ColorationSortie => 'Coloration — sortie',
            self::ColorationEntree => 'Coloration — entrée',
            self::ArtisanatSortie => 'Artisanat — sortie',
            self::ArtisanatEntree => 'Artisanat — entrée',
            self::Transfert => 'Transfert',
            self::Export => 'Export',
            self::SortieDechet => 'Sortie déchets',
            self::Inventaire => 'Inventaire',
            self::Retour => 'Retour',
            self::Autre => 'Autre',
        };
    }

    public function defaultSens(): SensMouvement
    {
        return match ($this) {
            self::Arrivage, self::TriageEntree, self::ColorationEntree, self::ArtisanatEntree, self::Retour, self::Inventaire => SensMouvement::Entree,
            default => SensMouvement::Sortie,
        };
    }
}
