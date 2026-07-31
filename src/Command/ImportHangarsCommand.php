<?php

namespace App\Command;

use App\Entity\Hangar;
use App\Repository\HangarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-hangars',
    description: 'Import hangars de traitement from data/hangars.csv (columns: numero,code)',
)]
class ImportHangarsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HangarRepository $hangarRepository,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Path to CSV (columns: numero,code)')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Remove existing hangars before import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getOption('file') ?? $this->projectDir.'/data/hangars.csv';

        if (!is_file($file)) {
            $io->error(sprintf('File not found: %s', $file));

            return Command::FAILURE;
        }

        if ($input->getOption('purge')) {
            foreach ($this->hangarRepository->findAll() as $existing) {
                $this->em->remove($existing);
            }
            $this->em->flush();
            $io->warning('Existing hangars purged.');
        }

        $rows = $this->readCsv($file);
        $created = 0;
        $updated = 0;
        $i = 0;

        foreach ($rows as [$numero, $code]) {
            $hangar = $this->hangarRepository->findOneByNumero($numero);
            if (!$hangar) {
                $hangar = new Hangar();
                $hangar->setNumero($numero);
                $this->em->persist($hangar);
                ++$created;
            } else {
                $hangar->touch();
                ++$updated;
            }
            $hangar->setCode($code);
            $hangar->setActif(true);

            if (++$i % 50 === 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
        $io->success(sprintf('Import finished: %d created, %d updated (%d rows).', $created, $updated, count($rows)));

        return Command::SUCCESS;
    }

    /**
     * @return list<array{0: string, 1: string}>
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
            if (count($data) < 2) {
                continue;
            }
            $numero = strtoupper(trim((string) $data[0]));
            $code = strtoupper(trim((string) $data[1]));
            if ($numero !== '' && $code !== '') {
                $rows[$numero] = [$numero, $code];
            }
        }
        fclose($handle);

        return array_values($rows);
    }
}
