<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Contrat;
use App\Entity\Couleur;
use App\Entity\Magasin;
use App\Enum\FamilleArticle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ReferentielStockFixtures extends Fixture implements FixtureGroupInterface
{
    public const MAGASIN_MORAFENO = 'magasin_morafeno';
    public const COULEUR_NATURELLE = 'couleur_naturelle';
    public const ARTICLE_TV_MAJUNGA = 'article_tv_majunga';

    public static function getGroups(): array
    {
        return ['stock'];
    }
    public function load(ObjectManager $manager): void
    {
        $magasins = [
            ['MORAFENO', 'Morafeno'],
            ['MAJUNGA', 'Majunga'],
            ['MAROVOAY', 'Marovoay'],
        ];
        $magasinRefs = [];
        foreach ($magasins as [$code, $nom]) {
            $m = (new Magasin())->setCode($code)->setNom($nom);
            $manager->persist($m);
            $magasinRefs[$code] = $m;
        }
        $this->addReference(self::MAGASIN_MORAFENO, $magasinRefs['MORAFENO']);

        $couleurs = [
            ['NAT', 'Naturelle'],
            ['BLEU1499', 'Bleu 14/99'],
            ['BLEUPASTEL', 'Bleu pastel 13/99'],
            ['VERT0499', 'Vert 04/99'],
            ['ROUGE2099', 'Rouge 20/99'],
            ['NOIR16', 'Noir 16/99'],
            ['FUSHIA1899', 'Fushia 18/99'],
            ['JAUNE0699', 'Jaune 06/99'],
        ];
        $couleurRefs = [];
        foreach ($couleurs as [$code, $libelle]) {
            $c = (new Couleur())->setCode($code)->setLibelle($libelle);
            $manager->persist($c);
            $couleurRefs[$code] = $c;
        }
        $this->addReference(self::COULEUR_NATURELLE, $couleurRefs['NAT']);

        $articles = [
            ['TV-MAJ', 'RAPHIA TOUT VENANT MAJUNGA', FamilleArticle::ToutVenant],
            ['TV-BES', 'RAPHIA TOUT VENANT BESALAMPY', FamilleArticle::ToutVenant],
            ['TV-COURT', 'RAPHIA TOUT VENANT COURT', FamilleArticle::ToutVenant],
            ['JAM-BEX', 'JAMBON 1KG NATURELLE BEX', FamilleArticle::SemiFini],
            ['JAM-BCS', 'JAMBON 1KG NATURELLE BCS', FamilleArticle::SemiFini],
            ['JAM-BCO', 'JAMBON 1KG NATURELLE BCO', FamilleArticle::SemiFini],
            ['JAM-MCS', 'JAMBON 1KG NATURELLE MCS', FamilleArticle::SemiFini],
            ['JAM-MCO', 'JAMBON 1KG NATURELLE MCO', FamilleArticle::SemiFini],
            ['JAM-MEX', 'JAMBON 1KG NATURELLE MEX', FamilleArticle::SemiFini],
            ['JAM500-MEX', 'JAMBON 500G NATURELLE MEX', FamilleArticle::SemiFini],
            ['PEL-150', 'PELOTE 150G NATUREL', FamilleArticle::SemiFini],
            ['PEL-50', 'PELOTE 50G NATURELLE 00/99', FamilleArticle::SemiFini],
            ['GUI-NAT', 'GUIRLANDE NATURELLE', FamilleArticle::SemiFini],
            ['CHUTE', 'CHUTE', FamilleArticle::Chute],
            ['CHUTE-250', 'CHUTE 250G', FamilleArticle::Chute],
            ['DECHET', 'DECHET', FamilleArticle::Dechet],
            ['FIL', 'FIL', FamilleArticle::Fil],
            ['CORDE', 'CORDE PRESSE', FamilleArticle::Fil],
            ['RETOUR', 'RETOUR', FamilleArticle::Retour],
        ];
        foreach ($articles as [$code, $libelle, $famille]) {
            $a = (new Article())->setCode($code)->setLibelle($libelle)->setFamille($famille);
            $manager->persist($a);
            if ($code === 'TV-MAJ') {
                $this->addReference(self::ARTICLE_TV_MAJUNGA, $a);
            }
        }

        $manager->persist((new Contrat())->setReference('DIV XXXXX')->setLibelle('Contrat divers / non imputé'));

        $manager->flush();
    }
}
