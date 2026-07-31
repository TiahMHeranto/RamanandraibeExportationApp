<?php

namespace App\DataFixtures;

use App\Entity\Personnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PersonnelFixtures extends Fixture
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $file = $this->projectDir.'/data/personnels.csv';
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
            if (count($data) < 2) {
                continue;
            }
            $numero = strtoupper(trim((string) $data[0]));
            $nom = trim((string) $data[1]);
            if ($numero === '' || $nom === '') {
                continue;
            }

            $personnel = (new Personnel())
                ->setNumeroPersonnel($numero)
                ->setNom($nom)
                ->setActif(true);
            $manager->persist($personnel);

            if (++$i % 50 === 0) {
                $manager->flush();
            }
        }
        fclose($handle);
        $manager->flush();
    }
}
