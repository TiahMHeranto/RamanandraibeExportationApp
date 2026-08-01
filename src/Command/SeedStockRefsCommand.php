<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\Contrat;
use App\Entity\Couleur;
use App\Entity\Magasin;
use App\Enum\FamilleArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed-stock-refs', description: 'Seed articles, couleurs, magasins, contrats (idempotent)')]
class SeedStockRefsCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ([['MORAFENO', 'Morafeno'], ['MAJUNGA', 'Majunga'], ['MAROVOAY', 'Marovoay']] as [$code, $nom]) {
            $m = $this->em->getRepository(Magasin::class)->findOneBy(['code' => $code]);
            if (!$m) {
                $m = (new Magasin())->setCode($code);
                $this->em->persist($m);
            }
            $m->setNom($nom)->setActif(true);
        }

        $couleurs = [
            ['NAT', 'Naturelle'], ['BLEU1499', 'Bleu 14/99'], ['BLEUPASTEL', 'Bleu pastel 13/99'],
            ['VERT0499', 'Vert 04/99'], ['ROUGE2099', 'Rouge 20/99'], ['NOIR16', 'Noir 16/99'],
            ['FUSHIA1899', 'Fushia 18/99'], ['JAUNE0699', 'Jaune 06/99'],
        ];
        foreach ($couleurs as [$code, $libelle]) {
            $c = $this->em->getRepository(Couleur::class)->findOneBy(['code' => $code]);
            if (!$c) {
                $c = (new Couleur())->setCode($code);
                $this->em->persist($c);
            }
            $c->setLibelle($libelle)->setActif(true);
        }

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
            $a = $this->em->getRepository(Article::class)->findOneBy(['code' => $code]);
            if (!$a) {
                $a = (new Article())->setCode($code);
                $this->em->persist($a);
            }
            $a->setLibelle($libelle)->setFamille($famille)->setActif(true);
        }

        $contrat = $this->em->getRepository(Contrat::class)->findOneBy(['reference' => 'DIV XXXXX']);
        if (!$contrat) {
            $contrat = (new Contrat())->setReference('DIV XXXXX');
            $this->em->persist($contrat);
        }
        $contrat->setLibelle('Contrat divers / non imputé')->setActif(true);

        $this->em->flush();
        $io->success('Référentiels stock (articles, couleurs, magasins, contrats) à jour.');

        return Command::SUCCESS;
    }
}
