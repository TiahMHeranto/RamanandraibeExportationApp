<?php

namespace App\DataFixtures;

use App\Entity\Fournisseur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FournisseurFixtures extends Fixture
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $file = $this->projectDir.'/data/fournisseurs.csv';
        if (!is_file($file)) {
            return;
        }

        $handle = fopen($file, 'rb');
        if ($handle === false) {
            return;
        }

        fgetcsv($handle);
        $i = 0;
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 3) {
                continue;
            }
            $code = strtoupper(trim((string) $data[0]));
            $nom = trim((string) $data[1]);
            $zone = trim((string) $data[2]);
            if ($code === '' || $nom === '') {
                continue;
            }

            $fournisseur = (new Fournisseur())
                ->setCode($code)
                ->setNom($nom)
                ->setZone($zone !== '' ? $zone : '—')
                ->setActif(true);
            $manager->persist($fournisseur);

            if (++$i % 50 === 0) {
                $manager->flush();
            }
        }
        fclose($handle);
        $manager->flush();
    }
}
