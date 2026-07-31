<?php

namespace App\Command;

use App\Entity\Fournisseur;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-fournisseurs',
    description: 'Import fournisseurs from data/fournisseurs.csv (LISTE FRNS RAPHIA.xlsx)',
)]
class ImportFournisseursCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FournisseurRepository $fournisseurRepository,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Path to CSV (columns: code,nom,zone)')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Remove existing fournisseurs before import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getOption('file') ?? $this->projectDir.'/data/fournisseurs.csv';

        if (!is_file($file)) {
            $io->error(sprintf('File not found: %s', $file));

            return Command::FAILURE;
        }

        if ($input->getOption('purge')) {
            foreach ($this->fournisseurRepository->findAll() as $existing) {
                $this->em->remove($existing);
            }
            $this->em->flush();
            $io->warning('Existing fournisseurs purged.');
        }

        $rows = $this->readCsv($file);
        $created = 0;
        $updated = 0;
        $i = 0;

        foreach ($rows as [$code, $nom, $zone]) {
            $fournisseur = $this->fournisseurRepository->findOneByCode($code);
            if (!$fournisseur) {
                $fournisseur = new Fournisseur();
                $fournisseur->setCode($code);
                $this->em->persist($fournisseur);
                ++$created;
            } else {
                $fournisseur->touch();
                ++$updated;
            }
            $fournisseur->setNom($nom);
            $fournisseur->setZone($zone);
            $fournisseur->setActif(true);

            if (++$i % 50 === 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
        $io->success(sprintf('Import finished: %d created, %d updated (%d rows).', $created, $updated, count($rows)));

        return Command::SUCCESS;
    }

    /**
     * @return list<array{0: string, 1: string, 2: string}>
     */
    private function readCsv(string $file): array
    {
        $rows = [];
        $handle = fopen($file, 'rb');
        if ($handle === false) {
            return $rows;
        }

        fgetcsv($handle);
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 3) {
                continue;
            }
            $code = strtoupper(trim((string) $data[0]));
            $nom = trim((string) $data[1]);
            $zone = trim((string) $data[2]);
            if ($code !== '' && $nom !== '') {
                $rows[$code] = [$code, $nom, $zone !== '' ? $zone : '—'];
            }
        }
        fclose($handle);

        return array_values($rows);
    }
}
